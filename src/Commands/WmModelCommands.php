<?php

namespace Drupal\wmmodel\Commands;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmodel\ModelPluginManager;
use Drush\Commands\DrushCommands;

class WmModelCommands extends DrushCommands
{
    use StringTranslationTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var ModelPluginManager */
    protected $pluginManager;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        ModelPluginManager $pluginManager
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->pluginManager = $pluginManager;
    }

    /**
     * List all bundles and their mapping
     *
     * @command wmmodel:list
     * @aliases model-list,wml
     */
    public function listModels()
    {
        foreach ($this->entityTypeManager->getDefinitions() as $entityType) {
            if (!$bundleEntityType = $entityType->getBundleEntityType()) {
                continue;
            }

            if (!$entityType instanceof ContentEntityTypeInterface) {
                continue;
            }

            $bundles = $this->entityTypeManager
                ->getStorage($bundleEntityType)
                ->getQuery()
                ->execute();

            foreach ($bundles as $bundle) {
                $id = implode('.', [$entityType->id(), $bundle]);

                if ($this->pluginManager->hasDefinition($id)) {
                    $message = $this->t('Model "@model" is mapped against "@mapping".', [
                        '@model' => $id,
                        '@mapping' => $this->pluginManager->getDefinition($id)['class'],
                    ]);
                } else {
                    $message = $this->t('Model "@model" is not mapped.', [
                        '@model' => $id,
                    ]);
                }

                $this->io()->text($message);
            }
        }
    }
}
