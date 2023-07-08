<?php
    class FileController {
        private $file;
        private $error;
        private $maxSize = 1024000;
        private $extension;

        function __construct($f) {
            $this->file = $f;
            $this->extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        }

        function getImage() {
            if (!file_exists("tmp")) {
                mkdir("tmp");
            }
            
            $name = "tmp/" . md5(rand()) . time() . "." . $this->extension;
            if (move_uploaded_file($this->file['tmp_name'], $name)) {
                $size = filesize($name);
                if ($size < $this->maxSize) {
                    $bytes = addslashes(fread(fopen($name, "r"), $size));
                    shell_exec("rm -rf " . $name);
                    return $bytes;
                } else {
                    $this->error = "Tamanho máximo de " . $this->formatMB($this->maxSize) . ". Imagem tem " . $this->formatMB($size);
                    shell_exec("rm -rf " . $name);
                    return false;
                }
            }
        }

        function getFormat() {
            return $this->extension;
        }

        function formatMB($size) {
            return round(($size / 1024000), 2) . " MBs";
        }

        function getError() {
            return $this->error;
        }
    }

?>