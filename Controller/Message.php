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

                $this->msg = str_replace("&feature=youtu.be", "",$this->msg);

                if (str_contains($this->msg,$urlY2)) {
                    $id = explode("youtu.be/",$this->msg)[1]; 
                    $id = explode("\n",$id)[0];
                    $id = explode(" ",$id)[0];
                } else if (str_contains($this->msg,$urlY1))  {
                    $id=explode("watch?v=",$this->msg)[1];
                    $id = explode("\n",$id)[0];
                    $id = explode(" ",$id)[0];
                }

                $this->msg.="<style> .thumb-video #$id { background-image:url(\"https://img.youtube.com/vi/".$id."/0.jpg\"); } </style>";
                $link="<div class='thumb-video' id=\"thumb-video$id\"><center><a href=\"https://youtu.be/".$id."\" target=\"_blank\"><img  id=\"$id\" height=100% src=\"Images/play.svg\"/></a></center></div>";

                $this->msg.=$link;
            }            
        }
        public function __toString():string {
            return $this->msg;   
        }
    }

?>    