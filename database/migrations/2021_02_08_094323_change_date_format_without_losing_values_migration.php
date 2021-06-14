<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PreOrderHdr;
use Illuminate\Support\Facades\DB;

class ChangeDateFormatWithoutLosingValuesMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('PreOrderHdr')) {
            //get the columns
            $columns =  $this->columns();
            foreach ($columns  as $c) {
                //create backup column name
                $NewColumn =  $c . "_bk";

                //add new column
                Schema::table('PreOrderHdr', function (Blueprint $table) use ($NewColumn) {
                    $table->date($NewColumn)->nullable();
                });

                //update to null values if the length less than 8
                PreOrderHdr::whereRaw("LEN($c) < ?", [8])->update(array($c => null));

                //change the numeric value to date value
                PreOrderHdr::whereRaw("LEN($c) = ? and $c is not null and PreOrderNum !=?", [8, 65444])
                    ->update(array(
                        $NewColumn => DB::raw("format(convert(date,CONVERT(nvarchar, $c)), 'yyyy-MM-dd')")
                    ));

                //drop the exsiting column and rename the new column to existing column
                Schema::table('PreOrderHdr', function (Blueprint $table) use ($c, $NewColumn) {
                    $table->dropColumn($c);
                    $table->renameColumn($NewColumn, $c);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function columns()
    {
        return array("CancelDate", "StartDate", "InStoreDate");
    }
}
