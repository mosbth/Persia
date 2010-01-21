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
// All these setting are moved to the file TP_SQLPATH/config.php
//


// -------------------------------------------------------------------------------------------
//
// Settings for this website (WS), some used as default values in CHTMPLPage.php
//
// PERHAPS SPLIT THIS WHEN UPDATING CHTMLPAGE
//
define('WS_SITELINK',   'http://dev.phpersia.org/persia//'); // Link to site.
define('WS_TITLE', 		'Persia');		    // The title of this site.
define('WS_STYLESHEET', 'style/plain/stylesheet_liquid.css');	// Default stylesheet of the site.
define('WS_FAVICON', 	'img/favicon.ico'); // Small icon to display in browser
define('WS_FOOTER', 	'Persia &copy; 2010 by Mikael Roos Home Copyrights Privacy About');	// Footer at the end of the page.
define('WS_VALIDATORS', TRUE);	            // Show links to w3c validators tools.
define('WS_TIMER', 		TRUE);              // Time generation of a page and display in footer.
define('WS_CHARSET', 	'utf-8');           // Use this charset
define('WS_LANGUAGE', 	'en');              // Default language


// -------------------------------------------------------------------------------------------
//
// Define the navigation menu.
//
// MOVE THIS TO CHTMLPAGE OR OTHER CONFIG-FILE OR LEAVE IT AS IT IS?
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
// Settings for the template (TP) structure, show where everything are stored.
// Support for storing in directories, no need to store everything under one directory
//
define('TP_ROOT',			dirname(__FILE__) . '/');		// The root of installation
define('TP_SOURCEPATH',		dirname(__FILE__) . '/src/');	// Classes, functions, code
define('TP_PAGESPATH',		dirname(__FILE__) . '/pages/');	// Pagecontrollers and modules
define('TP_LANGUAGEPATH',	dirname(__FILE__) . '/lang/');	// Multi-language support
define('TP_SQLPATH',		dirname(__FILE__) . '/sql/');	// SQL code


?>