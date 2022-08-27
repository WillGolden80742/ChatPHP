<?php
    $string = "0";

    $url = "https://www.youtu.be/8BKrWEvWcA";

    $urlY1 = "youtube.com/";
    $urlY2 = "youtu.be/";
    if (str_contains($url,$urlY1) || str_contains($url,$urlY2)) {
        if (str_contains($url,$urlY2)) {
            echo $urlY1."embed/".explode("youtu.be/",$url)[1];
        } else if (str_contains($url,$urlY1))  {
            echo $urlY1."embed/".explode("watch?v=",$url)[1];
        }
    }    

?>