<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_views extends Model
{
    /** @use HasFactory<\Database\Factories\PostViewsFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'viewed_at',
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
