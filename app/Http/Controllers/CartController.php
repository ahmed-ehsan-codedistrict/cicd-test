<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\LineSheetGroup;
use App\Models\LineSheetGroupProducts;
use App\Models\PRDTMS0;
use App\Models\PreOrderHdr;
use App\Models\Product;
use App\User;
use App\Utilities\Helpers;
use App\Models\PreOrderDtl;
use App\Models\PRHDMS0;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Test\CodeCleaner\NoReturnValueTest;

use function PHPSTORM_META\map;

class CartController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            '*.ProductId' => 'required|exists:PRHDMS0,Style',
            '*.ColorId' => 'required|exists:COLRMS0,Color'
        ]);

        try{
            foreach($request->all() as $r)
            {
                if(!PRDTMS0::where('style',$r['ProductId'])->where('Color',$r['ColorId'])->exists())
                {
                    return response()->json(['message' => "Something went wrong",'response' => 'Invalid Product Entered'], 400);
                }
            }
            $arr = $request->toArray();
            if($arr)
            {
                $Product = array();
              
                foreach($arr as $key => $a)
                {
                    foreach($a['ColorId'] as $k => $c)
                    {
                        $Product[] = array('ProductId'=>$a['ProductId'],'ColorId'=>$c,'UserId'=>Auth::id());
                    }
                }
                return response()->json(Cart::createCart($Product));
            }
            else
            {
                return response()->json(['response' => "Something went wrong", 'message' => "Invalid Data Entered"], 403);
            }
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'Quantity' => 'required|integer',
            'ProductId' => 'required|exists:PRHDMS0,Style',
            'ColorId' => 'required'
        ]);
        try{
            if(!Cart::where('ProductId',$request->ProductId)->where('Color',$request->ColorId)->exists())
            {
                return response()->json(['message' => "Something went wrong",'response' => 'No such product exists in cart'], 400);
            }
            $r = $request->toArray();
            Cart::updateCart($r['ProductId'], $r['ColorId'], $r['Quantity']);
            return response()->json(['response' => "Cart updated successfully"], 200);
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
    }

    public function deleteProductColor(Request $request)
    {
        $request->validate([
            'ProductId' => 'required|exists:PRHDMS0,Style',
            'ColorId' => 'required'
        ]);
        try{
            if(!Cart::where('ProductId',$request->ProductId)->where('ColorId',$request->ColorId)->where('UserId',Auth::id())->exists())
            {
                return response()->json(['message' => "Something went wrong",'response' => 'No such product exists'], 400);
            }
            $r = $request->toArray();
            Cart::DeleteCartProduct($r['ProductId'], $r['ColorId']);
            return response()->json(['response' => "Product Deleted successfully"], 200);
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
    }

    public function deleteProduct(Request $request)
    {
        $request->validate([
            'ProductId' => 'required|exists:PRHDMS0,Style',
        ]);
        try{
            if(!Cart::where('ProductId',$request->ProductId)->where('UserId',Auth::id())->exists())
            {
                return response()->json(['message' => "Something went wrong",'response' => 'No such product exists'], 400);
            }
            $r = $request->toArray();
            Cart::deleteProduct($r['ProductId']);
            return response()->json(['response' => "Product Deleted successfully"], 200);
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
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
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try{
            return response()->json(Cart::showAll());
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        try{
            if(Cart::where('UserId',Auth::id())->exists())
            {
                Cart::deleteAll();
                return response()->json(['message' => "Cart Deleted Successfully", 'success' => true, 'error' => null]);
            }
            else
            {
                return response()->json(['message' => "Nothing To Delete", 'success' => false, 'error' => 'Nothing to Delete']);
            }
        }
        catch(\Error $e)
        {
            return response()->json(['message' => "Something went wrong",'response' => $e->getMessage()], 400);
        }
    }

    public function addLineSheetToCart(Request $request)
    {
        $request->validate([
            'LinesheetId' => 'integer|required'
        ]);
        try {
            $groups = array_values(LineSheetGroup::getLineSheetGroups($request->LinesheetId)->toarray());
            foreach ($groups as $g) {
                $products = LineSheetGroupProducts::getGroupProducts($g['ID']);
                if ($products) {
                    foreach ($products as $p) {
                        Cart::updateOrInsertCart($p['ProductId'], $p['ColorId']);
                    }
                }
            }
            return response()->json(['message' => "Cart updated successfully", 'success' => true], 200);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }


    //move the cart items to orders
    public function addToOrders(Request $request)
    {
        try {

            $userCreated = $userMaintained =  Auth::user()->name;
            $dateCreated = $dateMaintained = Carbon::now()->format('Y-m-d');
            $preOrderdtlArr = [];
            $productArr = [];

            // DB::beginTransaction();

            $lastOrder =  PreOrderHdr::getLastOrderByCompanyOrUser();
            if ($lastOrder > 0) {
                $newOrderId =  $lastOrder + 1;
                $recordArr =   [
                    "PreOrderNum" =>  $newOrderId,
                    "UserCreated" => $userCreated,
                    "UserMaintained" => $userMaintained,
                    "DateCreated" => $dateCreated,
                    "DateMaintained" => $dateMaintained
                ];
                $lastOrderId = PreOrderHdr::createOrder($recordArr);
                if (isset($lastOrderId) && $lastOrderId > 0) {


                    foreach ($request->products as $pr) {
                        array_push($productArr, $pr['ProductId']);
                    }

                    $productArrWithPrice = Product::getPrice($productArr);
                    $preordernum = 1;
                    $OrderExtTotal = 0;
                    $OrderQuantityTotal = 0;

                    foreach ($request->products as $prdArr) {

                        $price =  $this->getProductWithPrice($productArrWithPrice, $prdArr['ProductId']);
                        $totaExtofProduct = $price * $prdArr['Quantity'];

                        $OrderExtTotal =  $OrderExtTotal + $totaExtofProduct;
                        $OrderQuantityTotal = $OrderQuantityTotal + $prdArr['Quantity'];

                        $preOrderdtlArr[] =  array(
                            "PreOrderNumdtl" => $lastOrderId,
                            "Style" => $prdArr['ProductId'],
                            "Color" => $prdArr['ColorId'],
                            "Qty" =>  $prdArr['Quantity'],
                            "Price" => $price,
                            "Ext" => $totaExtofProduct,
                            "UserCreated" => $userCreated,
                            "UserMaintained" => $userMaintained,
                            "CompanyNo" => Auth::user()->CompanyNo,
                            "PreOrderLinenum" => $preordernum
                            // "DateCreated" => $dateCreated,
                            // "DateMaintained" => $dateMaintained
                        );
                        $preordernum++;
                    }

                    $response = PreOrderDtl::bulkInsert($preOrderdtlArr);

                    if ($response) {

                        $ExtTotalUpdateArr =  [
                            "TotalUnits" => $OrderQuantityTotal,
                            "TotalExt" =>  $OrderExtTotal
                        ];
                        $response =  PreOrderHdr::updateQuantityWithExt($ExtTotalUpdateArr, $lastOrderId);

                        if ($response) {

                            Cart::deletefromCart($productArr);
                        //DB::commit();
                            // if ($response || $response == 0) {
                                return response()->json(['message' => " An order has been created.", 'success' => true], 200);
                            // }
                        }
                    }
                    return response()->json(['message' => "Something went wrong", 'error' => $response], 400);
                }
                return response()->json(['message' => "Something went wrong", 'error' => $lastOrderId], 400);
            }
            return response()->json(['message' => "Something went wrong", 'error' => $lastOrder], 400);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Something went wrong", 'error' => $th->getMessage()], 400);
        }
    }

    //get single product with price
    private function getProductWithPrice($productPriceArr, $productId)
    {


        $price = 0;

        foreach ($productPriceArr  as $key => $p) {


            if (trim($p['Style']) == trim($productId)) {

                $price =  $p['price'];
                break;
            }
        }
        return $price;
    }
}
