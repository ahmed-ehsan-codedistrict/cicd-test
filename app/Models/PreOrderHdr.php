<?php

namespace App\Models;

use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PreOrderHdr_Tags;
use App\Utilities\Helpers;
use App\User;
use App\Models\GDTS_BuyType;
use App\Models\ProdPLM;
use Illuminate\Support\Facades\Validator;
use App\Models\PreOrderDtl;
use Dotenv\Regex\Success;
use Illuminate\Support\Arr;
use Psy\CodeCleaner\FunctionContextPass;

class PreOrderHdr extends BaseModel
{
    public $table = 'PreOrderHdr';
    public $timestamps = false;

    protected $fillable = [
        'CompanyNo', 'PreOrderNum', 'PreOrderStatus',
        'CustAcct', 'CancelDate', 'PreOrderType',
        'Buyer', 'Lbl', 'SizeRange', 'Grp', 'Salesperson',
        'ProdType', 'Transferred', 'SwatPOAssigned',
        'SwatPOAssigned2', 'SwatPOAssigned3', 'SwatPOAssigned4',
        'SwatPOAssigned5', 'Division', 'DateCreated', 'UserCreated',
        'DateMaintained', 'UserMaintained', 'DraftDate',
        'CustomerSvcDate', 'PrintDesign', 'PrintProd',
        'PrintedDate', 'EmailCustSvc', 'InStoreDate', 'Region',
        'TotalExt', 'TotalUnits', 'CustomerRef', 'StartDate', 'LogNo',
        'CustNo', 'PayType', 'BOMDate', 'FinalDate', 'Shipto',
        'ShipAddress', 'CCType', 'OMQueue', 'OrderMarginDate',
        'UserAssign', 'OrdertoAS400', 'TOPHold', 'FitAprv',
        'Season', 'EcommCust', 'OrdTyp', 'Description', 'CustDept',
        'UnconfirmScale', 'NordRack'
    ];

    public function Tags()
    {
        return $this->belongsToMany('App\Models\Tags', 'PreOrderHdr_Tags', 'PreOrderNum', 'TagId');
    }

