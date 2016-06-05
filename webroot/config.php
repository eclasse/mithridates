<?php
/**
 * Config-file for Mithridates. Change settings here to affect installation.
 *
 */
 
/**
 * Set the error reporting.
 *
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly
 
 
/**
 * Define Mithridates paths.
 *
 */
define('MITHRIDATES_INSTALL_PATH', __DIR__ . '/..');
define('MITHRIDATES_THEME_PATH', MITHRIDATES_INSTALL_PATH . '/theme/render.php');
 
 
/**
 * Include bootstrapping functions.
 *
 */
include(MITHRIDATES_INSTALL_PATH . '/src/bootstrap.php');
 
 /**
 * Start the session.
 *
 */
session_name(preg_replace('/[^a-z\d]/i', '', __DIR__));
session_start();
 
/**
 * Create the Mithridates variable.
 *
 */
$mithridates = array();
 
 
/**
 * Site wide settings.
 *
 */
$mithridates['lang']         = 'sv';
$mithridates['title_append'] = ' | Mithridates';

$mithridates['header'] = <<<EOD
<img class='sitelogo' src='img/mithridateslogo.png' alt='Logo'/>
<span class='sitetitle'>Mithridates</span>
<span class='siteslogan'>Webbmall</span>
EOD;

$mithridates['footer'] = <<<EOD
<footer><span class='sitefooter'>Copyright (c) Mithridates Corp. | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

/**
 * Theme related settings.
 *
 */
$mithridates['stylesheets'][] = 'css/style.css';
$mithridates['favicon']    = 'img/mithridateslogo.png';

/**
 * Menu related settings.
 *
 */
$mithridates['menu_items'] = array(
  'home'  => array('text'=>'HEM',  'url'=>'index.php'),
  
);

/**
* Database related settings
*
*/

$mithridates['database']['dsn']            = 'mysql:host=localhost;dbname=kmom10;';
$mithridates['database']['username']       = 'root';
$mithridates['database']['password']       = '';
$mithridates['database']['driver_options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");

/*
define('DB_PASSWORD', '');
$mithridates['database']['dsn']            = '';
$mithridates['database']['username']       = '';
$mithridates['database']['password']       = DB_PASSWORD;
$mithridates['database']['driver_options'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
*/
