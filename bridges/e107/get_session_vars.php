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
//                                                                             user_id
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

if(!defined("_CHARSET")) exit( );

//replace with e107 stuff  */

if (USERID) {
    // is log in e107, so it's member
    define("isMEMBER", true);
    define("USERUID", USERID);
    define("USERPENNAME", USERNAME);
    // check if it's author, so they are in author table 
    $userdata = dbassoc(dbquery("SELECT ap.*, "._UIDFIELD." as uid, "._PENNAMEFIELD." as penname, "._EMAILFIELD." as email, "._PASSWORDFIELD." as password FROM "._AUTHORTABLE." 
    LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs as ap ON ap.uid = "._UIDFIELD." WHERE "._UIDFIELD." = '".USERUID."'"));
 
    if($userdata && $userdata['level'] != -1 ) {
		define("USERUID", $userdata['uid']);
		define("USERPENNAME", $userdata['penname']);
		// the following line fixes missing authorpref rows
		if(empty($userdata['userskin'] )) dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_authorprefs(uid, userskin, storyindex, sortby, tinyMCE) VALUES('".$userdata['uid']."', '$defaultskin', '$displayindex', '$defaultsort', '$tinyMCE')");
		if(!isset($_SESSION[$sitekey."_skin"]) && !empty($userdata['userskin'])) $siteskin = $userdata['userskin'];
		else if(isset($_SESSION[$sitekey."_skin"])) $siteskin = $_SESSION[$sitekey."_skin"];
		else $siteskin = $defaultskin;
		define("uLEVEL", $userdata['level']);
		define("isADMIN", uLEVEL > 0 ? true : false);
		define("isMEMBER", true);
		if(EMPTY($_SESSION[$sitekey."_agecontsent"])) $ageconsent = $userdata['ageconsent'];
		else $ageconsent = $_SESSION[$sitekey."_agecontsent"];
	} 
}
 
if(!defined("USERUID")) define("USERUID", 0);
if(!defined("USERPENNAME")) define("USERPENNAME", false);
if(!defined("uLEVEL")) define("uLEVEL", 0);
if(!defined("isMEMBER")) define("isMEMBER", false);
if(!defined("isADMIN")) define("isADMIN", false);
if(empty($siteskin)) $siteskin = $defaultskin;
 

?>