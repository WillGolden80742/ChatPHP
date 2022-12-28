<?php 
    require "EnvLoad/Environment.php"; 
    require "vendor/autoload.php";
    $iniEnv = new Environment();
    $iniEnv->load(__DIR__);
    #show erros in php
    ini_set('display_errors', 1);
?>