    //CRUD
    public static function updateOrderHeader($fieldAssocArr, $orderNumber = 0,   $Tags = null)
    {
        try {

            DB::beginTransaction();

            $Update  =  PreOrderHdr::where("PreOrderNum", $orderNumber)
                ->update($fieldAssocArr);

            // Update the Tags
            if ($Update) {
                $Tag =  PreOrderHdr::insertNewOrderTags($orderNumber, $Tags);
            }

            DB::commit();
            return $Update;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    // get all orders for a person
    public static function getAll($filters = null)
    {
        try {
            $pageNo = $filters['pageNumber'];
            $recordsPerPage = $filters['recordPerPage'];
            $offset =  $pageNo * $recordsPerPage;

            $orders = PreOrderHdr::distinct("PreOrderHdr.PreOrderNum")
                ->select(
                    "PreOrderHdr.PreOrderNum as OrderNumber",
                    "PreOrderHdr.CustAcct as Customer",
                    "PreOrderHdr.startDate as startDate",
                    "PreOrderHdr.cancelDate as cancelDate",
                    "PreOrderHdr.Division as Division",
                    "PreOrderHdr.Grp as Group",
                    "PreOrderHdr.Lbl as Label",
                    DB::raw("'Unconfirmed' AS CustomerPo"),
                    DB::raw("
                    CASE  PreOrderHdr.PreOrderStatus
                        WHEN 'M' then 'Marchendise'
                        when 'F' then 'Finalized'
                        when 'D' then 'Draft'
                        when 'S' then 'Sourcing'
                        when 'P' then 'Production'
                    END AS OrderStatus
               "),
                    "PreOrderHdr.UserCreated as Creater",
                    DB::raw("CONCAT_WS(',',SwatPOAssigned,SwatPOAssigned2,SwatPOAssigned3,SwatPOAssigned4,SwatPOAssigned5) as SWATPo"),
                    DB::raw("
                    CASE  PreOrderHdr.PreOrderType
                        WHEN 'O' then 'Order'
                        when 'S' then 'Sourcing'
                    END AS OrderType
                "),
                    DB::raw("'Unconfirmed' as PaymentStatus"),
                    "PreOrderHdr.Salesperson as Salesperson",
                    DB::raw("iif(PreOrderHdr.DateMaintained is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, PreOrderHdr.DateMaintained)), 'dd-MM-yyyy') ) as UserModifiedDate"),
                    DB::raw("iif(PreOrderHdr.DateCreated is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, PreOrderHdr.DateCreated)), 'dd-MM-yyyy') )  as CreatedDate")
                )
                ->Join("PreOrderDtl as pod", function ($join) {
                    $join->on('pod.PreOrderNumdtl', '=', 'PreOrderHdr.PreOrderNum');
                })
                ->leftJoin("PreOrderHdr_Tags as pt", function ($join) {
                    $join->on('pt.PreOrderNum', '=', 'PreOrderHdr.PreOrderNum');
                });
            if (isset($filters['Filter']) && count($filters['Filter']) > 0) {


                foreach ($filters['Filter'] as $key => $value) {

                    if ($key != "swatPo") {
                        $orders  = Helpers::addDynamicWheres($orders, $value, $key);
                    }
                }

                if (
                    array_key_exists("swatPo", $filters['Filter'])
                ) {

                    $orders = $orders->where("PreOrderHdr.SwatPOAssigned", $filters['Filter']['swatPo']['id'])
                        ->orWhere("PreOrderHdr.SwatPOAssigned2", $filters['Filter']['swatPo']['id'])
                        ->orWhere("PreOrderHdr.SwatPOAssigned3", $filters['Filter']['swatPo']['id'])
                        ->orWhere("PreOrderHdr.SwatPOAssigned4", $filters['Filter']['swatPo']['id'])
                        ->orWhere("PreOrderHdr.SwatPOAssigned5", $filters['Filter']['swatPo']['id']);
                }
            }
            $orders =  $orders->orderBy("PreOrderHdr.PreOrderNum", "desc")
                ->offset($offset)
                ->limit($recordsPerPage)
                ->get();

            return $orders;
        } catch (\Throwable $th) {
            return  $th;
        }
    }


    //insert new tags
    public static function insertNewOrderTags($orderNumber, $Tags)
    {
        $delete = PreOrderHdr_Tags::where("PreOrderNum", $orderNumber)->delete();

        $OrderTag = PreOrderHdr_Tags::insert($Tags);

        return $OrderTag;
    }

    public static function getOrderFields($orderNo)
    {
        $companyNo = Auth::user()->CompanyNo;
        $orderFields = DB::table("PreOrderHdr as po")
            ->select(
                "po.PreOrderNum as OrderNumber",
                "op.DisplayValue as StatusValue",
                "op.DisplayID as Status",
                "po.PreOrderType as WorksheetType",
                "op2.DisplayValue as WorksheetTypeValue",
                // DB::raw("
                //         CASE  po.PreOrderType
                //             WHEN 'O' then 'Order'
                //             when 'S' then 'Sourcing'
                //         END AS WorksheetTypeValue
                //     "),
                "po.CustAcct as CustAcct",
                "po.CustNo as CustNo",
                DB::raw("IIF ( (ec.Custno>0), 1, 0) AS EcommCust"),
                DB::raw("iif(po.StartDate is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, po.StartDate)), 'dd-MM-yyyy') ) as StartDate"),
                DB::raw("iif(po.CancelDate is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, po.CancelDate)), 'dd-MM-yyyy') ) as CancelDate"),
                DB::raw("iif(po.InStoreDate is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, po.InStoreDate)), 'dd-MM-yyyy') ) as InStoreDate"),
                "po.Description",
                "po.TOPHold",
                "po.FitAprv",
                DB::raw("trim(po.Region) as Region"),
                DB::raw("trim(po.Division) as Division"),
                DB::raw("trim(Salesperson) as SalesPerson"),
                DB::raw("trim(po.OrdTyp) as OrderType"),
                DB::raw("trim(po.Lbl) as Lbl"),
                DB::raw("trim(po.Grp) as Grp"),
                "po.CustDept as CustDept",
                DB::raw("trim(po.CustomerRef) as CustomerRef"),
                DB::raw("trim(po.Buyer) as Buyer"),
                DB::raw("trim(po.FabricCode) as FabricCode"),
                "po.TotalUnits as TotalUnits",
                "po.TotalExt as TotalExt",
                "po.UserCreated",
                DB::raw("iif(po.DateMaintained is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, po.DateMaintained)), 'dd-MM-yyyy') ) as UserModifiedDate"),
                DB::raw("iif(po.DateCreated is null , '', FORMAT (convert(datetime,CONVERT(nvarchar, po.DateCreated)), 'dd-MM-yyyy') ) as CreatedDate"),
                "po.UserMaintained",
                "pt.tagIds as tags"


            );

        $orderFields = $orderFields->Join('Options as op', function ($join) use ($companyNo) {
            $join->on('op.DisplayId', '=', 'po.PreOrderStatus')
                ->where('op.CompanyNo', '=', $companyNo)
                ->where('op.TableName', '=', 'PreOrderHdr')
                ->where('op.TableColumn', '=', 'PreOrderStatus');
        })->Join('Options as op2', function ($join) use ($companyNo) {
            $join->on('op2.DisplayId', '=', 'po.PreOrderType')

                ->where('op2.CompanyNo', '=', $companyNo)
                ->where('op2.TableName', '=', 'PreOrderHdr')
                ->where('op2.TableColumn', '=', 'WorkSheetType');
        })
            ->leftJoin("EcommCustomer as ec", function ($join) {
                $join->on("po.CustNo", "=",  "ec.Custno");
            })
            ->leftJoin(DB::raw("(select
             pt.PreOrderNum,pt.CompanyNo,   string_agg (cast(concat(t.TagId, '-' ,t.TagName) as NVARCHAR(MAX)) , ',') as tags,
             string_agg (cast(t.TagId as NVARCHAR(MAX)) , ',') as tagIds
             from PreOrderHdr_Tags pt
              left join Tags t
             on t.TagId =  pt.TagId
            GROUP by pt.PreOrderNum , pt.CompanyNo) as pt"), function ($join) {
                $join->on("pt.PreOrderNum", "=",  "po.PreOrderNum");
            })

            ->where('po.PreOrderNum', $orderNo)

            ->get();
        return $orderFields;
    }

    public static function getOrderDetails($orderNo, $pageNo, $recordsPerPage)
    {

        try {
            $offset =  $pageNo * $recordsPerPage;
            $companyNo = Auth::user()->CompanyNo;
            $orders = DB::table("PreOrderHdr as po")
                            ->select(
                                DB::RAW("Replace(TRIM(ph.EXDS3K),'','') as ProductName"),
                                "pd.PreOrderLinenum as LineNumber",
                                "pd.Style",
                                "pd.ReferenceStyle",
                                DB::RAW("Replace(TRIM(pd.ReferenceColor),'','') as ReferenceColor"),
                                DB::RAW("Replace(TRIM(pd.Color),'','') as Color"),
                                "pd.Fabric",
                                "pd.ReferenceFabric",
                                "cl.NCLR3J as NRFColorCode",
                                "pd.BuyType",
                                "pd.ReOrder",
                                "pd.CadReq",
                                "pd.ReferenceStyle",
                                "pd.OrderType as OrderType",
                                "pd.CutMinimum",
                                "pd.ProdType as ProductionType",
                                "pd.CutInstruction",
                                "pd.Price",
                                "ph.RTPR3K as MSRP",
                                "pd.Sale",
                                "pd.AdSampleDate",
                                "pd.AdQty",
                                "pd.TOPSample",
                                "pd.AdSample",
                                "pd.LineSample",
                                "pd.LineSampleDate",
                                "pd.LineQty",
                                "pd.TopDate",
                                "pd.TopQty",
                                DB::RAW("CONCAT(pd.Instructions1,pd.Instructions2,pd.Instructions3,pd.Instructions4) as Instructions"),
                                DB::RAW("CONCAT(pd.RefStyleInstructions1,pd.RefStyleInstructions2,pd.RefStyleInstructions3,pd.RefStyleInstructions4) as RefStyleInstructions"),
                                "pd.Description",
                                "pd.MarginDeptQty",
                                "pd.MarginDeptPrice",
                                "pd.MarginDeptDscQty",
                                "pd.MarginDeptDscPrice",
                                "pd.MarginSpecialtyQty",
                                "pd.MarginSpecialtyPrice",
                                "po.SizeRange",
                                "pd.Scale1",
                                "pd.Scale2",
                                "pd.Scale3",
                                "pd.Scale4",
                                "pd.Scale5",
                                "pd.Scale6",
                                "pd.Scale7",
                                "pd.Scale8",
                                "pd.Scale9",
                                "pd.Scale10",
                                "pd.Scale11",
                                "pd.Scale12",
                                "pd.Qty",
                                "pd.NoSizeRatio",
                                "sa.e101 as FirstQuality1",
                                "sa.e102 as FirstQuality2",
                                "sa.e103 as FirstQuality3",
                                "sa.e104 as FirstQuality4",
                                "sa.e105 as FirstQuality5",
                                "sa.e106 as FirstQuality6",
                                "sa.e107 as FirstQuality7",
                                "sa.e108 as FirstQuality8",
                                "sa.e109 as FirstQuality9",
                                "sa.e110 as FirstQuality10",
                                "sa.e111 as FirstQuality11",
                                "sa.e112 as FirstQuality12",
                                "sa.ewp01 as WIP1",
                                "sa.ewp02 as WIP2",
                                "sa.ewp03 as WIP3",
                                "sa.ewp04 as WIP4",
                                "sa.ewp05 as WIP5",
                                "sa.ewp06 as WIP6",
                                "sa.ewp07 as WIP7",
                                "sa.ewp08 as WIP8",
                                "sa.ewp09 as WIP9",
                                "sa.ewp10 as WIP10",
                                "sa.ewp11 as WIP11",
                                "sa.ewp12 as WIP12",
                                "sa.ATS01",
                                "sa.ATS02",
                                "sa.ATS03",
                                "sa.ATS04",
                                "sa.ATS05",
                                "sa.ATS06",
                                "sa.ATS07",
                                "sa.ATS08",
                                "sa.ATS09",
                                "sa.ATS10",
                                "sa.ATS11",
                                "sa.ATS12",
                                "pd.Ad01",
                                "pd.Ad02",
                                "pd.Ad03",
                                "pd.Ad04",
                                "pd.Ad05",
                                "pd.Ad06",
                                "pd.Ad07",
                                "pd.Ad08",
                                "pd.Ad09",
                                "pd.Ad10",
                                "pd.Ad11",
                                "pd.Ad12",
                                "pd.TOP01",
                                "pd.TOP02",
                                "pd.TOP03",
                                "pd.TOP04",
                                "pd.TOP05",
                                "pd.TOP06",
                                "pd.TOP07",
                                "pd.TOP08",
                                "pd.TOP09",
                                "pd.TOP10",
                                "pd.TOP11",
                                "pd.TOP12",
                                "pd.Line01",
                                "pd.Line02",
                                "pd.Line03",
                                "pd.Line04",
                                "pd.Line05",
                                "pd.Line06",
                                "pd.Line07",
                                "pd.Line08",
                                "pd.Line09",
                                "pd.Line10",
                                "pd.Line11",
                                "pd.Line12"
                        );


            $style = PreOrderDtl::where('PreOrderNumdtl',$orderNo)->pluck('Style');

            $orders = $orders->Join('PreOrderDtl as pd', function ($join) use ($companyNo) {
                $join->on('pd.PreOrderNumdtl', '=', 'po.PreOrderNum')
                    ->where('pd.CompanyNo', '=', $companyNo);
            })

            ->Join('PRHDMS0 as ph', function ($join) {
                $join->on('ph.CompanyNo', '=', 'pd.CompanyNo')
                    ->whereRaw("ph.Style = pd.Style  and pd.ReferenceStyle is null")
                    ->OrwhereRaw("ph.Style = pd.ReferenceStyle  and pd.ReferenceStyle is not null");
            })
            ->Join('StyleAvail as sa', function ($join) {
                $join->on('sa.CompanyNo', '=', 'pd.CompanyNo')
                    ->whereRaw("sa.Style = pd.Style  and pd.ReferenceStyle is null")
                    ->OrwhereRaw("sa.Style = pd.ReferenceStyle  and pd.ReferenceStyle is not null");
            })
            ->Join('COLRMS0 as cl', function ($join) {
                $join->on('cl.CompanyNo', '=', 'pd.CompanyNo')
                    ->whereRaw("cl.Color = pd.Color  and pd.ReferenceStyle is null")
                    ->OrwhereRaw("cl.Color = pd.ReferenceColor  and pd.ReferenceStyle is not null");
            })
            ->where('po.PreOrderNum', '=', $orderNo);
            $orders = $orders->offset($offset)
            ->limit($recordsPerPage)
            ->get();

            // Color
            $o = $orders;
            // $fabArr = PreOrderHdr::getFabric();
            foreach($orders as $o)
            {
                $o->AvailableCutInstruction = [];
                $o->AvailableOrderType = [];
                $o->AvailableCutMinimum = [];
                $o->AvailableBuyType = [];
                $o->AvailableProductionType = [];
                $o->AvailableFabrics = [];
                $o->AvailableColors = [];
                $o->AvailableSizeRange = [];
                if(!$o->ReferenceColor)
                {
                    $colorArr = PRDTMS0::getColors($o->Style);
                    foreach($colorArr as $c)
                    {
                        array_push($o->AvailableColors, $c['value']);
                    }
                }
                else
                {
                    $colorArr = PRDTMS0::getColors($o->ReferenceStyle);
                    foreach($colorArr as $c)
                    {
                        array_push($o->AvailableColors, $c['value']);
                    }
                }
            }

            //Fabric
            if(ProdPLM::distinct('FabricContent')->select('FabricContent')->whereRaw('FabricContent is not null')->exists())
            {
                $o->AvailableFabrics = array();
                foreach($orders as $o)
                {
                    $o->AvailableFabrics = [];
                    $o->AvailableFabrics = ProdPLM::distinct('value')->select(DB::raw("Replace(TRIM(FabricContent),' ', '') as value"))->where('FabricContent','<>',null)->where('FabricContent','<>','')
                                                    ->pluck('value');
                }
            }

            // OrderType
            $o->AvailableOrderType = array();
            foreach($orders as $o)
            {
                $o->AvailableOrderType = ['Cut to Order', 'Pull from Projection/Stock'];
            }

            // Size Range
            $o->AvailableSizeRange = array();
            foreach($orders as $o)
            {
                $o->AvailableSizeRange = ['4-6X','7-16','8.5-18.5','1-15','23-32'];
            }

            // Buy Type
            if(GDTS_BuyType::distinct('BuyTypeDesc')->select('BuyTypeDesc')->whereRaw('BuyTypeDesc is not null')->exists())
            {
                $o->AvailableBuyType = array();
                foreach($orders as $o)
                {
                    $o->AvailableBuyType = [];
                    $o->AvailableBuyType = GDTS_BuyType::distinct('BuyTypeDesc')->where('BuyTypeDesc','<>','')->where('BuyTypeDesc','<>',null)->pluck('BuyTypeDesc');
                }
            }

            // Production Type
            $o->AvailableProductionType = array();
            foreach($orders as $o)
            {
                $o->AvailableProductionType = ['Domestic', 'Import'];
            }

            return $orders;
        } catch (\Throwable $th) {
            return ['message' => "Something went wrong", 'error' => $th->getMessage()];
        }
    }

    public static function editOrderDetailProduct($orderDetail)
    {
        try{
            $companyNo = Auth::user()->CompanyNo;
            if(PreOrderDtl::select('PreOrderNumdtl')->where('PreOrderNumdtl',$orderDetail['PreOrderNumdtl'])->where('PreOrderLinenum',$orderDetail['PreOrderLinenum'])->exists())
            {
                DB::beginTransaction();
                $orders = PreOrderDtl::where('PreOrderNumdtl', $orderDetail['PreOrderNumdtl'])
                                     ->where('PreOrderLinenum', $orderDetail['PreOrderLinenum'])
                                     ->where('CompanyNo',$companyNo)
                                     ->update($orderDetail);
                if($orders == 1)
                {
                    DB::commit();
                    return ['message' => "Order Edited Successfully", 'success'=>true, 'error'=>null, 'code'=>200];
                }
                else
                {
                    DB::rollback();
                    return ['message' => "Something Went Wrong", 'success'=>false, 'error'=>"Order didn't updated successfully", 'code'=>400];
                }
            }
            else
            {
                return ['message' => "Something Went Wrong", 'success'=>false, 'error'=>"LineNumber/OrderNo didn't exists", 'code'=>400];
            }
        } catch(\Throwable $th) {
            return ['message' => "Something went wrong", 'success'=>false, 'error' => $th->getMessage(), 'code'=>400];
        }
    }

    public static function deleteOrder($OrderId, $Password = null)
    {
        if (PreOrderHdr::where('PreOrderNum', $OrderId)->exists()) {
            if (PreOrderHdr::where('PreOrderNum', $OrderId)->where('PreOrderStatus', 'P')->exists()) {
                $validator = Validator::make(["Password" => $Password], [
                    'Password' => 'string|required',
                ]);
                $errors = $validator->errors()->get('Password');
                if ($Password) {
                    $user = User::withoutGlobalScopes()->with('company')->where('id', Auth::id())->first();
                    if (!strcmp($Password, $user->password)) {
                        PreOrderHdr::where('PreOrderNum', $OrderId)->where('PreOrderStatus', 'P')->delete();
                        PreOrderDtl::where('PreOrderNumdtl', $OrderId)->delete();
                        return ['message' => "Deleted Successfully", 'success' => true, 'error' => null, 'code' => 200];
                    } else {
                        return ['message' => "Wrong Password", 'success' => false, 'error' => "Wrong Password Entered", 'code' => 403];
                    }
                } else {
                    return ['message' => "Password Requied", 'success' => false, 'error' => $errors[1], 'code' => 403];
                }
            } else {
                PreOrderHdr::where('PreOrderNum', $OrderId)->delete();
                PreOrderDtl::where('PreOrderNumdtl', $OrderId)->delete();
                return ['message' => "Deleted Successfully", 'success' => true, 'error' => null, 'code' => 200];
            }
        }
    }

    public static function getSwatPO()
    {
        $swatPO = PreOrderHdr::select(DB::raw("CONCAT(SwatPOAssigned, ',',SwatPOAssigned2,',',SwatPOAssigned3,',',SwatPOAssigned4,',',SwatPOAssigned5) AS value"))
            ->whereRaw('SwatPOAssigned IS NOT NULL
        OR
        SwatPOAssigned2 IS NOT NULL
        OR
        SwatPOAssigned3 IS NOT NULL
        OR
        SwatPOAssigned4 IS NOT NULL
        OR
        SwatPOAssigned5 IS NOT NULL')
            ->get();
        $swat = [];
        foreach ($swatPO as $s) {
            array_push($swat, (array_values(array_unique(explode(',', $s->toArray()['value'])))));
        }
        $swatArray = Arr::flatten($swat);
        $swatArrayFinal = [];
        foreach($swatArray as $swat) {
            if($swat)
                array_push($swatArrayFinal,[
                    "ID" => $swat,
                    "value" => $swat
                    ]);
        }
        return $swatArrayFinal;
    }

    public static function assignSizesToSImilarProducts($sizeArray)
    {
        try{
            PreOrderDtl::where('PreOrderNumdtl',$sizeArray['OrderNo'])->whereIn('PreOrderLinenum',$sizeArray['LineNumber'])->update([
                'Qty' => $sizeArray['Qty'],
                'NoSizeRatio' => $sizeArray['NoSizeRatio'],
                'Scale1' => $sizeArray['Scale1'],
                'Scale2' => $sizeArray['Scale2'],
                'Scale3' => $sizeArray['Scale3'],
                'Scale4' => $sizeArray['Scale4'],
                'Scale5' => $sizeArray['Scale5'],
                'Scale6' => $sizeArray['Scale6'],
                'Scale7' => $sizeArray['Scale7'],
                'Scale8' => $sizeArray['Scale8'],
                'Scale9' => $sizeArray['Scale9'],
                'Scale10' => $sizeArray['Scale10'],
                'Scale11' => $sizeArray['Scale11'],
                'Scale12' => $sizeArray['Scale12'],
            ]);
            return ['message'=>'Successfully Assigned', 'success'=>true,'error'=>null, 'code'=>200];
        } catch(\Throwable $th) {
            return ['message' => "Something went wrong", 'success'=>false, 'error' => $th->getMessage(), 'code'=>400];
        }
    }

}
