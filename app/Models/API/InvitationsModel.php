<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationsModel extends Model
{
    use HasFactory;

    protected $table = "t_invitations";
  
    protected $fillable = [
        'customer_id',
        'category_id',
        'theme_id',
        'domain',
    ];
}
