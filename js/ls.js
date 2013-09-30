

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

function mel_valid() {
	var mel=document.getElementById('email').value;
	var filter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if (!filter.test(mel)) {
		document.getElementById('melisvalid').innerHTML = "<font style=color:red;>error</font>";
		document.getElementById('domainisvalid').innerHTML = "<font style=color:red;>error</font>";
		document.getElementById('ppby1').disabled=true;
		document.getElementById('fontnet').style.textDecoration='line-through';
		document.getElementById('ppby1').checked=false;
	} else {
		document.getElementById('melisvalid').innerHTML = "<img class='m' src=images/ok.png width='15px'>";
		var xhr = getXhr();
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				reponse = eval('(' + xhr.responseText + ')');
				document.getElementById('domainisvalid').innerHTML = reponse.domain;
				if (reponse.isOK=='1' && document.getElementById('noremindmel').checked==false){
					document.getElementById('ppby1').disabled=false;
					document.getElementById('fontnet').style.textDecoration='';
				} else {
					if (reponse.isOK=='0' && document.getElementById('noremindmel').checked==false){
						document.getElementById('ppby1').disabled=true;
						document.getElementById('fontnet').style.textDecoration='line-through';
					}
				}
			}
		}
		//alert(i);
		xhr.open("POST","ajax.php",true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xhr.send("test_domain="+mel);
	}
	
}

function heure() {
	auj = new Date();
	h = auj.getHours();
	m = auj.getMinutes();
	s = auj.getSeconds();
		if(h<10) { h = '0'+h; }
		if(m<10) { m = '0'+m; }
		if(s<10) { s = '0'+s; }
	date = ''+h+'h'+m;
	if(document.getElementById("heure_dyna")) {
		document.getElementById("heure_dyna").innerHTML = date;
	}
	
}

function envoieRequete(url,id) {
	document.getElementById('divinfotoken').style.display='none';
	document.getElementById('divinfotokenresum').style.display='';
	document.getElementById('lms').style.display = 'inline';
	document.getElementById('lms').style.border= '1px solid #ddd';
	document.getElementById('lms').style.height=(window.innerHeight-document.getElementById('titretoken').offsetHeight)-240+'px';
	document.getElementById('lms').src = url;
}

function testforopa(){
	command='if (document.getElementById(\'tout\').style.opacity==\'0.3\' && document.getElementById(\'tabmenu\').style.display==\'none\') {document.getElementById(\'tout\').style.opacity=\'1\';}';
	
 setTimeout(command, 60);
}

function opa_progress(action,id) {
	document.getElementById('titretoken').style.display='none';
	document.getElementById('tabmenu').style.display='none';
	if (document.getElementById('heure_dyna')) {document.getElementById('heure_dyna').style.display='none';}
	
	var opa=document.getElementById('tout').style.opacity;
	var offset=30;
	var t=0;
	do {
		opa=opa-0.1;
		t=t+offset;
		command='document.getElementById(\'tout\').style.opacity='+opa+';';
		setTimeout(command, t);
	} while (opa > 0.1);

	command='goldppn(\''+action+'\',\''+id+'\');';
	setTimeout(command, t);

	do {
		opa=opa+0.1;
		t=t+offset;
		command='document.getElementById(\'tout\').style.opacity='+opa+';';
		setTimeout(command, t);
	} while (opa < 0.9);
}

