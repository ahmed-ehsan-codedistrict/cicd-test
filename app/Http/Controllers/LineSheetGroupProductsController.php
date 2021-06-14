<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LineSheetGroupProducts;
use App\Models\LSGPPrivatNotes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomSortCombination;
use App\Models\LineSheetGroup;
use App\Models\LineSheets;
use App\Utilities\Helpers;
use App\Models\Product;
use App\Models\CustomSort;

class LineSheetGroupProductsController extends Controller
{

    /**
     * add products to linesheet Group
     * @param  | requests
     * @return | json response
     */
    public function store(Request $request)
    {
        try {

            // add validation to request
            $this->validate($request, [
                "groupId" => "required",
                "productId" => "required",
                "colorId" => "array|required"
            ]);

            //check product is already assigned to this group
            $ProductExistArr = LineSheetGroupProducts::isProductAlreadyAssigned($request->groupId, $request->productId,  $request->colorId);
            $tempArr =  [];

            foreach ($ProductExistArr as $value) {
                $tempArr[$value['ColorId']] = $value;
            }

            $ProductExistArr =  $tempArr;

            //products addtion array
            $ProductsAddArr =  [];

            foreach ($request->colorId as $color) {
                if (!array_key_exists($color, $ProductExistArr)) {

                    $ProductsAddArr[] =  array(
                        "GroupId" => $request->groupId,
                        "ProductId" => $request->productId,
                        "ColorId" => $color,
                        "CompanyNo" => Auth::user()->CompanyNo,
                        "CreatedBy" => Auth::id(),
                        "created_at" => Carbon::now()
                    );
                }
            }

            $response =  LineSheetGroupProducts::addProductsToGroup($ProductsAddArr);

            if ($response) {
                return response()->json(['message' => 'Product is added successfully.', 'success' => true], 200);
            }
            return response()->json(['message' => 'oops! something went wrong', 'error' => $response], 403);
        } catch (\Throwable $th) {
            return response()->json(
                ['message' => 'oops! something went wrong', 'error' => $th->getMessage()],
                403
            );
        }
    }

