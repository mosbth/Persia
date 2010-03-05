<?php
// ===========================================================================================
//
// config.php, config-sample.php
//
// Module specific configurations. This file is the default config-file for modules. It can
// be overidden by another config.php-file residing in the module library. For example, 
// a file: modules/core/config.php would replace this file. 
// This way can each module can have their own settings. 
// All definitions must be made if done in a module specific config.php.
//
// Author: Mikael Roos, mos@bth.se
//


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), some used as default values in CHTMPLPage.php
//
define('WS_SITELINK',   'http://dev.phpersia.org/persia/'); 	// Link to site.
define('WS_TITLE', 		'Persia');		    					// The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');	// Default stylesheet of the site.
define('WS_IMAGES',		WS_SITELINK . 'img/'); 					// Images
define('WS_FAVICON', 	WS_IMAGES . 'favicon.ico'); 			// Small icon to display in browser
define('WS_FOOTER', 	'Persia &copy; 2010 by Mikael Roos Home Copyrights Privacy About');	// Footer at the end of the page.
define('WS_VALIDATORS', TRUE);	            	// Show links to w3c validators tools.
define('WS_TIMER', 		TRUE);              	// Time generation of a page and display in footer.
define('WS_CHARSET', 	'utf-8');           	// Use this charset
define('WS_LANGUAGE', 	'en');              	// Default language
define('WS_JAVASCRIPT',	WS_SITELINK . '/js/');	// JavaScript code


// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
$menuNavBar = Array (
	'Home' 				=> '?p=home',
	'Template'	 		=> '?p=template',
	'About' 			=> '?p=about',
	'404' 				=> '?p=NOT_EXISTING',
	'Install' 			=> '?p=install',
	'Sourcecode' 		=> '?p=ls',
);
define('MENU_NAVBAR', 		serialize($menuNavBar));


// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
// All these setting are moved to the file TP_SQLPATH/config.php
//


// -------------------------------------------------------------------------------------------
//
// Settings for the template (TP) structure, show where everything are stored.
// Support for storing in directories, no need to store everything under one directory
//
// All these setting are moved to the file config-global.php
//


// -------------------------------------------------------------------------------------------
//
// Settings for commonly used external resources, for example javascripts.
//
// All these setting are moved to the file config-global.php
//


?>