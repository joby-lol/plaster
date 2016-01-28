<?php 
namespace jobyone\Plaster\Interfaces;

interface Config 
{
    
    function __construct($config);
    
    function loadFile($file);
    
    function set($config);
    function get($key = false);
    
}