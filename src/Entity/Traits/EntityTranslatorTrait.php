<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\TranslatableInterface;

trait EntityTranslatorTrait
{
    /** @return EntityInterface[] */
    protected function translateEntities(array $entities = [], $lancode = null): array
    {
        $translated = [];
        foreach ($entities as $key => $entity) {
            $translated[$key] = $this->translateEntity($entity, $lancode);
        }

        return array_filter($translated);
    }

    protected function translateEntity(?EntityInterface $entity, ?string $langcode = null): ?EntityInterface
    {
        if (!$langcode) {
            $langcode = $this->languageManager()
                ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
                ->getId();
        }

        if (
            !$entity
            || !$entity instanceof TranslatableInterface
            || $entity->language()->getId() === $langcode
        ) {
            return $entity;
        }

        if (!$entity->hasTranslation($langcode)) {
            return null;
        }

        return $entity->getTranslation($langcode);
    }
}
