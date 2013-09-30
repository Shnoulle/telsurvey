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

	
	
	$telconnect = vconnect($databaselocation, $telbaseuser, $telbasepass, $telbasename);
	//=============================================
	// affichage 
	//=============================================
	if (isset($_POST['rchcp'])) {
		$requete = "SELECT * FROM `postal_ville` where `code`='".$_POST['rchcp']."' order by `ville` ASC;";
		$resultat = extraire ($telconnect, $requete);
		if ($resultat) {
			$tab_ville=intab($resultat);
			$lst_ville="<table style='width:400px;'>";
			$lst_ville.="<tr class='effectpp' style='background:#eee;' onclick=\"".$divinfonoopaK."document.getElementById('lstville').style.display='';\"><td></td><td></td><td><b>X</b></td></tr>";
			for ($i = 0; $i < count($tab_ville['ID']); $i++) {
					$lst_ville.="<tr class=effectpp onclick=\"".$divinfonoopaK."document.getElementById('ville').value = '".utf8_encode(addslashes($tab_ville['ville'][$i]))."';document.getElementById('lstville').style.display = 'none';\">";
					$lst_ville.="<td class='al'>".addslashes($tab_ville['code'][$i])."</td>";
					$lst_ville.="<td class='al'>".utf8_encode($tab_ville['ville'][$i])."</td>";
					$lst_ville.="<td class='al'>".addslashes($tab_ville['dept'][$i])."</td>";
					$lst_ville.="</tr>";
			}
			$lst_ville.="</table>";
		}
		
		
			$reponse=json_encode(array( 'lstville' => $lst_ville));
			echo $reponse;
	}
	
?>
