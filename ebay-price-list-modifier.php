<?php

require __DIR__ . '/vendor/autoload.php';

if ($argc != 6) {
    echo "Usage: php ebay-price-list-modifier.php <input file> <output file> <modifier> <value> <%|€>\n";
    exit(1);
}
/*
if (!ini_get("auto_detect_line_endings")) {
    ini_set("auto_detect_line_endings", '1');
};
*/
//print_r($argc);
//var_dump($argv);
//exit(1);

$input_file = $argv[1];
$output_file = $argv[2];
$modifier = $argv[3];
$value = $argv[4];
$currency = $argv[5];

$row = 1;

$header =
    "#INFO;Version=1.0.0;Template= eBay-active-revise-price-quantity-download_IT;;;;;;;;;" . "\n" .
    "Action;Item number;Title;Listing site;Currency;Start price;Buy It Now price;Available quantity;Relationship;Relationship details;Custom label (SKU)" . "\n";


file_put_contents($output_file, $header);

if (($input_handle = fopen($input_file, "r")) !== FALSE) {
    while (($input_array = fgetcsv($input_handle, 1000, ";")) !== FALSE) {
        if ($row > 2) {
            //print_r($input_array);
            $input_value = $input_array[5];
            $output_value = (float)$input_value;

            //$modified_value = $output_value;

            switch($currency) {
                case '€':
                    $modified_value = $value;
                    break;
                case '%':
                    $modified_value = $output_value/100*$value;
                    break;
            }


            switch($modifier) {
                case '+':
                    $output_value = $input_value + $modified_value;
                    break;
                case '-':
                    $output_value = $input_value - $modified_value;
                    break;
                case '*':
                    $output_value = $input_value * $modified_value;
                    break;
                case '/':
                    $output_value = $input_value / $modified_value;
                    break;
                default:
                    echo "Unknown modifier: " . $modifier . "\n";
                    exit(1);
            }

//            die($input_value);
//            die($output_value);

            $output_row = sprintf('%s;"%s";"%s";"%s";"%s";"%.1f";%s;"%s";%s;%s;%s',
                $input_array[0],
                $input_array[1],
                $input_array[2],
                $input_array[3],
                $input_array[4],
                    $output_value,
                $input_array[6],
                $input_array[7],
                $input_array[8],
                $input_array[9],
                $input_array[10]
            ) . "\n";
//die($output_row);
            /*
             * Revise;"261138538202";16 Bulloni ruota per cerchi in lega chiave da 17mm perni vite x tutte le marche;"IT";"EUR";"14.0";;"105";;;prestashop-1344
             */

            file_put_contents($output_file, $output_row, FILE_APPEND);
        }

        $row++;

    }
    fclose($input_handle);
};
