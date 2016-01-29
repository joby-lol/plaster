<?php
namespace jobyone\Plaster\Interfaces;

interface TemplateManager
{

    function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\TransformationLayer $stack
    );

    function useConfig(
        \jobyone\Plaster\Interfaces\Config $config
    );

    function buildFields(
        \jobyone\Plaster\Interfaces\Response $context, $fields = array()
    );

    function render(
        $template,
        \jobyone\Plaster\Interfaces\Response $context,
        $fields = array()
    );

    function renderString(
        $template,
        \jobyone\Plaster\Interfaces\Response $context,
        $fields = array()
    );

}
