<?php
use CRM_Mosaico_ExtensionUtil as E;

return [
  'name' => 'MosaicoMsgTemplate',
  'table' => 'civicrm_mosaico_msg_template',
  'class' => 'CRM_Mosaico_DAO_MosaicoMsgTemplate',
  'getInfo' => fn() => [
    'title' => E::ts('Mosaico Message Template'),
    'title_plural' => E::ts('Mosaico Message Templates'),
    'description' => E::ts('Mosaico Templates Table'),
    'log' => TRUE,
  ],
  'getFields' => fn() => [
    'id' => [
      'title' => E::ts('ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Number',
      'required' => TRUE,
      'description' => E::ts('Unique Settings ID'),
      'primary_key' => TRUE,
      'auto_increment' => TRUE,
    ],
    'msg_tpl_id' => [
      'title' => E::ts('message template ID'),
      'sql_type' => 'int unsigned',
      'input_type' => 'Select',
      'required' => TRUE,
      'description' => E::ts('FK to civicrm_msg_template.'),
      'pseudoconstant' => [
        'table' => 'civicrm_msg_template',
        'key_column' => 'id',
        'label_column' => 'msg_title',
      ],
      'entity_reference' => [
        'entity' => 'MessageTemplate',
        'key' => 'id',
        'on_delete' => 'CASCADE',
      ],
    ],
    'hash_key' => [
      'title' => E::ts('hash_key'),
      'sql_type' => 'varchar(32)',
      'input_type' => 'Text',
      'required' => TRUE,
    ],
    'name' => [
      'title' => E::ts('name'),
      'sql_type' => 'varchar(32)',
      'input_type' => 'Text',
      'required' => TRUE,
      'description' => E::ts('name'),
    ],
    'html' => [
      'title' => E::ts('HTML'),
      'sql_type' => 'longtext',
      'input_type' => 'RichTextEditor',
      'required' => TRUE,
      'description' => E::ts('HTML'),
      'input_attrs' => [
        'rows' => 10,
        'cols' => 75,
      ],
    ],
    'metadata' => [
      'title' => E::ts('metadata'),
      'sql_type' => 'longtext',
      'input_type' => 'RichTextEditor',
      'required' => TRUE,
      'description' => E::ts('metadata'),
      'input_attrs' => [
        'rows' => 10,
        'cols' => 75,
      ],
    ],
    'template' => [
      'title' => E::ts('template'),
      'sql_type' => 'longtext',
      'input_type' => 'RichTextEditor',
      'required' => TRUE,
      'description' => E::ts('template'),
      'input_attrs' => [
        'rows' => 10,
        'cols' => 75,
      ],
    ],
  ],
];
