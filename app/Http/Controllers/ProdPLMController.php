<?php

namespace App\Http\Controllers;

use App\Models\ProdPLM;
use App\Models\UserBrand;
use Illuminate\Http\Request;
use DB;

class ProdPLMController extends Controller
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

    public function getAll()
    {
        $markets = ProdPLM::select('Market')->groupBy('Market');

        return response()->json(['Markets'=>$markets]);

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
     * @param  \App\ProdPLM  $prodPLM
     * @return \Illuminate\Http\Response
     */
    public function show(ProdPLM $prodPLM)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProdPLM  $prodPLM
     * @return \Illuminate\Http\Response
     */
    public function edit(ProdPLM $prodPLM)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProdPLM  $prodPLM
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProdPLM $prodPLM)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProdPLM  $prodPLM
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProdPLM $prodPLM)
    {
        //
    }

    public function getBrands(Request $request)
    {
        try{
            return response()->json(UserBrand::getUserBrands());
        } catch(\Error $e)
        {
            return response()->json(['message'=>'Something went wrong', 'success'=>false,'error'=>$e->getMessage()],400);
        }
    }
}
