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
        // Define a expressão regular para encontrar URLs
        $pattern = '/(https?:\/\/(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\/\S*)?)/';

        // Substitui todas as URLs no texto por links HTML
        $text = preg_replace_callback($pattern, function ($matches) {
            $url = $matches[0];
            self::$countLinks++;
            $linkId = self::$countLinks . uniqid();
            return "<a class='linkMsg' id='$linkId' href='$url' target='_blank'>$url</a>";
        }, $text);

        return $text;
    }

    function spotify($text) {
        // Remova "https://" ou "http://"
        $text = preg_replace('/^(https?:\/\/)/i', '', $text);
    
        // Remova parâmetros irrelevantes, como "?si=..."
        $text = preg_replace('/\?.*$/i', '', $text);
    
        // Verifique ambos os estilos de URL
        $pattern1 = '/open\.spotify\.com\/([^\/]+)\/track\/([^?]+)/i';
        $pattern2 = '/open\.spotify\.com\/track\/([^?]+)/i';
    
        // Verifique se o padrão 1 ou o padrão 2 corresponde à URL
        if (preg_match($pattern1, $text, $matches) || preg_match($pattern2, $text, $matches)) {
            // Se corresponder a qualquer um dos padrões, use o grupo correspondente
            $track_id = isset($matches[2]) ? $matches[2] : $matches[1];
    
            // Construa a URL desejada
            $embeddedText = '<iframe class="spotify_embed" src="https://open.spotify.com/embed/track/' . $track_id . '?utm_source=generator" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
    
            return $embeddedText;
        }
    
        // Se a URL não corresponder a nenhum dos padrões, retorne o texto original
        return $text;
    }
    

    function youtube($text)
    {
        $urlY1 = "youtube.com/";
        $urlY2 = "youtu.be/";
    
        // Remove "&feature=youtu.be"
        $text = str_replace("&feature=youtu.be", "", $text);
    
        // Separate "&t=" if present
        $timeParameter = "";
        if (str_contains($text, "&t=")) {
            $splitText = explode("&t=", $text);
            $text = $splitText[0];
            $timeParameter = "&t=" . $splitText[1];
        }
    
        if (str_contains($text, "&")) {
            $text = "https://www.youtube.com/watch?" . $this->splitLink(explode("&", $text)[0]);
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
    
        $text .= $link . $timeParameter;
    
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

    function isSpotify($text) {
        $pattern1 = '/open\.spotify\.com\/[^\/]+\/track\//i';
        $pattern2 = '/open\.spotify\.com\/track\//i';
    
        if (preg_match($pattern1, $text) || preg_match($pattern2, $text)) {
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
