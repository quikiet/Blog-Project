<?php

namespace App\Http\Controllers;

use App\Models\WebsiteSetting;
use App\Http\Requests\StoreWebsiteSettingRequest;
use App\Http\Requests\UpdateWebsiteSettingRequest;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{

    public function index()
    {
        $settings = WebsiteSetting::first();

        if (!$settings) {
            return response()->json([
                'data' => null,
                'message' => 'No settings found'
            ], 404);
        }

        return response()->json([
            'data' => $settings,
            'message' => 'Settings retrieved successfully'
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_title' => 'sometimes|string|max:255',
            'site_slogan' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url|max:255',
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required_with:social_links|string',
            'social_links.*.url' => 'required_with:social_links|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'footer_copyright' => 'nullable|string',
            'footer_links' => 'nullable|array',
            'footer_links.*.text' => 'required_with:footer_links|string',
            'footer_links.*.url' => 'required_with:footer_links|url'
        ]);

        $settings = WebsiteSetting::firstOrNew();
        $settings->fill($validated);
        $settings->save();

        return response()->json([
            'data' => $settings,
            'message' => 'Settings updated successfully'
        ]);
    }

}
