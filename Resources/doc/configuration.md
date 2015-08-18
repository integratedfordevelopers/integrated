# Configuration #

## Routing ##
Enable the routing by adding the following configuration to your routing file:

	//app/config/routing.yml
	integrated_content:
        resource: "@IntegratedContentBundle/Resources/config/routing.xml"
        prefix: "/admin"	

## Assetic ###
The IntegratedContentBundle is shipped with the [BraincraftedBootstrapBundle](http://bootstrap.braincrafted.com/). 
Besides the configuration for this Bundle the IntegratedContentBundle also needs some configuration for assetic:

	//app/config/config.yml

	assetic:
        debug: %kernel.debug%
        use_controller: true
        filters:
            less:
                node_paths: [/usr/local/lib/node_modules]
                compress: true
                apply_to: "\.less$"
            cssrewrite: ~
        assets:
            integrated_css:
                inputs:
                    -
            integrated_js:
                inputs:
                    -

	braincrafted_bootstrap:
	    less_filter: less

 
Back to [Getting started](index.md).