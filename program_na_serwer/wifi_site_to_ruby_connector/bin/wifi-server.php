<?php
use MyApp\Listen;

require dirname(__DIR__) . '/vendor/autoload.php';
$app = new Ratchet\App("ws.online.wik.pl", 8888, '0.0.0.0');
$app->route('/sendUpdate', new \Update(), array('*'));
$app->route('/ListenForUpdate', new \ListenForUpdate(), array('*'));
$app->run();
