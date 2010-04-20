<?php
// ===========================================================================================
//
// File: CDatabaseController.php
//
// Description: To ease database usage for pagecontroller. Supports MySQLi.
//
// Author: Mikael Roos
//

// Include commons for database
require_once(TP_SQLPATH . 'config.php');


class CDatabaseController {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	protected $iMysqli;
	protected $iPc;


	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() {

		$this->iMysqli = FALSE;		
		
		$this->iPc = new CPageController();
		$this->iPc->LoadLanguage(__FILE__);	}


	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() {
		;
	}


	// ------------------------------------------------------------------------------------
	//
	// Connect to the database, return a database object.
	//
	public function Connect() {

		$this->iMysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

		if (mysqli_connect_error()) {
   			die(sprintf($this->iPc->lang['CONNECT_TO_DATABASE_FAILED'], mysqli_connect_error()));
		}

		return $this->iMysqli;
	}


	// ------------------------------------------------------------------------------------
	//
	// Execute a database multi_query
	//
	public function MultiQuery($aQuery) {

		$res = $this->iMysqli->multi_query($aQuery) 
			or die(sprintf($this->iPc->lang['COULD_NOT_QUERY_DATABASE'], $aQuery, $this->iMysqli->error));
            
		return $res;
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Retrieve and store results from multiquery in an array.
	//
	public function RetrieveAndStoreResultsFromMultiQuery(&$aResults) {

		$mysqli = $this->iMysqli;
		
		$i = 0;
		do {
			$aResults[$i++] = $mysqli->store_result();
		} while($mysqli->next_result());
		
		// Check if there is a database error
		!$mysqli->errno 
				or die(sprintf($this->iPc->lang['FAILED_RETRIEVING_RESULTSET'], $this->iMysqli->errno, $this->iMysqli->error));
	}


	// ------------------------------------------------------------------------------------
	//
	// Retrieve and ignore results from multiquery, count number of successful statements
	// Some succeed and some fail, must count to really know.
	//
	public function RetrieveAndIgnoreResultsFromMultiQuery() {

		$mysqli = $this->iMysqli;
		
		$statements = 0;
		do {
			$res = $mysqli->store_result();
			$statements++;
		} while($mysqli->next_result());

		return $statements;
	}


	// ------------------------------------------------------------------------------------
	//
	// Load a database query from file in the directory TP_SQLPATH
	//
	public function LoadSQL($aFile) {
		
		$mysqli = $this->iMysqli;
		require(TP_SQLPATH . $aFile);
		return $query;
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Execute a database query
	//
	public function Query($aQuery) {

		$res = $this->iMysqli->query($aQuery) 
			or die(sprintf($this->iPc->lang['COULD_NOT_QUERY_DATABASE'], $aQuery, $this->iMysqli->error));

		return $res;
	}

	// ------------------------------------------------------------------------------------
	//
	// Execute a database query from file, check the number of rows affected.
	// If aRowsAffected is 0, then skip checking.
	//
	public function ConnectAndExecuteSingleSQLQueryFromFileCheckRowsAffected($aFile, $aRowsAffected=0) {

		$this->Connect();
		$query 	= $this->LoadSQL($aFile);
		$res 		= $this->Query($query);

		if($aRowsAffected != 0 && $this->iMysqli->affectedRows != $aRowsAffected) {
			$this->iPc->SetSessionErrorMessage(sprintf($this->iPc->lang['NUMBER_OF_ROWS_AFFECTED_MISMATCH']), $aRowsAffected, $this->iMysqli->affectedRows);
		}
		
		$res->close();
		$this->iMysqli->close();
	}


} // End of Of Class

?>