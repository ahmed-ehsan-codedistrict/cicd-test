<?php

namespace App\Http\Controllers;

use App\Models\StyleAvail;
use Illuminate\Http\Request;

class StyleAvailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAvailability(Request $request)
    {
        //validating inputs
        $request->validate([
            'type' => 'required',
        ]);
        return response(StyleAvail::getAvailable($request->type));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\StyleAvail  $styleAvail
     * @return \Illuminate\Http\Response
     */
    public function show(StyleAvail $styleAvail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StyleAvail  $styleAvail
     * @return \Illuminate\Http\Response
     */
    public function edit(StyleAvail $styleAvail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StyleAvail  $styleAvail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StyleAvail $styleAvail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StyleAvail  $styleAvail
     * @return \Illuminate\Http\Response
     */
    public function destroy(StyleAvail $styleAvail)
    {
        //
    }
}
