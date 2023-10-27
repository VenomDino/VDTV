<?php

function getStringBetween($originalString, $startString, $endString) {
    $startPosition = strpos($originalString, $startString);
    if ($startPosition === false) {
        return false; // Start string not found
    }

    $startPosition += strlen($startString);

    $endPosition = strpos($originalString, $endString, $startPosition);
    if ($endPosition === false) {
        return false; // End string not found
    }

    return substr($originalString, $startPosition, $endPosition - $startPosition);
}


$input_data = file_get_contents("https://hunterzxd.club/playlist/samsungtv.json");
$lines = explode("#EXTINF:-1", $input_data);

$final = array();
$channels = array();

$i = 1;
$j = 1;

foreach($lines as $line){

    $j++;

    $group_title = getStringBetween($line, "group-title=\"", "\"");
    $channel_name = getStringBetween($line, "\",", "\nhttp");
    $license_key = "https://proxy.uat.widevine.com/proxy?provider=widevine_test";
    $logo = getStringBetween($line, "tvg-logo=\"", "\"");
    $license_type = "widevine";

    $dd = explode("\n", trim($line));

  

    foreach($dd as $d){

        if(filter_var(trim($d), FILTER_VALIDATE_URL)){
            // echo $i . ") " . $d . "<br>";

            $channels["license-type"] = trim($license_type);
            $channels["tvg-logo"] = trim($logo);
            $channels["group-title"] = trim($group_title);
            $channels["channel-name"] = trim($channel_name);
            $channels["video-streaming-link"] = trim($d);

            if(filter_var(trim($license_key), FILTER_VALIDATE_URL)){
                $channels["license-key"] = trim($license_key);
                $channels["license-key-local"] = "";
            } else{
                $channels["license-key"] = "";
                $channels["license-key-local"] = trim($license_key);
            }

            // print_r($channels);

            $final[] = $channels;

            $i = $i + 1;
        }
    }

    // echo $line . "\n\n <br><br> \n\n";
    
}

// echo "<br>" . $j;

header("Content-type: application/json");

echo json_encode($final);