<?php
// Start the session
//@ob_start();
//$_SESSION['lastslide']=0;
include ("pripojeni.php");
if(empty($_SESSION["loguser"])){
	header("Location: index.php?Message=1");
}
if(isset($_POST['Logout'])){
	session_destroy();
	header("Location: index.php");
	@session_stop();
	die();
}

include ("zprava_songu.php");
include ("zprava_playlistu.php");
include ("zprava_medii.php");
include ("administrace.php");
//********Pouzite nazvy SESSION
//$_SESSION['conn'] - parametry pro spojeni s databazi
//$_SESSION['note'] - pomocna
//$_SESSION['filtr'] - filtrovany dotaz pro vypis
//$_SESSION['report']
//$_SESSION['pagelimit']
//$_SESSION['namepage']

//pro filty
  $songname="";
  $kapela="";
  $min=" skladby.Delka>=0 and ";
  $max=" skladby.Delka<=1000 and ";
  $user="";
if(!empty($_GET['sidenumber']))$_SESSION['sidenumber']=$_GET['sidenumber'];
  //Count_record("skladby",NULL,3);
//******************************************SKladby******************************************
 //****************************************Insert new DATA**********************************/ 
//$_SESSION['limitmax'];

if(isset($_POST['Add_song']) | isset($_POST['Update']) | isset($_POST['Delete_song'])){
	Vytvorit();//Proces vytvoreni Skladby
}
//*****************************************************************************************
//Vytvoreni filtru pro zobrazeni
if(isset($_POST['Vyhledat'])){
	$_SESSION['sidenumber']=0;
	$_SESSION['limitmin']=0;
	/********************************************************************/
	if(!empty($_POST['findsong_name']))
		$songname="skladby.Nazev like '%".$_POST['findsong_name']."%' and ";
	/********************************************************************/
	if(!empty($_POST['findkapela']) && $_POST['findkapela']!=0) 
		$kapela="skladby.Kapela=".$_POST['findkapela']." and ";
	/********************************************************************/
	if(!empty($_POST['user_select_filtr']))
		$user="user.ID=".$_POST['user_select_filtr']." and ";
	/********************************************************************/
	if(!empty($_POST['delka_max']) | !empty($_POST['delka_min'])){
		if(!empty($_POST['delka_min']) && empty($_POST['delka_max']))
			$min="skladby.Delka >=".minuteSecond($_POST['delka_min'])." and ";
		else if(empty($_POST['delka_min']) && !empty($_POST['delka_max']))
			$max="skladby.Delka<=".minuteSecond($_POST['delka_max'])." and ";
		else
		{
			$max="skladby.Delka<=".minuteSecond($_POST['delka_max'])." and ";
			$min="skladby.Delka>=".minuteSecond($_POST['delka_min'])." and ";
		}
	}
		$_SESSION['filtr']=$songname.
                      $kapela.
                      $user.
                      $max.
                      $min;
}
//*****************************************************************************************
//Zrusi filtr zobrazi se vsechny pisne v databazi
if(isset($_POST['zrusit_filtr'])){
	unset($_SESSION['filtr']);unset($_SESSION['ascsong']);
	$songname="";
	$kapela="";
	$min=" skladby.Delka>=0 and ";
	$max=" skladby.Delka<=1000 and ";
	$user="";
}
//*****************************************************************************************
//ulozi pocet zaznamu na stranku
if(isset($_POST['zobraz'])){
	$_SESSION['lastnumberzobraz']=$_POST["pocet"];
}
else
{
	if(empty($_SESSION['lastnumberzobraz']))$_SESSION['lastnumberzobraz']=10;
	if(empty($_SESSION['sidenumber']))$_SESSION['sidenumber']=1;
}

if(!empty($_GET['getsongdetail']))
			$_SESSION['last_selecet_song']=$_GET['getsongdetail'];//Ulozeni ID posledni pisnicky
