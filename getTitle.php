<?php 
    header("Content-type: application/json; charset=utf-8");
    function get_title($url){
        $str = file_get_contents($url);
        if(strlen($str)>0){
        $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
        preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
        return $title[1];
        }
    }
    echo json_encode(get_title($_GET['link']));
?>    