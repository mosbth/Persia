<?php
// ===========================================================================================
//
// File: CCaptcha.php
//
// Description: Class CCaptcha
//
// A interface to hide specific implementations of CAPTCHA services. The pagecontroller can
// create a object of this class to use CAPTCHA-services. The actual implementation of the 
// CAPTCHA-service is hidden behind this class. This makes it easier to change and extend the 
// support of more CAPTHCHA services.
//
// Currently supporting:
//  http://recaptcha.net/ through their recaptcha-php library.
//
// Author: Mikael Roos, mos@bth.se
//


require_once(TP_SOURCEPATH . '/recaptcha-php/recaptchalib.php');

class CCaptcha {

	// ------------------------------------------------------------------------------------
	//
	// Internal variables
	//
	public $iErrorMessage;
	

	// ------------------------------------------------------------------------------------
	//
	// Constructor
	//
	public function __construct() { 
		$this->iErrorMessage = ""; 
	}
	

	// ------------------------------------------------------------------------------------
	//
	// Destructor
	//
	public function __destruct() { ; }

	
	// ------------------------------------------------------------------------------------
	//
	// Get HTML to display the CAPTCHA
	//
	public function GetHTMLToDisplay($aStyle='', $use_ssl=false) {
		$this->iErrorMessage 	= ''; 
		$html 								= '';
		$pubkey	= reCAPTCHA_PUBLIC; 
		$server = ($use_ssl) ? RECAPTCHA_API_SECURE_SERVER : RECAPTCHA_API_SERVER;
		$noscript = <<<EOD
<noscript>
   <iframe src="{$server}/noscript?k={$pubkey}"
       height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field" 
       value="manual_challenge">
</noscript>
EOD;

		switch ($aStyle) {

			case 'custom': {
				$html = <<< EOD
<script>
var RecaptchaOptions = {
   theme : '{$aStyle}',
   lang: 'en',
   custom_theme_widget: 'recaptcha_widget'
};
</script>

<div id="recaptcha_widget" style="display:none">
<div id="recaptcha_image"></div>
<input type="text" id="recaptcha_response_field" class="captcha width300px" name="recaptcha_response_field" />
<br />
<span class="recaptcha_only_if_image">Enter the words above:</span>
<span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>
<div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>

<div><a href="javascript:Recaptcha.showhelp()">Help</a>
</div>

<script type="text/javascript"
   src="{$server}/challenge?k={$pubkey}">
</script>
{$noscript}

EOD;
			} break;
			
			case 'red':
			case 'white':
			case 'blackglass':
			case 'clean': {
				$html = <<< EOD
<script>
var RecaptchaOptions = {
   theme : '{$aStyle}',
   lang: 'en',
};
</script>

<script type="text/javascript"
   src="{$server}/challenge?k={$pubkey}">
</script>
{$noscript}

EOD;
			} break;
			
			case '':
			default: {
				$html = recaptcha_get_html($pubkey, null, $use_ssl);
			}
		}
		return $html;
	}

	
	// ------------------------------------------------------------------------------------
	//
	// Validate the answer
	//
	public function CheckAnswer() {
		$this->iErrorMessage = ""; 

		$privatekey = reCAPTCHA_PRIVATE;
		$resp = recaptcha_check_answer ($privatekey,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);

		if (!$resp->is_valid) {
			$this->iErrorMessage = "The reCAPTCHA wasn't entered correctly. Go back and try it again." .
				"(reCAPTCHA said: " . $resp->error . ")";
			return FALSE;
		}
		return TRUE;
	}


} // End of Of Class


?>