//******************************************Playlisty**************************************
//*****************************************************************************************
//Vytvoreni playlistu
if(isset($_POST['new_playlist'])){
	if(!empty($_POST['name_playlist'])){
		Vytvorit_playlist();//Proces vytvoreni Playlistu
	}
	else{
		$_SESSION['report']="Nebylo zadáno jméno playlistu!";
	}
}
//*****************************************************************************************
////Pridani pisne do playlistu
if(isset($_POST['Insert_to_playlist'])){
	add_to_playlist($_SESSION["logID"],$_GET['getsongdetail'],$_POST['playlist_select']);
}
//*****************************************************************************************
//Smazani skladby z playlistu $_GET["IDPlaylist"]
if(isset($_GET['Dsong'])){
	 $sql = 'DELETE FROM playlist'  
         . ' where ID_playlist='.$_GET["playlist"].' and ID_song='.$_GET['Dsong'].' ';
     mysqli_query($_SESSION['conn'],$sql);         
}
///***************************************************************************************
//Editace nazvu playlistu
if(isset($_POST['Editnameplaylist'])){
	 $sql = 'UPDATE playlist_name '
	 		.'SET Name="'.$_POST["new_name_playlist"].'"'  
            .' where ID='.$_GET['edit_playlist_ID'].'';
     mysqli_query($_SESSION['conn'],$sql);    
}
//*****************************************************************************************
//Smazani playlistu
if(isset($_GET['del'])){
	$sql = 'DELETE FROM playlist_name'
         . ' where ID='.$_GET['del'].' ';                 
  mysqli_query($_SESSION['conn'],$sql);
  
  $sql = 'DELETE FROM playlist'
         . ' where ID_playlist='.$_GET['del'].' ';                 
  mysqli_query($_SESSION['conn'],$sql); 
}
//******************************************MEDIA***************************************
//****************************************************************************************
//Vytvoreni media
if(isset($_POST['new_medium'])){
	if(!empty($_POST['name_medium'])){
		Vytvorit_medium();//Proces vytvoreni media
	}
	else{
		$_SESSION['report']="Nebyl zadán název media!";
	}
}
///***************************************************************************************
////Pridani playlistu do media
//function add_to_media($IDuser,$IDplaylist,$IDmedia)
if(isset($_POST['Insert_to_media'])){
	add_to_media($_SESSION["logID"],$_GET['edit_playlist_ID'],$_POST['media_select']);
	//add_to_playlist($IDuser,$IDsong,$IDplaylist)
}
///***************************************************************************************
//Smazani playlistu z media
if(isset($_POST['delplmedia'])){
    $sql = 'DELETE FROM media'
         . ' where ID_media='.$_GET['media'].' and 
         		   ID_uzivatele='.$_SESSION["logID"].' and 
         		   ID_playlist='.$_GET['IDplinmedia'].'';                 
     if(!mysqli_query($_SESSION['conn'],$sql)) $_SESSION['report']=mysqli_error($_SESSION['conn']);
}
///***************************************************************************************
//Editace nazvu media
if(isset($_POST['Editnamedia'])){
	 $sql = 'UPDATE media_name '
	 		.'SET Name="'.$_POST["new_name_media"].'"'  
            .' where ID='.$_GET['edit_media_ID'].'';
     if(!mysqli_query($_SESSION['conn'],$sql)) $_SESSION['report']="Zadaný název již existuje";
}
///***************************************************************************************
//Smazání media
if(isset($_POST['delmedia_button'])){
	 $sql = 'DELETE FROM media '
             .' where ID_media='.$_GET['delmedia'].' and 
         		   ID_uzivatele='.$_SESSION["logID"].'';    		   
    mysqli_query($_SESSION['conn'],$sql); 
}

//**************************************************************************************
//******************************************Uzivatele**************************************
//update($_GET['getuser']);
if(isset($_POST['new_pas']) && !empty($_POST['new_pas1']) && !empty($_POST['new_pas2'])){
	if($_POST['new_pas1']==$_POST['new_pas2']){
		$sql = 'UPDATE user'.' SET Heslo="'.$_POST["new_pas1"].'" 
				Where ID="'.$_SESSION["logID"].'"';
		if(mysqli_query($_SESSION['conn'],$sql)){
			$_SESSION["report"]="Heslo bylo ulozeno";
			$_SESSION["heslo"]=$_POST['new_pas1'];
		}
		else 		
			$_SESSION["report"]=mysqli_error($_SESSION['conn']);		
	}
	else
	{
		$_SESSION["report"]="Hesla se neshoduji";
	}	
}
//administrace vstupni paramater ID uzivatele
if(isset($_POST['E_jmeno'])){
	edit_uzivatele($_GET['I1']);
}
?>
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html; charset=utf-8_czech_ci">
<meta charset="UTF-8">

