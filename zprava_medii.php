<?php
//phpinfo();

function vypis_medii($druh){
//Funkce vycita jmena existujicich playlistu kam je pozne ulozit pisne v karte song_detail
//$druh nabyva hodnot 0 vypisuje se na stranku vypis playlistu jako tabulka
//                    1 vypisuje se v karte song detail jako rolovaci seznam 

//DISTINCT zajisti ze se nazev ktery se v tabulce opakuje vybere pouze jednou
$sql = "SELECT DISTINCT media_name.ID, \n"
    . "media_name.Name \n"
    . "FROM media_name\n"
    . "\n"
    . ' WHERE 1';
  if(!empty($_GET['mediaasc']))$asc='asc';
  else $asc='desc'; 

  if(!empty($_GET['mediaorder']))  $sql=$sql." ORDER BY ".$_GET['mediaorder']." ".$asc;
         
  if($result = mysqli_query($_SESSION['conn'],$sql)){     
  
        if(!$druh){
          echo"
          <table  cellpadding='5' cellspacing='5' frame='bellow' style='text-align:center'>     
          <tr style='background:white;'>";
          for($i=1;$i<$result->field_count;$i++){
              if(!empty($_GET['mediaasc']))
                  echo "<th>".$result->fetch_field_direct($i)->name."<a class=\"order\" href=\"?form=song11&cl=2&mediaorder=".$result->fetch_field_direct($i)->name."&mediaasc=0\"> <span>▲</span></a></th>";
              else 
                  echo "<th>".$result->fetch_field_direct($i)->name."<a class=\"order\" href=\"?form=song11&cl=2&mediaorder=".$result->fetch_field_direct($i)->name."&mediaasc=1\"> <span>▼</span></a></th>";   
              }
          echo "</tr>";
          while($row=mysqli_fetch_array($result)) 
          {
            echo"<form action ='main.php?form=song11&delmedia=$row[0]' method='post'>\n\t\t\t";
              echo"<tr>
                  <td onclick=\"get_media_info($row[0]);\" style=\"cursor: pointer\">$row[1]</td>
                   
              </tr></form>";
          }//<td><button type='submit' name='delmedia_button'>Smazat</button></td>
          echo"</table><br>";
        }
        else
        {
         // $_SESSION['kapela']= $result;
          echo"\n\t\t\t\t<th>Medium</th><td>";
          echo"<select name=\"media_select\" size='1'>";
          echo"<option value=\"0\"></option>\n\t\t\t\t";
          while($row=mysqli_fetch_array($result))
          {
            echo"<option value=\"".$row[0]."\">$row[1]</option>\n\t\t\t\t";
          }
          echo"</select>"; 
          echo"</td>";
        }
  }
}
//******************************************************************************************
function Vytvorit_medium(){
  $sql ="INSERT INTO media_name (ID,Name)
         VALUES(NULL,'".$_POST["name_medium"]."')";   
 
  if($result=mysqli_query($_SESSION['conn'],$sql))$_SESSION['report']="Medium bylo vytvoreno";
  else
  {
    $_SESSION['report']="Medium jiz existuje";
  }
  
}

