# Plaster CMS

**CURRENTLY UNSTABLE** This project is under active, pre-release development. It would be ill-advised to use it for any kind of production work. The APIs and interfaces are subject to change.

A flat-file non-CMS. Plaster puts a pretty coat on flat files, without disrupting the fundamental file/folder paradigm of a web server.
The primary purpose of this application is to give select CMS-like features to a website that is primarily static and composed of flat files.
Features include:

* Markdown parsing
* Twig parsing
* Templating
* Easy control over HTTP headers (particularly caching controls)
* Output caching
* Helpers for generating file lists and navigation elements
* URL rewriting (i.e. serving `.md` files as if they had a `.html` extension)

## Install with Composer

```
composer require jobyone/plaster
```

## Basic use

```php
//initialize a PlasterApplication, passing it a 
//list of config files to use
$config = array('path-to-your-config.yaml');
$app = new jobyone\Plaster\PlasterApplication($config);

//render using default settings to use $_SERVER['PATH_INFO']
//as the url
$app->render();

//render() can also take a URL explicitly as
//its first argument, if you want to get it in
//some other way
```
    
## Advanced use

For more advanced users, Plaster provides tools for doing many more interesting things.
The basic paradigm consists of `Response` objects, which are designed to contain all information necessary to render a file.
Each `Response` begins its life as simply a shell with a URL attached to it.
These `Response` objects are then run through a series of `TransformationLayer` objects.
Each alters and builds upon the `Response` until a fully-formed `Response` pops out the other side, ready to render as a complete page.

`Response` objects are also fully serializable, and any set of `TransformationLayer` objects can be wrapped in a `TransformationCache` object.