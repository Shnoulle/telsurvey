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


function ratio($ratio){
	if ($ratio=='0') { $value='0';}
	if ($ratio>='1') { $value='10';}
	if ($ratio>='20') { $value='30';}
	if ($ratio>='40') { $value='50';}
	if ($ratio>='60') { $value='70';}
	if ($ratio>='80') { $value='90';}
	if ($ratio=='100') { $value='100';}
    return $value;
}

function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

function vconnect($databaselocation, $databaseuser, $databasepass, $databasename) {
$connect = mysql_connect($databaselocation, $databaseuser, $databasepass);
if (! $connect) {
	echo "Connexion impossible au serveur Mysql à $hote";
	} else {
	$ok = mysql_select_db($databasename, $connect);
	if (!$ok) {
		echo "Connexion impossible à la base $bd";
		} else {
			return $connect;
			}
	}
}

function extraire($connexion ,$requete) {
	/* requete de sélection */
	$resultat =  mysql_query($requete,  $connexion);
	if ($resultat)
	  return $resultat;
	else {
	  //echo "Pas de r&eacute;ponse pour la requ&ecirc;te \"$requete\" ";
	  //print_r($resultat);
	}
}

function intab($resultat) {
	//print_r($resultat);
	$nblignes=mysql_num_rows($resultat);
	$nbchamps=mysql_num_fields($resultat);
	for($z=0; $z < $nbchamps; $z++) {
		$tabchamps[$z] = mysql_field_name($resultat,$z);
	}
	if ($tabchamps[0]) {
		$intab=array();
		for($z=0; $z < $nblignes; $z++) {
		  $ligne = mysql_fetch_row($resultat);
		  for($y=0; $y < $nbchamps; $y++) {
			$intab[$tabchamps[$y]][]=$ligne[$y];
		  }
		}
		if ($intab) {return $intab;}
	}	
}

function aff_tab ($table) {
			echo '<pre>';
			print_r($table);
			echo '</pre>';
}

function array_sort_func($a,$b=NULL) {
	static $keys;
	if($b===NULL) return $keys=$a;
	foreach($keys as $k) {
		if(@$k[0]=='!') {
			$k=substr($k,1);
			if(@$a[$k]!==@$b[$k]) {
				return strcmp(@$b[$k],@$a[$k]);
				}
			} else if(@$a[$k]!==@$b[$k]) {
				return strcmp(@$a[$k],@$b[$k]);
				}
		}
	return 0;
}
			
function array_sort(&$array) {
	if(!$array) return $keys;
	$keys=func_get_args();
	array_shift($keys);
	array_sort_func($keys);
	usort($array,"array_sort_func");       
} 

function convertLatin1ToHtml($str1) {
    $html_entities = array (
        "&" =>  "&amp;",     #ampersand  
        "á" =>  "&aacute;",     #latin small letter a
        "Â" =>  "&Acirc;",     #latin capital letter A
        "â" =>  "&acirc;",     #latin small letter a
        "Æ" =>  "&AElig;",     #latin capital letter AE
        "æ" =>  "&aelig;",     #latin small letter ae
        "À" =>  "&Agrave;",     #latin capital letter A
        "à" =>  "&agrave;",     #latin small letter a
        "Å" =>  "&Aring;",     #latin capital letter A
        "å" =>  "&aring;",     #latin small letter a
        "Ã" =>  "&Atilde;",     #latin capital letter A
        "ã" =>  "&atilde;",     #latin small letter a
        "Ä" =>  "&Auml;",     #latin capital letter A
        "ä" =>  "&auml;",     #latin small letter a
        "â" =>  "&acirc;",
        "Ç" =>  "&Ccedil;",     #latin capital letter C
        "ç" =>  "&ccedil;",     #latin small letter c
        "É" =>  "&Eacute;",     #latin capital letter E
        "é" =>  "&eacute;",     #latin small letter e
        "è" =>  "&egrave;",     #latin small letter e
        "Ê" =>  "&Ecirc;",     #latin capital letter E
        "ê" =>  "&ecirc;",     #latin small letter e
        "È" =>  "&Egrave;",     #latin capital letter E
        "î" =>  "&icirc;",
        "ô" =>  "&ocirc;",
        "û" =>  "&ucirc;",     #latin small letter u
        "Ù" =>  "&Ugrave;",     #latin capital letter U
        "ù" =>  "&ugrave;",     #latin small letter u
        "Ü" =>  "&Uuml;",     #latin capital letter U
        "ü" =>  "&uuml;",     #latin small letter u
        "Ý" =>  "&Yacute;",     #latin capital letter Y
        "ý" =>  "&yacute;",     #latin small letter y
        "ÿ" =>  "&yuml;",     #latin small letter y
        "Ÿ" =>  "&Yuml;",     #latin capital letter Y
        "«" =>  "&laquo;",
        "»" =>  "&raquo;",
        "’" =>  "&#146;",
        "’" =>  "&#146;",
        "'" =>	"&#146;"
    );

    foreach ($html_entities as $key => $value) {
        $str1 = str_replace($key, $value, $str1);
    }
    return $str1;
}

function test_domain($mel) {
	list($ident, $domain) = split( "@", $mel, 2);
	exec( "nslookup -type=MX ".$domain , $resultd);
	foreach ($resultd as $line) {
		if(eregi( "^".$domain,$line)) {
			return true;
			break;
		}
	}
	return false;
}

function H($str1) {
	return nl2br(htmlentities(utf8_decode($str1)));
}
function H8($str1) {
	return utf8_decode($str1);
}
function HTML($str1) {
	return htmlentities(utf8_decode(stripslashes($str1)));
}


?>
