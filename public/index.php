<?php
// Set Includes path
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(
    APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/default/controllers'   . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/manager/controllers'   . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/models'
);

// Setting the default time zone
date_default_timezone_set('Asia/Jakarta');

// Autoloader activation
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// Configuration file read
if ( (isset($_ENV['BZ_STAGE']) && $_ENV['BZ_STAGE'] === '1') || file_exists( "/etc/bz_stage" ) ) {
    error_reporting(E_ALL|E_STRICT);
    ini_set('display_errors', 0);
    $config = new Zend_Config_Ini(APPLICATION_PATH . "/../application/configs/config.ini", "staging");
    Zend_Registry::set('config', $config);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    $config = new Zend_Config_Ini(APPLICATION_PATH . "/../application/configs/config.ini", "production");
    Zend_Registry::set('config', $config);
}

// Sessions start
Zend_Session::start();

// Layout enabled
Zend_Layout::startMvc();

// Configuring the Controller
$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory(array(
    'default'       => APPLICATION_PATH . '/../application/default/controllers',
    'manager'       => APPLICATION_PATH . '/../application/manager/controllers',
));

// Database connection initialization
$db = Zend_Db::factory('Pdo_Mysql', $config->database);

// Application execution start
$front->throwExceptions(true);

try {
    // Database connection start
    $db->beginTransaction();
    $db->query('set names utf8');
    Zend_Db_Table_Abstract::setDefaultAdapter($db);

    // Dispatch start
    $front->dispatch();

    // Commit the changes
    $db->commit();
} catch(Zend_Controller_Dispatcher_Exception $e) {
    if ($config->app->debug) {
        // Display Error
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
        exit();
    } else {
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
} catch(Zend_Controller_Action_Exception $e) {
    if ($config->app->debug) {
        // Display Error
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
    } else {
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
} catch(Exception $e) {
    // Roll back if it is connected to the database
    if ($db->isConnected()) {
        $db->rollBack();
    }

    // I skip the error mail
    Util_Mail::send(array(
        'to' => 'tya@buzoo.biz',
        'subject' => '['.$_SERVER['HTTP_HOST'].'] application error',
        'body' => 'env => ' . print_r($_SERVER, true) . "\n" .
                  'err => ' . $e->__toString()

    ));

    // Display Error
    if ($config->app->debug) {
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
        exit();
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Internal Server Error';
    }
}

// Shortcut
function h($var) {
    return htmlspecialchars($var);
}

// Display Error
function my_mb_error($errors, $key) {
    if (isset($errors[$key])) {
        $front = Zend_Controller_Front::getInstance();
        if ($front->getRequest()->getModuleName() == 'default') {
            return '<font color="#FF0000">※'. $errors[$key] .'</font><br>'."\n";
        } elseif ($front->getRequest()->getModuleName() == 'manager') {
            return '<font color="#FF0000">'. $errors[$key] .'</font><br>'."\n";
        } else {
            return '<span class="textred test">※'. $errors[$key] .'</span><br>'."\n";
        }
    }
}

// Dotted substr
function substrdot($str, $limit = 20) {
    $str = str_replace("\r", "", $str);
    $str = str_replace("\n", "", $str);
    if (mb_strlen($str, 'UTF-8') > $limit) {
        $str = mb_substr($str, 0, $limit, 'UTF-8') . '…';
    }
    return $str;
}
