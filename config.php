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

$databasetype='mysql';
$databaselocation='localhost';

$telbasename='telsurvey';		// The name of the database for TelSurvey
$telbaseuser='telsurvey';		// The name of the user for TelSurvey db
$telbasepass='passwd';		// Password of db user

$lmsbasename='limesurvey';		// The name of the database LimeSurvey
$lmsbaseuser='limesurvey';		// The name of the user for LimeSurvey db
$lmsbasepass='passwd';		// Password of db user
$dbprefix='lime_'; 				// prefix of Limesurvey

$params["host"] = 'smtp.xxx.fr';
$params["port"] = '25';

$admin=array("admintoto","usertoto");		// listes des utilisateurs qui seront administrateurs du site (uid CAS)

$lang="fr";

date_default_timezone_set('Europe/Paris');

$skin="blue";

//
// ne pas modifier la suite sauf si répertoire install différent
//



$rooturlsimple  ="http://{$_SERVER['HTTP_HOST']}/telsurvey/";
$rootlms  ="http://{$_SERVER['HTTP_HOST']}/limesurvey";

$rooturlrep  ="http://{$_SERVER['HTTP_HOST']}/limesurvey/index.php?lang=fr&sid=SID&token=TOKEN";
$rooturllogout  ="http://{$_SERVER['HTTP_HOST']}/limesurvey/optout.php?lang=fr&sid=SID&token=TOKEN";

$rootdir  =dirname(__FILE__);

$opacity='0.3';
$opacityfort='0.3';
$divinfoopaK="
	document.getElementById('info1').style.opacity='".$opacity."';
	document.getElementById('info2').style.opacity='".$opacity."';
	document.getElementById('info3').style.opacity='".$opacity."';
	document.getElementById('info4').style.opacity='".$opacity."';
	document.getElementById('info5').style.opacity='".$opacity."';
	document.getElementById('part').style.opacity='".$opacity."';
	";
$divinfonoopaK="
	document.getElementById('info1').style.opacity='1';
	document.getElementById('info2').style.opacity='1';
	document.getElementById('info3').style.opacity='1';
	document.getElementById('info4').style.opacity='1';
	document.getElementById('info5').style.opacity='1';
	document.getElementById('part').style.opacity='1';
	";

include('function.php');
include('langues/langues.php');
?>
