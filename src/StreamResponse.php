<?php
namespace jobyone\Plaster;

class StreamResponse extends Response implements Interfaces\Response
{
    protected $srcFile = null;

    public function __construct($url, $file = null, $content = null, $headers = array(), $meta = array())
    {
        parent::__construct($url, $file, $content, $headers, $meta);
        $this->setContent(md5($file . filemtime($file)));
        $this->srcFile = $file;
    }

    public function renderContent()
    {
        readfile($this->srcFile);
    }

}
