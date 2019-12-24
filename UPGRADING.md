# Upgrade Guide

This document describes breaking changes and how to upgrade. For a complete list of changes including minor and patch releases, please refer to the [`CHANGELOG`](CHANGELOG.md).

## Unreleased
More information about breaking changes, removed functionality and their
alternatives is provided in the [Upgrade Guide](UPGRADING.md).

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
