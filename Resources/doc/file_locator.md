# FileLocator #

The IntegratedSolrBundle ships with a FileLocator driver. In order to use the [YAML](yaml.md) and [XML](xml.md) driver you must configure the FileLocator.


## How to use ##

After [installing](index.md) the IntegratedSolrBundle the `integrated_solr` namespace is available in your configuration:

	// app/config.yml
	integrated_solr:
    	mapping:
        	directories:
				acme_demo:
					namepace_prefix: 'Acme\DemoBundle'
					path: '%kernel.root_dir%/../src/Acme/DemoBundle/Resources/config/mapping'


Please note that all the `.xml` or `.yml` files in the directory will be read.