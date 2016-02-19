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

    /**
     * shortcut to $this->meta['title']
     * @return string
     */
    public function title()
    {
        $meta = $this->meta();
        return $meta['title'];
    }

    /**
     * shortcut to $this->meta()['date']
     * @return DateTime
     */
    public function date()
    {
        $meta = $this->meta();
        return $meta['date'];
    }

    /**
     * Return a string of an HTML link to this page
     *
     * Behavior is controlled by the following config options:
     * + system.currentUrl
     *   The current URL that we are in the context of. May or may not have a
     *   trailing slash for directories.
     * + TemplateHelperPage.activeClass
     *   The class applied when the page being linked to is the current context
     * + TemplateHelperPage.pathActiveClass
     *   The class applied when the page being linked to is a parent of the current context
     * + TemplateHelperPage.pathActiveOnHome
     *   Links to '/' never have the path active class, unless this is set to true.
     *   Default is false.
     *
     * @param  string $title allows page's title to be overidden
     * @param  string $class    any extra string of classes to add
     * @param  boolean $absolute whether link should be absolute
     * @return string            HTML link
     */
    public function link($title = null, $class = null, $absolute = false)
    {
        $classes = array();
        if ($class) {
            $classes[] = $class;
        }
        $curUrl = $this->config->get('system.currentUrl');
        if ($curUrl) {
            $relUrl = $this->response->getUrl();
            if ($curUrl == $relUrl) {
                $classes[] = $this->config->get('TemplateHelperPage.activeClass');
            }
            if (($this->config->get('TemplateHelperPage.pathActiveOnHome' || $relUrl != '/'))
                && strpos($curUrl, $relUrl) === 0) {
                $classes[] = $this->config->get('TemplateHelperPage.pathActiveClass');
            }
        }
        if (!$title) {
            $title = $this->title();
        }
        return '<a href="' . $this->url($absolute) . '" class="' . implode(' ', $classes) . '">' . $title . '</a>';
    }
}
