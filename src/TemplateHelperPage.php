<?php 
namespace jobyone\Plaster;

class TemplateHelperPage implements Interfaces\TemplateHelperPage
{
    protected $response;
    
    function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $response
    )
    {
        $this->config = $config;
        $this->response = $response;
    }
    
    function url($absolute = false)
    {
        $url = $this->response->getUrl();
        if ($absolute && $this->config->get('TemplateManager.baseUrl')) {
            $url = $this->config->get('TemplateManager.baseUrl') . $url;
        }
        return $url;
    }
    
    function content()
    {
        return $this->response->getContent();
    }
    
    function meta()
    {
        return $this->response->getMeta();
    }
}