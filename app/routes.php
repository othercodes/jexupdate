<?php

$app->get('/', '\App\Controllers\CollectionXMLController:index');
$app->get('/index.xml', '\App\Controllers\CollectionXMLController:index');
$app->get('/{extension}', '\App\Controllers\ExtensionXMLController:index');