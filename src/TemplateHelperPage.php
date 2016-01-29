<?php
namespace jobyone\Plaster;

class TemplateHelperPage implements Interfaces\TemplateHelperPage
{
    protected $response;

    public function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $response
    ) {
        $this->config   = $config;
        $this->response = $response;
    }

    public function url($absolute = false)
    {
        $url = $this->response->getUrl();
        if ($absolute && $this->config->get('TemplateManager.baseUrl')) {
            return $this->config->get('TemplateManager.baseUrl') . $url;
        }
        return $this->config->get('System.docRoot') . $url;
    }

    public function content()
    {
        return $this->response->getContent();
    }

    public function meta()
    {
        return $this->response->getMeta();
    }
}
