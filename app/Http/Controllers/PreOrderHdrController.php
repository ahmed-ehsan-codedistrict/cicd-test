<?php

namespace App\Http\Controllers;

use App\Models\PreOrderDtl;
use App\Models\PreOrderHdr;
use App\Utilities\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreOrderHdrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PreOrderHdr  $preOrderHdr
     * @return \Illuminate\Http\Response
     */
    public function show(PreOrderHdr $preOrderHdr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PreOrderHdr  $preOrderHdr
     * @return \Illuminate\Http\Response
     */
    public function edit(PreOrderHdr $preOrderHdr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PreOrderHdr  $preOrderHdr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PreOrderHdr $preOrderHdr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PreOrderHdr  $preOrderHdr
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreOrderHdr $preOrderHdr)
    {
        //
    }

    public function getFields(Request $request)
    {
        //validating inputs
        $request->validate([
            'OrderNo' => 'required',
        ]);
        try{


              $preOrder = PreOrderHdr::getOrderFields($request->OrderNo);
             if(count($preOrder) >0){
                $preOrder = PreOrderHdr::getOrderFields($request->OrderNo)[0];
                $preOrder->tags =explode(",",  $preOrder->tags);
             }

            return response()->json($preOrder);
        }
        catch(\Error $e)
        {
            return response()->json(['Message' => "Something went wrong", 'Error' => $e->getMessage()], 400);
        }
    }

    public function getOrderDetail(Request $request)
    {
            //validating inputs
            $request->validate([
                'OrderNo' => 'required|integer',
                'pageNumber' => 'required|integer',
                'recordPerPage' => 'required|integer',
            ]);
        try{
            return response()->json(PreOrderHdr::getOrderDetails($request->OrderNo, $request->pageNumber, $request->recordPerPage));
        } catch(\Error $e)
        {
            return response()->json(['Message' => "Something went wrong", 'Error' => $e->getMessage()], 400);
        }
    }

    public function editOrderDetailProduct(Request $request)
    {
            //validating inputs
            $request->validate([
                'OrderNo' => 'integer|required|exists:PreOrderHdr,PreOrderNum',
                'LineNumber' => 'integer|required|exists:PreOrderDtl,PreOrderLinenum',
                'Reorder' => 'required|in:Y,N',
                'CadBoard' => 'required|in:Y,N',
                'Status' => 'required|in:Existing,Reference',
                'Style' => 'string|required',
                'Color' => 'string|required|exists:COLRMS0,Color',
                'Fabric' => 'string|required|exists:ProdPLM,FabricContent',
                'OrderType' => 'required|in:C,P',
                'BuyType' => 'required|exists:PreOrderDtl,BuyType',
                'ProdType' => 'required|in:D,I',
                'SizeRange' => 'required|in:4-6X,7-16,8.5-18.5,1-15,23-32',
                'AdSample' => 'required|in:N,Y',
                'LineSample' => 'required|in:N,Y',
                'TOPSample' => 'required|in:N,Y',
                'AdQty' => 'required|integer',
                'LineQty' => 'required|integer',
                'TOPQty' => 'required|integer',
                'AdSampleDate' => 'nullable|date|date_format:d-m-Y',
                'TOPDate' => 'nullable|date|date_format:d-m-Y',
                'LineSampleDate' => 'nullable|date|date_format:d-m-Y',
                'Scale1' => 'integer|required',
                'Scale2' => 'integer|required',
                'Scale3' => 'integer|required',
                'Scale4' => 'integer|required',
                'Scale5' => 'integer|required',
                'Scale6' => 'integer|required',
                'Scale7' => 'integer|required',
                'Scale8' => 'integer|required',
                'Scale9' => 'integer|required',
                'Scale10' => 'integer|required',
                'Scale11' => 'integer|required',
                'Scale12' => 'integer|required',
                'Qty' => 'integer|required',
                'NoSizeRatio' => 'required|in:Y,N',
                'MarginDeptQty' => 'integer|required',
                'MarginDeptPrice' => 'integer|required',
                'MarginDeptDscQty' => 'integer|required',
                'MarginDeptDscPrice' => 'integer|required',
                'MarginSpecialtyQty' => 'integer|required',
                'MarginSpecialtyPrice' => 'integer|required',
                'Notes' => 'string|required|max:400',
                'Description' => 'string|required',
                'Ad01' => 'required|integer',
                'Ad02' => 'required|integer',
                'Ad03' => 'required|integer',
                'Ad04' => 'required|integer',
                'Ad05' => 'required|integer',
                'Ad06' => 'required|integer',
                'Ad07' => 'required|integer',
                'Ad08' => 'required|integer',
                'Ad09' => 'required|integer',
                'Ad10' => 'required|integer',
                'Ad11' => 'required|integer',
                'Ad12' => 'required|integer',
                'Line01' => 'required|integer',
                'Line02' => 'required|integer',
                'Line03' => 'required|integer',
                'Line04' => 'required|integer',
                'Line05' => 'required|integer',
                'Line06' => 'required|integer',
                'Line07' => 'required|integer',
                'Line08' => 'required|integer',
                'Line09' => 'required|integer',
                'Line10' => 'required|integer',
                'Line11' => 'required|integer',
                'Line12' => 'required|integer',
                'TOP01' => 'required|integer',
                'TOP02' => 'required|integer',
                'TOP03' => 'required|integer',
                'TOP04' => 'required|integer',
                'TOP05' => 'required|integer',
                'TOP06' => 'required|integer',
                'TOP07' => 'required|integer',
                'TOP08' => 'required|integer',
                'TOP09' => 'required|integer',
                'TOP10' => 'required|integer',
                'TOP11' => 'required|integer',
                'TOP12' => 'required|integer',
            ]);
        try{
            $p = $request->all();
            $Instructions = str_split($p['Notes'], 100);
            $orders = [
                'PreOrderNumdtl'=>$p['OrderNo'],
                'PreOrderLinenum'=>$p['LineNumber'],
                'Description'=>$p['Description'],
                'ReOrder'=>$p['Reorder'],
                'CadReq'=>$p['CadBoard'],
                'OrderType'=>$p['OrderType'],
                'ProdType'=>$p['ProdType'],
                'BuyType'=>$p['BuyType'],
                'Scale2'=>$p['Scale2'],
                'Scale3'=>$p['Scale3'],
                'Scale4'=>$p['Scale4'],
                'Scale1'=>$p['Scale1'],
                'Scale5'=>$p['Scale5'],
                'Scale6'=>$p['Scale6'],
                'Scale7'=>$p['Scale7'],
                'Scale8'=>$p['Scale8'],
                'Scale9'=>$p['Scale9'],
                'Scale10'=>$p['Scale10'],
                'Scale11'=>$p['Scale11'],
                'Scale12'=>$p['Scale12'],
                'MarginDeptQty'=>$p['MarginDeptQty'],
                'MarginDeptPrice'=>$p['MarginDeptPrice'],
                'MarginDeptDscQty'=>$p['MarginDeptDscQty'],
                'MarginDeptDscPrice'=>$p['MarginDeptDscPrice'],
                'MarginSpecialtyQty'=>$p['MarginSpecialtyQty'],
                'MarginSpecialtyPrice'=>$p['MarginSpecialtyPrice'],
                'DateMaintained'=>date("Y-m-d"),
                'AdSample'=>$p['AdSample'],
                'LineSample'=>$p['LineSample'],
                'TOPSample'=>$p['TOPSample'],
                'AdQty'=>$p['AdQty'],
                'TOPQty'=>$p['TOPQty'],
                'LineQty'=>$p['LineQty'],
                'AdSampleDate'=>isset($request['AdSampleDate']) ? Helpers::changeDateFormat($p['AdSampleDate'],'Y-m-d') : null,
                'LineSampleDate'=>isset($request['LineSampleDate']) ? Helpers::changeDateFormat($p['LineSampleDate'],'Y-m-d') : null,
                'TOPDate'=>isset($request['TOPDate']) ? Helpers::changeDateFormat($p['TOPDate'],'Y-m-d') : null,
                'Ad01'=>$p['Ad01'],
                'Ad02'=>$p['Ad02'],
                'Ad03'=>$p['Ad03'],
                'Ad04'=>$p['Ad04'],
                'Ad05'=>$p['Ad05'],
                'Ad06'=>$p['Ad06'],
                'Ad07'=>$p['Ad07'],
                'Ad08'=>$p['Ad08'],
                'Ad09'=>$p['Ad09'],
                'Ad10'=>$p['Ad10'],
                'Ad11'=>$p['Ad11'],
                'Ad12'=>$p['Ad12'],
                'Line01'=>$p['Line01'],
                'Line02'=>$p['Line02'],
                'Line03'=>$p['Line03'],
                'Line04'=>$p['Line04'],
                'Line05'=>$p['Line05'],
                'Line06'=>$p['Line06'],
                'Line07'=>$p['Line07'],
                'Line08'=>$p['Line08'],
                'Line09'=>$p['Line09'],
                'Line10'=>$p['Line10'],
                'Line11'=>$p['Line11'],
                'Line12'=>$p['Line12'],
                'TOP01'=>$p['TOP01'],
                'TOP02'=>$p['TOP02'],
                'TOP03'=>$p['TOP03'],
                'TOP04'=>$p['TOP04'],
                'TOP05'=>$p['TOP05'],
                'TOP06'=>$p['TOP06'],
                'TOP07'=>$p['TOP07'],
                'TOP08'=>$p['TOP08'],
                'TOP09'=>$p['TOP09'],
                'TOP10'=>$p['TOP10'],
                'TOP11'=>$p['TOP11'],
                'TOP12'=>$p['TOP12']
            ];
            $p['Fabric'] = str_replace(' ','',$p['Fabric']);
            if($p['Status'])
            {
                if($p['Status'] == 'Existing')
                {
                    $orders['Color'] = $p['Color'];
                    $orders['Style'] = $p['Style'];
                    $orders['Fabric']= $p['Fabric'];
                    $orders['Instructions1'] = isset($Instructions[0]) ? $Instructions[0] : null;
                    $orders['Instructions2'] = isset($Instructions[1]) ? $Instructions[1] : null;
                    $orders['Instructions3'] = isset($Instructions[2]) ? $Instructions[2] : null;
                    $orders['Instructions4'] = isset($Instructions[3]) ? $Instructions[3] : null;
                }
                else if($p['Status'] == 'Reference')
                {
                    $orders['ReferenceColor'] = $p['Color'];
                    $orders['ReferenceStyle'] = $p['Style'];
                    $orders['ReferenceFabric'] = $p['Fabric'];
                    $orders['RefStyleInstructions1'] = isset($Instructions[0]) ? $Instructions[0] : null;
                    $orders['RefStyleInstructions2'] = isset($Instructions[1]) ? $Instructions[1] : null;
                    $orders['RefStyleInstructions3'] = isset($Instructions[2]) ? $Instructions[2] : null;
                    $orders['RefStyleInstructions4'] = isset($Instructions[3]) ? $Instructions[3] : null;
                }
            }
            $orderStatus = PreOrderHdr::editOrderDetailProduct($orders);
            if($orderStatus['error']!=null)
            {
                return response()->json(['message'=>$orderStatus['message'],'success'=>$orderStatus['success'],'error'=>$orderStatus['error']],$orderStatus['code']);
            }
            return response()->json(['message'=>$orderStatus['message'],'success'=>$orderStatus['success'],'error'=>$orderStatus['error']],$orderStatus['code']);
        } catch(\Error $e)
        {
            return response()->json(['Message' => "Something went wrong", 'Error' => $e->getMessage()], 400);
        }
    }

    public function editOrderDetailProducts(Request $request)
    {
        //validating inputs
        $request->validate([
            '*.OrderNo' => 'integer|required|exists:PreOrderHdr,PreOrderNum',
            '*.LineNumber' => 'integer|required|exists:PreOrderDtl,PreOrderLinenum',
            '*.Reorder' => 'required|in:Y,N',
            '*.CadBoard' => 'required|in:Y,N',
            '*.Status' => 'required|in:Existing,Reference',
            '*.Style' => 'string|required',
            '*.Color' => 'string|required|exists:COLRMS0,Color',
            '*.Fabric' => 'string|required|exists:ProdPLM,FabricContent',
            '*.OrderType' => 'required|in:C,P',
            '*.BuyType' => 'required|exists:PreOrderDtl,BuyType',
            '*.ProdType' => 'required|in:D,I',
            '*.SizeRange' => 'required|in:4-6X,7-16,8.5-18.5,1-15,23-32',
            '*.Scale1' => 'integer|required',
            '*.Scale2' => 'integer|required',
            '*.Scale3' => 'integer|required',
            '*.Scale4' => 'integer|required',
            '*.Scale5' => 'integer|required',
            '*.Scale6' => 'integer|required',
            '*.Scale7' => 'integer|required',
            '*.Scale8' => 'integer|required',
            '*.Scale9' => 'integer|required',
            '*.Scale10' => 'integer|required',
            '*.Scale11' => 'integer|required',
            '*.Scale12' => 'integer|required',
            '*.Qty' => 'integer|required',
            '*.NoSizeRatio' => 'required|in:Y,N',
            '*.AdSample' => 'required|in:Y,N',
            '*.TOPSample' => 'required|in:Y,N',
            '*.LineSample' => 'required|in:Y,N',
            '*.AdQty' => 'required|integer',
            '*.LineQty' => 'required|integer',
            '*.TOPQty' => 'required|integer',
            '*.AdSampleDate' => 'nullable|date|date_format:d-m-Y',
            '*.TOPDate' => 'nullable|date|date_format:d-m-Y',
            '*.LineSampleDate' => 'nullable|date|date_format:d-m-Y',
            '*.MarginDeptQty' => 'integer|required',
            '*.MarginDeptPrice' => 'integer|required',
            '*.MarginDeptDscQty' => 'integer|required',
            '*.MarginDeptDscPrice' => 'integer|required',
            '*.MarginSpecialtyQty' => 'integer|required',
            '*.MarginSpecialtyPrice' => 'integer|required',
            '*.Notes' => 'string|required|max:400',
            '*.Description' => 'string|required',
            '*.Ad01' => 'required|integer',
            '*.Ad02' => 'required|integer',
            '*.Ad03' => 'required|integer',
            '*.Ad04' => 'required|integer',
            '*.Ad05' => 'required|integer',
            '*.Ad06' => 'required|integer',
            '*.Ad07' => 'required|integer',
            '*.Ad08' => 'required|integer',
            '*.Ad09' => 'required|integer',
            '*.Ad10' => 'required|integer',
            '*.Ad11' => 'required|integer',
            '*.Ad12' => 'required|integer',
            '*.Line01' => 'required|integer',
            '*.Line02' => 'required|integer',
            '*.Line03' => 'required|integer',
            '*.Line04' => 'required|integer',
            '*.Line05' => 'required|integer',
            '*.Line06' => 'required|integer',
            '*.Line07' => 'required|integer',
            '*.Line08' => 'required|integer',
            '*.Line09' => 'required|integer',
            '*.Line10' => 'required|integer',
            '*.Line11' => 'required|integer',
            '*.Line12' => 'required|integer',
            '*.TOP01' => 'required|integer',
            '*.TOP02' => 'required|integer',
            '*.TOP03' => 'required|integer',
            '*.TOP04' => 'required|integer',
            '*.TOP05' => 'required|integer',
            '*.TOP06' => 'required|integer',
            '*.TOP07' => 'required|integer',
            '*.TOP08' => 'required|integer',
            '*.TOP09' => 'required|integer',
            '*.TOP10' => 'required|integer',
            '*.TOP11' => 'required|integer',
            '*.TOP12' => 'required|integer'
        ]);
        $orders = array();
        $products = $request->all();
        foreach($products as $key => $p)
        {
            $Instructions = str_split($p['Notes'], 100);
            $orders[] = [
                'PreOrderNumdtl'=>$p['OrderNo'],
                'PreOrderLinenum'=>$p['LineNumber'],
                'Description'=>$p['Description'],
                'ReOrder'=>$p['Reorder'],
                'CadReq'=>$p['CadBoard'],
                'OrderType'=>$p['OrderType'],
                'ProdType'=>$p['ProdType'],
                'BuyType'=>$p['BuyType'],
                'Scale2'=>$p['Scale2'],
                'Scale3'=>$p['Scale3'],
                'Scale4'=>$p['Scale4'],
                'Scale1'=>$p['Scale1'],
                'Scale5'=>$p['Scale5'],
                'Scale6'=>$p['Scale6'],
                'Scale7'=>$p['Scale7'],
                'Scale8'=>$p['Scale8'],
                'Scale9'=>$p['Scale9'],
                'Scale10'=>$p['Scale10'],
                'Scale11'=>$p['Scale11'],
                'Scale12'=>$p['Scale12'],
                'MarginDeptQty'=>$p['MarginDeptQty'],
                'MarginDeptPrice'=>$p['MarginDeptPrice'],
                'MarginDeptDscQty'=>$p['MarginDeptDscQty'],
                'MarginDeptDscPrice'=>$p['MarginDeptDscPrice'],
                'MarginSpecialtyQty'=>$p['MarginSpecialtyQty'],
                'MarginSpecialtyPrice'=>$p['MarginSpecialtyPrice'],
                'DateMaintained'=>date("Y-m-d"),
                'AdSample'=>$p['AdSample'],
                'LineSample'=>$p['LineSample'],
                'TOPSample'=>$p['TOPSample'],
                'AdQty'=>$p['AdQty'],
                'TOPQty'=>$p['TOPQty'],
                'LineQty'=>$p['LineQty'],
                'AdSampleDate'=>isset($request[$key]['AdSampleDate']) ? Helpers::changeDateFormat($p['AdSampleDate'],'Y-m-d') : null,
                'LineSampleDate'=>isset($request[$key]['LineSampleDate']) ? Helpers::changeDateFormat($p['LineSampleDate'],'Y-m-d') : null,
                'TOPDate'=>isset($request[$key]['TOPDate']) ? Helpers::changeDateFormat($p['TOPDate'],'Y-m-d') : null,
                'Ad01'=>$p['Ad01'],
                'Ad02'=>$p['Ad02'],
                'Ad03'=>$p['Ad03'],
                'Ad04'=>$p['Ad04'],
                'Ad05'=>$p['Ad05'],
                'Ad06'=>$p['Ad06'],
                'Ad07'=>$p['Ad07'],
                'Ad08'=>$p['Ad08'],
                'Ad09'=>$p['Ad09'],
                'Ad10'=>$p['Ad10'],
                'Ad11'=>$p['Ad11'],
                'Ad12'=>$p['Ad12'],
                'Line01'=>$p['Line01'],
                'Line02'=>$p['Line02'],
                'Line03'=>$p['Line03'],
                'Line04'=>$p['Line04'],
                'Line05'=>$p['Line05'],
                'Line06'=>$p['Line06'],
                'Line07'=>$p['Line07'],
                'Line08'=>$p['Line08'],
                'Line09'=>$p['Line09'],
                'Line10'=>$p['Line10'],
                'Line11'=>$p['Line11'],
                'Line12'=>$p['Line12'],
                'TOP01'=>$p['TOP01'],
                'TOP02'=>$p['TOP02'],
                'TOP03'=>$p['TOP03'],
                'TOP04'=>$p['TOP04'],
                'TOP05'=>$p['TOP05'],
                'TOP06'=>$p['TOP06'],
                'TOP07'=>$p['TOP07'],
                'TOP08'=>$p['TOP08'],
                'TOP09'=>$p['TOP09'],
                'TOP10'=>$p['TOP10'],
                'TOP11'=>$p['TOP11'],
                'TOP12'=>$p['TOP12']
            ];

            $p['Fabric'] = str_replace(' ','',$p['Fabric']);
            if($p['Status'])
            {
                if($p['Status'] == 'Existing')
                {
                    $orders[$key]['Color'] = $p['Color'];
                    $orders[$key]['Style'] = $p['Style'];
                    $orders[$key]['Fabric']= $p['Fabric'];
                    $orders[$key]['Instructions1'] = isset($Instructions[0]) ? $Instructions[0] : null;
                    $orders[$key]['Instructions2'] = isset($Instructions[1]) ? $Instructions[1] : null;
                    $orders[$key]['Instructions3'] = isset($Instructions[2]) ? $Instructions[2] : null;
                    $orders[$key]['Instructions4'] = isset($Instructions[3]) ? $Instructions[3] : null;
                }
                else if($p['Status'] == 'Reference')
                {
                    $orders[$key]['ReferenceColor'] = $p['Color'];
                    $orders[$key]['ReferenceStyle'] = $p['Style'];
                    $orders[$key]['ReferenceFabric'] = $p['Fabric'];
                    $orders[$key]['RefStyleInstructions1'] = isset($Instructions[0]) ? $Instructions[0] : null;
                    $orders[$key]['RefStyleInstructions2'] = isset($Instructions[1]) ? $Instructions[1] : null;
                    $orders[$key]['RefStyleInstructions3'] = isset($Instructions[2]) ? $Instructions[2] : null;
                    $orders[$key]['RefStyleInstructions4'] = isset($Instructions[3]) ? $Instructions[3] : null;
                }
            }
        }
        foreach($orders as $o)
        {
            $orderStatus = PreOrderHdr::editOrderDetailProduct($o);
            if($orderStatus['error']!=null)
            {
                return response()->json(['message'=>$orderStatus['message'],'success'=>$orderStatus['success'],'error'=>$orderStatus['error']],$orderStatus['code']);
            }
        }
        return response()->json(['message'=>$orderStatus['message'],'success'=>$orderStatus['success'],'error'=>$orderStatus['error']],$orderStatus['code']);
    }

    public function getSwatPO()
    {
        try{
            return response()->json(PreOrderHdr::getSwatPO());
        } catch(\Error $e)
        {
            return response()->json(['message'=>'Something went wrong', 'success'=>false,'error'=>$e->getMessage()],400);
        }
    }

    public function assignSimilar(Request $request)
    {
        //validating inputs
        $request->validate([
            'OrderNo' => 'required|integer|exists:PreOrderDtl,PreOrderNumdtl',
            'LineNumber.*' => 'required|integer|exists:PreOrderDtl,PreOrderLinenum',
            'Qty' => 'required|integer',
            'NoSizeRatio' => 'required|in:Y,N',
            'Scale1' => 'required|integer',
            'Scale2' => 'required|integer',
            'Scale3' => 'required|integer',
            'Scale4' => 'required|integer',
            'Scale5' => 'required|integer',
            'Scale6' => 'required|integer',
            'Scale7' => 'required|integer',
            'Scale8' => 'required|integer',
            'Scale9' => 'required|integer',
            'Scale10' => 'required|integer',
            'Scale11' => 'required|integer',
            'Scale12' => 'required|integer',
        ]);
        try{
            $sizes = PreOrderHdr::assignSizesToSImilarProducts($request->all());
            return response()->json(['message'=>$sizes['message'], 'success'=>$sizes['success'],'error'=>$sizes['error']],$sizes['code']);
        }
        catch(\Error $e)
        {
            return response()->json(['message'=>'Something went wrong', 'success'=>false,'error'=>$e->getMessage()],400);
        }
    }
}
