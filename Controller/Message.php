<?php

    use Goutte\Client;
    use Symfony\Component\HttpClient\HttpClient;
    class Message {

        private $msg;
        function __construct($msg,$async) {
            $this->msg = preg_replace('[\']','',$msg);
            $this->msg = preg_replace('[\--]','',$this->msg);
            $this->msg = str_replace("<", "&lt;",$this->msg);
            $this->msg = str_replace(">", "&gt;",$this->msg);
            $this->msg = str_replace("\"", "&quot;",$this->msg);

            $msg = $this->msg;
            if ($this->isYoutube ($msg)) {
                $msg = $this->youtube ($msg);
                $msgArray = explode("<style id=\"embed\">",$msg); 
                $this->msg = $this->link ($msgArray[0],$async)."<style id=\"embed\">".$msgArray[1];
            } else {
                $this->msg = $this->link ($this->msg,$async);
            }
        }

        
        function get_title($url){
            return "";
        }
        

        function link ($text,$async) {

            $urlY1 = "https://";
            $urlY2 = "http://";
            $urlY3 = "www.";

            if ( str_contains($text,$urlY1) || str_contains($text,$urlY2) || str_contains($text,$urlY3) ) {
               
                if (str_contains($text,$urlY1))  {
                    $id = explode($urlY1,$text)[1];
                    $id = $this->splitLink($id);
                    $id = $urlY1.$id;
                } else if (str_contains($text,$urlY2)) {
                    $id = explode($urlY2,$text)[1]; 
                    $id = $this->splitLink($id);
                    $id = $urlY2.$id;
                } else if (str_contains($text,$urlY3)) {
                    $id = explode($urlY3,$text)[1]; 
                    $id = $this->splitLink($id);
                    $id = $urlY3.$id;
                } 

                $linkId = microtime(true).random_int(0, 999);
                if ($async) {
                    #$text = str_replace($id,"<a class='linkMsg' id='$linkId' href='".$this->href($id)."' target=\"_blank\">".$this->get_title($this->href($id))."<span style='opacity:0.5;'>".$this->href($id)."</span></a>",$text);
                } else {
                    $text = str_replace($id,"<a class='linkMsg' id='$linkId' href='".$this->href($id)."' target=\"_blank\">".$this->href($id)."</a>",$text)."<script> link (); function link (){ arrayLink = document.getElementById('$linkId'); link = arrayLink.innerHTML; $.ajax({ url: 'getTitle.php', method: 'GET', data: {link: link}, dataType: 'json' }).done(function(result) { link = document.getElementById('$linkId').innerHTML; document.getElementById('$linkId').innerHTML = result+\"<span style='opacity:0.5;'>\"+link+\"</span>\" }); }</script>";
                }  
            }
            
            return $text;
        } 

        function youtube ($text) {

            $urlY1 = "youtube.com/";
            $urlY2 = "youtu.be/";
           
            $text = str_replace("&feature=youtu.be","",$text);
             
            if (str_contains($text,"&")) {
                $text  = "https://www.youtube.com/watch?".$this->splitLink(explode("&",$text)[1]);
            } 

            if (str_contains($text,$urlY1))  {
                $id = $text;
                if (str_contains($text,"watch?v=")) {
                    $id = explode("watch?v=",$text)[1];
                }    
                $id = $this->splitLink($id);
            } else if (str_contains($text,$urlY2)) {
                $id = explode("youtu.be/",$text)[1]; 
                $id = $this->splitLink($id);
            } 

            $text.="<style id=\"embed\"> .thumb-video #$id { background-image:url(\"https://img.youtube.com/vi/".$id."/0.jpg\"); } </style>";
            $link="<div class='thumb-video' id=\"thumb-video$id\"><center><a href=\"https://youtu.be/".$id."\" target=\"_blank\"><img  id=\"$id\" height=100% src=\"Images/play.svg\"/></a></center></div>";

            $text.=$link;
            
            return $text;
        } 

        function splitLink ($link) {
            $link = explode("\n",$link)[0];    
            $link = explode(" ",$link)[0];    
            return  $link;
        }

        function href ($link) {
            $link = str_replace("https://", "",$link);
            $link = str_replace("http://", "",$link);
            return "https://".$link;
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