<?php
    error_reporting(0);
    date_default_timezone_set("CET");

    $txt = file_get_contents("https://amsterdam.jekuntmeer.nl/georss.xml");
    $txt = str_replace("xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\"","xmlns:georss=\"http://www.georss.org/georss\" xmlns:gml=\"http://www.opengis.net/gml\"", $txt);
    
    
    $pattern = "/(\<georss\:point\>)([-+]?[0-9]*\.?[0-9]+) ([-+]?[0-9]*\.?[0-9]+)(\<\/georss\:point\>)/i";
    $replace = "<georss:where><gml:Point srsName=\"http://www.opengis.net/def/crs/EPSG/0/4326\"><gml:pos>$2 $3</gml:pos></gml:Point></georss:where>";
    $txt = preg_replace($pattern, $replace, $txt);
    header('Content-Type: text/xml');
    print($txt);
    exit();
?>