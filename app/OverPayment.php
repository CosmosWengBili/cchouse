<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Audit Model
 *
 * In order to prevent from infinite callback call,
 * please DO NOT add audit trait and contract to this model.
 */
class OverPayment extends Model
{
}