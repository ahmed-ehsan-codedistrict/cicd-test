<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\LineSheets;
use App\Http\Controllers\Controller;

class checkUnqiueValuOneUpdate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $lsID;
    private $lsCompanyNo;
    private $lsUserId;

    public function __construct($lsID = 0, $companyNo = 0, $userId = 0)
    {

        $this->lsID =  $lsID;
        $this->lsCompanyNo =  $companyNo;
        $this->lsUserId =  $userId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $LineSheet =  LineSheets::find($this->lsID);

        if ($LineSheet->lineSheetName == $value) {
            return true;
        }

        if ($LineSheet->lineSheetName != $value) {

            $LineSheet = LineSheets::where('lineSheetName', '=', $value)
                ->where('createdBy', '=', $this->lsUserId)
                ->where('companyNo', '=', $this->lsCompanyNo)->count();

            if ($LineSheet > 0) {
                return false;
            }
            if ($LineSheet == 0) {
                return true;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return   "LineSheet name must be unique";
    }
}
