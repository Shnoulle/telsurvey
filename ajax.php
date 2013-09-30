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

	require_once('config.php');
	if (isset($_SERVER["REMOTE_USER"])) {
		$uid=$_SERVER["REMOTE_USER"];
	} elseif (isset($_SERVER["HTTP_CAS_USER"])) {
		 $uid=$_SERVER["HTTP_CAS_USER"];
	}
	if (isset($_POST['menu'])) {
		if (stristr($_POST['menu'],'publipost')) {include('publipost.php');}
		elseif (stristr($_POST['menu'],'enqlister')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'enqadd')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'enqaddconfirm')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'enqsuppr')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'enqsupprconfirm')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'enqetat')) {include('enq.php');}
		elseif (stristr($_POST['menu'],'tel')) {include('tel.php');}
	}
	
	
	if (isset($_POST['enqsupprconfirm'])) {
		include('enq.php');
	}
	if (isset($_POST['rchcp'])) {
		include('code_postal.php');
	}
	if (isset($_POST['reload'])) {
		include('reload_champ.php');
	}
	if (isset($_POST['rappelmel'])) {
		include('mel.php');
	}
	if (isset($_POST['publipost'])) {
		include('publipost.php');
	}
	if (isset($_POST['test_domain'])) {
		list($ident, $domain) = split( "@", $_POST['test_domain'], 2);
		if (test_domain($_POST['test_domain'])) {
			$Domain="<img class='m' src=images/ok.png width='15px'>&nbsp;(".$domain.")";
			$isOK="1";
		} else {
			$Domain="<font style=color:red;>erreur</font>&nbsp;(".$domain.")";
			$isOK="0";
		}
		$reponse=json_encode(array( 'domain' => utf8_encode($Domain), 'isOK'=>$isOK));
		echo $reponse;
	}
	
?>
