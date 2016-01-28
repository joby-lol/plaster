<?php
namespace jobyone\Plaster;

class TransformationStack extends AbstractLayer implements Interfaces\TransformationStack
{
    protected $stack = array();
    protected $config;
    
    function addLayer($name, Interfaces\TransformationLayer $layer, $test = null)
    {
        $this->stack[$name] = array(
            'layer' => $layer,
            'test' => $test
        );
    }
    
    function doTransform(Interfaces\Response $response)
    {
        foreach ($this->stack as $layer) {
            if ($layer['test'] === null || $layer['test']($response)) {
                $response = $layer['layer']->transform($response);
            }
        }
        return $response;
    }
}