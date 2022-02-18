# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0]
### Removed
- Remove EntityBundleConverter

## [2.1.0]
### Changed
- Make overriding AccountProxy optional
- Make argument resolving of FormStateInterface optional

## [2.0.0]
### Added
- Add PHP 8 support

### Changed
- Increase minimum Drupal core version to 9.3 due to entity bundle class support
- Increase minimum PHP requirement to 7.3 due to Drupal core PHP requirement

### Removed
- Remove `WmModelInterface` and `WmModel` trait
- Remove `ModelFactory` (`wmmodel.factory.model`) and `ModelFactoryInterface`

## [1.6.2] - 2022-01-07
### Fixed
- Support overriding all entity type classes, except if the @Model annotation has a bundle. The motivation for this 
  change is [this issue](https://www.drupal.org/project/eck/issues/3257431).

## [1.6.1] - 2021-12-06
### Fixed
- Set storage timezone before storing datetime fields using `FieldHelpers::setDateTime(s)`

## [1.6.0] - 2021-10-31
### Added
- Add `FieldHelpers::getMediaSource` method to get the source field item list of a referenced media entity.

## [1.5.0] - 2021-10-22
### Added
- Add entity bundle route param converter

### Fixed
- Stop resolving null values for non-optional controller arguments

## [1.4.1] - 2021-09-02
### Fixed
- Remove `self` return type from `FieldHelpers` methods

## [1.4.0] - 2021-07-06
### Changed
- Add back loaded entity to link item array

### Fixed
- Fix `ModelValueResolver::resolve()` must yield at least one value.

## [1.3.9] - 2021-04-29
### Fixed
- Fix `formatLink` field helper with nonexistent internal links

## [1.3.8] - 2021-04-28
### Fixed
- Fix `formatLink` field helper with links to fragments on the same page

## [1.3.7] - 2021-04-19
### Fixed
- Fix _Call to undefined method languageManager()_ when using `EntityTranslatorTrait`

## [1.3.6] - 2021-04-14
### Added
- Add getDateTimes & setDateTimes field helpers

## [1.3.5] - 2021-03-26
### Fixed
- Fix controller methods with optional arguments

## [1.3.4] - 2021-03-06
### Added
- Add support for EntityInterface::load

## [1.3.3] - 2021-03-01
### Added
- Add support for timestamp fields to FieldHelpers methods

## [1.3.2] - 2020-10-27
### Fixed
- Return Datetime in correct timezone

## [1.3.1] - 2020-07-23
### Added
- Add docblocks to WmModel trait methods

## [1.3.0] - 2020-07-23
### Added
- Add support for Drupal 9

## [1.2.0] - 2020-06-04
### Added
- Add basic test coverage
- Add support for changing class of entities without bundles. The bundle annotation parameter is now no longer required
 for entity types without bundles, e.g. `Drupal\user\Entity\User`

### Changed
- Change minimum required core version to 8.6
- Mention related core issue in ModelValueResolver
- Add composer.lock to .gitignore

## [1.1.4] - 2020-03-26
### Changed
- Update wmmodel.api.php

### Fixed
- Update [`EntityTranslatorTrait::translateEntities`](src/Entity/Traits/EntityTranslatorTrait.php) arguments.

## [1.1.3] - 2020-03-10
### Fixed
- Fix [`FieldHelpers::setDateTime`](src/Entity/Traits/FieldHelpers.php) method not working when `datetime_type` equals `date`.

## [1.1.2] - 2020-03-05
### Fixed
- Change [`ModelFactoryInterface::getEntityTypeAndBundle`](src/Factory/ModelFactoryInterface.php) to also consider entity type class

## [1.1.1] - 2020-03-04
### Added
- Add argument resolving for FormState so we can typehint $formState instead of $form_state

## [0.3.7] - 2020-03-04
### Added
- Add argument resolving for FormState so we can typehint $formState instead of $form_state

## [1.1.0] - 2020-02-05
More information about breaking changes, removed functionality and their
alternatives is provided in the [Upgrade Guide](UPGRADING.md).

### Added
- Add getDateTime, setDateTime, formatLinks & formatLink field helpers
- Add EntityTranslatorTrait to easily translate entities
- Add bundle-specific EntityInterface::loadMultiple implementation

### Removed
- Remove removeReference & removeFromList field helpers

## [1.0.0] - 2020-01-31
More information about breaking changes, removed functionality and their
alternatives is provided in the [Upgrade Guide](UPGRADING.md).

### Added
- Add Drush 10 support
- Add php & drupal/core version requirements
- Add coding style fixers
- Add issue & pull request templates
- Add getEntityTypeAndBundle method to ModelFactoryInterface

### Changed
- Replace manual class mapping with Drupal plugins
- Replace deprecated ControllerResolver with an ArgumentValueResolver
  ([Drupal core issue](https://www.drupal.org/node/2959408))
- Normalize composer.json
- Update .gitignore
- Update README
- Apply code style related fixes

### Removed
- Remove Drush 8 support
- Remove `wmmodel.entity_type.bundle.info` service
- Remove eck & node dependencies

## [0.3.6] - 2019-06-28
### Added
- Add rebuildMapping method to ModelFactory

## [0.3.5] - 2019-06-28
### Fixed
- Fix issue where class file exists but is not loaded

## [0.3.4] - 2019-03-19
### Added
- Add Drush services declaration to composer.json

## [0.3.3] - 2018-04-17
### Changed
- Fix code style

## [0.3.2] - 2018-03-27
### Added
- Add Drush 9 compatibility

## [0.3.1] - 2018-03-05
### Fixed
- Fix issue with controller argument resolver

## [0.3.0] - 2018-02-13
### Changed
- Store class mapping in state instead of in cache

## [0.2.8] - 2017-09-01
### Removed
- Remove optimization

## [0.2.7] - 2017-08-10
### Changed
- AccountProxy->getAccount() now returns a fully loaded user model

## [0.2.6] - 2017-06-20
### Removed
- Remove the singularizing of bundles

## [0.2.5] - 2017-04-07
### Fixed
- Updated the drupal/core patch

## [0.2.4] - 2017-02-27
### Fixed
- Fix Model::create() for Taxonomy terms

## [0.2.3] - 2017-01-30
### Changed
- Make getReferencedEntitiesCacheTags method public

## [0.2.2] - 2017-01-18
### Removed
- Remove override of getCacheTags on entity classes

## [0.2.1] - 2017-01-11
### Changed
- Call the correct model class on preCreate, postLoad, preDelete and postDelete

## [0.2.0] - 2017-01-09

## [0.1.2] - 2017-01-06
### Added
- Use custom controller resolver to inject models into controller method

## [0.1.1] - 2017-01-05
### Added
- Add drupal/core patch

## [0.1.0] - 2017-01-04
Initial release
