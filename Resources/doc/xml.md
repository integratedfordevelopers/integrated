# XML #

The IntegratedSolrBundle ships with a XML driver. This means you can provide a configuration for your documents or entities in XML format.

## How to use ##

Follow these two steps to use the XML configuration method:

1. Configure the FileLocator
2. Write your configuration in XML format

### 1. Configure the FileLocator ###

You must tell the XML driver where to look for XML files, this can be accomplished using the FileLocator. You can read more about this in de [FileLocator documentation](file_locator.md).

### 2. Write your configuration in XML ###

After configuring the FileLocator you can put XML files in the configured directories. 

	<?xml version="1.0" encoding="UTF-8" ?>
	<mapping>
    	<documents>
    	    <document class="Acme\DemoBundle\Document\Article" index="1">
    	        <fields>
    	            <field name="title" index="1" facet="0" display="1" sort="0"  />
    	            <field name="subtitle" index="1" facet="0" sort="0"  />
    	        </fields>
    	    </document>
    	    <document class="Acme\DemoBundle\Document\Company" index="0" />
    	</documents>
	</mapping>
 
Per document a class must be defined and one or more fields can be configured. A field must have a corresponding name.

You can find more information about the configuration options in the [Getting started](index.md) documentation.