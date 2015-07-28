# IntegratedStorageBundle #
This bundle enhances storage in any Integrated (0.4+) project.  

## Requirements ##
* See the require section in the composer.json

## Documentation ##
* [Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers")

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/storage-bundle:~0.4

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
			new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Integrated\Bundle\StorageBundle\IntegratedStorageBundle()
            // ...
        );
    }

### Configuration ###
The bundle makes use of *knplabs/knp-gaufrette-bundle* for configuration and a file system map. In order to upgrade you current installation to the new version of Integrated you'll need to add the following to you the application configuration.  

	// app/config.yml
	knp_gaufrette:
	    adapters:
	        local:
	            local:
	                directory: %kernel.root_dir%/../web/uploads/documents
	    filesystems:
	        local:
	            adapter: local

	integrated_storage:
		resolver:
			local:
				public: /uploads/documents
	
The StorageBundle places the files on all known file systems. The order defined in the configuration will be used to determine its main path. When a file system has no resolver storage (for protected files) the developer must write an own implementation to give access to file. 

#### Protecting files ####
Additionally to protected entities from being stored in a public accessible storage a developer can configure filesystems for entities.   

	// app/config.yml
	integrated_storage:
		// ...
		decision_map:
			"Integrated\Bundle\StorageBundle\Document\File": [local]

**The redistribution command does not make use of the decision map and copies all files in the given storage.**

#### File identifier ###
A file requires an unique identifier. By default the identifier is based on the contents of the file. You can write your own implementation of `Integrated\Bundle\StorageBundle\Storage\Identifier\Identifier`. 

	// app/config.yml
	integrated_storage:
		// ...
		identifier_class: FileIdentifier

When no namespace has been given it will look in `Integrated\Bundle\StorageBundle\Storage\Identifier` by default. To provide a class add a backspace in front.  

#### Url Resolver ###
In some cases you will need to add logic to the URL resolver. You can write your own implementation of `Integrated\Bundle\StorageBundle\Storage\Resolver\ResolverInterface`.

	// app/config.yml
	integrated_storage:
		// ..
		resolver:
			public:
				public: /uploads/documents
				resolver_class: UriResolver

When no namespace has been given it will look in `Integrated\Bundle\StorageBundle\Storage\Resolver` by default.

 

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
