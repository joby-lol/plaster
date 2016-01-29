<?php 
require __DIR__ . '/../vendor/autoload.php';

/*
This file demonstrates how to produce the same result as what is done in
demo-basic.php without using the PlasterApplication helper. 

This is 
*/

use jobyone\Plaster\Config;
use jobyone\Plaster\Response;
use jobyone\Plaster\FileLayer;
use jobyone\Plaster\ContentLayer;
use jobyone\Plaster\TemplateLayer;
use jobyone\Plaster\TransformationStack;
use jobyone\Plaster\TemplateManager;

//initialize config object
//this object is shared through all transformation layers
$config = new Config(array(
    __DIR__ . '/demo.yaml'
));

//initialize an empty Response 
//The steps this Response will follow are:
// - Have its corresponding file located by a FileLayer
// - Have its content loaded by a ContentLayer
//   - Within that ContentLayer have its content optionally parsed by a ContentHandler
// - Optionally have its content wrapped in a template by a TemplateLayer
$response = new Response($_SERVER['PATH_INFO']);

//set up transformation layers
//This layer locates the files that a URL refers to
$fileLayer = new FileLayer($config);
//This layer loads the contents of a file and processes it 
//using ContentHandlers. It also initializes a Response's metadata 
//and headers.
$contentLayer = new ContentLayer($config);
//This layer wraps a Response's content in a the template 
//specified in it's metadata's "template" field
$templateLayer = new TemplateLayer($config);

//set up content stack
//This allows the FileLayer and ContentLayer to be called 
//as if they were a single TransformationLayer
$contentStack = new TransformationStack($config);
$contentStack->addLayer('file', $fileLayer);
$contentStack->addLayer('content', $contentLayer);

//set up main transformation stack
//This allows the content stack and TemplateLayer to be called 
//as if they were a single TransformationLayer
$fullStack = new TransformationStack($config);
$fullStack->addLayer('content', $contentStack);
//When adding a layer to a TransformationStack, an optional
//second parameter allows a check to be made before that layer's 
//transformation is applied. It must be a function, which will
//be passed a Request. It should return true/false, whether the 
//layer should be used for that Request.
$fullStack->addLayer('template', $templateLayer, function($request) {
    $meta = $request->getMeta();
    if (isset($meta['skipTemplate']) && $meta['skipTemplate']) {
        return false;
    }
    return true;
});

//set up Template Manager
//This is passed the content stack. It will use that stack to allow
//template helpers to pull just the content of files, before they have
//their templates wrapped around them.
$templateManager = new TemplateManager($config, $contentStack);

//use layers to do transformations to response
$response = $fullStack->transform($response);

//For demonstration purposes this demo dumps the Response
//This can be useful to see in detail what's being produced
$response->dump();
// var_dump($config);

//Use this call to render final response
//$response->render();