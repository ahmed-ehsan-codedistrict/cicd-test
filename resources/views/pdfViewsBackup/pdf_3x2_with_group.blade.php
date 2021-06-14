<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3x2 PDF</title>
    <style>
        @page { margin: 2rem 2rem 0 2rem; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        img {
            max-width: 100%;
        }
        .pdf-header {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: #f0f0f0 1px solid;
        }
        .pdf-header img {
           max-width: 140px;
           max-height: 32px;
        }
        .products-grid-head {
            background-color: #f0f0f0;
            text-align: center;
            padding: 5px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .product-card-row {
            /* width: 100%;
            float: left; */
        }
        .product-card {
            width: 29.2%;
            float: left;
            border: #f0f0f0 1px solid;
            padding: 10px;
            height: 505px;
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
        .mr-2-per {
            margin-right: 1.7%;
        }
        /* this is for lanscape legal mode */
        .pdf-legal-landscape .product-card{
            height: 247px;
            width: 30.5%;
        }
        .pdf-legal-landscape .product-card .product-card-thumb  {
                height: 115px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb img {
            max-height: 115px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }
        /*this is for a4 paper portrait mode */
       .pdf-a4-portrait .product-card{
           height: 400px !important;
           /* width: 29.2%; */
       }
       .pdf-a4-portrait .product-card .product-card-thumb  {
            height: 165px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb img {
            max-height: 165px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 15px;
        }
         /*this is for a4 paper landscape mode */
       .pdf-a4-landscape .product-card{
           height: 237px !important;
           width: 30.1%;

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
           height: 365px !important;
       }

       .pdf-letter-portrait .product-card .product-card-thumb  {
            height: 150px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb img {
            max-height: 150px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 10px;
        }
        /* this is for letter page landscape mode */
        .pdf-letter-landscape .product-card {
            height: 240px !important;
            width: 30%;
        }
        .pdf-letter-landscape .product-card .product-card-thumb  {
            height: 120px;
        }
        .pdf-letter-landscape .product-card .product-card-thumb img {
            max-height: 120px;
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

        echo $header ;
    @endphp
<div class="pdf-<?php echo strtolower($pdf['size']).'-'.strtolower($pdf['orientation']);?>">
    <?php

          $recordIdx = 0;
          $previousGroupName = $pdf['ProductInfo'][0]['groupName'];
          $currentGroupName =  $pdf['ProductInfo'][0]['groupName'];
          echo "<div class='products-grid-head'>" . $pdf['ProductInfo'][$recordIdx]['groupName'] . "</div>";
        for($i=0; $i<count($pdf['rowWithRecords']); $i++){

            if($i % 2 == 0 && $i!=0){
                echo "<div class='wrapper-page'></div>";
                echo $header;
            }
            $currentGroupName = $pdf['ProductInfo'][$recordIdx]['groupName'];
            if( $currentGroupName!=  $previousGroupName){
                $previousGroupName =  $currentGroupName;
                echo "<div class='products-grid-head'>" . $pdf['ProductInfo'][$recordIdx]['groupName'] . "</div>";
            }
            echo " <div class='product-card-row'>";

            for($j=0 ; $j<$pdf['rowWithRecords'][$i]; $j++){

                $class = "";
                if($j >=0){
                    $class = "mr-2-per";
                }

    ?>
            <div class="product-card <?php echo $class; ?>">
                @if (isset($pdf['ProductInfo'][$recordIdx]['groupName']))
                   <!-- <div class="product-card-head"><?php echo $pdf['ProductInfo'][$recordIdx]['groupName']; ?></div> -->
                @endif
                <div class="product-card-thumb">
                    <img src="@php  echo $pdf['ProductInfo'][$recordIdx]['urlFront'];  @endphp" alt="" class="<?php echo $pdf['ProductInfo'][$recordIdx]['NotFoundClass'];  ?>">
                </div>
                <div class="product-card-info">
                    <h5>@php  echo $pdf['ProductInfo'][$recordIdx]['ProductName'];  @endphp</h5>
                    <table class="pci-table" cellspacing="0" cellpadding="0">
                        <?php
                        foreach ($pdf['ProductInfo'][$recordIdx]['fields'][0] as $key => $value) {
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

                        <?php if(array_key_exists('ProductDescription', $pdf['ProductInfo'][$recordIdx]['fields'][0])){   ?>
                            <tr>
                                <th>Description: </th>
                                <td>@php echo  $pdf['ProductInfo'][$recordIdx]['fields'][0]['ProductDescription']; @endphp</td>
                            </tr>
                        <?php }?>

                    </table>
                </div>
            </div>


     <?php
          $recordIdx++;
           }
             echo "</div>";
             echo "<div style='width: 100%; clear: both; height: 8px;'></div>";
        }
      ?>
</div>
</body>

</html>
