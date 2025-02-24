<?php

namespace App\Http\Controllers;

use App\Models\comments;
use App\Http\Requests\StorecommentsRequest;
use App\Http\Requests\UpdatecommentsRequest;
use Exception;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $comment = comments::all();
            return response()->json($comment, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Fail",
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
            $validateFields = $request->validate([
                'content' => 'required',
                'post_id' => 'required|integer|exists:posts,id',
                'user_id' => 'required|integer|exists:users,id',
                'parent_id' => 'nullable|integer|exists:comments,id'
            ]);

            $comment = comments::create($validateFields);
            return response()->json($comment, 201);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Fail",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $comment = comments::findOrFail($id);
            return response()->json($comment, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Fail",
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
            $comment = comments::findOrFail($id);
            $validateFields = $request->validate([
                'content' => 'required',
                'post_id' => 'required|integer|exists:posts,id',
                'user_id' => 'required|integer|exists:users,id',
                'parent_id' => 'nullable|integer|exists:comments,id'
            ]);
            $comment->update($validateFields);
            return response()->json($comment, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Fail",
                "error" => $e->getMessage()
            ], 500); // Return status 500 for server errors
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = comments::findOrFail($id);

            $comment->delete();
            return response()->json($comment, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Fail",
                "error" => $e->getMessage()
            ], 500); // Return status 500 for server errors
        }
    }
}