<script type="text/javascript" src="jquery-1.10.2.js"></script>
 <style>
	body{/*background:url('1383304937115.jpg');*/
  		 background:url('tapeta.jpg');
		 background-repeat:no-repeat;
		 background-size: cover;
		margin:0 auto;
    --barva: #157919;/*Green*/
    --barva2: #3e8e41;/*Green hover*/
    user-select: none;
	}
	/******************Menu a hlavicka**************************/
    #user{position:relative;
    	  top:6px;
    	  right: 0px;
    	 }

    #user p{color:white;
    		font-family: arial;
    		font-size:110%;
    		width: 100px;
    	  	position:relative;
    	  	top:-56px;
    	  	right:10px;
    	    text-align: left;
    	}
    #user img{position:relative;/*#004D73;*/
    		  right:120px;
    		  cursor: -webkit-pointer; 
    	      cursor: pointer;
    	  	  background-color: #004D73;
    	  	  border-radius: 500px;
    	  	}	
    #Menu{background-color: var(--barva);
    	 width:100%;
    	 height:70px;
    	

    	}	      
    #Menu h3{margin-left: 20px;
    		 cursor: -webkit-pointer; 
    	     cursor: pointer;
    	      position:relative;
    	      top:5px;
    		}		      
    #Menu h3:hover{
    				background-color: var(--barva2); 
    				padding: 0px 0px 1px;
    				border-radius: 4px;
    			}		
    #logout{/*Ramecek user logout*/
    		display:none;
    		background: grey;
    		color:black;
    		width: 250px;
    		height: 160px;
    		position:absolute;
    		right:2px;
    		top:75px;
    		z-index: 1;
    		border: 1px solid white;
    		border-radius: 10px;
    		}
    #logout img{/*Img user logout*/
    			margin:10px;
    			background-color: #004D73;
    			padding: 5px 5px 5px;
    			border-radius: 4px;
    		}
    #logout button{width:250px;
    				position: relative;top:-11px;
    				background: green;
    				background-color: var(--barva); /* Green */
					border: none;
					color: white;
					padding: 15px 32px;
					text-align: center;
					text-decoration: none;
					display: inline-block;
					font-size: 16px;
					border-radius: 0px 0px 10px 10px;
					cursor: -webkit-pointer; 
    	    		cursor: pointer;
    		}
    #logout button:hover{
    	background-color: var(--barva2);
    }	
    #info{margin:0px;}			
    #info h4{background-color: #4CAF50;
    		 position: relative;top:-2px;
    		 display: inline-block;
    		 /*width:96px;*/
    		 margin:0px 0px 0px 0px;
    		 padding: 3px 3px;
    		 color: black;
    		 cursor: -webkit-pointer; 
    	     cursor: pointer;
    }
    #info h4:hover{
    	background-color: var(--barva2);
    }
    /******************Obsah stranky**************************/	
    		
    #container{background-color:grey;
    		   opacity:0.9;
    		   border-radius: 10px;
    		   width: -webkit-fit-content;
  			   width: -moz-fit-content;
  			   width: fit-content;
  			   border: 2px white solid;
  			   position: relative;
  			   top:20px; 
    }
    #reportstyl h3{color:black;
    			   font-size:110%;}

    #song1{display:none;}/*Skladby vypis*/
    #song2{display:none;}/*Skladby insert*/
    #song3{display:none;}/*Skladby filtr*/
    #song4{display:none;}/*Skladby detail*/
    #song8{display:none;}/*Playlist vypis*/
    #song9{display:none;}/*Playlist insert*/
    #song10{display:none;}/*Playlist edit*/
    #song11{display:none;}/*Media vypis*/
    #song12{display:none;}/*Media insert*/
    #song13{display:none;}/*Media edit*/
    #song20{display:none;}/*Administrace*/
    #song25{display:none;}/*user edit*/

    #song1 table{margin: 20px;}/*Skladby vypis*/
    #song2 table{margin: 20px;}/*Skladby insert*/
    #song3 table{margin: 20px;}/*Skladby filtr*/
    #song4 table{margin: 20px;}/*Skladby detail*/
    #song8 table{margin: 20px 80px 20px 80px;}/*Playlist vypis*/
    #song9 table{margin: 20px;}/*Playlist insert*/
    #song10 table{margin: 20px;}/*Playlist edit*/
    #song11 table{margin: 20px 60px 20px 60px;}/*Media vypis*/
    #song12 table{margin: 20px;}/*Media insert*/
    #song13 table{margin: 20px 60px 20px 60px;}/*Media edit*/
    #song20 table{margin: 20px}/*Administrace*/
    #song25 table{margin: 20px}/*user edit*/
    #song25 button{width:265px;/*user edit button*/
    				position: relative;top:-7px;
    				background: green;
    				background-color: var(--barva); /* Green */
					border: none;
					color: white;
					padding: 15px 15px;
					text-align: center;
					text-decoration: none;
					display: inline-block;
					font-size: 16px;
					cursor: -webkit-pointer; 
    	    		cursor: pointer;
    	    		opacity: 1.0;
    		}
    #song25 button:hover{/*user edit*/
    	background-color: var(--barva2);
    }	
    #song4 form{/*Skladby detail*/
    			float:right;
    			display: inline-block;
    			margin: 0px 0px;
    			padding: 30px 0px 0px 0px;
    			position: relative;top:-10px;
    			background-color:grey;
    			border-radius: 10px;
    			}
    #video{/*Skladby detail*/
    	   display: inline-block;
    	   padding:41px 20px 20px 20px;}	

    #box_media_name{display:none;}/*Pro editaci nazvu media*/
    #box_pl_name{display:none;}/*Pro editaci nazvu playlistu*/
    

    #modul{background: white;}
    
	.selected_side{background: blue;
				   color:white;
				   margin:1px;
				   border-radius: 2px;
				}
	.unselected_side{background: white;
					 color:black;}

    #item1{cursor: -webkit-pointer; /*Polozky menu v hlavičce*/
    	   cursor: pointer;color:white;
    	   position:relative;
    	   z-index: 1;}
    .order {text-decoration:none;
    		color:black;}
    .user-UI-form{display:block}

    #extend2{display: none}/*Rozsirujici okno v edit*/
    #extend{display: none};/*Rozsirujici okno v insertu*/

	table{background: red;
	      border: 1px solid black;
	   	  position: absolute;
	   	  width:auto;
	   		}
	   
	td{border: 1px solid black;
	   	  word-wrap: break-word;}
	th{background: white;
	   	  border: 1px solid black;}	

	button{
    	   position: relative;top:0px;
    	   background: green;
    	   background-color: var(--barva); /* Green */
		   border: none;
	       color: white;
	       padding: 10px 15px;
	       text-align: center;
	       text-decoration: none;
	       display: inline-block;
			font-size: 16px;
			cursor: -webkit-pointer; 
    	    cursor: pointer;
    	    opacity: 1.0;
	}
	button:hover{
   		background-color: var(--barva2);
    }

 </style>
 
