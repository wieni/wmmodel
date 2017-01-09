<?php

namespace Drupal\wmmodel\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeBundleInfo as CoreBundleInfo;

class EntityTypeBundleInfo extends CoreBundleInfo
{
    /**
     * {@inheritdoc}
     */
    public function getAllBundleInfo()
    {
        if (!empty($this->bundleInfo)) {
            return $this->bundleInfo;
        }

        $langcode = $this->languageManager->getCurrentLanguage()->getId();
        if ($cache = $this->cacheGet("wmmodel_entity_bundle_info:$langcode")) {
            return $this->bundleInfo = $cache->data;
        }

        $this->bundleInfo = $this->moduleHandler->invokeAll('entity_bundle_info');
        foreach ($this->entityTypeManager->getDefinitions() as $type => $entity_type) {
            if (!$bundle_entity_type = $entity_type->getBundleEntityType()) {
                continue;
            }
            $bundles = $this->entityTypeManager->getStorage($bundle_entity_type)->loadMultiple();
            foreach ($bundles as $bundle) {
                $this->bundleInfo[$type][$bundle->id()]['label'] = $bundle->label();
            }
        }
        $this->moduleHandler->alter('entity_bundle_info', $this->bundleInfo);
        $this->cacheSet(
            "wmmodel_entity_bundle_info:$langcode",
            $this->bundleInfo,
            Cache::PERMANENT,
            ['entity_types', 'entity_bundles']
        );

        return $this->bundleInfo;
    }
}