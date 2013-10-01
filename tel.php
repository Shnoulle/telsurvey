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
	$forcode='1';
	//=============================================
	// affichage 
	//=============================================
    if(isset($bInternalUserDb) && $bInternalUserDb){
        include('connect.php');
    }elseif (isset($_SERVER["REMOTE_USER"])) {
	    $uid=$_SERVER["REMOTE_USER"];
    } elseif (isset($_SERVER["HTTP_CAS_USER"])) {
	     $uid=$_SERVER["HTTP_CAS_USER"];
    }
if ((isset($_POST['menu']) && !isset($_POST['maj']) && $_POST['menu']=='tel' && isset($_POST['sid']))) {
	$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_50` = 'L'  WHERE `attribute_50` ='".$uid."';";
	//$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_50` = 'L', `attribute_54` = '' WHERE `attribute_50` ='".$uid."';";
	$resultat = extraire ($lmsconnect, $requete);
// pour quelle raison completed est vide ???? on corrige 
	$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `completed` = 'N' WHERE `completed`='';";
	$resultat = extraire ($lmsconnect, $requete1);
	$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_54` = '0' WHERE `attribute_54`='';";
	$resultat = extraire ($lmsconnect, $requete1);
	if (isset($_POST['suivant']) && $_POST['suivant']!='') {
		// on a cliqué sur "suivant"
		//$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_54` = '".date('Y-m-d H:i:s:').substr(strrchr(microtime(true), "."), 1)."' WHERE `tid` ='".$_POST['suivant']."' and (`attribute_50` ='".$uid."' or `attribute_50` ='L');";
		//
		// pour que le candidat soit en tete de liste car ordre = vide / 0 /date
		// on met à vide si pas pris
		//
		$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_54` = '',`attribute_50` = '".$uid."' WHERE `tid` ='".$_POST['suivant']."' and (`attribute_50` ='".$uid."' or `attribute_50` ='L');";
		$resultat = extraire ($lmsconnect, $requete1);
	}
	if (isset($_POST['tid']) && $_POST['tid']!='') {
		$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_54` = '',`attribute_50` = '".$uid."'  WHERE `tid` ='".$_POST['tid']."' and (`attribute_50` ='".$uid."' or `attribute_50` ='L');";
		$resultat = extraire ($lmsconnect, $requete1);
	}
	$requete = "SELECT `etape` FROM `peupler` where `sid`='".$_POST['sid']."';";
		$resultat = extraire ($telconnect, $requete);
	if ($resultat) {
		$tab_peupler=intab($resultat);
		$etape=$tab_peupler['etape'][0];
	}
	$requete = "SELECT * FROM {$dbprefix}surveys_languagesettings WHERE `surveyls_survey_id`='".$_POST['sid']."';";  // recherche du nombre d'exprimé
	$resultat1 = extraire ($lmsconnect, $requete);
	if ($resultat1) {
		$title_tmp=intab($resultat1);
		$titre=$title_tmp['surveyls_title'][0];
	}
	$title="<br><br>";
	$enqpeupler="";
	
	$titretoken=$titre." (".$_POST['sid'].")";
	$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." where  `attribute_50` = '".$uid."' or`attribute_50` = 'L' order by `attribute_54` ASC, `attribute_53` ASC, `completed` DESC;";  // recherche du nombre d'exprimé

		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$tokens=intab($resultat);
			//aff_tab($tokens);
		}
		
			
							
//
// Tabbleau list des rdv
//
				if ($forcode) {
					// RAZ rdv si date dépassée  ?
					$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." where `attribute_104` is not null AND `attribute_104`!='' order by (`attribute_104`+0) DESC,(`attribute_105`+0) DESC";  // recherche du nombre d'exprimé
					$resultat = extraire ($lmsconnect, $requete);
					if ($resultat) {
						$rdv=intab($resultat);
						if ($rdv['tid'][0]) {
							for($i=0; $i<count($rdv['tid']); $i++) {
								$tmp=explode('/',$rdv['attribute_104'][$i]);
								$daterdv=date("d/m/Y",mktime(0,0,0,$tmp[1],$tmp[0],$tmp[2]));
								$datehier=date("d/m/Y", strtotime('-1 day'));
								if ($daterdv<$datehier) {
									$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_104` = '' WHERE `tid` ='".$rdv['tid'][$i]."';";
									$resultat = extraire ($lmsconnect, $requete);
									$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_105` = '' WHERE `tid` ='".$rdv['tid'][$i]."';";
									$resultat = extraire ($lmsconnect, $requete);
								}
								
							}
						}
					}
					$nbrdv="0";
					$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." where (`attribute_104` is not null AND `attribute_104`!='') or (`attribute_105` is not null AND `attribute_105`!='') order by (`attribute_105`+0) ASC,(`attribute_104`+0) ASC";  // recherche du nombre d'exprimé
					$resultat = extraire ($lmsconnect, $requete);
					$tab_rdv="";
					$tab_rdv.="<div class='lbgprogress rubFirstH'>";
							$tab_rdv.="<table class='rubFirst'>";
								$tab_rdv.="<tr><td class='rubFirst'><div class='titrerub'>".H(${$lang}['t-rdv'])."</div></td></tr>";
								$tab_rdv.="<tr><td>";
												$tab_rdv.="<div id='lstrdv'>";
													$tab_rdv.="<table id=tablstrdv class='bf dcol'>";
										if ($resultat) {
											$rdv=intab($resultat);
											if ($rdv['tid'][0]) {
														for($i=0; $i<count($rdv['tid']); $i++) {
															$tab_rdv.="<tr class='cdt effect'>";
																if ($rdv['attribute_50'][$i]!='L' && $rdv['attribute_50'][$i]!=$uid) {
																	$tab_rdv.="<td class='l dcol gf' style='background:#eaa;'>";
																} else  $tab_rdv.="<td class='l dcol gf' style='cursor:pointer;' onclick=\"tel('".$_POST['sid']."','".$rdv['tid'][$i]."');\">";
																if ($rdv['attribute_105'][$i]==date('G')."h") {
																	$tab_rdv.="<font color=blue>".$rdv['attribute_105'][$i]."&nbsp;</font>";
																} else $tab_rdv.=$rdv['attribute_105'][$i]."&nbsp;";
																if ($rdv['attribute_104'][$i]==date("d/m/Y")) {
																	$tab_rdv.="<font color=blue>aujourd'hui </font> ";
																} else $tab_rdv.="".$rdv['attribute_104'][$i];
																$tab_rdv.=" - ".$rdv['firstname'][$i]." ".$rdv['lastname'][$i]."&nbsp;(".$rdv['tid'][$i].")";
																$tab_rdv.="</td>";
															$tab_rdv.="</tr>";
															$nbrdv++;
														}
											}
										}
													$tab_rdv.="</table>";
												$tab_rdv.="</div>";
									$tab_rdv.="</td></tr>";
							$tab_rdv.="</table>";
						$tab_rdv.="</div>";
				}
							
		
