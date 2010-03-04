<?php
// ===========================================================================================
//
// Class CWYSIWYGEditor_WYMeditor
//
// Support for WYSIWYG JavaScript editor WYMeditor.
// http://www.wymeditor.org/
//
//
// Author: Mikael Roos, mos@bth.se
//

require_once('CWYSIWYGEditor.php');

class CWYSIWYGEditor_WYMeditor extends CWYSIWYGEditor {

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
<!-- Updated for WYMeditor ============================================================== -->

<!-- jQuery library is required, see http://jquery.com/ -->
<script type="text/javascript" src="{$jquery}"></script>

<!-- WYMeditor main JS file, minified version -->
<script type="text/javascript" src="{$tpJavaScript}/wymeditor/wymeditor/jquery.wymeditor.min.js"></script>

<script type="text/javascript">

/* Here we replace each element with class 'wymeditor'
 * (typically textareas) by a WYMeditor instance.
 * 
 * We could use the 'html' option, to initialize the editor's content.
 * If this option isn't set, the content is retrieved from
 * the element being replaced.
 */

jQuery(function() {
    jQuery('.{$this->iCSSClass}').wymeditor();
});

</script>

EOD;

		return $head;
	}
	

} // End of Of Class

?>