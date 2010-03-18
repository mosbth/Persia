<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_WYMeditor.php
//
// Description: Class CWYSIWYGEditor_WYMeditor
//
// Support for WYSIWYG JavaScript editor WYMeditor.
// http://www.wymeditor.org/
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_WYMeditor extends CWYSIWYGEditor_Plain {

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
	// Return the HTML header for the editor, usually stylesheet, js-file and javascript 
	// code to instantiate editor.
	//
	public function GetHTMLHead() {
	
		$tpJavaScript = WS_JAVASCRIPT;
		$jquery 			= JS_JQUERY;

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
    jQuery('.{$this->iTextareaClass}').wymeditor();
});

</script>

EOD;

		return $head;
	}
	

} // End of Of Class

?>