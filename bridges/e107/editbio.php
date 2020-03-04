<?php
// ----------------------------------------------------------------------
// eFiction 3.2
// Copyright (c) 2007 by Tammy Keefer
// Valid HTML 4.01 Transitional
// Based on eFiction 1.1
// Copyright (C) 2003 by Rebecca Smallwood.
// http://efiction.sourceforge.net/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

if(!defined("_CHARSET")) exit( );
if(!function_exists("random_char")) {

function random_char($string)
{
	$length = strlen($string);
	$position = mt_rand(0, $length - 1);
	$output = ($string[$position]);
	return $output;
}

function random_string ($charset_string, $length)
{
	$return_string = random_char($charset_string);
	for ($x = 1; $x < $length; $x++)
	$return_string .= random_char($charset_string);
	return $return_string;
}

}
	$uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : false;
	if(!$uid) $uid = USERUID;

	if((!isADMIN || uLEVEL > 2) && $uid != USERUID && $action == "editbio") $output .= write_error(_NOTAUTHORIZED);
	if(isMEMBER) $output .= "<div id=\"pagetitle\">"._EDITPERSONAL."</div>";
	else $output .= "<div id=\"pagetitle\">"._NEWACCOUNT."</div>";
	if(!empty($_POST['submit'])) {
 
 
/* The section adds fields from the authorfields table to the authorinfo table allowing dynamic additions to the bio/registration page */
			$fields = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorfields WHERE field_on = '1'");
			while($field = dbassoc($fields)) {
				$uid = isset($_POST['uid']) && isNumber($_POST['uid']) ? $_POST['uid'] : false;
				if(!$uid) continue;
				$oldfield = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorinfo WHERE field='".$field['field_id']."' AND uid = '".$uid."'");
				if(dbnumrows($oldfield) > 0) {
					$newinfo = isset($_POST["af_".$field['field_name']]) ? escapestring(descript($_POST["af_".$field['field_name']])) : false;
					if(!empty($newinfo)) dbquery("UPDATE ".TABLEPREFIX."fanfiction_authorinfo SET info='".$newinfo."' WHERE uid = '$uid' AND field = '".descript($field['field_id'])."'");
					else dbquery("DELETE FROM ".TABLEPREFIX."fanfiction_authorinfo WHERE uid = '$uid' AND field = '".$field['field_id']."'");
				}
				else if(!empty($_POST["af_".$field['field_name']])) dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_authorinfo(`uid`, `info`, `field`) VALUES('$uid', '".escapestring($_POST["af_".$field['field_name']])."', '".$field['field_id']."');");
			}
/* End dynamic fields */
		   
			$output .= write_message(_ACTIONSUCCESSFUL."  ".(isset($_GET['uid']) ? _BACK2ADMIN : _BACK2ACCT));
	 
	}
	else {
		if($action != "register") {
			$result = dbquery("SELECT * FROM "._AUTHORTABLE." WHERE "._UIDFIELD." = '$uid' LIMIT 1");
			$user = dbassoc($result);
			$result2 = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorinfo WHERE uid = '$uid'");
			while($field = dbassoc($result2)) {
				$user["af_".$field['field']] = $field['info'];
			}
		}
 
		$output .= "<div id='settingsform'>
        <form method=\"POST\" id=\"editbio\" name=\"editbio\" enctype=\"multipart/form-data\" style='margin: 0 auto;' 
        action=\"user.php?action=$action".($uid != USERUID ? "&uid=".$uid : "")."\">
		<div><label for='newpenname'>"._PENNAME.":</label>";
		$output .= " ".$user['user_name'];
		$output .= "</div>
        <div><label for='realname'>"._REALNAME.": </label>";
        $output .= " ".$user['user_realm'];
        $output .= "</div>";
 		$output .= "<div><label for='realname'>"._EMAIL.": </label>";
        $output .= " ".$user['user_email'];
        $output .= "</div>";       
 		$output .= "<div><label for='realname'>"._BIO.": </label>";
        $output .= " ".$user['user_email'];
        $output .= "</div>"; 
	 	 /* The section adds fields to the form from the authorfields table to the authorinfo table allowing dynamic additions to the bio/registration page */
		$authorfields = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorfields WHERE field_on = '1'");
		while($field = dbassoc($authorfields)) {
			if($field['field_type'] == 1 || $field['field_type'] == 4 || $field['field_type'] == 6) 
				$output .= "<div><label for='".$field['field_name']."'>".$field['field_title'].":</label>\n<input type='text' class='textbox' name='af_".$field['field_name']."'".(!empty($user["af_".$field['field_id']]) ? "value='".$user["af_".$field['field_id']]."'" : "").">\n</div>\n";
			if($field['field_type'] == 2) {
				$output .= "<div><label for='".$field['field_name']."'>".$field['field_title'].":</label>\n
						<select class='textbox' name='af_".$field['field_name']."'>\n";
				$opts = explode("|#|", $field['field_options']);
				foreach($opts as $opt) {
					$output .= "<option".(!empty($user["af_".$field['field_id']]) && $user["af_".$field['field_id']] == $opt ? " selected" : "").">$opt</option>\n";
				}
				$output .= "</select>\n</div>\n";
			}
			if($field['field_type'] == 5) eval(stripslashes($field['field_code_in']));
			if($field['field_type'] == 3) {
				$output .= "<div class='fieldset'><span class='label'>".$field['field_title'].":</span>\n";
				$output .= "<input type='radio' name='af_".$field['field_name']."' id='af_".$field['field_name']._YES."' value='"._YES."'".(!empty($user["af_".$field['field_id']]) && $user["af_".$field['field_id']] == _YES ? "checked='checked'" : "")."> <label for='".$field['field_name']._YES."'>"._YES."</label>\n
					<input type='radio' name='af_".$field['field_name']."' id='af_".$field['field_name']._NO."' value='"._NO."'".(!empty($user["af_".$field['field_id']]) && $user["af_".$field['field_id']] == _NO ? "checked='checked'" : "")."> <label for='".$field['field_name']._NO."'>"._NO."</label></div>\n";
			}
		}
/* End dynamic fields */
 	 	$output .= "<div style='text-align: center; margin: 1em;'><INPUT type=\"hidden\" name=\"uid\" value=\"".(isset($user) ? $user['uid'] : "")."\"><INPUT type=\"submit\" class=\"button\" name=\"submit\" value=\""._SUBMIT."\">";
	 	if(!isADMIN && $action != "register")
	 	{
			 	$output .= " [<a href=\"admin.php?action=members&delete=$uid\">"._DELETE."</a>]";
	 	}
	 	$output .= "</div></form></div>".write_message("<font color=\"red\">*</font> "._REQUIREDFIELDS);
	}
?>