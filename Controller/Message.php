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
                    $id=explode("youtu.be/",$this->msg)[1];
                } else if (str_contains($this->msg,$urlY1))  {
                    $id=explode("watch?v=",$this->msg)[1];
                }
               
                $this->msg= str_replace("https://www.","",$this->msg);
                $this->msg= str_replace("youtu.be","",$this->msg);
                $this->msg= str_replace("youtube.com/","",$this->msg);
                $this->msg= str_replace("watch?v=","",$this->msg);
                $this->msg= str_replace($id,"<a href='https://youtu.be/".$id."' target=\"_blank\" >youtu.be/".$id."</a>",$this->msg);

                $link = "<a href=\"https://youtu.be/".$id."\" target=\"_blank\"><img width=\"100%\" src=\"https://img.youtube.com/vi/".$id."/0.jpg\"/></a>";

                $this->msg.=$link;
            }            
        }
        public function __toString():string {
            return $this->msg;   
        }
    }

?>    