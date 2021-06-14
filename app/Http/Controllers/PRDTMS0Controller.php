<?php

namespace App\Http\Controllers;

use App\Models\PRDTMS0;
use Illuminate\Http\Request;

class PRDTMS0Controller extends Controller
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

    public function getProductColors(Request $request)
    {
        //validating inputs
        $request->validate([
            'ProductId' => 'string'
        ]);
        return response()->json(PRDTMS0::getColors($request->ProductId));
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
     * @param  \App\PRDTMS0  $pRDTMS0
     * @return \Illuminate\Http\Response
     */
    public function show(PRDTMS0 $pRDTMS0)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PRDTMS0  $pRDTMS0
     * @return \Illuminate\Http\Response
     */
    public function edit(PRDTMS0 $pRDTMS0)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PRDTMS0  $pRDTMS0
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PRDTMS0 $pRDTMS0)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PRDTMS0  $pRDTMS0
     * @return \Illuminate\Http\Response
     */
    public function destroy(PRDTMS0 $pRDTMS0)
    {
        //
    }
}
