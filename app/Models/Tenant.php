<?php

namespace App\Models;

use Eloquent as Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Tenant
 * @package App\Models
 * @version October 30, 2019, 8:41 pm CST
 *
 * @property \App\Models\User confirmBy
 * @property \Illuminate\Database\Eloquent\Collection tenantContracts
 * @property string name
 * @property string certificate_number
 * @property boolean is_legal_person
 * @property string line_id
 * @property string residence_address
 * @property string company
 * @property string job_position
 * @property string company_address
 * @property integer confirm_by
 * @property string confirm_at
 * @property string|\Carbon\Carbon created_at
 * @property string|\Carbon\Carbon updated_at
 * @property string|\Carbon\Carbon deleted_at
 * @property string birth
 */
class Tenant extends Model implements AuditableContract
{
    use AuditableTrait;
    use SoftDeletes;

    public $table = 'tenants';
    
    public $timestamps = false;


    protected $dates = ['deleted_at'];



    protected $guarded = [];

    protected $fillable = [
        'name',
        'certificate_number',
        'is_legal_person',
        'line_id',
        'residence_address',
        'company',
        'job_position',
        'company_address',
        'confirm_by',
        'confirm_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'birth'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'certificate_number' => 'string',
        'is_legal_person' => 'boolean',
        'line_id' => 'string',
        'residence_address' => 'string',
        'company' => 'string',
        'job_position' => 'string',
        'company_address' => 'string',
        'confirm_by' => 'integer',
        'confirm_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'birth' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'certificate_number' => 'required',
        'is_legal_person' => 'required',
        'line_id' => 'required',
        'residence_address' => 'required',
        'company' => 'required',
        'job_position' => 'required',
        'company_address' => 'required',
        'birth' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function confirmBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirm_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function tenantContracts()
    {
        return $this->hasMany(\App\Models\TenantContract::class, 'tenant_id');
    }
}
