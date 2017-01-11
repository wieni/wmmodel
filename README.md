# Wieni Drupal Models

A convenient way to manage bundle-specific code in Drupal 8.

```
// Have the entitystorage return your classes
$articleId = 1;
$entity = \Drupal::entityTypeManager()->getStorage('node')->load($articleId);
echo get_class($entity); // Drupal\mymodule\Entity\Node\Article

// Or create a new article
$newArticle = Article::create();
$newArticle->setSomeCustomField($foobar);
```

## Installation

```
composer require wieni\wmmodel
```

Patch your core installation of Drupal 8.

The patch will alter how Drupal creates an instance of entities so Storages will return your models.

```
// composer.json
{
    ...
    
    "extra": {
        "patches": {
            "drupal/core": "https://cdn.rawgit.com/wieni/wmmodel/0.2.1/src/Patch/core/wmmodel.patch"
        }
    }
}
```

Clear the cache

```
drush cr
drush en wmmodel
drush model-list
```

## Creating Models

A model has to implement `ContentEntityInterface` and `WmModelInterface`.

It's recommended to extend `Drupal\node\Entity\Node`, `Drupal\taxonomy\Entity\Term` or `Drupal\eck\Entity\EckEntity` depending on what kind of model you are making.

Implement `Drupal\wmmodel\Entity\Interfaces\WmModelInterface`. This forces you to define a static method called `getModelInfo()` that will return an array with the `EntityType` and `BundleName` of your model.

The trait `Drupal\wmmodel\Entity\Traits\WmModel` can do this for you.

> Follow the `<module>/Entity/<storage>/<bundle>` namespace convention to make your life easier.

### Example model

```
<?php

namespace Drupal\mymodule\Entity\Node;

use Drupal\node\Entity\Node;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Drupal\wmmodel\Entity\Traits\WmModel;

class Article extends Node implements WmModelInterface
{
    use WmModel;
}
```

> Create an Abstract[EntityTypeId] class for each of your types.
> It takes about 5 minutes at the start of your project.
>
> `src/Entity/Node/NodeModel`
> 
> `src/Entity/TaxonomyTerm/TermModel`
>
> `src/Entity/Subcontent/SubcontentModel`

## Mapping

### List all bundles and check if a model exists

Use the drush command `drush model-list` or `drush wml` to list all bundles and their model.

### Registering models
Use the `hook_entity_model_mapping()` hook to map your models.

```
function mymodule_entity_model_mapping()
{
    // Add mappings
    $mapping['node.article'] = \Drupal\mymodule\Entity\Node\Article::class;
    $mapping['subcontent.product'] = \Drupal\mymodule\Entity\Subcontent\Product::class;

    return $mapping;
}
```

When you follow the `<module>/Entity/<storage>/<bundle>` namespace convention you can use a service that will automatically find your models.

```
function mymodule_entity_model_mapping()
{
    $modelFinder = \Drupal::service('wmmodel.service.finder');
    return $modelFinder->findModels('mymodule');
}
```

### Eck

Eck is an awesome module. I prefer to rest my eck entities in a `src/Entity/Eck/` directory. However this means breaking from my own convention.

To get around this you can create an **abstract** class that overrides how this module deduces the EntityType and Bundle from a namespace.

```
// src/Entity/Eck/EckModel.php
<?php

namespace Drupal\mymodule\Entity\Eck;

use Drupal\eck\Entity\EckEntity;
use Drupal\wmmodel\Entity\Interfaces\WmModelInterface;
use Drupal\wmmodel\Entity\Traits\WmModel;

abstract class EckModel extends EckEntity implements WmModelInterface
{
    use WmModel;

    protected static function bundleDeduceRegex()
    {
        return '#/Entity/Eck/(.*?)/(.*?)$#';
    }

}
```

And then have your Eck models extend this class

```
// src/Entity/Eck/Subcontent/Product.php
<?php

namespace Drupal\mymodule\Entity\Eck\Subcontent;

use Drupal\mymodule\Entity\Eck\EckModel;

class Product extends EckModel
{
}
```

### Altering mapped models

The `hook_entity_model_mapping_alter` hook can be used to alter registered models by other modules.
 
```
function mymodule_entity_model_mapping_alter(&$mapping)
{
    // Map a bundle "article" with entity type "node" to a custom class
    $mapping['node.article'] = 'Drupal\mymodule\Entity\Node\Article';
}
```

## Cool stuff you should do

### PreSave / PostSave

I don't like having bundle-specific _presave _postsave _predelete stuff in my module files.
It makes more sense to have it at the Model.
 
```
 // Drupal\mymodule\Entity\Node\Article
 
 public function postSave(EntityStorageInterface $storage, $update = true)
 {
    parent::postSave($storage, $update);
    
    // Do whatever you have to do after a save
    // Send events, queue mails, whatever
 }
```

### Bundle-specific controllers

Team up this module with [wieni/wmcontroller](https://github.com/wieni/wmcontroller) and use bundle-specific controllers with models!

```
// src/Controller/Node/ArticleController.php
<?php

namespace Drupal\mymodule\Controller\Node;

use Drupal\wmcontroller\Controller\ControllerBase;
use Drupal\mymodule\Entity\Node\Article;

class ArticleController extends ControllerBase
{

    public function show(Article $article)
    {
        // Whatever logic you need to perform in order
        // to render an Article
    }

}
```