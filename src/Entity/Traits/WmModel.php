<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\Exception\NoCorrespondingEntityClassException;
use Drupal\Core\Entity\Plugin\DataType\EntityReference;
use Drupal\taxonomy\Entity\Term;

trait WmModel
{
    protected static $storageMapping = [];

    public static function create(array $values = [])
    {
        try {
            return parent::create($values);
        } catch (NoCorrespondingEntityClassException $e) {
            [$entityType, $bundleName] = static::getModelInfo();
            if (empty($entityType) || empty($bundleName)) {
                throw $e;
            }

            // Set the bundle type
            $type = (is_subclass_of(static::class, Term::class)) ? 'vid' : 'type';
            $values[$type] = $bundleName;

            return \Drupal::entityTypeManager()
                ->getStorage($entityType)
                ->create($values);
        }
    }

    /**
     * Get the entityType and bundle of this model
     *
     * @return array
     */
    public static function getModelInfo()
    {
        if (isset(static::$storageMapping[static::class])) {
            return static::$storageMapping[static::class];
        }

        $match = [];
        $class = str_replace('\\', '/', static::class);
        // Get the entityType and bundlename from the namespace
        preg_match(static::bundleDeduceRegex(), $class, $match);

        if ($match) {
            $entityType = static::snake($match[1]);
            $bundleName = static::snake($match[2]);
        }

        // Allow for modules to overwrite h
        $entityType = static::entityType() ?: $entityType ?? '';
        $bundleName = static::bundleName() ?: $bundleName ?? '';

        if (empty($entityType) || empty($bundleName)) {
            throw new \RuntimeException(sprintf(
                "Can't deduce entityType or bundle name from class %s" . PHP_EOL
                . "Got entityType: '%s' , bundle: '%s'" . PHP_EOL
                . 'Overwrite %s or %s',
                static::class,
                $entityType,
                $bundleName,
                'static::entityType()',
                'static::bundleName()'
            ));
        }

        static::$storageMapping[static::class] = [$entityType, $bundleName];

        return [$entityType, $bundleName];
    }

    /**
     * Get cache keys of referenced entities without instantiating them
     * @return string[]
     */
    public function getReferencedEntitiesCacheTags()
    {
        $referenced_tags = [];
        foreach ($this->getFields() as $field_items) {
            foreach ($field_items as $field_item) {
                // Loop over all properties of a field item.
                foreach ($field_item->getProperties(true) as $property) {
                    if (!$property instanceof EntityReference) {
                        continue;
                    }

                    $id = $property->getTargetIdentifier();
                    $entityType = $property->getTargetDefinition()->getEntityTypeId();

                    if (is_numeric($id) && $entityType != 'user') {
                        $referenced_tags[] = $entityType . ':' . $id;
                    }
                }
            }
        }
        return $referenced_tags;
    }

    /**
     * The entityTypeId of this entity
     * @return string
     */
    protected static function entityType()
    {
        return '';
    }

    /**
     * The bundle name of this entity
     * @return string
     */
    protected static function bundleName()
    {
        return '';
    }

    /**
     * A regex that extracts the entityType and bundle name from a class name
     * @return string
     */
    protected static function bundleDeduceRegex()
    {
        return '#/Entity/(.*?)/(.*?)$#';
    }

    /**
     * Convert a string to snake case.
     * Taken from illuminate/support
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    private static function snake($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = mb_strtolower(
                preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value),
                'UTF-8'
            );
        }

        return $value;
    }
}
