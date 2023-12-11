<?php
/**
 * 8.12.2023
 * 16:14
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

require_once 'Helper/helper.php';
require_once 'Api/api.php';
require_once 'vendor/autoload.php';
error_reporting(0);
includeAllClassesInFolder();

$db = new BasicDB('localhost', 'eticaret-api', 'root', '');
