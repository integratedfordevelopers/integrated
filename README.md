# Integrated Workflow Bundle

This bundle will intergrate workflow into the content bundle. The bundle is still work in progess so
for now this readme will only contain instructions to get the bundle workin.

## Installation ##

The following thing should be done in any order but I would recommend doing the first one first or
else you get errors when adding the entities.

* Add the bundle to the AppKernel
* Add the routing

	_intergrated_workflow:
		prefix: /workflow/
		resource: "@IntegratedWorkflowBundle/Resources/config/routing/workflow.xml"

* The bundle got entities so execute the Doctrine commands

	php app/console doctrine:schema:update

* There are no config options for now but it is recommend to add this to the security config

    access_decision_manager:
        # strategy can be: affirmative, unanimous or consensus
        strategy: unanimous

Well that is it.

## Scheduled Tasks ##

The console command **workflow:worker:run** is recommanded to be scheduled to run atleast **once every 
minute**. This needs to be done to done to make sure that changed to workflows reflect into the reindex 
of the affected content.

**Pro tip:** activate the quite mode to suppress output.
