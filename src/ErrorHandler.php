<?php 
namespace jobyone\Plaster;

class ErrorHandler extends AbstractLayer implements Interfaces\ErrorLayer
{
    function doTransform(Interfaces\Response $response)
    {
        return $response;
    }
    function transformError(Interfaces\Response $response, $status = 500, $message = false)
    {
        $response->setStatus($status);
        $response->setMeta(array(
            'title' => "Error $status",
            'skipTemplate' => false
        ));
        $response->setContent("<h1>Error $status</h1><p>Unable to fulfill request for <em>" . $response->getUrl() . "</em></p>");
        return $response;
    }
}