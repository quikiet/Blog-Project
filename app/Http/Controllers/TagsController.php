<?php

namespace App\Http\Controllers;

use App\Models\tags;
use App\Http\Requests\StoretagsRequest;
use App\Http\Requests\UpdatetagsRequest;
use Exception;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tag = tags::all();
            return response()->json($tag, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
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
                'name' => "required|unique:tags"
            ]);
            $tag = tags::create($validateFields);
            return response()->json($tag, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $tag = tags::findOrFail($id);
            return response()->json($tag, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
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
                'name' => "required|unique:tags"
            ]);
            $tag = tags::findOrFail($id);
            $tag->update($validateFields);
            return response()->json($tag, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tag = tags::findOrFail($id);
            $tag->delete();
            return response()->json($tag, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
