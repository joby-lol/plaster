<?php
namespace jobyone\Plaster\Interfaces;

interface TransformationLayer
{

    function __construct(
        \jobyone\Plaster\Interfaces\Config $config
    );

    function useConfig(
        \jobyone\Plaster\Interfaces\Config $config
    );

    function getConfig();

    function transform(
        \jobyone\Plaster\Interfaces\Response $response
    );

    function hookBefore(
        \jobyone\Plaster\Interfaces\TransformationHook $hook
    );

    function hookAfter(
        \jobyone\Plaster\Interfaces\TransformationHook $hook
    );

}
