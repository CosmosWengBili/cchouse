<?php

namespace App\Services;

use App\ReversalErrorCase;
use App\KeyRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

use App\User;
use App\LandlordContract;
use App\Landlord;
use App\Room;
use App\CompanyIncome;
use App\TenantContract;
use App\TenantPayment;
use App\Maintenance;
use App\MonthlyReport;
use App\SystemVariable;

use App\Notifications\LandlordContractDue;
use App\Notifications\TenantContractDueInTwoMonths;
use App\Notifications\TextNotify;
use App\Notifications\RoomHasChanged;

use App\Services\MonthlyReportService;
use App\Services\ReceiptService;

class ScheduleService
{
    protected $method;
    protected $args;

    public function __construct($method, $args)
    {
        $this->method = $method;
        $this->args = $args;
    }

    public function __invoke()
    {
        // call function dynamically
        $result = call_user_func([$this, $this->method], $this->args);
        // manipulate result, and return
        return $result;
    }

    // returned the packed instance
    public static function make($method, $args = null)
    {
        return new ScheduleService($method, $args);
    }

    // daily task to notify users that some contracts due in 2 months
    public function notifyContractDue()
    {
        // 代管 is 2 months, 包租 is 6 months
        LandlordContract::where([
                'commission_end_date' => Carbon::today()->addMonth(2),
                'commission_type' => '代管',
            ])
            ->orWhere([
                'commission_end_date' => Carbon::today()->addMonth(6),
                'commission_type' => '包租',
            ])
            ->with(['commissioner', 'building:id,city,district,address', 'landlords:name'])
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->commissioner->notify(
                    new LandlordContractDue($landlordContract)
                );
                $account_users =  User::whereHas('groups', function ($q) {
                    $q->where('name', '帳務組');
                })
                        ->get();
                foreach ($account_users as $account_user) {
                    $account_user->notify(
                        new LandlordContractDue($landlordContract)
                    );
                }
            });
    }

    // daily task to adjust rent
    public function adjustRent()
    {
        LandlordContract::with('building.rooms')
            ->where('rent_adjusted_date', Carbon::today())
            ->get()
            ->each(function ($landlordContract) {
                $landlordContract->building->rooms->each(function ($room) use (
                    $landlordContract
                ) {

                    $ratio = intval($landlordContract->adjust_ratio);
                    $isRatioLTE100 = $ratio <= 100;
                    if ($isRatioLTE100) {
                        // 用 % 數調漲
                        $room->rent_reserve_price = intval(
                            round(
                                ($room->rent_reserve_price * (100 + $landlordContract->adjust_ratio) ) / 100
                            )
                        );
                    } else {
                        // 直接將租金加上此值
                        $room->rent_reserve_price = intval(
                            round(
                                $room->rent_reserve_price + $landlordContract->adjust_ratio
                            )
                        );
                    }
                    $room->save();


                    if ($room->rent_reserve_price > $room->rent_actual) {
                        $users = User::group('管理組')->get();
                        foreach ($users as $user) {
                            $user->notify(new RoomHasChanged('PriceOverActual', $room));
                        }
                    }
                });
            });
    }

    public function notifyBirth()
    {
        $landlord_names = Landlord::where(
            'birth',
            'like',
            '%'.Carbon::today()->addWeek(2)->format('m-d').'%'
        )->pluck('name')->toArray();
        foreach (User::all() as $key => $user) {
            $user->notify(new TextNotify(implode(' ', $landlord_names)));
        }
    }

    // daily task to notify users that some tenant contracts due in 2 months
    public function notifyTenantContractDueInTwoMonths()
    {
        // escrow is 2 months
        TenantContract::where('contract_end', Carbon::today()->addMonth(2))
            ->with('commissioner')
            ->get()
            ->each(function ($tenantContract) {
                $tenantContract->commissioner->notify(
                    new TenantContractDueInTwoMonths($tenantContract)
                );
            });
    }
    public function notifyMaintenanceStatus()
    {
        // 超過『預計處理日期』 時，且『狀態』不是『案件完成』和 『已取消』才跳通知
        $notifyRequiredDays = SystemVariable::where('group', 'Maintenances')
                                            ->where('code', 'MaintenanceNotifyRequiredDays')
                                            ->first()->value;
        $limitDatetime = Carbon::now()->subDays($notifyRequiredDays);
        $maintenances = Maintenance::whereNotIn('status', ['案件完成', '已取消'])
            ->where('expected_service_date', '<=', $limitDatetime)
            ->get();
        foreach ($maintenances as $maintenance) {
            $commissioner = $maintenance->commissioner()->first();
            // 通知附上連結，點擊後到對應的維修清潔的查看頁面
            $url = route('maintenances.show', [$maintenance->id]);
            $commissioner->notify(
                new TextNotify(
                    "維修清潔單號：{$maintenance->id} 狀態超過 {$notifyRequiredDays} 天未更新，煩請抽空查看。".'<a href="'.$url.'">點我</a>'
                )
            );
        }
    }

    public function genarateDebtCollections()
    {
        $delay = SystemVariable::where('code', 'debt_collection_delay_days')->first('value');
        $delay = $delay ? intval($delay->value) : config('finance.debt_collection_delay_days');
        $notifyAt = Carbon::today()->subDays($delay);
        $tenantPayments = TenantPayment::with('payLogs')->where('is_charge_off_done', false)->where('due_time', $notifyAt)->get();

        foreach ($tenantPayments as $tenantPayment) {
            $tenantPayment->tenantContract->debtCollections()->create([
                'status' => '催收中',
            ]);
        }
    }

    public function notifyReversalErrorCases()
    {
        if (ReversalErrorCase::where('status', '未結案')->exists()) {
            $users = User::group('管理組')->get();
            $users->each(function ($user) {
                $user->notify(new TextNotify('尚有未結案之異常沖銷案件'));
            });
        }
    }

    public static function setMonthlyReportCarryFoward()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        $service = new MonthlyReportService();

        $landlordContracts = LandlordContract::where('commission_start_date', '<', Carbon::today())
                                            ->where('commission_end_date', '>', Carbon::today())
                                            ->get();

        foreach ($landlordContracts as $landlordContract) {
            $data = $service->getMonthlyReport($landlordContract, $month, $year);
            $revenue = $data['meta']['total_income'] - $data['meta']['total_expense'];

            // store carry forward if current day it the last day of the month
            if (Carbon::now()->format('Y-m-d') == Carbon::now()->endOfMonth()->format('Y-m-d')) {
                $monthlyReport = MonthlyReport::create(['year' => $year,
                    'month' => $month,
                    'carry_forward' => $revenue,
                    'landlord_contract_id' => $landlordContract->id]);
            }

            // store to Redis each time
            Redis::set('monthlyRepost:carry:'.$landlordContract->id, $revenue);
        }
    }
    // public function anotherNotification($data) {
    //     //
    // }

    /**
     * 預約中 使用中 已完成
     * 超過預計借日 borrow_date，狀態仍未到使用中
     * 超過預計還日 return_date，狀態仍未到已歸還
     * 以上 通知鑰匙保管者和借閱者
     */
    public function notifyKeyRequestBorrowAllowed()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. get all by conditions

        $keyRequests = KeyRequest::where('borrow_date', '<', $today)
            ->where('status', '預約中')

            ->orWhere(function ($query) use ($today) {
                $query->where('return_date', '<', $today);
                $query->where('status', '<>', '已完成');
            })
            ->get();

        // 2. send notifications.
        /** @var KeyRequest $keyRequest */
        foreach ($keyRequests as $keyRequest) {
            // send to request user 借用人
            $keyRequest->requestUser->notify(
                new TextNotify(
                    '請更新鑰匙出借紀錄。'
                )
            );

            // send to holder 保管人
            User::find($keyRequest->key->keeper_id)->notify(
                new TextNotify(
                    '請更新鑰匙出借紀錄。'
                )
            );
        }
    }
    public function genarateCharterManagementFee()
    {
        $landlordContracts = LandlordContract::where('commission_start_date', '<', Carbon::today())
                                            ->where('commission_end_date', '>', Carbon::today())
                                            ->where('commission_type', '包租')
                                            ->get();
        foreach ($landlordContracts as $landlordContract) {
            foreach ($landlordContract->building->normalRooms() as $room) {
                $income = 0;
                if ($room->management_fee_mode == '比例') {
                    $income = intval(round($room->rent_actual * $room->management_fee / 100));
                } else {
                    $income  = intval($room->management_fee);
                }
                CompanyIncome::create([
                    'subject' => '租金',
                    'income_date' => Carbon::today(),
                    'amount' => $income,
                    'incomable_id' => $room->id,
                    'incomable_type' => Room::class
                ]);
            }
        }
    }

    public function genarateDepositInterest()
    {
        $tenantContracts = TenantContract::active()
            ->with(['tenant', 'room'])
            ->get();
        $depositInterest = SystemVariable::where(
            'code',
            '=',
            'depositRate'
            )->first()->value;
        foreach ($tenantContracts as $tenantContract) {
            if ($tenantContract->room->building->activeContracts()->commission_type == '代管') {
                continue;
            }
            CompanyIncome::create([
                'subject' => '押金設算息',
                'income_date' => Carbon::today(),
                'amount' => round($tenantContract->deposit_paid * $depositInterest),
                'incomable_id' => $tenantContract->id,
                'incomable_type' => TenantContract::class
            ]);
        }
    }

    /**
     * 每天檢查每份有效租客合約，
     * 把租客合約下 tenantPayment 科目為『履約保證金』對應的 paylog amount 做加總，
     * 並更新『押金已繳納 deposit_paid』這個欄位。
     */

    public function updateDepositPaid() {
        $contracts = TenantContract::active()->get();

        foreach ($contracts as $contract) {
            $payments = $contract->tenantPayments()->where('subject', '履約保證金')->get();
            $sum = 0;
            foreach ($payments as $payment) {
                $sum += $payment->payLogs()->sum('amount');
            }
            $contract->update(['deposit_paid' => $sum]);
        }
    }
}
