<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Models\Tags;
use App\Utilities\Helpers;
use Illuminate\Support\Facades\Auth;
use App\Models\PreOrderDtl;
use App\Models\SZSCMS0;
use App\Models\PRDAVL0;

class Product extends Model
{

    /**
     * The table use against this model
     *
     * @var string
     * Table Name : Product Header
     */
    public $table = 'PRHDMS0';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'Style';

    /**
     * Primary key is not incremented
     *
     * @var boolean
     */
    public $incrementing = false;


    /**
     * Attributes need to be filled
     */
    protected $fillable = [
        'CompanyNo', 'Style', 'PRDS3K', 'SHDS3K', 'EXDS3K', 'CLCD3K', 'SCCD3K',
        'DivisionNo', 'MKGP3K', 'STCD3K', 'SZCD3K', 'RTPR3K', 'DateCreated', 'DateMaintained',
        'AS400Style'
    ];



    /** Relationships */

    public function size()
    {
        return $this->hasOne(SZSCMS0::class, 'SZCD3G', 'SZCD3K');
    }

    public function cost()
    {
        return $this->hasMany(ProdCost::class, 'Style', 'Style');
    }

    public function Tags()
    {
        return $this->belongsToMany('App\Models\Tags', 'Product_Tags', 'ProductId', 'TagId');
    }

    // one product belongs to many group
    public function groups()
    {
        return $this->belongsToMany('App\Models\LineSheetGroup', 'LineSheetGroupProducts', 'ProductId', 'GroupId');
    }

    // one product has many private notes in linesheet
    public function lsgprivatenotes()
    {
        return $this->hasMany('App\Models\LSGPPrivatNotes', 'ProductId', 'Style');
    }


    /** Custom defined function  */

    /**
     * function is used to return all the products based on filters
     *
     * @return array
     */


    public static function getProducts(
        $userId,
        $companyNo,
        array $filters = null,
        array $divisions = null,
        $groupId = 0,
        $workSpace = 0,
        $productArrWithColors = null,
        $lineSheetIdForExport = 0,
        $skipProducts = 0
    ) {

        $pageNo = $filters['pageNumber'];
        $recordsPerPage = $filters['recordPerPage'];
        $offset =  $pageNo * $recordsPerPage;

        $Products =  DB::table("PRHDMS0 as ph")
            ->select(
                "ph.Style as StyleNumber",
                DB::raw("trim(max(ph.Style ))as productId"),
                DB::raw("max(ph.EXDS3K )as ProductName"),
                DB::raw("max(ph.RTPR3K ) as Price"),
                DB::raw("max(ph.PRDS3K) as ProductDescription"),
                DB::raw("max(sa.WIP) as WIP"),
                DB::raw("max(sa.Color) as SAColor"),
                DB::raw("max(sa.FirstQuality) AS FirstQuality"),
                DB::raw("max(sa.Available) as Total"),
                DB::raw("max(ph.SZCD3K) as SizeCode"),
                DB::raw("max(div.DVNM3C) as Division"),
                DB::raw("max(ph.DivisionNo) as DivisionNo"),
                DB::raw("max(class.CLDS3D) as Class"),
                DB::raw("max(sClass.SCDS3E) as SubClass"),
                DB::raw("max(mk.MKDS3N) as MarketGroup"),
                DB::raw("max(plm.Season) as Season"),
                DB::raw("max(plm.Market) as Market"),
                DB::raw("max(plm.FabricType) as FabType"),
                DB::raw("max(plm.FabricType) as StyleName"),
                DB::raw("max(plm.FabricName) as FabName"),
                DB::raw("max(plm.FabricName) as FabricContent"),
                DB::raw("max(plm.Brand) as Brand"),
                DB::raw("dbo.ProductInfo(max(ph.CompanyNo), ph.Style, 5) as QueryAttributes"),
                DB::raw("dbo.ProductInfo(max(ph.CompanyNo), ph.Style, 6) as ColorCodeName"),
                DB::raw("max(upc.UCPNXC) as UPCXInfo"),
                DB::raw("(select

                    concat(
                    STRING_AGG(case when pt.userId>0 and pt.userId=$userId then concat(pt.TagId,'/',t.TagName) end,',')
                    ,
                    '||'
                    ,
                    STRING_AGG(case when pt.userId<=0 then concat(pt.TagId,'/',t.TagName) end,',')
                    ) as Tag

                    from Product_Tags pt
                    inner join Tags t
                    on pt.TagId = t.TagId
                    where pt.ProductId=ph.Style and pt.CompanyNo = $companyNo
                    ) as Tags
                    "),
                //DB::raw("STRING_AGG(case when pt.userId>0 and pt.userId=$userID then concat(pt.TagId,'/',t.TagName) end,',') as PrivateTags"),
                //DB::raw("STRING_AGG(case when pt.userId<=0 then concat(pt.TagId,'/',t.TagName) end,',') as PublicTags"),
                DB::raw("max(pd.Color) as MaxColor")
            );

        // add extra columns if the group id is passed

        if (($groupId != 0 && $groupId != '' && $groupId > 0) || $lineSheetIdForExport > 0) {

            $Products =  $Products->addSelect(
                DB::raw("max(lpg.ColorID) as ColorID"),
                DB::raw("max(pg.GroupName) as groupName"),
                DB::raw("max(lpg.groupId) as groupId"),
                DB::raw("max(lpg.id) as LSGPID"),
                DB::raw("max(lsppn.id) as LSGPPID"),
                DB::raw("max(lpg.PublicNotes) as LSGPPublicNotes"),
                DB::raw("max(lpg.DisplayOrder) as DisplayOrder"),
                DB::raw("max(lsppn.Notes) as LSGPPrivateNotes"),
                DB::raw("max(ls.lineSheetName) as lineSheetName"),
                DB::raw("max(ls.id) as lineSheetId")
            );
        }
        // Add this join if the product fetch for Groups
        if (($groupId != 0 && $groupId != '' && $groupId > 0) || $lineSheetIdForExport > 0) {

            $Products =  $Products->join("LineSheetGroupProducts as lpg", function ($join) use ($companyNo) {
                $join->on('ph.Style', '=', 'lpg.ProductId')
                    ->where('lpg.CompanyNo', '=', $companyNo);
            })
                ->join("LineSheetGroup as pg", function ($join) use ($companyNo) {
                    $join->on('pg.id', '=', 'lpg.GroupId')
                        ->where('pg.CompanyNo', '=', $companyNo);
                })
                ->join("LineSheets as ls", function ($join) use ($companyNo) {
                    $join->on('ls.id', '=', 'pg.LineSheetId')
                        ->where('ls.CompanyNo', '=', $companyNo);
                })

                ->leftJoin("LSGPPrivatNotes as lsppn", function ($join) use ($companyNo) {
                    $join->on('lpg.id', '=', 'lsppn.LSGPId')
                        ->where('lsppn.CompanyNo', '=', $companyNo)
                        ->where('lsppn.UserId', '=', Auth::id());
                })
                ->leftJoin("CustomSortCombination as csc", function ($join) use ($companyNo) {
                    $join->on('csc.LineSheetId', '=', 'ls.id');
                    $join->on('csc.GroupId', '=', 'lpg.GroupId')
                        ->where('csc.UserId', '=', Auth::id())
                        ->where('csc.CompanyNo', '=', $companyNo);
                })
                ->leftJoin("CustomSort as cs", function ($join) use ($companyNo) {
                    $join->on('cs.LSGPId', '=', 'lpg.id');
                    $join->on('cs.CombinationId', '=', 'csc.id')
                        ->where('cs.CompanyNo', '=', $companyNo);
                });
        }
        $Products = $Products->leftJoin(DB::raw("(SELECT
                        plm.Style,
                        plm.CompanyNo,
                        string_agg(REPLACE(RTRIM(season),'',''),',' ) As Season,
                        string_agg(REPLACE(RTRIM(Market),'',''),',' ) As Market,
                        string_agg(REPLACE(FabType,'',''),',' ) as FabricType,
                        string_agg(REPLACE(RTRIM(FabricName),'',''),',' ) as FabricName,
                        string_agg(REPLACE(RTRIM(Brand),'',''),',' ) as Brand,
                        string_agg(REPLACE(RTRIM(FabricContent),'',''),',' ) as FabricContent
                        FROM   ProdPLM plm
                        GROUP BY plm.Style,plm.CompanyNo) as plm
                    "), function ($join) use ($companyNo) {
            $join->on('plm.Style', '=', 'ph.Style')
                ->where('plm.CompanyNo', '=', $companyNo);
        });

