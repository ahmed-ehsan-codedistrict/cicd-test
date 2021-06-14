<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Product_Tags;
use App\Models\Tags;
use Exception;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ProductTagsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        /*TODO: Change the Input name to correct JSON format (camelCasing) */

        $request->validate([
            "PublicTags" => 'nullable|array',
            "PublicTags.*.name" => 'regex:/^[a-zA-Z0-9 ]+$/',
            "ProductID" => 'required|exists:PRHDMS0,Style',
            "PrivateTags" => 'nullable|array',
            "PrivateTags.*.name" => 'regex:/^[a-zA-Z0-9 ]+$/',
        ]);

        //recieve the post request
        $params = json_decode($request->getContent(), true);

        // set the variable
        $privateTags =  $params['PrivateTags'];
        $publicTags  =  $params['PublicTags'];
        $productId   = $params['ProductID'];
        $companyNo = Auth::user()->CompanyNo;
        $userId =  Auth::id();
        $privateRecordsArr =  [];
        $publicRecordsArr = [];


        // Two dimensional array for multiple record insertion

        foreach ($privateTags as  $key => $value) {

            $id =  $this->isValueSet($value['id']);

            if ($id == 0 && isset($value['name'])) {

                //finding tag if exists
                $tag = Tags::whereRaw('LOWER(TagName) =?', strtolower($value['name']))->first();
                $id = empty($tag) ? $this->newTagCreate($value['name']) : $tag->TagId;
            }


            $privateRecordsArr[] = [
                'ProductId' => $productId,
                'CompanyNo' => $companyNo,
                'TagId' => $id,
                'userId' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $privateTagIds = Arr::pluck($privateRecordsArr, 'TagId');


        foreach ($publicTags as  $key => $value) {

            $id =  $this->isValueSet($value['id']);

            if ($id == 0 && isset($value['name'])) {
                //finding tag if exists
                $tag = Tags::whereRaw('LOWER(TagName) =?', strtolower($value['name']))->first();
                $id = empty($tag) ? $this->newTagCreate($value['name']) : $tag->TagId;
            }

            $publicRecordsArr[] = [
                'ProductId' => $productId,
                'CompanyNo' => $companyNo,
                'TagId' => $id,
                'userId' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $publicTagIds = Arr::pluck($publicRecordsArr, 'TagId');

        //merging arrays
        $recordsArr = array_merge($privateRecordsArr, $publicRecordsArr);



        // Insert the records

        try {
            $delete = Product_Tags::where("ProductId", $productId)->delete();

            $ProductTag = Product_Tags::insert($recordsArr);
            // $Product = Product::find($productId);
            // $ProductTag = $Product->Tags()->sync($recordsArr);

            if ($ProductTag) {
                $privateTagsReturnedArray = Tags::getTagsIdNameByIds($privateTagIds);
                $publicTagsReturnedArray = Tags::getTagsIdNameByIds($publicTagIds);
                return response()->json([
                    "PrivateTags" => $privateTagsReturnedArray,
                    "PublicTags" => $publicTagsReturnedArray
                ]);
                //return response()->json(["response" => "Tags has been created successfully."]);
            }


            return response()->json(["message" => "oop! something went wrong.", "error" => $ProductTag], 403);
        } catch (Exception $e) {
            return response()->json(["message" => "oop! something went wrong.", "error" => $e->getMessage()], 403);
        }
    }

    /**
     * New Tag created and return the tag id
     * @return int
     */

    public function newTagCreate($tageName = null)
    {

        $Tag =  Tags::create([
            "TagName" => $tageName
        ]);
        try {
            if ($Tag) {

                return  $Tag->TagId;
            }

            return 0;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * check that whether the id is set and greater than 0
     *@return int
     */

    public function isValueSet($value = 0)
    {
        $id =  isset($value) && $value > 0 ? $value : 0;

        return $id;
    }
}