//
// Tabbleau liste des candidats
//			
				

				if ($forcode) {
					//
					// calcul du nombre total de candidat disponibles
					//
					$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid'].";";  // recherche du nombre d'exprimé
					$resultat = extraire ($lmsconnect, $requete);
					if ($resultat) {
						$nbcdt=intab($resultat);
					}
						
					$tidselectsuivant='';
					if (isset($_POST['tid'])) {
						$idenable='1';
						$tidselect=$_POST['tid'];
						// quelle sera le suivant ?

						$key = array_search($tidselect, $tokens['tid']); 
						$tidselectsuivant=$tokens['tid'][$key++];

						
						} elseif (isset($_POST['suivant'])) {
						$idenable='1';
						$tidselect=$_POST['suivant'];
						// quelle sera le suivant ?
						} else {
							$idenable='0';
							for($i=0; $i<count($tokens['tid']); $i++) {
								$requete = "SELECT `attribute_50` FROM {$dbprefix}tokens_".$_POST['sid']." where `tid`='".$tokens['tid'][$i]."';";
								$resultat = extraire ($lmsconnect, $requete);
								//$tidselect=$tokens['tid'][0];
								if ($resultat) {
									$tmp=intab($resultat);
									if ($tmp['attribute_50'][0]=='L') {
										
										break;
									}
								}
							}
						}
						//
						// on met la derniere viste afin qu'un autre opérateur ne le prenne pas  car attribute_54 est vide
						//
						if (isset($tidselect)) {
							$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_50` = '".$uid."', `attribute_54` = '".date('Y-m-d H:i:s:').substr(strrchr(microtime(true), "."), 1)."' WHERE `tid` ='".$tidselect."';";
							$resultat = extraire ($lmsconnect, $requete1);
						}

					
						$black=array();
						$cpt_black='0';
						$green=array();
						$cpt_green='0';
						$red=array();
						$cpt_red='0';
						$orange=array();
						$cpt_orange='0';
						for($i=0; $i<count($tokens['tid']); $i++) {
							$tid=$tokens['tid'][$i];
							if (isset($_POST['tid']) && $_POST['tid']==$tokens['tid'][$i] && $tokens['attribute_50'][$i]==$uid) {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="";
								//$black['img'][$cpt_black]="eta-appel.png";
								$cpt_black++;
								//$tidselect=$i;
							} elseif (isset($_POST['suivant']) && $_POST['suivant']==$tokens['tid'][$i] && $tokens['attribute_50'][$i]==$uid) {
							//} elseif (isset($_POST['suivant']) && $_POST['suivant']==$tokens['tid'][$i-1] && $tokens['attribute_50'][$i]==$uid) {
								// c celui en cours normalement après SUIVANT
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="";
								//$black['img'][$cpt_black]="eta-appel.png";
								//$tidselect=$i;
								$cpt_black++;
							} elseif (($tokens['completed'][$i]!='N' && $tokens['completed'][$i]!='' && $tokens['completed'][$i]!='want no remind') || $tokens['completed'][$i]=='Y') {
								$green['index'][$cpt_green]=$tid;
								$green['img'][$cpt_green]="eta-fin.png";
								$cpt_green++;
							//} elseif ($tokens['completed'][$i]=='want no remind' || ($tokens['emailstatus'][$i]=='OptOut' && $tokens['attribute_109'][$i]=='0')) {
							} elseif ($tokens['completed'][$i]=='want no remind' || $tokens['emailstatus'][$i]=='OptOut' ) {
								$red['index'][$cpt_red]=$tid;
								$red['img'][$cpt_red]="eta-refusmel.png";
								$cpt_red++;
							} elseif ($tokens['attribute_115'][$i]=='1' ||$tokens['attribute_110'][$i]=='1') {
								$red['index'][$cpt_red]=$tid;
								$red['img'][$cpt_red]="eta-refus.png";
								$cpt_red++;
							} elseif ($tokens['attribute_50'][$i]!='L' && $tokens['attribute_50'][$i]!=$uid) {
								// bloqué par une autre opérateur
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="used";
								$cpt_black++;
//
// on traite d'abord les cas en erreur
// exemple : demande à répondre par mel mais pas d'adresse....
//net/cour tel
							} elseif ($tokens['attribute_111'][$i]=='net' && (strlen($tokens['email'][$i])<'3' || $tokens['attribute_109'][$i]=='1')) {
								$orange['index'][$cpt_orange]=$tid;
								$orange['img'][$cpt_orange]="eta-erreur.png";
								$cpt_orange++;
							} elseif ($tokens['attribute_111'][$i]=='tel' 
								&& ($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
								&& ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')) {
								$orange['index'][$cpt_orange]=$tid;
								$orange['img'][$cpt_orange]="eta-erreur.png";
								$cpt_orange++;
							} elseif ($tokens['attribute_111'][$i]=='cour' 
								&& (strlen($tokens['attribute_14'][$i])<'3' || strlen($tokens['attribute_15'][$i])<'3' || $tokens['attribute_112'][$i]=='0')) {
								$orange['index'][$cpt_orange]=$tid;
								$orange['img'][$cpt_orange]="eta-erreur.png";
								$cpt_orange++;
/*
							} elseif ($tokens['attribute_111'][$i]=='' 
								&& ($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
								&& ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')
								&& ((strlen($tokens['attribute_14'][$i])<'3' || strlen($tokens['attribute_15'][$i])<'3') && $tokens['attribute_112'][$i]=='0')) {
								$orange['index'][$cpt_orange]=$tid;
								$orange['img'][$cpt_orange]="eta-erreur.png";
								$cpt_orange++;
*/
							
//
// cekisuit : 
//	tokens['attribute_50'][$i]=='L'
// 	et $tokens['completed'][$i]=='N'
//	et $tokens['attribute_115'][$i]=='1'
//
// priorité : mel / courrier /tel

							} elseif ($tokens['attribute_111'][$i]=='net') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-mel.png";
								$cpt_black++;
							} elseif ($tokens['attribute_111'][$i]=='cou') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-cour.png";
								$cpt_black++;
							} elseif ($tokens['attribute_111'][$i]=='tel') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-tel.png";
								$cpt_black++;
							} elseif (($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
									 && ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')
									 && ($tokens['attribute_109'][$i]=='1' || strlen($tokens['email'][$i])<'3')
									 && $tokens['attribute_112'][$i]=='1') {
								$red['index'][$cpt_red]=$tid;
								$red['img'][$cpt_red]="eta-refusimp.png";
								$cpt_red++;
							} elseif (($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
							 && ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')
							 && $tokens['attribute_109'][$i]=='1'
							 && $tokens['attribute_112'][$i]=='0') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-cour.png";
								$cpt_black++;
							} elseif (($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
							 && ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')
							 && $tokens['email'][$i]!=''
							 && $tokens['attribute_109'][$i]=='0'
							 && $tokens['attribute_112'][$i]=='1') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-mel.png";
								$cpt_black++;
							} elseif (($tokens['attribute_103'][$i]=='1' || $tokens['attribute_113'][$i]=='1' || strlen($tokens['attribute_19'][$i])<'3')
							 && ($tokens['attribute_101'][$i]=='1' || $tokens['attribute_114'][$i]=='1' || strlen($tokens['attribute_17'][$i])<'3')
							 && (strlen($tokens['email'][$i])<'3' || $tokens['attribute_109'][$i]=='1')
							 && (strlen($tokens['attribute_14'][$i])>'3' && strlen($tokens['attribute_15'][$i])>'2' && $tokens['attribute_112'][$i]=='0')
							 && $tokens['attribute_111'][$i]=='') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-lm-cour.png";
								$cpt_black++;
							} elseif ((strlen($tokens['attribute_19'][$i])<'3'
							 || strlen($tokens['attribute_17'][$i])<'3'
							 || strlen($tokens['email'][$i])<'3'
							 || ((strlen($tokens['attribute_14'][$i])>'3' && strlen($tokens['attribute_15'][$i])>'2') || $tokens['attribute_112'][$i]=='1'))
							 && ($tokens['attribute_103'][$i]=='0' || $tokens['attribute_113'][$i]=='0')
							 && ($tokens['attribute_114'][$i]=='0' || $tokens['attribute_101'][$i]=='0')
							 && $tokens['attribute_109'][$i]=='0') {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-appel.png";
								$cpt_black++;
							} else {
								$black['index'][$cpt_black]=$tid;
								$black['img'][$cpt_black]="eta-appel.png";
								$cpt_black++;
							}
						}
						
//
// on affiche
//
					if ($forcode) {
						$lst_cdts="";
						$lst_cdts.="<div class='lbgprogress rubFirstH'>";
							$lst_cdts.="<table class='rubFirst'>";
								$ico1="&nbsp;<img src='images/bargraph.png' class='m' height='18px' onmouseout=\"document.getElementById('popup').style.display='none';document.getElementById('info_candidat').style.opacity='';\" onmouseover=\"goldvisuetat('".$_POST['sid']."');document.getElementById('popup').style.display='block';document.getElementById('info_candidat').style.opacity='".$opacity."';\">&nbsp;";
								$ico2="&nbsp;<img src='images/btn_aide.png' class='m' height='18px' onmouseout=\"document.getElementById('legende').style.display='none';document.getElementById('info_candidat').style.opacity='';\" onmouseover=\"document.getElementById('legende').style.display='block';document.getElementById('info_candidat').style.opacity='".$opacity."';\">&nbsp;";
								$lst_cdts.="<tr><td class='rubFirst'><div class='titrerub'>".count($tokens['tid'])." / ".count($nbcdt['tid'])." ".H(${$lang}['cdt'])."&nbsp;&nbsp;&nbsp;".$ico1.$ico2."</div></td></tr>";
								$lst_cdts.="<tr><td>";
									$lst_cdts.="<div id='lstcandeta2' style='overflow:auto;'>";
										$lst_cdts.="<table id=tablstcandeta2 class='bf dcol'>";
										$nb='0';
										$style2='color:#aaa;font-style : italic; ';
										$tabtype=array("orange","black","red","green");
										for($k=0; $k<count($tabtype); $k++) {
											if (isset(${$tabtype[$k]}['index'][0])) {
												for($j=0; $j<count(${$tabtype[$k]}['index']); $j++) {
													$tmpclick='0';
													$style="color:".$tabtype[$k].";";
													//$i=${$tabtype[$k]}['index'][$j];
													$itmp=${$tabtype[$k]}['index'][$j];
													$i=array_search($itmp,$tokens['tid']);
													//if ($k==3) {echo $i." - ".$tokens['tid'][$realindex]." - ".$tokens['lastname'][$i]." / ";}
													$nomprenom="";
													if (strlen($tokens['lastname'][$i]) > 12) {
														$nomprenom.=strtoupper(substr($tokens['lastname'][$i],0,11)).". </b>";
													} else $nomprenom.=strtoupper($tokens['lastname'][$i])." </b>";
													if (strlen($tokens['firstname'][$i]) > 12) {
														$nomprenom.=substr($tokens['firstname'][$i],0,11).".";
													} else $nomprenom.=$tokens['firstname'][$i];
													if (isset($_POST['tid']) || isset($_POST['suivant'])) {
														$tmpclick='0';
														if (isset($_POST['tid']) && $_POST['tid']==$tokens['tid'][$i]) {
															$tidselect=$_POST['tid'];
															$tmpclick='1';
															$imgencours=$_POST['img'];
														}
														if (isset($_POST['suivant']) && $_POST['suivant']==$tokens['tid'][$i]) {
															$tidselect=$_POST['suivant'];
															$tmpclick='1';
															$imgencours=$_POST['img'];
														}
														if ($tmpclick) {
															$idenable='1';
															// quelle sera le suivant ?
															//$key=${$tabtype[$k]}['index'][$j+1];
															$keytmp=${$tabtype[$k]}['index'][$j+1];
															$key=array_search($keytmp,$tokens['tid']);
															$tidselectsuivant=$tokens['tid'][$key];
															$imgsuivant=${$tabtype[$k]}['img'][$j+1];
															$lst_cdts.="<tr class='sf' style='background: #afa;'>";
														} else $lst_cdts.="<tr class='effect cdt' onclick=\"tel('".$_POST['sid']."','".$tokens['tid'][$i]."','','".${$tabtype[$k]}['img'][$j]."');\">";
													} elseif ($k==1 && $j==0) {
													$lst_cdts.="<tr class='sf' style='background: #afa;'>";

															// quelle sera le suivant ?
															$tidselect=$tokens['tid'][$i];
				/*
															$key=${$tabtype[$k]}['index'][$j+1];
															$tidselectsuivant=$tokens['tid'][$key];
				*/
															$keytmp=${$tabtype[$k]}['index'][$j+1];
															$key=array_search($keytmp,$tokens['tid']);
															$tidselectsuivant=$tokens['tid'][$key];
															$imgsuivant=${$tabtype[$k]}['img'][$j+1];
															$imgencours=${$tabtype[$k]}['img'][$j];
/*
															$_POST['img']=${$tabtype[$k]}['img'][$j];
*/
													$idenable='1';
													} else $lst_cdts.="<tr class='effect cdt' onclick=\"tel('".$_POST['sid']."','".$tokens['tid'][$i]."','','".${$tabtype[$k]}['img'][$j]."');\">";
													
														$lst_cdts.="<td class='l' style='".$style."'>".$nomprenom."</td><td class='r' style='".$style."'>";
														if (!$tmpclick && !($k==1 && $j==0)) {
															$lst_cdts.="<img src=images/".${$tabtype[$k]}['img'][$j]." width='20px'>";
														}
														$lst_cdts.="</td><td class='l' style='".$style2."'>".$tokens['tid'][$i]."</td></tr>";
													$nb++;
													//if (!$idenable && $tabtype[$k]!="orange") {$tidselect=$tokens['tid'][$i];$tidselectsuivant=$tokens['tid'][$i+1];$idenable='1';}
												}
											}
										}
										$lst_cdts.="</table>";
									$lst_cdts.="</div>";
								$lst_cdts.="</td></tr>";
							$lst_cdts.="</table>";
						$lst_cdts.="</div>";
					}
					
// tab pris par autre operateur
					if ($forcode) {
						$by_autre_ope="";
						$by_autre_ope.="<div class='lbgprogress rubFirstH'>";
							$by_autre_ope.="<table class='rubFirst'>";
								$by_autre_ope.="<tr><td class='rubFirst'><div class='titrerub'>".H(${$lang}['t-ope'])."</div></td></tr>";
								$by_autre_ope.="<tr><td>";
									$by_autre_ope.="<div id='lstcandeta3' style='overflow:auto;'>";
										$by_autre_ope.="<table id=tablstcandeta3 class='bf dcol'>";
											$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." where  `attribute_50` != '".$uid."' and `attribute_50` != 'L' ;";  // recherche du nombre d'exprimé
											$resultat = extraire ($lmsconnect, $requete);
											if ($resultat) {
												$nbcdtpris=intab($resultat);
												for($i=0; $i<count($nbcdtpris['tid']); $i++) {
													$nomprenom="";
													if (strlen($nbcdtpris['lastname'][$i]) > 10) {
														$nomprenom.=strtoupper(substr($nbcdtpris['lastname'][$i],0,9)).". </b>";
													} else $nomprenom.=strtoupper($nbcdtpris['lastname'][$i])." </b>";
													if (strlen($nbcdtpris['firstname'][$i]) > 10) {
														$nomprenom.=substr($nbcdtpris['firstname'][$i],0,9).".";
													} else $nomprenom.=$nbcdtpris['firstname'][$i];
													$by_autre_ope.="<tr><td class='l' >".$nomprenom."</td><td class='l' >".$nbcdtpris['tid'][$i]."</td></tr>";
												}
											} else {
												$by_autre_ope.="<tr><td class='l sf' >vide</td></tr>";
											}
										$by_autre_ope.="</table>";
									$by_autre_ope.="<br></div>";
								$by_autre_ope.="</td></tr>";
							$by_autre_ope.="</table>";
						$by_autre_ope.="</div>";
					}
					
				}
				
				
				
					
					if ($idenable) {
						$requete="select * from {$dbprefix}tokens_".$_POST['sid']." where `tid`='".$tidselect."';";
						$resultat = extraire ($lmsconnect, $requete);
						if ($resultat) {
								$fichecdt=intab($resultat);
							}
							//print_r($fichecdt);
						
							$requete1="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_50` = '".$uid."', `attribute_54` = '".date('Y-m-d H:i:s:').substr(strrchr(microtime(true), "."), 1)."' WHERE `tid` ='".$tidselect."';";
							$resultat = extraire ($lmsconnect, $requete1);
							$requete="select count(what) from `".$_POST['sid']."_history` where `forid`='".$tidselect."';";
							$resultat = extraire ($telconnect, $requete);
							$nbintvfiche='0';
							if ($resultat) {
								$nbintvfichetmp=intab($resultat);
								$nbintvfiche=$nbintvfichetmp['count(what)'][0];
							}

					
if ($forcode) {
	
	
	$simodif="onKeyDown=\"document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';document.getElementById('suivant').style.display='none';";
	$simodif.="document.getElementById('btncenq').style.display='none';document.getElementById('sendrappel').style.display='none';\"";
	$simodif2="onclick=\"document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';document.getElementById('suivant').style.display='none';";
	$simodif2.="document.getElementById('btncenq').style.display='none';document.getElementById('sendrappel').style.display='none';\"";
	$simodif3="onclick=\"".$divinfoopaK."document.getElementById('lsthour').style.display='block';document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';document.getElementById('suivant').style.display='none';";
	$simodif3.="document.getElementById('btncenq').style.display='none';document.getElementById('sendrappel').style.display='none';\"";

	$simodif4="onKeyDown=\"document.getElementById('typelog1').checked=false;document.getElementById('typelog2').checked=false;document.getElementById('typelog3').checked=false;";
	$simodif4.="document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';document.getElementById('suivant').style.display='none';";
	$simodif4.="if (document.getElementById('btncenq')) {document.getElementById('btncenq').style.display='none';}";
	$simodif4.="if (document.getElementById('sendrappel')) {document.getElementById('sendrappel').style.display='none';}\"";

	$simodif5="onclick=\"document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';document.getElementById('suivant').style.display='none';";
	$simodif5.="document.getElementById('btncenq').style.display='none';document.getElementById('sendrappel').style.display='none';\"";
}

// Nom Prenom diplome
							if ($forcode) {
								$ndp_cdt="<div class=' l'>";
									$ndp_cdt.="<table width='100%' height='35px'>";
										$ndp_cdt.="<tr>";
											$ndp_cdt.="<td class='ncol bf m l' width='40px'>";
												$ndp_cdt.="n&deg;".$fichecdt['tid'][0];
											$ndp_cdt.="</td>";
											$ndp_cdt.="<td class='dcol bf l m' >";
												$ndp_cdt.=$fichecdt['attribute_10'][0]."<br><e class='r dcol maxf'>".$fichecdt['firstname'][0]."<b> ".strtoupper($fichecdt['lastname'][0])."</B></e>";
											$ndp_cdt.="</td>";
											$ndp_cdt.="<td class='l m '>";
												$ndp_cdt.="<e class='r dcol vbf'> ".$titretoken."</e>";
												$ndp_cdt.="<br>";
												$ndp_cdt.="<e class='r dcol vbf'> ".$fichecdt['attribute_11'][0]." - ".$fichecdt['attribute_12'][0]."</e>";
											$ndp_cdt.="</td>";
											$ndp_cdt.="<td class='m' width='50px'>";
												$ndp_cdt.=" <e class='r ncol bf' ID='heure_dyna'></e>";
											$ndp_cdt.="</td></tr>";
									$ndp_cdt.="</table>";
								$ndp_cdt.="</div>";
							}
// log des activités de l'opérateur sur fiche
							if ($forcode) {
								$ope_fiche="";
								$ope_fiche.="<div class='rubFirst'>";
									$ope_fiche.="<table class='rubFirst'>";
										$ope_fiche.="<tr><td class='rubFirstForV'><div class='titrerubrotv'>".H(${$lang}['t-l527'])."</div></td>";
											$ope_fiche.="<td>";
												$ope_fiche.="<table class='bf dcol'>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-statut'])."</td><td class='l dcol gf'><img class='l' src=images/".$imgencours." width='30px'></td></tr>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-intvf'])."</td><td class='l col000'>".$nbintvfiche."</td></tr>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-appelok'])."</td><td class='l col000'>".$fichecdt['attribute_51'][0]."</td></tr>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-appelnok'])."</td><td class='l col000'>".$fichecdt['attribute_52'][0]."</td></tr>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-invsend'])."</td><td class='l col000'>";
													if ($fichecdt['sent'][0]=='N') {
														$ope_fiche.="<font style='color:red'>";
													} else {
														$ope_fiche.="<font style='color:green'>";
													}
													$ope_fiche.=str_replace(' ','<br>',$fichecdt['sent'][0]);
													$ope_fiche.="</font></td></tr>";
													$ope_fiche.="<tr><td class='r'>".H(${$lang}['t-rappel'])."</td><td class='l col000'>".$fichecdt['remindercount'][0]."</td></tr>";
												$ope_fiche.="</table>";
											$ope_fiche.="</td>";
										$ope_fiche.="</tr>";
									$ope_fiche.="</table>";
								$ope_fiche.="</div>";
							}
