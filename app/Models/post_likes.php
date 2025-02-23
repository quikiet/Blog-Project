<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_likes extends Model
{
    /** @use HasFactory<\Database\Factories\PostLikesFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsTo(posts::class);
    }



}
