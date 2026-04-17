<?php

/*

git add .
git commit -m "message"
git push -u origin main --force


https://www.flaticon.com/search?word=edit&color=color - iconky
bootstrap frontend framework - https://adminlte.io/blog/bootstrap-login-forms/
https://colorlib.com/etc/regform/colorlib-regform-7/index.html

*/

// $domena = "http://localhost/bp/bp";
$domena = "http://" . $_SERVER['HTTP_HOST'] . "/bp/bp";

$env = parse_ini_file(__DIR__ . '/.env');
define('APP_SECRET_KEY', $env['APP_SECRET_KEY']);
define('MAIL_HOST', $env['MAIL_HOST']);
define('MAIL_PORT', $env['MAIL_PORT']);
define('MAIL_USERNAME', $env['MAIL_USERNAME']);
define('MAIL_PASSWORD', $env['MAIL_PASSWORD']);
define('MAIL_FROM', $env['MAIL_FROM']);
define('MAIL_FROM_NAME', $env['MAIL_FROM_NAME']);

session_start();
include 'Models/connect_db.php';
include 'helpers.php';


// Authorization check
$authorized = $_SESSION['authorized'] ?? null;

// Process URL
function process_URL()
{
    $URL = explode('/', $_GET['pozadavek'] ?? '');
    $controller = $URL[0] ?? null;
    $method = $URL[1] ?? null;
    $num_of_params = count($URL);

    $params = [];
    for ($i = 2; $i < $num_of_params; $i++) {
        $params[] = $URL[$i] ?? null;
    }

    return ['controller' => $controller, 'method' => $method, 'params' => $params];
}

$router = process_URL();

if (!$authorized && $router['controller'] !== 'auth') {
    $router = ['controller' => 'auth', 'method' => 'login', 'params' => null];
}

if ($authorized && !$_SESSION['verified_email'] && $router['controller'] !== 'auth') {
    $router = ['controller' => 'auth', 'method' => 'verify-email', 'params' => null];
}

if ($authorized && $_SESSION['verified_email'] && $router['controller'] === 'auth' && $router['method'] !== 'logout') {
    $router = ['controller' => 'dashboard', 'method' => 'show', 'params' => null];
}

// Whitelist allowed controllers
$allowed_controllers = ['auth', 'dashboard', 'events', 'profile', 'friends'];

if ($router['controller']) {

    if (!in_array($router['controller'], $allowed_controllers)) {
        include "Views/error.php";
        exit;
    }

    $controllerFile = "Controllers/" . $router['controller'] . ".php";

    if (file_exists($controllerFile)) {
        include $controllerFile;
    } else {
        include "Views/error.php";
    }

} else {
    include "Controllers/dashboard.php";
}
?>