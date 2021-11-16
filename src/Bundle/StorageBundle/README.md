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
The bundle makes use of *knplabs/knp-gaufrette-bundle* for configuration and a filesystem map. When you've enabled the bundle this config below is required for a default Integrated installation.  

    # app/config.yml
    knp_gaufrette:
        adapters:
            foo:
                local:
                    directory: %kernel.project_dir%/../web/uploads/documents
        filesystems:
            foo:
                adapter: foo

    integrated_storage:
        resolver:
            foo:
                public: /uploads/documents
    
The StorageBundle places the files on all known filesystems when no decision mapping exists. The order defined in the configuration will be used to determine its primary path. When a filesystem has no resolver storage (for protected files) the developer must write an own implementation to give access to file (see **Protecting files** section). The filesystems (foo above) in the *knp_gaufrette* configuration are linked to the *integrated_storage* configuration. Based on the key(s) a resolver or decision map entry is linked to a filesystem.
  
### Configuring the assets ###
The IntegratedStorageBundle uses the [SpBowerBundle](https://github.com/Spea/SpBowerBundle) for handling the external
resources.

	# app/config/config.yml
	sp_bower:
        bundles:
            IntegratedStorageBundle: ~
  
### Routing ###
The routing needs to be to imported into the application to support usage in various Integrated components. 
 
    # app/routing.yml
    integrated_storage:
        resource: "@IntegratedStorageBundle/Resources/config/routing/storage.xml"
        prefix: "/"

#### Decision map ####
Additionally to protected entities from being stored in a public accessible resource a developer can configure filesystems for entities.  
You can enforce entities to be stored in specific storage(s) (thus preventing a public storage).  

    # app/config.yml
    integrated_storage:
        // ...
        decision_map:
            "Integrated\Bundle\ContentBundle\Document\File": [foo]

**The redistribution command does not make use of the decision map and copies all files in the given storage.**

#### Identifier ###
A file requires an unique identifier. By default the identifier is based on the contents of the file. You can write your own implementation of `Integrated\Common\Storage\Identifier\IdentifierInterface`. The config below is default and does not have to be set.

    # app/config.yml
    integrated_storage:
        // ...
        identifier_class: Integrated\Bundle\StorageBundle\Storage\Identifier\FileIdentifier  

#### Resolver ###
A resolver returns the location of the stored file, providing a location for a browser. The filesystem does is not linked to public accessible location, for each filesystem a resolver must be written. In some cases you will need to add filesystem specific logic to the resolver. You can write your own implementation of `Integrated\Common\Storage\Resolver\ResolverInterface`.

The config below is default and does not have to be set.

    # app/config.yml
    integrated_storage:
        // ..
        resolver:
            foo:
                public: /uploads/documents
                resolver_class: Integrated\Bundle\StorageBundle\Storage\Resolver\LocalResolver

The arguments given to the resolver are passed in the options variable of the constructor. You can add a service or a parameter to your resolver trough the configuration.  

### Protecting files ###
In some cases the files may not be stored in a public available directory. Most cases a directory on the home of the user is enough which can be stored local. You can do this by adding an additional private local storage. However files can also be stored on any remote by not defining an resolver. 

A controller might look like the following:

    // File is a Integrated\Common\Content\Document\FileInterface object    
    $response = new Response();
    $response->setContent($file->getContent());
    $response->setHeaders($file->getMetadata()->getHeaders());

### Data fixtures ###
In order to make use of the StorageBundle in fixtures the file must exist on disk and must be created by an StorageManager. The manager creates the file on the disk as configured in the application. The file exists and is placed on the correct filesystem with the required properties. To use the helper you must add a trait to your *LoadFixtureData* class and the *LoadFixtureData* class must be *ContainerAware* to have access to the container.

	// Required class and trait
    use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension\FileExtensionTrait;
    use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension\ImageExtensionTrait;
    use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension\VideoExtensionTrait;
	use Symfony\Component\DependencyInjection\ContainerAwareInterface;
	use Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use Symfony\Component\DependencyInjection\ContainerInterface;
	...
	class LoadFixtureData implements FixtureInterface, ContainerAwareInterface {
	    // The trait to include the public helper
        use FileExtensionTrait;
        use ImageExtensionTrait;
        use VideoExtensionTrait;
        
        use ContainerAwareTrait;

 
Now you can use the following in your *alice/Fixtures.yml* file:

    # Create an image (storage object)
    <createImage(300, 400, 'nature')>
	# Create a file type (might be slower because it uses lorempixel)
    <createFile($this->fake('image', '', '/tmp', 300, 400, 'business'), 'name')>
	# Create a storage object (file) (might be slower because it uses lorempixel)
    <createStorage($this->fake('image', '', '/tmp', 300, 400, 'city'))>
	# Create a random video (storage object) (from wosvideo.e-activesites.nl)
    <createVideo()>
    

Depending on your implementation you can remove the type and extension requirements or as a parameter in whole. 


## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for Developers](http://integratedfordevelopers.com/ "Integrated for Developers") website.
