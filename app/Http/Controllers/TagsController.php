<?php

namespace App\Http\Controllers;

use App\Models\tags;
use App\Http\Requests\StoretagsRequest;
use App\Http\Requests\UpdatetagsRequest;

class TagsController extends Controller
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
    public function store(StoretagsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(tags $tags)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetagsRequest $request, tags $tags)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tags $tags)
    {
        //
    }
}
