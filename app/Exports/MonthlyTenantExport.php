<?php

namespace App\Exports;

use Carbon\Carbon;

use App\Building;
use App\TenantPayment;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyTenantExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    private $building;
    private $specific_date;
    private $payment_data;

    public function __construct(building $building, string $year, string $month)
    {;
        $this->building = $building;
        $this->specific_date = Carbon::create($year, $month);
        $this->search_other_payment($building);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:AZ100'; // All data rows
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16);
            },
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->buildCollection();
    }

    public function headings(): array
    {
        $first_part = ['房號', '租金'];
        $last_part = ['履保', '已收', '承租人', '電話', '出租起', '出租迄', '繳款日', '繳別'];

        if(isset(array_values($this->payment_data)[0])){
          return array_flatten([$first_part, array_keys(array_values($this->payment_data)[0]), $last_part ]);
        }
        else{
          return array_flatten([$first_part, $last_part]);
        }
    }

    private function buildCollection(): Collection {
        $building = $this->building;

        $data = collect();
        foreach( $building->rooms as $room ){
          $activeTenantContracts = $room->tenantContracts
              ->where('contract_end', '>=', $this->specific_date)
              ->where('contract_start', '<=', $this->specific_date->copy()->endOfMonth());

          foreach( $activeTenantContracts as $tenantContract ){

            $first_part = [$room->room_number,
                           $tenantContract->rent];
            $last_part = [
              $tenantContract->deposit,
              $tenantContract->deposit_paid,
              $tenantContract->tenant->name,
              implode(',', $tenantContract->tenant->phones->pluck('value')->toArray()),
              $tenantContract->contract_start,
              $tenantContract->contract_end,
              $tenantContract->rent_pay_day,
              $tenantContract->tenantPayments->where('subject', '租金')->last()->period,
            ];
            $middle_part = [];

            if(isset(array_values($this->payment_data)[0])){
              foreach($this->payment_data[$tenantContract->id] as $payment_amount){
                array_push($middle_part, $payment_amount);
              }
            }

            $data[] = array_flatten([$first_part, $middle_part, $last_part]);
          }
        }
        return $data;
    }

    private function search_other_payment($building){
        $activeTenantContractIds = array();
        $result = array();
        foreach( $building->rooms as $room ){
            $activeTenantContractIds[] = $room->tenantContracts
                                        ->where('contract_end', '>=', $this->specific_date)
                                        ->where('contract_start', '<=', $this->specific_date->copy()->endOfMonth())
                                        ->pluck('id')->toArray();
          
        }
        $activeTenantContractIds = array_flatten($activeTenantContractIds);
        $subjects = TenantPayment::whereIn('tenant_contract_id', $activeTenantContractIds)
                                      ->where('period', '!=', '次')
                                      ->where('is_pay_off', '!=', true)
                                      ->where('subject', '!=', '租金')
                                      ->pluck('subject')
                                      ->toArray();
        $subjects = array_unique($subjects);
        foreach( $activeTenantContractIds as $activeTenantContractId ){
            $result[$activeTenantContractId] = [];
              foreach( $subjects as $subject ){
                $subjectData = [];
                $payment = TenantPayment::where('tenant_contract_id', '=', $activeTenantContractId)
                                          ->where('subject', '=', $subject)
                                          ->first();
                if(isset($payment)){
                    $result[$activeTenantContractId][$subject] = $payment->amount;
                }
                else{
                    $result[$activeTenantContractId][$subject] = 0;
                }
            }
        }
        $this->payment_data = $result;
    }
}
