<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments extends Model
{
    /** @use HasFactory<\Database\Factories\CommentsFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'post_id',
        'user_id',
        'parent_id'
    ];

    public function comments_user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments_posts()
    {
        return $this->belongsTo(posts::class);
    }

    public function comments_comments()
    {
        return $this->hasMany(comments::class);
    }


}
