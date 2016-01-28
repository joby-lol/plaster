<?php 
namespace jobyone\Plaster;

class TemplateManager implements Interfaces\TemplateManager
{
    protected $config;
    protected $stack;
    protected $twig;
    
    function __construct(Interfaces\Config $config, \jobyone\Plaster\Interfaces\TransformationLayer $stack)
    {
        $this->useConfig($config);
        $this->stack = $stack;
        //set up Twig
        $loaders = array();
        foreach ($this->config->get('TemplateManager.Twig.paths') as $path) {
            $loaders[] = new \Twig_Loader_Filesystem($path);
        }
        $loader = new \Twig_Loader_Chain($loaders);
        $this->twig = new \Twig_Environment(
            $loader,
            array(
                'cache' => $this->config->get('TemplateManager.Twig.cache')
            )
        );
    }
    
    function useConfig(Interfaces\Config $config)
    {
        $this->config = $config;
        //register self in config, as TemplateManager.current
        $this->config->set(array(
            'TemplateManager' => array(
                'current' => $this
            )
        ));
    }
    
    function render($template, Interfaces\Response $context, $fields = array())
    {
        $fields = $this->buildFields($context, $fields);
        return $this->twig->render($template, $fields);
    }
    
    function renderString($template, Interfaces\Response $context, $fields = array())
    {
        $fields = $this->buildFields($context, $fields);
        $twig = clone $this->twig;
        $twig->setLoader(new \Twig_Loader_String());
        return $twig->render($template, $fields);
    }
    
    function buildFields(Interfaces\Response $context, $fields = array())
    {
        return TemplateHelper::twigFieldsFactory(
            $this->config,
            $context,
            $this->stack,
            $fields
        );
    }
    
}