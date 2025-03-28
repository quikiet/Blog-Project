<?php

namespace App\Http\Controllers;

use App\Models\posts;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Str;
class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $query = posts::with('posts_user', 'authors');

        if ($user && $user->role === 'admin') {
            $posts = $query->get();
        } else if ($user && $user->role === 'author') {
            $posts = $query->where('user_id', $user->id)->orWhere('status', 'published')->get();
        } else {
            $posts = $query->where('status', 'published')->get();
        }

        return response()->json($posts);
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
                'status' => 'required|in:draft,pending,published,scheduled,archived,rejected, deleted',
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id',
                'author_id' => 'nullable|exists:authors,id'
            ]);

            $validateFields['slug'] = Str::slug($validateFields['title']);

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


    public function show($slug)
    {
        try {
            $post = posts::where('slug', $slug)->firstOrFail();
            $this->authorize('view', $post);
            return response()->json($post);
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
    public function update(Request $request, $slug)
    {
        try {
            $post = posts::where('slug', $slug)->firstOrFail();
            $this->authorize('update', $post);
            $validateFields = $request->validate([
                'title' => "required|max:255",
                'content' => "required",
                'summary' => 'nullable|max:255',
                'thumbnail' => 'nullable | string',
                'status' => "required",
                'published_at' => 'nullable|date',
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id',
                'author_id' => 'nullable|exists:authors,id'
            ]);

            $validateFields['slug'] = Str::slug($validateFields['title']);

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
    public function destroy($slug)
    {
        try {
            $post = posts::where('slug', $slug)->firstOrFail();
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

    public function userPosts($slug)
    {
        try {
            $posts = posts::where('slug', $slug)
                ->with(['refuses.refuseReason'])
                ->get();

            return response()->json($posts, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
