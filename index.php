<?php
/**
 * 8.12.2023
 * 16:17
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

require "Config/config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($uri[1]) {
    case 'DeleteOrder':
    case 'OrderDetails':
        order($requestMethod, $uri[1],$uri[2]);
        break;
    case 'UpdateOrder':
    case 'CreateOrder':
        order($requestMethod, $uri[1]);
        break;
    case 'CompleteOrder':
        completeOrder($uri[2], $uri[3]);
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
}






