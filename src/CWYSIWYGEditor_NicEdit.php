<?php
// ===========================================================================================
//
// Class CWYSIWYGEditor_NicEdit
//
// Support for WYSIWYG JavaScript editor NicEdit.
// http://nicedit.com/
//
//
// Author: Mikael Roos, mos@bth.se
//

require_once('CWYSIWYGEditor.php');

class CWYSIWYGEditor_NicEdit extends CWYSIWYGEditor {

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
		
		$head = <<<EOD
<!-- Updated for NiceEditor ============================================================= -->
<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(nicEditors.allTextAreas);
</script>
<!-- ==================================================================================== -->

EOD;

		return $head;
	}
	

} // End of Of Class

?>