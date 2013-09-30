<?php

if(session_name()!=$sSessionName)
{
	session_name ($sSessionName);
	session_start ();
}

// Need crsf control here */

	if(!isset($_SESSION['uid']))
	{
		$errorstring="";
		$username=(isset($_POST['username']))?filter_var(trim($_POST['username']),FILTER_SANITIZE_STRING,!FILTER_FLAG_NO_ENCODE_QUOTES):"";
		$password=(isset($_POST['password']))?filter_var(trim($_POST['password']),FILTER_SANITIZE_STRING,!FILTER_FLAG_NO_ENCODE_QUOTES):"";
		if($username && $password){
			$telconnect = vconnect($databaselocation, $telbaseuser, $telbasepass, $telbasename);
			$requete ="SELECT * FROM users WHERE username='{$username}';";
			$resultat = extraire ($telconnect, $requete);
			$resultat = intab ($resultat);
			echo "<pre>".var_dump($resultat['password'][0])."</pre>";
				echo "<pre>".var_dump(hash("sha256" ,$password))."</pre>";
				echo "<pre>".var_dump(hash("sha256" ,$password)==$resultat['password'][0])."</pre>";
			if(isset($resultat['password'][0]) && $resultat['password'][0])
			{
				if(hash("sha256" ,$password)==$resultat['password'][0])
				{
					$_SESSION['uid']=$resultat['username'][0];
				}else{
					$errorstring=H(${$lang}['cnx-invalid']);
				}
			}else{
				$errorstring=H(${$lang}['cnx-invalid']);
			}
		}
		if(!isset($_SESSION['uid']))
		{
			$top="<!DOCTYPE html>";
			$top.="<head> <meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>
				<title>TelSurvey</title>
				<link rel='icon' type='image/png' href='images/favicon2.png'>\n
				<link href='css/ls.css' rel='stylesheet' type='text/css'>\n
				<link href='css/ls_".$skin.".css' rel='stylesheet' type='text/css'>\n";
			$top.="<div id='tout'>\n";
			if($errorstring){$top.="<p class='error'>{$errorstring}</p>";}
			$top.="<form name='loginform' id='loginform' method='post' action='index.php' >"
				. "<ul class='form'>"
				. "<li><label for='username'>".H(${$lang}['cnx-username'])."</label><input type='text' name='username' id='username' value='{$username}'></li>\n"
				. "<li><label for='password'>".H(${$lang}['cnx-password'])."</label><input type='password' name='password' id='password' value=''></li>\n"
				. "</ul>"
				. "<p class='button'><input type='submit' class='btn' value='".H(${$lang}['cnx-login'])."'></p>";
		
			$top.="</div>\n";
	

			$top.="<div class='dvpby'>\n";
			$top.="TelSurvey - <a href=http://telsurvey.univ-lemans.fr>http://telsurvey.univ-lemans.fr</a> - DSI Universite du Maine";
			$top.="</div>";
			$top.="</body></html>";
			echo $top;
			die();
		}else{
			$uid=$_SESSION['uid'];
		}
	}else{
		$uid=$_SESSION['uid'];
	}
//die("<pre>".var_export($_SESSION)."</pre>");
