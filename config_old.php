<?php
// ===========================================================================================
//
// config.php
//
// Website specific configurations.
//

// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST', 		'localhost');	// The database host
define('DB_USER', 		'Mikael');		// The username of the database
define('DB_PASSWORD', 	'hemligt');		// The users password
define('DB_DATABASE', 	'blog');		// The name of the database to use

//
// The following supports having many databases in one database by using table/view prefix.
//
define('DB_PREFIX', 	'v01_');		// Prefix to use infront of tablename and views

//
// Database tables (DBT), views (DBV), functions (DBF), procedures (DBP).
//

// Global
define('DBT_User', 			DB_PREFIX . 'User');
define('DBT_Group', 		DB_PREFIX . 'Group');
define('DBT_GroupMember', 	DB_PREFIX . 'GroupMember');

// Style
define('DBT_Style', 		DB_PREFIX . 'Style');

// Blog
define('DBT_Text', 			DB_PREFIX . 'Text');


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), used as default values in CHTMPLPage.php
//
define('WS_SITELINK',   'http://tekcp554.tek.bth.se/webdb/mom10/blog01/'); // Link to site.
define('WS_TITLE', 		'The Foogler Blog');		// The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');	// Default stylesheet of the site.
define('WS_FAVICON', 	'img/favicon.ico'); // Small icon to display in browser
define('WS_FOOTER', 	'Blog 0.1 &reg; Home Copyrights Privacy About');	// Footer at the end of the page.
define('WS_VALIDATORS', TRUE);	// Show links to w3c validators tools.
define('WS_TIMER', 		TRUE); // TRUE/FALSE to time the generation of a page and display in footer.
define('WS_LANGUAGE', 	'sv'); // Default language


// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
$menuNavBar = Array (
	'Hem' 				=> '?p=home',
	'Författare'	 	=> '?p=writers',
	'Style' 			=> '?p=style',
	'Om Foogler' 		=> '?p=about',
	'Sök' 				=> '?p=search',
	'Installera' 		=> '?p=install',
	'Visa filer' 		=> '?p=ls',
);
define('MENU_NAVBAR', 		serialize($menuNavBar));


// -------------------------------------------------------------------------------------------
//
// Define the user setting menu.
//
$menuSettingsBar = Array (
	'Blog' 				=> '?p=settingsblog',
	'Style'			 	=> '?p=style-details',
	'Konto' 			=> '?p=account',
);
define('MENU_SETTINGSBAR', 		serialize($menuSettingsBar));


// -------------------------------------------------------------------------------------------
//
// Define the user setting menu.
//
$menuAccountBar = Array (
	'Detaljer'			=> '?p=account-details',
);
define('MENU_ACCOUNTBAR', 		serialize($menuAccountBar));


// -------------------------------------------------------------------------------------------
//
// Define the style setting menu.
//
$menuStyleBar = Array (
	'Detaljer'			=> '?p=style-details',
);
define('MENU_STYLEBAR', 		serialize($menuStyleBar));


// -------------------------------------------------------------------------------------------
//
// Settings for the template (TP) structure, where are everything?
// Support for storing in directories
//
define('TP_ROOT',			dirname(__FILE__) . '/');		// The root of installation
define('TP_SOURCEPATH',		dirname(__FILE__) . '/src/');	// Classes, functions, code
define('TP_PAGESPATH',		dirname(__FILE__) . '/pages/');	// Pagecontrollers and modules
define('TP_SQLPATH',		dirname(__FILE__) . '/pages/blog/sql/');	// SQL code
define('TP_LANGUAGEPATH',	dirname(__FILE__) . '/lang/');	// Multi-language support


?>
