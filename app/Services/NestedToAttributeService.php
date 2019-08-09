<?php

namespace App\Services;

use App\ContactInfo;
use Illuminate\Database\Eloquent\Collection;

class NestedToAttributeService
{
    // Pluck contactInfos and set as model's attribute
    public static function contactInfoToAttribute(Collection $models)
    {
        // Init types
        $info_types = ContactInfo::pluck('info_type');

        foreach ($models as $model_key => $model) {
            // Init attribute
            foreach ($info_types as $type_key => $type) {
                $model[$type] = "";
            }
            // Set attribute
            foreach ($model->contactInfos as $info_key => $contactInfo) {
                $model[$contactInfo->info_type] .= $contactInfo->value . ",";
            }
            // Remove nested attribute
            unset($model['contactInfos']);
        }

        return $models;
    }
}
