<?php

namespace App\Models\API;

use App\Models\AuthModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSocialModel extends Model
{
    use HasFactory;

    protected $table = "m_customers_social";
  
    protected $fillable = [
        'customer_id',
        'service_id',
        'service_name',
    ];

    function user()
    {
        return $this->hasOne(AuthModel::class, 'id', 'customer_id');
    }
}
