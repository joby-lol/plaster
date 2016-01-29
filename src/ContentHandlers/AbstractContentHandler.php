<?php
namespace jobyone\Plaster\ContentHandlers;

use jobyone\Plaster\Interfaces\Config;
use jobyone\Plaster\Interfaces\ContentHandler;
use jobyone\Plaster\Interfaces\Response;

abstract class AbstractContentHandler implements ContentHandler
{
    abstract public function transform(Response $response);

    public function __construct(Config $config)
    {
        $this->useConfig($config);
    }

    public function useConfig(Config $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
