<?php
namespace jobyone\Plaster\Interfaces;

interface ErrorLayer extends TransformationLayer
{

    function transformError(
        Response $response,
        $code = 500,
        $message = false
    );

}
