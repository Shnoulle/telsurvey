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
	$title="<div class=title>";
	$title.="Enquetes";
	$title.="</div>";
	//=============================================
	// affichage 
	//=============================================
	if ((isset($_POST['menu']) && $_POST['menu']=='enqlister')) {
		$enqpeupler="";
		$lst_tab="";
		$requete = "SELECT * FROM `peupler` order by `sid` ASC;";
		$resultat = extraire ($telconnect, $requete);
		if ($resultat) {
			$tab_surveys=intab($resultat);
			$lst_tab="";
			for ($i = 0; $i < count($tab_surveys['sid']); $i++) {
				//
				// raz des attribute_50 si date de plus de 30mn
				//
				$requete2 = "SELECT * from {$dbprefix}tokens_".$tab_surveys['sid'][$i]." where `attribute_50` != 'L';";
				$resultat1 = extraire ($lmsconnect, $requete2);
				if ($resultat1) {
					
					$locked=intab($resultat1);
					if ($locked['tid'][0]) {
						for ($j = 0; $j < count($locked['tid']); $j++) {
							$locktmp=explode(" ",$locked['attribute_54'][$j]);
							$locktmp2=explode("-",$locktmp[0]);
							$lockday=$locktmp2[2];
							$suppr='0';
							if (date("d") != $lockday) {
								$suppr='1';
							} else {
								$lockheure=explode(":",$locktmp[1]);
								$lockH=$lockheure[0];
								$lockM=$lockheure[1];
								$lockH=($lockH*60)+$lockM;
								$lockReel=(date("H")*60)+date("i");
								if (($lockReel-$lockH)>'30') {
									$suppr='1';
								}
							}
							if ($suppr=='1') {
								$requete = "UPDATE {$dbprefix}tokens_".$tab_surveys['sid'][$i]." SET `attribute_50` = 'L',`attribute_54` = '0';";  // recherche du nombre d'exprimé
								$resultat1 = extraire ($lmsconnect, $requete);
							}
						}
					}
				}
				
				
				$requete = "SELECT `surveyls_title` FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat1 = extraire ($lmsconnect, $requete);
				if ($resultat1) {
					$title_tmp=intab($resultat1);
					$titre=$title_tmp['surveyls_title'][0];
				}
				
				$lst_tab.="<tr class='nomlst effect' style='cursor:pointer;' 
				onclick=\"tel('".$tab_surveys['sid'][$i]."');\" 
				onmouseover=\"goldvisuetat('".$tab_surveys['sid'][$i]."');document.getElementById('popup').style.display='block';\" onmouseout=\"document.getElementById('popup').style.display='none';\">";
				$requete = "SELECT `active` FROM {$dbprefix}surveys WHERE `sid`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat = extraire ($lmsconnect, $requete);
				$isactived=0;
				if ($resultat) {
					$tabisactived=intab($resultat);
					if ($tabisactived['active'][0]=="Y") {
						$isactived=1;
					}
				}
				
				if ($isactived) {
						$lst_tab.="<td class='m'>".$tab_surveys['sid'][$i]."&nbsp;&nbsp;</td>";
					} else {
						$lst_tab.="<td class='m'><s>".$tab_surveys['sid'][$i]."</s>&nbsp;&nbsp;</td>";
					}
				
				$lst_tab.="<td class='m'>&nbsp;&nbsp;<em>".$tab_surveys['date'][$i]."</em>&nbsp;&nbsp;</td>";
				if ($isactived) {
					$lst_tab.="<td class='l'>&nbsp;&nbsp;".$titre."</td>";
					} else {
					$lst_tab.="<td class='l'>&nbsp;&nbsp;<s>".$titre."</s></td>";
					}
				$lst_tab.="<td class='r'>";
				$lst_tab.="&nbsp;&nbsp;";
				
				$requete2 = "SELECT `completed` from {$dbprefix}tokens_".$tab_surveys['sid'][$i]." where `completed` != 'N';";
				$resultat1 = extraire ($lmsconnect, $requete2);
				if ($resultat1) {
					$nbrepondutmp=intab($resultat1);
					$nbrepondu=count($nbrepondutmp['completed']);
					$requete2 = "SELECT `completed` from {$dbprefix}tokens_".$tab_surveys['sid'][$i].";";
					$resultat1 = extraire ($lmsconnect, $requete2);
					if ($resultat1) {
						$nbinvittmp=intab($resultat1);
						$nbinvit=count($nbinvittmp['completed']);
						if ($nbinvit!='0') {
							$ratiopart=round(($nbrepondu*100)/$nbinvit);
						} else $ratiopart='0';
					} else $ratiopart='0';
				} else $ratiopart='0';
				
				$lst_tab.=$ratiopart."%</td><td><img src=\"images/pct".ratio($ratiopart).".png\">";
				$lst_tab.="&nbsp;&nbsp;</td>";
				$lst_tab.="</tr>";
			}
		}
		
		$enqpeupler.="<div id=enqlister class=''>";
		$enqpeupler.="<br><br>
			<table class='nom' id='tabcorps'>
			<tr class='nom'>
				<td class=''>ID</td>
				<td class=''>".H(${$lang}['e-init'])."</td>
				<td class=''>".H(${$lang}['survey'])."</td>
				<td class='' colspan=2>".H(${$lang}['participation'])."</a></td>
			</tr>";
		$enqpeupler.=$lst_tab;
		$enqpeupler.="</table>";
		$enqpeupler.="</div>";
		if (in_array($uid,$admin)) {
			$enqpeupler.="<br><br><table class='c' >";
			$enqpeupler.="<tr ><td >".H(${$lang}['e-l146'])."</td></tr>";

			$enqpeupler.="</table>";
		}
		$enqpeupler.="<div onMouseOut=\"document.getElementById('popup').style.display='none';\" id=\"popup\" class=\"stat\">";
		$enqpeupler.="</div>";
		$title="<img src=images/info_aide.png>";
		$title.=H(${$lang}['e-title']);
		
		$reponse=json_encode(array( 'title' => utf8_encode($title), 'coeur' => utf8_encode($enqpeupler)));
		echo $reponse;
	}
	
	if ((isset($_POST['menu']) && $_POST['menu']=='enqadd')) {
		$title="<img src=images/info_aide.png>";
		$title.=H(${$lang}['ea-title']);
		$enqpeupler="";
		$requete = "SELECT * FROM {$dbprefix}surveys WHERE `active`='Y' order by `datecreated` DESC;";  // recherche du nombre d'exprimé
		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$tab_surveys=intab($resultat);
			$lst_tab="";
			for ($i = 0; $i < count($tab_surveys['sid']); $i++) {
				$requete = "SELECT `surveyls_title` FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat1 = extraire ($lmsconnect, $requete);
				if ($resultat1) {
					$title_tmp=intab($resultat1);
					$titre=$title_tmp['surveyls_title'][0];
				}
				$requete = "SELECT * FROM `peupler` WHERE `sid`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat1 = extraire ($telconnect, $requete);
				if ($resultat1) {
					$ispeupler_tmp=intab($resultat1);
					if ($ispeupler_tmp['bywho'][0]!='') {
					} else {
						$lst_tab.="<tr class='nomlst effect'>";
						$lst_tab.="<td class='l'>".$tab_surveys['sid'][$i]."</td>";
						$lst_tab.="<td class='m'>&nbsp;&nbsp;<em>".$tab_surveys['datecreated'][$i]."</em>&nbsp;&nbsp;</td>";
						$lst_tab.="<td class='l'>&nbsp;&nbsp;".$titre."&nbsp;&nbsp;</td>";
						$lst_tab.="<td class='l'>";
						$lst_tab.="<div class='xadd' onclick=\"enq('enqaddconfirm','".$tab_surveys['sid'][$i]."');\">X</div>";
						$lst_tab.="</td></tr>";
					}
				}
				
			}
		}
		$enqpeupler.="<div id=enqlister style='border:0px solid;'>";
		$enqpeupler.="<br>
			<table class='nom' id='tabcorps'>
			<tr class='nom'>
				<td class=''>ID</td>
				<td class=''>".H(${$lang}['ea-creele'])."</td>
				<td class=''>".H(${$lang}['survey'])."</td>
				<td class=''>".H(${$lang}['ajouter'])."</td>
			</tr>";
		$enqpeupler.=$lst_tab;
		$enqpeupler.="</table>";
		$enqpeupler.="</div>";
		$reponse=json_encode(array( 'title' => utf8_encode($title), 'coeur' => utf8_encode($enqpeupler)));
		echo $reponse;
	}


	if ((isset($_POST['menu']) && $_POST['menu']=='enqaddconfirm' && isset($_POST['sid']))) {
		$enqpeupler="";
		$forcode='1';
		if ((isset($_POST['confirm']) && $_POST['confirm']=='ok')) {
			if ($forcode=='1') {
				$enqpeupler.="<br>";
				$requete = "insert into `peupler` (`sid` ,`bywho` ,`date`,`etape`) VALUES ('".$_POST['sid']."','".$_SERVER["HTTP_CAS_USER"]."','".date("Y-m-d H:i")."','2');";
				$resultat = extraire ($telconnect, $requete);
				$enqpeupler.=$requete."<br>";
				// base du token :
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_10 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_11 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_12 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_13 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_14 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_15 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_16 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_17 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
	/*
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_18 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
	*/
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_19 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
				$resultat = extraire ($lmsconnect, $requete);
				
				// system
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_50 VARCHAR(10) NOT NULL DEFAULT 'L';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_51 INT(2) NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_52 INT(2) NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_53 VARCHAR(30) NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_54 VARCHAR(30) NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				// tel
	/*
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_100 VARCHAR(3);";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
	*/
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_101 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
	/*
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_102 VARCHAR(3);";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
	*/
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_103 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				// RDV
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_104 VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_105 VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_106 VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				
				
				//Adresse
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_107 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_108 VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";			
				
				
				// ne pas recontacter par mel
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_109 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				// ne souhaite pas participer
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_110 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				// type souhaite participer
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_111 VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_112 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_113 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_114 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_115 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_116 VARCHAR(5);";
				$resultat = extraire ($lmsconnect, $requete);
				// Réponse à la question Comment avez-vous retrouvé le candidat ?
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_201 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_202 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_203 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_204 BOOLEAN NOT NULL DEFAULT '0';";
				$resultat = extraire ($lmsconnect, $requete);
				// Réponse à la question Le candidat a completée son enquête par :
				$requete = "ALTER TABLE {$dbprefix}tokens_".$_POST['sid']." ADD attribute_210 VARCHAR(5);";
				$resultat = extraire ($lmsconnect, $requete);
				$enqpeupler.=$requete."<br>";
				
				$enqpeupler.="<br><br>";
				$enqpeupler.="<input type=button class=action value=Retour onclick=opa_progress('enqlister','coeur');>";
				$requete = "CREATE TABLE IF NOT EXISTS `".$_POST['sid']."_history` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `forid` INT( 5 ) NOT NULL,
				  `what` TEXT NOT NULL,
				  `bywho` varchar(20) NOT NULL,
				  `date` varchar(30) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;";
				$resultat = extraire ($telconnect, $requete);
				//$enqpeupler="confirm nok";
			}
		} else {
			$enqpeupler.="<br><br><br><br><br><br>";
			$requete = "SELECT `surveyls_title` FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$_POST['sid']."';";  // recherche du nombre d'exprimé
			$resultat1 = extraire ($lmsconnect, $requete);
			if ($resultat1) {
				$title_tmp=intab($resultat1);
				$titre=$title_tmp['surveyls_title'][0];
			}
			
			$enqpeupler.="<table class='lbgprogress arr8 c' width='500px'>
			<tr>
				<td class='vbf l' style='color:green;' colspan=2><br><br>";
			$enqpeupler.=H(${$lang}['ea-confirm']);
			$enqpeupler.="<br><br></td></tr><tr class='border'>
				<td class='gf c' colspan=2>".$_POST['sid']." - ".$titre." </td>
			</tr><tr>
				<td class='vbf'colspan=2><br><br><br></td>
			</tr><tr class='border'>
				<td class='c'><input type=button class=action value=".H(${$lang}['oui'])." onclick=enq('enqaddconfirm','".$_POST['sid']."','ok');></td>
				<td class='c'><input type=button class=action value=".H(${$lang}['non'])." onclick=goldppn('enqadd','coeur');></td>
			</tr>";
			$enqpeupler.="</table>";
			
		}
		
		$title="&nbsp;";
		$reponse=json_encode(array( 'title' => $title, 'coeur' => utf8_encode($enqpeupler)));
		echo $reponse;
	}


	if ((isset($_POST['menu']) && $_POST['menu']=='enqsuppr')) {
		$enqpeupler="";
		$lst_tab="";
		$requete = "SELECT * FROM `peupler` order by `date` DESC;";
		$resultat = extraire ($telconnect, $requete);
		if ($resultat) {
			$tab_surveys=intab($resultat);
			$lst_tab="";
			for ($i = 0; $i < count($tab_surveys['sid']); $i++) {
				$requete = "SELECT `surveyls_title` FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$tab_surveys['sid'][$i]."';";  // recherche du nombre d'exprimé
				$resultat1 = extraire ($lmsconnect, $requete);
				if ($resultat1) {
					$title_tmp=intab($resultat1);
					$titre=$title_tmp['surveyls_title'][0];
				}
				
				$lst_tab.="<tr class='nomlst effect'>";
				$requete = "SELECT `active` FROM {$dbprefix}surveys WHERE `sid`='".$tab_surveys['sid'][$i]."';"; 
				$resultat = extraire ($lmsconnect, $requete);
				$isactived=0;
				if ($resultat) {
					$tabisactived=intab($resultat);
					if ($tabisactived['active'][0]=="Y") {
						$isactived=1;
					}
				}
				
				if ($isactived) {
						$lst_tab.="<td class='m'>".$tab_surveys['sid'][$i]."&nbsp;&nbsp;</td>";
					} else {
						$lst_tab.="<td class='m'><s>".$tab_surveys['sid'][$i]."</s>&nbsp;&nbsp;</td>";
					}
				
				$lst_tab.="<td class='m'>&nbsp;&nbsp;<em>".$tab_surveys['date'][$i]."</em>&nbsp;&nbsp;</td>";
				if ($isactived) {
					$lst_tab.="<td class='l'>&nbsp;&nbsp;".$titre."</td>";
					} else {
					$lst_tab.="<td class='l'>&nbsp;&nbsp;<s>".$titre."</s></td>";
					}
				$lst_tab.="<td class='m'>";
				$lst_tab.="<div class='xsuppr' onclick=\"enq('enqsupprconfirm','".$tab_surveys['sid'][$i]."');\">X</div>";
				
				$lst_tab.="</td>";
				$lst_tab.="</tr>";
			}
		}
		
		$enqpeupler.="<div id=enqlister style='border:0px solid;overflow:auto;'>";
		$enqpeupler.="<br><table class='nom' id='tabcorps'>
			<tr class='nom'>
				<td class=''>ID</td>
				<td class=''>".H(${$lang}['e-init'])."</td>
				<td class=''>".H(${$lang}['survey'])."</td>
				<td class=''>".H(${$lang}['suppr'])."</td>
			</tr>";
		$enqpeupler.=$lst_tab;
		$enqpeupler.="</table>";
			if (in_array($uid,$admin)) {
			$enqpeupler.="<br><br><table class='c' >";
			$enqpeupler.="<tr ><td >".H(${$lang}['es-l436'])."</td></tr>";
			$enqpeupler.="</table>";
		}
		$enqpeupler.="</div>";
		$enqpeupler.="<div onMouseOut=\"document.getElementById('popup').style.display='none';\" id=\"popup\" class=\"stat\">";

		$enqpeupler.="</div>";
		//$enqpeupler.="<br><p>Participation % = Nombre 'completed' dans lime_tokens_ID / Nombre 'pas completed' dans lime_tokens_ID *100</p><br>";
		
		$title="<img src=images/info_aide.png>";
		$title.=H(${$lang}['es-title']);
		
			$reponse=json_encode(array( 'title' => utf8_encode($title), 'coeur' => utf8_encode($enqpeupler)));
			echo $reponse;
		//}
		//echo $enqpeupler;
	}
	
	if (isset($_POST['menu']) && $_POST['menu']=='enqsupprconfirm' && isset($_POST['sid'])) {
		if (isset($_POST['confirm']) && ($_POST['confirm']=='ok' || $_POST['confirm']=='ok###his')) {
			if ($_POST['confirm']=='ok###his') {
				$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_51` = '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_52` = '0';";
				$resultat = extraire ($lmsconnect, $requete);
				$requete="DROP table `".$_POST['sid']."_history`";
				$resultat = extraire ($telconnect, $requete);
			}
			$requete="DELETE FROM `peupler` WHERE `sid`='".$_POST['sid']."';";
			$resultat = extraire ($telconnect, $requete);
			$enqsuppr="ok";
		} else {
			$enqsuppr="<br><br><br><br><br><br>";
			$requete = "SELECT `surveyls_title` FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$_POST['sid']."';";  // recherche du nombre d'exprimé
			$resultat1 = extraire ($lmsconnect, $requete);
			if ($resultat1) {
				$title_tmp=intab($resultat1);
				$titre=$title_tmp['surveyls_title'][0];
			}
			$enqsuppr.="<table class='lbgprogress arr8 c' width='500px'>
			<tr>
				<td class='vbf l' style='color:red;' colspan=2><br><br>".H(${$lang}['es-confirm'])."<br><br></td>
			</tr><tr class='border'>
				<td class='gf c' colspan=2>".$_POST['sid']." - ".$titre." </td>
			</tr><tr>
				<td class='vbf'colspan=2><br><br><br></td>
			</tr><tr>
				<td class='vbf m c'><input type=checkbox id=supprhis>".H(${$lang}['es-confirmhis'])." </td>
				<td class='vbf c'><img src=images/screen_histo.png style='width:200px;cursor:default;'><br><br></td>
			</tr><tr class='border'>
				<td class='c'><br><input type=button class=alert value=".H(${$lang}['oui'])." onclick=\"
					if (document.getElementById('supprhis').checked==false) {
							var val='ok';
						}else{
							var val='ok###his';
						}
						enq('enqsupprconfirm','".$_POST['sid']."',val);
						\"><br><br></td>
				<td class='c'><br><input type=button class=alert value=".H(${$lang}['non'])." onclick=goldppn('enqsuppr','coeur');><br><br></td>
			</tr>";
			$enqsuppr.="</table>";
		}
		$title="&nbsp;";
		$reponse=json_encode(array( 'title' => $title, 'coeur' => utf8_encode($enqsuppr)));
		echo $reponse;
	}
	
	if ((isset($_POST['menu']) && $_POST['menu']=='enqetat')) {
		
		$enqetat="";
		$lst_tab="";
				$lst_tab.="<tr class='effect'><td class='r' style=width:180px;></td><td class='l' style=width:150px;></td></tr>";
				//$lst_tab.="<tr  class='effect'><td class='l'>".$_POST['sid']."</td>";
					$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid'].";";
					$resultat = extraire ($lmsconnect, $requete);
					$total='0';
					if ($resultat) {
						$tab=intab($resultat);
						$total=$tab['count(tid)'][0];
					}
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-cdt'])."</td><td class='l'>".$total."</td></tr>";
				
				
					$requete = "select count(`completed`) FROM {$dbprefix}tokens_".$_POST['sid']." where `completed`!='N';";  // recherche du nombre d'exprimé
					$resultat = extraire ($lmsconnect, $requete);
					if ($resultat) {
						$repondutmp=intab($resultat);
						$nb=$repondutmp['count(`completed`)'][0];
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-complet'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";


					$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `attribute_53` != '';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-fmaj'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `attribute_110` = '1';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
					}
					if ($nb!='0') {
						//$ratio=round(100*($nb/$total));
						$ratio="<font color=red>".$nb."</font>";
					} else $ratio="<font color=green>".$nb."</font>";
					//$ratio='0';
				//$lst_tab.="<tr class='border'><td class='l'>Ne veulent pas participer :</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-wns'])."</td><td class='l'>".$ratio."</td></tr>";
				
					$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `attribute_111` = 'net';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-wbnet'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
				$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `attribute_111` = 'tel';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-wbphone'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
				$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `attribute_111` = 'cou';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-wbcour'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT count(tid) from {$dbprefix}tokens_".$_POST['sid']." where `email` != '';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['count(tid)'][0]) {$nb=$tab['count(tid)'][0];}
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-@mel'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_51` > '0';";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-phoneok'])."</td><td class='l'><img src='images/pct".ratio($ratio).".png'> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_51` = '0' and `attribute_52` > '0' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'>".H(${$lang}['ee-failed'])."</td><td class='l'><img src=\"images/ipct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
				// stat propre à l'investigation
				
				$lst_tab.="<tr class='border'><td class='l dcol gf bf' colspan=2>".H(${$lang}['ee-l637'])."</td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_201` = '1' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-safiche'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_202` = '1' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-rs'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_203` = '1' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-bao'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
		
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_204` = '1' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-comp'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
				$lst_tab.="<tr class='border'><td class='l dcol gf bf' colspan=2>".H(${$lang}['ee-repby'])."</td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_210` = 'cour' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-cour'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
					$requete = "SELECT `tid` from {$dbprefix}tokens_".$_POST['sid']." where `attribute_210` = 'tel' ;";
					$resultat = extraire ($lmsconnect, $requete);
					$nb='0';
					if ($resultat) {
						$tab=intab($resultat);
						if ($tab['tid'][0]) {$nb=count($tab['tid']);}
						
					}
					if ($nb!='0') {
						$ratio=round(100*($nb/$total));
					} else $ratio='0';
				$lst_tab.="<tr class='border'><td class='l'> - ".H(${$lang}['ee-phone'])." : </td><td class='l'><img src=\"images/pct".ratio($ratio).".png\"> ".$ratio."% <em>(".$nb.")</em></td></tr>";
				
		//$enqetat.="<div id=enqetat style='border:1px solid;overflow:auto;max-height:300px;'>";
		$enqetat.="<br><table class='dcol bf'>";
		$enqetat.=$lst_tab;
		$enqetat.="</table>";

			$reponse=json_encode(array( 'title' => '', 'coeur' => utf8_encode($enqetat)));
			echo utf8_encode($enqetat);
		//}
		//echo $enqpeupler;
	}



?>
