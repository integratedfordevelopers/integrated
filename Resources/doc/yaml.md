# YAML #

The IntegratedSolrBundle ships with a YAML driver. This means you can provide a configuration for your documents or entities in YAML format.

## How to use ##

Follow these two steps to use the YAML configuration method:

1. Configure the FileLocator
2. Write your config in XML format

### 1. Configure the FileLocator ###

You must tell the YAML driver where to look for YAML files, this can be accomplished using the FileLocator. You can read more about this in de [FileLocator documentation](file_locator.md).

### 2. Write your config in YAML ###

After configuring the FileLocator you can put YAML files in the configured directories. 

	'Acme\DemoBundle\Document\Article':
  	  index: true
	  fields:
        title:
		  index: true
      	  facet: false
      	  display: true
		  sort: false
		subtitle:
		  index: true
		  facet: false
		  sort: false
	'Acme\DemoBundle\Document\Company':
  	  index: false

 
Per class you can define one or more fields. 

You can find more information about the configuration options in the [Getting started](index.md) documentation.
