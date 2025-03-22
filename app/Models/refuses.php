<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refuses extends Model
{
    /** @use HasFactory<\Database\Factories\RefusesFactory> */
    use HasFactory;

    protected $fillalble = [
        'post_id',
        'reason_id'
    ];

    public function posts()
    {
        return $this->belongsTo(posts::class);
    }

    public function refuseReason()
    {
        return $this->belongsTo(refuseReasons::class, 'reason_id', 'id');
    }


}
