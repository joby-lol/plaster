<?php 
namespace jobyone\Plaster;

use \Exception;

class FileLayer extends AbstractLayer implements Interfaces\TransformationLayer
{
    function doTransform(Interfaces\Response $response)
    {
        $source = $this->config->get('FileLayer.source');
        $file = $source . $response->getUrl();
        
        //what file names may be indexes is declared in FileLayer.indexes
        if (is_dir($file)) {
            $file = $this->locateIndex($file);
        }
        
        //strip index filenames from URL, so that Response contains canonical URL
        $url = $response->getUrl();
        foreach ($this->config->get('FileLayer.indexes') as $index) {
            $url = preg_replace('/\/' . preg_quote($index) . '$/i', '', $url);
        }
        $response->setUrl($url);
        
        //rewriting allows regular expressions to be used to rewrite
        //one file to another -- for example to serve files with the 
        //extension md as if it were html
        if (!is_file($file) && $this->config->get('FileLayer.rewrite')) {
            $file = $this->rewrite($file);
        }
        
        //TODO: Check for dangerous paths
        $file = realpath($file);
        if (!$file || !file_exists($file)) {
            return $this->error($response, 404);
        }
        
        //return the altered response
        $response->setFile($file);
        return $response;
    }
    
    protected function locateIndex($path)
    {
        $indexFile = $path;
        foreach ($this->config->get('FileLayer.indexes') as $index) {
            $indexFile = $path . '/' . $index;
            if (file_exists($indexFile) && is_readable($indexFile)) {
                return $indexFile;
            }
        }
        return false;
    }
    
    protected function rewrite($file)
    {
        foreach ($this->config->get('FileLayer.rewrite') as $pattern => $replacement) {
            if (preg_match($pattern, $file)) {
                $alternate = preg_replace($pattern, $replacement, $file);
                if (file_exists($alternate)) {
                    return $alternate;
                }
            }
        }
        return $file;
    }
}