<?php
namespace jobyone\Plaster;

use Symfony\Component\Yaml\Yaml;
use \Exception;

class Config implements Interfaces\Config
{
    protected $configRaw = array(
        'System' => array(
            'debug' => false,
        ),
    );
    protected $config = array();

    public function __construct($config)
    {
        $this->loadFile(__DIR__ . '/Config-defaults.yaml');
        foreach ($config as $file) {
            $this->loadFile($file);
        }
    }

    public function loadFile($file)
    {
        if (!is_file($file)) {
            throw new Exception("Config '$file' not found");
        }
        if (!is_readable($file)) {
            throw new Exception("Config '$file' is not readable");
        }
        $newConfig = Yaml::parse(file_get_contents($file));
        $this->set($newConfig);
    }

    public function set($config)
    {
        if ($config) {
            $this->configRaw = array_replace_recursive($this->configRaw, $config);
        }
        // process config for variables, paths, etc
        $this->config = $this->configRaw;
        // System.root: 'auto' will be converted to cwd
        if ($this->configRaw['System']['root'] == 'auto') {
            $this->config['System']['root'] = getcwd();
        }
        // System.domain: 'auto' will be converted to current domain
        if ($this->configRaw['System']['domain'] == 'auto') {
            $this->config['System']['domain'] = $_SERVER['SERVER_NAME'];
        }
        // System.docRoot: 'auto' will be the url of the cwd
        if ($this->configRaw['System']['docRoot'] == 'auto') {
            $this->config['System']['docRoot'] = dirname($_SERVER['SCRIPT_NAME']);
        }
        // parse variables
        $this->config = $this->parseVariables($this->config);
        // set timezone
        date_default_timezone_set($this->get('System.timezone'));
    }

    public function get($key = false)
    {
        if (!$key) {
            return $this->config;
        }
        //dig down into config with dots as delimiters
        $value = $this->config;
        $key   = explode('.', $key);
        foreach ($key as $step) {
            if (!isset($value[$step])) {
                return false;
            }
            $value = $value[$step];
        }
        return $value;
    }

    protected function parseVariables($config)
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $config[$key] = $this->parseVariables($value);
            } elseif ($this->valueIsString($value)) {
                $obj          = $this;
                $config[$key] = preg_replace_callback(
                    '/\{\{ ?([^\{\}]+) ?\}\}/',
                    function ($matches) use ($obj) {
                        return $this->get($matches[1]);
                    },
                    $value);
            } else {
                $config[$key] = $value;
            }
        }
        return $config;
    }

    protected function valueIsString($value)
    {
        if (!is_string($value)) {
            return false;
        }
        return true;
    }

}
