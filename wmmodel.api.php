<?php

function hook_wmmodel_model_info_alter(array &$definitions)
{
    $definitions['node.page']['class'] = \Drupal\my_module\Entity\Model\Node\Page::class;
}
