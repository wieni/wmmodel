<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\TypedData\TranslatableInterface;

trait EntityTranslatorTrait
{
    /** @return EntityInterface[] */
    protected function translateEntities(array $entities = [], ?string $langcode = null, bool $strict = true): array
    {
        $translated = [];
        foreach ($entities as $key => $entity) {
            $translated[$key] = $this->translateEntity($entity, $langcode, $strict);
        }

        return array_filter($translated);
    }

    protected function translateEntity(?EntityInterface $entity, ?string $langcode = null, bool $strict = true): ?EntityInterface
    {
        if (!$langcode) {
            $langcode = \Drupal::languageManager()
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
            return $strict ? null : $entity;
        }

        return $entity->getTranslation($langcode);
    }
}
