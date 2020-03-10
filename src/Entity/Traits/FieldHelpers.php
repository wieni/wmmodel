<?php

namespace Drupal\wmmodel\Entity\Traits;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

trait FieldHelpers
{
    protected function getDateTime(string $fieldName): ?\DateTimeInterface
    {
        if (!$this->hasField($fieldName) || $this->get($fieldName)->isEmpty()) {
            return null;
        }

        if (!($date = $this->get($fieldName)->date) && !($date = $this->get($fieldName)->value)) {
            return null;
        }

        if ($date instanceof DrupalDateTime) {
            $date = $date->format('U');
        }

        $timezone = new \DateTimeZone(date_default_timezone_get());
        $dateTime = \DateTime::createFromFormat('U', $date, $timezone);

        if (!$dateTime) {
            return null;
        }

        return $dateTime;
    }

    protected function setDateTime(string $fieldName, \DateTimeInterface $dateTime): self
    {
        $datetimeType = $this->get($fieldName)->getFieldDefinition()->getSetting('datetime_type');
        $storageFormat = $datetimeType === DateTimeItem::DATETIME_TYPE_DATE
            ? DateTimeItemInterface::DATE_STORAGE_FORMAT
            : DateTimeItemInterface::DATETIME_STORAGE_FORMAT;

        $this->set($fieldName, $dateTime->format($storageFormat));

        return $this;
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
