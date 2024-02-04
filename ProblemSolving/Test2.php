<?php

function averagePostiveNegaiveZero($arr) {
    $count_array = count($arr);
    
    $positive_value = 0;
    $negative_value = 0;
    $zero_value = 0;
    
    for ($i=0; $i<$count_array; $i++) {
        if ($arr[$i] > 0) {
            $positive_value++;
        } else if ($arr[$i] < 0) {
            $negative_value++;
        } else {
            $zero_value++;
        }
    }
    
    print number_format(($positive_value / $count_array), 6) ."\n";
    echo number_format(($negative_value / $count_array), 6) ."\n";
    echo number_format(($zero_value / $count_array), 6) ."\n";
}

$arr = [1,1,0,-1,-1];
averagePostiveNegaiveZero($arr);


?>