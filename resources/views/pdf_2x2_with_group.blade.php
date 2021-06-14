<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2x2 PDF</title>
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
        .mr-2-per {
            margin-right: 1.6%;
        }
        .product-card {
            text-align: center;
            width: 46.2%;
            float: left;
            height: 542px;
            border: #f0f0f0 1px solid;
            padding: 5px 10px;
        }

        .product-card-thumb {
            height:310px;
        }
        .product-card-thumb img {
            max-height: 310px;
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
            height: 280px;
            width: 47.5%;
        }
        .pdf-legal-landscape .product-card .product-card-thumb  {
            height: 80px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb img {
            max-height: 80px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }

        /*this is for a4 paper portrate mode */
       .pdf-a4-portrait .product-card{
           height: 428px !important;
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
           height: 276px !important;
           width: 47.1%;
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
            height: 400px !important;
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
            height: 290px !important;
            width: 47%;
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
          //Group name need to display only one time
          $previousGroupName = $pdf['ProductInfo'][0]['groupName'];
          $currentGroupName =  $pdf['ProductInfo'][0]['groupName'];
          echo "<div class='products-grid-head'>" . $pdf['ProductInfo'][$recordIdx]['groupName'] . "</div>";
          $showRecordCount = 0;
        for($i=0; $i<count($pdf['rowWithRecords']); $i++){

            //Replace the group name
            $currentGroupName = $pdf['ProductInfo'][$recordIdx]['groupName'];
            if( $currentGroupName!=  $previousGroupName){
                $previousGroupName =  $currentGroupName;
                $showRecordCount = 0;
                echo "<div class='wrapper-page'></div>";
                echo $header;
                echo "<div class='products-grid-head'>" . $pdf['ProductInfo'][$recordIdx]['groupName'] . "</div>";
            }

            //start product row
            echo " <div class='product-cards-row'>";

            //loop to diplay the product in a row
            for($j=0 ; $j<$pdf['rowWithRecords'][$i]; $j++){

                if($showRecordCount > 3){
                  $showRecordCount = 0;
                  echo "<div class='wrapper-page'></div>";
                  echo $header;
                  echo "<div class='products-grid-head'>" . $pdf['ProductInfo'][$recordIdx]['groupName'] . "</div>";
                }
                //right margin to product card start from second product in a row
                $class = "";
                if($j == 0){
                    $class = "mr-2-per";
                }

    ?>
            <div class="product-card <?php echo $class; ?>">
                <div class="product-card-thumb">
                    <img src="@php  echo $pdf['ProductInfo'][$recordIdx]['urlFront'];  @endphp" alt="" class="<?php echo $pdf['ProductInfo'][$recordIdx]['NotFoundClass'];  ?>">
                </div>
                <div class="product-card-info">
                    <h5>@php  echo $pdf['ProductInfo'][$recordIdx]['ProductName'];  @endphp</h5>
                    <table class="pci-table" cellspacing="0" cellpadding="0">
                        <?php if(array_key_exists('productId', $pdf['ProductInfo'][$recordIdx])){   ?>
                            <tr>
                                 <th>Style Number: </th>
                                 <td>@php echo  $pdf['ProductInfo'][$recordIdx]['productId']; @endphp</td>
                            </tr>
                       <?php }?>
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

                        <?php if(array_key_exists('ProductDescription', $pdf['ProductInfo'][$recordIdx])){   ?>
                            <tr>
                                <th>Description: </th>
                                <td>@php echo  $pdf['ProductInfo'][$recordIdx]['ProductDescription']; @endphp</td>
                            </tr>
                        <?php }?>

                        <?php if(array_key_exists('publicNotes', $pdf['ProductInfo'][$recordIdx]) && isset($pdf['ProductInfo'][$recordIdx]['publicNotes']) && $pdf['publicNotes']==1){   ?>
                            <tr>
                                <th>Public Notes: </th>
                                <td>@php echo  $pdf['ProductInfo'][$recordIdx]['publicNotes']; @endphp</td>
                            </tr>
                        <?php }?>
                        <?php if(array_key_exists('privateNotes', $pdf['ProductInfo'][$recordIdx]) && isset($pdf['ProductInfo'][$recordIdx]['privateNotes']) && $pdf['privateNotes']==1){   ?>
                            <tr>
                                <th>Private Notes: </th>
                                <td>@php echo  $pdf['ProductInfo'][$recordIdx]['privateNotes']; @endphp</td>
                            </tr>
                        <?php }?>

                    </table>
                </div>
            </div>


     <?php
          $recordIdx++;
          $showRecordCount++;
           }
             echo "</div>";
             //move to next row
             if($pdf['orientation']=='portrait'){
                echo "<div style='width: 100%; clear: both; height: 10px;'></div>";
             }else{
                echo "<div style='width: 100%; clear: both; height: 4px;'></div>";
             }
        }
      ?>
</div>
</html>
