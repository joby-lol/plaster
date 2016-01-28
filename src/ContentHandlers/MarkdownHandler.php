<?php 
namespace jobyone\Plaster\ContentHandlers;

use jobyone\Plaster\Interfaces\ContentHandler;
use jobyone\Plaster\Interfaces\Response;

use \Michelf\MarkdownExtra;

class MarkdownHandler extends AbstractContentHandler implements ContentHandler
{
    function transform(Response $response)
    {
        $response->setContent(
            trim(MarkdownExtra::defaultTransform($response->getContent()))
        );
        
        $response->setHeaders(array(
            'Content-Type' => 'text/html'
        ));
        
        return $response;
    }
}