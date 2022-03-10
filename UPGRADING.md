# Upgrade Guide

This document describes breaking changes and how to upgrade. For a complete list of changes including minor and patch releases, please refer to the [`CHANGELOG`](CHANGELOG.md).

## 3.0.0
### Module rename
A lot of classes, config, etc. have changed.

1. Install the new `entity_model` module

```bash
composer install drupal/entity_model
drush en entity_model
```

2. Deploy to all your environments

3. Use the bash script in `scripts/update-to-v3.sh` for an
   automatic upgrade of your project. Paths that have to be scanned should be passed as arguments:

```bash
chmod +x ./public/modules/contrib/wmmodel/scripts/update-to-v3.sh
./public/modules/contrib/wmmodel/scripts/update-to-v3.sh config/* public/modules/custom/* public/themes/custom/* public/sites/*
```

If you're using macOS, make sure to run this before the script:
```bash
brew install gnu-sed
PATH="$(brew --prefix gnu-sed)/libexec/gnubin:$PATH"
```

4. Apply any changes:

```bash
drush cr
drush cim -y
```

5. Deploy these changes to all your environments
6. Remove the old module files:

```bash
composer remove wieni/wmmodel
```

## 2.2.0
### EntityBundleConverter
Specifying bundles for the entity route parameter converter has been a Drupal core feature since 9.2 
([more info](https://www.drupal.org/node/3155568)). Since we now require Drupal 9.3 we can remove our own 
`EntityBundleConverter`.

#### Before
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

#### After
```yml
mymodule.company.suggestion:
    path: '/companies/{company}/suggestion'
    defaults:
        _controller: 'Drupal\wmcustom\Controller\Node\CompanyController::addSuggestion'
    options:
        parameters:
            company:
                type: 'entity:node'
                bundle:
                    - 'company'
    requirements:
      _access: 'TRUE'
```

## 2.0.0
### Core patch
The core patch that was previously necessary in order to make this module work will no longer apply to core versions of 
9.3.0 and above. You can safely remove it from your projects.

### `EntityInterface::loadMultiple`
`EntityInterface::loadMultiple`, when invoked on a bundle class, is no longer guaranteed to return only instances of the
same class. For example, when calling `BasicPage::loadMultiple([5])` and the node with ID 5 is an article, it will 
return the article instead of filtering it out. In the future this might be fixed in Drupal core, you can follow 
[this issue](https://www.drupal.org/project/drupal/issues/3252421) for any progress. 

### `EntityInterface::create`
`EntityInterface::create`, when invoked on a bundle class, no longer automatically adds the bundle to the `$values` array.

### `ModelFactory`
`ModelFactory` (`wmmodel.factory.model`) and `ModelFactoryInterface` were removed. The following snippets can be used instead:

```diff
-[$entityTypeId, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);
+$entityTypeId = $this->entityTypeRepository->getEntityTypeFromClass($class);
+$storage = $this->entityTypeManager->getStorage($entityTypeId);
+$bundle = $storage->getBundleFromClass($class);
```

```diff
-$className = $this->modelFactory->getClassName($entityType, $bundle);
+$className = $this->entityTypeManager->getStorage($entityType->id())->getEntityClass($bundle);
```

```diff
-$className = $this->modelFactory->rebuildMapping();
+$className = $this->modelPluginManager->clearCachedDefinitions();
```

## 1.1.0
The `removeReference` & `removeFromList` field helpers were removed. Copy them to your classes if you still need them.

## 1.0.0
### Drupal plugins
Model classes are now discovered using the Drupal plugin system instead
of relying on our own implementation. These are the implications: 

The `wmmodel.service.finder` service was removed because it
is no longer needed.

The implementation of `wmmodel.factory.model` was changed, but the
implicit public interface remains the same, except for the `bundle`
argument of the `getClassName` method to be typehinted and a new method
to be added, `getEntityTypeAndBundle`. Also, an explicit interface (
`ModelFactoryInterface`) was added, so typehints should use this instead
of the class.

The `getModelInfo` method was removed from `WmModelInterface` because it
is no longer needed. The `wmmodel.model.factory` service should now be
used instead:
```diff
-[$entityType, $bundle] = $class::getModelInfo();
+[$entityType, $bundle] = $this->modelFactory->getEntityTypeAndBundle($class);
```

### `wmmodel.entity_type.bundle.info`
The `wmmodel.entity_type.bundle.info` service was removed. The only
difference with the core `entity_type.bundle.info` service was that our
implementation only returned entity types with bundles instead of all
entity types. The service was only used in
[`WmModelCommands`](src/Commands/WmModelCommands.php), which has since
been refactored.
