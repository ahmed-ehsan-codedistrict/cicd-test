<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreOrderHdr;
use App\Utilities\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Add Order Header info function
    public function updateOrderHeader(Request $request)
    {

        try {

            $validator = validator()->make(request()->all(), [
                'OrderNumber' => 'integer',
                'Status' => 'required',
                'WorksheetType' => 'required',
                'CustAcct' => 'required',
                'CustNo' => 'required | integer',
                'StartDate' => 'required',
                'InStoreDate' => 'required',
                'SalesPerson' => 'required',
                'Buyer' => 'required',
                'tags' => 'array',

            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            // get Tags and tags assoc array for saving at once
            $Tags =  [];
            foreach ($request->tags as $t) {
                $Tags[] = array(
                    'PreOrderNum' => $request->OrderNumber,
                    'TagId' => $t,
                    'CompanyNo' => Auth::user()->CompanyNo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                );
            }

            // make an associate array for update base on worksheet type
            if ($request->WorksheetType == 'O') {
                $PreOrderHdrArr =  array(
                    "PreOrderStatus" => $request->Status,
                    "PreOrderType" => $request->WorksheetType,
                    "CustAcct" => $request->CustAcct,
                    "CustNo" => $request->CustNo,
                    "EcommCust" => Helpers::isValueSet($request->EcommCust, 0),
                    "StartDate" => Helpers::changeDateFormat($request->StartDate, 'Y-m-d'),
                    "CancelDate" => Helpers::changeDateFormat($request->CancelDate, 'Y-m-d'),
                    "InStoreDate" => Helpers::changeDateFormat($request->InStoreDate, 'Y-m-d'),
                    "Description" => $request->Description,
                    "TOPHold" => Helpers::isValueSet($request->TOPHold, 0),
                    "FitAprv" => Helpers::isValueSet($request->FitAprv, 0),
                    "Region" => $request->Region,
                    "Division" => $request->Division,
                    "SalesPerson" => $request->SalesPerson,
                    "OrdTyp" => $request->OrderType,
                    "Lbl" => $request->Lbl,
                    "CustDept" => $request->CustDept,
                    "CustomerRef" => $request->CustomerRef,
                    "Buyer" => $request->Buyer,
                    "BuyType" => $request->BuyType,
                    "Grp" => $request->Grp,
                    "TotalUnits" => $request->TotalUnits,
                    "TotalExt" => $request->TotalExt,
                    "NordRack" => $request->Rack
                );

                $response = PreOrderHdr::updateOrderHeader($PreOrderHdrArr, $request->OrderNumber, $Tags);
                if ($response) {
                    return response()->json(["message" => "Order header is updated successfully.", 'success' => true], 200);
                }

                return response()->json(['message' => "oops! something went wrong.", 'error' => $response], 403);
            }

            if ($request->WorksheetType == 'S') {
                $PreOrderHdrArr =  array(
                    "PreOrderStatus" => $request->Status,
                    "PreOrderType" => $request->WorksheetType,
                    "CustAcct" => $request->CustAcct,
                    "CustNo" => $request->CustNo,
                    "EcommCust" => Helpers::isValueSet($request->EcommCust, 0),
                    "StartDate" => Helpers::changeDateFormat($request->StartDate, 'Y-m-d'),
                    "CancelDate" => Helpers::changeDateFormat($request->CancelDate, 'Y-m-d'),
                    "InStoreDate" => Helpers::changeDateFormat($request->InStoreDate, 'Y-m-d'),
                    "Description" => $request->Description,
                    "Division" => $request->Division,
                    "SalesPerson" => $request->SalesPerson,
                    "Buyer" => $request->Buyer,
                    "BuyType" => $request->BuyType,
                    "TotalUnits" => $request->TotalUnits,
                    "TotalExt" => $request->TotalExt,
                    "FabricCode" => $request->FabricCode,
                    "NordRack" => $request->Rack
                );
            }

            $response =  PreOrderHdr::updateOrderHeader($PreOrderHdrArr, $request->OrderNumber, $Tags);
            if ($response) {
                return response()->json(["message" => "Order header is updated successfully.", 'success' => true], 200);
            }

            return response()->json(['message' => "oops! something went wrong.", 'error' => $response], 403);
        } catch (\Throwable $th) {
            return response()->json(['message' => "oops! something went wrong.", 'error' => $th->getMessage()], 403);
        }
    }

    // get all orders
    public function getAll(Request $request)
    {

        try {
            $validator = validator()->make(request()->all(), [
                'pageNumber' => 'required|integer',
                'recordPerPage' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return $validator->errors();
            }
            $orders =  PreOrderHdr::getAll($request);
            return  $orders;
        } catch (\Throwable $th) {
            return response()->json(['message' => "oops! something went wrong.", 'error' => $th->getMessage()], 403);
        }
    }

    public function deleteOrder(Request $request)
    {
        //validating inputs
        $request->validate([
            'Password' => 'string|nullable',
            'OrderId' => 'integer|required'
        ]);
        try {
            $request->Password ?
                $orderStatus =  PreOrderHdr::deleteOrder($request->OrderId, $request->Password)
                :
                $orderStatus =  PreOrderHdr::deleteOrder($request->OrderId);
            if ($orderStatus['error']) {
                return response()->json(['message' => $orderStatus['message'], 'error' => $orderStatus['error']], $orderStatus['code']);
            } else {
                return response()->json(['message' => $orderStatus['message'], 'success' => $orderStatus['success']], $orderStatus['code']);
            }
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }
}
