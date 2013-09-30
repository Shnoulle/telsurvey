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

	
	$lmsconnect = vconnect($databaselocation, $lmsbaseuser, $lmsbasepass, $lmsbasename);
	$telconnect = vconnect($databaselocation, $telbaseuser, $telbasepass, $telbasename);
	$effect="";
	//=============================================
	// affichage 
	//=============================================
	if (isset($_POST['reload'])) {
		
		$what="";
		$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." WHERE `tid` ='".$_POST['tid']."';";  // recherche du nombre d'exprimé
		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$tokens=intab($resultat);
		}
		
		if ($_POST['reload']=='numptb') {
			$champ=$tokens['attribute_18'][0];
		}
		
		if ($_POST['reload']=='numfix') {
			$champ=$tokens['attribute_17'][0];
		}
		
		if ($_POST['reload']=='rdvdate') {
			$champ=$tokens['attribute_104'][0];
		}
		
		if ($_POST['reload']=='compl1') {
			$champ=$tokens['attribute_107'][0];
		}
		
		if ($_POST['reload']=='compl2') {
			$champ=$tokens['attribute_108'][0];
		}
		
		if ($_POST['reload']=='rue') {
			$champ=$tokens['attribute_13'][0];
		}
		
		if ($_POST['reload']=='cp') {
			$champ=$tokens['attribute_14'][0];
		}
		
		if ($_POST['reload']=='ville') {
			$champ=$tokens['attribute_15'][0];
		}
		
		if ($_POST['reload']=='email') {
			$champ=$tokens['email'][0];
		}
		
	
		
		$reponse=json_encode(array(  'champ' => utf8_encode($champ)));
			echo $reponse;
	}


//aff_tab($_POST);


?>
