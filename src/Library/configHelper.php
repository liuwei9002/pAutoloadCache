<?php
$globe_base_path = __DIR__ . '/..';
$globe_config_path = $globe_base_path . "/Config";

$configFiles = [
    'cache' => 'cache.php',
    'common' => 'common.php',
    'swoole' => 'swoole.php'
];

foreach ($configFiles as $key => $configFile) {
    \Suyain\Support\Config::put($key, require($globe_config_path . '/' . $configFile));
}
