<?php
//
//		Persia (http://phpersia.org), software to build webbapplications.
//    Copyright (C) 2010  Mikael Roos (mos@bth.se)
//
//    This file is part of Persia.
//
//    Persia is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    Persia is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Persia. If not, see <http://www.gnu.org/licenses/>.
//
// File: index.php
//
// Description:
// An implementation of a PHP frontcontroller for a web-site.
// All requests passes through this page, for each request a module frontcontroller is choosen.
// The module frontcontroller choosen which pagecontroller to use.
// The pagecontroller results in a response or a redirect.
//
// Author: Mikael Roos, mos@bth.se
//
// Known issues:
// -
//
// History: 
// 2010-07-01: Changed error_reporting to -1. Included license message.
//


// -------------------------------------------------------------------------------------------
//
// Require the files and actions that are common for all modules and pagecontrollers.
//
error_reporting(-1);

//
// Get global config-files with template structure
//
require_once('config-global.php');

//
// Enable autoload for classes
//
function __autoload($class_name) { require_once(TP_SOURCEPATH . $class_name . '.php'); }


// -------------------------------------------------------------------------------------------
//
// Redirect to the choosen modulecontroller (if a module is defined). Review modules.php
// for further details.
// 
global $gModulesAvailable; // Set in config-global.php

//
// Get the requested page- and module id.
//
$gModule 	= isset($_GET['m']) ? $_GET['m'] : 'core';
$gPage 		= isset($_GET['p']) ? $_GET['p'] : 'home';

//
// Check if the choosen module is available, if not show 404
//
if(!array_key_exists($gModule, $gModulesAvailable)) {
	require_once('config.php');
	require_once(TP_PAGESPATH . 'home/P404.php');
	exit;
}

//
// Load the module config-page, if it exists. Else load default config.php
//
$configFile = $gModulesAvailable["{$gModule}"] . '/config.php';

if(is_readable($configFile)) {
	require_once($configFile);
} else {
	require_once('config.php');
}

//
// Start a named session
//
session_name(preg_replace('/[:\.\/-_]/', '', WS_SITELINK));
session_start();

//
// Start a timer to time the generation of this request
//
if(WS_TIMER) { $gTimerStart = microtime(TRUE); }

//
// Redirect to module controller.
//
$moduleController = $gModulesAvailable["{$gModule}"] . '/index.php';

if(is_readable($moduleController)) {
	require_once($moduleController);
} else {
	require_once(TP_PAGESPATH . 'home/P404.php');
}


?>