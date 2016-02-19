<?php
namespace jobyone\Plaster;

use \Exception;

class Response implements Interfaces\Response
{
    protected $url     = null;
    protected $file    = null;
    protected $content = null;
    protected $headers = array();
    protected $meta    = array();
    protected $status  = 200;

    public function __construct($url, $file = null, $content = null, $headers = array(), $meta = array(), $status = 200)
    {
        $meta['generated'] = new \DateTime();
        $this->setUrl($url);
        $this->setFile($file);
        $this->setContent($content);
        $this->setHeaders($headers);
        $this->setMeta($meta);
        $this->setStatus($status);
    }

    public function getUrl()
    {
        $url = $this->url;
        // var_dump($this->file);
        // var_dump($url);
        $url = preg_replace('/([^\/])\/$/', '$1', $url);
        return $url;
    }

    public function setUrl($url)
    {
        $this->urlMustBeSafe($url);
        $url       = preg_replace('/([^\/])\/$/', '$1', $url);
        $this->url = $url;
    }

    protected function urlMustBeSafe($url)
    {
        if (!preg_match('/^\//', $url)) {
            throw new Exception("Url '$url' is invalid. URLs must begin with a '/'.");
        }
        if (preg_match('/\/\./', $url)) {
            throw new Exception("Url '$url' is invalid. Attempts to traverse directories or access dotfiles.");
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders($headers)
    {
        $this->headers = array_replace_recursive($this->headers, $headers);
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta($meta)
    {
        $this->meta = array_replace_recursive($this->meta, $meta);
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function render()
    {
        $this->renderHeaders();
        $this->renderContent();
    }
    public function renderContent()
    {
        echo $this->getContent();
    }
    public function renderHeaders()
    {
        $headers = $this->getHeaders();
        //shorthands in metadata
        if (isset($this->meta['redirect']) && $this->meta['redirect']) {
            $headers['Location'] = $this->meta['redirect'];
            $this->setStatus(302);
        }
        //render headers
        foreach ($headers as $header => $value) {
            if (!$value) {
                continue;
            }
            http_response_code($this->getStatus());
            if (!is_array($value)) {
                header("$header: $value");
            } else {
                $stringValue = "";
                foreach ($value as $valueKey => $valueValue) {
                    if (!$valueValue) {
                        continue;
                    }
                    if ($valueValue === true) {
                        $stringValue .= "$valueKey; ";
                        continue;
                    }
                    $stringValue .= "$valueKey=$valueValue; ";
                }
                $stringValue = trim($stringValue);
                header("$header: $stringValue");
            }
        }
    }
    public function dump()
    {
        echo "<div>";
        echo "<h1>" . get_class() . "</h1>";
        echo "<h2>Url/File</h2>";
        echo "<pre>" . $this->getUrl() . "\n" . $this->getFile() . "</pre>";
        echo "<h2>Status/Headers</h2>";
        echo "<pre>Status: " . $this->getStatus() . "\n" . $this->dumpArray($this->getHeaders()) . "</pre>";
        echo "<h2>Meta</h2>";
        echo "<pre>" . $this->dumpArray($this->getMeta()) . "</pre>";
        echo "<h2>Content</h2>";
        echo "<pre>" . htmlspecialchars($this->getContent()) . "</pre>";
        echo "</div>";
    }
    protected function dumpArray($array, $level = 0)
    {
        return print_r($array, true);
    }
}

if (!function_exists('http_response_code')) {
    function http_response_code($code = null)
    {

        if ($code !== null) {

            switch ($code) {
                case 100:$text = 'Continue';
                    break;
                case 101:$text = 'Switching Protocols';
                    break;
                case 200:$text = 'OK';
                    break;
                case 201:$text = 'Created';
                    break;
                case 202:$text = 'Accepted';
                    break;
                case 203:$text = 'Non-Authoritative Information';
                    break;
                case 204:$text = 'No Content';
                    break;
                case 205:$text = 'Reset Content';
                    break;
                case 206:$text = 'Partial Content';
                    break;
                case 300:$text = 'Multiple Choices';
                    break;
                case 301:$text = 'Moved Permanently';
                    break;
                case 302:$text = 'Moved Temporarily';
                    break;
                case 303:$text = 'See Other';
                    break;
                case 304:$text = 'Not Modified';
                    break;
                case 305:$text = 'Use Proxy';
                    break;
                case 400:$text = 'Bad Request';
                    break;
                case 401:$text = 'Unauthorized';
                    break;
                case 402:$text = 'Payment Required';
                    break;
                case 403:$text = 'Forbidden';
                    break;
                case 404:$text = 'Not Found';
                    break;
                case 405:$text = 'Method Not Allowed';
                    break;
                case 406:$text = 'Not Acceptable';
                    break;
                case 407:$text = 'Proxy Authentication Required';
                    break;
                case 408:$text = 'Request Time-out';
                    break;
                case 409:$text = 'Conflict';
                    break;
                case 410:$text = 'Gone';
                    break;
                case 411:$text = 'Length Required';
                    break;
                case 412:$text = 'Precondition Failed';
                    break;
                case 413:$text = 'Request Entity Too Large';
                    break;
                case 414:$text = 'Request-URI Too Large';
                    break;
                case 415:$text = 'Unsupported Media Type';
                    break;
                case 500:$text = 'Internal Server Error';
                    break;
                case 501:$text = 'Not Implemented';
                    break;
                case 502:$text = 'Bad Gateway';
                    break;
                case 503:$text = 'Service Unavailable';
                    break;
                case 504:$text = 'Gateway Time-out';
                    break;
                case 505:$text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}
