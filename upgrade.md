# Update instructions #

## Upgrade to Integrated version 0.4 ##
Add to config.yml:

    knp_gaufrette:
        adapters:
            local:
                local:
                    directory: %kernel.root_dir%/../web/uploads/documents

        filesystems:
            integrated:
                adapter: local

    integrated_storage:
        resolver:
            integrated:
                public: /uploads/documents

Add to composer.json:

    "integrated/storage-bundle": "~0.4",

Add to AppKernel.php:

    new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
    new Integrated\Bundle\StorageBundle\IntegratedStorageBundle(),

Two new parameters needed in parameters.yml.dist (and parameters.yml): recaptcha_site_key and recaptcha_secret_key
Your application need to be Symfony 2.7 compatible (https://github.com/symfony/symfony/blob/v2.7.0-BETA1/UPGRADE-2.7.md)
Registers the bundle in your app/AppKernel.php:

    new Gregwar\ImageBundle\GregwarImageBundle(),

Replace app.channel with _channel when used in Twig view
Remove secure: true from vihuvac_recaptcha section in config.yml

## Upgrade to Integrated 0.1.1.7 ##
php5-intl is now required
Update config.yml: services section has been added for Twig_Extensions_Extension_Intl
Modify init:queue command in composer.json to: php app/console init:queue --force
Add command in composer.json: php app/console init:locking --force
Edit and save your content types to use the TinyMCE editor instead of wysihtml5x 
Add locking bundle to AppKernel.php: new Integrated\Bundle\LockingBundle\IntegratedLockingBundle(),
Add cron: * * * * * php app/console locking:dbal:clean
