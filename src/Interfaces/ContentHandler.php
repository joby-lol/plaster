<?php
namespace jobyone\Plaster\Interfaces;

interface ContentHandler
{

    function __construct(Config $config);

    function useConfig(Config $config);

    function getConfig();

    function transform(Response $response);

}
