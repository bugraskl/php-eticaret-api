<?php

use OrderService\OrderService;


/**
 * @param $requestMethod
 * @param $uri1
 * @param null $orderCode
 * @return void
 */

function order($requestMethod, $uri1, $orderCode = null): void
{
    $validMethods = ['GET', 'POST', 'PUT', 'DELETE'];

    if (!in_array($requestMethod, $validMethods)) {
        echo json_encode(['status' => false, 'error' => 'Geçersiz metod. Desteklenen metodlar: ' . implode(', ', $validMethods)]);
        return;
    }

    switch ($uri1) {
        case 'OrderDetails':
            handleOrderDetails($requestMethod, $orderCode);
            break;
        case 'CreateOrder':
            handleCreateOrder($requestMethod);
            break;
        case 'UpdateOrder':
            handleUpdateOrder($requestMethod);
            break;
        case 'DeleteOrder':
            handleDeleteOrder($requestMethod, $orderCode);
            break;
        default:
            echo json_encode(['status' => false, 'error' => 'Geçersiz URI. Desteklenen URIs: OrderDetails, CreateOrder, UpdateOrder, DeleteOrder']);
    }
}

function handleOrderDetails($method, $orderCode): void
{
    if ($method == 'GET') {
        getOrder($orderCode);
    } else {
        echo json_encode(['status' => false, 'error' => 'OrderDetails sadece GET Metodu ile işleme alınabilir.']);
    }
}

function handleCreateOrder($method): void
{
    if ($method == 'POST') {
        createOrder();
    } else {
        echo json_encode(['status' => false, 'error' => 'CreateOrder sadece POST Metodu ile işleme alınabilir.']);
    }
}

function handleUpdateOrder($method): void
{
    if ($method == 'PUT') {
        updateOrder();
    } else {
        echo json_encode(['status' => false, 'error' => 'UpdateOrder sadece PUT Metodu ile işleme alınabilir.']);
    }
}

function handleDeleteOrder($method, $orderCode): void
{
    if ($method == 'DELETE') {
        deleteOrder($orderCode);
    } else {
        echo json_encode(['status' => false, 'error' => 'DeleteOrder sadece DELETE Metodu ile işleme alınabilir.']);
    }
}



function getOrder($orderCode)
{
    $getOrder = new OrderService();
    echo json_encode($getOrder->getOrderDetails($orderCode));
}

function deleteOrder($orderCode)
{
    $deleteOrder = new OrderService();
    echo json_encode($deleteOrder->deleteOrder($orderCode));
}

function createOrder()
{
    $input = (array)json_decode(file_get_contents('php://input'), TRUE);

    $createOrder = new OrderService();
    echo json_encode($createOrder->createOrder($input));
}

function updateOrder()
{
    $input = (array)json_decode(file_get_contents('php://input'), TRUE);

    $updateOrder = new OrderService();
    echo json_encode($updateOrder->updateOrder($input));
}

function completeOrder($orderCode, $userId)
{
    $completeOrder = new OrderService();
    echo json_encode($completeOrder->completeOrder($orderCode, $userId));
}