// log maj
							if ($forcode) {
								$log_maj="";
								$log_maj.="<div class='rubFirst'>";
									$log_maj.="<table class='rubFirst'>";
										$log_maj.="<tr><td class='rubFirstForV' style='height:82px;'><div class='titrerubrotv' >".H(${$lang}['historique'])."</div></td>";
										$log_maj.="<td>";
											$requete="select * from ".$_POST['sid']."_history where `forid`='".$tidselect."' order by `id` desc;";
											$resultat = extraire ($telconnect, $requete);
											if ($resultat) {
												$history=intab($resultat);
												if ($history['id'][0]) {
													$log_maj.="<div  style='overflow:auto;max-height:80px;height:80px;'>";
														$log_maj.="<table class='bf ncol' style='border-collapse:collapse;' width='100%'>";
														for ($j = 0; $j < count($history['id']); $j++) {
															$log_maj.="<tr class='dcol sf'>";
															$log_maj.="<td class='l'>".$history['date'][$j]." ".H(${$lang}['par'])." ".$history['bywho'][$j]."</td>";
															$log_maj.="</tr><tr>";
															$log_maj.="<td class='r col000' colspan=2>".$history['what'][$j]."</td>";
															$log_maj.="</tr>";
														}
														$log_maj.="</table>";
													$log_maj.="</div>";
												}
											}
											$log_maj.="</td>";
										$log_maj.="</tr>";
									$log_maj.="</table>";
								$log_maj.="</div>";
							}
// renseignement suppl
							if ($forcode) {
								$rens_sup="";
								$rens_sup.="<div class='rubFirst2'>";
									$rens_sup.="<table class='rubFirst'>";
										$rens_sup.="<tr><td class='rubFirstForV'><div class='titrerubrotv' >".H(${$lang}['t-suppl'])."</div></td>";
											$rens_sup.="<td>";
												$rens_sup.="<table class='bf ncol c'>";
													$rens_sup.="<tr>";
														$rens_sup.="<td width='10px'></td>";
														$rens_sup.="<td class='dcol l m' colspan=3>";
															$rens_sup.=H(${$lang}['t-ques']);
														$rens_sup.="</td>";
														$rens_sup.="</tr>";
													$rens_sup.="<tr class='effect'>";
														$rens_sup.="<td></td>";
														$rens_sup.="<td class='l m' colspan=3>";
															$rens_sup.="<input type=checkbox id=viafiche ".$simodif5." ";
																if ($fichecdt['attribute_201'][0]=='1') {$rens_sup.=" checked ";}
																$rens_sup.=">".H(${$lang}['ee-safiche'])."<br>";
															$rens_sup.="<input type=checkbox id=viars ".$simodif5." ";
																if ($fichecdt['attribute_202'][0]=='1') {$rens_sup.=" checked ";}
																$rens_sup.=">".H(${$lang}['ee-rs'])."<br>";
															$rens_sup.="<input type=checkbox id=viabao ".$simodif5." ";
																if ($fichecdt['attribute_203'][0]=='1') {$rens_sup.=" checked ";}
																$rens_sup.=">".H(${$lang}['ee-bao'])."<br>";
															$rens_sup.="<input type=checkbox id=viacomp ".$simodif5." ";
																if ($fichecdt['attribute_204'][0]=='1') {$rens_sup.=" checked ";}
																$rens_sup.=">".H(${$lang}['ee-comp'])."<br>";
														$rens_sup.="</td>";
														$rens_sup.="</tr>";
														$rens_sup.="<tr class='usf'><td colspan=4><br></td></tr>";
													$rens_sup.="<tr id='howhasbeencompletedQ' ";
													if ($fichecdt['completed'][0]=='N') {
														$rens_sup.="style='opacity:".$opacity.";'";
													}
													$rens_sup.=">";
														$rens_sup.="<td ></td>";
														$rens_sup.="<td class='dcol l m' colspan=3>";
															$rens_sup.=H(${$lang}['t-how'])." :";
														$rens_sup.="</td>";
														$rens_sup.="</tr>";
													$rens_sup.="<tr class='effect' id='howhasbeencompletedA' ";
													if ($fichecdt['completed'][0]=='N') {
														$rens_sup.="style='opacity:".$opacity.";'";
													}
													$rens_sup.=">";
														$rens_sup.="<td></td>";
														$rens_sup.="<td class='l m' colspan=2>";
															$rens_sup.="<input type=radio id=completedby1 value=cour name=completedby  ".$simodif5." ";
																if ($fichecdt['attribute_210'][0]=='cour') {$rens_sup.=" checked ";}
																$rens_sup.=">".H(${$lang}['ee-cour'])."<br>";
															$rens_sup.="<input type=radio id=completedby2 value=tel name=completedby ".$simodif5." ";
																if ($fichecdt['attribute_210'][0]=='tel') {$rens_sup.=" checked ";}
																$rens_sup.=" >".H(${$lang}['ee-phone'])."<br>";
														$rens_sup.="</td>";
														$rens_sup.="<td class='raz'>";
															$rens_sup.="<input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
													document.getElementById('btnmaj').style.display='';
													document.getElementById('annuler').style.display='';
													if (document.getElementById('btncenq')) {document.getElementById('btncenq').style.display='none';}
													document.getElementById('divwns').style.display='';
													document.getElementById('suivant').style.display='none';
													document.getElementById('sendrappel').style.display='none';
													document.getElementById('completedby1').checked=false;
													document.getElementById('completedby2').checked=false;\">";
														$rens_sup.="</td>";
													$rens_sup.="</tr>";
												$rens_sup.="</table>";
											$rens_sup.="</td>";
										$rens_sup.="</tr>";
									$rens_sup.="</table>";
								$rens_sup.="</div>";
							}