    //store the  notes
    public function storeNotes(Request $request)
    {

        try {
            // add validation to request
            $validator = validator()->make(request()->all(), [
                "lsgpId" => "required",
                "notesType" => "required"
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message'   => 'The given data was invalid.',
                    'errors'    => $validator->errors()
                ], 403);
            }

            $pitch = 0;
            $notes = isset($request->notes) ? $request->notes : null;
            $notesLegth = strlen($notes);

            // add public notes
            if ($request->notesType == "public") {

                $response =  LineSheetGroupProducts::addPublicNotes($request->lsgpId, $notes);
                $pitch = 1;
                if ($notesLegth <= 0 || is_null($notes)) {
                    $pitch = 4;
                }
            }

            // add/edit for private notes
            if ($request->notesType == "private") {

                if (isset($request->lsgppId) && $request->lsgppId > 0) {
                    if ($notesLegth <= 0 || is_null($notes)) {
                        $response =  LSGPPrivatNotes::deletePrivateNotes($request->lsgppId);
                        $pitch = 4;
                    } else {
                        $response =  LSGPPrivatNotes::updatePrivateNotes($request->lsgppId,  $notes);
                        $pitch = 3;
                    }
                } else {
                    $response =  LSGPPrivatNotes::addPrivateNotes($request->lsgpId,  $notes, $request->productId);
                    $pitch = 2;
                }
            }

            if ($response && ($pitch == 1 || $pitch == 2)) {
                return response()->json(['message' => 'Notes has been added successfully.', 'success' => true], 200);
            }
            if ($response && ($pitch == 3)) {
                return response()->json(['message' => 'Notes has been updated successfully.', 'success' => true], 200);
            }
            if ($response && $pitch == 4) {
                return response()->json(['message' => 'Notes deleted successfully.', 'success' => true], 200);
            }
            return response()->json(['message' => 'oops! something went wrong', 'error' => $response], 403);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'oops! something went wrong', 'error' => $th->getMessage()], 403);
        }
    }

    public function storeAll(Request $request)
    {
        try {
            // add validation to request
            $validator = validator()->make(request()->all(), [
                '*.ProductId' => 'string|required',
                '*.GroupId' =>  'required',
                '*.ColorId' => 'required',
                '*.LineSheetId' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message'   => 'The given data was invalid.',
                    'errors'    => $validator->errors()
                ], 403);
            }
            if (count($request->all()) > 0) {
                if (LineSheets::canLineSheetEdit($request[0]['LineSheetId'])) {

                    $records = [];
                    $productNotExistCount = 0;
                    $DisplayOrder =  null;
                    $GroupLastDisplayOrder =  LineSheetGroupProducts::getLastDisplayOrder($request[0]['GroupId']);

                    if ($GroupLastDisplayOrder > 0) {
                        $DisplayOrder =  $GroupLastDisplayOrder;
                    }

                    foreach ($request->all() as $r) {
                        if (LineSheetGroupProducts::productAlreadyInGroup($r['LineSheetId'], $r['ProductId'], $r['ColorId']) <= 0) {
                            if ($GroupLastDisplayOrder > 0) {
                                $DisplayOrder++;
                            }
                            $records[] = [
                                "LinesheetId" => $r['LineSheetId'],
                                "ProductId" => $r['ProductId'],
                                "ColorId" => $r['ColorId'],
                                "GroupId" => $r['GroupId'],
                                "DisplayOrder" => $DisplayOrder,
                                "CreatedBy" => Auth::id(),
                                "CompanyNo" => Auth::user()->CompanyNo,
                                "created_at" => Carbon::now(),
                                "updated_at" => Carbon::now()
                            ];
                        } else {
                            $productNotExistCount++;
                        }
                    }

                    $response =  LineSheetGroupProducts::bulkAddProducts($records);
                    if ($productNotExistCount > 0) {
                        return response()->json(['message' => $productNotExistCount . " products have not been added because these are already exist in linesheet", 'success' => true], 200);
                    }
                    if ($response) {
                        return response()->json(['message' => 'Products added successfully', 'success' => true], 200);
                    }
                    return response()->json(["message" => "oops! something went wrong", 'success' => $response], 403);
                } else {
                    return response()->json(['message' => "You don't have rights to add products", 'success' => false, 'error' => " You don't have rights to add products"], 400);
                }
            } else {
                return response()->json(['message' => "Please select atleast one product to add in a group", 'success' => false, 'error' => "Empty Array"], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => "Something went wrong", 'error' => $th->getMessage()], 403);
        }
    }

    /**
     * user can drag and drop the table rows to made a custom sort
     */
    public function customSort(Request $request)
    {

        try {

            if (!isset($request['SortBy']) || count($request['SortBy']) <= 0) {
                return response()->json(["message" => "Custom Sort is not selected", 'success' => false], 400);
            }

            if (isset($request['SortBy']) && ($request['customSort'] == false)) {

                $sortByArr = Helpers::sortAssociateArr($request['SortBy'], 'level');
                $sortByColumn =  $this->concatSortByColumn($sortByArr);
                $userSortCombination = CustomSortCombination::checkUserSortCombinationIsExist($sortByColumn, $request['lineSheetId'], $request['groupId']);
                $dbSortByColumn = isset($userSortCombination->SortByColumn) ? $userSortCombination->SortByColumn : '';
                $dbSortById = isset($userSortCombination->id) ? $userSortCombination->id : 0;
                $setValuesNull = false;
                $updateDisplayOrder = false;

                if (!isset($dbSortByColumn) || $dbSortByColumn == '') {

                    $dbSortById = CustomSortCombination::addUserSortCombination($sortByColumn, $request['lineSheetId'], $request['groupId']);
                    $setValuesNull =  true;
                }

                if (trim($dbSortByColumn) != trim($sortByColumn) && $dbSortByColumn != '') {

                    $CSC = CustomSortCombination::updateUserSortCombination($sortByColumn, $userSortCombination->id, $request['groupId']);
                    $dbSortById = $userSortCombination->id;
                    $updateDisplayOrder =  true;
                }

                $Products = Product::getProductsforCustomSort($request['SortBy'], Auth::user()->CompanyNo, $request['groupId']);

                if ($updateDisplayOrder) {
                    $updateCustomSort = LineSheetGroupProducts::updateDisplayOrder($Products, Auth::user()->CompanyNo, $request['groupId']);
                }

                if ($setValuesNull) {
                    // $updateDisOrder = LineSheetGroupProducts::updateDisplayOrder($Products, Auth::user()->CompanyNo, $request['groupId']);
                    $addDisplayOrder = LineSheetGroupProducts::addToCustomSort($Products, $dbSortById);
                }
            }

            //set the display Order if the items are deleted
            $CustomSortRecords  = CustomSort::getRecordsByCombinationId($dbSortById);
            $IsDisplayOrderSet = isset($CustomSortRecords[0]['DisplayOrder']) ? $CustomSortRecords[0]['DisplayOrder'] : '';
            if ($IsDisplayOrderSet != '') {
                $OrderUpted =  CustomSort::updateDisplayOrder($CustomSortRecords);
            }

            //$DisplayOrderNUllValues =  LineSheetGroupProducts::getNullValues($request['groupId']);
            $DisplayOrderNUllValues =  LineSheetGroupProducts::getNullValues($dbSortById);

            if ($DisplayOrderNUllValues > 0) {
                return response()->json([
                    "message" => "Disabled your custom sort and select sort combination first",
                    "Success" => false
                ], 403);
            }

            $lpgsId =  LineSheetGroupProducts::getLineSheetGroupProductId($request['productId'], $request['groupId'], $request['colorId'])->id;

            // $oldIdx = LineSheetGroupProducts::getProductDisplayOrder($request['productId'], $request['groupId'], $request['colorId'])->DisplayOrder;
            $oldIdx = LineSheetGroupProducts::getProductDisplayOrder($lpgsId, $dbSortById)->DisplayOrder;
            $newIdx = $oldIdx - $request->toLevel;
            if ($request->to == "down") {
                $newIdx = $request->toLevel + $oldIdx;
            }
            // $min = $request->oldIdx;
            // $max = $request->newIdx;
            $min =  $oldIdx;
            $max =  $newIdx;
            $startIdx =  0;
            $endIdx =  0;
            $ids = [];
            $cases = [];
            $params = [];
            $CompanyNo = Auth::user()->CompanyNo;
            $groupId =  $request->groupId;
            $sortType = "asc";

            // define the max and min range to get data from table
            if ($oldIdx > $newIdx) {
                $min = $newIdx;
                $max = $oldIdx;
                $sortType = "desc";
            }

            // $result = LineSheetGroupProducts::getProductsForDragDrop($sortType, $min, $max, $groupId);
            $result = LineSheetGroupProducts::getProductsForDragDrop($sortType, $min, $max, $dbSortById);
            // use if value drag from lower to upper
            // if ($request->newIdx - $request->oldIdx < 0) {
            if ($request->to == "up") {

                $totalRecords =  count($result);

                $startIdx  = $totalRecords - 1;

                for ($i = $startIdx; $i >= $endIdx; $i--) {

                    $result[$i]['OldPosition'] =  $result[$i]['DisplayOrder'];

                    // if (trim($result[$i]['ProductId']) ==  $request->productId && trim($result[$i]['ColorId']) == trim($request->colorId)) {

                    //     $result[$i]['DisplayOrder'] = $newIdx;
                    // } else {

                    //     $result[$i]['DisplayOrder'] =   $result[$i]['DisplayOrder'] + 1;
                    // }

                    if ($lpgsId == $result[$i]['LSGPId']) {

                        $result[$i]['DisplayOrder'] = $newIdx;
                    } else {

                        $result[$i]['DisplayOrder'] =   $result[$i]['DisplayOrder'] + 1;
                    }

                    $ids[] = $result[$i]['id'];
                    $cases[] = "WHEN {$result[$i]['id']} then ?";
                    $params[] = $result[$i]['DisplayOrder'];
                }
            }


            // use if drag value upper to lower

            // if ($request->newIdx - $request->oldIdx > 0) {
            if ($request->to == "down") {

                $totalRecords =  count($result);

                $endIdx  = $totalRecords - 1;

                for ($i = $startIdx; $i <= $endIdx; $i++) {

                    $result[$i]['OldPosition'] =  $result[$i]['DisplayOrder'];

                    // if (trim($result[$i]['ProductId']) ==  $request->productId && trim($result[$i]['ColorId']) == trim($request->colorId)) {

                    //     $result[$i]['DisplayOrder'] = $newIdx;
                    // } else {

                    //     $result[$i]['DisplayOrder'] =   $result[$i]['DisplayOrder'] - 1;
                    // }

                    if ($lpgsId == $result[$i]['LSGPId']) {

                        $result[$i]['DisplayOrder'] = $newIdx;
                    } else {

                        $result[$i]['DisplayOrder'] =   $result[$i]['DisplayOrder'] - 1;
                    }
                    $ids[] = $result[$i]['id'];
                    $cases[] = "WHEN {$result[$i]['id']} then ?";
                    $params[] = $result[$i]['DisplayOrder'];
                }
            }

            //  return $result;

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);

            //raw query to update the Display order
            // $response =  LineSheetGroupProducts::bulkUpdate($ids, $cases, $CompanyNo, $groupId, $params);
            $response =  CustomSort::bulkUpdate($ids, $cases, $CompanyNo, $groupId, $params, $dbSortById);

            if ($response) {
                return response()->json(["message" => "Products sorted successfully.", 'success' => true], 200);
            }
            return response()->json(["message" => "oops! something went wrong.", 'error' => $response], 403);
        } catch (\Throwable $th) {
            return response()->json(["message" => "oops! something went wrong.", 'error' => $th->getMessage()], 403);
        }
    }

    //concat the sort By Column
    private function concatSortByColumn($sortyByArr)
    {
        $SortByColumn = '';
        foreach ($sortyByArr as $key => $item) {

            $SortByColumn .=   $item['filterTableAlias'] . '.' . $item['filterColumn'] . ' ' . $item['sortType'] . ',';
        }
        $SortByColumn =  substr_replace($SortByColumn, "", -1);

        return $SortByColumn;
    }

    public function deleteLineSheetGroup(Request $request)
    {

        $validator = validator()->make(request()->all(), [
            'GroupId' =>  'integer|required|exists:LineSheetGroup,id',
            'LinesheetId' =>  'integer|required|exists:LineSheets,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'   => 'The given data was invalid.',
                'errors'    => $validator->errors()
            ], 403);
        }
        try {
            if (LineSheets::canLineSheetEdit($request->LinesheetId)) {
                if (LineSheetGroup::where('id', $request->GroupId)->where('LineSheetId', $request->LinesheetId)->exists()) {
                    $products = LineSheetGroupProducts::getLinesheetGroupProducts($request->GroupId);
                    if ($products) {
                        foreach ($products as $p) {
                            $productsStatus = LineSheetGroupProducts::deleteGroupProduct($request->GroupId, $p['ProductId'], $p['ColorId']);
                            if ($productsStatus['success'] == false) {
                                return response()->json(['message' => $productsStatus['message'], 'success' => false, 'error' => $productsStatus['error']], 400);
                            }
                        }
                    }
                    LineSheetGroup::deleteLinesheetGroup($request->GroupId);
                    return response()->json(['message' => 'Group Deleted Successfully', 'success' => true, 'error' => null]);
                } else {
                    return response()->json(['message' => 'Something went wrong', 'success' => false, 'error' => "Group doesn't exists"], 400);
                }
            } else {
                return response()->json(['message' => "You cannot delete the group.", 'success' => false, 'error' => "Don't have right to delete the group."], 400);
            }
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteLineSheetGroupProduct(Request $request)
    {
        $validator = validator()->make(request()->all(), [
            'GroupId' =>  'integer|required|exists:LineSheetGroup,id',
            'ProductId' =>  'string|required|exists:LineSheetGroupProducts,ProductId',
            'ColorId' =>  'string|required|exists:LineSheetGroupProducts,ColorId',
            'LinesheetId' =>  'string|required|exists:LineSheets,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'   => 'The given data was invalid.',
                'errors'    => $validator->errors()
            ], 403);
        }
        try {
            if (LineSheets::canLineSheetEdit($request->LinesheetId) > 0) {
                $status = LineSheetGroupProducts::deleteGroupProduct($request->GroupId, $request->ProductId, $request->ColorId);
                return $status['success'] == false ?
                    response()->json(['message' => $status['message'], 'success' => false, 'error' => $status['error']], 400) :
                    response()->json(['message' => $status['message'], 'success' => true, 'error' => $status['error']], 200);
            } else {
                return  response()->json(['message' => "You don't have rights to remove the product", 'success' => false, 'error' => "You don't have rights to remvoe the product"], 400);
            }
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteNotes(Request $request)
    {
        $request->validate([
            'GroupId' =>  'integer|required|exists:LineSheetGroup,id',
            'ProductId' =>  'string|required|exists:LineSheetGroupProducts,ProductId',
            'ColorId' =>  'string|required|exists:LineSheetGroupProducts,ColorId'
        ]);
        try {
            $status = LineSheetGroupProducts::deleteGroupProductNotes($request->GroupId, $request->ProductId, $request->ColorId);
            return $status['success'] == false ?
                response()->json(['message' => $status['message'], 'success' => false, 'error' => $status['error']], 400) :
                response()->json(['message' => $status['message'], 'success' => true, 'error' => $status['error']], 200);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'success' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