<body>
<script> 
	var last=1;
	//Funkce slouzi k prepinani mezi jednotlivymi nactenymi strankami
     function show(x){  
      if(last){
      	$("#song"+last).hide(0); 
      	$( "#song"+x).show(500);     
      	last=x; 
        var report=document.getElementById('reportstyl');
        report.innerHTML="";
  	  }
  	  else
  	  {
  	  	$("#song"+x).hide(0); 
      	$("#song"+last).show(500);       
      	last=x; 
        var report=document.getElementById('reportstyl').innerHTML="";
        report.innerHTML="";
  	  }
     }
     //Vlozi do adresy ID songu pro detail
     function get_song_info(obj,back){
     	var url=window.location.origin+window.location.pathname;
     	if(back)url+='?form=song04&getsongdetail='+obj+'&comeback='+back;
     	else url+='?form=song04&getsongdetail='+obj;
     	window.location=url;
     }
     //Vlozi do adresy ID playlistu pro detail
     function get_playlist_info(obj){
     	var url=window.location.origin+window.location.pathname;
     	url+='?form=song10&getplaylist='+obj;
     	window.location=url;
     }
     //Vlozi do adresy ID songu v playlistu
     function  get_song_playlist_info(obj){
     	var url=window.location.origin+window.location.pathname;
     	url+='?form=song10&get_songplaylist_info='+obj;
     	window.location=url;
     }
     //Vlozi do adresy ID media pro detail
     function  get_media_info(obj){
     	var url=window.location.origin+window.location.pathname;
     	url+='?form=song13&get_media_info='+obj;
     	window.location=url;
     }
	   //Vlozi do adresy ID playlistu pro smazani
      function get_playlist_del(obj){
      var url=window.location.origin+window.location.pathname;
      url+='?form=song08&del='+obj;
      window.location=url;
     }
     //Vlozi do adresy ID playlistu pro smazani
      function get_IDsonginplaylist(PL,obj){
      var url=window.location.origin+window.location.pathname;
      url+='?form=song10&Dsong='+obj+'&playlist='+PL;
      window.location=url;
     }
     //get_IDsonginplaylist
	//funkce zajisti ze pokud je v URL adrese retezec form a cislo stranky,tak po reloadu stranky bude zase tato stranka jako prvni viditelna
     function Onload(){
	     var query = window.location.href;
		    if(query.search("form=song02")> 0 ){
		    	var ob=document.getElementById('song2');
		    	ob.style='display:block';
		    	last=2; 
		    }
		    else if(query.search("form=song20")>0)
		    {
		    	var ob=document.getElementById('song20');
		    	ob.style='display:block';
		    	//ob.style.show(500);
		    	last=20; 
		    }
		    else if(query.search("form=song04")>0){
		    	var ob=document.getElementById('song4');
		    	ob.style='display:block';
		    	last=4;
		    }
		    else if(query.search("form=song09")>0){
		    	var ob=document.getElementById('song9');
		    	ob.style='display:block';
		    	last=9;
		    }
		    else if(query.search("form=song08")>0){
		    	var ob=document.getElementById('song8');
		    	ob.style='display:block';
		    	last=8;
		    }
		    else if(query.search("form=song10")>0){
		    	var ob=document.getElementById('song10');
		    	ob.style='display:block';
		    	last=10;
		    }
		    else if(query.search("form=song11")>0){
		    	var ob=document.getElementById('song11');
		    	ob.style='display:block';
		    	last=11;
		    }
		    else if(query.search("form=song12")>0){
		    	var ob=document.getElementById('song12');
		    	ob.style='display:block';
		    	last=12;
		    }
		    else if(query.search("form=song13")>0){
		    	var ob=document.getElementById('song13');
		    	ob.style='display:block';
		    	last=13;
		    }else if(query.search("form=song20")>0){
		    	var ob=document.getElementById('song20');
		    	ob.style='display:block';
		    	last=20;
		    }else if(query.search("form=song25")>0){
		    	var ob=document.getElementById('song25');
		    	ob.style='display:block';
		    	last=25;
		    }
		    else{
		    	var ob=document.getElementById('song1');
		    	ob.style='display:block';
		    	last=1;
		    }
		 }
	 //}

 	window.addEventListener("load", Onload);
    
  </script>	

