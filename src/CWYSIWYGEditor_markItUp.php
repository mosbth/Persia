<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_markItUp.php
//
// Description: Class CWYSIWYGEditor_markItUp
//
// Support for WYSIWYG JavaScript editor markItUp.
// http://markitup.jaysalvat.com/home/
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_markItUp extends CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') {
		parent::__construct($aTextareaId, $aTextareaClass, $aSubmitId, $aSubmitClass);
	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Does this editor need the jQuery JavaScript library?
	// Subclasses who does should reimplement this method and return TRUE.
	//
	public function DependsOnjQuery() {
		return TRUE;
	}
	
	
	// ------------------------------------------------------------------------------------
	//
	// Return the HTML header for the editor, usually stylesheet, js-file and javascript 
	// code to instantiate editor.
	//
	public function GetHTMLHead() {
	
		$tpJavaScript = WS_JAVASCRIPT;

		$head = <<<EOD
<!-- Updated for markItUp =============================================================== -->

<!-- markItUp! skin --> 
<link rel="stylesheet" type="text/css" href="{$tpJavaScript}/markitup/markitup/skins/markitup/style.css" /> 

<!--  markItUp! toolbar skin --> 
<link rel="stylesheet" type="text/css" href="{$tpJavaScript}/markitup/markitup/sets/html/style.css" /> 

<!-- markItUp! --> 
<script type="text/javascript" src="{$tpJavaScript}/markitup/markitup/jquery.markitup.pack.js"></script>

<!-- markItUp! toolbar settings --> 
<script type="text/javascript" src="{$tpJavaScript}/markitup/markitup/sets/html/set.js"></script>

<script language="javascript">
$(document).ready(function()	{
	$('.{$this->iTextareaClass}').markItUp(mySettings);
});
</script>
<!-- ==================================================================================== -->
EOD;

		return $head;
	}
	

} // End of Of Class

?>