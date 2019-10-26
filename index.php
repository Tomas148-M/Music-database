<?php
//session_start();
include ("pripojeni.php");
$_SESSION['report']="";
include ("administrace.php");

if(isset($_POST['log'])|!empty($_SESSION["logID"]))
{
	if(empty($_SESSION["logID"])){
		$sql = "select user.ID,\n"
	    . "	user.Login,\n"
	    . "	user.Heslo,\n"
	    . " role.Role_name,\n"
	    . "	user.Email\n"
	    . "FROM user left join (role,role_user) 
	    	on (role_user.ID_user=user.ID and role_user.ID_role=role.ID)\n"
	    . 'WHERE (Login="'.$_POST['login'].'" AND Heslo="'.$_POST['password'].' " AND Stav=0)';

		if($result = mysqli_query($_SESSION['conn'],$sql)){
			$row=mysqli_fetch_array($result);
			$_SESSION["logID"]=$row[0];
			$_SESSION["loguser"]=$row[1];
			$_SESSION["heslo"]=$row[2];
			$_SESSION["role"]=$row[3];
			$_SESSION["logemail"]=$row[4];
			unset($row);
			unset($result);
		}
	}	
		//echo "*".$_SESSION["ID"]." ".$_SESSION["user"]."*";
	if(!empty($_SESSION['logID'])){
			//if($_SESSION['ID']==0){
		header("Location:main.php");
		unset($_SESSION['note']);
	    //unset($_SESSION['error']);
		die();
	}
}	

if(isset($_POST['Registrovat'])){
	if(!empty($_POST['Jmeno']) 
		&& !empty($_POST['Prijmeni']) 
		&& !empty($_POST['Email'])
		&& !empty($_POST['Login'])
		&& !empty($_POST['Heslo'])){
		registrace();        
	}
	else
	{
		$_SESSION['note']="Vyplňte všechny udaje";
	}
}
if(!empty($_GET['Message']))$_SESSION['note']="Neoprávněná operace";

?>
<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta charset="UTF-8">
<script src="jquery-1.10.2.js"></script>
 <style>
	body{/*background:url('1383304937115.jpg');*/
		background:url('tapeta.jpg');
		 background-repeat:no-repeat;
		 background-size: cover;}

    #user{position:absolute;left:95%;top:20px;}
    #user p{color:white;font-family: arial;font-size:110%;}
    #left_panel{background-color:black;color:white;
    			width:100px;
    			
    			border-right: 4px solid white;  
    			border-bottom: 4px solid white;
    			box-shadow:4px red;
    			border-radius: 5px  
    			}
    #container{background-color:grey;
    		   opacity:0.9;
    		   width:70%;
    		   position:relative; 
    		   border-radius: 10px }
    .order{text-decoration:none;color:black}
    .user-UI-form{display:none};
   
	table{background:green;
			 color:white;
			}	
	#form1{display:none;
		   background:grey;
		   width:400px ;
		   position:relative; 
		   top: 140px;
		   border-radius: 15px;
		   border: 1px solid white;}
	#form2{display:none;
		   background:grey;
		   width:400px ;
		   position:relative; 
		   top: 140px;
		   border-radius: 15px;
		   border: 1px solid white;	
	}

	#zprava{background: red;
			opacity:0.7;
			font-size: 35px;
			color:white;
			border: 1px solid white;
			border-radius: 5px;	
			box-shadow: 5px 5px 5px 0px rgba(255,255,255,0.8);
			padding: 0px;
			position: absolute;
			left: 0; 
  			right: 0; 
  			margin-left: auto; 
  			margin-right: auto;
  			text-align: center 
	}		
	
	button{
    	   position: relative;top:0px;
    	   background: green;
    	   background-color: #4CAF50; /* Green */
		   border: none;
	       color: white;
	       padding: 10px 15px;
	       text-align: center;
	       text-decoration: none;
	       display: inline-block;
			font-size: 16px;
			cursor: -webkit-pointer; 
    	    cursor: pointer;
    	    float:left;
    	    opacity: 1.0;
    	    display: inline-block;
	}
	button:hover{
   		background-color: #3e8e41;
    }
    td{padding:4px;}
    .user-UI-form{display:block};

 </style>
<body>

