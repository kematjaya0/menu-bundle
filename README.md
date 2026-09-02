# menu-bundle for Symfony 5
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
- add to config/routes/annotations.yaml
```
...
kmj_menu:
    resource: '@MenuBundle/Resources/config/router.xml'
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
- optional configuration (config/packages/menu.yaml), all keys have defaults:
```yaml
menu:
    resources_dir: '%kernel.project_dir%/resources'  # where menu.yaml lives
    resources_file: 'menu.yaml'
    redirect_path_on_exception: null   # route name to redirect to on access-denied instead of showing the built-in page
    homepage_route: 'homepage'         # route used by the "back to homepage" link on the access-denied page; must exist in your app
```

### Notes / conventions

- The first menu entry encountered for a given `group` determines that group's link and, unless `icon_group` is set on some entry in the group, its icon.
- Any menu entry whose route generation fails (missing mandatory route parameters, unknown route, etc.) is skipped with a PHP warning instead of breaking the whole `{{ kmj_menu() }}` render — check your logs if a menu item unexpectedly disappears.
- Route names ending in `_index` have that suffix stripped when building the access-control group tree (`kmj_menu_access_control_*` pages), so route naming follows the `..._index` / `..._show` convention used by `kematjaya/url-bundle`.
- `homepage` and `kmj_access_denied` route names are always allowed through `RouteCredential`, regardless of role configuration.

### Access control management (`AccessControlController`)

- `kmj_menu_access_control_index` / `kmj_menu_access_control_show` only require the visitor to be authenticated. **You must additionally restrict these two routes with a `role:` entry in menu.yaml** (as shown above for `kmj_menu_access_control_index`), otherwise any logged-in user can open them and change which roles can access which routes.
- When saving, a role can only be granted or revoked on a route if that role is within the *acting user's own reachable role hierarchy* (`RoleHierarchyInterface::getReachableRoleNames()`), in both directions: granting a route to a role you have no authority over, and revoking a role you have no authority over, are both rejected — the affected route(s) keep their previous role state and a `warning` flash message is added explaining what was skipped.
- A route's role checkboxes read from the persisted routing storage (`url-bundle`'s `RoutingSourceInterface`) once it exists there; menu.yaml's `role:` list is only used as the *initial default* before that route has ever been saved through this UI.
