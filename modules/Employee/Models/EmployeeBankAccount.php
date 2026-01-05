<?php

namespace Modules\Employee\Models;

use Modules\Support\Models\BaseModel;
use Modules\Support\Traits\ActivityLog;
use Modules\Support\Traits\Searchable;

class EmployeeBankAccount extends BaseModel
{
    use ActivityLog, Searchable;

    protected $table = 'employee_bank_accounts';

    protected $fillable = [
        'employee_id',
        'bank_name',
        'branch_name',
        'account_no',
        'account_name',
        'routing_no',
        'account_type',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