// Portable
							if ($forcode) {
								if ($fichecdt['attribute_110'][0]=='1' ||$fichecdt['attribute_115'][0]=='1' ||$fichecdt['attribute_103'][0]=='1' || $fichecdt['attribute_113'][0]=='1') {
									$thisstyle="opacity:".$opacityfort.";";
								} else $thisstyle="";
								$enq_ptb="<div id=info1 class='rub'>";
									$enq_ptb.="<div id=telptb style='".$thisstyle."'>";
										$enq_ptb.="<table class='c'>";
											$enq_ptb.="<tr class='effect'>";
												$enq_ptb.="<td class='titrerubcdt'>".H(${$lang}['t-ptb'])."</td>";
												$enq_ptb.="<td class='c m'> : </td>";
												$enq_ptb.="<td class='l'><input type=text size=14 id=numptb value=\"".$fichecdt['attribute_19'][0]."\" ".$simodif." onKeyPress='return isnumspace(event);' ></td>";
												$enq_ptb.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','numptb');></td>";
											$enq_ptb.="</tr>";
											$enq_ptb.="<tr class='usf'><td colspan=4><br></td></tr>";
											$enq_ptb.="<tr class='effect'><td class='r dcol m'>".H(${$lang}['t-aec'])."</td>";
												$enq_ptb.="<td class='c m'> : </td>";
												$enq_ptb.="<td class='l'>";
													$enq_ptb.="<input type=radio id=encoursptb1 name=encoursptb value=ok ".$simodif2.">".H(${$lang}['ok'])."<br>";
													$enq_ptb.="<input type=radio id=encoursptb2 name=encoursptb value=rep ".$simodif2.">".H(${$lang}['t-rep'])."<br>";
													$enq_ptb.="<input type=radio id=encoursptb3 name=encoursptb value=nb ".$simodif2.">".H(${$lang}['t-nbd']);
												$enq_ptb.="</td>";
												$enq_ptb.="<td class='raz'><input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
												document.getElementById('encoursptb1').checked=false;
												document.getElementById('encoursptb2').checked=false;
												document.getElementById('encoursptb3').checked=false;
														document.getElementById('btnmaj').style.display='';
														document.getElementById('annuler').style.display='';
														document.getElementById('btncenq').style.display='none';
														document.getElementById('suivant').style.display='none';
														document.getElementById('sendrappel').style.display='none';
												\"></td>";
											$enq_ptb.="</tr>";
											$enq_ptb.="<tr class='usf'><td colspan=4><br></td></tr>";
										$enq_ptb.="</table>";
									$enq_ptb.="</div>";
									$enq_ptb.="<table class='c'>";
										$enq_ptb.="<tr class='effect'>";
											$enq_ptb.="<td class='l'>";
											$ptbonlick="onclick=\"
												if (document.getElementById('mnptb').checked==true || document.getElementById('noremindptb').checked==true) {
													document.getElementById('telptb').style.opacity='".$opacityfort."';
													document.getElementById('rappelptb').disabled=true;
												} else {
													document.getElementById('telptb').style.opacity='1';
													document.getElementById('rappelptb').disabled=false;
												}
												if (document.getElementById('noremindptb').checked==true) {
													document.getElementById('idmnptb').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('idmnptb').style.opacity='1';
												}
												if (document.getElementById('mnptb').checked==true) {
													document.getElementById('idnoremindptb').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('idnoremindptb').style.opacity='1';
												}
												if (document.getElementById('btncenq')) {document.getElementById('btncenq').style.display='none';}
												if (document.getElementById('sendrappel')) {document.getElementById('sendrappel').style.display='none';}
								
												if ((document.getElementById('mnfix').checked==true || document.getElementById('noremindfix').checked==true) && (document.getElementById('mnptb').checked==true || document.getElementById('noremindptb').checked==true)) {
													document.getElementById('irdv').style.opacity='".$opacityfort."';
													document.getElementById('fonttel').style.textDecoration='line-through';
													document.getElementById('ppby2').disabled=true;
												} else {
													document.getElementById('irdv').style.opacity='1';
													document.getElementById('fonttel').style.textDecoration='';
													document.getElementById('ppby2').disabled=false;
												}
												if (document.getElementById('ppby1').disabled==true && document.getElementById('ppby2').disabled==true && document.getElementById('ppby3').disabled==true) {
													document.getElementById('nosurveypossible').checked=true;
													document.getElementById('divnosurveypossible').style.display='';
													document.getElementById('divwns').style.display='none';
													document.getElementById('part').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('nosurveypossible').checked=false;
													document.getElementById('divnosurveypossible').style.display='none';
													document.getElementById('divwns').style.display='';
													document.getElementById('part').style.opacity='1';
												}
												document.getElementById('btnmaj').style.display='';
												document.getElementById('annuler').style.display='';
												document.getElementById('suivant').style.display='none';
												\"";
												
											$enq_ptb.="<div id=idmnptb  class='l' ";
											if ($fichecdt['attribute_103'][0]=='1') { $enq_ptb.="style='opacity:".$opacityfort.";'";}
											$enq_ptb.="><input type=checkbox id=mnptb ";
											if ($fichecdt['attribute_113'][0]=='1') {$enq_ptb.=" checked ";}
											$enq_ptb.=$ptbonlick.">".H(${$lang}['t-wnum'])."</div>";
											
											$enq_ptb.="<div id=idnoremindptb class='l' ";
											if ($fichecdt['attribute_113'][0]=='1') { $enq_ptb.="style='opacity:".$opacityfort.";'";}
											$enq_ptb.="><input type=checkbox id=noremindptb  ";
											if ($fichecdt['attribute_103'][0]=='1') {$enq_ptb.=" checked ";}
											$enq_ptb.=$ptbonlick.">".H(${$lang}['t-noretel'])."</div>";
										$enq_ptb.="</td></tr>";
									$enq_ptb.="</table>";
								$enq_ptb.="</div>";
								
							}
// Fixe
							if ($forcode) {
								if ($fichecdt['attribute_110'][0]=='1' ||$fichecdt['attribute_115'][0]=='1' ||$fichecdt['attribute_101'][0]=='1' || $fichecdt['attribute_114'][0]=='1') {
									$thisstyle="opacity:".$opacityfort.";";
								} else $thisstyle="";
								$enq_fixe="<div id=info2  class='rub'>";
									$enq_fixe.="<div id=telfix style='".$thisstyle."'>";
										$enq_fixe.="<table class='c'>";
											$enq_fixe.="<tr class='effect'>";
												$enq_fixe.="<td class='titrerubcdt'>".H(${$lang}['t-fix'])."</td>";
												$enq_fixe.="<td class='c m'> : </td>";
												$enq_fixe.="<td class='l'><input type=text size=14 id=numfix value=\"".$fichecdt['attribute_17'][0]."\" ".$simodif." onKeyPress='return isnumspace(event);' ></td>";
												$enq_fixe.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','numfix');></td>";
											$enq_fixe.="</tr>";
											$enq_fixe.="<tr class='usf'><td colspan=4><br></td></tr>";
											$enq_fixe.="<tr class='effect'>";
												$enq_fixe.="<td class='r dcol m'>".H(${$lang}['t-aec'])."</td>";
												$enq_fixe.="<td class='c m'> : </td>";
												$enq_fixe.="<td class='l'>";
													$enq_fixe.="<input type=radio id=encoursfix1 name=encoursfix value=ok ".$simodif2.">".H(${$lang}['ok'])."<br>";
													$enq_fixe.="<input type=radio id=encoursfix2 name=encoursfix value=rep ".$simodif2.">".H(${$lang}['t-rep'])."<br>";
													$enq_fixe.="<input type=radio id=encoursfix3 name=encoursfix value=nb ".$simodif2.">".H(${$lang}['t-nbd']);
												$enq_fixe.="</td>";
												$enq_fixe.="<td class='raz'><input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
												document.getElementById('encoursfix1').checked=false;
												document.getElementById('encoursfix2').checked=false;
												document.getElementById('encoursfix3').checked=false;
														document.getElementById('btnmaj').style.display='';
														document.getElementById('annuler').style.display='';
														document.getElementById('btncenq').style.display='none';
														document.getElementById('suivant').style.display='none';
														document.getElementById('sendrappel').style.display='none';
												\"></td>";
											$enq_fixe.="</tr>";
											$enq_fixe.="<tr class='usf'><td colspan=4><br></td></tr>";
											$enq_fixe.="<tr class='effect'>";
												$enq_fixe.="<td class='r dcol m'>".H(${$lang}['t-fixlog'])."</td>";
												$enq_fixe.="<td class='c m'> : </td>";
												$enq_fixe.="<td class='l'>";
													$enq_fixe.="<input type=radio id=logfix1 name=logfix value=par ".$simodif2;
													if ($fichecdt['attribute_116'][0]=='par') {$enq_fixe.=" checked";}
													$enq_fixe.=">".H(${$lang}['t-parents'])."<br>";
													$enq_fixe.="<input type=radio id=logfix2 name=logfix value=per ".$simodif2;
													if ($fichecdt['attribute_116'][0]=='per') {$enq_fixe.=" checked";}
													$enq_fixe.=">".H(${$lang}['t-perso'])."<br>";
													$enq_fixe.="<input type=radio id=logfix3 name=logfix value=etu ".$simodif2;
													if ($fichecdt['attribute_116'][0]=='etu') {$enq_fixe.=" checked";}
													$enq_fixe.=">".H(${$lang}['t-logeetu'])."<br>";
													$enq_fixe.="<input type=radio id=logfix4 name=logfix value=aut ".$simodif2;
													if ($fichecdt['attribute_116'][0]=='aut') {$enq_fixe.=" checked";}
													$enq_fixe.=">".H(${$lang}['autre']);
												$enq_fixe.="</td>";
												$enq_fixe.="<td class='raz'><input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
												document.getElementById('logfix1').checked=false;
												document.getElementById('logfix2').checked=false;
												document.getElementById('logfix3').checked=false;
												document.getElementById('logfix4').checked=false;
														document.getElementById('btnmaj').style.display='';
														document.getElementById('annuler').style.display='';
														document.getElementById('btncenq').style.display='none';
														document.getElementById('suivant').style.display='none';
														document.getElementById('sendrappel').style.display='none';
												\"></td>";
											$enq_fixe.="</tr>";
											$enq_fixe.="<tr class='usf'><td colspan=4><br></td></tr>";
										$enq_fixe.="</table>";
									$enq_fixe.="</div>";
									$enq_fixe.="<table class='c'>";
										$enq_fixe.="<tr class='effect'><td class='l'>";
											$fixonlick="onclick=\"
												if (document.getElementById('mnfix').checked==true || document.getElementById('noremindfix').checked==true) {
													document.getElementById('telfix').style.opacity='".$opacityfort."';
													document.getElementById('rappelfix').disabled=true;
												} else {
													document.getElementById('telfix').style.opacity='1';
													document.getElementById('rappelfix').disabled=false;
												}
												if (document.getElementById('noremindfix').checked==true) {
													document.getElementById('idmnfix').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('idmnfix').style.opacity='1';
												}
												if (document.getElementById('mnfix').checked==true) {
													document.getElementById('idnoremindfix').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('idnoremindfix').style.opacity='1';
												}
												document.getElementById('btnmaj').style.display='';
												document.getElementById('annuler').style.display='';
												if (document.getElementById('btncenq')) {document.getElementById('btncenq').style.display='none';}
												document.getElementById('suivant').style.display='none';
												if (document.getElementById('sendrappel')) {document.getElementById('sendrappel').style.display='none';}
												if ((document.getElementById('mnfix').checked==true || document.getElementById('noremindfix').checked==true) && (document.getElementById('mnptb').checked==true || document.getElementById('noremindptb').checked==true)) {
													document.getElementById('irdv').style.opacity='".$opacityfort."';
													document.getElementById('fonttel').style.textDecoration='line-through';
													document.getElementById('ppby2').disabled=true;
												} else {
													document.getElementById('irdv').style.opacity='1';
													document.getElementById('fonttel').style.textDecoration='';
													document.getElementById('ppby2').disabled=false;
												}
												if (document.getElementById('ppby1').disabled==true && document.getElementById('ppby2').disabled==true && document.getElementById('ppby3').disabled==true) {
													document.getElementById('nosurveypossible').checked=true;
													document.getElementById('divnosurveypossible').style.display='';
													document.getElementById('divwns').style.display='none';
													document.getElementById('part').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('nosurveypossible').checked=false;
													document.getElementById('divnosurveypossible').style.display='none';
													document.getElementById('divwns').style.display='';
													document.getElementById('part').style.opacity='1';
												}
												\"";
											$enq_fixe.="<div id=idmnfix  class='l' ";
											if ($fichecdt['attribute_101'][0]=='1') { $enq_fixe.="style='opacity:".$opacityfort.";'";}
											$enq_fixe.="><input type=checkbox id=mnfix ";
											if ($fichecdt['attribute_114'][0]=='1') {$enq_fixe.=" checked ";}
											$enq_fixe.=$fixonlick.">".H(${$lang}['t-wnum'])."</div>";
/*
										$enq_fixe.="<br>";
*/
											$enq_fixe.="<div id=idnoremindfix class='al' ";
											if ($fichecdt['attribute_114'][0]=='1') { $enq_fixe.="style='opacity:".$opacityfort.";'";}
											$enq_fixe.="><input type=checkbox id=noremindfix  ";
											if ($fichecdt['attribute_101'][0]=='1') {$enq_fixe.=" checked ";}
											$enq_fixe.=$fixonlick.">".H(${$lang}['t-noretel'])."</div>";
											$enq_fixe.="</td></tr>";
									$enq_fixe.="</table>";
								$enq_fixe.="</div>";
								
							}