//*****************************************************************************************
//Funkce pro pridani playlistu do media
function add_to_media($IDuser,$IDplaylist,$IDmedia){
  $sql ="INSERT INTO media (ID_media,ID_uzivatele,ID_playlist)
         VALUES('".$IDmedia."','".$IDuser."','".$IDplaylist."')";        
  if(!empty($IDmedia)){
	  if($result=mysqli_query($_SESSION['conn'],$sql)){
	       $_SESSION['report']="Úspěšně přidáno";
	  }
	  else
	  {
	    //$_SESSION['report']=mysqli_error($_SESSION['conn']);
	    $_SESSION['report']="Tento playlist se již v mediu nacházi";
	  } 
  }
  else
  		$_SESSION['report']="Nebylo vybráno medium";        
}
//****************************************************************************************
function Edit_media($user,$IDmedia){
if($_SESSION["role"]!="admin")
 $sql = "SELECT media.ID_playlist,
				playlist_name.Name as Playlist_název,
				user.Login as Vložil
                \n"
              . " FROM media left join (playlist_name,user,media_name)\n"
              . "ON(media.ID_uzivatele=user.ID and\n"
              . " media.ID_playlist=playlist_name.ID and \n"
              . " media.ID_media=media_name.ID)\n"
              . 'WHERE (user.Login="'.$user.'" and media_name.ID="'.$IDmedia.'")';
else
	$sql = "SELECT media.ID_playlist,playlist_name.Name as Playlist_název,
                user.Login as Vložil\n"
              . " FROM media left join (playlist_name,user,media_name)\n"
              . "ON(media.ID_uzivatele=user.ID and\n"
              . " media.ID_playlist=playlist_name.ID and \n"
              . " media.ID_media=media_name.ID)\n"
              . 'WHERE (media_name.ID="'.$IDmedia.'")' ;

  if(!empty($_GET['ascplaylist']))$asc='asc';
    else $asc='desc'; 

  //Vkládá string pro serazeni
  if(!empty($_GET['orderplaylist']))  $sql=$sql." ORDER BY ".$_GET['orderplaylist']." ".$asc;

//$_SESSION['note']=$sql;
//slouzi pro vypis jmena playlistu 
$result=mysqli_query($_SESSION['conn'],$sql);
$sqlpozadavek = 'select media_name.Name,media_name.ID FROM media_name where media_name.ID="'.$IDmedia.'"';
$vysl=mysqli_query($_SESSION['conn'],$sqlpozadavek);
$Nazev=mysqli_fetch_array($vysl);
echo "<h2>Výpis media: ".$Nazev[0]."</h2>";
//*******************************************
//Pro zmenu nazvu playlistu
if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";
echo"<button type=\"button\" onclick=\"show(11)\" >Zpět na výpis</button><br><br>
     <div id='box_media_tlbuttonvissible'>
        <button type='button' onclick='medium_extend(1,\"$Nazev[0]\")'>Upravit jméno media</button>
     </div>
     
     \n\t\t\t";
echo"<form action ='main.php?form=song13&edit_media_ID=$Nazev[1]' method='post'>\n\t\t\t
        <table id='box_media_name'>
        <div>
         <tr><th>Název:</th><td><input type='text' size='10' name='new_name_media' value=".$Nazev[0]."></td></tr>
         <tr><td colspan=\"2\">
              <button type=\"button\" onclick='medium_extend(0,0)'>Cancel</button>
              <button type=\"submit\" name='Editnamedia' align=\"center\">Save</button></td></tr>
        </div>\n\t\t\t
        <table> 
    </form>";
//*****************************************************************

echo"<table cellpadding='2' cellspacing='2' frame='bellow' style='text-align:left'>\n\t\t\t\t";
echo "<tr style='background:white;'>\n\t\t\t\t\t";
for($i=1;$i<$result->field_count;$i++){
        if(!empty($_GET['ascplaylist']))
            echo "<th>".$result->fetch_field_direct($i)->name."
                  <a class=\"order\" href=\"?form=song10&cl=4&orderplaylist=".$result->fetch_field_direct($i)->name."&ascplaylist=0\"></a>
                  </th>\n\t\t\t\t\t";
        else 
            echo "<th>".$result->fetch_field_direct($i)->name."
                  <a class=\"order\" href=\"?form=song10&cl=4&orderplaylist=".$result->fetch_field_direct($i)->name."&ascplaylist=1\"></a>
                  </th>\n\t\t\t\t\t";   
        }
    echo "</tr>\n\t\t\t";
//onclick=\"get_song_playlist_info($row[6])\"
$pocet=0;//pomocna promenna ktera pocita pocet zaznamu pokud se bude rovnat 0 medium je prazdny
while($row=mysqli_fetch_array($result)) 
    {
      $pocet++;
       echo"<form action ='main.php?form=song13&media=$IDmedia&IDplinmedia=$row[0]' method='post'>\n\t\t\t";
        echo"<tr>
            <td onclick=\"get_playlist_info($row[0]);\" style=\"cursor: pointer\">$row[1]</td>";
            //if($_SESSION["role"]=="admin")
            	echo "<td> $row[2]</td><td><button type='submit' name='delplmedia'>Smazat</button></td>
           
        </tr></form>";
          //else	
            	//echo"<td><button type='submit' name='delplmedia'>Smazat</button></td>
    }
if(!$pocet)echo"<tr><td colspan='100%'><center>Medium je prázdné</center></td></tr>";    
    //echo"<tr></tr>";
    echo"</table>";
}

?>