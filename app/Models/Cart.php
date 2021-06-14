<?php

namespace App\Models;

use App\User;
use App\Utilities\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpParser\Node\Stmt\TryCatch;

class Cart extends BaseModel
{
    public $table = 'Cart';

    public $timestamps = true;

    protected $fillable = ['Id', 'UserId', 'ProductId', 'ColorId', 'Quantity'];

    public static function createCart($products)
    {
        try{
            $cart = Cart::select('Id','ProductId','ColorId','UserId')->where('UserId',Auth::id())->get();
            $arr = array();
            $idArray = array();
            foreach($cart as $c){
                $idArray[] = $c->Id;
                $arr =  array('ProductId'=>$c->ProductId, 'ColorId'=>$c->ColorId, 'UserId'=>Auth::id());
                if(in_array($arr, $products)){
                  array_search( $arr, $products);
                  unset($products[array_search($arr, $products)]);
                }
            }
            $Product = array();
            foreach($products as $p)
            {
                $Product[] = array('ProductId'=>$p['ProductId'],'ColorId'=>$p['ColorId'],'UserId'=>Auth::id());
            }
            Cart::insert($products);
            $cartLatest = Cart::select('Id','ProductId','ColorId')->whereNotIn('Id',$idArray)->where('UserId',Auth::id())->get();
            $userId =  Auth::id();
            $companyId = Auth::user()->CompanyNo;
            $divisions = [];
            //get User Divisions
            foreach (User::find(Auth::id())->Divisions->pluck('DivisionNo') as $d) {
                $divisions[] = $d;
            };
            $filters = array();
            $filters['SortBy'] = [];
            $filters['Search'] = [];
            $filters['Filter'] = array();
            $filters['pageNumber'] = 0;
            $filters['recordPerPage'] = 1;
            $cart = Cart::where('UserId', Auth::id())->get();
            $arr = [];
            foreach ($cartLatest as $c) {
                $tempTagArr  =  array();
                $tempPTagArr =  array();    
                $filters['productId'] = $c['ProductId'];
                $products = Product::getProducts($userId, $companyId, $filters, $divisions)[0];
                $arr[$c['Id']]['ID'] = $c['Id'];
                $arr[$c['Id']]['ProductId'] = $c['ProductId'];
                $arr[$c['Id']]['ColorId'] = $c['ColorId'];
                $arr[$c['Id']]['AddedAt'] = $c['updated_at'];
                $arr[$c['Id']]['ProductName'] = trim($products->ProductName);
                $arr[$c['Id']]['ProductDescription'] = trim($products->ProductDescription);
                $arr[$c['Id']]['SizeCode'] = trim($products->SizeCode);
                $arr[$c['Id']]['Division'] = trim($products->Division);
                $arr[$c['Id']]['DivisionNo'] = trim($products->DivisionNo);
                $arr[$c['Id']]['Class'] = trim($products->Class);
                $arr[$c['Id']]['SubClass'] = trim($products->SubClass);
                $arr[$c['Id']]['MarketGroup'] = trim($products->MarketGroup);
                $arr[$c['Id']]['Season'] = trim($products->Season);
                $arr[$c['Id']]['Market'] = trim($products->Market);
                $arr[$c['Id']]['FabType'] = trim($products->FabType);
                $arr[$c['Id']]['FabricContent'] = trim($products->FabricContent);
                $arr[$c['Id']]['Brand'] = trim($products->Brand);
                $TagsArr = trim($products->Tags);
                $arr[$c['Id']]['Price'] = trim($products->Price);

                $tempCCArr = [];
                if (isset($products->ColorCodeName)) {
                    $tempCC = explode('|', $products->ColorCodeName);
    
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
                $arr[$c['Id']]['Colors'] = $tempCCArr;

                $tempTagArr  =  array();   // temp array for private tags
                $tempPTagArr =  array();   // temp array for public tags
                $TagsArray = explode('||',  $TagsArr);

                $tempTag = explode(',', $TagsArray[0]);

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

                $arr[$c['Id']]['PrivateTags'] = $tempTagArr;

                $tempTag = explode(',', $TagsArray[1]);
                if (isset($tempTag) && count($tempTag) > 0 && isset($tempTag[0]) && !empty($tempTag[0])) {
                    foreach ($tempTag as $key => $val) {
                        $tempVal = explode('/', $val);
                        if (isset($tempVal) && count($tempVal) > 0) {
                            $tempAssignArr =  array("id" => $tempVal[0], "name" => $tempVal[1]);
                            if (!in_array($tempAssignArr, $tempPTagArr)) {
                                $tempPTagArr[] = $tempAssignArr;
                            }
                        }
                    }
                }

                $arr[$c['Id']]['PublicTags'] = $tempPTagArr;
        }  
            return array_values($arr);
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function UpdateCart($productId, $colorId, $quantity)
    {
        try{
            Cart::where('UserId', Auth::id())
                ->where('ProductId', $productId)->where('ColorId', $colorId)->update(['Quantity' => $quantity]);
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function deleteProduct($productId)
    {
        try{
            Cart::where('UserId', Auth::id())
            ->where('ProductId', $productId)->delete();
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function DeleteCartProduct($productId, $colorId)
    {
        try{
            Cart::where('UserId', Auth::id())
            ->where('ProductId', $productId)->where('ColorId', $colorId)->delete();
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function showAll()
    {
        try
        {         
            $userId =  Auth::id();
            $companyId = Auth::user()->CompanyNo;
            $divisions = [];
            //get User Divisions
            foreach (User::find(Auth::id())->Divisions->pluck('DivisionNo') as $d) {
                $divisions[] = $d;
            };
            $filters = array();
            $filters['SortBy'] = [];
            $filters['Search'] = [];
            $filters['Filter'] = array();
            $filters['pageNumber'] = 0;
            $filters['recordPerPage'] = 1;
            $cart = Cart::where('UserId', Auth::id())->get();
            $arr = [];
            foreach ($cart as $c) {
                $tempTagArr  =  array();
                $tempPTagArr =  array();    
                $filters['productId'] = $c['ProductId'];
                $products = Product::getProducts($userId, $companyId, $filters, $divisions)[0];
                $arr[$c['Id']]['ID'] = $c['Id'];
                $arr[$c['Id']]['ProductId'] = $c['ProductId'];
                $arr[$c['Id']]['ColorId'] = $c['ColorId'];
                $arr[$c['Id']]['AddedAt'] = $c['updated_at'];
                $arr[$c['Id']]['ProductName'] = trim($products->ProductName);
                $arr[$c['Id']]['ProductDescription'] = trim($products->ProductDescription);
                $arr[$c['Id']]['SizeCode'] = trim($products->SizeCode);
                $arr[$c['Id']]['Division'] = trim($products->Division);
                $arr[$c['Id']]['DivisionNo'] = trim($products->DivisionNo);
                $arr[$c['Id']]['Class'] = trim($products->Class);
                $arr[$c['Id']]['SubClass'] = trim($products->SubClass);
                $arr[$c['Id']]['MarketGroup'] = trim($products->MarketGroup);
                $arr[$c['Id']]['Season'] = trim($products->Season);
                $arr[$c['Id']]['Market'] = trim($products->Market);
                $arr[$c['Id']]['FabType'] = trim($products->FabType);
                $arr[$c['Id']]['FabricContent'] = trim($products->FabricContent);
                $arr[$c['Id']]['Brand'] = trim($products->Brand);
                $TagsArr = trim($products->Tags);
                $arr[$c['Id']]['Price'] = trim($products->Price);

                $tempCCArr = [];
                if (isset($products->ColorCodeName)) {
                    $tempCC = explode('|', $products->ColorCodeName);
    
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
                $arr[$c['Id']]['Colors'] = $tempCCArr;

                $tempTagArr  =  array();   // temp array for private tags
                $tempPTagArr =  array();   // temp array for public tags
                $TagsArray = explode('||',  $TagsArr);

                $tempTag = explode(',', $TagsArray[0]);

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

                $arr[$c['Id']]['PrivateTags'] = $tempTagArr;

                $tempTag = explode(',', $TagsArray[1]);
                if (isset($tempTag) && count($tempTag) > 0 && isset($tempTag[0]) && !empty($tempTag[0])) {
                    foreach ($tempTag as $key => $val) {
                        $tempVal = explode('/', $val);
                        if (isset($tempVal) && count($tempVal) > 0) {
                            $tempAssignArr =  array("id" => $tempVal[0], "name" => $tempVal[1]);
                            if (!in_array($tempAssignArr, $tempPTagArr)) {
                                $tempPTagArr[] = $tempAssignArr;
                            }
                        }
                    }
                }

                $arr[$c['Id']]['PublicTags'] = $tempPTagArr;
        }  
            return array_values($arr);
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function deleteAll()
    {
        try{
            Cart::where('UserId', Auth::id())->delete();
        }
        catch(\Throwable $th)
        {
            return $th->getMessage();
        }
    }

    public static function updateOrInsertCart($productId, $colorId)
    {
        Cart::updateOrCreate(
            ['ProductId' => $productId, 'ColorId' => $colorId, 'UserId' => Auth::id(), 'CompanyNo' => Auth::user()->CompanyNo],
            ['Quantity' => 1]
        );
    }


    public static function deletefromCart($products)
    {
        try {
            return Cart::where(
                "UserId",
                Auth::id()
            )
                ->whereIn("ProductId", $products)
                ->delete();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
