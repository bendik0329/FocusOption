<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function secureCode() {
	global $set;
	$html = '<input type="text" requierd name="code" id="code" maxlength="6" style="text-align: center; background: #FFF; border: 1px #CECECE solid; width: 80px;" autocomplete="off"/> <img border="0" src="/rndcode/gfx.php" alt="" align="absmiddle" /><br />'.
	lang('Please type the following word');
	return $html;
	}

function chkSecure($secureCode="") {
	global $_SESSION;
	if (empty($secureCode))
		return false;
	
	if (!$secureCode OR strtolower($_SESSION["codes"]) != strtolower($secureCode)) return false;
		 else return true;

	}

?>