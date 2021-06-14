<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3x2 PDF</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            font-size: 12px;
        }
        img {
            max-width: 100%;
        }
        .pdf-header {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: #f0f0f0 1px solid;
        }
        .product-card-row {
            /* width: 100%;
            float: left; */
        }
        .product-card {
            width: 29%;
            float: left;
            border: #f0f0f0 1px solid;
            padding: 10px;
            height: 525px;
        }
        .product-card-head {
            background-color: #f0f0f0;
            text-align: center;
            padding: 5px;
            font-weight: bold;
        }
        .product-card-thumb {
            text-align: center;
            height: 280px;
        }
        .product-card-thumb img {
            max-height: 280px;
        }
        .mx-2-per {
            margin: 0 1.6%;
        }
        .product-card-info h5 {
            border-color: #f0f0f0;
            border-style: solid;
            border-width: 1px 0 1px 0;
            padding: 8px 0;
            text-align: center;
            margin: 0px 0 10px 0;
        }
        .product-card-info table tr th,
        .product-card-info table tr td {
            padding: 2px 4px;
            font-size: 10px;
            vertical-align: top;
        }
        .product-card-info table tr th {
            width:80px;
            text-align: right;
        }
        .product-card-list {
            width: 100%;
            border-top: 1px solid #f0f0f0;
            /* text-align: right; */
            padding: 10px;
        }
        .wrapper-page {
            page-break-after: always;
        }
        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        .image-not-found {
             margin-top: 86px;
        }
        /* this is for lanscape legal mode */
        .pdf-legal-landscape .product-card{
            height: 260px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb  {
                height: 120px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb img {
            max-height: 120px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }
        /*this is for a4 paper portrait mode */
       .pdf-a4-portrait .product-card{
           height: 415px !important;
       }
       .pdf-a4-portrait .product-card .product-card-thumb  {
            height: 180px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb img {
            max-height: 180px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }
         /*this is for a4 paper landscape mode */
       .pdf-a4-landscape .product-card{
           height: 253px !important;
           /* background-color: aquamarine; */
       }
       .pdf-a4-landscape .product-card .product-card-thumb  {
            height: 115px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb img {
            max-height: 115px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }
          /*this is for letter paper portrait mode */
       .pdf-letter-portrait .product-card{
           height: 383px !important;
       }

       .pdf-letter-portrait .product-card .product-card-thumb  {
            height: 140px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb img {
            max-height: 140px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 10px;
        }
        /* this is for letter page landscape mode */
        .pdf-letter-landscape .product-card {
            height: 260px !important;
        }
        .pdf-letter-landscape .product-card .product-card-thumb  {
            height: 130px;
        }
        .pdf-letter-landscape .product-card .product-card-thumb img {
            max-height: 130px;
        }
        .pdf-letter-landscape .product-card .product-card-thumb .image-not-found {
            margin-top: 0px;
        }
    </style>
</head>

<body>

    @php

        $header = "<div class='pdf-header'>
                            <img src='{$pdf['logo']}' alt=''>
                        </div>
                   <h3>{$pdf['title']}</h3>
                 ";

        $pagePerRecord = 6;
        $totalProducts = count($pdf['ProductInfo']);
        $tpArr =  explode('.', $totalProducts / $pagePerRecord);
        $increasBy1 =  isset($tpArr[1]) && $tpArr[1]>0 ? 1:0;
        $totalPages =  $tpArr[0]+$increasBy1;

        $srtIdx =  0;
        $endIdx  = $pagePerRecord;

        echo $header ;
    @endphp
 <div class="pdf-<?php echo strtolower($pdf['size']).'-'.strtolower($pdf['orientation']);?>">
    <?php

        for($i=0; $i<$totalPages; $i++){

                if($i>0){

                    echo "<div class='wrapper-page'></div>";

                    echo $header;
                    $leftProducts =  $totalProducts-$endIdx;
                    $srtIdx = $endIdx;
                    $endIdx =  $endIdx + $pagePerRecord;
                    if($leftProducts<$pagePerRecord){
                        $endIdx = $totalProducts;
                    }

                }
        $tempExecution = 0;
         $srtIdx;
         $endIdx;
        for($j=$srtIdx ; $j<$endIdx; $j++){

            if($tempExecution==0 || $tempExecution==3){
               echo " <div class='product-card-row'>";
            }
             $class = "";
            if($tempExecution==1 || $tempExecution==4){
                $class = "mx-2-per";
            }

    ?>

            <div class="product-card <?php echo $class; ?>">
                <div class="product-card-head">REPLENISHMENT 12/30</div>
                <div class="product-card-thumb">
                    <img src="@php  echo $pdf['ProductInfo'][$j]['urlFront'];  @endphp" alt="" class="<?php echo $pdf['ProductInfo'][$j]['NotFoundClass'];  ?>">
                </div>
                <div class="product-card-info">
                    <h5>@php  echo $pdf['ProductInfo'][$j]['ProductName'];  @endphp</h5>
                    <table class="pci-table" cellspacing="0" cellpadding="0">
                        <?php
                           foreach ($pdf['ProductInfo'][$j]['fields'][0] as $key => $value) {
                              if($key!='ProductDescription'){
                        ?>
                        <tr>
                            <th>@php echo preg_replace('/([a-z])([A-Z])/s','$1 $2', $key); @endphp:</th>
                            <td>@php echo $value; @endphp</td>
                        </tr>
                        <?php
                            }
                           }
                        ?>

                        <?php if(array_key_exists('ProductDescription', $pdf['ProductInfo'][$j]['fields'][0])){   ?>
                             <tr>
                                  <th>Description: </th>
                                  <td>@php echo  $pdf['ProductInfo'][$j]['fields'][0]['ProductDescription']; @endphp</td>
                             </tr>
                        <?php }?>

                    </table>
                </div>
            </div>

            <?php

                if($tempExecution==2 || $tempExecution==5){
                    echo "<div style='width: 100%; clear: both; height: 10px;'></div>";
                    echo " </div>";
                }

                $tempExecution++;

                    }
                }
          ?>
          </div>

</body>

</html>
