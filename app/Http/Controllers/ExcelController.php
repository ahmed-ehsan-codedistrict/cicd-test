<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PDFController;
use App\Utilities\Helpers;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ExportPdf;

class ExcelController extends Controller
{
    public function createExcel(Request $request)
    {

        $url = $request->root();
        $data = [];
        $title = "";
        $logo = "";
        $publicNotes = 0;
        $privateNotes = 0;
        $recordPerPage = 0;
        $linesheetId = 0;

        if ($request->has('linesheetId')) {
            $linesheetId = $request->linesheetId;
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

        //Excel title if product export from linesheet
        $title = Helpers::getTitle($linesheetId, $product);

        $data['title'] = $title;
        $data['fileName'] = $fileName;
        $data['notFound'] =  "No-image-found.jpg";
        $data['ProductInfo'] = [];
        $data['groupName'] = [];
        $data['userId'] =  Auth::id();
        $data['format'] = $request->format;
        $data['companyNo'] =   Auth::user()->CompanyNo;
        $data['url'] =   $url;
        $data['linesheetId'] =   $linesheetId;
        $data['publicNotes'] =   $publicNotes;
        $data['privateNotes'] =   $privateNotes;
        $publicPathImages = [];

        foreach ($product as $p) {

            $thumbnail_f =  $data['notFound'];
            $groupName = "";
            $thumbnail_f_uri =  "storage/images/SM_" . trim(strtoupper($p->StyleNumber)) . "_" . trim(strtoupper($p->SAColor)) . "_" . trim(strtoupper("F")) . ".jpg";

            if (file_exists($thumbnail_f_uri)) {
                // $thumbnail_f =  $url . "/" . $thumbnail_f_uri;
                $thumbnail_f =  $thumbnail_f_uri;
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

            $temp = [
                "Style" => $p->StyleNumber,
                "Name" => $p->ProductName,
                "Description" => $p->ProductDescription,
            ];

            $headings = ["Style", "Name", "Description"];

            foreach ($request->fields as $f) {
                if ($f == 'LSGPPrivateNotes' || $f == 'LSGPPublicNotes') {
                    continue;
                }
                $temp[$f] = trim($p->$f);
                array_push($headings, Helpers::removeSpaceBetweenCamelCaseString($f));
            }

            if (isset($groupName) && strlen($groupName) > 0) {
                $temp["Group"] = $groupName;
                array_push($headings, 'Group');
            }

            $pubNotes = "";
            $privNotes = "";
            if ($linesheetId > 0) {
                $pubNotes =  $p->LSGPPublicNotes;
                $privNotes = $p->LSGPPrivateNotes;
                if ($publicNotes > 0) {
                    $temp['PublicNotes'] = $pubNotes;
                    array_push($headings, 'Public Notes');
                }
                if ($privateNotes > 0) {
                    $temp['PrivateNotes'] = $privNotes;
                    array_push($headings, 'Private Notes');
                }
            }
            // $temp["ImageUrl"] = $thumbnail_f;
            // array_push($headings, 'ImageUrl');
            array_push($data['ProductInfo'], $temp);
            array_push($publicPathImages, $thumbnail_f);
        }
        $data['headings'] = array_unique($headings);
        $data['groupName'] = array_unique($data['groupName']);
        $data['imageURI'] = ($publicPathImages);
        dispatch(new ExportPdf($data));
        return ($data);
    }
}
