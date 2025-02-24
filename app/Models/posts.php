<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class posts extends Model
{
    /** @use HasFactory<\Database\Factories\PostsFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'summary',
        'thumbnail',
        'published_at',
        'category_id',
        'user_id'
    ];

    public function posts_user()
    {
        return $this->belongsTo(User::class);
    }

    public function post_likes()
    {
        return $this->hasMany(post_likes::class);
    }

    public function category()
    {
        return $this->belongsTo(categories::class);
    }

    public function post_tag()
    {
        return $this->hasMany(post_tag::class);
    }

    public function posts_comments()
    {
        return $this->hasMany(comments::class);
    }

    public function tags()
    {
        return $this->belongsToMany(tags::class, 'post_tags', 'post_id', 'tag_id');
    }



}
