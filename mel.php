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

$erreur=0;
$text="";
if (isset($_POST['rappelmel']) && isset($_POST['sid']) && isset($_POST['tid']) && isset($_POST['what'])) {
	if ($_POST['sid']!='' && $_POST['tid']!='') {
		$lmsconnect = vconnect($databaselocation, $lmsbaseuser, $lmsbasepass, $lmsbasename);
		$telconnect = vconnect($databaselocation, $telbaseuser, $telbasepass, $telbasename);
		$requete1 = "SELECT * from {$dbprefix}tokens_".$_POST['sid']." where `tid` = '".$_POST['tid']."' and `completed` = 'N';";
		$resultat = extraire ($lmsconnect, $requete1);
		if ($resultat) {
			$token=intab($resultat);
			if ($debug) {
				$to=$todebug;
			} else $to="\"".$token['firstname'][0]." ".$token['lastname'][0]."\" <".$token['email'][0].">";
		} else $erreur=1;
		
		$requete = "SELECT * FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$_POST['sid']."';";  // recherche du nombre d'exprimé
		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$survey=intab($resultat);
		}			
		$requete = "SELECT * FROM {$dbprefix}surveys WHERE `sid`='".$_POST['sid']."';";  // recherche du nombre d'exprimé
		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$surveyinfo=intab($resultat);
		}
		if (!$erreur && $token['email'][0]!='' && ($_POST['what']=='envoi' || $_POST['what']=='rappel')) {
			if ($_POST['what']=='envoi') {
				$sujet = utf8_encode($survey['surveyls_email_invite_subj'][0]);
				$text.= utf8_encode($survey['surveyls_email_invite'][0]);
			}
			if ($_POST['what']=='rappel') {
				$sujet = utf8_encode($survey['surveyls_email_remind_subj'][0]);
				$text.= utf8_encode($survey['surveyls_email_remind'][0]);
			}
			
			$requete = "SELECT * from {$dbprefix}tokens_".$_POST['sid']." where `tid` = '".$_POST['tid']."' and `completed` = 'N';";
			$res = mysql_query($requete, $lmsconnect);
			$list_champs=array();
			for ( $i = 0; $i < mysql_num_fields($res); $i++ ) {
				$list_champs[$i]= "{".strtoupper(mysql_field_name($res, $i))."}";
				$sujet = str_replace($list_champs[$i],$token[mysql_field_name($res, $i)][0],$sujet);
				$text = str_replace($list_champs[$i],$token[mysql_field_name($res, $i)][0],$text);
			}
			$surveyurl=str_replace('SID',$_POST['sid'],$rooturlrep);
			$surveyurl=str_replace('TOKEN',$token['token'][0],$surveyurl);
			if (stristr($text,'html') && stristr($text,'body')) {
				$surveyurl="<a href=\"".$surveyurl."\">".$surveyurl."</a>";
			}
			$surveylogout=str_replace('SID',$_POST['sid'],$rooturllogout);
			$surveylogout=str_replace('TOKEN',$token['token'][0],$surveylogout);
			if (stristr($text,'html') && stristr($text,'body')) {
				$surveylogout="<a href=\"".$surveylogout."\">".$surveylogout."</a>";
			}
			$text = str_replace("{SURVEYURL}",$surveyurl,$text);
			$text = str_replace("{OPTOUTURL}",$surveylogout,$text);
			
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			if (stristr($text,'html') && stristr($text,'body')) {
				$headers[] = "Content-Type: text/html;charset=iso-8859-1";
			} else {
				$headers[] = "Content-type: text/plain; charset=utf-8";
			}
			$headers[] = "From: ".$surveyinfo['admin'][0]." <".$surveyinfo['adminemail'][0].">";
			$headers[] = "Subject: ".$sujet;
			$headers[] = "X-Mailer: TelSurvey";

			mail($to, $sujet, html_entity_decode($text), implode("\r\n", $headers));
				$ex_text=$text;
				$text= "============================================================\n";
				$text.="                              ok\n";
				$text.= "============================================================\n\n";
				$text.= "[From] : ".$surveyinfo['admin'][0]." <".$surveyinfo['adminemail'][0]."\n";
				$text.= "[To] : ".$to."\n";
				$text.= "[Subject] : ".$sujet."\n";
				$text.= "\n";
				$mess=$text;
				$mess.=$ex_text;
				
				if ($_POST['what']=='envoi') {
					$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `sent` = '".date("Y-m-d H:i")."' WHERE `tid` ='".$_POST['tid']."';";
					$resultat = extraire ($lmsconnect, $requete);
				}
				if ($_POST['what']=='rappel') {
					$token['remindercount'][0]++;
					$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `remindercount` = '".$token['remindercount'][0]."' WHERE `tid` ='".$_POST['tid']."';";
					$resultat = extraire ($lmsconnect, $requete);
					$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `remindersent` = '".date("Y-m-d H:i")."' WHERE `tid` ='".$_POST['tid']."';";
					$resultat = extraire ($lmsconnect, $requete);
				}
				
			} else {
				$mess=" ".H(${$lang}['erreur']);
			}
		$reponse=json_encode(array( 'reprappelmel' => utf8_encode($mess)));
		echo $reponse;
			
			
		}
	}
?>
