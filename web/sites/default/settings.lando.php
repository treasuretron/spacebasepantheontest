<?php

if ($_SERVER['LANDO'] == 'ON') {
  $lando_info = json_decode(getenv('LANDO_INFO'), TRUE);
  $databases['default']['default'] = [
    'driver' => 'mysql',
    'database' => $lando_info['database']['creds']['database'],
    'username' => $lando_info['database']['creds']['user'],
    'password' => $lando_info['database']['creds']['password'],
    'host' => $lando_info['database']['internal_connection']['host'],
    'port' => $lando_info['database']['internal_connection']['port'],
  ];

/*
  $config['search_api.server.default_solr_server']['backend_config']['connector_config']['core'] = 'drupal';
  $config['search_api.server.default_solr_server']['backend_config']['connector_config']['path'] = '/solr';
  $config['search_api.server.default_solr_server']['backend_config']['connector_config']['host'] = 'solrserver';
  $config['search_api.server.default_solr_server']['backend_config']['connector_config']['port'] = '8983';
*/

  $settings['hash_salt'] = md5(getenv('LANDO_HOST_IP'));

  $config['stage_file_proxy.settings']['origin'] = 'https://spacebase.co';
  $config['stage_file_proxy.settings']['hotlink'] = TRUE;
}
