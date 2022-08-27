<?php
   
    class Message {
        private $msg;
        function __construct($msg) {
            $this->msg = preg_replace('[\']','',$msg);
            $this->msg = preg_replace('[\--]','',$this->msg);
            $this->msg = str_replace("<", "&lt;",$this->msg);
            $this->msg = str_replace(">", "&gt;",$this->msg);
            $this->msg = str_replace("\"", "&quot;",$this->msg);
            $urlY1 = "youtube.com/";
            $urlY2 = "youtu.be/";

            if (str_contains($this->msg,$urlY1) || str_contains($this->msg,$urlY2)) {
                
                if (str_contains($this->msg,$urlY2)) {
                    $url = "https://www.".$urlY1."embed/".explode("youtu.be/",$this->msg)[1];
                } else if (str_contains($this->msg,$urlY1))  {
                    $url = "https://www.".$urlY1."embed/".explode("watch?v=",$this->msg)[1];
                }

                $this->msg = "<iframe width=\"100%\" height=\"300px\" src=\"".$url."\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";   
            }            
        }
        public function __toString():string {
            return $this->msg;   
        }
    }

?>    