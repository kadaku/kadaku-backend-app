<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationsModel extends Model
{
    use HasFactory;

    protected $table = "t_invitations";
    protected $guarded = [];
}
