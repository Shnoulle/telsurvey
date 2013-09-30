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

//
//


$menu="";
$menu.="<table><tr><td class='l m' style='width:300px;'>";
$menu.="<div style='cursor:pointer;'  class='menu' onmouseover=\"document.getElementById('tabmenu').style.display='inline';document.getElementById('tout').style.opacity='0.3';\"
 onmouseout=\"document.getElementById('tabmenu').style.display='none';testforopa();\">";
$menu.="TelSurvey&nbsp;<img src=images/m.png width=10px><br>";
	$menu.="<div  id=tabmenu  style='display:none;'>";
	$menu.="<div class=menuscd>";
		$menu.="<table >";
			$menu.="<tr><td></td><td class=menu >&nbsp;</td></tr>";
			$size='20px';
			$menu.="<tr class=menu onclick=\"opa_progress('enqlister','coeur');\" onmouseover=\"document.getElementById('img1').style.display='';\" onmouseout=\"document.getElementById('img1').style.display='none';\">
			<td class=menu width='".$size."' style='vertical-align:middle;text-align:center;'><img id=img1 src=images/lmr-biblio-f2.png style='display:none;'></td><td class=menu>".H(${$lang}['lister'])."</td><td class=menu width='".$size."'></td></tr>";
			if (in_array($uid,$admin)) {
				$menu.="<tr class=menu onclick=\"opa_progress('enqadd','coeur');\" onmouseover=\"document.getElementById('img2').style.display='';\" onmouseout=\"document.getElementById('img2').style.display='none';\">
				<td class=menu width='".$size."' style='vertical-align:middle;text-align:center;'><img id=img2 src=images/lmr-biblio-f2.png style='display:none;'></td><td class=menu>".H(${$lang}['ajouter'])."</td><td class=menu width='".$size."'></td></tr>";
				$menu.="<tr class=menu onclick=\"opa_progress('enqsuppr','coeur');\" onmouseover=\"document.getElementById('img4').style.display='';\" onmouseout=\"document.getElementById('img4').style.display='none';\">
				<td class=menu width='".$size."' style='vertical-align:middle;text-align:center;'><img id=img4 src=images/lmr-biblio-f2.png style='display:none;'></td><td class=menu>".H(${$lang}['suppr'])."</td><td class=menu width='".$size."'></td></tr>";
				}
				//$menu.="<tr><td class='vide'>&nbsp;&nbsp;&nbsp;</td></tr>";
			$menu.="<tr class=menu onclick=\"opa_progress('publipost','coeur');\" onmouseover=\"document.getElementById('img5').style.display='';\" onmouseout=\"document.getElementById('img5').style.display='none';\">
			<td class=menu width='".$size."' style='vertical-align:middle;text-align:center;'><img id=img5 src=images/lmr-biblio-f2.png style='display:none;'></td><td class=menu>".H(${$lang}['publipostage'])."</td><td class=menu width='".$size."'></td></tr>";
			$menu.="<tr><td></td><td  style='cursor:default;'>&nbsp;</td></tr>";
			$menu.="<tr class='c'><td></td><td class='vsf c m'  style='color:#555;'>V. 2012-11-11 <a href=http://www.gnu.org/licenses/gpl.txt target='_blank'><img src=images/gplv3.png width='30px'></a></td></tr>";
			
			$menu.="<tr><td><td  style='cursor:default;'>&nbsp;</td></tr>";
		$menu.="</table>";
	$menu.="</div>";
	$menu.="</div>";
	$menu.="</div>";
$menu.="</td><td><div class='gf l vbf  dcol m' id='titretoken'></div>";
$menu.="</td></tr>";
$menu.="</table>";
?>
