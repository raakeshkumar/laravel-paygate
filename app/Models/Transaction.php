<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'paygate_id',
        'pay_request_id',
        'reference',
        'transaction_status',
        'result_code',
        'auth_code',
        'currency',
        'amount',
        'result_desc',
        'transaction_id',
        'risk_indicator',
        'pay_method',
        'pay_method_detail',
        'vault_id',
        'payvault_data_1',
        'payvault_data_2',
        'checksum'
    ];
}
