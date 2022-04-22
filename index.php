<?php


// Session Start
session_start();

// Config

define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', currentDomain() . '/100DaysCode/PHP-toplearn/05-php-news/');
define('DISPLAY_ERROR', true);

// Database

define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');

//  Helper

function protocol()
{
    return strpos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
}

function currentDomain()
{
    return protocol() . $_SERVER['HTTP_HOST'];
}

function asset($src)
{
    $domain = trim(CURRENT_DOMAIN, '/');
    $src = $domain . '/' . trim($src, '/');
    return $src;
}


// echo asset('public/admin/css/style.css');

function url($url)
{
    $domain = trim(CURRENT_DOMAIN, '/');
    $url = $domain . '/' . trim($url, '/');
    return $url;
}

function currentUrl()
{
    return currentDomain() . $_SERVER['REQUEST_URI'];
}


function methodField()
{
    return $_SERVER['REQUEST_METHOD'];
}

function displayError($displayError)
{
    if ($displayError) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }
}


global $flashMessage;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

function flash($name, $type = 'success', $value = null)
{
    if ($value === null) {
        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;
    } else {
        $_SESSION['flash_message'][$name] = $value;
    }
}

function dd($var)
{
    echo '</pre>';
    var_dump($var);
    exit;
}

// Routing
// uri('admin/category', 'Category', 'index')
// uri('admin/category/store', 'Category', 'store', 'POST')
function uri($reservedUrl, $class, $method, $requestMethod = 'GET')
{

    // Current Url Array
    $currentUrl = explode('?', currentUrl())[0];
    $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
    $currentUrl = trim($currentUrl, '/');
    $currentUrlArray = explode('/', $currentUrl);
    $currentUrlArray = array_filter($currentUrlArray);

    // Reserved Url array
    $reservedUrl = trim($reservedUrl, '/');
    $reservedUrlArray = explode('/', $reservedUrl);
    $reservedUrlArray = array_filter($reservedUrlArray);

    // Check Client URL with Reserved URL 
    if (sizeof($currentUrlArray) !== sizeof($reservedUrlArray) || methodField() !== $requestMethod) {
        return false;
    }

    $parameters = [];
    for ($key = 0; $key < sizeof($currentUrlArray); $key++) {

        if ($reservedUrlArray[$key][0] == '{' && $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] == '}') {
            array_push($parameters, $currentUrlArray[$key]);
        } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {

            return false;
        }
    }

    if (methodField() == 'POST') {
        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);
    }

    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    exit();
}

uri('admin/posts', 'posts', 'index');