<!-- a ***************************************USER_LOGOUT--------------------------------------- -->
<script type="text/javascript">
	function logvisible(){
		var p=document.getElementById("logout");
		if(p.style.display=="block")$("#logout").hide(500);
		else $("#logout").show(500);
	}

</script>

<div id="Menu">
	<h3 id="item1" onclick="show(1)"  style="float:left" >Skladby</h3>
	<h3 id="item1" onclick="show(8)"  style="float:left">Playlisty</h3>
	<h3 id="item1" onclick="show(11)" style="float:left">Media</h3>

	<?php
			//get_modul($_SESSION["namepage"],$_SESSION["loguser"],$_SESSION["heslo"]);
		if($_SESSION["role"]=="admin"){
			echo"<h3 id='item1'onclick='show(20)' style='float:left' name='Admin'>Administrace</h3>";
		}
		if(!empty($_SESSION['last_selecet_song'])&& !empty($_GET['getsongdetail']))
			echo"<h3 id='item1'onclick='show(4)' style='float:left'>Last_Song </h3>";
	?>	

	<div id="user" align="right">
		<img  src="user.png" width="50px" height="50px" onclick="logvisible();">
		<p><?php echo $_SESSION['loguser'];?></p>
	</div>

</div>

 <div id="logout" align="right">
	<img  align="left" src="user.png" width="80px" height="80px">
	<div id="info" align="left">
		<p>Admin email<br>musron@seznam.cz</p>
		<HR size=1 width="52%" align="left" noshade color="white">
		<h4 onclick='show(25);$("#logout").hide(500);'>Změnit heslo</h4>
	</div>
	<center>
		<br>
		<form action="main.php" method="post">
			<button type="submit" name="Logout">Odhlásit se</button>
		</form>
	</center>
</div>

<!-- a ***************************************Obsah stranky************************************ -->
<center>
<?php
 	if(!empty($_SESSION['note']))echo "<h2 style=\"color:white\"> Zprava - ". $_SESSION['note']."</h2>";
 ?>

