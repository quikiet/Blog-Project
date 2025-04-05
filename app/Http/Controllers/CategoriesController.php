<?php

namespace App\Http\Controllers;

use App\Models\categories;
use App\Http\Requests\StorecategoriesRequest;
use App\Http\Requests\UpdatecategoriesRequest;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Str;
use function PHPUnit\Framework\throwException;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', categories::class);

        try {
            $category = categories::all();
            return $category;
        } catch (Exception $e) {
            return response()->json([
                "message" => "error",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $this->authorize('create', categories::class);

            $validateFields = $request->validate([
                'name' => 'required | max:255|unique:categories,name',
            ]);

            $validateFields['slug'] = Str::slug($validateFields['name']);

            $category = categories::create($validateFields);

            return $category;
        } catch (Exception $e) {
            return response()->json([
                $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {

            // $this->authorize('view', categories::class);

            $post = categories::with('categories_posts')->where('slug', $slug)->first();

            if (!$post) {
                return response()->json([
                    'message' => 'Category not found.'
                ], 404);
            }

            return response()->json($post, 200);
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
            $categories = categories::where('slug', $slug)->firstOrFail();
            $this->authorize('update', $categories);
            $validateFields = $request->validate([
                'name' => 'required|unique:categories,name',
            ]);

            if (!$categories) {
                return response()->json(['message' => 'Danh mục không tồn tại!'], 404);
            }

            $validateFields['slug'] = Str::slug($validateFields['name']);

            $categories->update($validateFields);

            return response()->json([
                'message' => 'Cập nhật danh mục thành công!',
                'category' => $categories
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi cập nhật danh mục!",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        try {
            $categories = categories::where('slug', $slug)->firstOrFail();
            $this->authorize('delete', $categories);
            $categories->delete();
            return ["message" => "delete"];
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi xoá danh mục!",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
