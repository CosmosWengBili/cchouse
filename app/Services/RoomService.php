<?php

namespace App\Services;

use App\Maintenance;
use App\Notifications\TextNotify;
use App\Room;
use App\Appliance;
use App\User;
use App\Traits\Controllers\HandleDocumentsUpload;

class RoomService
{
    use HandleDocumentsUpload;

    public static function make($data)
    {
        return Room::make($data);
    }

    public static function create($data, $appiances)
    {
        $room           = Room::create($data);
        $new_appliances = [];
        // generates all of appliances
        foreach ($appiances as $appiance) {
            $new_appliance = Appliance::make([
                'subject'           => $appiance['subject'],
                'spec_code'         => $appiance['spec_code'],
                'count'             => $appiance['count'],
                'vendor'            => $appiance['vendor'],
                'maintenance_phone' => $appiance['maintenance_phone'],
                'comment'           => $appiance['comment']
            ]);
            array_push($new_appliances, $new_appliance);
        }

        // link these appliances to the newly created room
        $room->appliances()->saveMany($new_appliances);

        return $room;
    }

    /**
     * @param Room $room
     * @param $data
     * @param Array $appliances
     *
     * @return mixed
     */
    public static function update($room, $data, $appliances, $maintenances = [])
    {
        $keepIds = array_map(function ($appliance) {
            return isset($appliance['id']) ? $appliance['id'] : null;
        }, $appliances);

        // remove removed item
        $room
            ->appliances()
            ->whereNotIn('id', $keepIds)
            ->delete();

        foreach ($appliances as $appliance) {
            if (isset($appliance['id'])) {
                $id           = $appliance['id'];
                $db_appliance = $room->appliances()->find($id);
                $db_appliance->update($appliance);
            } else {
                Appliance::create(
                    array_merge($appliance, [
                        'room_id' => $room->id
                    ])
                );
            }
        }

        //
        $keepIds = array_map(function ($maintenance) {
            return isset($maintenance['id']) ? $maintenance['id'] : null;
        }, $maintenances);
        $keepIds = array_values(array_filter($keepIds));

        $room
            ->roomMaintenances()
            ->whereNotIn('id', $keepIds)
            ->delete();

        $maintenances = array_map(function ($maintenance) use ($room) {
            $data = $maintenance;
            $data['room_id'] = $room->id;
            if (isset($data['id']) && $data['id'] > 0) {
                $maintenance = \App\RoomMaintenance::find($data['id']);
            } else {
                $maintenance = new \App\RoomMaintenance($data);
            }
            $maintenance->save();
            if (isset($data['pictures'])) {
                $maintenance->storePictures($data['pictures']);
            }

            return $maintenance;
        }, $maintenances);

        $maintenances = $room->roomMaintenances()->saveMany($maintenances);

        self::notifyMaintenanceBuilder($room, $data);

        return $room->update($data);
    }

    /**
     * 狀態改動時(room_status)，如果有對應的維修清潔，不是處於『案件完成』或『已取消』的狀態，則通知該清潔維修的建立者
     * @param Room  $room
     * @param array $data
     */
    private static function notifyMaintenanceBuilder(Room $room, array $data)
    {
        if ($data['room_status'] !== $room->room_status) {
            $tenantContractIds = collect($room->tenantContracts)->pluck('id')->toArray();
            $maintenances      = Maintenance::whereIn('tenant_contract_id', $tenantContractIds)
                ->where('status', '<>', '案件完成')
                ->where('status', '<>', '已取消')
                ->get();

            if (collect($maintenances)->count() > 0) {
                collect($maintenances)->each(function ($maintenance, $key) use ($room, $data) {
                    /** @var User $builder */
                    $builder = User::find($maintenance->commissioner_id);
                    $builder->notify(
                        new TextNotify(
                            '房代碼'.$room->room_code.'狀態已從'.$room->room_status.'改變成'.$data['room_status']
                        )
                    );
                });
            }
        }
    }

    // alias to make, but do it with custom data
    public static function makeEmptyRoom($building)
    {
        return Room::make([
            'building_id' => $building->id,
            'room_layout' => '公區',
            'virtual_account' => 'ffffffff',
            'room_number' => '100',
            'room_code' => '系統產生',
            'internet_form' => '無網路',
            'internet_form' => '無網路',
            'management_fee_mode' => '固定',
            'management_fee' => '0',
            'wifi_account' => 'admin',
            'wifi_password' => 'admin',
        ]);
    }

    public function belongsToBuilding($room, $building)
    {
        $building->rooms()->save($room);
    }
}