<!-- a ***************************************SONG_VYPIS----------------------------------------->
<div id="song1" class="user-UI-form">
	<div id="container">

		<h1>Výpis Skladeb</h1>
		<h3>Databáze obsahuje: 
		<?php echo Count_record("skladby",NULL,0)." songů\n<br>";
			
			 /* if(!empty($_SESSION['filtr']))
				Count_record("skladby",$_SESSION['filtr'],1);
			  else
				Count_record("skladby",NULL,1);*/
		?>
		</h3>
		<form method="post" action="main.php">
		<?php
			if(!empty($_SESSION['filtr']))
				echo "<button type='submit' name='zrusit_filtr'>Zrušit všechny filtry</button> <br><br>";
		?>	
		<table>	
		<tr><th>Počet záznamů</th>
		<td>
		<?php
			echo"<select style='width: 40px;' name='pocet' size='1'>";
			
			//zajistuje vybarveni vybraneho poctu zaznamu na strance pri dalsim reload stranky
			if(!empty($_SESSION['lastnumberzobraz'])){
				for($i=10;$i<=40;$i+=10){
					if($i==$_SESSION['lastnumberzobraz'])echo "<option value='".$i."' selected>".$i."</option>\n\t\t\t";
					else echo "<option value='".$i."'>".$i."</option>\n\t\t\t";
				}		
			}
			else
			{
				for($i=10;$i<=40;$i+=10){
					echo "<option value='".$i."'>".$i."</option>\n\t\t\t";
				}	
			}
				echo"</select>";
		?>
		</td>

		<td><button type="submit" name="zobraz">Zobrazit</button></td>	
		</form>
		<td><button type="button" onclick="show(3)">Vyhledavani</button></td> 
		<?php
			$_SESSION['namepage']="song_vypis";	
			get_modul($_SESSION["namepage"],$_SESSION["loguser"],$_SESSION["heslo"]);//Funkce vygeneruje tlacitka na zaklade prav uzivatele
		?>	
			</tr>
		</table>
		
		<?php
    //zobraz je indikace ze bylo stitknute pro vyber poctu zaznamu na stranku
    //sidenumber je cislo stranky pri strankovani
		if(isset($_POST['zobraz']) | !empty($_SESSION['sidenumber'])){
			   if(!empty($_SESSION['filtr']))
			   		vypis_songy($_SESSION['filtr'],$_SESSION['limitmin'],$_SESSION['lastnumberzobraz']);
			   else
			   		vypis_songy(NULL,$_SESSION['limitmin'],$_SESSION['lastnumberzobraz']);
		}	   
		else
		{
			if(!empty($_SESSION['filtr'])){
				vypis_songy($_SESSION['filtr'],0,$_SESSION['lastnumberzobraz']);
			}
			else
				vypis_songy(NULL,0,$_SESSION['lastnumberzobraz']);
		}

		//Provadi generovani odkazu  na zaklade toho jestli byl zadan filtr ci nikoli
		if(!empty($_SESSION['filtr']))
			Count_record("skladby",$_SESSION['filtr'],1);
		else
			Count_record("skladby",NULL,1);
		?>
		<br>
	</div>

</div>	


<script type="text/javascript">
	function addKapela(x){
		var pole=document.getElementById("extend");
		var pole2=document.getElementById("extend2");
		var editkapela=document.getElementById("editpolekapela");
		var polozka=x.selectedOptions[0].text;
		var polozka2=x.selectedOptions[0].text;
		//if(pokus=="Jiný")p.show();
		//else p.hide();
		if(polozka=="Jiný"){
			pole.innerHTML="Nová kapela:<br> <input type='text' name='jinakapela'>";
			$(pole).show(1000);
		}
		else {
			pole.innerHTML="";
			$(pole).hide(50);
		}
		
		//Slouzi zaroven i pro zobrazeni pole v detailu skladby neni dodelano!
		if(polozka2=="Jiný"){
			editkapela.disabled=true;
			/*pole2.innerHTML="Nová kapela:<br> <input type='text' name='jinakapela2'><br><button type=\"submit\" name='Pridatsong' >Přidat</button>";
			$(pole2).show(1000);*/
		}
		else {
			/*pole2.innerHTML="";*/
			editkapela.disabled=false;
			//$(pole2).hide(50);
		}
		
	} 

