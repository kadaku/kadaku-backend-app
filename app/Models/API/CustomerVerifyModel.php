<?php

namespace App\Models\API;

use App\Models\AuthModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVerifyModel extends Model
{
    use HasFactory;

    protected $table = "m_customers_verify";
  
    protected $fillable = [
        'customer_id',
        'token',
    ];
}
