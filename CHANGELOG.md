# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
### Added
- Add getDateTime, setDateTime, formatLinks & formatLink field helpers
- Add EntityTranslatorTrait to easily translate entities
- Add bundle-specific EntityInterface::loadMultiple implementation

### Removed
- Remove removeReference & removeFromList field helpers

## [1.0.0] - 2020-01-31
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
