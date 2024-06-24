<?php
declare(strict_types=1);

switch ($_SERVER['SCRIPT_NAME']) {
    case '/method':
        echo $_SERVER['REQUEST_METHOD'];
        break;
    case '/get':
        header('Content-Type: application/json');
        echo json_encode($_GET);
        break;
    case '/post':
        header('Content-Type: application/json');
        echo json_encode($_POST);
        break;
    case '/json':
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    case '/auth':
        header('Content-Type: application/json');
        echo json_encode([
            'username' => $_SERVER['PHP_AUTH_USER'] ?? '',
            'password' => $_SERVER['PHP_AUTH_PW'] ?? ''
        ]);
        // no break
    case '/agent':
        echo $_SERVER['HTTP_USER_AGENT'] ?? '';
        break;
    case '/header':
        echo $_SERVER['HTTP_ACCEPT'] ?? '';
        break;
    case '/version':
        echo $_SERVER['SERVER_PROTOCOL'];
        break;
    default:
        break;
}
