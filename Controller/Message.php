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
        } else if ($this->isDeezer($msg)) {
            return $this->deezer($msg);
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
        // Remove "https://" or "http://"
        $text = preg_replace('/^(https?:\/\/)/i', '', $text);
    
        // Remove irrelevant parameters, such as "?si=..."
        $text = preg_replace('/\?.*$/i', '', $text);
    
        // Define patterns for different Spotify URLs
        $patterns = [
            '/open\.spotify\.com\/[^\/]+\/track\//i' => 'track',
            '/open\.spotify\.com\/track\//i' => 'track',
            '/open\.spotify\.com\/playlist\//i' => 'playlist',
            '/open\.spotify\.com\\/[^\/]+\/playlist\//i' => 'playlist',
            '/open\.spotify\.com\/[^\/]+\/album\//i' => 'album',
            '/open\.spotify\.com\/album\//i' => 'album'
        ];
    
        // Check if any pattern matches the URL
        foreach ($patterns as $pattern => $type) {
            if (preg_match($pattern, $text)) {
                // Extract the ID from the URL
                $id = preg_replace($pattern, '', $text);
    
                // Build the desired URL
                $embeddedText = '<iframe class="media_embed" src="https://open.spotify.com/embed/' . $type . '/' . $id . '?utm_source=generator" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>';
    
                return $embeddedText;
            }
        }
    
        // If the URL doesn't match any of the patterns, return the original text
        return $text;
    }
    
    
    function deezer($text)
    {
        // Remove "https://" or "http://"
        $text = preg_replace('/^(https?:\/\/)/i', '', $text);
    
        // Remove irrelevant parameters, such as "?si=..."
        $text = preg_replace('/\?.*$/i', '', $text);
    
        // Define patterns for different Deezer URLs
        $patterns = [
            'track' => '/deezer\.com\/([^\/]+)\/track\/([^?]+)/i',
            'artist' => '/deezer\.com\/([^\/]+)\/artist\/([^?]+)/i',
            'album' => '/deezer\.com\/([^\/]+)\/album\/([^?]+)/i',
            'playlist' => '/deezer\.com\/([^\/]+)\/playlist\/([^?]+)/i'
        ];
    
        // Check if any pattern matches the URL
        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $id = isset($matches[2]) ? $matches[2] : $matches[3];
                $widgetType = $type;
    
                // If it's an artist, append '/top_tracks' to the URL
                $urlSuffix = ($widgetType === 'artist') ? '/top_tracks' : '';
    
                // Build the desired URL
                $embeddedText = '<iframe title="deezer-widget" class="media_embed" src="https://widget.deezer.com/widget/dark/' . $widgetType . '/' . $id . $urlSuffix . '" frameborder="0" allowtransparency="true" allow="encrypted-media; clipboard-write"></iframe>';
    
                return $embeddedText;
            }
        }
    
        // If the URL doesn't match any of the patterns, return the original text
        return $text;
    }
    
    

    function youtube($text)
    {
        // Remove "&feature=youtu.be" and "?si=..."
        $text = preg_replace("/[&?]feature=youtu\.be|si=.*/", "", $text);
    
        // Separate "&t=" if present
        $timeParameter = "";
        if (strpos($text, "&t=") !== false) {
            list($text, $timeParameter) = explode("&t=", $text, 2);
            $timeParameter = "&t=" . $timeParameter;
        }
    
        // Extract video ID from the link
        $id = "";
        if (strpos($text, "/live/") !== false) {
            // Handle live link
            $id = explode("/live/", $text)[1];
        } elseif (strpos($text, "/shorts/") !== false || strpos($text, "/shorts?") !== false) {
            // Handle shorts link
            $id = explode("/shorts/", $text)[1];
            // Handle the case when the link has a query parameter after /shorts
            $id = explode("?", $id)[0];
        } elseif (strpos($text, "&") !== false) {
            // Handle regular YouTube link with parameters
            $text = "https://www.youtube.com/watch?" . parse_url($text, PHP_URL_QUERY);
            $id = explode("v=", parse_url($text, PHP_URL_QUERY))[1];
        } elseif (strpos($text, "youtube.com/") !== false) {
            // Handle regular YouTube link without parameters
            $id = $text;
            if (strpos($text, "watch?v=") !== false) {
                $id = explode("watch?v=", $text)[1];
            }
        } elseif (strpos($text, "youtu.be/") !== false) {
            // Handle youtu.be link
            $id = explode("youtu.be/", $text)[1];
        }
    
        $id = str_replace(["?", "\r", "\n"], "", $id);
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
        $patterns = [
            '/open\.spotify\.com\/[^\/]+\/track\//i',
            '/open\.spotify\.com\/track\//i',
            '/open\.spotify\.com\/playlist\//i',
            '/open\.spotify\.com\\/[^\/]+\/playlist\//i',
            '/open\.spotify\.com\/[^\/]+\/album\//i',
            '/open\.spotify\.com\/album\//i',
        ];
    
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
    
        return false;
    }    
    

    function isDeezer($text)
    {
        $patterns = [
            '/deezer\.com\/[^\/]+\/track\//i',
            '/deezer\.com\/[^\/]+\/artist\//i',
            '/deezer\.com\/[^\/]+\/album\//i',
            '/deezer\.com\/[^\/]+\/playlist\//i', // New playlist pattern
        ];
    
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
    
        return false;
    }    
        
    public function __toString(): string
    {
        return $this->msg;
    }
}
