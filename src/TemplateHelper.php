<?php
namespace jobyone\Plaster;

use \Exception;

class TemplateHelper implements Interfaces\TemplateHelper
{
    protected $config;
    protected $stack;
    protected $context;

    public function __construct(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $context,
        \jobyone\Plaster\Interfaces\TransformationLayer $stack
    ) {
        $this->config  = $config;
        $this->stack   = $stack;
        $this->context = $context;
    }

    public function twigFieldsFactory(
        \jobyone\Plaster\Interfaces\Config $config,
        \jobyone\Plaster\Interfaces\Response $context,
        \jobyone\Plaster\Interfaces\TransformationLayer $stack,
        $fields = array()
    ) {
        $fields            = array_replace_recursive($config->get('TemplateManager.fields'), $fields);
        $fields['page']    = new TemplateHelperPage($config, $context);
        $fields['plaster'] = new TemplateHelper(
            $config,
            $context,
            $stack,
            $fields
        );
        return $fields;
    }

    public function page($url = false)
    {
        $url = $this->parseUrl($url);
        //try/catch is here because this is template code
        //it should try as hard as possible to not explode
        try {
            $response = new Response($url);
            $response = $this->stack->transform($response);
        } catch (\Exception $ex) {
            return false;
        }
        if ($response && $response->getStatus() == 200) {
            return new TemplateHelperPage($this->config, $response);
        }
        return false;
    }

    public function parent($url = false)
    {
        $url       = $this->parseUrl($url);
        $page      = false;
        $parentUrl = $url;
        do {
            $parentUrl = preg_replace('/^(.*\/)[^\/]+/', '$1', $parentUrl);
            //if parent url is the same, parent was called on the home page
            if ($parentUrl == $url) {
                return false;
            }
            //if we've reached the root, return
            //if that returns an error there is a problem, but it's not this
            //function's problem
            if ($parentUrl == '/') {
                return $this->page($parentUrl);
            }
            $page = $this->page($parentUrl);
        } while (!$page);
        return $page;
    }

    public function breadcrumb($url = false)
    {
        $url = $this->parseUrl($url);
        if ($url == '/') {
            return array();
        }
        $breadCrumb = array();
        $parent     = $this->parent($url);
        while ($parent) {
            $breadCrumb[] = $parent;
            $parent       = $this->parent($parent->url());
        }
        return array_reverse($breadCrumb);
    }

    public function children($url = false, $sort = 'meta.title', $order = 'asc')
    {
        $url = $this->parseUrl($url);
        $dir = realpath($this->config->get('FileLayer.source') . '/' . $url);
        if (!is_dir($dir)) {
            return false;
        }
        $pages = array();
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if ($page = $this->page($url . $file)) {
                if ($page->url() != $this->context->getUrl()) {
                    $pages[] = $page;
                }
            }
        }
        return $pages;
    }

    public function siblings($url = false, $sort = 'meta.title', $order = 'asc')
    {
        $parent = $this->parent($url);
        if (!$parent) {
            return false;
        }
        return $this->children($parent->url());
    }

    public function sort($pages, $sort = 'meta.title', $order = 'asc')
    {
        if (!$url) {
            $url = $this->context->getUrl();
        }
        throw new Exception('Not implemented');
    }

    protected function parseUrl($url)
    {
        if (!$url) {
            $url = $this->context->getUrl();
        }
        if (!preg_match('/^\//', $url)) {
            $url = $this->context->getUrl() . '/' . $url;
        }
        return $url;
    }
}
