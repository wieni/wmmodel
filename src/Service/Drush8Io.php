<?php

namespace Drupal\wmmodel\Service;

use Psr\Log\LogLevel;

/**
 * Class Drush8Io.
 *
 * This is a stand in for \Symfony\Component\Console\Style\StyleInterface with
 * Drush 8 so that we don't need to depend on Symfony components.
 */
class Drush8Io
{
    public function confirm($text)
    {
        return drush_confirm($text);
    }

    public function success($text)
    {
        drush_log($text, LogLevel::INFO);
    }

    public function error($text)
    {
        drush_log($text, LogLevel::ERROR);
    }

    public function text($text)
    {
        drush_log($text, LogLevel::NOTICE);
    }
}
