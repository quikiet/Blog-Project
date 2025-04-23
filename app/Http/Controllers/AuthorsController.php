<?php

namespace App\Http\Controllers;

use App\Models\authors;
use App\Http\Requests\StoreauthorsRequest;
use App\Http\Requests\UpdateauthorsRequest;
use Exception;
use Illuminate\Http\Request;
use Str;

class AuthorsController extends Controller
{
    private function generateSlugUnique(string $name, ?int $excludeId = null)
    {
        $index = 1;
        $slug = Str::slug($name);
        $originSlug = $slug;

        while (true) {
            $query = authors::where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            $exists = $query->exists();

            if (!$exists) {
                return $slug;
            }

            $slug = $originSlug . '-' . $index;
            $index++;
        }
    }

    public function index()
    {
        $authors = authors::with('posts')->get();
        return response()->json($authors, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateFields = $request->validate([
                'name' => 'required|string|max:255',
                'bio' => 'nullable',
                'email' => 'required|email|unique:authors,email',
                'avatar' => 'nullable',
            ]);

            $validateFields['slug'] = $this->generateSlugUnique($validateFields['name']);
            // $validateFields['avatar'] = "https://res.cloudinary.com/djk2ys41m/image/upload/v1742972953/lvyrjwewxzjlht1leiqi.jpg";
            $author = authors::create($validateFields);

            return response()->json([
                'message' => 'Author created successfully',
                'data' => $author
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $author = authors::with('posts')->where('slug', $slug)->firstOrFail();
        return response()->json($author, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        try {
            $validateFields = $request->validate([
                'name' => 'required|string|max:255',
                'bio' => 'nullable',
                'email' => 'required|email|unique:authors,email,' . $slug . ',slug',
                'avatar' => 'nullable|string|max:255',
            ]);

            $author = authors::where('slug', $slug)->firstOrFail();
            $validateFields['slug'] = $this->generateSlugUnique($validateFields['name'], $author->id);

            $author->update($validateFields);
            return response()->json([
                'message' => 'Author updated successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Lỗi cập nhật tác giả",
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
            $author = authors::where('slug', $slug)->firstOrFail();
            $author->delete();
            return response()->json([
                'message' => 'Author is deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi xoá tác giả!",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restore($slug)
    {
        try {
            $author = authors::withTrashed()->where('slug', $slug);
            $author->restore();
            return response()->json(['message' => 'Tác giả đã được khôi phục']);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi khôi phục tác giả!",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete($slug)
    {
        try {
            $author = authors::withTrashed()->where('slug', $slug)->firstOrFail();
            $author->forceDelete();
            return response()->json(['message' => 'Tác giả đã bị xoá vĩnh viễn'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi xoá vĩnh viễn tác giả!",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDeletedAuthors()
    {
        return authors::onlyTrashed()->get();
    }

    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'slugs' => 'required|array',
                'slugs.*' => 'required|string',
            ]);

            $slugs = $validated['slugs'];

            // Kiểm tra các slugs tồn tại
            $existingSlugs = authors::whereIn('slug', $slugs)->pluck('slug')->toArray();
            $notFoundSlugs = array_diff($slugs, $existingSlugs);

            if (!empty($notFoundSlugs)) {
                return response()->json([
                    'message' => 'Một số tác giả không tồn tại',
                    'not_found' => $notFoundSlugs,
                ], 404);
            }

            if (empty($existingSlugs)) {
                return response()->json([
                    'message' => 'Không có tác giả nào để xóa',
                ], 404);
            }

            $deleteCount = authors::whereIn('slug', $slugs)->delete();

            return response()->json([
                'message' => "Đã xóa thành công $deleteCount tác giả",
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Đsaã xảy ra lỗi khi xóa tác giả!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
