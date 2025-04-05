<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class categories extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriesFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'name',
        'slug'
    ];

    public function categories_posts()
    {
        return $this->hasMany(posts::class, 'category_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }


}
