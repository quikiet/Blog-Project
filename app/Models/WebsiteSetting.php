<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    /** @use HasFactory<\Database\Factories\WebsiteSettingFactory> */
    use HasFactory;
    protected $fillable = [
        'site_title',
        'site_slogan',
        'logo_url',
        'contact_address',
        'contact_phone',
        'contact_email',
        'social_links',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'footer_copyright',
        'footer_links'
    ];

    protected $casts = [
        'social_links' => 'array',
        'footer_links' => 'array'
    ];
}
