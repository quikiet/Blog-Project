<?php

namespace App\Http\Controllers;

use App\Models\post_tag;
use App\Http\Requests\Storepost_tagRequest;
use App\Http\Requests\Updatepost_tagRequest;

class PostTagController extends Controller
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
    public function store(Storepost_tagRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(post_tag $post_tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatepost_tagRequest $request, post_tag $post_tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post_tag $post_tag)
    {
        //
    }
}
