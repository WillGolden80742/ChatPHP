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
        if ($this->isYoutubeEmbed($msg)) {
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
        $pattern = '/(?:https?:\/\/(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\/\S*)?|www\.[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}(?:\/\S*)?)/';
    
        // Substitui todas as URLs no texto por links HTML
        $text = preg_replace_callback($pattern, function ($matches) {
            $url = $matches[0];
    
            // Adiciona "https://" se não houver prefixo
            if (!preg_match('/^https?:\/\//', $url)) {
                $url = 'https://' . $url;
            }
    
            self::$countLinks++;
            $linkId = self::$countLinks . uniqid();
            return "<a class='linkMsg' id='$linkId' href='$url' target='_blank'>$url</a>";
        }, $text);
    
        return $text;
    }
    

    function spotify($text)
    {
        // Remova "https://" ou "http://"
        $text = preg_replace('/^(https?:\/\/)/i', '', $text);
    
        // Remova parâmetros irrelevantes, como "?si=..."
        $text = preg_replace('/\?.*$/i', '', $text);
    
        // Verifique todos os estilos de URL
        $pattern1 = '/open\.spotify\.com\/([^\/]+)\/track\/([^?]+)/i';
        $pattern2 = '/open\.spotify\.com\/track\/([^?]+)/i';
        $pattern3 = '/open\.spotify\.com\/playlist\/([^?]+)/i';
    
        // Verifique se o padrão 1, padrão 2 ou padrão 3 corresponde à URL
        if (preg_match($pattern1, $text, $matches) || preg_match($pattern2, $text, $matches) || preg_match($pattern3, $text, $matches)) {
            // Se corresponder a qualquer um dos padrões, use o grupo correspondente
            $id = isset($matches[3]) ? $matches[3] : (isset($matches[2]) ? $matches[2] : $matches[1]);
    
            // Construa a URL desejada
            $embeddedText = '<iframe class="spotify_embed" src="https://open.spotify.com/embed/' . (strpos($text, 'playlist') !== false ? 'playlist' : 'track') . '/' . $id . '?utm_source=generator" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
    
            return $embeddedText;
        }
    
        // Se a URL não corresponder a nenhum dos padrões, retorne o texto original
        return $text;
    }
    


    function youtube($text)
    {
        $urlY1 = "youtube.com/";
        $urlY2 = "youtu.be/";
    
        // Remove "&feature=youtu.be" and "?si=..."
        $text = preg_replace("/[&?]feature=youtu\.be|si=.*/", "", $text);
    
        // Separate "&t=" if present
        $timeParameter = "";
        if (str_contains($text, "&t=")) {
            $splitText = explode("&t=", $text);
            $text = $splitText[0];
            $timeParameter = "&t=" . $splitText[1];
        }
    
        // Extract video ID from the link
        $id = "";
        if (str_contains($text, "/live/")) {
            // Handle live link
            $id = explode("/live/", $text)[1];
        } elseif (str_contains($text, "/shorts/") || str_contains($text, "/shorts?")) {
            // Handle shorts link
            $id = explode("/shorts/", $text)[1];
            // Handle the case when the link has a query parameter after /shorts
            if (str_contains($id, "?")) {
                $id = explode("?", $id)[0];
            }
        } elseif (str_contains($text, "&")) {
            // Handle regular YouTube link with parameters
            $text = "https://www.youtube.com/watch?" . parse_url($text, PHP_URL_QUERY);
            $id = explode("v=", parse_url($text, PHP_URL_QUERY))[1];
        } elseif (str_contains($text, $urlY1)) {
            // Handle regular YouTube link without parameters
            $id = $text;
            if (str_contains($text, "watch?v=")) {
                $id = explode("watch?v=", $text)[1];
            }
        } elseif (str_contains($text, $urlY2)) {
            // Handle youtu.be link
            $id = explode("youtu.be/", $text)[1];
        }
    
        $id = str_replace("?", "", $id);
        $id = str_replace(["\r", "\n"], "", $id);
        $id = explode("&", $id)[0];
    
        // Generate thumbnail and embed links
        $thumbLink = "https://img.youtube.com/vi/" . $id . "/0.jpg";
    
        $text .= "<style id=\"embed\"> .thumb-video #$id { background-image:url(\"$thumbLink\"); } </style>";
        $link = "<div class='thumb-video' id=\"thumb-video$id\"><div class=\"center\"><a><img  id=\"$id\" onClick=\"embedYoutube('$id')\" height=100% src=\"Images/play.svg\"/></a></div></div>";
    
        // Reconcatenate "&t=" parameter
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

    function isYoutubeEmbed($text)
    {
        $urlY1 = "youtube.com/";
        $urlY2 = "youtu.be/";
    
        // Check for the presence of YouTube URLs
        if (str_contains($text, $urlY1) || str_contains($text, $urlY2)) {
    
            // Check for specific cases that should not be treated as YouTube embeddable
            $nonEmbeddablePatterns = [
                "/youtube\.com\/(channel|@)/",  // Exclude channel and @username
                "/^youtube\.com/", // Exclude base URL with studio.youtube.com
                "/youtu\.be\/$/",                // Exclude just the base URL for youtu.be
                "/youtube\.com\/$/",                // Exclude just the base URL for youtu.be
                "/\/studio\.youtube\.com\//",   // Exclude studio.youtube.com in the path
            ];
    
            foreach ($nonEmbeddablePatterns as $pattern) {
                if (preg_match($pattern, $text)) {
                    return false;
                }
            }
    
            // If none of the exclusion patterns match, consider it as YouTube embeddable
            return true;
        } else {
            return false;
        }
    }


    function isSpotify($text)
    {
        $pattern1 = '/open\.spotify\.com\/[^\/]+\/track\//i';
        $pattern2 = '/open\.spotify\.com\/track\//i';
        $pattern3 = '/open\.spotify\.com\/playlist\//i';
    
        if (preg_match($pattern1, $text) || preg_match($pattern2, $text) || preg_match($pattern3, $text)) {
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
