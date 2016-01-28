<?php 
namespace jobyone\Plaster\ContentHandlers;

use jobyone\Plaster\Interfaces\ContentHandler;
use jobyone\Plaster\Interfaces\Response;
use jobyone\Plaster\Interfaces\Config;

abstract class AbstractContentHandler implements ContentHandler
{
    abstract function transform(Response $response);
    
    function __construct(Config $config)
    {
        $this->useConfig($config);
    }
    
    function useConfig(Config $config)
    {
        $this->config = $config;
    }
    
    function getConfig()
    {
        return $this->config;
    }
}