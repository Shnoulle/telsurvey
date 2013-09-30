<?php


	$top="<html>";
	$top.="<head><link href='css/ls.css' rel='stylesheet' type='text/css'></head><body>";
	$bgarray=array("nbg","lbg","dbg","nbg31","lbg31");
	$colarray=array("ncol","lcol","dcol","ncol31","lcol31");
	$fontarray=array("Arial", "Helvetica","sans-serif", "Lucida", "Tahoma","Trebuchet MS", "Verdana");
	$fontsizearray=array("9px","10px", "11px", "12px", "13px", "14px");
	$top.="<table width='100%'>";
	
	for($i=0; $i<count($bgarray); $i++) {
		$top.="<tr>";
		for($j=0; $j<count($colarray); $j++) {
			$top.="<td class='".$bgarray[$i]." ".$colarray[$j]."' style='border:1px solid white;'>".$bgarray[$i]." / ".$colarray[$j]."</td>";
		}
		$top.="</tr>";
		$top.="<tr>";
		for($j=0; $j<count($colarray); $j++) {
			$top.="<td class='gf ".$bgarray[$i]." ".$colarray[$j]."' style='border:1px solid white;'>".$bgarray[$i]." / ".$colarray[$j]."</td>";
		}
		$top.="</tr>";
	}
	for($i=0; $i<count($colarray); $i++) {
		$top.="<tr>";
		for($j=0; $j<count($bgarray); $j++) {
			$top.="<td class='".$bgarray[$i]." ".$colarray[$j]."' style='border:1px solid black;'>".$bgarray[$i]." / ".$colarray[$j]."</td>";
		}
		$top.="</tr>";
		$top.="<tr>";
		for($j=0; $j<count($bgarray); $j++) {
			$top.="<td class='gf ".$bgarray[$i]." ".$colarray[$j]."' style='border:1px solid black;'>".$bgarray[$i]." / ".$colarray[$j]."</td>";
		}
		$top.="</tr>";
	}
	$top.="</table><br><br>";

// font

	$top.="<table width='100%'>";
	for($i=0; $i<count($fontarray); $i++) {
		$top.="<tr>";
		for($j=0; $j<count($fontsizearray); $j++) {
			$top.="<td style='text-align:left;font:".$fontsizearray[$j]." ".$fontarray[$i].";'>AZERTYGS azertygs ".$fontsizearray[$j]." ".$fontarray[$i]."</td>";
		}
		$top.="</tr>";
	}
	for($i=0; $i<count($fontarray); $i++) {
		$top.="<tr>";
		for($j=0; $j<count($fontsizearray); $j++) {
			$top.="<td style='text-align:left;font:".$fontsizearray[$j]." ".$fontarray[$i].";'><b>AZERTYGS azertygs ".$fontsizearray[$j]." ".$fontarray[$i]."</b></td>";
		}
		$top.="</tr>";
	}
	$top.="</table>";

	$top.="</body></html>";
	echo $top;
	//phpinfo();

?>