// RDV
							if ($forcode) {
								if ((($fichecdt['attribute_101'][0]=='1' || $fichecdt['attribute_114'][0]=='1') && ($fichecdt['attribute_103'][0]=='1' || $fichecdt['attribute_113'][0]=='1'))||($fichecdt['attribute_110'][0]=='1')||($fichecdt['attribute_115'][0]=='1')) {
									$thisstyle="opacity:".$opacityfort.";";
								} else $thisstyle="";
								$enq_rdv="<div id=info3 class='rub'>";
									$enq_rdv.="<div id=irdv  style='".$thisstyle."'>";
										$enq_rdv.="<table class='c'>";
											$enq_rdv.="<tr class='effect'>";
												$enq_rdv.="<td class='l titrerubcdt' colspan=5>".H(${$lang}['t-rdv'])."</td>";
											$enq_rdv.="</tr>";
											$enq_rdv.="<tr class='effect'>";
												$enq_rdv.="<td class='r dcol m' colspan=2>".H(${$lang}['date'])."</td>";
												$enq_rdv.="<td class='c m'> : </td>";
												$enq_rdv.="<td class='l'><input type=text id=rdvdate NAME=rdvdate value=\"".$fichecdt['attribute_104'][0]."\" onmouseover=Calendar.setup({position:['500','100'],inputField:'rdvdate',ifFormat:'%d/%m/%Y',showsTime:false,timeFormat:'24',singleClick:true}); onchange=\"document.getElementById('btnmaj').style.display='';document.getElementById('annuler').style.display='';\"></td>";
												$enq_rdv.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','rdvdate');></td>";
											$enq_rdv.="</tr>";
											$enq_rdv.="<tr class='effect'>";
												$enq_rdv.="<td class='r dcol m' colspan=2>".H(${$lang}['t-rdvH'])."</td>";
												$enq_rdv.="<td class='c m'> : </td>";
												$enq_rdv.="<td class='l'><input type=text id=rdvh NAME=rdvh value=\"".$fichecdt['attribute_105'][0]."\" ".$simodif3." size=2></td>";
												$enq_rdv.="<td></td>";
											$enq_rdv.="</tr>";
											$enq_rdv.="<tr class='effect'>";
												$enq_rdv.="<td colspan=2></td>";
												$enq_rdv.="<td></td>";
												$enq_rdv.="<td class='l'>";$enq_rdv.="<input type=checkbox id=rappelptb ".$simodif2." ";
														if ($fichecdt['attribute_106'][0]=='ptb' || $fichecdt['attribute_106'][0]=='ptbfix') {$enq_rdv.=" checked";}
														if ($fichecdt['attribute_103'][0]=='1') {$enq_rdv.=" disabled";}
														$enq_rdv.=">".H(${$lang}['t-onptb']);
													$enq_rdv.="<br><input type=checkbox id=rappelfix ".$simodif2." ";
														if ($fichecdt['attribute_106'][0]=='fix' || $fichecdt['attribute_106'][0]=='ptbfix') {$enq_rdv.=" checked";}
														if ($fichecdt['attribute_101'][0]=='1') {$enq_rdv.=" disabled";}
														$enq_rdv.=">".H(${$lang}['t-onfix']);
												$enq_rdv.="</td>";
												$enq_rdv.="<td></td>";
											$enq_rdv.="</tr>";
										$enq_rdv.="</table>";
									$enq_rdv.="</div>";
								$enq_rdv.="</div>";
							}
// Courriel
							if ($forcode) {
								if ($fichecdt['attribute_110'][0]=='1' ||$fichecdt['attribute_115'][0]=='1' ||$fichecdt['attribute_109'][0]=='1') {
									$thisstyle="opacity:".$opacityfort.";";
								} else $thisstyle="";
								$enq_mel="<div id=info5  class='rub'>";
									$enq_mel.="<div id=tabmail style='".$thisstyle."'>";
										$enq_mel.="<table class='c'>";
											$enq_mel.="<tr class='effect'>";
												$enq_mel.="<td class='titrerubcdt'>".H(${$lang}['@mel'])."</td>";
												$enq_mel.="<td class='c m'> : </td>";
												$enq_mel.="<td class='l'>";
													$enq_mel.="<input type=text id=email value='".$fichecdt['email'][0]."' size=40 ".$simodif." ";
													$enq_mel.="onkeyup=\"mel_valid();\" onclick=\"mel_valid();\">";
												$enq_mel.="</td>";
												$enq_mel.="<td class='reload'><img src=images/back16.png onclick=\"reload('".$_POST['sid']."','".$tidselect."','email');\"></td>";
											$enq_mel.="</tr>";
											$enq_mel.="<tr>";
												$enq_mel.="<td class='r nf dcol m'>".H(${$lang}['t-synt'])."</td>";
												$enq_mel.="<td class='c m'> : </td>";
												$enq_mel.="<td class='nf l m'><div class='l' id='melisvalid'></div></td>";
												$enq_mel.="<td></td>";
											$enq_mel.="</tr>";
											$enq_mel.="<tr>";
												$enq_mel.="<td class='r nf dcol m'>".H(${$lang}['t-dom'])."</td>";
												$enq_mel.="<td class='c m'> : </td>";
												$enq_mel.="<td class='nf l m'><div class='l' id='domainisvalid'>";
													if ($fichecdt['email'][0]!='') {
														list($ident, $domain) = explode( "@", $fichecdt['email'][0]);
														if (test_domain($fichecdt['email'][0])) {
															$enq_mel.="<img class='m' src=images/ok.png width='15px'>&nbsp;(".$domain.")";
														} else {
															$enq_mel.="<img class='m' src=images/nok.png width='15px'>&nbsp;(".$domain.")";
														}
													} else $enq_mel.="<img class='m' src=images/nok.png width='15px'>";
												$enq_mel.="</div></td>";
												$enq_mel.="<td></td>";
											$enq_mel.="</tr>";
											$enq_mel.="<tr class='usf'><td colspan=4><br></td></tr>";
										$enq_mel.="</table>";
									$enq_mel.="</div>";
									$enq_mel.="<table class='c'>";
										$enq_mel.="<tr class='effect'>";
											$enq_mel.="<td class='l'>";
												$enq_mel.="<input type=checkbox id=noremindmel ";
												if ($fichecdt['attribute_109'][0]=='0' && $fichecdt['emailstatus'][0]=='OptOut' && $fichecdt['attribute_110'][0]=='0') {
													$enq_mel.=" disabled";
												} elseif ($fichecdt['completed'][0]=='want no remind' || ($fichecdt['attribute_109'][0]=='1'&& ($fichecdt['emailstatus'][0]=='OptOut' || $fichecdt['emailstatus'][0]==''))) {
													$enq_mel.=" checked";
													}
												$enq_mel.=" onclick=\"
												if (this.checked==true) {
													document.getElementById('tabmail').style.opacity='".$opacityfort."';
													document.getElementById('fontnet').style.textDecoration='line-through';
													document.getElementById('ppby1').disabled=true;
													document.getElementById('ppby1').checked=false;
												} else {
													document.getElementById('tabmail').style.opacity='1';
													document.getElementById('fontnet').style.textDecoration='';
													document.getElementById('ppby1').disabled=false;
												}
												document.getElementById('btnmaj').style.display='';
												document.getElementById('annuler').style.display='';
												document.getElementById('btncenq').style.display='none';
												document.getElementById('suivant').style.display='none';
												if (document.getElementById('sendrappel')) {document.getElementById('sendrappel').style.display='none';}
												if (document.getElementById('ppby1').disabled==true && document.getElementById('ppby2').disabled==true && document.getElementById('ppby3').disabled==true) {
													document.getElementById('nosurveypossible').checked=true;
													document.getElementById('divnosurveypossible').style.display='';
													document.getElementById('divwns').style.display='none';
													document.getElementById('part').style.opacity='".$opacityfort."';
												} else {
													document.getElementById('nosurveypossible').checked=false;
													document.getElementById('divnosurveypossible').style.display='none';
													document.getElementById('divwns').style.display='';
													document.getElementById('part').style.opacity='1';
												}
												\">";
												if ($fichecdt['attribute_109'][0]=='0' && $fichecdt['emailstatus'][0]=='OptOut' && $fichecdt['attribute_110'][0]=='0') {
													$enq_mel.="<font style='color:red;'>".H(${$lang}['t-clicnoremind'])."</font>";
												} else {
													$enq_mel.=H(${$lang}['t-noremel']);
												}
											$enq_mel.="</td>";
										$enq_mel.="</tr>";
									$enq_mel.="</table>";
								$enq_mel.="</div>";
							}
