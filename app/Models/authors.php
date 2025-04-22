<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class authors extends Model
{
    /** @use HasFactory<\Database\Factories\AuthorsFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'slug',
        'bio',
        'email',
        'avatar',
        'phone',
        'address'
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
