<?php 
namespace jobyone\Plaster\ContentHandlers;

use jobyone\Plaster\Interfaces\ContentHandler;
use jobyone\Plaster\Interfaces\Response;
use jobyone\Plaster\StreamResponse;

class StreamHandler extends AbstractContentHandler implements ContentHandler
{
    function transform(Response $response)
    {
        //copy response over into a StreamResponse
        // TODO: Find a way to use StreamResponse but strip front YAML
        // That probably means caching a copy of this request's content 
        // somewhere in another file
        // $response = new StreamResponse(
        //     $response->getUrl(),
        //     $response->getFile(),
        //     $response->getContent(),
        //     $response->getHeaders(),
        //     $response->getMeta()
        // );
        
        //set meta and headers
        $meta = $response->getMeta();
        if (!isset($meta['skipTemplate'])) {
            $response->setMeta(array('skipTemplate' => true));
        }
        
        return $response;
    }
}