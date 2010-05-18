<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_PersiaEditor.php
//
// Description: Class CWYSIWYGEditor_PersiaEditor
//
// An enhanced plain textarea editor, just for showing how to implement your own editor.
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_PersiaEditor extends CWYSIWYGEditor {

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
	// Subclasses who does, should reimplement this method and return TRUE.
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
<!-- PersiaEditor ================================================================== -->

<!-- JavaScript for PersiaEditor -->
<script type="text/javascript" src="{$tpJavaScript}/persiaeditor/jquery.persiaeditor.js"></script>

<!-- Stylesheet for PersiaEditor -->
<link rel="stylesheet" href="{$tpJavaScript}/persiaeditor/persiaeditor.css" />

<script language="javascript">
$(document).ready(function()	{
	$('.{$this->iTextareaClass}').persiaEditor({
		doe: 'doe',
		john: true,
	});
});
</script>

EOD;

		return $head;
	}
	

} // End of Of Class


?>