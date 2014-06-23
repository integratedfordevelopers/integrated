# Update instructions #

## From 0.1.1.6 to 0.1.1.7 ##
- php5-intl is now required
- Update config.yml: services section has been added for Twig_Extensions_Extension_Intl

## From 0.1.1.5 to 0.1.1.6 ##

- Modify init:queue command in composer.json to: php app/console init:queue --force
- Add command in composer.json: php app/console init:locking --force
- Edit and save your content types to use the TinyMCE editor instead of wysihtml5x 
- Add locking bundle to AppKernel.php: new Integrated\Bundle\LockingBundle\IntegratedLockingBundle(),
- Add cron: * * * * * php app/console locking:dbal:clean
