<?php
namespace jobyone\Plaster;

use \Exception;

abstract class AbstractLayer implements Interfaces\TransformationLayer
{
    protected $config;
    protected $hooksBefore = array();
    protected $hooksAfter  = array();

    abstract public function doTransform(Interfaces\Response $response);

    public function __construct(Interfaces\Config $config)
    {
        $this->useConfig($config);
    }

    public function useConfig(Interfaces\Config $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function transform(Interfaces\Response $response)
    {
        //run before hooks
        foreach ($this->hooksBefore as $hook) {
            throw new Exception('hooks not implemented');
        }
        //do transformation (classes that implement this one define this)
        $response = $this->doTransform($response);
        //run after hooks
        foreach ($this->hooksAfter as $hook) {
            throw new Exception('hooks not implemented');
        }
        //return the altered response
        return $response;
    }

    public function error(Interfaces\Response $response, $code, $message = false)
    {
        $handler = $this->getConfig()->get('System.errorHandler');
        $handler = new $handler($this->getConfig());
        return $handler->transformError($response, $code, $message);
    }

    public function hookBefore(Interfaces\TransformationHook $hook)
    {
        return array_push($this->hooksBefore, $hook);
    }

    public function hookAfter(Interfaces\TransformationHook $hook)
    {
        return array_push($this->hooksAfter, $hook);
    }
}
