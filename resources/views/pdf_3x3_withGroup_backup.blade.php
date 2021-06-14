<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3x3 PDF</title>
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
            width: 32%;
            float: left;
            border: #f0f0f0 1px solid;
            padding: 10px 0px;
            height: 337px;
            min-height: 100px;
        }

        .product-card-head {
            background-color: #f0f0f0;
            text-align: center;
            padding: 5px;
            margin: 0 5px 0 5px;
            font-weight: bold;
        }
        .product-card-thumb {
            text-align: center;
            height: 140px;
        }
        .product-card-thumb img {
            max-height: 120px;

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

          /* this is for lanscape legal mode */
        .pdf-legal-landscape .product-card{
            height: 149px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb  {
                height: 105px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb img {
            max-height: 97px;
        }
        .pdf-legal-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 5px;
        }

        /*this is for a4 paper portrait mode */
       .pdf-a4-portrait .product-card{
           height: 263px ;
       }
       .pdf-a4-portrait .product-card .product-card-thumb  {
            height: 95px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb img {
            max-height: 90px;
        }
        .pdf-a4-portrait .product-card .product-card-thumb .image-not-found{
            margin-top: 2px;
        }
         /*this is for a4 paper landscape mode */
       .pdf-a4-landscape .product-card{
           height: 134px ;
           /* background-color: aquamarine; */
       }
       .pdf-a4-landscape .product-card .product-card-thumb  {
            height: 90px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb img {
            max-height: 80px;
        }
        .pdf-a4-landscape .product-card .product-card-thumb .image-not-found{
            margin-top: 0px;
        }
         /* this is for letter page landscape mode */
         .pdf-letter-landscape .product-card {
            height: 144px ;
        }
        .pdf-letter-landscape .product-card .product-card-thumb  {
            height: 100px;
        }
        .pdf-letter-landscape .product-card .product-card-thumb img {
            max-height: 95px;
        }
        .pdf-letter-landscape .product-card .product-card-thumb .image-not-found {
            margin-top: 0px;
        }

          /*this is for letter paper portrait mode */
       .pdf-letter-portrait .product-card{
           height: 240px;
       }

       .pdf-letter-portrait .product-card .product-card-thumb  {
            height: 70px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb img {
            max-height: 70px;
        }
        .pdf-letter-portrait .product-card .product-card-thumb .image-not-found {
            margin-top: 5px;
            max-height: 60px;
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
        for($i=0; $i<count($pdf['rowWithRecords']); $i++){

            if($i % 3 == 0 && $i!=0){
                echo "<div class='wrapper-page'></div>";
                echo $header;
            }
            echo " <div class='product-card-row'>";

            for($j=0 ; $j<$pdf['rowWithRecords'][$i]; $j++){

                $class = "";
                if($j > 0){
                    $class = "mx-2-per";
                }

    ?>
            <div class="product-card <?php echo $class; ?>">
                @if (isset($pdf['ProductInfo'][$recordIdx]['groupName']))
                    <div class="product-card-head"><?php echo $pdf['ProductInfo'][$recordIdx]['groupName']; ?></div>
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
             echo "<div style='width: 100%; clear: both; height: 10px;'></div>";
        }
      ?>
</div>
</body>

</html>
