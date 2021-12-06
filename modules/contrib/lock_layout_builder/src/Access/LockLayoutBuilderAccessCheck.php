<?php

namespace Drupal\lock_layout_builder\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\content_lock\ContentLock\ContentLock;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * Checks for the concurrent user on layout page and provide access accordingly.
 */
class LockLayoutBuilderAccessCheck implements AccessInterface {

  /**
   * Drupal\Core\Language\LanguageManagerInterface definition.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Drupal\content_lock\ContentLock\ContentLock definition.
   *
   * @var \Drupal\content_lock\ContentLock\ContentLock
   */
  protected $contentLock;

  /**
   * Constructs a new LockLayoutBuilderAccess object.
   */
  public function __construct(LanguageManagerInterface $language_manager, ContentLock $content_lock) {
    $this->languageManager = $language_manager;
    $this->contentLock = $content_lock;
  }

  /**
   * Permission to access the page.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   RouterMatch object.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User associated currently to page.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Access allowed if no user is concurrently editing layout page.
   */
  public function access(RouteMatchInterface $route_match, AccountInterface $account) {
    $section_storage = $route_match->getParameter('section_storage');

    $node_id = '';
    $entity_type = '';
    $lang_code = $this->languageManager->getCurrentLanguage()->getId();
    if (array_key_exists('entity', $section_storage->getContexts())) {
      $entity_context = $section_storage->getContext('entity');
      if (!empty($entity_context) && $entity_context instanceof EntityContext) {
        $context_data = $entity_context->getContextData();
        if (!empty($context_data)) {
          $entity_data = $context_data->getEntity();
          if (!empty($entity_data) && $entity_data instanceof NodeInterface) {
            $node_id = $entity_data->id();
            $entity_type = $entity_data->getEntityTypeId();
          }
        }
      }
    }

    if (!empty($entity_type) && !empty($node_id) && $entity_type == 'node') {
      if (!$this->contentLock->isLockedBy($node_id, $lang_code, 'layout_builder', $account->id(), $entity_type)) {
        $access = AccessResult::forbidden();
      }
      else {
        $access = AccessResult::allowed();
      }
    }
    else {
      $access = AccessResult::allowed();
    }

    return $access->addCacheableDependency($section_storage);
  }

}
