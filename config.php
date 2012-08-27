<?php


# read the credentials file
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($string, true);


return array (
  'id' => '470d',
  'database' => 
  array (
    'server' => $creds['MYSQLS']['MYSQLS_HOSTNAME'],
    'user' => $creds['MYSQLS']['MYSQLS_USERNAME'],
    'password' => $creds['MYSQLS']['MYSQLS_PASSWORD'],
    'name' => $creds['MYSQLS']['MYSQLS_DATABASE'],
    'prefix' => 'dev_',
    'type' => 'mysql',
    'persistent' => '0',
  ),
  'fileGroupPermission' => 'rw',
  'fileEveryonePermission' => '-',
  'cache' => 
  array (
    'class' => '\\Core\\Cache\\Files',
  ),
  'passwordHashCompat' => 0,
  'passwordHashKey' => 'EWQfA{F kOaQ6"^]ZN?R-_^}KT4D1t@l',
  'displayErrors' => 0,
  'displayRestErrors' => 0,
  'logErrors' => 0,
  'systemTitle' => 'Fresh installation',
  'client' => 
  array (
    'class' => '\\Core\\Client\\KrynUsers',
    'config' => 
    array (
      'emailLogin' => false,
      'store' => 
      array (
        'class' => 'database',
        'config' => 
        array (
        ),
      ),
    ),
  ),
  'timezone' => 'Europe/Berlin',
); ?>