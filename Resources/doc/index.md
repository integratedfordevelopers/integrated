#Getting started#
This bundle provides some extra functionality for the [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle). 
The menu provider in this bundle fires an event. You can listen to this event and edit the menu within your Listener or
Subscriber.

##Requirements##
* <= PHP 5.4
* Symfony 2.3+
* KnpMenuBundle 2.0+

##Instalation##
1. Download IntegratedMenuBundle using composer
2. Enable the Bundle
3. Create your ConfigureMenuListener

###1. Download IntegratedMenuBundle using composer###
You can download the IntegratedMenuBundle by running the command:

`$ php composer.phar require integrated/menu-bundle`

###2. Enable the Bundle###
Enable the bundle in the kernel:

    <?php
	// app/AppKernel.php

	public function registerBundles()
	{
    	$bundles = array(
    	    // ...
    	    new Integrated\Bundle\MenuBundle\IntegratedMenuBundle(),
    	);
	}

###3. Create your ConfigureMenuListener###
The IntegratedMenuBundle fires a `ConfigureMenuEvent`. You can listen to this event by using a Listener or Subscriber. 
You can read more about events in the [Symfony Cookbook](http://symfony.com/doc/2.3/components/event_dispatcher/index.html).

With this Listener you can edit the menu by adding, removing or replacing menu items:

    <?php
    // src/AppBundle/EventListener/ConfigureMenuListener.php
    
    namespace AppBundle\EventListener;
    
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
    
    use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
    
    class ConfigureMenuListener
    {
        public function onMenuConfigure(ConfigureMenuEvent $event)
        {
            $menu = $event->getMenu();
        
            // You can check if you want to edit this menu
            if ($menu->getName() !== 'my_menu') {
                return;
            }
    
            // Customize the menu
            $label = $menu->addChild('This is just a label');
            $label->addChild('Menu item', array('route' => 'my_route'));
        }
    }

After creating the Listener you can register it as a service and notify Symfony that it is a "listener" on 
the integrated_menu.configure event by using a special "tag":

    # app/config/services.yml
    services:
        kernel.listener.your_listener_name:
            class: AppBundle\EventListener\ConfigureMenuListener
            tags:
                - { name: kernel.event_listener, event: integrated_menu.configure, method: onMenuConfigure }