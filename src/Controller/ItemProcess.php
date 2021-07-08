<?php

namespace Drupal\dms_instance\Controller;

use Drupal\Core\Database\Database;
use Drupal\dms_instance\Queue\DMSInstanceQueue;

class ItemProcess extends ControllerBase {


  public function processJobReturn() {
    $item_id = \Drupal::routeMatch()->getParameter('item_id');
    $aegir_instance = \Drupal::routeMatch()->getParameter('aegir_instance');
    $queue = new DMSInstanceQueue($aegir_instance, Database::getConnection());
    $item = new \stdClass();
    $item->item_id = $item_id;
    $queue->deleteItem($item);
  }

}
