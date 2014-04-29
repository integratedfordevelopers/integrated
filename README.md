# Integrated Content Bundle

TODO: write a readme file ;)

### Assetic config

Don't forget to do a assetic dump so that the bootstrap css and javascript are generated

	# Assetic Configuration
	assetic:
		debug:          %kernel.debug%
		use_controller: false
		filters:
			less:
				node: /usr/bin/node
				node_paths: [/usr/local/lib/node_modules]
				compress: true
				apply_to: "\.less$"
			cssrewrite: ~
		assets:
			integrated_css:
				inputs:
	#                - @IntegratedFormTypeBundle/Resources/public/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css
			integrated_js:
				inputs:
	#                - @IntegratedFormTypeBundle/Resources/public/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js
	#                - @IntegratedFormTypeBundle/Resources/public/js/*

	# Bootstrap configuration
	braincrafted_bootstrap:
		less_filter: less