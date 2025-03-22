<?php

namespace App\Http\Controllers;

use App\Models\post_views;
use App\Http\Requests\Storepost_viewsRequest;
use App\Http\Requests\Updatepost_viewsRequest;

class PostViewsController extends Controller
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
    public function store(Storepost_viewsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(post_views $post_views)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatepost_viewsRequest $request, post_views $post_views)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post_views $post_views)
    {
        //
    }
}
