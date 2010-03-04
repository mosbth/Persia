<?php
// ===========================================================================================
//
// Class CWYSIWYGEditor_markItUp
//
// Support for WYSIWYG JavaScript editor markItUp.
// http://markitup.jaysalvat.com/home/
//
// Author: Mikael Roos, mos@bth.se
//

require_once('CWYSIWYGEditor.php');

class CWYSIWYGEditor_markItUp extends CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $iCSSId;		// A CSS id, if available
	public $iCSSClass;	// A CSS class, if available
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aCSSId='none', $aCSSClass='none') {
		$this->iCSSId 		= $aCSSId; 
		$this->iCSSClass 	= $aCSSClass; 
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Return the HTML header for the editor, usually stylesheet, js-file and javascript 
	// code to instantiate editor.
	//
	public function GetHTMLHead() {
	
		$tpJavaScript 	= WS_JAVASCRIPT;
		$jquery 		= JS_JQUERY;

		$head = <<<EOD
<!-- Updated for markItUp =============================================================== -->

<!-- markItUp! skin --> 
<link rel="stylesheet" type="text/css" href="{$tpJavaScript}/markitup/markitup/skins/markitup/style.css" /> 

<!--  markItUp! toolbar skin --> 
<link rel="stylesheet" type="text/css" href="{$tpJavaScript}/markitup/markitup/sets/html/style.css" /> 

<!-- jQuery --> 
<script type="text/javascript" src="{$jquery}"></script>

<!-- markItUp! --> 
<script type="text/javascript" src="{$tpJavaScript}/markitup/markitup/jquery.markitup.pack.js"></script>

<!-- markItUp! toolbar settings --> 
<script type="text/javascript" src="{$tpJavaScript}/markitup/markitup/sets/html/set.js"></script>

<script language="javascript">
$(document).ready(function()	{
	$('.{$this->iCSSClass}').markItUp(mySettings);
});
</script>
<!-- ==================================================================================== -->
EOD;

		return $head;
	}
	

} // End of Of Class

?>