<?php

namespace Drupal\dms_instance\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the DMS Instance entity.
 *
 * @ingroup dms_instance
 *
 * @ContentEntityType(
 *   id = "dms_instance",
 *   label = @Translation("DMS Instance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dms_instance\DmsInstanceEntityListBuilder",
 *     "views_data" = "Drupal\dms_instance\Entity\DmsInstanceEntityViewsData",
 *     "translation" = "Drupal\dms_instance\DmsInstanceEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dms_instance\Form\DmsInstanceEntityForm",
 *       "add" = "Drupal\dms_instance\Form\DmsInstanceEntityForm",
 *       "edit" = "Drupal\dms_instance\Form\DmsInstanceEntityForm",
 *       "delete" = "Drupal\dms_instance\Form\DmsInstanceEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dms_instance\DmsInstanceEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dms_instance\DmsInstanceEntityAccessControlHandler",
 *   },
 *   base_table = "dms_instance",
 *   data_table = "dms_instance_field_data",
 *   translatable = TRUE,
 *   revision_table = "dms_instance_revision",
 *   admin_permission = "administer dms instance entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "label" = "instance_prefix",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/dms/dms_instance/{dms_instance}",
 *     "add-form" = "/admin/dms/dms_instance/add",
 *     "edit-form" = "/admin/dms/dms_instance/{dms_instance}/edit",
 *     "delete-form" = "/admin/dms/dms_instance/{dms_instance}/delete",
 *     "collection" = "/admin/dms/dms_instance",
 *   },
 *   field_ui_base_route = "dms_instance.settings"
 * )
 */
class DmsInstanceEntity extends ContentEntityBase implements DmsInstanceEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('instance_prefix')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('instance_prefix', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  public function getAegirInstance() {
    return $this->get('aegir_instance')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the DMS Instance entity.'))
      ->setReadOnly(TRUE);
    $fields['instance_prefix'] = BaseFieldDefinition::create('string')
      ->setLabel(t('DMS Instance Prefix'))
      ->setDescription(t('The DMS Instance Prefix (i.e. part before .canadahelps.org in the url.'))
      ->setRequired(TRUE)
      ->setStorageRequired(TRUE)
      ->setSettings([
        'default_value' => '',
        'max_length' => 10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ]);
    $fields['dns_created_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('DNS Created Date'))
      ->setDescription(t('Date DNS record was created'))
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
      ]);
    $fields['email_address'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS Email Address'))
    ->setDescription(t('The email address from gmail associated with this DMS instance'))
    ->setRequired(FALSE)
    ->setStorageRequired(TRUE)
    ->setSettings([
      'default_value' => '',
      'max_length' => 40,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['email_name'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS Email Address username'))
    ->setDescription(t('User name to login to the DMS Email inbox'))
    ->setRequired(FALSE)
    ->setStorageRequired(TRUE)
    ->setSettings([
      'default_value' => '',
      'max_length' => 40,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['email_password'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS Email Address password'))
    ->setDescription(t('Password to login to the DMS Email inbox'))
    ->setRequired(FALSE)
    ->setStorageRequired(TRUE)
    ->setSettings([
      'default_value' => '',
      'max_length' => 40,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['email_created_date'] = BaseFieldDefinition::create('timestamp')
    ->setLabel(t('Email Created Date'))
    ->setDescription(t('Date Email Address was created'))
    ->setDisplayOptions('form', [
      'type' => 'datetime_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('view', [
      'type' => 'datetime_default',
      'weight' => 0,
    ]);
  $fields['smtp_service'] = BaseFieldDefinition::create('list_string')
    ->setLabel(t('DMS Outbound SMTP Service'))
    ->setDescription(t('SMTP service used by the DMS to send emails out'))
    ->setRequired(FALSE)
    ->setStorageRequired(TRUE)
    ->setSettings([
      'max_length' => 40,
      'allowed_values' => ['sendgrid' => 'SendGrid', 'manual' => 'Manual'],
      'default_value' => 'sendgrid',
    ])
    ->setDisplayOptions('view', [
      'type' => 'list_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'options_select',
      'weight' => 0,
    ]);
  $fields['smtp_apikey'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS SMTP API Key'))
    ->setDescription(t('SMTP API Key to use with SendGrid'))
    ->setSettings([
      'max_length' => 255,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['smtp_password'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS SMTP password'))
    ->setDescription(t('Password to connect to the SMTP service'))
    ->setSettings([
      'max_length' => 40,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['smtp_created_date'] = BaseFieldDefinition::create('timestamp')
    ->setLabel(t('SMTP Created Date'))
    ->setDescription(t('Date SMTP credentials was created'))
    ->setDisplayOptions('form', [
      'type' => 'datetime_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('view', [
      'type' => 'datetime_default',
      'weight' => 0,
    ]);
  $fields['civicrm_site_key'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS Site Key'))
    ->setDescription(t('DMS Site key to be used for API queries to the DMS'))
    ->setSettings([
      'max_length' => 255,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['civicrm_api_key'] = BaseFieldDefinition::create('string')
    ->setLabel(t('DMS API Key'))
    ->setDescription(t('API Key for Rest API to DMS'))
    ->setSettings([
      'max_length' => 255,
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'text_default',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => 0,
    ]);
  $fields['instance_status'] = BaseFieldDefinition::create('list_integer')
    ->setLabel(t('DMS Instance Status'))
    ->setDescription(t('Current Status of the DMS instance'))
    ->setRequired(TRUE)
    ->setStorageRequired(TRUE)
    ->setSettings([
      'default_value' => 0,
      'max_length' => 40,
      'allowed_values' => [0 => 'Config Saved', 1 => 'Instance Created', 2 => 'CH Data Pushed', 3 => 'Config Pushed to Instance'],
    ])
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'number_integer',
      'weight' => 0,
    ])
    ->setDisplayOptions('form', [
      'type' => 'options_select',
      'weight' => 0,
    ]);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the DMS Instance entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['aegir_instance'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Aegir instance'))
      ->setDescription(t('Full domain name of the aegir instance'))
      ->setRequired(TRUE)
      ->setStorageRequired(TRUE)
      ->setSettings([
        'default_value' => 'aegir.canadahelps.org',
        'max_length' => 255,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ]);
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
