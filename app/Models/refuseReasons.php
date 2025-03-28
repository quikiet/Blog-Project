<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refuseReasons extends Model
{
    /** @use HasFactory<\Database\Factories\RefuseReasonsFactory> */
    use HasFactory;

    protected $fillable = [
        'reason'
    ];

    public function refuses()
    {
        return $this->hasMany(refuses::class, 'reason_id');
    }

}
