<?php

namespace App\Http\Controllers;

use App\Models\posts;
use App\Http\Requests\StorepostsRequest;
use App\Http\Requests\UpdatepostsRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $post = posts::all();
        return $post;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateFields = $request->validate([
                'title' => "required|max:255",
                'content' => "required",
                'summary' => 'nullable|max:255',
                'thumbnail' => 'nullable | string',
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id'
            ]);

            if ($request->hasFile('thumbnail')) {
                $validateFields['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }
            $post = posts::create($validateFields);

            return $post;
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'images/' . $fileName; // Lưu trong storage/app/public/images
            Storage::disk('public')->put($filePath, file_get_contents($file));

            return response()->json([
                'link' => asset('storage/' . $filePath) // Trả về URL ảnh đã lưu
            ]);
        }

        return response()->json(['error' => 'Không có file trong request'], 400);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $post = posts::findOrFail($id);
            return $post;
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validateFields = $request->validate([
                'title' => "required|max:255",
                'content' => "required",
                'summary' => 'nullable|max:255',
                'thumbnail' => 'nullable | string',
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id'
            ]);

            $post = posts::findOrFail($id);
            $post->update($validateFields);
            return $post;
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $post = posts::findOrFail($id);
            $post->delete();
            return $post;
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