function div_progress(id,divlisting,sens) {
	
	var divlist = divlisting.toString();
	var divlst = divlist.split('/');
	var offset=40;
	var t=1;
	htdispo=0;
	for(i=0; i < divlst.length-1; i++){
		if (document.getElementById(divlst[i]).style.display!='none') {
			var ht=document.getElementById(divlst[i]).offsetHeight;
			var htdispo=document.getElementById(divlst[i]).offsetHeight;
			var opa=1;
			var offsetht=Math.round(ht/5);
			do {
				opa=opa-0.2;
				command='document.getElementById(\''+divlst[i]+'\').style.opacity='+opa+';';
				setTimeout(command, t);
				ht=ht-offsetht;
				command='document.getElementById(\''+divlst[i]+'\').style.height=\''+ht+'px\';';
				setTimeout(command, t);
				t=t+offset;
			} while (opa > 0);
			command='document.getElementById(\''+divlst[i]+'\').style.display=\'none\';';
			setTimeout(command, t);
			command='document.getElementById(\'imgon'+i+'\').style.display=\'\';';
			setTimeout(command, t);
			command='document.getElementById(\'imgoff'+i+'\').style.display=\'none\';';
			setTimeout(command, t);
				t=t+offset;
		}
		//document.getElementById('tr'+i).style.border='';
	}
	if (sens=='open') {
		var htreste = window.innerHeight-document.getElementById('publi').offsetHeight-document.getElementById('title').offsetHeight-80;
		if (htreste==0) {
			if (htdispo==0) {
				htreste=300;
			} else {
				htreste=htdispo;
			}
		}
		htreste=300;
		var opa=0;
		var offsetht=Math.round(htreste/5);
		var ht=0;
		command='document.getElementById(\'pp'+id+'\').style.height=\'0px\';';
		setTimeout(command, t);
		command='document.getElementById(\'imgon'+id+'\').style.display=\'none\';';
		setTimeout(command, t);
		command='document.getElementById(\'imgoff'+id+'\').style.display=\'\';';
		setTimeout(command, t);
		command='document.getElementById(\'pp'+id+'\').style.display=\'\';';
		setTimeout(command, t);
		command='document.getElementById(\'pp'+id+'\').style.opacity=\'0\';';
		setTimeout(command, t);
		do {
			opa=opa+0.2;
			command='document.getElementById(\'pp'+id+'\').style.opacity='+opa+';';
			setTimeout(command, t);
			ht=ht+offsetht;
			command='document.getElementById(\'pp'+id+'\').style.height=\''+ht+'px\';';
			setTimeout(command, t);
			t=t+offset;
		} while (opa < 1);
		command='document.getElementById(\'pp'+id+'\').style.opacity=\'1\';';
		setTimeout(command, t);
		t=t+offset;
	}
}

function isnumspace(event) {
	// empeche la saisi accent ou chiffre
	// Compatibilité IE / Firefox
	//alert(imhere);
	if(!event&&window.event) {
	event=window.event;
	}
	//alert(event.type);
	// IE
	if((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 32 && event.keyCode != 8 && event.keyCode != 9 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode!= 39) {
	event.returnValue = false;
	event.cancelBubble = true;
	}
	// DOM
	if((event.which < 48 || event.which > 57) && event.which != 32 && event.which != 8 && event.keyCode != 9 && event.keyCode != 46 && event.which != 46 && event.keyCode != 37 && event.keyCode!= 39) {
	event.preventDefault();
	event.stopPropagation();
	
	}
	//alert(event.which+" "+event.keyCode+" "+event.keypress+" "+event.charCode);
}

function isnum(event) {
	// empeche la saisi accent ou chiffre
	// Compatibilité IE / Firefox
	//alert(imhere);
	if(!event&&window.event) {
	event=window.event;
	}
	//alert(event.type);
	// IE
	if((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8 && event.keyCode != 9 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode!= 39) {
	event.returnValue = false;
	event.cancelBubble = true;
	}
	// DOM
	if((event.which < 48 || event.which > 57) && event.which != 8 && event.keyCode != 9 && event.keyCode != 46 && event.which != 46 && event.keyCode != 37 && event.keyCode!= 39) {
	event.preventDefault();
	event.stopPropagation();
	
	}
	//alert(event.which+" "+event.keyCode+" "+event.keypress+" "+event.charCode);
}

function getXhr(){
	var xhr = null; 
	if(window.XMLHttpRequest) // Firefox et autres
	   xhr = new XMLHttpRequest(); 
	else if(window.ActiveXObject){ // Internet Explorer 
	   try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
	}
	else { // XMLHttpRequest non supporté par le navigateur 
	   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
	   xhr = false; 
	} 
	return xhr;
}

function goldppn(type,t){
	//var here=window.location;//alert(window.parent.document.getElementById('CAS').location);
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
      		if (reponse.title) {
	      		document.getElementById('title').innerHTML = reponse.title;
			}
			if (reponse.coeur) {
	      		document.getElementById('coeur').innerHTML = reponse.coeur;
			}
			if (reponse.divurl) {
	      		document.getElementById('divurl').innerHTML = reponse.divurl;
			}
			
	
		//document.getElementById(t).innerHTML = xhr.responseText; // => place dans le select de etapeS
		}
	}
	//alert(i);
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.send("menu="+type);
}

function goldvisuetat(sid){
	var xhr = getXhr();
	//alert(t);
	// On défini ce qu'on va faire quand on aura la réponse
	xhr.onreadystatechange = function(){
		// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
		if(xhr.readyState == 4 && xhr.status == 200){
			leselect = xhr.responseText;
			document.getElementById('popup').innerHTML = leselect; // => place dans le div etapeF
			}
		}
	// Ici on va voir comment faire du post
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.send("sid="+sid+"&menu=enqetat");  // => envoi dans le div etapeF, les choix form

}

