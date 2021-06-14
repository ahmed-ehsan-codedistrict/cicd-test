<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LineSheetGroup;
use App\Models\LineSheets;
use App\Models\LineSheetShare;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LineSheetGroupController extends Controller
{

    /**
     * Update and Add the LineSheet Groups
     */
    public function store(Request $request)
    {
        try {

            //validation
            $request->validate([
                "groupName" => "required|string",
                "lineSheetId" => "required"
            ]);

            if (LineSheets::canLineSheetEdit($request->lineSheetId) > 0) {
                //create group
                if (isset($request->id) && $request->id == 0) {

                    $response =  LineSheetGroup::createGroup($request->groupName, $request->lineSheetId);

                    if ($response) {
                        $id = DB::getPdo()->lastInsertId();
                        return response()->json(LineSheetGroup::select('id as GroupId', 'LineSheetId', 'GroupName')->where('id', $id)->get());
                    }
                    return response()->json(["message" => "Something went wrong.", "success" => false], 403);
                }

                //update  group
                if (isset($request->id) && $request->id > 0) {

                    $response =  LineSheetGroup::updateGroup($request->id, $request->groupName, $request->lineSheetId);

                    if ($response) {
                        return response()->json(["message" => "Line Sheet group has been updated successfully.", "success" => true, "error" => null], 200);
                    }
                    return response()->json(["message" => "Something went wrong.", "success" => false, "error" => null], 403);
                }
            } else {
                return response()->json(["message" => "You have not permission to create/edit the group", "success" => false, "error" => null], 403);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Something went wrong', 'success' => false, "error" => $th->getMessage()], 403);
        }
    }

    public function show(Request $request)
    {
        //validating inputs
        $request->validate([
            'LinesheetId' => 'integer|required'
        ]);
        return response()->json(LineSheetGroup::getLineSheetGroups($request->LinesheetId));
    }
}
