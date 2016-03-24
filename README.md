# IntegratedAssetBundle #
This bundle provides asset management

## Requirements ##
* See the require section in the composer.json

## Features ##
* Asset management

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/asset-bundle:~0.5

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\AssetBundle\IntegratedAssetBundle()
            // ...
        );
    }

## Examples ##

### Add inline style ###

    {% integrated_stylesheets inline=true %}
        body { background: red; }
        p { font-size: 10px }
    {% endintegrated_stylesheets %}
    
### Add external styleheets ###

    {% integrated_stylesheets
        'bundles/app/css/styles.css'
        'bundles/app/css/styles2.css' %}
    {% endintegrated_stylesheets %}
    
### Prepend javascript ###
    
    {% integrated_javascripts mode='prepend'
        'bundles/app/js/script.js' %}
    {% integrated_javascripts %}
    
### Render stylesheets ###
 
    {{ integrated_stylesheets() }}  
     
### Render javascripts ###
 
    {{ integrated_javascripts() }}

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