<script type="text/javascript">
	var last=1;

	 function Onload(){
	     var query = window.location.href;
		    if(query.search("form=form2")> 0 ){
		    	var ob=document.getElementById('form2');
		    	ob.style='display:block';
		    	last=2; 
		    }
		    else
		    {
		    	var ob=document.getElementById('form1');
		    	ob.style='display:block';
		    	last=1; 
		    }
	}	    
	 function show(x){  	
      if(last){
      	$("#form"+last).hide(200); 
      	$( "#form"+x).show(500);       
      	last=x; 
  	  }
  	  else
  	  {
  	  	$("#form"+x).hide(200); 
      	$("#form"+last).show(500);       
      	last=x; 
  	  }
  	  
     }

     function Vymaz(){
     	$("#zprava").hide(600); 
     }
     function Delete(){
     	document.getElementById("Jmeno").value = "";
     	document.getElementById("Prijmeni").value = "";
     	document.getElementById("Email").value = "";
     	document.getElementById("Login").value = "";
     	document.getElementById("Heslo").value = "";
     }

     window.addEventListener("load", Onload);
     setTimeout(Vymaz,4000);
</script>
<?php
	if(isset($_POST['log']) && empty($_SESSION['logID']))$_SESSION['note']="Přístup odepřen";
 	if(!empty($_SESSION['note']))echo "<div id='zprava'>". $_SESSION['note']."</div>";
?>
<center>

	<!-- a ***************************************Prihlaseni--------------------------------------- -->
	<div id="form1" class="user-UI-form">
 		<h2>Přihlášení do hudební databáze</h2>
		<form action ="index.php" method="post">
		<table >
			<tr><th>Login:</th><td><input type="text" name="login" size=8></td></tr>
			<tr><th>Password:</th><td><input type="password" name="password" size=8></td></tr>
			<tr><td><button type="button" onclick="show(2)" name="log">Registrace</button></td>
				<td><button type="submit" name="log" onclick="Vymaz();">Přihlásit</button></td>
			</tr>
			<tr></tr>
		</table>
		<br>
		</form>
	</div>

	<!-- a ***************************************Registrace--------------------------------------- -->
	<div id="form2" class="user-UI-form">
 		<h2>Vytvoření účtu</h2>
 		<?php
 		//if(isset($_POST['log'])){
 			//if(empty($_SESSION['logID'])) echo "Přístup odepřen";
 			//else echo "Přístup povolen".$_SESSION["loguser"];
 		//}
 		/*else
 		{

 		}	
 		*/
 		if(!empty($_SESSION['report']))echo $_SESSION['report'];//haseni o stavu
 		?>	
		<form action ="index.php?form=form2" method="post">
		<table >
			<tr><th>Jméno:</th>
				<td><input type="text" id="Jmeno" name="Jmeno" size=20
					<?php 
						if($_SESSION['report']!="Registrace proběhla úspěšně" && isset($_POST['Registrovat']))
						   echo' value="'.$_POST['Jmeno'].'"';
						else
						 echo'';
					?> >
				</td>
			</tr>
			<tr>
				<th>Příjmení:</th>
				<td><input type="text" id="Prijmeni" name="Prijmeni" size=20
					<?php 
						if($_SESSION['report']!="Registrace proběhla úspěšně" && isset($_POST['Registrovat']))
						   echo' value="'.$_POST['Prijmeni'].'"';
						else
						  echo'';
					?>>
				</td>
			</tr>
			<tr><th>Email:</th>
				<td><input type="text" id="Email" name="Email" size=20 
					<?php 
						if($_SESSION['report']!="Registrace proběhla úspěšně" && isset($_POST['Registrovat']))
						   echo' value="'.$_POST['Email'].'"';
						else
						  echo'';
					?> >
				</td>
			</tr>
			<tr><th>Login:</th>
				<td><input type="text" id="Login" name="Login" size=20></td>
			</tr>
			<tr><th>Heslo:</th><td><input type="password" id="Heslo" name="Heslo" size=20></td></tr>
			<tr><td><button type="button" onclick="show(1);Delete()">Zpět na prihlášení</button></td>
				<td><button type="submit" name="Registrovat" >Registrovat</button></td>
			</tr>	
		</table>
		<br>
		</form>
	</div>
</center>
<?php
unset($_SESSION['note']);
unset($_SESSION['report']);
unset($_SESSION['error']);
?>
</body>
</html>