// Adresse
							if ($forcode) {
								$enq_adr="<div id=info4 class='rub'>";
									if ($fichecdt['attribute_110'][0]=='1' || $fichecdt['attribute_115'][0]=='1' || $fichecdt['attribute_112'][0]=='1') {
										$thisstyle="opacity:".$opacityfort.";";
									} else $thisstyle="";
									$enq_adr.="<div id=adresse style='".$thisstyle."'>";
										$enq_adr.="<table class='c'>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol'>".H(${$lang}['t-compl'])." 1</td>";
												$enq_adr.="<td class='c m'> : </td>";
												$enq_adr.="<td class='l'><input type=text size=30 id=compl1 value=\"".$fichecdt['attribute_107'][0]."\" ".$simodif."></td>";
												$enq_adr.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','compl1');></td>";
											$enq_adr.="</tr>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol'>".H(${$lang}['t-compl'])." 2</td>";
												$enq_adr.="<td class='c m'> : </td>";
												$enq_adr.="<td class='l'><input type=text size=30 id=compl2 value=\"".$fichecdt['attribute_108'][0]."\" ".$simodif."></td>";
												$enq_adr.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','compl2');></td>";
												$enq_adr.="</tr>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol'>".H(${$lang}['t-rue'])."</td>";
												$enq_adr.="<td class='c m'> : </td>";
												$enq_adr.="<td class='l'><input type=text size=30 id=rue value=\"".$fichecdt['attribute_13'][0]."\" ".$simodif."></td>";
												$enq_adr.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','rue');></td>";
												$enq_adr.="</tr>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol'>".H(${$lang}['t-cp'])."</td>";
												$enq_adr.="<td class='c m'> : </td>";
												if ($lang!='fr') {
													$enq_adr.="<td class='l'><input type=text size=5 id=cp value=\"".$fichecdt['attribute_14'][0]."\" ".$simodif." onKeyPress='return isnum(event);'></td>";
												} else $enq_adr.="<td class='l'><input type=text size=5 id=cp value=\"".$fichecdt['attribute_14'][0]."\" ".$simodif." onKeyPress='return isnum(event);' onkeyup=\"if (this.value.length > 4) {code_postal(this.value);".$divinfoopaK."}\"></td>";
												$enq_adr.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','cp');></td>";
												$enq_adr.="</tr>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol'>".H(${$lang}['t-ville'])."</td>";
												$enq_adr.="<td class='c m'> : </td>";
												$enq_adr.="<td class='l'><input type=text size=30 id=ville value=\"".$fichecdt['attribute_15'][0]."\" ".$simodif."></td>";
												$enq_adr.="<td class='reload'><img src=images/back16.png onclick=reload('".$_POST['sid']."','".$tidselect."','ville');></td>";
												$enq_adr.="</tr>";
											$enq_adr.="<tr class='usf'><td colspan=4><br></td></tr>";
											$enq_adr.="<tr class='effect'>";
												$enq_adr.="<td class='r dcol m'>".H(${$lang}['t-typlog'])."</td>";
												$enq_adr.="<td class='c m'> : </td>";
												$enq_adr.="<td class='l'>";
													$enq_adr.="<input type=radio id=typelog1 name=typelog value=parent ".$simodif2."";
													if ($fichecdt['attribute_16'][0]=='parent') {$enq_adr.=" checked";}
													$enq_adr.=">".H(${$lang}['t-parents'])."<br>
													<input type=radio id=typelog2 name=typelog value=perso ".$simodif2."";
													if ($fichecdt['attribute_16'][0]=='perso') {$enq_adr.=" checked";}
													$enq_adr.=">".H(${$lang}['t-perso'])."<br>
													<input type=radio id=typelog3 name=typelog value=etu ".$simodif2."";
													if ($fichecdt['attribute_16'][0]=='etu') {$enq_adr.=" checked";}
													$enq_adr.=">".H(${$lang}['t-logeetu'])."<br>
													".H(${$lang}['ou'])." : <input type=text size=20 id=typelogautre value=\"";
													if ($fichecdt['attribute_16'][0]!='parent' && $fichecdt['attribute_16'][0]!='perso' && $fichecdt['attribute_16'][0]!='etu') {
														$enq_adr.=$fichecdt['attribute_16'][0];
													}
													$enq_adr.="\" ".$simodif4." >";
												$enq_adr.="</td>";
												$enq_adr.="<td class='raz'>";
													$enq_adr.="<input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
													document.getElementById('typelog1').checked=false;
													document.getElementById('typelog2').checked=false;
													document.getElementById('typelog3').checked=false;
													document.getElementById('typelogautre').value='';
													document.getElementById('btnmaj').style.display='';
													document.getElementById('annuler').style.display='';
													document.getElementById('btncenq').style.display='none';
													document.getElementById('divwns').style.display='';
													document.getElementById('suivant').style.display='none';
													document.getElementById('sendrappel').style.display='none';
													\">";
												$enq_adr.="</td>";
											$enq_adr.="</tr>";
											$enq_adr.="<tr class='usf'><td colspan=4><br></td></tr>";
										$enq_adr.="</table>";
								$enq_adr.="</div>";
								$enq_adr.="<table class='c'>";
									$enq_adr.="<tr class='effect'>";
										$enq_adr.="<td class='l'>";
											$enq_adr.="<input type=checkbox id=npai  ";
											if ($fichecdt['attribute_112'][0]=='1') {$enq_adr.=" checked ";}
											$enq_adr.="onclick=\"
											if (this.checked==true) {
												document.getElementById('adresse').style.opacity='".$opacityfort."';
												document.getElementById('fontcou').style.textDecoration='line-through';
												document.getElementById('ppby3').checked=false;
												document.getElementById('ppby3').disabled=true;
											} else {
												document.getElementById('adresse').style.opacity='1';
												document.getElementById('fontcou').style.textDecoration='';
												document.getElementById('ppby3').disabled=false;
											}
											document.getElementById('btnmaj').style.display='';
											document.getElementById('annuler').style.display='';
											document.getElementById('btncenq').style.display='none';
											document.getElementById('suivant').style.display='none';
											if (document.getElementById('sendrappel')) {
												document.getElementById('sendrappel').style.display='none';
												}
											if (document.getElementById('ppby1').disabled==true
											 && document.getElementById('ppby2').disabled==true && document.getElementById('ppby3').disabled==true) {
												document.getElementById('nosurveypossible').checked=true;
												document.getElementById('divnosurveypossible').style.display='';
												document.getElementById('divwns').style.display='none';
												document.getElementById('part').style.opacity='".$opacityfort."';
											} else {
												document.getElementById('nosurveypossible').checked=false;
												document.getElementById('divnosurveypossible').style.display='none';
												document.getElementById('divwns').style.display='';
												document.getElementById('part').style.opacity='1';
											}
											\">".H(${$lang}['t-norelog']);
										$enq_adr.="</td>";
									$enq_adr.="</tr>";
								$enq_adr.="</table>";
							$enq_adr.="</div>";
							
						}
// participation
							if ($forcode) {
								if ($fichecdt['attribute_110'][0]=='1' ||$fichecdt['attribute_115'][0]=='1' ) {
									$thisstyle="opacity:".$opacityfort.";";
								} else $thisstyle="";
								$enq_part="<div id=info6 class='rub' width='1px'>";
									$enq_part.="<div id=part style='".$thisstyle."' width='1px'>";
										$enq_part.="<table class='c'>";
											$enq_part.="<tr class='effect'>";
												$enq_part.="<td class='titrerubcdt'>".H(${$lang}['participation'])."</td>";
												$enq_part.="<td class='c m'> : </td>";
												$enq_part.="<td class='l'>";
													$enq_part.="<input type=radio id=ppby1 name=ppby value=net ".$simodif5." ";
													if ($fichecdt['attribute_111'][0]=='net' && $fichecdt['attribute_109'][0]=='0' && $fichecdt['emailstatus'][0]!='OptOut') {
														$enq_part.=" checked";
													}
													if ($fichecdt['attribute_109'][0]=='1' || $fichecdt['emailstatus'][0]=='OptOut' || $fichecdt['email'][0]=='') {
														$enq_part.=" disabled ><font id=fontnet style='text-decoration: line-through;'>par internet</font><br>";
													} else {
														$enq_part.="><font id=fontnet >".H(${$lang}['t-bynet'])."</font><br>";
													}
													
													$enq_part.="<input type=radio id=ppby2 name=ppby value=tel ".$simodif5." ";
													if ($fichecdt['attribute_111'][0]=='tel' && 
														(($fichecdt['attribute_103'][0]=='0' && $fichecdt['attribute_113'][0]=='0') || ($fichecdt['attribute_101'][0]=='0' && $fichecdt['attribute_114'][0]=='0'))
														) {
														$enq_part.=" checked";
													}
													if (($fichecdt['attribute_103'][0]=='1' || $fichecdt['attribute_113'][0]=='1') && ($fichecdt['attribute_101'][0]=='1' || $fichecdt['attribute_114'][0]=='1')) {
														$enq_part.=" disabled ><font id=fonttel style='text-decoration: line-through;'>par telephone</font><br>";
													} else {
														$enq_part.="><font id=fonttel >".H(${$lang}['t-byphone'])."</font><br>";
													}
													
													$enq_part.="<input type=radio id=ppby3 name=ppby value=cou ".$simodif5." ";
													if ($fichecdt['attribute_111'][0]=='cou' && $fichecdt['attribute_112'][0]=='0') {
														$enq_part.=" checked";
													}
													if ($fichecdt['attribute_112'][0]=='1') {
														$enq_part.=" disabled ><font id=fontcou style='text-decoration: line-through;'>par courrier</font><br>";
													} else {
														$enq_part.="><font id=fontcou >".H(${$lang}['t-bycour'])."</font><br>";
													}
												$enq_part.="</td>";
												$enq_part.="<td class='raz'><input type=button class=raz value='".H(${$lang}['raz'])."' onclick=\"
													document.getElementById('ppby1').checked=false;
													document.getElementById('ppby2').checked=false;
													document.getElementById('ppby3').checked=false;
													document.getElementById('btnmaj').style.display='';
													document.getElementById('annuler').style.display='';
													document.getElementById('btncenq').style.display='none';
													document.getElementById('divwns').style.display='';
													document.getElementById('suivant').style.display='none';
													document.getElementById('sendrappel').style.display='none';
													\">";
												$enq_part.="</td>";
											$enq_part.="</tr>";
										$enq_part.="</table>";
									$enq_part.="</div>";
										$enq_part.="<table class='c'>";
											$enq_part.="<tr class='effect'>";
												$enq_part.="<td class='l'>";
													$enq_part.="<div id=divnosurveypossible style='color:red;text-align:center;background:transparent;font-size:small;";
													if ($fichecdt['attribute_115'][0]=='1' 
														|| (($fichecdt['attribute_103'][0]=='1' || $fichecdt['attribute_113'][0]=='1')
															&& ($fichecdt['attribute_101'][0]=='1' || $fichecdt['attribute_114'][0]=='1')
															&& ($fichecdt['attribute_109'][0]=='1')
															&& ($fichecdt['attribute_112'][0]=='1')
															)
														|| ($fichecdt['emailstatus'][0]=='OptOut' && $fichecdt['attribute_110'][0]=='0' && $fichecdt['attribute_109'][0]=='0')) {
																$enq_part.="display:;";
														} else {
															$enq_part.="display:none;";
														}
													$enq_part.="'>";
													$enq_part.="<input type=checkbox id=nosurveypossible disabled ";
													if ($fichecdt['attribute_115'][0]=='1') {$enq_part.=" checked ";}
														$enq_part.=">".H(${$lang}['t-nosurveypossible']);
													$enq_part.="</div>";
												$enq_part.="</td>";
											$enq_part.="</tr>";
										$enq_part.="</table>";
										
	// div "want no survey"
									if ($forcode) {
										$enq_part.="<table class='c'>";
											$enq_part.="<tr class='effect'>";
												$enq_part.="<td class='l'>";
													$enq_part.="<div id=divwns style='text-align:center;background:transparent;font-size:small;";
													if ($fichecdt['attribute_115'][0]=='1' || ($fichecdt['emailstatus'][0]=='OptOut' && $fichecdt['attribute_110'][0]=='0'&& $fichecdt['attribute_109'][0]=='0')) {
														$enq_part.="display:none;";
													}
														$enq_part.="'>";
														$enq_part.="<input type=checkbox id=wantnosurvey ";
														if ($fichecdt['attribute_110'][0]=='1') {$enq_part.=" checked ";}
														$enq_part.="onclick=\"
														document.getElementById('btnmaj').style.display='';
														document.getElementById('annuler').style.display='';
														if (this.checked==true) {
															".$divinfoopaK."
														} else {
															".$divinfonoopaK."
														}
														document.getElementById('btnmaj').style.display='';
														document.getElementById('annuler').style.display='';
														document.getElementById('btncenq').style.display='none';
														document.getElementById('suivant').style.display='none';
														document.getElementById('sendrappel').style.display='none';
															\"
														>".H(${$lang}['t-wns']);
													$enq_part.="</div>";
												$enq_part.="</td>";
											$enq_part.="</tr>";
										$enq_part.="</table>";
									}
								$enq_part.="</div>";
							}


