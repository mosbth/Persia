<?php
// ===========================================================================================
//
// File: CLDAP.php
//
// Description: Class CLDAP
//
// Wrapper for LDAP.
//
// Author: Mikael Roos, mos@bth.se
//
// History:
// 2010-05-09: First try.
//


class CLDAP {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $iServer;
	public $iPortNr;
	public $iProtocolVersion;
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct($aServer='', $aPortNr=389, $aProtocolVersion=3) {
		$this->iServer = $aServer;
		$this->iPortNr = $aPortNr;
		$this->iProtocolVersion = $aProtocolVersion;
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }

	
	// ------------------------------------------------------------------------------------
	//
	// Connect and set options
	//
	public function ConnectAndSetOptions() {
	
		$ds	= ldap_connect($this->iServer, $this->iPortNr);
		echo $ds;
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		return $ds;
	}


	// ------------------------------------------------------------------------------------
	//
	// Authenticate user, return userid or false.
	//
	public function Authenticate($aDs, $aBaseDn, $aUid, $aPassword) {

		if(empty($aPassword)) {
			return false;
		}
		
		// Escape characters
		$basedn 	= self::EscapeChars($aBaseDn, true);
		$uid 			= $aUid;
		$password	= self::EscapeChars($aPassword);
		
		// Do anonmomous bind and check that id exists
		$r 	= ldap_bind($aDs);
		$sr	= ldap_search($aDs, $basedn, "uid={$uid}");

		// Should be 1 on success
		if(ldap_count_entries($aDs, $sr) == 0) {
			return false;
		}

		//Binding using dn and password...";
		$info	=	ldap_get_entries($aDs, $sr);
		$r		=	@ldap_bind($aDs, $info[0]['dn'], $password);

		if($r) {
			return $aUid;
		}
		
		return false;
	}


	// -------------------------------------------------------------------------------------------
	//
	// Function to escape special characters when using LDAP.
	// Got it from the PHP manual in the user comments.
	// http://www.php.net/manual/en/function.ldap-search.php#90158
	//
	public static function EscapeChars($str, $for_dn = false) {
			// see:
			// RFC2254
			// http://msdn.microsoft.com/en-us/library/ms675768(VS.85).aspx
			// http://www-03.ibm.com/systems/i/software/ldap/underdn.html       
			if  ($for_dn) {
					$metaChars = array(',','=', '+', '<','>',';', '\\', '"', '#');
			} else {
					$metaChars = array('*', '(', ')', '\\', chr(0));
			}
			$quotedMetaChars = array();
			foreach ($metaChars as $key => $value) $quotedMetaChars[$key] = '\\'.str_pad(dechex(ord($value)), 2, '0');
			$str=str_replace($metaChars,$quotedMetaChars,$str); //replace them
			return ($str);
	} 
	

} // End of Of Class


?>