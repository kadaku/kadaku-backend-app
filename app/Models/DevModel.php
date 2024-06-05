<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevModel extends Model
{
    use HasFactory;

    protected $table = "m_musics";
	protected $fillable = ["temp_id", "name", "file", "file_url", "categories"];
}
