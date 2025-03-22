<?php

namespace App\Http\Controllers;

use App\Models\authors;
use App\Http\Requests\StoreauthorsRequest;
use App\Http\Requests\UpdateauthorsRequest;
use Exception;
use Illuminate\Http\Request;

class AuthorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $validateFields = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:authors,slug',
            'bio' => 'required|string|max:255',
            'email' => 'required|email|unique:authors,email',
            'avatar' => 'nullable|string|max:255',
        ]);
        $author = authors::create($validateFields);

        return response()->json([
            'message' => 'Author created successfully',
            'data' => $author
        ], 201);
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
                'slug' => 'required|string|max:255|unique:authors,slug',
                'bio' => 'required|string|max:255',
                'email' => 'required|email|unique:authors,email',
                'avatar' => 'nullable|string|max:255',
            ]);
            $author = authors::where('slug', $slug)->firstOrFail();
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
            ], 204);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Đã xảy ra lỗi khi xoá tác giả!",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
