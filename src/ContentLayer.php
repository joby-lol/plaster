<?php
namespace jobyone\Plaster;

use Symfony\Component\Yaml\Yaml;

/*
TODO: Meta/Header processing makes this file pretty big and complex
I might want to split that off into another layer, or into
TransformationHooks attached to this layer
 */
class ContentLayer extends AbstractLayer implements Interfaces\TransformationLayer
{
    public function doTransform(Interfaces\Response $response)
    {
        //load content from file
        $content = $response->getContent();
        if (is_file($response->getFile())) {
            $content = file_get_contents($response->getFile());
        }

        //set up default meta and headers
        $meta = $this->defaultMeta($response->getFile());

        //look for accompanying YAML .meta file
        $metaFile = $response->getFile() . $this->getConfig()->get('ContentLayer.metaSuffix');
        if (file_exists($metaFile) && is_readable($metaFile)) {
            $newMeta = Yaml::parse(file_get_contents($metaFile));
            if ($newMeta) {
                $meta = array_replace_recursive(
                    $meta,
                    $newMeta
                );
            }
        }

        //try to find YAML front-matter
        if (preg_match("/^\-\-\-[\s]*$/m", $content)) {
            $contentSplit = preg_split("/^\-\-\-[\s]*$/m", $content);
            if (count($contentSplit) == 3) {
                $content = $contentSplit[2];
                $newMeta = Yaml::parse($contentSplit[1]);
                if ($newMeta) {
                    $meta = array_replace_recursive(
                        $meta,
                        $newMeta
                    );
                }
            }
        }

        //set content, meta and headers into response
        $response->setHeaders($meta['headers']);
        unset($meta['headers']);
        $response->setMeta($meta);
        $response->setContent($content);

        //locate handler
        $chosenHandler = false;
        //file handlers
        $chosenHandler = $this->returnPatternMatch(
            $this->getConfig()->get('ContentLayer.fileHandlerPatterns'),
            $response->getFile()
        ) ?: $chosenHandler;
        //default handler
        if (!$chosenHandler) {
            $chosenHandler = $this->getConfig()->get('ContentLayer.defaultHandler');
        }

        //construct handler and apply transformation
        $chosenHandler = new $chosenHandler($this->getConfig());
        $response      = $chosenHandler->transform($response);

        //clean up meta and headers
        $meta    = $response->getMeta();
        $headers = $response->getHeaders();

        //ETags
        if (isset($headers['ETag']) && $headers['ETag'] == 'auto') {
            $headers['ETag'] = md5($response->getContent());
        }
        //try to force date to a DateTime
        if (!($meta['date'] instanceof \DateTime)) {
            $time         = strtotime($meta['date']);
            $meta['date'] = new \DateTime();
            $meta['date']->setTimeStamp($time);
        }
        //set title to filename
        if (!isset($meta['title']) || !$meta['title']) {
            $meta['title'] = preg_replace("/^.*[\/\\\]/", '', $response->getFile());
        }
        //set single category
        if (isset($meta['category']) && $meta['category']) {
            $meta['categories'] = $meta['category'];
            unset($meta['category']);
        }
        //support categories and tags as strings
        if (!is_array($meta['categories'])) {
            $meta['categories'] = preg_split("/,\s*/", $meta['categories']);
        }
        if (!is_array($meta['tags'])) {
            $meta['tags'] = preg_split("/,\s*/", $meta['tags']);
        }
        //Cache-control: max-age
        if ($headers['Cache-control']['max-age'] == 'auto') {
            if (isset($meta['ttl']) && $meta['ttl']) {
                $headers['Cache-control']['max-age'] = $meta['ttl'];
            } else {
                unset($headers['Cache-control']['max-age']);
            }
        }

        //write cleaned-up meta and headers into response
        $response->setMeta($meta);
        $response->setHeaders($headers);

        //return the altered response
        return $response;
    }

    protected function returnPatternMatch($patterns, $target)
    {
        if (!is_array($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern => $value) {
            if (preg_match($pattern, $target)) {
                return $value;
            }
        }
        return false;
    }

    protected function defaultMeta($file)
    {
        $meta = $this->getConfig()->get('ContentLayer.defaultMeta');
        //date modified
        $meta['date'] = $meta['date'] = new \DateTime();
        $meta['date']->setTimestamp(filemtime($file));
        //content disposition
        $meta['headers']['Content-Disposition'] = array(
            'filename' => preg_replace("/^.*[\/\\\]/", '', $file),
        );
        //mime
        if (function_exists("finfo_open") && false) {
            $finfo                           = finfo_open(FILEINFO_MIME_TYPE);
            $meta['headers']['Content-Type'] = finfo_file($finfo, $file);
        } else {
            $meta['headers']['Content-Type'] = $this->simpleMime($file);
        }
        return $meta;
    }

    protected function simpleMime($file)
    {
        foreach ($this->getConfig()->get('ContentLayer.simpleMime') as $pattern => $mime) {
            if (preg_match($pattern, $file)) {
                return $mime;
            }
        }
        //fallback is text/plain
        return "text/plain";
    }
}
