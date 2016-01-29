<?php
namespace jobyone\Plaster\Interfaces;

interface Response
{

    function __construct(
        $url,
        $file = null,
        $content = null,
        $headers = array(),
        $meta = array(),
        $status = 200
    );

    function getUrl();
    function setUrl($url);

    function getFile();
    function setFile($file);

    function getContent();
    function setContent($content);

    function getHeaders();
    function setHeaders($headers);

    function getMeta();
    function setMeta($meta);

    function getStatus();
    function setStatus($status);

    function render();
    function renderContent();
    function renderHeaders();
    function dump();

}
