<?php

namespace Drupal\wmmodel\Commands;

use Drupal\wmmodel\Service\CliService;
use Drush\Commands\DrushCommands;

class WmModelCommands extends DrushCommands
{
    /** @var CliService */
    private $cliService;

    public function __construct(CliService $cliService)
    {
        $this->cliService = $cliService;
    }

    /**
     * List all bundles and their mapping
     *
     * @command wmmodel:list
     * @aliases model-list,wml
     */
    public function listModels()
    {
        $this->cliService->listModels($this->io(), 'dt');
    }
}
