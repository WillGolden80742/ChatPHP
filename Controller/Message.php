<?php

    class Message {
        private $msg;
        function __construct($msg) {
            $this->msg = preg_replace('[\']','',$msg);
            $this->msg = preg_replace('[\--]','',$this->msg);
            $this->msg = str_replace("<", "&lt;",$this->msg);
            $this->msg = str_replace(">", "&gt;",$this->msg);
            $this->msg = str_replace("\"", "&quot;",$this->msg);
            $msg = $this->msg;
            if ($this->isYoutube ($msg)) {
                $msg = $this->youtube ($msg);
                $msgArray = explode("<style id=\"embed\">",$msg); 
                $this->msg = $this->link ($msgArray[0])."<style id=\"embed\">".$msgArray[1];
            } else {
                $this->msg = $this->link ($this->msg);
            }
        }


        function link ($text) {

            $urlY1 = "https://";
            $urlY2 = "http://";

            if (str_contains($text,$urlY1) || str_contains($text,$urlY2)) {

                if (str_contains($text,$urlY2)) {
                    $id = explode("http://",$text)[1]; 
                    $id = explode("\n",$id)[0];
                    $id = explode(" ",$id)[0];
                    $id = "http://".$id;
                } else if (str_contains($text,$urlY1))  {
                    $id = explode("https://",$text)[1];
                    $id = explode("\n",$id)[0];
                    $id = explode(" ",$id)[0];
                    $id = "https://".$id;
                }

                $text = str_replace($id,"<a href='$id' target=\"_blank\">$id</a>",$text);

            } 
            return $text;
        }

        function youtube ($text) {

            $urlY1 = "youtube.com/";
            $urlY2 = "youtu.be/";

            $text = str_replace("&feature=youtu.be", "",$text);

            if (str_contains($text,$urlY2)) {
                $id = explode("youtu.be/",$text)[1]; 
                $id = explode("\n",$id)[0];
                $id = explode(" ",$id)[0];
            } else if (str_contains($text,$urlY1))  {
                $id=explode("watch?v=",$text)[1];
                $id = explode("\n",$id)[0];
                $id = explode(" ",$id)[0];
            }

            $text.="<style id=\"embed\"> .thumb-video #$id { background-image:url(\"https://img.youtube.com/vi/".$id."/0.jpg\"); } </style>";
            $link="<div class='thumb-video' id=\"thumb-video$id\"><center><a href=\"https://youtu.be/".$id."\" target=\"_blank\"><img  id=\"$id\" height=100% src=\"Images/play.svg\"/></a></center></div>";

            $text.=$link;
            
            return $text;
        }

        function isYoutube ($text) {
            $urlY1 = "youtube.com/";
            $urlY2 = "youtu.be/";
            if (str_contains($text,$urlY1) || str_contains($text,$urlY2)) {
                return true; 
            } else {
                return false;
            }
        }        

        public function __toString():string {
            return $this->msg;   
        }
    }

?>    