//
// Tabbleau fiche du candidat
//	

	$enqpeupler.="<div id=candidat >";
		$enqpeupler.="<table class='colsep'  width=100% >";
			$enqpeupler.="<tr>";
				$enqpeupler.="<td id=tdlstcdt style='width:190px;'>";
					$enqpeupler.="<div class='arr6' >";
						$enqpeupler.=$tab_rdv;
						$enqpeupler.=$lst_cdts;
						$enqpeupler.=$by_autre_ope;
					$enqpeupler.="</div>";
				$enqpeupler.="</td><td style='width:5px;'></td>";
//
// Tabbleau candidat
//					
				$enqpeupler.="<td>";
					$enqpeupler.="<div class='bgprogress arr10' >";
						$enqpeupler.="<div id=info_candidat>";
							$enqpeupler.=$ndp_cdt;
							$enqpeupler.="<div id=divinfotoken>";
								$enqpeupler.="<table id=infotoken style='width:100%;border-collapse:collapse;'>";
									$enqpeupler.="<tr >";
// colonne 1
										$enqpeupler.="<td class='t' width='230px'>";
	// appel log maj
											$enqpeupler.="<br>".$ope_fiche;
	// appel des logs operateur
											$enqpeupler.="<br>".$log_maj;
	// appel renseignement suppl
											$enqpeupler.="<br>".$rens_sup;
// colonne 2
										$enqpeupler.="</td><td class='c t'><br>";
	// appel Portable
											$enqpeupler.=$enq_ptb;
	// appel Fixe
											$enqpeupler.=$enq_fixe;
	// appel RDV
											$enqpeupler.=$enq_rdv;
// colonne 3
										$enqpeupler.="</td><td class='c t'><br>";
	// Courriel
											$enqpeupler.=$enq_mel;
	// Adresse
											$enqpeupler.=$enq_adr;
	// participation
											$enqpeupler.=$enq_part;
											
										$enqpeupler.="</td>";
									$enqpeupler.="</tr>";
								$enqpeupler.="</table>";
								
							$enqpeupler.="</div>";
							
//===============================================
// Résumé
//===============================================
							if ($forcode) {
								$enqpeupler.="<div id=divinfotokenresum style='display:none;'>";
									$enqpeupler.="<br><table class='c'><tr><td>";
										$infotokenresum1="<table class='bf dcol border'>";
											if ($fichecdt['attribute_103'][0]=='1') {
												$infotokenresum1.="<tr><td class='border r'>".H(${$lang}['t-ptb'])." :</td><td class='lcol31 border l'>".H(${$lang}['t-noretel'])."</td></tr>";
											} else {
												$infotokenresum1.="<tr><td class='border r'>".H(${$lang}['t-ptb'])." :</td><td class='lcol31 border l'>".$fichecdt['attribute_19'][0]."</td></tr>";
											}
											if ($fichecdt['attribute_101'][0]=='1') {
												$infotokenresum1.="<tr><td class='border r'>".H(${$lang}['t-fix'])." :</td><td class='lcol31 border l'>".H(${$lang}['t-noretel'])."</td></tr>";
											} else {
												$infotokenresum1.="<tr><td class='border r'>".H(${$lang}['t-fix'])." :</td><td class='lcol31 border l'>".$fichecdt['attribute_17'][0]."</td></tr>";
											}
											$infotokenresum1.="</table>";
										$enqpeupler.=$infotokenresum1;
										$enqpeupler.="</td><td>";
										$infotokenresum2="<table class='bf dcol border'>";
										$infotokenresum2.="<tr><td class='border r'>".H(${$lang}['t-typlog'])." : </td><td class='lcol31 border l'>";
										if ($fichecdt['attribute_16'][0]=='parent') {$infotokenresum2.="parent";}
										elseif ($fichecdt['attribute_16'][0]=='perso') {$infotokenresum2.="perso";}
										elseif ($fichecdt['attribute_16'][0]=='etu') {$infotokenresum2.="logement etu";}
										else {$infotokenresum2.=$fichecdt['attribute_16'][0];}
										$infotokenresum2.="</td></tr>";
										$infotokenresum2.="<tr><td class='border r'>".H(${$lang}['t-adresse'])." : </td><td class='lcol31 border l'>";
										if ($fichecdt['attribute_107'][0]!='') {$infotokenresum2.=$fichecdt['attribute_107'][0]."<br>";}
										if ($fichecdt['attribute_108'][0]!='') {$infotokenresum2.=$fichecdt['attribute_108'][0]."<br>";}
										$infotokenresum2.=$fichecdt['attribute_13'][0]."<br>";
										$infotokenresum2.=$fichecdt['attribute_14'][0]." ";
										$infotokenresum2.=$fichecdt['attribute_15'][0];
										$infotokenresum2.="</td></td></tr>";
										$infotokenresum2.="</table>";
										$enqpeupler.=$infotokenresum2;
										$enqpeupler.="</td><td>";
										$infotokenresum3="<table class='bf dcol border'>";
											$infotokenresum3.="<tr><td class='border r'>".H(${$lang}['@mel'])." : </td><td class='lcol31 border l'>".$fichecdt['email'][0]."</td></tr>";
											$infotokenresum3.="<tr><td class='border r'>".H(${$lang}['participation'])." : </td><td class='lcol31 border l'><b>";
											if ($fichecdt['attribute_111'][0]=='net') {$infotokenresum3.=H(${$lang}['t-bynet']);}
											if ($fichecdt['attribute_111'][0]=='tel') {$infotokenresum3.=H(${$lang}['t-byphone']);}
											if ($fichecdt['attribute_111'][0]=='cou') {$infotokenresum3.=H(${$lang}['t-bycour']);}
											$infotokenresum3.="</b></td></tr>";
											$infotokenresum3.="</table>";
										$enqpeupler.=$infotokenresum3;
										$enqpeupler.="</td></tr></table>";
									$enqpeupler.="</div>";
							}	

//===============================================
// div caché code postal
//===============================================
							if ($forcode) {
								$enq_cp="<div id='lstville' class='popup'></div>";
								$enq_cp.="<div id='lsthour' class='popup'>";
									$enq_cp.="<table width=100%>";
										$enq_cp.="<tr><td>";
											$enq_cp.="<table width=100%>";
											$enq_cp.="<tr class='' style='background:#eee;' onclick=\"
												".$divinfonoopaK."
												document.getElementById('lsthour').style.display='';\"><td></td><td></td><td></td><td></td><td><b>X</b></td></tr>";
											for($h='18'; $h<'22'; $h++) {
												$enq_cp.="<tr><td>&nbsp;</td><td class='effect r' onclick=\"".$divinfonoopaK."document.getElementById('lsthour').style.display='none';document.getElementById('rdvh').value='".$h."h';\">".$h."h</td>";
												$enq_cp.="<td>&nbsp;</td><td class='effect r' onclick=\"".$divinfonoopaK."document.getElementById('lsthour').style.display='none';document.getElementById('rdvh').value='".$h."h30';\">".$h."h30</td><td>&nbsp;</td></tr>";
												}
											$enq_cp.="</table>";
										$enq_cp.="</td></tr>";
									$enq_cp.="</table>";
								$enq_cp.="</div>";
								$enqpeupler.=$enq_cp;
							}
				
//===============================================
// iframe limesurvey 
//===============================================
							$enqpeupler.="<iframe id='lms' width=100% style=\"background:#ddd;border:0px;display:none;\"></iframe><br>";

//===============================================
// Action bouton
//===============================================
							if ($forcode) {
								$enqpeupler.="<div>";
									$enqpeupler.="<table class='r'>";
										$enqpeupler.="<tr>";
											$enqpeupler.="<td></td>";
											$enqpeupler.="<td width='100px'>";
			// Annuler
												$enqpeupler.="<input type=button class='alert' value='<< ".H(${$lang}['annuler'])."' id=annuler style='display:none;' onclick=\"tel('".$_POST['sid']."','".$tidselect."','','".$imgencours."');\">";
											$enqpeupler.="</td>";
											$enqpeupler.="<td width='100px'>";
			// MAJ
												$enqpeupler.="<input type=button class='alert' value='".H(${$lang}['maj'])." >>' id=btnmaj style='display:none;' onclick=\"maj('".$_POST['sid']."','".$tidselect."','".$imgencours."');\">";
											$enqpeupler.="</td>";
											$enqpeupler.="<td width='140px'>";
			// retour fiche
												$enqpeupler.="<input type=button class='alert' id='btnRetourFiche' style='display:none;' value='".H(${$lang}['t-quitLS'])."' onclick=\"
												if (confirm('".H8(${$lang}['t-messquitLS'])."')) {
													tel('".$_POST['sid']."','".$tidselect."','','".$imgencours."');
													document.getElementById('tdlstcdt').style.display='none';
												}\">";
											$enqpeupler.="</td>";
										$enqpeupler.="</tr>";
										$enqpeupler.="<tr>";
											$enqpeupler.="<td></td>";
											$enqpeupler.="<td width='260px'>";
											if ($fichecdt['attribute_110'][0]=='0' && $fichecdt['attribute_115'][0]=='0' && $fichecdt['emailstatus'][0]=='OK') {
												if ($fichecdt['email'][0]!='' && $fichecdt['attribute_109'][0]=='0'&& $fichecdt['token'][0]!='' && $fichecdt['completed'][0]=='N') {
													if ($fichecdt['sent'][0]!='N') {
			// envoi rappel
														$enqpeupler.="<input type=button class=action id=sendrappel value='".H(${$lang}['sendrappel'])." >>' onclick=\"rappelmel('".$_POST['sid']."','".$tidselect."','rappel','".$imgencours."');\">";
													} else {
			// envoi invit
														$enqpeupler.="<input type=button class=action id=sendrappel value='".H(${$lang}['sendinvit'])." >>' onclick=\"rappelmel('".$_POST['sid']."','".$tidselect."','envoi','".$imgencours."');\">";
													}
												} else {
														$enqpeupler.="<input type=button class=action id=sendrappel value='' style='display:none;'>";
													}
											} else {
													$enqpeupler.="<input type=button class=action id=sendrappel value='' style='display:none;'>";
												}
											$enqpeupler.="</td>";
											$enqpeupler.="<td width='195px'>";
			// faire l'enquete
												$linkDoEnquete=$rooturlrep;
												$linkDoEnquete=str_replace ("SID",(int)$_POST['sid'],$linkDoEnquete);
												$linkDoEnquete=str_replace ("TOKEN",$fichecdt['token'][0],$linkDoEnquete);
												$enqpeupler.="<input type=button class='action' value='".H(${$lang}['doLS'])." >>' id=btncenq onclick=\"
													envoieRequete('".$linkDoEnquete."');
													document.getElementById('btnRetourFiche').style.display='';
													document.getElementById('suivant').style.display='none';
													document.getElementById('btncenq').style.display='none';
													document.getElementById('sendrappel').style.display='none';
													document.getElementById('tdlstcdt').style.display='none';
													\"";
												if ($fichecdt['attribute_110'][0]=='0' && $fichecdt['attribute_115'][0]=='0' && ($fichecdt['emailstatus'][0]=='OK' || $fichecdt['emailstatus'][0]=='' ||  $fichecdt['usesleft'][0]=='1')) {
													$enqpeupler.=">";
												} else $enqpeupler.="style='display:none;'>";
											$enqpeupler.="</td>";
											$enqpeupler.="<td width='70px'>";
			// suivant
												$enqpeupler.="<input type=button class='action' id='suivant' value='".H(${$lang}['suivant'])." (n&deg;".$tidselectsuivant.") >>' onclick=\"tel('".$_POST['sid']."','".$tidselectsuivant."','suivant','".$imgsuivant."');\">";
											
											$enqpeupler.="</td>";
										$enqpeupler.="</tr>";
									$enqpeupler.="</table>";
								$enqpeupler.="</div>";
							}
						
						$enqpeupler.="</div>";

