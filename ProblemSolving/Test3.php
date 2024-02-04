<?php

function conversionTwentyFourHoursFormat($stringTime){
    $conversion = date("H:i:s", strtotime($stringTime));
    print($conversion ."\n");
}

$twelveHoursFormat = "07:28:45PM";
conversionTwentyFourHoursFormat($twelveHoursFormat);

?>