<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor.php
//
// Description: Class CWYSIWYGEditor
//
// Abstract base class WYSIWYG JavaScript editors as a replacement for <textarea>. 
// Each specific editor must inherit this class and implement its methods.
//
// Examples on usage of classes in a pagecontroller:
//  http://dev.phpersia.org/persia/?p=article-edit-all
//
//
// Author: Mikael Roos, mos@bth.se
//

abstract class CWYSIWYGEditor {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iTextareaId;
	protected $iTextareaClass;
	protected $iSubmitId;
	protected $iSubmitClass;
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') { ; }


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
	public function GetHTMLHead() { ; }


	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the textarea.
	//
	public function GetTextareaSettings() { ; }
	

	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the submit button.
	//
	public function GetSubmitSettings() { ; }


} // End of Of Class

?>