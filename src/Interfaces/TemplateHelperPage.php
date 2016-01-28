<?php 
namespace jobyone\Plaster\Interfaces;

interface TemplateHelperPage 
{
    
    function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $response
    );
    
    function url($relative = false);
    
    function content();
    
    function meta();
    
}