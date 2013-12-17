# Getting started #

This bundle adds different ways to add Solr configuration to your documents or entities:

1. Annotations
2. YAML
3. XML

## Configuration options ##

The configuration options can be divided into two levels:

### Document level ###

Per document the following options are available

* **index**: this option can be true or false. The default value is false. If this option is set to true the document
will be indexed into Solr.

### Property level ###

Per property the following options are available:

* **index**: by default this option is false. When set to true the property will be indexed.
* **facet**: by default this option is false. When set to true the property will be indexed as a facet.
* **sort**: by default this option is false. When set to true the property will be sortable.
* **display**: by default this option is false. When set to true the property will be available for display.

## Installation ##

The IntegratedSolrBundle can be installed following these steps:

1. Install using composer
2. Enable the bundle
3. Choose a configuration method

### 1. Install using composer ##

	{
    	"repositories": [
        	{
            	"type": "vcs",
            	"url": "git@bitbucket.org:eactive/integrated-solr-bundle.git"
        	}
    	],		
    	"require": {
    	    "integrated/solr-bundle": "dev-develop"
    	}
	}

### 2. Enable the bundle ###

Enable the bundle in the kernel:

	// app/AppKernel.php
	
	public function registerBundles()
	{
    	$bundles = array(
    	    // ...
    	    new Integrated\Bundle\SolrBundle\IntegratedSolrBundle(),
    	);
	}

### 3. Choose a configuration method ###

After installing and enabling the bundle a document can be configured using one of the following methods:

1. [Annotations](annotations.md)
2. [YAML](yaml.md)
3. [XML](xml.md)