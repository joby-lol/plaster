<?php 
namespace jobyone\Plaster\Interfaces;

interface TransformationStack extends TransformationLayer 
{
    
    function addLayer(
        $name,
        \jobyone\Plaster\Interfaces\TransformationLayer $layer,
        $test = null
    );
    
}