</script>
<!-- a ***************************************SONG_INSERT----------------------------------------->
<div id="song2" class="user-UI-form">
	<div id="container">
		<h1>Insert</h1>	
		<?php if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";?>
		<form action ="main.php?form=song02" method="post">

			<table >
				<tr><th colspan="2">Databáze obsahuje: <?php echo Count_record("skladby",NULL,0);?> songů</th></tr>
				<tr><th>Název (*povinné)</th>
					<th><input type="text" name="song_name"></th>
				</tr>
				<tr>
					<th>Kapela (*povinné)</th>
					<th><select style="width: 173px;" onchange="addKapela(this)" name="kapela" size="10"
						><?php get_items(NULL); ?></select></th>
					<th id="extend"></th>
				</tr>
				<tr><th>Délka(formát min:sec)</th>
					<th><input type="value" name="delka"></th>
				</tr>
				<tr><th>Odkaz(Youtube) (*povinné)</th>
					<th><input type="text" name="url"></th>
				</tr>
				<tr><th><button type="button" onclick="show(1)" >Zpět na výpis</button></th>
					<th><button type="submit" name="Add_song" >Add song</button></th>
				</tr>
			</table>
		</form>
		<br>
		<br>
	</div>
</div>
<!-- a ***************************************SONG_FILTR----------------------------------------->
<div id="song3" class="user-UI-form">
	<div id="container">
		<h1>Filtr</h1>	
		<form action ="main.php" method="post">
			<table>
				<tr><th>Název</th><th>
					<input type="text" name="findsong_name" size="25" 
					<?php if(!empty($_POST['findsong_name']))echo"value='".$_POST['findsong_name']."'";?> ></th></tr>
				<tr><th>Kapela</th>
					<td><select style="width: 173px;" name="findkapela" size="10"><?php get_items($_POST['findkapela']);?></select></td>
				</tr>
				<tr><th>Délka (min a max v minutách)</th>
					<th><input style='text-align:center' center size="8" type="value" name="delka_min" 
						<?php if(!empty($_POST['delka_min']))echo'value='.$_POST['delka_min'].'';
							  else 
                  echo'value="00:00"';
						?>>  
						<input style='text-align:center' size="10" type="value" name="delka_max" 
						<?php if(!empty($_POST['delka_max']))echo'value='.$_POST['delka_max'].'';
							    else 
                  echo'value="10:00"';
						?>> 
				</th>
				</tr>
				<tr><th>Autor</th>
					<th><?php vypis_uzivatelu(NULL,0,30,1);?></th>
				</tr>
				<!--<tr><th>Datum vložení ve formátu (den/mesic/rok)</th>
					<td><input type="value" name="finddate" disabled></td>
				</tr>-->
				<tr><th><button type="button" onclick="show(1)">Zpět na výpis</button></th>
					<th><button type="submit" name="Vyhledat" >Vyhledat</button></th>
				</tr>
			</table>
		</form>
		<br>
		<br>
	</div>
</div>
<!-- a ***************************************SONG_DETAIL----------------------------------------->
<script type="text/javascript">
	function editKapela(x){//funkce vlozi do kapela edit vybranou polozku ze selectu
		var pole=document.getElementById("editpolekapela");
		var select=document.getElementById('selectkapela');
		pole.value=select.options[select.selectedIndex].text;
		var ID=select.value;
	} 
</script>

<div id="song4" class="user-UI-form">
	<div id="container">
		<h1>Detail</h1>		
		<?php
		if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";
		if(!empty($_GET['getsongdetail'])){
			Edit_form($_GET['getsongdetail']);
		}
		?>
		<br>
		<br>
	</div>
</div>

<!-- a ***************************************PLAYLIST_VYPIS----------------------------------------->
<div id="song8" class="user-UI-form">
	<div id="container">

		<h1>Výpis Playlistů</h1>
		<button type="button" onclick="show(9)">New playlist</button>		
		<?php
			vypis_playlist(0);
		?>
	</div>
</div>
<!-- a ***************************************PLAYLIST_Insert----------------------------------------->
<div id="song9" class="user-UI-form">
	<div id="container">
		<h1>New Playlist</h1>	
		<?php if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";
		?>
		<form action ="main.php?form=song09" method="post">
			<table >
				<tr><th>Název playlistu(*povinné)</th>
					<th><input type="text" name="name_playlist"></th>
				</tr>
				<tr>
					<th colspan="2">						
						<button type="submit" name="new_playlist" >Add Playlist</button>					
					</th>
				</tr>

			</table>
			<button type="button" onclick="show(8)">Zpět na výpis playlistu</button>
		</form>
		<br>
		<br>
	</div>
