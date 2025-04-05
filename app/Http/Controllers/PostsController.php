<?php

namespace App\Http\Controllers;

use App\Mail\PostStatusChanged;
use App\Models\categories;
use App\Models\posts;
use Cache;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
            $posts = $query->orderByDesc('created_at')->get();
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

            $forbiddenWords = Cache::remember('forbidden_words', 3600, function () {
                return config('forbidden_words.words', []);
            });

            $content = $validateFields['content'];
            $normalizedContent = strtolower($this->removeVietnameseAccents($content));
            $foundForbiddenWords = [];

            foreach ($forbiddenWords as $word) {
                $normalizedWord = strtolower($this->removeVietnameseAccents($word));
                if (stripos($normalizedContent, $normalizedWord) !== false) {
                    $pattern = '/\b' . preg_quote($word, '/') . '\b/ui';
                    if (preg_match($pattern, $content)) {
                        $foundForbiddenWords[] = $word;
                    }
                }
            }

            $validateFields['slug'] = Str::slug($validateFields['title']);

            if ($request->hasFile('thumbnail')) {
                $validateFields['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            if (!empty($foundForbiddenWords)) {
                $validateFields['status'] = "deleted";
                $post = posts::create($validateFields);
                $post->load('posts_user');
                if ($post->posts_user) {
                    Mail::to($post->posts_user->email)->send(new PostStatusChanged($post, $post->posts_user));
                }
                return response()->json([
                    'message' => 'Bài viết chứa từ cấm: ' . implode(', ', $foundForbiddenWords),
                    'forbidden_words' => $foundForbiddenWords
                ], 201);
            } else {
                $post = posts::create($validateFields);
                if ($post->posts_user) {
                    Mail::to($post->posts_user->email)->send(new PostStatusChanged($post, $post->posts_user));
                }
                return response()->json([
                    $post
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    private function removeVietnameseAccents($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
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
            $post = posts::with('posts_user', 'authors', 'refuses')->where('slug', $slug)->firstOrFail();
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
            $oldStatus = $post->status;
            $post->update($validateFields);
            if ($oldStatus !== $post->status) {
                Mail::to($post->posts_user->email)->send(new PostStatusChanged($post, $post->posts_user));
            }
            return response()->json(['message' => 'Cập nhật bài viết thành công']);
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

    public function getFeaturedPost()
    {
        try {
            $post = posts::with(['posts_user', 'authors'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->first();

            if (!$post) {
                return response()->json(['message' => 'Không có bài viết nổi bật'], 404);
            }

            return response()->json($post, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching featured post: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết nổi bật'], 500);
        }
    }

    public function getSubFeatures()
    {
        try {
            $posts = posts::with(['posts_user', 'authors'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->skip(1)
                ->take(2)
                ->get();

            return response()->json($posts, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching sub features: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết phụ'], 500);
        }
    }

    public function getLatestPosts()
    {
        try {
            $posts = posts::with(['posts_user', 'authors'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->skip(4)
                ->take(8)
                ->get();

            return response()->json($posts, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching latest posts: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết mới nhất'], 500);
        }
    }

    public function getTrendingPosts()
    {
        try {
            $posts = posts::with(['posts_user', 'authors'])
                ->where('status', 'published')
                // ->orderBy('views', 'desc')
                ->take(3)
                ->get();

            return response()->json($posts, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching trending posts: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết thịnh hành'], 500);
        }
    }

    public function getArchivedPosts()
    {
        try {
            $posts = posts::with(['posts_user', 'authors'])
                ->where('status', 'archived')
                ->get();

            return response()->json($posts, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching archived posts: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết lưu trữ'], 500);
        }
    }

    public function getPendingPosts()
    {
        try {
            $posts = posts::with(['posts_user', 'authors'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json($posts, 200);
        } catch (Exception $e) {
            \Log::error("Error fetching pending posts: {$e->getMessage()}");
            return response()->json(['message' => 'Lỗi khi lấy bài viết đang chờ'], 500);
        }
    }

}
