<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Jobs\ExportPdf;
use App\Utilities\Helpers;
// use Pusher\Laravel\Facades\Pusher;
use Illuminate\Support\Facades\Event;
use Pusher\Pusher;
use App\Exports\ProductExports;
use Maatwebsite\Excel\Facades\Excel;

class PDFController extends Controller
{


    public function createPDF(Request $request)
    {

        $url = $request->root();
        $orientation = "portrait";
        $paperSize = "a4";
        $recordPerPage = 0;
        $linesheetId = 0;
        $rowPerPage =  0;
        $productPerRow =  0;
        $rowWithRecords = [];
        $data = [];
        $title = "";
        $logo = "";
        $publicNotes = 0;
        $privateNotes = 0;

        //validations
        if ($request->has('orientation')) {
            $orientation = $request->orientation;
        }
        if ($request->has('size')) {
            $paperSize = $request->size;
        }
        if ($request->has('linesheetId')) {
            $linesheetId = $request->linesheetId;
        }
        if ($request->has('logo') && isset($request->logo)) {
            $logo = $url . "/DivLogo/" . $request->logo;
        }

        if ($request->has('publicNotes') && isset($request->publicNotes) && $request->publicNotes == 1) {
            $publicNotes = 1;
        }
        if ($request->has('privateNotes') && isset($request->privateNotes) && $request->privateNotes == 1) {
            $privateNotes = 1;
        }

        /* finding how many product need to be fetched
          when pdf export from workspace */
        if ($request->has('products')) {
            $recordPerPage = Helpers::getRecordPerPageCount($request->products);
        }

        //get User Divisions
        $divisions = User::userDivisions();

        //get Json for product
        $product   =  Helpers::getProductJsonForExport($recordPerPage);

        //get products
        $product =  Helpers::getProducts($request, $product, $divisions, $recordPerPage);

        /*  serialize the data for frontend for pdf  */

        //unique file name
        $fileName = $request->FileName . '-' . date('Y-m-d H:m:s');

        //pdf title if product export from linesheet
        $title = Helpers::getTitle($linesheetId, $product);
        $data['title'] = $title;
        $data['fileName'] = $fileName;
        $data['format'] = $request->format;
        $data['template'] = $request->template;
        $data['orientation'] = $orientation;
        $data['size'] = $paperSize;
        $data['logo'] = $logo;
        $data['notFound'] = $url . "/not_found.svg";
        $data['ProductInfo'] = [];
        $data['groupName'] = [];
        $data['userId'] =  Auth::id();
        $data['companyNo'] =   Auth::user()->CompanyNo;
        $data['url'] =   $url;
        $data['linesheetId'] =   $linesheetId;
        $data['publicNotes'] =   $publicNotes;
        $data['privateNotes'] =   $privateNotes;

        //fetch product information from array and make json
        foreach ($product as $p) {

            $urlFront =  $data['notFound'];
            $urlBack =  $data['notFound'];
            $imageNotFound = "image-not-found";
            $groupName = "";

            $uriFront =  "storage/images/" . trim(strtoupper($p->StyleNumber)) . "_" . trim(strtoupper($p->SAColor)) . "_" . trim(strtoupper("F")) . ".jpg";
            $uriBack =  "storage/images/" . trim(strtoupper($p->StyleNumber)) . "_" . trim(strtoupper($p->SAColor)) . "_" . trim(strtoupper("B")) . ".jpg";

            if (file_exists($uriFront)) {
                $urlFront =  $url . "/" . $uriFront;
                $imageNotFound = "";
            }
            if (file_exists($uriBack)) {
                $urlBack =  $url . "/" . $uriBack;
            }

            /**
             * find a product's group and push group name to group array
             */
            if ($linesheetId > 0) {
                $groupName =  $p->groupName;
                array_push($data['groupName'], $p->groupName);
            } else {
                if (in_array(trim($p->StyleNumber), array_column($request->products, 'productId'))) {
                    $arrIdx = array_search(trim($p->StyleNumber), array_column($request->products, 'productId'));
                    if (isset($arrIdx) && $arrIdx > -1) {
                        if (isset($request->products[$arrIdx]['groupName'])) {
                            $groupName = $request->products[$arrIdx]['groupName'];
                            array_push($data['groupName'], $groupName);
                        }
                    }
                }
            }

            $pubNotes = "";
            $privNotes = "";
            if ($linesheetId > 0) {
                $pubNotes =  strlen($p->LSGPPublicNotes) <= $this->getCharLen($orientation, $paperSize, $request->template) ? $p->LSGPPublicNotes : substr($p->LSGPPublicNotes, 0, $this->getCharLen($orientation, $paperSize, $request->template)) . '....';
                $privNotes = strlen($p->LSGPPrivateNotes) <= $this->getCharLen($orientation, $paperSize, $request->template) ? $p->LSGPPrivateNotes : substr($p->LSGPPrivateNotes, 0, $this->getCharLen($orientation, $paperSize, $request->template)) . '....';
            }
            $temp = [
                "productId" => $p->StyleNumber,
                "groupName" => $groupName,
                "publicNotes" => $pubNotes,
                "privateNotes" => $privNotes,
                "urlFront" => $urlFront,
                "NotFoundClass" => $imageNotFound,
                "urlBack" => $urlBack,
                "ProductName" => $p->ProductName,
                "ProductDescription" => $p->ProductDescription,
                "fields" => []
            ];

            $tempFieldArr = array();
            foreach ($request->fields as $f) {
                if ($f == 'LSGPPrivateNotes' || $f == 'LSGPPublicNotes') {
                    continue;
                }
                $tempFieldArr[$f] = trim($p->$f);
            }

            array_push($temp['fields'], $tempFieldArr);
            array_push($data['ProductInfo'], $temp);
        }

        //sort the group name asc
        sort($data['groupName']);

        if ($request->template == "2x2") {
            $rowPerPage =  2;
            $productPerRow =  2;
        }
        if ($request->template == "3x2") {
            $rowPerPage =  2;
            $productPerRow =  3;
        }
        if ($request->template == "3x3") {
            $rowPerPage =  3;
            $productPerRow =  3;
        }
        //Group name with total number of records
        $GroupsWithValues =  array_count_values($data['groupName']);

        //finding how many products will show in a row
        foreach ($GroupsWithValues as $keyArr => $value) {

            if ($value <= $productPerRow) {
                $rowWithRecords[] = $value;
            } else {
                $tempStorValue =  $value;
                $totalRows = explode('.', $value / $productPerRow);
                $increasBy1 =  isset($totalRows[1]) && $totalRows[1] > 0 ? 1 : 0;
                $totalRows =  $totalRows[0] + $increasBy1;
                for ($idx = 0; $idx < $totalRows; $idx++) {
                    if ($tempStorValue > $productPerRow) {
                        $rowWithRecords[] = $productPerRow;
                    } else {
                        $rowWithRecords[] = $tempStorValue;
                    }
                    $tempStorValue =  $tempStorValue - $productPerRow;
                }
            }
        }

        //Finding total pages for pdf
        $totalPages = explode('.', count($rowWithRecords) / $rowPerPage);
        $increasBy1 =  isset($totalPages[1]) && $totalPages[1] > 0 ? 1 : 0;
        $totalPages =  $totalPages[0] + $increasBy1;

        $data['totalPages'] = $totalPages;
        $data['rowWithRecords'] = $rowWithRecords;
        $data['rowPerPage'] = $rowPerPage;
        $data['productPerRow'] = $productPerRow;
        $data['viewName'] = $this->getViewName($data);

        // return $data;
        //sort the product info array asc
        $data['ProductInfo'] = Helpers::sortAssociateArr($data['ProductInfo'], 'groupName', 'asc');
        // dispatch job
        dispatch(new ExportPdf($data));
        echo "job stored";
    }


