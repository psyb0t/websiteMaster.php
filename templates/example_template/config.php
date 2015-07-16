<?php
function wm_templateConfig() {
  $config = [];
  
  $config['required_data'] = [
    'general' => [
      'site_title' => '',
      'robots' => ''
    ],
    'content' => [
      'homepage' => [
        'meta_description' => '',
        'meta_keywords' => '',
        'dynamic_word1' => '',
        'dynamic_word2' => ''
      ]
    ]
  ];
  
  $config['rewrite_rules'] = [
    ['page' => '/^(\w+)$/'],
    ['pageArg' => '/^([a-z0-9]+)$/i']
  ];
  
  return $config;
}
?>
