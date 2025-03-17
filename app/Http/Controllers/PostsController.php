<?php

namespace App\Http\Controllers;

use App\Models\posts;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use AuthorizesRequests;

    public function index()
    {

        $query = posts::with('posts_user');

        if (auth()->check() && auth()->user()->role === 'admin') {
            return response()->json($query->get());
        }

        return response()->json($query->where('status', 'published')->get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', posts::class);
            $validateFields = $request->validate([
                'title' => "required|max:255",
                'content' => "required",
                'summary' => 'nullable|max:255',
                'thumbnail' => 'nullable | string',
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id'
            ]);

            // $validateFields['user_id'] = Auth::id();

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


    public function show($id)
    {
        try {
            $post = posts::findOrFail($id);
            $this->authorize('view', $post);
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
            $post = posts::findOrFail($id);
            $this->authorize('update', $post);
            $validateFields = $request->validate([
                'title' => "required|max:255",
                'content' => "required",
                'summary' => 'nullable|max:255',
                'thumbnail' => 'nullable | string',
                'status' => "required",
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id'
            ]);

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
            $this->authorize('delete', $post);
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
