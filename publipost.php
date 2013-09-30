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
	$forcode='1';
	//=============================================
	// affichage 
	//=============================================
	
	
if ((isset($_POST['menu']) && $_POST['menu']=='publipost') || (isset($_POST['publipost']))) {
	$requete = "SELECT * FROM `peupler` order by `date` DESC;";
	$resultat = extraire ($telconnect, $requete);
	if ($resultat) {
		$tab_surveys=intab($resultat);
		$lst_tab="";
		$publi="<br><br>";
		$publi.="<table class='nom'>";
			$publi.="<tr class=''>";
				$publi.="<td class='gf ncol31 nbg' colspan=2>".H(${$lang}['pb-cond'])."</td>";
			$publi.="</tr>";
			$publi.="<tr class='nomlst'>";
				$publi.="<td class='l'>".H(${$lang}['pb-enqcomp'])."</td>";
				$publi.="<td class='l'>".H(${$lang}['pb-npai'])."</td>";
			$publi.="</tr>";
			$publi.="<tr class='nomlst'>";
				$publi.="<td class='l'>".H(${$lang}['pb-enqimp'])."</td>";
				$publi.="<td class='l'>".H(${$lang}['pb-adresse'])."</td>";
			$publi.="</tr>";
			$publi.="<tr class='nomlst'>";
				$publi.="<td class='l'>".H(${$lang}['pb-wns'])."</td>";
				$publi.="<td class='l'>".H(${$lang}['pb-bycour'])."";
				if (isset($_POST['publipost']) && $_POST['publipost']=='ppcoui') {
					$publi.="<input type=radio name=ppc value=non onclick=\"publipost('ppcnon');\">".H(${$lang}['non'])."
					<input type=radio name=ppc value=oui checked onclick=\"publipost('ppcoui');\">".H(${$lang}['oui'])."</td>";
					$cond = "(`attribute_111`='cou')";
				} else {
					$publi.="<input type=radio name=ppc checked value=non onclick=\"publipost('ppcnon');\">".H(${$lang}['non'])."
					<input type=radio name=ppc value=oui onclick=\"publipost('ppcoui');\">".H(${$lang}['oui'])."</td>";
					$cond = "(`attribute_111`='cou' or `attribute_111`='')";
				}
			$publi.="</tr>";
			$publi.="</table><br>";
		//$publi.="`emailstatus`!='OptOut'and
		//	(`attribute_111`='cou' or `attribute_111`='')
		$publi.="</div>";
		$publi_result="<div id=publi class='l' style='overflow:auto;'>";
			$listdiv='';
			for ($i = 0; $i < count($tab_surveys['sid']); $i++) {
				$listdiv.="pp".$i."/";
			}
			
			for ($i = 0; $i < count($tab_surveys['sid']); $i++) {
				$requete = "SELECT * FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat1 = extraire ($lmsconnect, $requete);
				if ($resultat1) {
					$title_tmp=intab($resultat1);
					$titre=$title_tmp['surveyls_title'][0];
				}
				$publi_result.="<div class='c '>";
					$publi_result.="<table class='c'>";
					
						$publi_result.="<tr id=tr".$i." class='bf effect'>";
						$publi_result.="<td class='l' width='400px'>".$tab_surveys['sid'][$i]."&nbsp;&nbsp;-&nbsp;&nbsp;".$titre."</td>";
						$publi_result.="<td class='l' width='10px'></td>";
						$publi_result.="<td class='l'><div class=xopen id=imgon".$i." onclick=div_progress('".$i."','".$listdiv."','open'); >".H(${$lang}['voir'])."</div></td>";
						$publi_result.="<td class='l'><div class=xclose id=imgoff".$i." onclick=div_progress('".$i."','".$listdiv."','close'); style='display:none;' >".H(${$lang}['fermer'])."</div></td>";
						$publi_result.="<td class='l'><a href=".$rooturlsimple."publipost/".$tab_surveys['sid'][$i].".csv style='text-decoration:none;'><div class=xcsv>".H(${$lang}['pb-csv'])."</div></a></td>";
						$publi_result.="</tr>";
					$publi_result.="</table>";
				$publi_result.="</div>";
				$publi_result.="<div class='c ' id=pp".$i." style='display:none;overflow:auto;'>";
					$publi_result.="<table class='c nom' style='width:100%;'>";
						$publi_result.="<tr class='bf nom '>";
						$publi_result.="<td class='c'>attribute_10</td>";
						$publi_result.="<td class='c'>firstname</td>";
						$publi_result.="<td class='c'>lastname</td>";
						$publi_result.="<td class='c'>token</td>";
						$publi_result.="<td class='c'>attribute_11</td>";
						$publi_result.="<td class='c'>attribute_12</td>";
						$publi_result.="<td class='c'>attribute_107</td>";
						$publi_result.="<td class='c'>attribute_108</td>";
						$publi_result.="<td class='c'>attribute_13</td>";
						$publi_result.="<td class='c'>attribute_14</td>";
						$publi_result.="<td class='c'>attribute_15</td>";
						$publi_result.="</tr>";
						$csv="\"attribute_10\";\"firstname\";\"lastname\";\"token\";\"attribute_11\";\"attribute_12\";\"attribute_107\";\"attribute_108\";\"attribute_13\";\"attribute_14\";\"attribute_15\"\r";
						$requetePUBLI = "SELECT * FROM {$dbprefix}tokens_".$tab_surveys['sid'][$i]." where 
						`emailstatus`!='OptOut'and (`completed`='N' or `completed`='') and
						`attribute_112`='0' and `attribute_110`='0' and `attribute_115`='0' and
						".$cond." and
						`attribute_14`!='' and `attribute_15`!='' and (`attribute_13`!='' or `attribute_107`!='' or `attribute_108`!='') 
						order by `lastname` asc, `firstname` asc;";
						$resultat = extraire ($lmsconnect, $requetePUBLI);
						if ($resultat) {
							$tokens=intab($resultat);
							for ($j = 0; $j < count($tokens['tid']); $j++) {
								$publi_result.="<tr class='effect'>";
								$publi_result.="<td class='r'>".$tokens['attribute_10'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['firstname'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['lastname'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['token'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_11'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_12'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_107'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_108'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_13'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_14'][$j]."</td>";
								$publi_result.="<td class='r'>".$tokens['attribute_15'][$j]."</td>";
								$publi_result.="</tr>";
								$csv.="\"".utf8_encode($tokens['attribute_10'][$j])."\";\"".utf8_encode($tokens['firstname'][$j])."\";\"".utf8_encode($tokens['lastname'][$j])."\";\"".utf8_encode($tokens['token'][$j])."\";\"".utf8_encode($tokens['attribute_11'][$j])."\";\"".utf8_encode($tokens['attribute_12'][$j])."\";\"".utf8_encode($tokens['attribute_107'][$j])."\";\"".utf8_encode($tokens['attribute_108'][$j])."\";\"".utf8_encode($tokens['attribute_13'][$j])."\";\"".utf8_encode($tokens['attribute_14'][$j])."\";\"".utf8_encode($tokens['attribute_15'][$j])."\"\r";
							}
						}
					$publi_result.="</table><br>";
					
					$file=$rootdir."/publipost/".$tab_surveys['sid'][$i].".csv";
					if (file_exists($file)) {$f = fopen($file,'wb');} else $f = fopen($file,'x+');
						fwrite($f,$csv);
						fclose($f);
				$publi_result.="</div>";
			}
		$publi_result.="</div>";
	}
	
	

	if (isset($_POST['publipost'])) {
		$reponse=json_encode(array( 'coeur' => utf8_encode($publi_result)));
		} else {
			$publi_total=$publi.$publi_result;
			$reponse=json_encode(array( 'title' => 'Publipostage',  'coeur' => utf8_encode($publi_total)));
			}
	echo $reponse;
}

?>
