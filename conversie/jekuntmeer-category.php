<?php
    error_reporting(0);
    date_default_timezone_set("CET");

    $mapping = Array(
        "administratie / financiën" => 1,
        "computer / internet" => 2,
        "klussen / reparaties / techniek" => 3,
        "ambachtelijk werk / productiewerk" => 3,
        "schoonmaakwerkzaamheden" => 3,
        "dierverzorging / natuur" => 3,
        "groenvoorziening / tuinonderhoud" => 3,
        "contact (lotgenoten)" => 4,
        "creatief" => 4,
        "uitgaan / uitstapjes" => 4,
        "soc. activit (ontspanning / recreatie)" => 4,
        "muziek, toneel, zang en dans" => 4,
        "horeca / catering" => 5,
        "koken en eten" => 5,
        "kinderen en opvoeden" => 6,
        "sport" => 7,
        "sport en bewegen" => 7,
        "transport / vervoer" => 8,
        "zorg / mantelzorg" => 9,
        "maatje of buddy" => 10,
        "belangenbehartiging en buurtwerk" => 11,
        "persoonlijke ontwikkeling / leefstijl" => 12,
        "taal" => 12,
        "trajectbegeleiding en coaching" => 12,
        "verkoop, gastvrouw(heer), receptie" => 0
    );
    
    $category = $_REQUEST["category"];
    if(!$category) exit();
    
    $txt = file_get_contents("https://amsterdam.jekuntmeer.nl/georss.xml");
    $txt = str_replace("xmlns:geo=\"http://www.w3.org/2003/01/geo/wgs84_pos#\"","xmlns:georss=\"http://www.georss.org/georss\" xmlns:gml=\"http://www.opengis.net/gml\"", $txt);
    
    
    $pattern = "/(\<georss\:point\>)([-+]?[0-9]*\.?[0-9]+) ([-+]?[0-9]*\.?[0-9]+)(\<\/georss\:point\>)/i";
    $replace = "<latitude>$2</latitude><longitude>$3</longitude>";
    $txt = preg_replace($pattern, $replace, $txt);
    
    $xml = new SimpleXMLElement($txt, LIBXML_NOCDATA);
    $json = Array("type" => "FeatureCollection", "features" => Array());
    $ids = Array();
    
    foreach($xml->channel->item as $key => $item){
        $id = str_replace("https://amsterdam.jekuntmeer.nl/zoeken/show/","",(string)$item->guid);
        $id = substr($id,0,strpos($id,"-"));

        if($mapping[(string)$item->category] == $category){
            $json_item = Array(
                "type" => "Feature",                                                                                     
                "geometry" => Array("type" => "Point", "coordinates" => Array((float)$item->longitude, (float)$item->latitude)),
                "properties" => Array()
            );
            foreach($item as $key2 => $value){
                $json_item["properties"][$key2] = (string)$value;
            }
            if(!in_array($id, $ids)){
                $json["features"][] = $json_item;
                $ids[] = $id;
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($json);
?>