//===============================================
// div caché stat
//===============================================
							$enqpeupler.="<div onMouseOut=\"document.getElementById('popup').style.display='none';\" id='popup' class='stat2'></div>";

//===============================================
// div caché legende
//===============================================
							if ($forcode) {
								$legende="";
								$legende.="<div onMouseOut=\"document.getElementById('legende').style.display='none';\" id='legende' class='stat2'>";
									$legende.="<table style='width:100%'>";
										$legende.="<tr><td>";
											$legende.="<table class='r' style='width:100%'>";
												$legende.="<tr class=opaque><td width='20%'></td><td width='20%'></td><td width='20%'></td><td width='20%'></td><td width='20%'></td></tr>";
												
												$legende.="<tr><td class='l' style='color:orange;'>".H(${$lang}['nom'])." ".H(${$lang}['prenom'])."</td><td class='l' colspan=4 style='color:orange;'>=> ".H(${$lang}['erreur'])."</td></tr>";
												$legende.="<tr class=opaque><td class=opaque>&nbsp;</td><td class='l' style='border: 1px solid orange;' colspan=4>";
														$legende.="<table class='l' style='width:100%;'>";
															$legende.="<tr><td class='l'><img src=images/eta-erreur.png>&nbsp;".H(${$lang}['leg-orange'])."</td></tr>";
														$legende.="</table>";
												$legende.="</td></tr>";
												
												$legende.="<tr><td class='l'>".H(${$lang}['nom'])." ".H(${$lang}['prenom'])."</td><td class='l' colspan=4>=> ".H(${$lang}['leg-wait'])."</td></tr>";
												$legende.="<tr><td>&nbsp;</td><td class='l' style='border: 1px solid #aaa;' colspan=4>";
														$legende.="<table class='l' style='width:100%'>";
															$legende.="<tr><td class='l'><img src=images/eta-appel.png>&nbsp;".H(${$lang}['leg-black1'])."</td></tr>";
															$legende.="<tr><td class='l'><img src=images/eta-lm-cour.png>&nbsp;".H(${$lang}['leg-black2'])."</td></tr>";
															$legende.="<tr><td class='l'><img src=images/eta-lm-tel.png>&nbsp;".H(${$lang}['leg-black3'])."</td></tr>";
															$legende.="<tr><td class='l'><img src=images/eta-lm-mel.png width=25px>&nbsp;".H(${$lang}['leg-black4'])."</td></tr>";
														$legende.="</table>";
												$legende.="</td></tr>";
												
												$legende.="<tr><td class='l' style='color:red;'>".H(${$lang}['nom'])." ".H(${$lang}['prenom'])."</td><td class='l' colspan=4 style='color:red;'>=> ".H(${$lang}['leg-refus'])."</td></tr>";
												$legende.="<tr><td>&nbsp;</td><td class='l' style='border: 1px solid red;' colspan=4>";
														$legende.="<table class='l' style='width:100%'>";
															$legende.="<tr><td class='l'><img src=images/eta-refus.png>&nbsp;".H(${$lang}['leg-red1'])."</td></tr>";
															$legende.="<tr><td class='l'><img src=images/eta-refusmel.png>&nbsp;".H(${$lang}['leg-red2'])."</td></tr>";
															$legende.="<tr><td class='l'><img src=images/eta-refusimp.png>&nbsp;".H(${$lang}['leg-red3'])."</td></tr>";
														$legende.="</table>";
												$legende.="</td></tr>";
												
												$legende.="<tr><td class='l' style='color:green;'>".H(${$lang}['nom'])." ".H(${$lang}['prenom'])."</td><td class='l' colspan=4 style='color:green;'>=> ".H(${$lang}['leg-enqterm'])."</td></tr>";
												$legende.="<tr><td>&nbsp;</td><td class='l' style='border: 1px solid green;' colspan=4>";
														$legende.="<table class='l' style='width:100%'>";
															$legende.="<tr><td class='l'><img src=images/eta-fin.png>&nbsp;".H(${$lang}['leg-green'])."</td></tr>";
														$legende.="</table>";
												$legende.="</td></tr>";
												
												$legende.="<tr><td class='l' style='color:#aaa;'>".H(${$lang}['autre'])."</td><td class='l' colspan=4></td></tr>";
												$legende.="<tr><td>&nbsp;</td><td class='l' colspan=4>";
														$legende.="<table class='l' style='width:100%'>";
															$legende.="<tr><td class='l' style='background-color:#afa;'>".H(${$lang}['leg-autre1'])."</td></tr>";
															$legende.="<tr><td class='l' style='background-color:#faa;'>".H(${$lang}['leg-autre2'])."</td></tr>";
														$legende.="</table>";
												$legende.="</td></tr>";
												
											$legende.="</table>";
									$legende.="</td></tr>";
								$legende.="</table>";
							$legende.="</div>";
							$enqpeupler.=$legende;
						}
	


				} else {
					$enqpeupler.=H(${$lang}['t-nocdt']);
				}
				
				$enqpeupler.="</div>";
				$enqpeupler.="</td></tr>";
			$enqpeupler.="</table>";
		$enqpeupler.="</div>";
		$reponse=json_encode(array( 'title' => $title, 'coeur' => utf8_encode($enqpeupler), 'nbrdv' => utf8_encode($nbrdv)));
		echo $reponse;
	//}
}






if ((isset($_POST['menu']) && isset($_POST['maj']) && $_POST['menu']=='tel' && isset($_POST['sid']) && isset($_POST['tid']))) {
		//'&tel1='+tel1+'&tel2='+tel2+'&noremind='+noremind+'&rdvdate='+rdvdate+'&rdvh='+rdvh);
		
		$what="";
		$requete = "SELECT * FROM {$dbprefix}tokens_".$_POST['sid']." WHERE `tid` ='".$_POST['tid']."';";  
		$resultat = extraire ($lmsconnect, $requete);
		if ($resultat) {
			$tokens=intab($resultat);
		}
	
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_19` = '".$_POST['numptb']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_113` = '".$_POST['mnptb']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_103` = '".$_POST['noremindptb']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);

		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_17` = '".$_POST['numfix']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_114` = '".$_POST['mnfix']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_101` = '".$_POST['noremindfix']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_116` = '".$_POST['logfix']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		if ($_POST['encoursptb']=='ok' || $_POST['encoursfix']=='ok') {
			$tokens['attribute_51'][0]++;
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_51` = '".$tokens['attribute_51'][0]."' WHERE `tid` ='".$_POST['tid']."';";
			if ($_POST['encoursptb']=='ok') {$what.="appel reussi sur ptb";}
			if ($_POST['encoursfix']=='ok') {$what.="appel reussi sur fixe";}
		} elseif ($_POST['encoursptb']=='rep' || $_POST['encoursfix']=='rep') {
			$tokens['attribute_51'][0]++;
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_51` = '".$tokens['attribute_51'][0]."' WHERE `tid` ='".$_POST['tid']."';";
			if ($_POST['encoursptb']=='rep') {$what.="message sur r&eacute;pondeur ptb";}
			if ($_POST['encoursfix']=='rep') {$what.="message sur r&eacute;pondeur fixe";}
		} elseif ($_POST['encoursptb']=='nb' || $_POST['encoursfix']=='nb') {
			$tokens['attribute_52'][0]++;
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_52` = '".$tokens['attribute_52'][0]."' WHERE `tid` ='".$_POST['tid']."';";
			if ($_POST['encoursptb']=='nb') {$what.="appel echec (personne sur ptb)";}
			if ($_POST['encoursfix']=='nb') {$what.="appel echec (personne sur fixe)";}
		}
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_104` = '".$_POST['rdvdate']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_105` = '".$_POST['rdvh']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		if ($_POST['rappelptb'] && $_POST['rappelfix']) {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_106` = 'ptbfix' WHERE `tid` ='".$_POST['tid']."';";
		} elseif ($_POST['rappelptb']) {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_106` = 'ptb' WHERE `tid` ='".$_POST['tid']."';";
		} elseif ($_POST['rappelfix']) {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_106` = 'fix' WHERE `tid` ='".$_POST['tid']."';";
		} else $requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_106` = '' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `email` = '".$_POST['email']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_111` = '' WHERE `attribute_109` = '1' and `attribute_111` = 'net' and `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_107` = '".utf8_decode(addslashes($_POST['compl1']))."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_108` = '".utf8_decode(addslashes($_POST['compl2']))."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_13` = '".utf8_decode(addslashes($_POST['rue']))."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_14` = '".$_POST['cp']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_15` = '".utf8_decode(addslashes($_POST['ville']))."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_16` = '".utf8_decode(addslashes($_POST['typelog']))."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_112` = '".$_POST['npai']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_111` = '".$_POST['ppby']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_110` = '".$_POST['wantnosurvey']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_115` = '".$_POST['nosurveypossible']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		if ($_POST['wantnosurvey']=='0' && $tokens['emailstatus']=='OK' && $tokens['attribute_109']=='0' && $tokens['email']!='' ) {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `emailstatus` = 'OK',`usesleft` = '1' WHERE `tid` ='".$_POST['tid']."';";
			$resultat = extraire ($lmsconnect, $requete);
		}
		//
		// mettre à OptOut / ok dans{$dbprefix}}tokens_ pour 109 :
		//
		// fonctionnement Limesurvey :
		// emailstatus : OK / OptOut / vide
		// usesleft (Utilisations restantes) : 0 ou 1
		//	OK => on peut envoyer mel et repondre enq
		//	OptOut => on ne peut pas envoyer mel
		//		si usesleft = 1, on peut répondre ds LS via engrenage
		//		si usesleft = 0, on ne peut pas répondre ds LS
	
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_109` = '".$_POST['noremindmel']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		if ($_POST['wantnosurvey']=='1') {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `emailstatus` = 'OptOut',`usesleft` = '0' WHERE `tid` ='".$_POST['tid']."';";
			$resultat = extraire ($lmsconnect, $requete);
		} elseif ($_POST['noremindmel']=='1') {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `emailstatus` = '',`usesleft` = '1' WHERE `tid` ='".$_POST['tid']."';";
			$resultat = extraire ($lmsconnect, $requete);
		} else {
			$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `emailstatus` = 'OK',`usesleft` = '1' WHERE `tid` ='".$_POST['tid']."';";
			$resultat = extraire ($lmsconnect, $requete);
		}
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_201` = '".$_POST['viafiche']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_202` = '".$_POST['viars']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_203` = '".$_POST['viabao']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_204` = '".$_POST['viacomp']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_210` = '".$_POST['completedby']."' WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		$requete="UPDATE {$dbprefix}tokens_".$_POST['sid']." SET `attribute_53`='".date('Y-m-d H:i:s:').substr(strrchr(microtime(true), "."), 1)."'  WHERE `tid` ='".$_POST['tid']."';";
		$resultat = extraire ($lmsconnect, $requete);
		
		if ($what=="") { $what="autre(s) maj sur fiche...";	}
		$requete="INSERT INTO `".$_POST['sid']."_history` (`forid` ,`what` ,`bywho` ,`date`) VALUES (
			'".$_POST['tid']."',
			'".$what."',
			'".$uid."',
			'".date('Y-m-d H:i:s')."');";
		$resultat = extraire ($telconnect, $requete);
		
		$maj="1";
		unset($_POST);
		$reponse=json_encode(array(  'coeur' => utf8_encode($maj)));
		echo $reponse;
	}


//aff_tab($_POST);


?>
