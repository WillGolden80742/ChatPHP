<?php

    class FileController {
        private $file; 
        private $error; 
        private $maxSize = 1024000;  
        
        function __construct($f) {
            $this->file = $f;
        }

        function getFile () {
            if (!file_exists("tmp")) {
                mkdir("tmp");
            }
            $name = "tmp/".md5(rand()).time().".jpg";
            if (move_uploaded_file($this->file['tmp_name'], $name)) {
                $size = filesize($name);      
                if ($size < $this->maxSize) {    
                    $bytes = addslashes(fread(fopen($name, "r"), $size));
                    shell_exec("rm -rf ".$name);
                    return $bytes;
                }  else {
                    $this->error = "<p>Tamanho máximo de ".$this->formatMB($this->maxSize)." <br> Imagem tem ".$this->formatMB($size)." </p>";
                    shell_exec("rm -rf ".$name);
                    return false;
                }
            }
        }

        function formatMB ($size) {
            return round(($size/1024000),2)." MBs";
        }

        function getError () {
            return  $this->error;
        }
    }
?>