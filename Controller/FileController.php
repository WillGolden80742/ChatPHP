<?php
class FileController
{
    private $file;
    private $error;
    private $maxSize = 1024000;
    private $extension;

    function __construct($f)
    {
        $this->file = $f;
        $this->extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
    }

    function getImage()
    {
        if (!file_exists("tmp")) {
            mkdir("tmp");
        }

        $name = "tmp/" . md5(rand()) . time() . "." . $this->extension;
        if (move_uploaded_file($this->file['tmp_name'], $name)) {
            $image = null;
            switch ($this->extension) {
                case 'gif':
                    $image = imagecreatefromgif($name);
                    break;
                case 'png':
                    $image = imagecreatefrompng($name);
                    break;
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($name);
                    break;
            }

            if ($image !== null) {
                $originalWidth = imagesx($image);
                $originalHeight = imagesy($image);
                $newWidth = 400;
                $newHeight = ($originalHeight / $originalWidth) * $newWidth;

                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

                $resizedName = "tmp/" . md5(rand()) . time() . "." . $this->extension;
                switch ($this->extension) {
                    case 'gif':
                        imagegif($resizedImage, $resizedName);
                        break;
                    case 'png':
                        imagepng($resizedImage, $resizedName);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($resizedImage, $resizedName);
                        break;
                }

                imagedestroy($image);
                imagedestroy($resizedImage);

                $size = filesize($resizedName);
                if ($size < $this->maxSize) {
                    $bytes = addslashes(fread(fopen($resizedName, "r"), $size));
                    shell_exec("rm -rf " . $name);
                    shell_exec("rm -rf " . $resizedName);
                    return $bytes;
                } else {
                    $this->error = "Tamanho máximo de " . $this->formatMB($this->maxSize) . ". Imagem tem " . $this->formatMB($size);
                    shell_exec("rm -rf " . $name);
                    shell_exec("rm -rf " . $resizedName);
                    return false;
                }
            }
        }
    }


    function getFormat()
    {
        return $this->extension;
    }

    function formatMB($size)
    {
        return round(($size / 1024000), 2) . " MBs";
    }

    function getError()
    {
        return $this->error;
    }
}
