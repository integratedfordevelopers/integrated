Integrated User Bundle
=====

TODO: write a readme file ;)

### Example Security Config

Below example security config that can be used to setup a basic login support
for a integrated website.

    security:
        enable_authenticator_manager: true

        password_hasher:
            Integrated\Bundle\UserBundle\Model\User: auto

        providers:
            integrated_user:
                id: integrated_user.security.provider

        firewalls:
            dev:
                pattern:    ^/(_(profiler|wdt|configurator)|css|images|js)/
                security:   false

            main:
                pattern:  ^/
                lazy: true
                form_login:
                    enable_csrf: true

                    login_path:    integrated_user_login
                    check_path:    integrated_user_check

                logout:
                    path:   integrated_user_logout
                    target: /
                remember_me:
                    secret:   '%kernel.secret%'
                    lifetime: 2592000 # 30 days
                    path:     /					

        access_control:
            - { path: ^/login, roles: PUBLIC_ACCESS }
            - { path: ^/, roles: IS_AUTHENTICATED_REMEMBERED }
