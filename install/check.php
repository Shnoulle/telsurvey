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


include('../config.php');

	$top="<html>";
	$top.="<head> <meta http-equiv=Content-Type content=text/html; charset=iso-8859-1><title>TelSurvey</title>
		<link href='../css/ls.css' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='../js/ls.js'></script></head><body>";
	
	$top.="<div class='vbf c'>";
	$top.="<table class='vbf c l'>";
	
	$top.="<tr><td class='l'>Authentification CAS </td>";
		if (isset($_SERVER["REMOTE_USER"]) || isset($_SERVER["HTTP_CAS_USER"])) {
			$top.="<td class='l' style='color:green;'>OK</td></tr>";
		} else $top.="<td class='l' style='color:red;'>erreur</td></tr>";
	
	$top.="<tr><td class='l'>Ecriture dans repertoire publipostage</td>";
		$dir=$rootdir."/publipost/";
		if (file_exists($dir)) {
			$file=$rootdir."/publipost/index.php";
			if (file_exists($file)) {$f = fopen($file,'wb');} else $f = fopen($file,'x+');
				fwrite($f,'');
				fclose($f);
				if (file_exists($file)) {
					$top.="<td class='l' style='color:green;'>OK</td></tr>";
				} else $top.="<td class='l' style='color:red;'>erreur</td></tr>";
			} else $top.="<td class='l' style='color:red;'>erreur (pas de rep \"publipostage\"</td></tr>";
	
/*
	$telconnect = vconnect($databaselocation, $telbaseuser, $telbasepass, $telbasename);
*/
	$top.="<tr><td class='l'>Connexion Database Limesurvey</td>";
	$lmsconnect = mysql_connect($databaselocation, $lmsbaseuser, $lmsbasepass, $lmsbasename);
	if (! $lmsconnect) {
		$top.="<td class='l' style='color:red;'>erreur : mauvais identifiants</td></tr>";
	} else {
		$ok = mysql_select_db($lmsbasename, $lmsconnect);
		if (!$ok) {
			$top.="<td class='l' style='color:red;'>erreur : mauvaise base</td></tr>";
		} else {
			$top.="<td class='l' style='color:green;'>OK</td></tr>";
		}
	}
	$top.="<tr><td class='l'>Connexion Database TelSurvey</td>";
	$telconnect = mysql_connect($databaselocation,  $telbaseuser, $telbasepass, $telbasename);
	if (! $telconnect) {
		$top.="<td class='l' style='color:red;'>erreur : mauvais identifiants</td></tr>";
	} else {
		$ok = mysql_select_db($telbasename, $telconnect);
		if (!$ok) {
			$top.="<td class='l' style='color:red;'>erreur : mauvaise base</td></tr>";
		} else {
			$top.="<td class='l' style='color:green;'>OK</td></tr>";
			$top.="<tr><td class='l'>Creation table Code postal dans TelSurvey</td>";
			exec("mysql -h $databaselocation -D $telbasename -u $telbaseuser --password=$telbasepass <postal_ville.sql");
			$requete="SHOW TABLES from `".$telbasename."`;";
			$resultat=extraire($telconnect, $requete);
			$exist=0;
			while ( $rows=mysql_fetch_array($resultat) ) {
				if ($rows[0] == 'postal_ville') {
					$exist=1;
				}
			}
			if ($exist) {
					$top.="<td class='l' style='color:green;'>OK</td></tr>";
				} else {
					$top.="<td class='l' style='color:red;'>erreur : mauvaise base</td></tr>";
				}
			$top.="<tr><td class='l'>Creation table Peupler dans TelSurvey</td>";
			exec("mysql -h $databaselocation -D $telbasename -u $telbaseuser --password=$telbasepass <peupler.sql");
			$requete="SHOW TABLES from `".$telbasename."`;";
			$resultat=extraire($telconnect, $requete);
			$exist=0;
			while ( $rows=mysql_fetch_array($resultat) ) {
				if ($rows[0] == 'peupler') {
					$exist=1;
				}
			}
			if ($exist) {
					$top.="<td class='l' style='color:green;'>OK</td></tr>";
				} else {
					$top.="<td class='l' style='color:red;'>erreur : mauvaise base</td></tr>";
				}
		}
	}
	$top.="</table>";
	$top.="</div>";
	$top.="</body></html>";
	echo $top;
/*
	phpinfo();
*/

?>
