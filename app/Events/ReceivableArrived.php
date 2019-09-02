<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\TenantContract;

class ReceivableArrived
{
    use Dispatchable, SerializesModels;

    public $tenantContract;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TenantContract $tenantContract, $data)
    {
        $this->tenantContract = $tenantContract;
        $this->data = $data;
    }

}