</div>
<!-- a ***************************************PLAYLIST_editace----------------------------------------->

<script type="text/javascript">
	function pl_extend(x,invalue){
		var editname=document.getElementById("box_pl_name");
		var buttonvissible=document.getElementById("box_pl_tlbuttonvissible");
		if(x){
			//buttonvissible.style='display:none';	
			//editname.style='display:block';
			$( "#box_pl_tlbuttonvissible").hide(500);  	
			$( "#box_pl_name").show(500);  
		}
		else{
			//buttonvissible.style='display:block';	
			//editname.style='display:none';
			$("#box_pl_name").hide(500);  
			$( "#box_pl_tlbuttonvissible").show(500);  	
		}	
	} 
</script>
<div id="song10" class="user-UI-form">
	<div id="container">
		<?php
			if(isset($_GET['getplaylist']))$_SESSION['lastplaylist']=$_GET['getplaylist'];
				Edit_playlist($_SESSION["loguser"],$_SESSION['lastplaylist']);
		?>	
		<br>
		<br>
	</div>
</div>

<!-- a ***************************************Media_VYPIS----------------------------------------->
<div id="song11" class="user-UI-form">
	<div id="container">

		<h1>Výpis Medii</h1>
		<button type="button" onclick="show(12)">New medium</button>		
		<?php
			vypis_medii(0);
		?>
	</div>
</div>
<!-- a ***************************************Media_Insert----------------------------------------->
<div id="song12" class="user-UI-form">
	<div id="container">
		<h1>New Media name</h1>	
		<?php if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";
		?>
		<form action ="main.php?form=song12" method="post">
			<table >
				<tr><th>Název media(*povinné)</th>
					<th><input type="text" name="name_medium"></th>
				</tr>
				<tr>
					<th colspan="2">						
						<button type="submit" name="new_medium" >Add Media</button>					
					</th>
				</tr>

			</table>
			<button type="button" onclick="show(11)">Zpět na výpis medii</button>
		</form>
		<br>
		<br>
	</div>
</div>
<!-- a ***************************************Media_editace----------------------------------------->
<script type="text/javascript">
	function medium_extend(x,invalue){
		var editname=document.getElementById("box_media_name");
		var buttonvissible=document.getElementById("box_media_tlbuttonvissible");

		if(x){
			$(buttonvissible).hide(200);  	
			$(editname).show(500);  
		}
		else{
			$(editname).hide(200);  
			$(buttonvissible).show(500);  	
		}	
	} 
</script>
<div id="song13" class="user-UI-form">
	<div id="container">
		<?php
			if(isset($_GET['get_media_info']))$_SESSION['lastmedia']=$_GET['get_media_info'];
				Edit_media($_SESSION["loguser"],$_SESSION['lastmedia']);
		?>	
		<br>
		<br>
	</div>
</div>

<!-- a ***********************************User edit----------------------------------------->
<div id="song25" class="user-UI-form">
	<div id="container">
		<h2>Změna hesla</h2>
		<?php
		if(!empty($_SESSION['report'])){echo "<div id='reportstyl'><h4>".$_SESSION['report']."</h4></div>";}
		?>
			<form action ="main.php?form=song25" method="post">
				<table >
				<tr><th>Nové heslo</th>
					<td><input type="password" name="new_pas1"></td>
				</tr>
				<tr><th>Heslo znovu</th>
					<td><input type="password" name="new_pas2"></td>	
				</tr>
			</table>
			<button type="submit" name="new_pas">Uložit</button>
			</form>
		<br>
		<br>
	</div>
</div>
<!-- a ***************************************Administrace----------------------------------------->
<div id="song20" class="user-UI-form">
	<div id="container">
		<h1>Administrace</h1>	
		<?php 
			if($_SESSION["role"]=="admin"){
				vypis_uzivatelu(NULL,0,30,0);
				vypis_roli();
			}
		?>
		<br>
		<br>
	</div>
</div>

<?php
unset($_SESSION['report']);
//unset($_SESSION['filtr']);
//unset($_SESSION['note']);//pro ladici ucely
?>
</center>	
</body>
</html>
