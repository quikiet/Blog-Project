<?php

namespace App\Http\Controllers;

use App\Models\post_tag;
use App\Http\Requests\Storepost_tagRequest;
use App\Http\Requests\Updatepost_tagRequest;
use App\Models\posts;
use Exception;
use Illuminate\Http\Request;

class PostTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $postTag = post_tag::all();
            return response()->json($postTag, 200);
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
                'tag_id' => "required|exists:tags,id"
            ]);

            $existingTag = post_tag::where('post_id', $validateFields['post_id'])
                ->where('tag_id', $validateFields['tag_id'])->first();

            if ($existingTag) {
                return response()->json(["message" => "Tag already exists in this post!"], 409);//409 = conflict
            }

            $postTag = post_tag::create($validateFields);
            return response()->json($postTag, 201);
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
            $postTag = post_tag::findOrFail($id);
            return response()->json($postTag, 200);
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
                'tag_id' => "required|exists:tags,id"
            ]);
            $postTag = post_tag::findOrFail($id);
            $postTag->update($validateFields);
            return response()->json($postTag, 200);
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
                'tag_id' => "required|exists:tags,id"
            ]);

            $postTag = post_tag::where('post_id', $validateFields['post_id'])
                ->where('tag_id', $validateFields['tag_id'])->first();

            if (!$postTag) {
                return response()->json(["message" => "This tag is not found!"], 404);
            }

            $postTag->delete();
            return response()->json($postTag, 200);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getTagsByPost($postId)
    {
        try {
            $post = posts::with('tags')->findOrFail($postId);
            return response()->json([
                "post_id" => $postId,
                "tags_count" => $post->tags->count(),
                "tags" => $post->tags
            ], 200);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }

}
