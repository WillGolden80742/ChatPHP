<?php
class Message
{
    private static $countLinks;
    private $msg;
    function __construct($msg)
    {
        $this->msg = htmlspecialchars($msg, ENT_QUOTES);
        $this->msg = $this->links($this->msg);
    }

    function setSession($key, $value)
    {
        $key = "a" . $key;
        $_SESSION[$key] = $value;
    }

    function getSession($key)
    {
        $key = "a" . $key;
        if (empty($_SESSION[$key])) {
            return "";
        } else {
            return $_SESSION[$key];
        }
    }

    function links($msg)
    {
        if ($this->isYoutube($msg)) {
            $msg = $this->youtube($msg);
            $msgArray = explode("<style id=\"embed\">", $msg);
            return "<style id=\"embed\">" . $msgArray[1] . $this->link($msgArray[0]);
        } else if ($this->isSpotify($msg)) {
            return $this->spotify($msg);
        } else {
            return $this->link($this->msg);
        }
    }

    function link($text)
    {

        $urlY1 = "https://";
        $urlY2 = "http://";
        $urlY3 = "www.";

        if (str_contains($text, $urlY1) || str_contains($text, $urlY2) || str_contains($text, $urlY3)) {

            if (str_contains($text, $urlY1)) {
                $id = explode($urlY1, $text)[1];
                $id = $this->splitLink($id);
                $id = $urlY1 . $id;
            } else if (str_contains($text, $urlY2)) {
                $id = explode($urlY2, $text)[1];
                $id = $this->splitLink($id);
                $id = $urlY2 . $id;
            } else if (str_contains($text, $urlY3)) {
                $id = explode($urlY3, $text)[1];
                $id = $this->splitLink($id);
                $id = $urlY3 . $id;
            }
            self::$countLinks++;
            $linkId = self::$countLinks . uniqid();
            $text = str_replace($id, "<a class='linkMsg' id='$linkId' href='" . $this->href($id) . "' target=\"_blank\">" . $this->href($id) . "</a>", $text);
        }

        return $text;
    }
    function spotify($text)
    {
        // Remove "https://" ou "http://"
        $text = preg_replace('/^(https?:\/\/)/i', '', $text);
    
        // Remove parâmetros irrelevantes, como "?si=..."
        $text = preg_replace('/\?.*$/i', '', $text);
    
        $pattern = '/open\.spotify\.com\/([^\/]+)\/track\/([^?]+)/i';
        $replacement = '<iframe style="border-radius: 12px" src="https://open.spotify.com/embed/track/$2" width="100%" height="250" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
    
        $embeddedText = preg_replace($pattern, $replacement, $text);
    
        return $embeddedText;
    }
    
    function youtube($text)
    {

        $urlY1 = "youtube.com/";
        $urlY2 = "youtu.be/";

        $text = str_replace("&feature=youtu.be", "", $text);

        if (str_contains($text, "&")) {
            $text  = "https://www.youtube.com/watch?" . $this->splitLink(explode("&", $text)[1]);
        }

        if (str_contains($text, $urlY1)) {
            $id = $text;
            if (str_contains($text, "watch?v=")) {
                $id = explode("watch?v=", $text)[1];
            }
            $id = $this->splitLink($id);
        } else if (str_contains($text, $urlY2)) {
            $id = explode("youtu.be/", $text)[1];
            $id = $this->splitLink($id);
        }

        $text .= "<style id=\"embed\"> .thumb-video #$id { background-image:url(\"https://img.youtube.com/vi/" . $id . "/0.jpg\"); } </style>";
        $link = "<div class='thumb-video' id=\"thumb-video$id\"><div class=\"center\"><a><img  id=\"$id\" onClick=\"embedYoutube('$id')\" height=100% src=\"Images/play.svg\"/></a></div></div>";

        $text .= $link;

        return $text;
    }

    function splitLink($link)
    {
        $link = explode("\n", $link)[0];
        $link = explode(" ", $link)[0];
        return  $link;
    }

    function href($link)
    {
        $link = str_replace("https://", "", $link);
        $link = str_replace("http://", "", $link);
        return "https://" . $link;
    }

    function isYoutube($text)
    {
        $urlY1 = "youtube.com/";
        $urlY2 = "youtu.be/";
        if (str_contains($text, $urlY1) || str_contains($text, $urlY2)) {
            return true;
        } else {
            return false;
        }
    }

    function isSpotify($text)
    {
        $pattern = '/open\.spotify\.com\/[^\/]+\/track\//i';
        if (preg_match($pattern, $text)) {
            return true;
        } else {
            return false;
        }
    }


    public function __toString(): string
    {
        return $this->msg;
    }
}
