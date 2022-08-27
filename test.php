<?php 
    $curlSession = curl_init();
    curl_setopt($curlSession, CURLOPT_URL, 'https://www.youtube.com/watch?v=UTHHEOqJIaU');
    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

    $jsonData = json_decode(curl_exec($curlSession));

    echo $jsonData;
    echo "Wolol";
    curl_close($curlSession);
?>