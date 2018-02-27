# IntegratedSolrBundle #
Adds support for Solr configuration and indexing of your documents or entities

## Requirements ##
* See the require section in the composer.json

## Documentation ##
* [Integrated for developers website](http://www.integratedfordevelopers.com "Integrated for developers website")

### Commands ###
* The command **solr:indexer:queue** will queue up all the documents to be indexed
* The command **solr:indexer:run** will tranform the queued up documents to a solr compatible format and send them to solr for indexing.
* The command **solr:worker:run** will start a worker run and will process up to end of the queue or 1000 tasks, whichever comes first.

It's recommended to the execute **solr:indexer:run** and **solr:worker:run** as automated tasks by configuring them as cronjobs.

## Installation ##
This bundle can be installed following these steps:

### Install using composer ###

    $ php composer.phar require integrated/solr-bundle:~0.3

### Enable the bundle ###

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Integrated\Bundle\SolrBundle\IntegratedSolrBundle()
            // ...
        );
    }

### Setup the queue ###
The solr bundle requires a queue to work properly, so if not already done setup the queue by executing the **init:queue** command.

## License ##
This bundle is under the MIT license. See the complete license in the bundle:

    LICENSE

## Contributing ##
Pull requests are welcome. Please see our [CONTRIBUTING guide](http://www.integratedfordevelopers.com/contributing "CONTRIBUTING guide").

## About ##
This bundle is part of the Integrated project. You can read more about this project on the
[Integrated for developers](http://www.integratedfordevelopers.com/ "Integrated for developers") website.