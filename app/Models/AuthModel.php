<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Illuminate\Foundation\Auth\AuthModel as Authenticatable;


class AuthModel extends Authenticatable
{
    use HasFactory, SanctumHasApiTokens, Notifiable;

    protected $table = "m_customers";
    protected $guarded = [];
}
