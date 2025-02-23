<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post_tag extends Model
{
    /** @use HasFactory<\Database\Factories\PostTagFactory> */
    use HasFactory;

    protected $fillable = [
        'post_id',
        'tag_id'
    ];

    public function posts()
    {
        return $this->belongsTo(posts::class);
    }

    public function tags()
    {
        return $this->belongsTo(tags::class);
    }




}
