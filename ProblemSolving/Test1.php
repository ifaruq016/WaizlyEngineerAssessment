<?php

function minMaxResult($arr){
    $length_arr = count($arr);
    
    $arr_sum = [];
    $index = 0;
    while ($index != $length_arr) {
        $sum = 0;
        for ($i=0; $i<$length_arr; $i++) {
            if ($index != $i){
                $sum += $arr[$i];
            }
        }
        array_push($arr_sum, $sum);
        $index++;
    }

    sort($arr_sum);
    echo $arr_sum[0] ." ". $arr_sum[count($arr_sum) -1] ."\n";
}

$arr = [1,2,3,4,5];
minMaxResult($arr)


?>