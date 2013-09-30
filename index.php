<?php

/* ======================================================================
 * This file is part of TelSurvey.
 * Copyright © 2012 Université du Maine
 *
 *    TelSurvey is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    TelSurvey is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with TelSurvey (see COPYING).  If not, see <http://www.gnu.org/licenses/>. 
*/


include('config.php');
if(isset($bInternalUserDb) && $bInternalUserDb){
    include('connect.php');
}elseif (isset($_SERVER["REMOTE_USER"])) {
	$uid=$_SERVER["REMOTE_USER"];
} elseif (isset($_SERVER["HTTP_CAS_USER"])) {
	 $uid=$_SERVER["HTTP_CAS_USER"];
}



	$top="<!DOCTYPE html>";
	$top.="<head> <meta http-equiv=Content-Type content=text/html; charset=iso-8859-1><title>TelSurvey</title>
		<link rel='icon' type='image/png' href='images/favicon2.png'>
		<link href='css/ls_".$skin.".css' rel='stylesheet' type='text/css'>
		<link rel='stylesheet' type='text/css' media='all' href='jscal/calendar-brown.css'>
		<script type='text/javascript' src='jscal/calendar.js'></script>
		<script type='text/javascript' src='jscal/lang/calendar-fr.js'></script>
		<script type='text/javascript' src='jscal/calendar-setup.js'></script>
		<script type='text/javascript' src='js/ls.js'></script></head><body>";
	include('menu.php');
	$top.=$menu;
	//$top.=date('Y-m-d H:i:s:').substr(strrchr(microtime(true), "."), 1)."<br>";
	$top.="<div id=tout>";
	//$top.="<div ID='heure_dyna' style='display:none;'><script type='text/javascript'>window.setInterval('heure()',1000);</script></div>";
	$top.="<div id=title></div>";
	$top.="<div id=coeur></div>";
	$top.="</div>";
	
	$top.="<script type='text/javascript'>opa_progress('enqlister','coeur');</script>";
	$top.="<div class='dvpby'>";
	$top.="TelSurvey - <a href=http://telsurvey.univ-lemans.fr>http://telsurvey.univ-lemans.fr</a> - DSI Universite du Maine";
	$top.="</div>";
	$top.="</body></html>";
	echo $top;
	//phpinfo();

?>