    //function to get the view name
    private function getViewName(array $data)
    {
        $viewName = "pdf";

        if (
            (strtolower($data['orientation']) == 'portrait'
                ||
                strtolower($data['orientation']) == 'landscape')
            &&

            (strtolower($data['size']) == "legal"
                ||
                strtolower($data['size']) == "a4"
                ||
                strtolower($data['size']) == "letter")
        ) {

            if (
                ($data['template'] == '3x3'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0)
                ||
                ($data['template'] == '3x3'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0 &&
                    $data['linesheetId'] > 0)
            ) {
                $viewName = "pdf_3x3_with_group";
            }

            if (
                $data['template'] == '3x3' && count($data['rowWithRecords']) <= 0
                && count($data['groupName']) <= 0
            ) {
                $viewName = "pdf_3x3_without_group";
            }

            if (
                ($data['template'] == '3x2'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0)
                ||
                ($data['template'] == '3x2'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0 &&
                    $data['linesheetId'] > 0)
            ) {
                $viewName = "pdf_3x2_with_group";
            }

            if (
                $data['template'] == '3x2' && count($data['rowWithRecords']) <= 0
                && count($data['groupName']) <= 0
            ) {
                $viewName = "pdf_3x2_without_group";
            }

            if (
                ($data['template'] == '2x2'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0)
                ||
                ($data['template'] == '2x2'
                    && count($data['rowWithRecords']) > 0
                    && count($data['groupName']) > 0 &&
                    $data['linesheetId'] > 0)
            ) {
                $viewName = "pdf_2x2_with_group";
            }

            if (
                $data['template'] == '2x2' && count($data['rowWithRecords']) <= 0
                && count($data['groupName']) <= 0
            ) {
                $viewName = "pdf_2x2_without_group";
            }
        }

        return $viewName;
    }
    //get character length for notes
    private function getCharLen($mode, $size, $pdf)
    {
        if ($mode == "landscape" && ($size == "a4" || $size == "letter")  && $pdf == "3x2") {
            return 30;
        }
        if ($mode == "portrait" && ($size == "legal" || $size == "letter" || $size == "a4")  && $pdf == "3x3") {
            return 20;
        }
        if ($mode == "landscape" && ($size == "legal" || $size == "letter" || $size == "a4")  && $pdf == "3x3") {
            if ($size == "a4" || $size == "letter") {
                return 35;
            }
            return 45;
        }
        return 70;
    }
}
