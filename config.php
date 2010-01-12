<?php
// ===========================================================================================
//
// config.php, config-sample.php
//
// Website specific configurations. The goal is to have all configs here. Anything that 
// should be configured to kickstart a new website should be here.
//
// Author: Mikael Roos, mos@bth.se
//

// -------------------------------------------------------------------------------------------
//
// Settings for the database connection
//
define('DB_HOST', 		'localhost');	// The database host
define('DB_USER', 		'Mikael');		// The username of the database
define('DB_PASSWORD', 	'hemligt');		// The users password
define('DB_DATABASE', 	'persia');		// The name of the database to use
define('DB_PREFIX', 	'pe_');		// Prefix to use infront of tablename and views


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), some used as default values in CHTMPLPage.php
//

// SPLIT THIS WHEN UPDATING CHTMLPAGE

define('WS_SITELINK',   'http://persia.se/git/persia//'); // Link to site.
define('WS_TITLE', 		'Persia');		// The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');	// Default stylesheet of the site.
define('WS_FAVICON', 	'img/favicon.ico'); // Small icon to display in browser
define('WS_FOOTER', 	'Persia &reg; Home Copyrights Privacy About');	// Footer at the end of the page.
define('WS_VALIDATORS', TRUE);	// Show links to w3c validators tools.
define('WS_TIMER', 		TRUE); // TRUE/FALSE to time the generation of a page and display in footer.
define('WS_CHARSET', 	'utf-8'); // Use this charset
define('WS_LANGUAGE', 	'sv'); // Default language


// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
// MOVE THIS TO CHTMLPAGE

$menuNavBar = Array (
	'Hem' 				=> '?p=home',
	'Template'	 		=> '?p=template',
	'Style' 			=> '?p=style',
	'Om Foogler' 		=> '?p=about',
	'404' 				=> '?p=NOT_EXISTING',
	'Installera' 		=> '?p=install',
	'Visa filer' 		=> '?p=ls',
);
define('MENU_NAVBAR', 		serialize($menuNavBar));


// -------------------------------------------------------------------------------------------
//
// Settings for the template (TP) structure, where are everything?
// Support for storing in directories, no need to store everything under one directory
//
define('TP_ROOT',			dirname(__FILE__) . '/');		// The root of installation
define('TP_SOURCEPATH',		dirname(__FILE__) . '/src/');	// Classes, functions, code
define('TP_PAGESPATH',		dirname(__FILE__) . '/pages/');	// Pagecontrollers and modules
define('TP_SQLPATH',		dirname(__FILE__) . '/pages/blog/sql/');	// SQL code
define('TP_LANGUAGEPATH',	dirname(__FILE__) . '/lang/');	// Multi-language support


?>