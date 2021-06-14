<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StyleAvail extends BaseModel
{
    public $table = 'StyleAvail';

    protected $fillable = ['CompanyNo','Company','Style','Color','Size','FirstQuality',
                            'OpenOrders','WIP','Available','ExtraFirst','ExtraWIP',
                            'OpenToSell','Damaged','ResTotal','ResCust','ResEcom',
                            'ResSales','SetStyle','ComponentStyle','ParentStyle','ParentColor',
                            'SetComponent','AV015V','AV025V','AV035V','AV045V','AV055V',
                            'AV065V','AV075V','AV085V','AV095V','AV105V','AV115V','AV125V',
                            'ATS01','ATS02','ATS03','ATS04','ATS05','ATS06','ATS07','ATS08',
                            'ATS09','ATS10','ATS11','ATS12','e101','e102','e103','e104',
                            'e105','e106','e107','e108','e109','e110','e111','e112','ewp01',
                            'ewp02','ewp03','ewp04','ewp05','ewp06','ewp07','ewp08','ewp09',
                            'ewp10','ewp11','ewp12','UA015V','UA025V','UA035V','UA045V',
                            'UA055V','UA065V','UA075V','UA085V','UA095V','UA105V','UA115V',
                            'UA125V','RT01','RT02','RT03','RT04','RT05','RT06','RT07','RT08',
                            'RT09','RT10','RT11','RT12','RC01','RC02','RC03','RC04','RC05',
                            'RC06','RC07','RC08','RC09','RC10','RC11','RC12','RE01','RE02',
                            'RE03','RE04','RE05','RE06','RE07','RE08','RE09','RE10','RE11',
                            'RE12','RS01','RS02','RS03','RS04','RS05','RS06','RS07','RS08',
                            'RS09','RS10','RS11','RS12'];

                            public function COMPMS0()
                            {
                                return $this->belongsTo('App\Models\COMPMS0');
                            }

                            public function COLRMS0()
                            {
                                return $this->belongsTo('App\Models\COLRMS0');
                            }

                            public function PRHDMS0()
                            {
                                return $this->belongsTo('App\Models\PRHDMS0');
                            }

                            public static function getAvailable($type)
                            {
                                if($type == 'availability')
                                {
                                    return array_values(config('constants.styleAvailable.availability'));

                                }
                                else
                                {
                                    return array_values(config('constants.styleAvailable.availabilityToSell'));
                                }
                            }
}
