wmmodel
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmmodel/v/stable)](https://packagist.org/packages/wieni/wmmodel)
[![Total Downloads](https://poser.pugx.org/wieni/wmmodel/downloads)](https://packagist.org/packages/wieni/wmmodel)
[![License](https://poser.pugx.org/wieni/wmmodel/license)](https://packagist.org/packages/wieni/wmmodel)

> Adds support for bundle-specific models for Drupal 8 entities.

## Why?
- Improve the developer experience of the [entity bundle classes functionality](https://www.drupal.org/node/3191609) 
  by making it possible to register them through annotations and by providing field and translation related helper 
  functions

## Installation

This package requires PHP 7.1 and Drupal 9.3 or higher. It can be
installed using Composer:

```bash
 composer require wieni/wmmodel
```

## How does it work?
### Creating models
Models are Drupal plugins with the `@Model` annotation, extending their entity type class. Classes with this
annotation should be placed in the `Entity` namespace of your module.

The annotation two required parameters:
- if only `entity_type` is provided, the entity type class is overridden using `hook_entity_type_alter`
- if both `entity_type` and `bundle` are provided, the bundle class is overridden using `hook_entity_bundle_info_alter`. 

```php
<?php

namespace Drupal\mymodule\Entity\Node;

use Drupal\node\Entity\Node;

/**
 * @Model(
 *     entity_type = "node",
 *     bundle = "page"
 * )
 */
class Page extends Node
{
}
```

To make sure bundles are mapped to the right classes, you can use the
`wmmodel:list` Drush command.

```bash
> drush wmmodel:list
 Model "media.image" is not mapped.
 Model "node.page" is mapped against "Drupal\mymodule\Entity\Node\Page".
```

### Controller resolving
If a controller is handling a route with entity parameters, the models
can be automatically injected in the arguments of the controller method
by using the right type hint. If the route has two parameters of the same type, 
matching is done based on the parameter/argument name. This functionality is
especially useful in combination with the [`wmcontroller`](https://github.com/wieni/wmcontroller) module.

```php
<?php

namespace Drupal\mymodule\Controller\Node;

use Drupal\mymodule\Entity\Node\Page;
use Drupal\wmcontroller\Controller\ControllerBase;

class PageController extends ControllerBase
{
    public function show(Page $page)
    {
        return $this->view(
            'node.page.detail',
            ['page' => $page]
        );
    }
}

```

If you're creating a custom route with an entity parameter and you want to restrict that parameter to only entities of a
certain bundle, you can now add the bundle to the parameter type definition in the route:

```yml
mymodule.company.suggestion:
    path: '/companies/{company}/suggestion'
    defaults:
        _controller: 'Drupal\wmcustom\Controller\Node\CompanyController::addSuggestion'
    options:
        parameters:
            company:
                type: 'entity:node:company'
    requirements:
      _access: 'TRUE'
```

### Injecting the user entity
This module provides an alternative implementation of
`Drupal\Core\Session\AccountProxyInterface` (returned by the
`current_user` service) that makes the `getAccount` method return the
actual User entity instead of an instance of
`Drupal\Core\Session\UserSession`.

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE.md) file
for more information.
