<?php

$app->get('/', '\JEXUpdate\Controllers\CollectionXMLController:index');
$app->get('/index.xml', '\JEXUpdate\Controllers\CollectionXMLController:index');
$app->get('/{extension}', '\JEXUpdate\Controllers\ExtensionXMLController:index');