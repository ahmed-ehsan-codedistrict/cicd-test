<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomSortCombination extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : CustomSortCombination
     */
    public $table = 'CustomSortCombination';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Primary key is not incremented
     *
     * @var boolean
     */
    public $incrementing = true;


    /**
     * Attributes need to be filled
     */
    protected $fillable = [
        'tblAlias', 'SortByColumn', 'Priority', 'UserId', 'GroupId', 'LineSheetId'
    ];


    //function
    //delete user previous sort combination
    public static function deleteUserPreviousSortCombination($lineSheetId, $groupId)
    {
        $deleted =  CustomSortCombination::where([
            'UserId' => Auth::id(),
            'GroupId' => $groupId,
            'LineSheetId' => $lineSheetId,
        ])->delete();
    }

    // check that user has current combination is exist

    public static function checkUserSortCombinationIsExist($sortByColumn, $lineSheetId, $groupId)
    {

        $UserCombinatinRecord = CustomSortCombination::select(
            [
                'id',
                'SortByColumn'
            ]
        )->where(
            [
                'UserId' => Auth::id(),
                'GroupId' => $groupId,
                'LineSheetId' => $lineSheetId
            ]

        )->first();

        return $UserCombinatinRecord;
    }

    //store the user combination
    public static function addUserSortCombination($sortByColumn, $lineSheetId, $groupId)
    {

        $IsCombinationCreated  = CustomSortCombination::create(
            [
                'UserId' => Auth::id(),
                'GroupId' => $groupId,
                'LineSheetId' => $lineSheetId,
                'SortByColumn' => $sortByColumn
            ]
        );

        return $IsCombinationCreated->id;
    }

    // update the custom sort combination
    public static function updateUserSortCombination($sortByColumn, $id, $GroupId)
    {
        $UserCombinatinRecord = CustomSortCombination::where(
            [
                'UserId' => Auth::id(),
                'id' => $id,
                'GroupId' => $GroupId
            ]

        )->update([
            'SortByColumn' => $sortByColumn
        ]);

        return  $UserCombinatinRecord;
    }
}
