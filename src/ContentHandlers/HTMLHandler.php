<?php
namespace jobyone\Plaster\ContentHandlers;

use jobyone\Plaster\ContentHandlers\AbstractContentHandler;
use jobyone\Plaster\Interfaces\ContentHandler;
use jobyone\Plaster\Interfaces\Response;

class HTMLHandler extends AbstractContentHandler implements ContentHandler
{
    /**
     * This handler does nothing. It just passes the response through
     * unchanged, but doesn't flag it to have templating skipped like
     * the StreamHandler does.
     * @param  Response $response [description]
     * @return [type]             [description]
     */
    public function transform(Response $response)
    {
        return $response;
    }
}
