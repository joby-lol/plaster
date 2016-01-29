<?php
namespace jobyone\Plaster\Interfaces;

use jobyone\Plaster\Interfaces\TransformationLayer;

interface TemplateHelper
{

    function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $context,
        \jobyone\Plaster\Interfaces\TransformationLayer $stack
    );

    function page(
        $url = false
    );

    function parent(
        $url = false
    );

    function children(
        $url = false,
        $sort = 'meta.title',
        $order = 'asc'
    );

    function siblings(
        $url = false,
        $sort = 'meta.title',
        $order = 'asc'
    );

    function sort(
        $pages, $sort = 'meta.title', $order = 'asc'
    );

}
