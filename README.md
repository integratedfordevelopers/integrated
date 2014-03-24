Integrated User Bundle
=====

TODO: write a readme file ;)

### Example Security Config

Below example security config that can be used to setup a basic login support
for a integrated website.

	security:
		encoders:
			Integrated\Bundle\UserBundle\Model\User: sha512

		providers:
			integrated_user:
				id: integrated_user.security.provider

		firewalls:
			dev:
				pattern:    ^/(_(profiler|wdt|configurator)|css|images|js)/
				security:   false

			main:
				pattern:  ^/
				anonymous: ~
				form_login:
					csrf_provider: form.csrf_provider

					login_path:    integrated_user_login
					check_path:    integrated_user_check

				logout:
					path:   integrated_user_logout
					target: /

		access_control:
			- { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
			- { path: ^/, roles: IS_AUTHENTICATED_REMEMBERED }
