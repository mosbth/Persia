<?php
// ===========================================================================================
//
// File: CWYSIWYGEditor_Plain.php
//
// Description: Class CWYSIWYGEditor_Plain
//
// A plain textarea editor, just for showing how to implement the abstract base class 
// for WYSIWYGEditor.
//
// Author: Mikael Roos, mos@bth.se
//


class CWYSIWYGEditor_Plain extends CWYSIWYGEditor {

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
	public function __construct($aTextareaId='', $aTextareaClass='', $aSubmitId='', $aSubmitClass='') {
		$this->iTextareaId		= $aTextareaId; 
		$this->iTextareaClass = $aTextareaClass; 
		$this->iSubmitId			= $aSubmitId; 
		$this->iSubmitClass		= $aSubmitClass; 
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
		return ''; 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the textarea.
	//
	public function GetTextareaSettings() { 

		$id 		= (empty($this->iTextareaId)) 		? '' : "id='{$this->iTextareaId}' "; 
		$class 	= (empty($this->iTextareaClass)) 	? '' : "class='{$this->iTextareaClass}' "; 

		return "{$id}{$class}"; 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Return the id and class attributes, if set, specific for this editor and
	// the submit button.
	//
	public function GetSubmitSettings() { 

		$id 		= (empty($this->iSubmitId)) 		? '' : "id='{$this->iSubmitId}' "; 
		$class 	= (empty($this->iSubmitClass)) 	? '' : "class='{$this->iSubmitClass}' "; 

		return "{$id}{$class}"; 
	}
	

} // End of Of Class


?>