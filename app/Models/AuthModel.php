<?php

namespace App\Models;

use App\Models\API\CustomerSocialModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class AuthModel extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SanctumHasApiTokens, Notifiable;

    protected $table = "m_customers";
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone_code',
        'phone_dial_code',
        'phone',
        'photo',
        'photo_ext',
        'avatar',
        'is_active',
        'remember_token',
    ];
    protected $hidden = [
        'password', 
        'remember_token',
    ];
    protected $cast = [
        'email_verified_at' => 'datetime',
    ];

    function social()
    {
        return $this->hasMany(CustomerSocialModel::class, 'customer_id', 'id');
    }

    function hasSocialLinked($service)
    {
        return $this->social->where('service_name', $service)->count();
    }
}
