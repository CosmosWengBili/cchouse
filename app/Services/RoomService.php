<?php

namespace App\Services;

use App\Maintenance;
use App\Notifications\TextNotify;
use App\Room;
use App\Appliance;
use App\User;

class RoomService
{
    public static function make($data)
    {
        return Room::make($data);
    }

    public static function create($data, $appiances)
    {
        $roomId = Room::insertGetId($data);
        $room = Room::find($roomId);
        $new_appliances = [];
        // generates all of appliances
        foreach ($appiances as $appiance) {
            $new_appliance = Appliance::make([
                'subject' => $appiance['subject'],
                'spec_code' => $appiance['spec_code'],
                'count' => $appiance['count'],
                'vendor' => $appiance['vendor'],
                'maintenance_phone' => $appiance['maintenance_phone'],
                'comment' => $appiance['comment']
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
     * @param Appliance $appliances
     *
     * @return mixed
     */
    public static function update($room, $data, $appliances)
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
                $id = $appliance['id'];
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
            $maintenances = Maintenance::whereIn('tenant_contract_id', $tenantContractIds)
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
    public static function makeEmptyRoom()
    {
        return Room::make([
            'room_layout' => '公用'
        ]);
    }

    public function belongsToBuilding($room, $building)
    {
        $building->rooms()->save($room);
    }
}
