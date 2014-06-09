# Getting started #

The IntegratedFormTypeBundle provides different Symfony Form Types for the Integrated project.

## Installation ##

The Bundle is part of the default Integrated package. Manual installation can also be done by following these steps:

1. Install using composer
2. Enable the bundle in the AppKernel.php
3. Load front-end resources
4. Add front-end resources to the Integrated assets

### 1. Install using composer ##

  	"require": {
   	    "integrated/formtype-bundle": "dev-develop"
   	}

### 2. Enable the bundle ###

Enable the bundle in the kernel:

	// app/AppKernel.php

	public function registerBundles()
	{
    	$bundles = array(
    	    // ...
    	    new Integrated\Bundle\FormTypeBundle\IntegratedFormTypeBundle(),
    	);
	}


### 3. Load front-end resources ###

The different Form Types can contain front-end resources. This Bundle is compatible with the
[SpBowerBundle](https://github.com/Spea/SpBowerBundle) that uses [Bower](http://bower.io/) to load these resources.

### 4. Add front-end resources to the Integrated assets ###

After loading the front-end resources the resources must be added to the assets of Integrated. The
IntergratedContentBundle uses two different assets: `integrated_js` and `integrated_css`.

The different assets that are used can be found per Form Type:

* [DateTime Type](datetime_type.md)





