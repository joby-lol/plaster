<?php
namespace jobyone\Plaster;

class TemplateLayer extends AbstractLayer implements Interfaces\TransformationLayer
{
    public function doTransform(Interfaces\Response $response)
    {
        $meta = $response->getMeta();

        $response->setContent(
            $this->getConfig()->get('TemplateManager.current')->render(
                $meta['template'],
                $response
            )
        );

        //return the altered response
        return $response;
    }
}
