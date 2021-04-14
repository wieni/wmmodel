<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\TimestampItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

trait FieldHelpers
{
    protected function getDateTime(string $fieldName): ?\DateTimeInterface
    {
        $dateTimes = $this->getDateTimes($fieldName);

        return reset($dateTimes) ?: null;
    }

    /** @return \DateTimeInterface[] */
    protected function getDateTimes(string $fieldName): array
    {
        if (!$this->hasField($fieldName) || $this->get($fieldName)->isEmpty()) {
            return [];
        }

        $dateTimes = [];

        foreach ($this->get($fieldName) as $field) {
            if ($field instanceof TimestampItem) {
                $timestamp = $field->value;
            }

            if ($field instanceof DateTimeItem) {
                if (!$date = $field->date) {
                    continue;
                }

                $timestamp = $date->format('U');
            }

            if (!isset($timestamp)) {
                throw new \InvalidArgumentException(
                    sprintf('FieldHelpers::getDateTimes cannot deal with %s fields.', $field->getFieldDefinition()->getType())
                );
            }

            $dateTimes[] = \DateTime::createFromFormat('U', $timestamp)
                ->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        return $dateTimes;
    }

    protected function setDateTime(string $fieldName, \DateTimeInterface $dateTime): self
    {
        return $this->setDateTimes($fieldName, [$dateTime]);
    }

    /** @param \DateTimeInterface[] $dateTimes */
    protected function setDateTimes(string $fieldName, array $dateTimes): self
    {
        $fieldDefinition = $this->get($fieldName)->getFieldDefinition();
        $fieldType = $fieldDefinition->getType();

        if (in_array($fieldType, ['created', 'changed', 'timestamp'], true)) {
            $storageFormat = 'U';
        }

        if ($fieldType === 'datetime') {
            $datetimeType = $fieldDefinition->getSetting('datetime_type');
            $storageFormat = $datetimeType === DateTimeItem::DATETIME_TYPE_DATE
                ? DateTimeItemInterface::DATE_STORAGE_FORMAT
                : DateTimeItemInterface::DATETIME_STORAGE_FORMAT;
        }

        if (!isset($storageFormat)) {
            throw new \InvalidArgumentException(
                sprintf('FieldHelpers::setDateTime cannot deal with %s fields.', $fieldType)
            );
        }

        return $this->set($fieldName, array_map(
            static function (\DateTimeInterface $dateTime) use ($storageFormat) {
                return $dateTime->format($storageFormat);
            },
            $dateTimes
        ));
    }

    protected function formatLinks(string $fieldName): array
    {
        $links = [];

        if (!$this->hasField($fieldName) || $this->get($fieldName)->isEmpty()) {
            return $links;
        }

        foreach ($this->get($fieldName) as $value) {
            $links[] = $this->formatLinkItem($value);
        }

        return $links;
    }

    protected function formatLink(string $fieldName): array
    {
        $link = [
            'url' => '',
            'text' => '',
            'external' => false,
            'target' => '_self',
        ];

        /** @var LinkItem $item */
        if (!$this->hasField($fieldName) || $this->get($fieldName)->isEmpty()) {
            return $link;
        }

        return $this->formatLinkItem(
            $this->get($fieldName)->first()
        );
    }

    private function formatLinkItem(LinkItem $item): array
    {
        $link = [
            'url' => '',
            'text' => '',
            'external' => false,
        ];

        if (!$item->isExternal() && $item->getUrl()->isRouted() && in_array($item->getUrl()->getRouteName(), ['<nolink>', '<none>'])) {
            $url = '';
        } elseif (!$item->isExternal() && $entity = $this->getReferencedEntityFromLink($item)) {
            $url = $entity->toUrl()->toString();
        } else {
            $url = $item->getUrl()->toString();
        }

        $link['url'] = $url;
        $link['text'] = $item->title ?? '';
        $link['external'] = $item->isExternal();

        return $link;
    }

    private function getReferencedEntityFromLink(LinkItem $link): ?EntityInterface
    {
        $uri = explode(':', $link->uri, 2);
        if ($uri[0] !== 'entity' || count($uri) !== 2) {
            return null;
        }

        [$entityTypeId, $entityId] = explode('/', $uri[1], 2);

        $entity = $this->entityTypeManager()
            ->getStorage($entityTypeId)
            ->load($entityId);

        return $this->translateEntity($entity);
    }
}
