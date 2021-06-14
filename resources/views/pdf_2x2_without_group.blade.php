<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2x2 PDF</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        img {
            max-width: 100%;
        }
        .sf-pdf-header-soul img{
            max-width: 140px;
           max-height: 32px;
        }
        .pdf-header {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: #f0f0f0 1px solid;
        }
        .pdf-header img {
           max-width: 140px;
           max-height: 32px;
        }
        .product-card {
            text-align: center;
            width: 46%;
            float: left;
            height: 520px;
            border: #f0f0f0 1px solid;
            padding: 10px;

            /* background-color: aqua; */
        }
        .product-card-thumb {
            /* border: #f0f0f0 1px solid; */
            /* padding: 10px 10px 8px 10px; */
            height:320px;
        }
        .product-card-thumb img {
            max-height: 320px;
        }
        .product-card-attributes h6 {
            margin: 0;
            font-weight: normal;
            padding: 5px;
        }
        .product-card-attributes{
            height: 120px;
        }
        .product-card-attributes h6 span {
            padding: 6px 5px;

            line-height: 2;
        }
        .pca-desc {
            display: block;
            width: 100%;
        }
        .wrapper-page {
            page-break-after: always;
        }

        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        .image-not-found {
            margin-top: 92px;
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

        /* this is for lanscape legal mode */
        .pdf-legal-landscape .product-card{
            height: 255px;
            width: 47%;
        }
        .pdf-legal-landscape .product-card .product-card-thumb  {
            height: 125px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb img {
            max-height: 125px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }

        /*this is for a4 paper portrate mode */
       .pdf-a4-portrait .product-card{
           height: 400px !important;
       }
       .pdf-a4-portrait .product-card .product-card-thumb  {
            height: 200px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb img {
            max-height: 200px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 50px;
        }

        /*this is for a4 paper landscape mode */
       .pdf-a4-landscape .product-card {
           height: 250px !important;
           width: 47%;
       }
       .pdf-a4-landscape .product-card .product-card-thumb  {
            height: 115px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb img {
            max-height: 115px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb .image-not-found {
            margin-top: 0px;
        }

        /* this is for letter page portrait mode */
        .pdf-letter-portrait .product-card{
            height: 380px !important;
        }
        .pdf-letter-portrait .product-card .product-card-thumb  {
            height: 180px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb img {
            max-height: 180px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb .image-not-found {
            margin-top: 20px;
        }

        /* this is for letter page landscape mode */
        .pdf-letter-landscape .product-card {
            height: 260px !important;
            width: 46.8%;
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

        $header = "<div>
            <div class='sf-pdf-header-soul'><img src='{$pdf['logo']}' alt=''></div>
            <div style='background-color: #f0f0f0; margin: 20px 0 15px; height: 1px;'></div>
            <h5>{$pdf['title']}</h5></div>";

        $pagePerRecord = 4;
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
        for($j=$srtIdx ; $j<$endIdx; $j++){

    ?>

                <div class="product-card">
                    <div class="product-card-thumb">
                        <img src="@php  echo $pdf['ProductInfo'][$j]['urlFront'];   @endphp" alt="" class="<?php echo $pdf['ProductInfo'][$j]['NotFoundClass'];  ?>">
                    </div>
                    <div class="product-card-info">
                        <h5>@php  echo $pdf['ProductInfo'][$j]['ProductName'];  @endphp</h5>
                        <table class="pci-table" cellspacing="0" cellpadding="0">
                            <?php if(array_key_exists('productId', $pdf['ProductInfo'][$j])){   ?>
                                <tr>
                                     <th>Style Number: </th>
                                     <td>@php echo  $pdf['ProductInfo'][$j]['productId']; @endphp</td>
                                </tr>
                           <?php }?>
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

                            <?php if(array_key_exists('ProductDescription', $pdf['ProductInfo'][$j])){   ?>
                                 <tr>
                                      <th>Description: </th>
                                      <td>@php echo  $pdf['ProductInfo'][$j]['ProductDescription']; @endphp</td>
                                 </tr>
                            <?php }?>

                        </table>
                    </div>
                </div>
            {{-- </div> --}}


      <?php
            if($tempExecution == 0 || $tempExecution == 2){
                echo " <div style='width:2%;float: left;'></div>";
            }

            if($tempExecution == 1){

                echo  "<div style='width: 100%; clear: both; height: 10px;'></div>";

            }
            $tempExecution++;

                }
            }
        ?>

        </div>

</body>
</html>
