<?php 
namespace jobyone\Plaster;

use jobyone\Plaster\Config;
use jobyone\Plaster\FileLayer;
use jobyone\Plaster\ContentLayer;
use jobyone\Plaster\TemplateLayer;
use jobyone\Plaster\TransformationStack;
use jobyone\Plaster\TemplateManager;

use \Exception;

class PlasterApplication 
{
    protected $config;
    
    protected $fileLayer;
    protected $contentLayer;
    protected $templateLayer;
    
    protected $contentStack;
    protected $fullStack;
    
    function __construct($configFiles = array())
    {
        $this->config = new Config($configFiles);
        
        //set up transformation layers
        $this->fileLayer = new FileLayer($this->config);
        $this->contentLayer = new ContentLayer($this->config);
        $this->templateLayer = new TemplateLayer($this->config);
        
        //set up content stack
        $this->contentStack = new TransformationStack($this->config);
        $this->contentStack->addLayer('file', $this->fileLayer);
        $this->contentStack->addLayer('content', $this->contentLayer);
        
        //set up the full stack
        $this->fullStack = new TransformationStack($this->config);
        $this->fullStack->addLayer('content', $this->contentStack);
        $this->fullStack->addLayer('template', $this->templateLayer, function($request) {
            $meta = $request->getMeta();
            if (isset($meta['skipTemplate']) && $meta['skipTemplate']) {
                return false;
            }
            return true;
        });
        
        //set up the Template Manager
        //it is passed the content stack to use for its helper
        $this->templateManager = new TemplateManager($this->config, $this->contentStack);
    }
    
    function render($url = false)
    {
        if (!$url) {
            $url = $_SERVER['PATH_INFO'];
        }
        //set up a new Response and transform it through the full stack
        $response = new Response($url);
        $response = $this->fullStack->transform($response);
        $response->render();
    }
}