<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Repositories\BaseRepository;

/**
 * Class TenantRepository
 * @package App\Repositories
 * @version October 30, 2019, 8:41 pm CST
*/

class TenantRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Tenant::class;
    }
}