function enq(type,sid,confirm){
	//alert(etape);
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.title) {
	      		document.getElementById('title').innerHTML = reponse.title;
			}
			if (reponse.coeur) {
				if (reponse.coeur=='ok') {
					opa_progress('enqlister','coeur');
				} else {
					document.getElementById('coeur').innerHTML = reponse.coeur;
				}
			}
		}
	}
	//alert(i);
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	if (sid!='' && (confirm=='ok' || confirm=='ok###his')) {
		value='menu='+type+'&sid='+sid+'&confirm='+confirm;
		xhr.send(value);
		} else {
		value='menu='+type+'&sid='+sid;
		xhr.send(value);
		}
}

function tel(sid,tid,yyy,img){
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.title) {
	      		document.getElementById('title').innerHTML = reponse.title;
			}
			if (reponse.titretoken) {
	      		document.getElementById('titretoken').style.display = '';
	      		document.getElementById('titretoken').innerHTML = reponse.titretoken;
	      	}
			if (reponse.coeur) {
	      		document.getElementById('coeur').innerHTML = reponse.coeur;
	      		//~ document.getElementById('divinfotoken').style.height=(window.innerHeight-document.getElementById('titretoken').offsetHeight)-180+'px';
			}
			if (document.getElementById('heure_dyna')) {window.setInterval('heure()',1000);}
			mel_valid();
			if (reponse.nbrdv<10) {
				document.getElementById('lstrdv').style.height=((reponse.nbrdv*16)+10)+'px';
				document.getElementById('tablstrdv').style.height=(reponse.nbrdv*16)+'px';
				document.getElementById('lstcandeta2').style.height=(window.innerHeight-(reponse.nbrdv*16)-180)+'px';
				document.getElementById('tablstcandeta2').style.height=(window.innerHeight-(reponse.nbrdv*16)-190)+'px';
			} else {
				document.getElementById('lstrdv').style.height='220px';
				document.getElementById('tablstrdv').style.height='200px';
				document.getElementById('lstcandeta2').style.height=(window.innerHeight-450)+'px';
				document.getElementById('tablstcandeta2').style.height=(window.innerHeight-400)+'px';
			}
		}
	}
	//alert(i);
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	if (yyy=='suivant') {
	xhr.send('menu=tel&sid='+sid+'&suivant='+tid+'&img='+img);
	} else if (tid) {
		xhr.send('menu=tel&sid='+sid+'&tid='+tid+'&img='+img);
	} else xhr.send('menu=tel&sid='+sid+'&img='+img);
			
}

function code_postal(cp){
	//alert(cp);

	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.lstville) {
				document.getElementById('lstville').style.display = 'block';
	      		document.getElementById('lstville').innerHTML = reponse.lstville;
			}
		}
	}
	//alert(i);
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	xhr.send('rchcp='+cp);
}

function rappelmel(sid,tid,what,img){
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.reprappelmel) {
				//~ onrecharge
				tel(sid,tid,'',img);
				alert(reponse.reprappelmel);
				
			}
		}
	}
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	xhr.send('rappelmel=now&tid='+tid+'&sid='+sid+'&what='+what);
}

function reload(sid,tid,champ) {
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.champ) {
				document.getElementById(champ).value = reponse.champ;
				if (champ=='email') {
					mel_valid();
				}
			}
		}
	}
	//alert(i);
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	xhr.send('reload='+champ+'&sid='+sid+'&tid='+tid);
}

