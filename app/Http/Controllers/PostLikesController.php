<?php

namespace App\Http\Controllers;

use App\Models\post_likes;
use App\Http\Requests\Storepost_likesRequest;
use App\Http\Requests\Updatepost_likesRequest;

class PostLikesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storepost_likesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(post_likes $post_likes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatepost_likesRequest $request, post_likes $post_likes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post_likes $post_likes)
    {
        //
    }
}
