<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\DivLogo;
use Illuminate\Support\Facades\Auth;
class DivLogoController extends Controller
{
    //

    public function getAll(Request $request)
    {
        try {
            $divisions =  User::userDivisions();
            return DivLogo::userDivisionLogo($divisions);
        } catch (\Throwable $th) {
            return response()->json(['message'=>'Something went wrong', 'success'=>false,'error'=>$th->getMessage()],400);
        }
    }
}
