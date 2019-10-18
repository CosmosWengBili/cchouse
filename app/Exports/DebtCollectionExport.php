<?php

namespace App\Exports;

use App\Room;
use App\Tenant;
use App\TenantContract;
use App\TenantElectricityPayment;
use App\TenantPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DebtCollectionExport implements FromCollection, WithHeadings
{
    private $date;
    private $months;
    private $tenantContracts;

    public function __construct(Carbon $date)
    {
        $this->date = $date;
        $this->tenantContracts = $this->findTenantContracts();
        $this->months = $this->buildDebtMonths();
    }

    public function collection()
    {
        return $this->buildDataRows();
    }

    public function headings(): array
    {
        $headings = [
            '行政區',
            '物件',
            '房號',
            '租金',
            '管理費',
            '垃圾費',
            '水雜費',
            '服務費',
            '履保金',
            '已收',
            '110v',
            '220v',
            '承租人',
            '電話',
            '合約起',
            '合約迄',
            '續約',
            '繳款',
            '遲繳'
        ];

        foreach ($this->months as $month) {
            $headings[] = "{$month->month}月";
        }
        $headings[] = '電費';
        $headings[] = '合計';
        $headings[] = '催收回報';

        return $headings;
    }

    private function buildDataRows(): Collection
    {
        return $this->tenantContracts->map(function ($tc) {
            return $this->buildDataRow($tc);
        });
    }

    private function buildDataRow(TenantContract $tenantContract): array
    {
        $building =  $tenantContract->building;
        $room =  $tenantContract->room;
        $tenant = $tenantContract->tenant;
        $phones = $tenant ? $tenant->phones()->get()->map(function ($p) { return $p->value; })->toArray() : [];
        $managementFee = $this->fetchPaymentSumBySubject('管理費');
        $trashFee = $this->fetchPaymentSumBySubject('垃圾費');
        $waterFee = $this->fetchPaymentSumBySubject('水雜費');
        $serviceFee = $this->fetchPaymentSumBySubject('管理服務費');
        $contractRange = $this->fetchContractRange($tenant, $room);
        $oldestPaymentDate = $this->findOldestDueTime([$tenantContract->id]);

        $data = [
            $building->district,                                                            // 行政區
            $building->title,                                                               // 物件
            $room->room_number,                                                             // 房號
            $tenantContract->rent,                                                          // 租金
            $managementFee,                                                                 // 管理費
            $trashFee,                                                                      // 垃圾費
            $waterFee,                                                                      // 水雜費
            $serviceFee,                                                                    // 管理服務費
            $tenantContract->deposit,                                                       // 履保金
            $tenantContract->deposit_paid,                                                  // 已收
            $tenantContract->{"110v_start_degree"},                                         // 110v
            $tenantContract->{"220v_start_degree"},                                         // 220v
            $tenant->name,                                                                  // 承租人
            join(', ', $phones),                                                            // 電話
            $contractRange[0]->format('Y-m-d'),                                                              // 合約起
            $contractRange[1]->format('Y-m-d'),                                                              // 合約迄
            '',                                                                             // 續約
            $tenantContract->rent_pay_day,                                                  // 繳款,
            $oldestPaymentDate->diffInDays($this->date),                                    // 遲繳
        ];
        $total = 0;
        foreach ($this->months as $month) {
            $unpaidFee = $this->sumTenantPaymentAmountsByMonth($tenantContract, $month);    // 每個月未繳的費用
            $total += intval($unpaidFee);
            $data[] = $unpaidFee;
        }
        $unpaidElectricityFee = $this->sumTenantElectricityPaymentsAmount($tenantContract); // 未繳電費
        $total += intval($unpaidElectricityFee);
        $data[] = $unpaidElectricityFee;
        $data[] = $total;                                                                   // 合計


        return array_map(function ($value) { return strval($value); }, $data);
    }

    private function buildDebtMonths(): array
    {
        $tenantContractIds = $this->tenantContracts
                                  ->map(function ($tc) {
                                      return $tc->id;
                                  })
                                  ->toArray();
        $startFrom = $this->findOldestDueTime($tenantContractIds);

        $result = [];
        while ($startFrom->lte($this->date)) {
            $result[] = $startFrom->copy();
            $startFrom->addMonth();
        }

        return $result;
    }

    private function findOldestDueTime(array $tenantContractIds)
    {
        $date = $this->date;
        $tenantPayment = TenantPayment::whereIn('tenant_contract_id', $tenantContractIds)
            ->where('is_charge_off_done', false)
            ->where('due_time', '<=', $date)
            ->orderBy('due_time', 'asc')
            ->first(); // $date 之前的最早 TenantPayment
        $tenantElectricityPayment = TenantElectricityPayment::whereIn('tenant_contract_id', $tenantContractIds)
            ->where('is_charge_off_done', false)
            ->where('due_time', '<=', $date)
            ->orderBy('due_time', 'asc')
            ->first(); // $date 之前的最早 TenantElectricityPayment
        $date1 = $tenantPayment ? $tenantPayment->due_time : $this->date;
        $date2 = $tenantElectricityPayment ? $tenantElectricityPayment->due_time : $this->date;

        return $date1->min($date2)->copy();
    }

    private function findTenantContracts(): Collection
    {
        $date = $this->date;
        $dateStr = DB::raw("'{$date}'");

        return TenantContract::with(['building', 'room', 'tenant'])
            ->leftJoin('debt_collections', function ($q) use ($dateStr) { // 在 $date 之前有 debt_collections 紀錄的 TenantContract
                $q->on('debt_collections.tenant_contract_id', '=', 'tenant_contract.id')
                  ->on('debt_collections.created_at', '<=', $dateStr);
            })
            ->where('tenant_contract.contract_end', '>=', $dateStr) // 在 $date 是有效的 TenantContract
            ->where('tenant_contract.contract_start', '<=', $dateStr) // 在 $date 是有效的 TenantContract
            ->groupBy('tenant_contract.id')
            ->having(DB::raw('COUNT(debt_collections.id)'), '>', 0)
            ->select('tenant_contract.*')
            ->get();
    }

    private function fetchPaymentSumBySubject(string $subject): string
    {
        $lastMonth = [$this->date->copy()->subMonth()->startOfMonth(), $this->date->copy()->subMonth()->endOfMonth()];
        return TenantPayment::where('subject', $subject)
                            ->where('is_charge_off_done', false)
                            ->whereBetween('due_time', $lastMonth)
                            ->sum('amount');
    }

    private function fetchContractRange(Tenant $tenant, Room $room): array
    {
        $startAt = $room->tenantContracts()
                        ->where('tenant_id', $tenant->id)
                        ->orderBy('contract_start', 'asc')
                        ->first()
                        ->contract_start;
        $endAt = $room->tenantContracts()
                      ->where('tenant_id', $tenant->id)
                      ->orderBy('contract_end', 'desc')
                      ->first()
                      ->contract_end;

        return [$startAt, $endAt];
    }

    private function sumTenantPaymentAmountsByMonth(TenantContract $tenantContract, Carbon $month) {
        $monthRange = [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()];
        return $tenantContract->tenantPayments()
                              ->where('is_charge_off_done', false)
                              ->whereBetween('due_time', $monthRange)
                              ->sum('amount');
    }

    private function sumTenantElectricityPaymentsAmount(TenantContract $tenantContract) {
        return $tenantContract->tenantElectricityPayments()
                              ->where('is_charge_off_done', false)
                              ->where('due_time', '<=', $this->date)
                              ->sum('amount');
    }
}
