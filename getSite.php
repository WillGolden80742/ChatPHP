<?php 
    header("Content-type: application/json; charset=utf-8");
    $html = explode("src=\"",file_get_contents($_GET['link']));
    $arraySrc = array ();
    $htmlSize = sizeof($html);
    foreach ($html as $src) {
        array_push($arraySrc,explode("\"",$src)[0]);
    }
    echo json_encode($arraySrc); 
?>    