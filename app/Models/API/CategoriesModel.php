<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesModel extends Model
{
    use HasFactory;

    protected $table = "m_invitation_categories";
  
    protected $fillable = [
        'title', 'slug', 'icon', 'meta_title', 'meta_description', 'is_active'
    ];
}
