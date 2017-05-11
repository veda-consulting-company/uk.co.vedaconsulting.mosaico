<?php
return array(
  'mosaico_layout' => array(
    'group_name' => 'Mosaico Preferences',
    'group' => 'mosaico',
    'name' => 'mosaico_layout',

    'type' => 'String',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'callback' => 'CRM_Mosaico_Utils::getLayoutOptions',
    ),
    'default' => 'bootstrap-wizard',
    'add' => '4.7',
    'title' => 'Mosaico editor layout',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => NULL,
    'help_text' => NULL,
  ),
);
