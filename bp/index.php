<?php

/*

git add .
git commit -m "message"
git push -u origin main --force

verifikace emailu
pridat forget password
obrazky - prava
pridat znovunacteni vice nez 100 eventu nebudu zobrazovat(to same kamaradi)
nefunnguje vkladani obrazku na macu

https://www.flaticon.com/search?word=edit&color=color - iconky
bootstrap frontend framework - https://uicookies.com/bootstrap-login-form/

super, tedka tedy jdu na to overovani emailu, jak jsme se o tom bavili uz drive. Chci to tedy udelat, jak to dela laravel, pomoz mi to udelat, pouzivej kod a styl(system), kterym to dela laravel.

zeptat se na to, jestli pri testovani bude internet: kvuli fullcalendaru
zeptap se na odevzdavani BP

*/

$domena = "http://localhost/bp/bp";

define('APP_SECRET_KEY', trim(parse_ini_file(__DIR__ . '/.env')['APP_SECRET_KEY']));
define('MAIL_HOST', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_HOST']));
define('MAIL_PORT', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_PORT']));
define('MAIL_USERNAME', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_USERNAME']));
define('MAIL_PASSWORD', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_PASSWORD']));
define('MAIL_FROM', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_FROM']));
define('MAIL_FROM_NAME', trim(parse_ini_file(__DIR__ . '/.env')['MAIL_FROM_NAME']));
// tady je mozna DIR navic - dala by se pouzit domena?

session_start();
include 'Models/connect_db.php';
include 'helpers.php';


// Ověření, zda je uživatel přihlášen
$autorized = $_SESSION['autorized'] ?? null;

// Zpracování URL
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

if (!$autorized && $router['controller'] !== 'auth') {
    $router = ['controller' => 'auth', 'method' => 'login', 'params' => null];
}

if ($autorized && !$_SESSION['verified_email'] && $router['controller'] !== 'auth') {
    $router = ['controller' => 'auth', 'method' => 'verify-email', 'params' => null];
}

if ($autorized && $_SESSION['verified_email'] && $router['controller'] === 'auth' && $router['method'] !== 'logout') {
    $router = ['controller' => 'dashboard', 'method' => 'show', 'params' => null];
}

// Whitelist povolených controllerů - ochrana před Path Traversal útokem
$allowed_controllers = ['api', 'auth', 'dashboard', 'events', 'profile', 'friends'];

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