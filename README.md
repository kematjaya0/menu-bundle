# menu-bundle for Symfony 8
- installation
```
composer require kematjaya/menu-bundle
```
- configure to config/bundles.php
```
...
Kematjaya\MenuBundle\MenuBundle::class => ['all' => true]
...
```
- add to config/routes.yaml
```
...
kmj_menu:
    resource: '@MenuBundle/Resources/config/router.yml'
...
```
- create file resources/menu.yaml for setting list of menu and insert menu like this:
```
dashboard:                        # Path name / route name
    label: dashboard              # label
    icon: ft-home                 # css icon
    group: null                   # group menu 
    
kmj_menu_access_control_index:    # Path name / route name
    label: access_control         # label
    icon: ft-aperture             # css icon
    group: administrator          # css icon
    icon_group: fi fi-rr-settings 
    role:                         # role for allowed to access this menu
        - ROLE_SUPER_USER
        - ROLE_ADMINISTRATOR
```
- view menu in twig, add this to your twig template
```
{{ kmj_menu() }}
```
- url:
```
access control: kmj_menu_access_control_index
setting access control: kmj_menu_access_control_show
```
