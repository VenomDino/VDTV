<?php

function getStringBetween($originalString, $startString, $endString)
{
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


$input_data = file_get_contents("http://localhost/IPTV-Generator/aaaa.txt");
$lines = explode("#KODIPROP:inputstream.adaptive.license_type=", $input_data);

$final = array();
$channels = array();


foreach ($lines as $line) {

    $line = trim($line);

    $license_type = "widevine";
    $license_key_local = "";

    if (strpos(strtolower($line), "clearkey") === 0) {
        $license_type = "clearkey";
    } else if (strpos(strtolower($line), "playready") === 0) {
        $license_type = "widevine";
    }

    $license_key = getStringBetween($line, "#KODIPROP:inputstream.adaptive.license_key=", "\n");

    if($license_key == ""){
        $license_key = "https://proxy.uat.widevine.com/proxy?provider=widevine_test";
    }

    $group_title = getStringBetween($line, "group-title=\"", "\"");
    $logo = getStringBetween($line, "tvg-logo=\"", "\"");
    $channel_name = getStringBetween($line, "\" ,", "\n");

    $video_links = explode("\n", $line);
    $video_link = end($video_links);

    $channels["license-type"] = trim($license_type);
    $channels["tvg-logo"] = trim($logo);
    $channels["group-title"] = trim($group_title);
    $channels["channel-name"] = trim($channel_name);
    $channels["video-streaming-link"] = trim($video_link);

    if (filter_var(trim($license_key), FILTER_VALIDATE_URL)) {
        $channels["license-key"] = trim($license_key);
        $channels["license-key-local"] = "";
    } else {
        $channels["license-key"] = "";
        $channels["license-key-local"] = trim($license_key);
    }

    // print_r($channels);

    $final[] = $channels;
}

// Convert the output to JSON format
$json_output = json_encode($final, JSON_PRETTY_PRINT);

header("Content-type: application/json");
// Output the JSON
echo $json_output;

// file_put_contents("manda-c.json", $json_output);
