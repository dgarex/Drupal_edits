<?php

namespace Drupal\lock_layout_builder\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class LockLayoutBuilderRouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class LockLayoutBuilderRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $routes = [
      'layout_builder.choose_section' => 'delta',
      'layout_builder.configure_section_form' => 'delta',
      'layout_builder.add_section' => 'delta',
      'layout_builder.configure_section' => 'delta',
      'layout_builder.remove_section' => 'delta',
      'layout_builder.choose_block' => 'delta',
      'layout_builder.add_block' => 'delta',
      'layout_builder.choose_inline_block' => 'delta',
      'layout_builder.move_block_form' => 'delta',
      'layout_builder.update_block' => 'delta',
      'layout_builder.remove_block' => 'delta',
      'layout_builder.move_block' => 'delta_from',
    ];

    foreach ($routes as $route_name => $access) {
      if ($route = $collection->get($route_name)) {
        $route->setRequirement('_lock_layout_builder_access', $access);
      }
    }
  }

}
