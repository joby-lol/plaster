<?php
namespace jobyone\Plaster;

class FileLayer extends AbstractLayer implements Interfaces\TransformationLayer
{
    public function doTransform(Interfaces\Response $response)
    {
        $source = $this->config->get('FileLayer.source');
        $file   = $source . $response->getUrl();

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

}
