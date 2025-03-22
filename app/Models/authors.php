<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class authors extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'email',
        'avatar',
    ];

    public function posts()
    {
        return $this->hasMany(posts::class, 'author_id');
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }

}