function maj(sid,tid,img){
	//alert(etape);
	var xhr = getXhr();
	xhr.onreadystatechange = function(){
		if(xhr.readyState == 4 && xhr.status == 200){
			reponse = eval('(' + xhr.responseText + ')');
			if (reponse.coeur && reponse.coeur=="1") {
	      		tel(sid,tid,'',img);
	      		//~ alert("bonjour"+tid+" "+sid);
			}
		}
	}
	email=document.getElementById('email').value;
	value='menu=tel&maj=1&sid='+sid+'&tid='+tid;
	
	value+='&numptb='+document.getElementById('numptb').value;
	if (document.getElementById('encoursptb1').checked) { value+='&encoursptb='+document.getElementById('encoursptb1').value; } else  
	if (document.getElementById('encoursptb2').checked) { value+='&encoursptb='+document.getElementById('encoursptb2').value; } else
	if (document.getElementById('encoursptb3').checked) { value+='&encoursptb='+document.getElementById('encoursptb3').value; } else
		{ value+='&encoursptb='; }
		//~ { value+='&encoursptb=\'\''; }
	if (document.getElementById('mnptb').checked) {value+='&mnptb=1'; } else {value+='&mnptb=0'; }
	if (document.getElementById('noremindptb').checked) {value+='&noremindptb=1'; } else {value+='&noremindptb=0'; }
	
	value+='&numfix='+document.getElementById('numfix').value;
	if (document.getElementById('encoursfix1').checked) { value+='&encoursfix='+document.getElementById('encoursfix1').value; } else 
	if (document.getElementById('encoursfix2').checked) { value+='&encoursfix='+document.getElementById('encoursfix2').value; } else 
	if (document.getElementById('encoursfix3').checked) { value+='&encoursfix='+document.getElementById('encoursfix3').value; } else 
		{ value+='&encoursfix='; }
		//~ { value+='&encoursfix=\'\''; }
	if (document.getElementById('logfix1').checked) { value+='&logfix='+document.getElementById('logfix1').value; } else 
	if (document.getElementById('logfix2').checked) { value+='&logfix='+document.getElementById('logfix2').value; } else 
	if (document.getElementById('logfix3').checked) { value+='&logfix='+document.getElementById('logfix3').value; } else 
	if (document.getElementById('logfix4').checked) { value+='&logfix='+document.getElementById('logfix4').value; } else 
		{ value+='&logfix='; }
		//~ { value+='&logfix=\'\''; }
	if (document.getElementById('mnfix').checked) {value+='&mnfix=1'; } else {value+='&mnfix=0'; }
	if (document.getElementById('noremindfix').checked) { value+='&noremindfix=1'; } else {value+='&noremindfix=0'; }
		
	value+='&rdvdate='+document.getElementById('rdvdate').value;
	value+='&rdvh='+document.getElementById('rdvh').value;
	if (document.getElementById('rappelptb').checked) {	value+='&rappelptb=1'; } else { value+='&rappelptb=0'; }
	if (document.getElementById('rappelfix').checked) {	value+='&rappelfix=1'; } else { value+='&rappelfix=0'; }
	
	if (document.getElementById('noremindmel').checked) { value+='&email='+email+'&noremindmel=1'; } else { value+='&email='+email+'&noremindmel=0'; }
	
	value+='&compl1='+encodeURIComponent(document.getElementById('compl1').value);
	value+='&compl2='+encodeURIComponent(document.getElementById('compl2').value);
	value+='&rue='+encodeURIComponent(document.getElementById('rue').value);
	value+='&cp='+document.getElementById('cp').value;
	value+='&ville='+encodeURIComponent(document.getElementById('ville').value);
	if (document.getElementById('typelog1').checked) { value+='&typelog='+document.getElementById('typelog1').value; } else
	if (document.getElementById('typelog2').checked) { value+='&typelog='+document.getElementById('typelog2').value; } else 
	if (document.getElementById('typelog3').checked) { value+='&typelog='+document.getElementById('typelog3').value; } else 
	if (document.getElementById('typelogautre').value!='') { value+='&typelog='+encodeURIComponent(document.getElementById('typelogautre').value); } else
		{ value+='&typelog='; }
		//~ { value+='&typelog=\'\''; }
	if (document.getElementById('npai').checked) { value+='&npai=1'; } else { value+='&npai=0'; }
	
	if (document.getElementById('ppby1').checked) { value+='&ppby='+document.getElementById('ppby1').value; } else
	if (document.getElementById('ppby2').checked) { value+='&ppby='+document.getElementById('ppby2').value; } else
	if (document.getElementById('ppby3').checked) { value+='&ppby='+document.getElementById('ppby3').value; } else 
		{ value+='&ppby='; }
	if (document.getElementById('nosurveypossible').checked) { value+='&nosurveypossible=1'; } else { value+='&nosurveypossible=0'; }
	if (document.getElementById('wantnosurvey').checked) { value+='&wantnosurvey=1'; } else { value+='&wantnosurvey=0'; }
	
	
	if (document.getElementById('viafiche').checked) { value+='&viafiche=1'; } else { value+='&viafiche=0'; }
	if (document.getElementById('viars').checked) { value+='&viars=1'; } else { value+='&viars=0'; }
	if (document.getElementById('viabao').checked) { value+='&viabao=1'; } else { value+='&viabao=0'; }
	if (document.getElementById('viacomp').checked) { value+='&viacomp=1'; } else { value+='&viacomp=0'; }
	
	
	if (document.getElementById('completedby1').checked) { value+='&completedby='+document.getElementById('completedby1').value; } else
	if (document.getElementById('completedby2').checked) { value+='&completedby='+document.getElementById('completedby2').value; } else
		{ value+='&completedby='; }
	xhr.open("POST","ajax.php",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
	if (tid && sid) {
		xhr.send(value);
	}
	
}

function publipost(val) {
		var xhr = getXhr();
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				reponse = eval('(' + xhr.responseText + ')');
				if (reponse.coeur) {
					document.getElementById('publi').innerHTML =reponse.coeur;
				}
			}
		}
		xhr.open("POST","ajax.php",true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=iso-8859-1');
		value='publipost='+val;
		xhr.send(value);
}
