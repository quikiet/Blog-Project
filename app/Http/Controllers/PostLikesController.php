<?php

namespace App\Http\Controllers;

use App\Models\post_likes;
use App\Http\Requests\Storepost_likesRequest;
use App\Http\Requests\Updatepost_likesRequest;
use Exception;
use Illuminate\Http\Request;

class PostLikesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $postLike = post_likes::all();
            return response()->json($postLike, 200);
        } catch (Exception $e) {
            return response()->json([
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
                'post_id' => "required|exists:posts,id",
                'user_id' => "required|exists:users,id"
            ]);

            $existingLike = post_likes::where('post_id', $validateFields['post_id'])
                ->where('user_id', $validateFields['user_id'])->first();
            if ($existingLike) {
                return response()->json(["message" => "You already liked this post", 409]); //409 = conflict
            }
            $postLike = post_likes::create($validateFields);
            return response()->json($postLike, 201);
        } catch (Exception $e) {
            return response()->json([
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
            $postLike = post_likes::findOrFail($id);
            return response()->json($postLike, 200);
        } catch (Exception $e) {
            return response()->json([
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
                'post_id' => "required|exists:posts,id",
                'user_id' => "required|exists:users,id"
            ]);
            $postLike = post_likes::findOrFail($id);
            $postLike->update($validateFields);
            return response()->json($postLike, 200);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $validateFields = $request->validate([
                'post_id' => "required|exists:posts,id",
                'user_id' => "required|exists:users,id"
            ]);

            $postLike = post_likes::where('post_id', $validateFields['post_id'])
                ->where('user_id', $validateFields['user_id'])
                ->first();

            if (!$postLike) {
                return response()->json(["message" => "Like not found"], 404);
            }

            $postLike->delete();
            return response()->json(["message" => "Unliked successfully"], 200);

        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

}
