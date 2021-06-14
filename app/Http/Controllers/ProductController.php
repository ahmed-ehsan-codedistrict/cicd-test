<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Tags;
use App\User;
use App\Utilities\Helpers;
use  Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request  $request)
    {
        // $params = (array) json_decode(file_get_contents('php://input'), TRUE);
        $params = (array) json_decode($request->getContent(), true);
        // $productId = isset($params['productId']) ? $params['productId'] : "";
        // $Products = Product::getProducts($params,  $productId);

        $userId =  Auth::id();
        $companyId = Auth::user()->CompanyNo;
        $divisions = [];

        //get User Divisions
        foreach (User::find(Auth::id())->Divisions->pluck('DivisionNo') as $d) {
            $divisions[] = $d;
        };

        //return $divisions;

        $groupId =  $request->groupId;

        //Skip Products
        $skipProducts =  isset($request->skip) && $request->skip > 0 ? $request->skip : 0;
        $workSpace = 0;
        $productArrWithColors = null;
        $lineSheetIdForExport = 0;

         $Products = Product::getProducts(
            $userId,
            $companyId,
            $params,
            $divisions,
            $groupId,
            $workSpace,
            $productArrWithColors,
            $lineSheetIdForExport,
            $skipProducts
        );


        // Converting data into JSON
        foreach ($Products as $key => $value) {


            $tempQAArr =  array();     //temp array for Qeury Attributes
            $tempUPArr =  array();     // temp array for UPCX info
            $tempCCArr =  array();     // temp array for color code
            $tempTagArr  =  array();   // temp array for private tags
            $tempPTagArr =  array();   // temp array for public tags

            /**
             *Making Quality Attributes array
             *---Format-----
             *"QualityAttributes": {
             *   "AttribName": "AttribVal",
             * }
             */
            if (isset($value->QueryAttributes)) {

                $tempQA = explode('|', $value->QueryAttributes);
                foreach ($tempQA as $val) {
                    $tempVal = explode(':', $val);
                    if (isset($tempInVal) && count($tempInVal) > 0) {
                        $tempQAArr[$tempVal[0]] =  $tempVal[1];
                    }
                }
                $value->QueryAttributes = $tempQAArr;
            }
            /*
                    Making Color Code Array
                    "ColorCodeName": [
                                {
                                    "ColorCode": "ADBLD",
                                }
                            ]
                    */

            if (isset($value->ColorCodeName)) {
                $tempCC = explode('|', $value->ColorCodeName);

                foreach ($tempCC as $key => $val) {

                    $tempVal = explode('-', $val);

                    $tempInnerCCArr =  array();
                    foreach ($tempVal as $key => $v) {
                        $tempInVal = explode(':', $v);
                        if (isset($tempInVal) && count($tempInVal) > 0) {
                            $tempInnerCCArr[$tempInVal[0]] = isset($tempInVal[1]) ? $tempInVal[1] : "";
                        }
                    }
                    $tempCCArr[] = $tempInnerCCArr;
                }
            }
            $value->ColorCodeName = $tempCCArr;

            // $value->ColorCodeName = $tempCCArr;

            /*
               Making UPC into Array
                "UPCXInfo": {
                  "652874045788": "210",
                }
             */
            $tempUP = explode('|', $value->UPCXInfo);
            foreach ($tempUP as $key => $val) {

                $tempVal = explode('-', $val);
                if (isset($tempInVal) && count($tempInVal) > 0) {
                    $tempUPArr[$tempVal[0]] =   isset($tempInVal[1]) ? $tempInVal[1] : "";
                }
            }

            $value->UPCXInfo =  $tempUPArr;




            /** Making  Tags Json */

            $TagsArray = explode('||', $value->Tags);

            $tempTag = explode(',', $TagsArray[0]);

            // $tempTag = explode(',',$value->PrivateTags);

            if (isset($tempTag) && count($tempTag) > 0  && isset($tempTag[0]) && !empty($tempTag[0])) {
                foreach ($tempTag as $key => $val) {
                    $tempVal = explode('/', $val);
                    if (isset($tempVal) && count($tempVal) > 0) {

                        $tempAssignArr =  array("id" => $tempVal[0], "name" => $tempVal[1]);
                        if (!in_array($tempAssignArr, $tempTagArr)) {
                            $tempTagArr[] = $tempAssignArr;
                        }
                    }
                }
            }
            $value->PrivateTags = $tempTagArr;


            /** Making Public Tags Json */

            $tempTag = explode(',', $TagsArray[1]);
            //  $tempTag = explode(',',$value->PublicTags);
            if (isset($tempTag) && count($tempTag) > 0 && isset($tempTag[0]) && !empty($tempTag[0])) {
                foreach ($tempTag as $key => $val) {
                    $tempVal = explode('/', $val);
                    if (isset($tempVal) && count($tempVal) > 0) {
                        // print_r($tempVal);
                        $tempAssignArr =  array("id" => $tempVal[0], "name" => $tempVal[1]);
                        if (!in_array($tempAssignArr, $tempPTagArr)) {
                            $tempPTagArr[] = $tempAssignArr;
                        }
                    }
                }
            }

            $value->PublicTags = $tempPTagArr;
        }

        /** send response to api */
        return response()->json(['Products' => $Products]);
    }

    public function getDetail(Request $request)
    {
        $request->validate([
            'ProductId' => 'string|required|exists:PRHDMS0,Style'
        ]);
        try {
            return Product::getProductDetail($request->ProductId);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    //product arrtibutes to display in fields dropdown using for export functionality
    public function attributes(Request $request)
    {
        try {
            return Product::attributes();
        } catch (\Throwable $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }
}
