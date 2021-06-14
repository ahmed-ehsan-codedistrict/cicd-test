<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\CUSTMS0;

class CUSTMS0Controller extends Controller
{
    public function getCustomerListing(Request $request)
    {
        try {
            return CUSTMS0::getCustomerListing();
        } catch (\Throwable $th) {
            $err = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return $err;
        }

    }
}