        //join to get the unique product with quantity
        if ($groupId != 0 && $groupId != '' && $groupId > 0) {
            $Products =  $Products->leftJoin('StyleAvail as sa', function ($join) use ($companyNo) {
                $join->on('sa.Style', '=', 'ph.Style')
                    ->whereRaw('sa.Color = lpg.ColorId')
                    ->where('sa.CompanyNo', '=', $companyNo);
            });
        } else {
            $Products =  $Products->leftJoin('StyleAvail as sa', function ($join) use ($companyNo) {
                $join->on('sa.Style', '=', 'ph.Style')
                    ->where('sa.CompanyNo', '=', $companyNo);
            });
        }

        $Products =  $Products->leftJoin('DIVNMS0 as div', function ($join) use ($companyNo) {
            $join->on('div.DivisionNo', '=', 'ph.DivisionNo')
                ->where('div.CompanyNo', '=', $companyNo);
        })
            ->leftJoin('PRCLMS0 as class', function ($join) use ($companyNo) {
                $join->on('class.CLCD3D', '=', 'ph.CLCD3K')
                    ->where('class.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('PRSCMS0 as sClass', function ($join) use ($companyNo) {
                $join->on('sClass.CLCD3E', '=', 'ph.CLCD3K')
                    ->on('sClass.SCCD3E', '=', 'ph.SCCD3K')
                    ->on('sClass.CLCD3E', '=', 'class.CLCD3D')
                    ->where('sClass.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('MKGPMS0 as mk', function ($join) use ($companyNo) {
                $join->on('mk.MKGP3N', '=', 'ph.MKGP3K')
                    ->where('mk.CompanyNo', '=', $companyNo);
            })
            ->leftJoin(DB::raw("(SELECT
                    ucp.Style,
                    ucp.CompanyNo,
                    STRING_AGG(CONCAT(ucp.UPCN5R,'-',ucp.NCLR5R),'|') AS UCPNXC,
                    STRING_AGG(ucp.NCLR5R,'|') AS UCPNLC
                    from
                    dbo.UPCXRF0 ucp
                    group by ucp.Style , ucp.CompanyNo) as upc
                "), function ($join) use ($companyNo) {
                $join->on('upc.Style', '=', 'ph.Style')
                    ->where('upc.CompanyNo', '=', $companyNo);
            })

            ->leftJoin('ProdFit as pf', function ($join) use ($companyNo) {
                $join->on('pf.Style', '=', 'ph.Style')
                    ->where('pf.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('PRDTMS0 as pd', function ($join) use ($companyNo) {
                $join->on('pd.Style', '=', 'ph.Style')
                    ->where('pd.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('COLRMS0 as cm', function ($join) use ($companyNo) {
                $join->on('cm.Color', '=', 'pd.Color')
                    ->where('cm.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('Product_Tags as pt', function ($join) use ($companyNo) {
                $join->on('pt.ProductId', '=', 'ph.Style')
                    ->where('pt.CompanyNo', '=', $companyNo);
            })
            ->leftJoin('Tags as t', function ($join) use ($companyNo) {
                $join->on('t.TagId', '=', 'pt.TagId')
                    ->where('t.CompanyNo', '=', $companyNo);
            });


        /** if the group is set and greater than 0 */
        if ($groupId != 0 && $groupId != '' && $groupId > 0) {
            $Products = $Products->where("pg.id", $groupId);
        }

        /** show only products of division which to assigned to users  */
        $Products = $Products->whereIn("ph.DivisionNo", $divisions);

        /** Single Product */
        if (isset($filters['productId']) && !empty($filters['productId']) && $filters['productId'] != '') {
            $Products =  $Products->where("ph.Style", "=", $filters['productId']);
        }

        /**  Apply the input search */
        if (isset($filters['Search']) && count($filters['Search']) > 0) {

            $Products = Helpers::addDynamicSearch($Products, $filters['Search']);
        }

        /**
         * Apply the filters if the filters array containt filters
         */

        if (isset($filters['Filter']) && count($filters) > 0 && $workSpace == 0) {

            // merge the color description and color filter and remove the color description
            // from array
            // if (
            //     array_key_exists("colorDescription", $filters['Filter'])
            //     &&
            //     array_key_exists("colorCode", $filters['Filter'])
            // ) {
            //     $colors =  $filters['Filter']['colorDescription']['id'];
            //     array_push($filters['Filter']['colorCode']['id'], $colors);
            //     unset($filters['Filter']['colorDescription']);
            // }

            foreach ($filters['Filter'] as $key => $value) {

                $Products = Helpers::addDynamicWheres($Products, $value, $key);
            }
        }

        // used this condition when products fetch for pdf export and workpspace is product
        if ($workSpace == 1) {

            $iteration = 0;
            foreach ($productArrWithColors as $p) {

                $product = $p["productId"];
                foreach ($p['Colors'] as $c) {
                    if ($iteration == 0) {
                        $Products =  $Products->where(function ($query) use ($product, $c) {
                            $query->where("sa.Style",  $product)
                                ->where("sa.Color", $c);
                        });
                    }
                    if ($iteration != 0) {
                        $Products =  $Products->orWhere(
                            function ($query) use ($product, $c) {
                                $query->where("sa.Style",  $product)
                                    ->where("sa.Color", $c);
                            }
                        );
                    }

                    $iteration++;
                }
            }
        }

        //use this condition if products fetched for Linesheet export

        if ($lineSheetIdForExport > 0) {
            $Products = $Products->where("ls.id", $lineSheetIdForExport);
            $Products = $Products->groupBy("lpg.groupId");
            $Products = $Products->groupBy("lpg.ColorId");
        }

        $Products = $Products->groupBy("ph.Style");

        //group by on StyleAvail table
        if ($workSpace != 0) {
            // $Products = $Products->groupBy("sa.Style");
            $Products = $Products->groupBy("sa.Color");
        }

        if ($groupId != 0 && $groupId != '' && $groupId > 0) {
            $Products = $Products->groupBy("lpg.ColorID");
            $Products = $Products->groupBy("sa.Available");
        }

        //Apply the Sort By filter
        if (isset($filters['SortBy']) && count($filters['SortBy']) > 0) {

            //Display products in asc order if the group id is set
            // if ($groupId != 0 && $groupId != '' && $groupId > 0) {
            //     $Products =  $Products->orderByRaw("max(lpg.DisplayOrder) asc");
            // }

            $filters['SortBy'] = Helpers::sortAssociateArr($filters['SortBy'], 'level', 'asc');
            $Products = Helpers::addDynamicSortBy($Products, $filters['SortBy'], "products");
        }

        /** apply sort by group name for linesheet export */

        if ($lineSheetIdForExport > 0) {
            $Products = $Products->orderByRaw('max(pg.GroupName) asc');
        }

        /** Apply the offset , limit and group by to Product Query */

        if ($skipProducts > 0) {
            $Products = $Products->skip($skipProducts)->take($recordsPerPage);
        } else {
            $Products = $Products->offset($offset)
                ->limit($recordsPerPage);
        }
        $Products = $Products->get();

        return $Products;
    }

    public static function getProductDetail($productId)
    {
        try {
            $companyNo = Auth::user()->CompanyNo;
            $Products =  DB::table("PRHDMS0 as ph")
                ->select(
                    "ph.Style as StyleNumber",
                    DB::raw("max(RTRIM(ph.EXDS3K))as ProductName"),
                    DB::raw("max(RTRIM(ph.SZCD3K)) as SizeCode"),
                    DB::raw("max(ph.RTPR3K) as Price"),
                    DB::raw("max(RTRIM(div.DVNM3C)) as Division"),
                    DB::raw("max(RTRIM(ph.CLCD3K)) as ClassCode"),
                    DB::raw("max(RTRIM(class.CLDS3D)) as Class"),
                    DB::raw("max(RTRIM(ph.SCCD3K)) as SubClassCode"),
                    DB::raw("max(RTRIM(sClass.SCDS3E)) as SubClass"),
                    DB::raw("max(RTRIM(mk.MKDS3N)) as MarketGroup"),
                    DB::raw("max(RTRIM(plm.Season)) as Season"),
                    DB::raw("max(RTRIM(plm.Market)) as Market"),
                    DB::raw("max(RTRIM(plm.FabType)) as FabType"),
                    DB::raw("max(RTRIM(plm.FabricName)) as FabName"),
                    DB::raw("max(RTRIM(plm.Brand)) as Brand"),
                    DB::raw("max(ph.DateCreated) as DateCreated"),
                    DB::raw("max(ph.DateMaintained) as DateMaintained"),
                    DB::raw("string_agg(pd.Color,',') as Colors"),
                    DB::raw("dbo.ProductInfo(max(ph.CompanyNo), ph.Style, 5) as QueryAttributes")
                    // DB::raw("dbo.ProductInfo(max(ph.CompanyNo), ph.Style, 6) as ColorCodeName"),
                    // DB::raw("max(upc.UCPNXC) as UPCXInfo"),
                    // DB::raw("max(pd.Color) as MaxColor")
                );
            $Products =  $Products->leftJoin('DIVNMS0 as div', function ($join) use ($companyNo) {
                $join->on('div.DivisionNo', '=', 'ph.DivisionNo')
                    ->where('div.CompanyNo', '=', $companyNo);
            })
                ->leftJoin('PRCLMS0 as class', function ($join) use ($companyNo) {
                    $join->on('class.CLCD3D', '=', 'ph.CLCD3K')
                        ->where('class.CompanyNo', '=', $companyNo);
                })
                ->leftJoin('PRSCMS0 as sClass', function ($join) use ($companyNo) {
                    $join->on('sClass.CLCD3E', '=', 'ph.CLCD3K')
                        ->on('sClass.SCCD3E', '=', 'ph.SCCD3K')
                        ->on('sClass.CLCD3E', '=', 'class.CLCD3D')
                        ->where('sClass.CompanyNo', '=', $companyNo);
                })
                ->leftJoin('MKGPMS0 as mk', function ($join) use ($companyNo) {
                    $join->on('mk.MKGP3N', '=', 'ph.MKGP3K')
                        ->where('mk.CompanyNo', '=', $companyNo);
                })
                ->leftJoin('ProdPLM as plm', function ($join) use ($companyNo) {
                    $join->on('plm.Style', '=', 'ph.Style')
                        ->where('mk.CompanyNo', '=', $companyNo);
                })
                ->leftJoin('PRDTMS0 as pd', function ($join) use ($companyNo) {
                    $join->on('pd.Style', '=', 'ph.Style')
                        ->where('pd.CompanyNo', '=', $companyNo);
                })
                ->leftJoin('ProdFit as pf', function ($join) use ($companyNo) {
                    $join->on('pf.Style', '=', 'ph.Style')
                        ->where('pf.CompanyNo', '=', $companyNo);
                })->where('ph.Style', $productId)->groupBy("ph.Style")->get();

            $tempQAArr = array();
            $Products[0]->QueryAttributes = array_values(array_unique(explode('|', $Products[0]->QueryAttributes)));
            foreach ($Products[0]->QueryAttributes as $val) {
                $tempVal = explode(':', $val);
                $tempQAArr[$tempVal[0]] =  $tempVal[1];
            }
            $Products[0]->QueryAttributes = $tempQAArr;

            $Products[0]->Colors = array_values(array_unique(explode(',', $Products[0]->Colors)));

            $Products[0]->OrderDetail = DB::table("PreOrderDtl as od")->select(
                "PreOrderNumdtl as OrderNo",
                "Price",
                "OrdTyp as Type",
                DB::raw("RTRIM(oh.CustAcct) as CustomerName"),
                DB::raw("Scale1+Scale2+Scale3+Scale4+Scale5+Scale6+Scale7+Scale8+Scale9+Scale10+Scale11+Scale12 as Ordered")
            )
                ->leftJoin('PreOrderHdr as oh', function ($join) use ($companyNo) {
                    $join->on('oh.PreOrderNum', '=', 'od.PreOrderNumdtl')
                        ->where('oh.CompanyNo', '=', $companyNo);
                })
                ->where('od.Style', $productId)->get();

            $Products[0]->Costing = ProdCost::whereStyle($productId)->select('ProdType', 'FactoryName as Supplier', 'Version as Description', 'Revision as Rev', 'ModifiedOn as RevDate', 'Cost as TotalCost', 'CostSheet', 'FaceCard')->get();
            if (!$Products[0]->Costing) {
                $Products[0]->Costing = [];
            }

            $Orders = PreOrderDtl::whereStyle($productId)->get();
            $StyleAvailable = StyleAvail::whereStyle($productId)->get();
            $ProductAvailable = PRDAVL0::whereStyle($productId)->get();

            $OrdersTotal = strval($Orders->sum(function ($t) {
                $sum = $t->Scale1 + $t->Scale2 + $t->Scale3 + $t->Scale4 + $t->Scale5 + $t->Scale6 + $t->Scale7 + $t->Scale8 + $t->Scale9 + $t->Scale10 + $t->Scale11 + $t->Scale12;
                return $sum;
            }));
            $Scale1 = strval($Orders->sum('Scale1'));
            $Scale2 = strval($Orders->sum('Scale2'));
            $Scale3 = strval($Orders->sum('Scale3'));
            $Scale4 = strval($Orders->sum('Scale4'));
            $Scale5 = strval($Orders->sum('Scale5'));
            $Scale6 = strval($Orders->sum('Scale6'));
            $Scale7 = strval($Orders->sum('Scale7'));
            $Scale8 = strval($Orders->sum('Scale8'));
            $Scale9 = strval($Orders->sum('Scale9'));
            $Scale10 = strval($Orders->sum('Scale10'));
            $Scale11 = strval($Orders->sum('Scale11'));
            $Scale12 = strval($Orders->sum('Scale12'));

            $TotalWIP = strval($StyleAvailable->sum(function ($t) {
                $sum = $t->ewp01 + $t->ewp02 + $t->ewp03 + $t->ewp04 + $t->ewp05 + $t->ewp06 + $t->ewp07 + $t->ewp08 + $t->ewp09 + $t->ewp10 + $t->ewp11 + $t->ewp12;
                return $sum;
            }));
            $WIP1 = strval($StyleAvailable->sum('ewp01'));
            $WIP2 = strval($StyleAvailable->sum('ewp02'));
            $WIP3 = strval($StyleAvailable->sum('ewp03'));
            $WIP4 = strval($StyleAvailable->sum('ewp04'));
            $WIP5 = strval($StyleAvailable->sum('ewp05'));
            $WIP6 = strval($StyleAvailable->sum('ewp06'));
            $WIP7 = strval($StyleAvailable->sum('ewp07'));
            $WIP8 = strval($StyleAvailable->sum('ewp08'));
            $WIP9 = strval($StyleAvailable->sum('ewp09'));
            $WIP10 = strval($StyleAvailable->sum('ewp10'));
            $WIP11 = strval($StyleAvailable->sum('ewp11'));
            $WIP12 = strval($StyleAvailable->sum('ewp12'));

            $OH015V = $ProductAvailable->sum('OH015V');
            $OH025V = $ProductAvailable->sum('OH025V');
            $OH035V = $ProductAvailable->sum('OH035V');
            $OH045V = $ProductAvailable->sum('OH045V');
            $OH055V = $ProductAvailable->sum('OH055V');
            $OH065V = $ProductAvailable->sum('OH065V');
            $OH075V = $ProductAvailable->sum('OH075V');
            $OH085V = $ProductAvailable->sum('OH085V');
            $OH095V = $ProductAvailable->sum('OH095V');
            $OH105V = $ProductAvailable->sum('OH105V');
            $OH115V = $ProductAvailable->sum('OH115V');
            $OH125V = $ProductAvailable->sum('OH125V');

            $UA015V = $ProductAvailable->sum('UA015V');
            $UA025V = $ProductAvailable->sum('UA025V');
            $UA035V = $ProductAvailable->sum('UA035V');
            $UA045V = $ProductAvailable->sum('UA045V');
            $UA055V = $ProductAvailable->sum('UA055V');
            $UA065V = $ProductAvailable->sum('UA065V');
            $UA075V = $ProductAvailable->sum('UA075V');
            $UA085V = $ProductAvailable->sum('UA085V');
            $UA095V = $ProductAvailable->sum('UA095V');
            $UA105V = $ProductAvailable->sum('UA105V');
            $UA115V = $ProductAvailable->sum('UA115V');
            $UA125V = $ProductAvailable->sum('UA125V');

            $NH015V = $ProductAvailable->sum('NH015V');
            $NH025V = $ProductAvailable->sum('NH025V');
            $NH035V = $ProductAvailable->sum('NH035V');
            $NH045V = $ProductAvailable->sum('NH045V');
            $NH055V = $ProductAvailable->sum('NH055V');
            $NH065V = $ProductAvailable->sum('NH065V');
            $NH075V = $ProductAvailable->sum('NH075V');
            $NH085V = $ProductAvailable->sum('NH085V');
            $NH095V = $ProductAvailable->sum('NH095V');
            $NH105V = $ProductAvailable->sum('NH105V');
            $NH115V = $ProductAvailable->sum('NH115V');
            $NH125V = $ProductAvailable->sum('NH125V');

            $WP015V = $ProductAvailable->sum('WP015V');
            $WP025V = $ProductAvailable->sum('WP025V');
            $WP035V = $ProductAvailable->sum('WP035V');
            $WP045V = $ProductAvailable->sum('WP045V');
            $WP055V = $ProductAvailable->sum('WP055V');
            $WP065V = $ProductAvailable->sum('WP065V');
            $WP075V = $ProductAvailable->sum('WP075V');
            $WP085V = $ProductAvailable->sum('WP085V');
            $WP095V = $ProductAvailable->sum('WP095V');
            $WP105V = $ProductAvailable->sum('WP105V');
            $WP115V = $ProductAvailable->sum('WP115V');
            $WP125V = $ProductAvailable->sum('WP125V');

            $PO015V = $ProductAvailable->sum('PO015V');
            $PO025V = $ProductAvailable->sum('PO025V');
            $PO035V = $ProductAvailable->sum('PO035V');
            $PO045V = $ProductAvailable->sum('PO045V');
            $PO055V = $ProductAvailable->sum('PO055V');
            $PO065V = $ProductAvailable->sum('PO065V');
            $PO075V = $ProductAvailable->sum('PO075V');
            $PO085V = $ProductAvailable->sum('PO085V');
            $PO095V = $ProductAvailable->sum('PO095V');
            $PO105V = $ProductAvailable->sum('PO105V');
            $PO115V = $ProductAvailable->sum('PO115V');
            $PO125V = $ProductAvailable->sum('PO125V');


            $FirstQuality1 = strval($OH015V + $UA015V);
            $FirstQuality2 = strval($OH025V + $UA025V);
            $FirstQuality3 = strval($OH035V + $UA035V);
            $FirstQuality4 = strval($OH045V + $UA045V);
            $FirstQuality5 = strval($OH055V + $UA055V);
            $FirstQuality6 = strval($OH065V + $UA065V);
            $FirstQuality7 = strval($OH075V + $UA075V);
            $FirstQuality8 = strval($OH085V + $UA085V);
            $FirstQuality9 = strval($OH095V + $UA095V);
            $FirstQuality10 = strval($OH105V + $UA105V);
            $FirstQuality11 = strval($OH115V + $UA115V);
            $FirstQuality12 = strval($OH125V + $UA125V);

            $TotalFirstQuality = strval($FirstQuality1 + $FirstQuality2 + $FirstQuality3 + $FirstQuality4 +
                $FirstQuality5 + $FirstQuality6 + $FirstQuality7 + $FirstQuality8 +
                $FirstQuality9 + $FirstQuality10 + $FirstQuality11 + $FirstQuality12);

            $Available1 = strval($StyleAvailable->sum('AV015V'));
            $Available2 = strval($StyleAvailable->sum('AV025V'));
            $Available3 = strval($StyleAvailable->sum('AV035V'));
            $Available4 = strval($StyleAvailable->sum('AV045V'));
            $Available5 = strval($StyleAvailable->sum('AV055V'));
            $Available6 = strval($StyleAvailable->sum('AV065V'));
            $Available7 = strval($StyleAvailable->sum('AV075V'));
            $Available8 = strval($StyleAvailable->sum('AV085V'));
            $Available9 = strval($StyleAvailable->sum('AV095V'));
            $Available10 = strval($StyleAvailable->sum('AV105V'));
            $Available11 = strval($StyleAvailable->sum('AV115V'));
            $Available12 = strval($StyleAvailable->sum('AV125V'));

            $TotalAvailable = strval($Available1 + $Available2 + $Available3 + $Available4 +
                $Available5 + $Available6 + $Available7 + $Available8 +
                $Available9 + $Available10 + $Available11 + $Available12);

            $OrderOnHold1 = strval($ProductAvailable->sum('HL015V'));
            $OrderOnHold2 = strval($ProductAvailable->sum('HL025V'));
            $OrderOnHold3 = strval($ProductAvailable->sum('HL035V'));
            $OrderOnHold4 = strval($ProductAvailable->sum('HL045V'));
            $OrderOnHold5 = strval($ProductAvailable->sum('HL055V'));
            $OrderOnHold6 = strval($ProductAvailable->sum('HL065V'));
            $OrderOnHold7 = strval($ProductAvailable->sum('HL075V'));
            $OrderOnHold8 = strval($ProductAvailable->sum('HL085V'));
            $OrderOnHold9 = strval($ProductAvailable->sum('HL095V'));
            $OrderOnHold10 = strval($ProductAvailable->sum('HL105V'));
            $OrderOnHold11 = strval($ProductAvailable->sum('HL115V'));
            $OrderOnHold12 = strval($ProductAvailable->sum('HL125V'));

            $TotalOrderOnHold = strval($OrderOnHold1 + $OrderOnHold2 + $OrderOnHold3 + $OrderOnHold4 +
                $OrderOnHold5 + $OrderOnHold6 + $OrderOnHold7 + $OrderOnHold8 +
                $OrderOnHold9 + $OrderOnHold10 + $OrderOnHold11 + $OrderOnHold12);

            $ExtraFirstQuality1 = strval($OH015V - $NH015V - $OrderOnHold1);
            $ExtraFirstQuality2 = strval($OH025V - $NH025V - $OrderOnHold2);
            $ExtraFirstQuality3 = strval($OH035V - $NH035V - $OrderOnHold3);
            $ExtraFirstQuality4 = strval($OH045V - $NH045V - $OrderOnHold4);
            $ExtraFirstQuality5 = strval($OH055V - $NH055V - $OrderOnHold5);
            $ExtraFirstQuality6 = strval($OH065V - $NH065V - $OrderOnHold6);
            $ExtraFirstQuality7 = strval($OH075V - $NH075V - $OrderOnHold7);
            $ExtraFirstQuality8 = strval($OH085V - $NH085V - $OrderOnHold8);
            $ExtraFirstQuality9 = strval($OH095V - $NH095V - $OrderOnHold9);
            $ExtraFirstQuality10 = strval($OH105V - $NH105V - $OrderOnHold10);
            $ExtraFirstQuality11 = strval($OH115V - $NH115V - $OrderOnHold11);
            $ExtraFirstQuality12 = strval($OH125V - $NH125V - $OrderOnHold12);

            $TotalExtraFirstQuality = strval($ExtraFirstQuality1 + $ExtraFirstQuality2 + $ExtraFirstQuality3 + $ExtraFirstQuality4 +
                $ExtraFirstQuality5 + $ExtraFirstQuality6 + $ExtraFirstQuality7 + $ExtraFirstQuality8 +
                $ExtraFirstQuality9 + $ExtraFirstQuality10 + $ExtraFirstQuality11 + $ExtraFirstQuality12);

            $ExtraWIP1 = strval($PO015V + $WP015V);
            $ExtraWIP2 = strval($PO025V + $WP025V);
            $ExtraWIP3 = strval($PO035V + $WP035V);
            $ExtraWIP4 = strval($PO045V + $WP045V);
            $ExtraWIP5 = strval($PO055V + $WP055V);
            $ExtraWIP6 = strval($PO065V + $WP065V);
            $ExtraWIP7 = strval($PO075V + $WP075V);
            $ExtraWIP8 = strval($PO085V + $WP085V);
            $ExtraWIP9 = strval($PO095V + $WP095V);
            $ExtraWIP10 = strval($PO105V + $WP105V);
            $ExtraWIP11 = strval($PO115V + $WP115V);
            $ExtraWIP12 = strval($PO125V + $WP125V);

            $TotalExtraWIP = strval($ExtraWIP1 + $ExtraWIP2 + $ExtraWIP3 + $ExtraWIP4 +
                $ExtraWIP5 + $ExtraWIP6 + $ExtraWIP7 + $ExtraWIP8 +
                $ExtraWIP9 + $ExtraWIP10 + $ExtraWIP11 + $ExtraWIP12);

            $OpenToSell1 = strval($OH015V - $NH015V - $OrderOnHold1 + $PO015V + $WP015V);
            $OpenToSell2 = strval($OH025V - $NH025V - $OrderOnHold2 + $PO025V + $WP025V);
            $OpenToSell3 = strval($OH035V - $NH035V - $OrderOnHold3 + $PO035V + $WP035V);
            $OpenToSell4 = strval($OH045V - $NH045V - $OrderOnHold4 + $PO045V + $WP045V);
            $OpenToSell5 = strval($OH055V - $NH055V - $OrderOnHold5 + $PO055V + $WP055V);
            $OpenToSell6 = strval($OH065V - $NH065V - $OrderOnHold6 + $PO065V + $WP065V);
            $OpenToSell7 = strval($OH075V - $NH075V - $OrderOnHold7 + $PO075V + $WP075V);
            $OpenToSell8 = strval($OH085V - $NH085V - $OrderOnHold8 + $PO085V + $WP085V);
            $OpenToSell9 = strval($OH095V - $NH095V - $OrderOnHold9 + $PO095V + $WP095V);
            $OpenToSell10 = strval($OH105V - $NH105V - $OrderOnHold10 + $PO105V + $WP105V);
            $OpenToSell11 = strval($OH115V - $NH115V - $OrderOnHold11 + $PO115V + $WP115V);
            $OpenToSell12 = strval($OH125V - $NH125V - $OrderOnHold12 + $PO125V + $WP125V);

            $TotalOpenToSell = strval($OpenToSell1 + $OpenToSell2 + $OpenToSell3 + $OpenToSell4 +
                $OpenToSell5 + $OpenToSell6 + $OpenToSell7 + $OpenToSell8 +
                $OpenToSell9 + $OpenToSell10 + $OpenToSell11 + $OpenToSell12);

            $Products[0]->Inventory = [
                [
                    'key' => '1st Quality',
                    'Total' => $TotalFirstQuality,
                    'Value1' => $FirstQuality1, 'Value2' => $FirstQuality2, 'Value3' => $FirstQuality3, 'Value4' => $FirstQuality4,
                    'Value5' => $FirstQuality5, 'Value6' => $FirstQuality6, 'Value7' => $FirstQuality7, 'Value8' => $FirstQuality8,
                    'Value9' => $FirstQuality9, 'Value10' => $FirstQuality10, 'Value11' => $FirstQuality11, 'Value12' => $FirstQuality12
                ],
                [
                    'key' => 'WIP',
                    'Total' => $TotalWIP,
                    'Value1' => $WIP1, 'Value2' => $WIP2, 'Value3' => $WIP3, 'Value4' => $WIP4,
                    'Value5' => $WIP5, 'Value6' => $WIP6, 'Value7' => $WIP7, 'Value8' => $WIP8,
                    'Value9' => $WIP9, 'Value10' => $WIP10, 'Value11' => $WIP11, 'Value12' => $WIP12
                ],
                [
                    'key' => 'Orders',
                    'Total' => $OrdersTotal,
                    'Value1' => $Scale1, 'Value2' => $Scale2, 'Value3' => $Scale3, 'Value4' => $Scale4,
                    'Value5' => $Scale5, 'Value6' => $Scale6, 'Value7' => $Scale7, 'Value8' => $Scale8,
                    'Value9' => $Scale9, 'Value10' => $Scale10, 'Value11' => $Scale11, 'Value12' => $Scale12
                ],
                [
                    'key' => 'Ord-On-Hold',
                    'Total' => $TotalOrderOnHold,
                    'Value1' => $OrderOnHold1, 'Value2' => $OrderOnHold2, 'Value3' => $OrderOnHold3, 'Value4' => $OrderOnHold4,
                    'Value5' => $OrderOnHold5, 'Value6' => $OrderOnHold6, 'Value7' => $OrderOnHold7, 'Value8' => $OrderOnHold8,
                    'Value9' => $OrderOnHold9, 'Value10' => $OrderOnHold10, 'Value11' => $OrderOnHold11, 'Value12' => $OrderOnHold12
                ],
                [
                    'key' => 'Available',
                    'Total' => $TotalAvailable,
                    'Value1' => $Available1, 'Value2' => $Available2, 'Value3' => $Available3, 'Value4' => $Available4,
                    'Value5' => $Available5, 'Value6' => $Available6, 'Value7' => $Available7, 'Value8' => $Available8,
                    'Value9' => $Available9, 'Value10' => $Available10, 'Value11' => $Available11, 'Value12' => $Available12
                ],
                [
                    'key' => 'Open to Sell',
                    'Total' => $TotalOpenToSell < 0 ? "0" : $TotalOpenToSell,
                    'Value1' => $OpenToSell1 < 0 ? "0" : $OpenToSell1, 'Value2' => $OpenToSell2 < 0 ? "0" : $OpenToSell2, 'Value3' => $OpenToSell3 < 0 ? "0" : $OpenToSell3, 'Value4' => $OpenToSell4 < 0 ? "0" : $OpenToSell4,
                    'Value5' => $OpenToSell5 < 0 ? "0" : $OpenToSell5, 'Value6' => $OpenToSell6 < 0 ? "0" : $OpenToSell6, 'Value7' => $OpenToSell7 < 0 ? "0" : $OpenToSell7, 'Value8' => $OpenToSell8 < 0 ? "0" : $OpenToSell8,
                    'Value9' => $OpenToSell9 < 0 ? "0" : $OpenToSell9, 'Value10' => $OpenToSell10 < 0 ? "0" : $OpenToSell10, 'Value11' => $OpenToSell11 < 0 ? "0" : $OpenToSell11, 'Value12' => $OpenToSell12 < 0 ? "0" : $OpenToSell12
                ],
                [
                    'key' => 'Extra 1st',
                    'Total' => $TotalExtraFirstQuality < 0 ? "0" : $TotalExtraFirstQuality,
                    'Value1' => $ExtraFirstQuality1 < 0 ? "0" : $ExtraFirstQuality1, 'Value2' => $ExtraFirstQuality2 < 0 ? "0" : $ExtraFirstQuality2, 'Value3' => $ExtraFirstQuality3 < 0 ? "0" : $ExtraFirstQuality3, 'Value4' => $ExtraFirstQuality4 < 0 ? "0" : $ExtraFirstQuality4,
                    'Value5' => $ExtraFirstQuality5 < 0 ? "0" : $ExtraFirstQuality5, 'Value6' => $ExtraFirstQuality6 < 0 ? "0" : $ExtraFirstQuality6, 'Value7' => $ExtraFirstQuality7 < 0 ? "0" : $ExtraFirstQuality7, 'Value8' => $ExtraFirstQuality8 < 0 ? "0" : $ExtraFirstQuality8,
                    'Value9' => $ExtraFirstQuality9 < 0 ? "0" : $ExtraFirstQuality9, 'Value10' => $ExtraFirstQuality10 < 0 ? "0" : $ExtraFirstQuality10, 'Value11' => $ExtraFirstQuality11 < 0 ? "0" : $ExtraFirstQuality11, 'Value12' => $ExtraFirstQuality12 < 0 ? "0" : $ExtraFirstQuality12
                ],
                [
                    'key' => 'Extra WIP',
                    'Total' => $TotalExtraWIP < 0 ? "0" : $TotalExtraWIP,
                    'Value1' => $ExtraWIP1 < 0 ? "0" : $ExtraWIP1, 'Value2' => $ExtraWIP2 < 0 ? "0" : $ExtraWIP2, 'Value3' => $ExtraWIP3 < 0 ? "0" : $ExtraWIP3, 'Value4' => $ExtraWIP4 < 0 ? "0" : $ExtraWIP4,
                    'Value5' => $ExtraWIP5 < 0 ? "0" : $ExtraWIP5, 'Value6' => $ExtraWIP6 < 0 ? "0" : $ExtraWIP6, 'Value7' => $ExtraWIP7 < 0 ? "0" : $ExtraWIP7, 'Value8' => $ExtraWIP8 < 0 ? "0" : $ExtraWIP8,
                    'Value9' => $ExtraWIP9 < 0 ? "0" : $ExtraWIP9, 'Value10' => $ExtraWIP10 < 0 ? "0" : $ExtraWIP10, 'Value11' => $ExtraWIP11 < 0 ? "0" : $ExtraWIP11, 'Value12' => $ExtraWIP12 < 0 ? "0" : $ExtraWIP12
                ]
            ];
            $Products[0]->InventoryColumn = SZSCMS0::find($Products[0]->SizeCode, ['SZ013G', 'SZ023G', 'SZ033G', 'SZ043G', 'SZ053G', 'SZ063G', 'SZ073G', 'SZ083G', 'SZ093G', 'SZ103G', 'SZ113G', 'SZ123G']);
            $Products[0]->InventoryColumn = Helpers::removeEmptyKey(array_map('trim', $Products[0]->InventoryColumn->toArray()));
            $Products[0]->InventoryColumn = array('Total' => 'Total') + $Products[0]->InventoryColumn;

            return $Products;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function getProductsforCustomSort($sortByArr, $companyNo, $groupId)
    {
        $Products =  DB::table("LineSheetGroupProducts as lgp")
            ->select(
                DB::raw("max(lgp.id) as id"),
                DB::raw("max(lgp.ProductId) AS productId"),
                DB::raw("max(lgp.ColorId) as ColorID"),
                DB::raw("max(pd.Color) as PDColor"),
                DB::raw("max(ph.EXDS3K ) as ProductName"),
                DB::raw("max(cm.CRDS3J) as ColorName"),
                DB::raw("max(ph.PRDS3K) as ProductDescription"),
                DB::raw("max(sa.Available) as Total"),
                DB::raw("max(ph.DivisionNo) as DivisionNo"),
                DB::raw("max(cs.id) as customSortId")
            )
            ->join("PRHDMS0  as ph", function ($join) use ($companyNo) {
                $join->on('ph.Style', '=', 'lgp.ProductId')
                    ->where('lgp.CompanyNo', '=', $companyNo);
            })
            ->leftJoin("CustomSort  as cs", function ($join) use ($companyNo) {
                $join->on('cs.LSGPId', '=', 'lgp.id')
                    ->where('cs.CompanyNo', '=', $companyNo);
            })
            ->leftJoin("PRDTMS0  as pd", function ($join) use ($companyNo) {
                $join->on('pd.Style', '=', 'lgp.ProductId')
                    ->on('pd.Color', '=', 'lgp.ColorId')
                    ->where('pd.CompanyNo', '=', $companyNo);
            })
            ->leftJoin("COLRMS0  as cm", function ($join) use ($companyNo) {
                $join->on('cm.Color', '=', 'pd.Color')
                    ->where('cm.CompanyNo', '=', $companyNo);
            })
            ->leftJoin("StyleAvail  as sa", function ($join) use ($companyNo) {
                $join->on('sa.Style', '=',  'ph.Style')
                    ->on('sa.Color', '=',  'lgp.ColorId')
                    ->where('sa.CompanyNo', '=', $companyNo);
            })
            ->where("lgp.GroupId", $groupId)
            ->groupBy(['lgp.ProductId', 'lgp.ColorId', 'sa.Available']);

        $Products = Helpers::addDynamicSortBy($Products, $sortByArr, "products");
        return $Products = $Products->get();
    }

    //get ProductPrice

    public static function getPrice($arr)
    {
        try {
            $productdetail =  Product::select("Style", "RTPR3K as price")
                ->whereIn("Style", $arr)
                ->get();
            return $productdetail;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    //attributes
    public static function attributes()
    {
        return array(
            array("ID" => "Price", "Value" => "Price"),
            array("ID" => "WIP", "Value" => "WIP"),
            array("ID" => "FirstQuality", "Value" => "First Quality"),
            array("ID" => "Total", "Value" => "Quantity"),
            array("ID" => "SizeCode", "Value" => "Size"),
            array("ID" => "Division", "Value" => "Division"),
            array("ID" => "Class", "Value" => "Class"),
            array("ID" => "SubClass", "Value" => "Sub Class"),
            array("ID" => "MarketGroup", "Value" => "Market Group"),
            array("ID" => "Season", "Value" => "Season"),
            array("ID" => "Market", "Value" => "Market"),
            array("ID" => "FabType", "Value" => "Fabric Type"),
            array("ID" => "FabName", "Value" => "Fabric Name"),
            array("ID" => "Brand", "Value" => "Brand"),
            array("ID" => "UPCXInfo", "Value" => "UPCX")
        );
    }
}
