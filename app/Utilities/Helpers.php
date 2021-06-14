<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Utilities;

use Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\COLRMS0;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class Helpers
{

    /**
     * Convert array keys to snake case recursively.
     *
     * @param  array  $array
     * @param  string $delimiter
     * @return string
     */
    public static function snakeKeys($array, $delimiter = '_')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = Helpers::snakeKeys($value, $delimiter);
            }
            $result[Str::snake($key, $delimiter)] = $value;
        }
        return $result;
    }

    public static function getCurrentTanentId()
    {
        return intval(request()->header('tenant_id'));
    }
    /**
     * $array should be in format returns unique values in value
     * @param[
     *      {
     *          "ID":"key",
     *          "value":"value1,value2,value3,value4"
     *      }
     *      {
     *          "ID":"key2",
     *          "value":"value5,value6,value7,value8"
     *      }
     * ]
     * @return [
     *      {
     *          "ID":"key1",
     *          "value":["value1","value2","value3","value4"]
     *      }
     *      {
     *          "ID":"key2",
     *          "value":["value5","value6","value7","value8"]
     *      }
     * ]
     */
    public static function convertValueFromCommaSeperatedToArray($element)
    {
        $element['value'] = array_values(array_unique(explode(',', $element['value'])));
        return $element;
    }


    /**
     * format change
     * return date
     */
    public static function changeDateFormat($date, $format)
    {
        // change the date format
        if ($date != '' && !empty($date) && isset($date)) {
            $date = Carbon::parse($date)->format($format);
        }
        return $date;
    }

    /**
     * Sort the Associate array
     * @return array
     */
    public static function sortAssociateArr($dataArr, $column, $sortType = 'asc')
    {

        usort($dataArr, function ($item1, $item2) use ($column, $sortType) {

            if ($sortType == 'asc')
                return $item1[$column] <=> $item2[$column];
            if ($sortType == 'desc')
                return $item2[$column] <=> $item1[$column];
        });

        return $dataArr;
    }


    /**
     * Add dynamic Where to models
     * @return array
     */
    public static function addDynamicWheres($modelCollection, $valueArr, $key = null)
    {
        $alias =  $valueArr['filterTableAlias'] ?? null;
        $Operator = $valueArr['Operator'] ?? null;
        $fieldName = $valueArr['filterColumn'] ?? null;
        $id = $valueArr['id'] ?? null;
        $fieldName2 = $valueArr['filterColumn2'] ?? null;
        $id2 = $valueArr['id2'] ?? null;

        if ((!is_array($id) && $id != '' && isset($id)) || (is_array($id) && count($id) > 0)) {
            $modelCollection =  Helpers::addConditions($modelCollection, $id, $alias, $fieldName, $Operator);
        }

        if ((!is_array($id2) && $id != '' && isset($id2)) || (is_array($id2) && count($id2) > 0)) {
            $modelCollection = Helpers::addConditions($modelCollection, $id2, $alias, $fieldName2, $Operator);
        }

        return $modelCollection;
    }


    /**
     * Add dynamic SortBy to models
     * @return array
     */

    public static function addDynamicSortBy($modelCollection, $sortfilters, $entity = "others")
    {
        foreach ($sortfilters as $key => $value) {

            $tableAlias = $value['filterTableAlias'];
            $filterColumn = $value['filterColumn'];
            $sortType = $value['sortType'];

            $column  =  $tableAlias . '.' . $filterColumn;

            if (!isset($tableAlias) || empty($tableAlias)) {
                $column  = $filterColumn;
            }

            if ($entity == "products") {

                if ($filterColumn == 'Season') {
                    $modelCollection = $modelCollection->orderByRaw('SUBSTRING(max(' . $tableAlias . '.' . $filterColumn . '), LEN(max(' . $tableAlias . '.' . $filterColumn . '))-3, 4)' . $sortType);
                } else if ($filterColumn == 'Market') {
                    $modelCollection = $modelCollection->orderByRaw('LEFT(max(' . $tableAlias . '.' . $filterColumn . '), PATINDEX("%[^0-9]%",max(' . $tableAlias . '.' . $filterColumn . '))-1)' . $sortType);
                } else {
                    $modelCollection = $modelCollection->orderByRaw('max(' . $tableAlias . '.' . $filterColumn . ')' . $sortType);
                }
            }
            if ($entity == "others") {
                $modelCollection = $modelCollection->orderBy($column, $sortType);
            }
        }

        return $modelCollection;
    }


    /**
     * get unique arrays from multi array
     * @param array
     * @return array
     */
    public static function array_multi_unique($multiArray)
    {

        $uniqueArray = array();

        foreach ($multiArray as $subArray) {

            if (!in_array($subArray, $uniqueArray)) {
                $uniqueArray[] = $subArray;
            }
        }

        return $uniqueArray;
    }

    /**
     * add Dynamic search
     * @param array
     * @return array
     */

    public static function addDynamicSearch($modelCollection, $searchFilter)
    {

        $modelCollection =  $modelCollection->where(function ($query) use ($searchFilter) {

            foreach ($searchFilter as $key => $search) {

                $column =  $search['filterColumn'];
                $alias  =  $search['filterTableAlias'];
                $value  = $search['value'];

                if ($key == 0)
                    $query->where($alias . '.' . $column, 'like', '%' . $value . '%');
                if ($key > 0)
                    $query->orWhere($alias . '.' . $column, 'like', '%' . $value . '%');
            }
        });

        return $modelCollection;
    }

    private static function addConditions($modelCollection, $id, $alias, $fieldName, $Operator)
    {
        if (is_array($id)) {

            $valuesLength =  count($id);

            if ($valuesLength > 0) {
                $modelCollection =  $modelCollection->whereIn($alias . "." . $fieldName, $id);
            }
        }

        if (!is_array($id) && $id != '' && isset($id)) {
            $ColumnValue = $id;
            if ($Operator == "like") {
                $ColumnValue =  "%" . $ColumnValue . "%";
            }
            $modelCollection =  $modelCollection->where($alias . "." . $fieldName, $Operator, $ColumnValue);
        }

        return $modelCollection;
    }

    public static function storeFile($request)
    {
        $url = 'https://www.junaidjamshed.com/spring-summer-collection-2020';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->store('public/' . $request->path);
            return Storage::url($imageName);
        }
        return $url;
    }


    public static function getProductJson($Products, $colors = [], $orders = null)
    {
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
                    $tempQAArr[$tempVal[0]] =  $tempVal[1];
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

            if ($colors != []) {
                $value->SelectedColor = array();
                $Selected = array();
                foreach ($colors as $c) {
                    $values = COLRMS0::where('Color', $c)->get();
                    $results = [];
                    foreach ($values as $v) {
                        $results[] = array('ColorCode' => $v['Color'], 'ColorName' => $v['CRDS3J'], 'ColorExDes' => $v['CDES3J'], 'ColorNLC' => $v['NCLR3J']);
                    }
                    $Selected[$c] = $results[0];
                }
                $value->SelectedColor = array_values($Selected);
            }

            /*
                    Making UPC into Array
                        "UPCXInfo": {
                        "652874045788": "210",
                        }
                    */
            $tempUP = explode('|', $value->UPCXInfo);
            foreach ($tempUP as $key => $val) {

                $tempVal = explode('-', $val);
                $tempUPArr[$tempVal[0]] =  $tempVal[1];
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
        return $Products[0];
    }
    // value is set or not
    public  static function isValueSet($var = null, $returnValueIfNotSet = '')
    {
        return isset($var) && !empty($var) && $var != '' && $var != null ? $var : $returnValueIfNotSet;
    }

    // change the date format like 12/18/2020  to 20201812
    public static function changeDateFormatToSimpleNumericValue($date = null)
    {
        if ($date != '' && $date != null) {
            $date =  explode("/", $date);
            $year =  Helpers::isValueSet($date[2], '');
            $month =  Helpers::isValueSet($date[1], '');
            $day =  Helpers::isValueSet($date[0], '');
            $date = $year . $month . $day;
        }

        return $date;
    }


    // add spaces in PasCal case
    public static function addSpaceBetweenString($str)
    {

        // return preg_split('/(?<=\\w)(?=[A-Z])/', $str);
        return $str;
    }

    public static function removeEmptyKey($arr)
    {
        return array_filter($arr, function ($v) {
            return !is_null($v) && $v !== '';
        });
    }

    public static function getRecordPerPageCount(array $products)
    {

        $recordPerPage = 0;
        if (isset($products)) {
            foreach ($products as $p) {
                $recordPerPage =   $recordPerPage + count($p['Colors']);
            }
        }
        return $recordPerPage;
    }


    public static function getProductJsonForExport($recordPerPage = 0)
    {
        //make json for getting the products
        $product = [];
        $product['pageNumber'] = 0;
        $product['recordPerPage'] = $recordPerPage;
        $product['SortyBy'] = [];
        $product['Filter'] = array();
        $product['Search'] = [];

        return $product;
    }

    // if part executed if PDF export from LineSheet
    // else part executed when pdf export from workspace
    public static function getProducts($request, $product, $divisions, $recordPerPage = 0)
    {
        if (
            $request->has('linesheetId')
            &&
            isset($request->linesheetId)
            &&
            $request->linesheetId > 0
        ) {
            $product = Product::getProducts(
                Auth::id(),
                Auth::user()->CompanyNo,
                $product,
                $divisions,
                0,
                0,
                null,
                $request->linesheetId
            );
        } else {

            $product = Product::getProducts(
                Auth::id(),
                Auth::user()->CompanyNo,
                $product,
                $divisions,
                0,
                1,
                $request->products,
                0
            );
        }

        return $product;
    }
    // product title
    public static function getTitle($linesheetId, $product)
    {
        $title = "";
        if (isset($linesheetId) && $linesheetId > 0) {
            if (isset($product[0]->lineSheetName)) {
                $title = $product[0]->lineSheetName;
            }
            if (isset($product[1]->lineSheetName)) {
                $title = $product[1]->lineSheetName;
            }
        }

        return $title;
    }
    public static  function removeSpaceBetweenCamelCaseString($key)
    {
        return preg_replace('/([a-z])([A-Z])/s','$1 $2', $key);
    }
}
