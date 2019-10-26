<?php
function vypis_playlist($druh){
//Funkce vycita jmena existujicich playlistu kam je pozne ulozit pisne v karte song_detail
//$druh nabyva hodnot 0 vypisuje se na stranku vypis playlistu jako tabulka
//                    1 vypisuje se v karte song detail jako rolovaci seznam 
if($_SESSION["role"]!="admin")
  $sql = 'Select playlist_name .ID,'
        . ' playlist_name .Name as Název, '
        . ' user.Login'
        . ' from playlist_name left join (user) '
        . ' on (playlist_name .ID_uzivatele=user.ID)'
        . ' where user.Login="'.$_SESSION["loguser"].'"';
else
   $sql = 'Select playlist_name .ID,'
        . ' playlist_name .Name as Název, '
        . ' user.Login'
        . ' from playlist_name left join (user) '
        . ' on (playlist_name .ID_uzivatele=user.ID)'
        . ' where 1';     

  //*************************Serazeni  ********************************
  if(!empty($_GET['playlistasc']))$asc='asc';//rozpoznání jestli se mají záznamy seradit sestupně nebo vzestupně
  else $asc='desc'; 

  if(!empty($_GET['playlistorder']))  $sql=$sql." ORDER BY ".$_GET['playlistorder']." ".$asc;
  $result = mysqli_query($_SESSION['conn'],$sql);     
   
   //Podminka zajisti ze se adminovi objevi jmeno sloupce kde je vypsan majitel playlistu
  if($_SESSION["role"]!="admin") $sloupce=1;
  else $sloupce=0;
  if(!$druh){//Pokud se vstupni parametr rovna 0 pak se playlisty vypisuji do tabulky na strance playlist
    echo"
    <table  cellpadding='5' cellspacing='5' frame='bellow' style='text-align:center'>     
    <tr style='background:white;'>";
    for($i=1;$i<$result->field_count-$sloupce;$i++){
        if(!empty($_GET['playlistasc']))
            echo "<th>".$result->fetch_field_direct($i)->name."<a class=\"order\" href=\"?form=song08&cl=2&playlistorder=".$result->fetch_field_direct($i)->name."&playlistasc=0\"> <span>▲</span></a></th>";
        else 
            echo "<th>".$result->fetch_field_direct($i)->name."<a class=\"order\" href=\"?form=song08&cl=2&playlistorder=".$result->fetch_field_direct($i)->name."&playlistasc=1\"> <span>▼</span></a></th>";   
        }
    echo "</tr>";
    $pocet=0;
    while($row=mysqli_fetch_array($result)) 
    {
      $pocet++;
      //echo"<form action ='main.php?form=song08&del=$row[0]' method='post'>\n\t\t\t";
        echo"<tr>
            <td onclick=\"get_playlist_info($row[0]);\" style=\"cursor: pointer\">$row[1]</td>";
            if($_SESSION["role"]=="admin")
              echo"<td style=\"cursor: pointer\">$row[2]</td>
                    <td><button type='button' onclick=\"get_playlist_del($row[0]);\">Smazat</button></td> ";
            else
            echo"<td><button type='button' onclick=\"get_playlist_del($row[0]);\">Smazat</button></td> 
        </tr>";//</form>";
    }
    if(!$pocet)echo"<tr><td colspan='100%'><center>Nemáte žádné playlisty</center></td></tr>";  
    echo"</table>";
  }
  else//Pokud se vstupni parametr nerovna 0 pak se playlisty vypisuji do selectu na strance song detail
  {
    echo"<tr>\n\t\t\t\t<th>Playlist</th><td>";
    echo"<select name=\"playlist_select\" size='1'>";
    echo"<option value=\"0\"></option>\n\t\t\t\t";
    while($row=mysqli_fetch_array($result))
    {
      //do value se uklada ID playlistu
    	if($_SESSION["role"]=="admin")
      		echo"<option value=\"".$row[0]."\">$row[1] - $row[2]</option>\n\t\t\t\t";
      	else
      		echo"<option value=\"".$row[0]."\">$row[1]</option>\n\t\t\t\t";
    }
    echo"</select>"; 
    echo"</td></tr>";
  }
}
//******************************************************************************************
function Vytvorit_playlist(){
  $sql ="INSERT INTO playlist_name (ID,Name,ID_uzivatele)
         VALUES(NULL,'".$_POST["name_playlist"]."','".$_SESSION["logID"]."')";
  if(mysqli_query($_SESSION['conn'],$sql))
   $_SESSION['report']="Playlist byl vytvořen";  
  else
    $_SESSION['report']="Playlist tohoto názvu již existuje";  
}
//*****************************************************************************************
//Funkce pro pridani pisne do playlistu
function add_to_playlist($IDuser,$IDsong,$IDplaylist){
  $sql ="INSERT INTO playlist (ID_uzivatele,ID_playlist,ID_song,Date_insert)
         VALUES('".$IDuser."','".$IDplaylist."','".$IDsong."','".date("Y/m/d")."')";  
  if($IDplaylist){
    if($result=mysqli_query($_SESSION['conn'],$sql))
          $_SESSION['report']="song byl přidán do playlistu";
    else
          $_SESSION['report']="Song již playlistu existuje";
  }
  else
       $_SESSION['report']="Není vybrán playlist";  
}
//****************************************************************************************
function Edit_playlist($user,$IDplaylist){
 $sql = 'SELECT skladby.Nazev, '
        . ' kapela_name.K_name as Kapela, '
        . ' skladby.Delka, '
        . ' skladby.Odkaz, '
        . ' user.Login as Vložil,'
        . ' playlist.ID as PL_ID, '
        . ' playlist_name.Name, '
        . ' skladby.ID,'
        . ' playlist.ID FROM playlist left join (skladby,playlist_name,kapela_name,user) '
        . ' on (playlist.ID_song=skladby.ID and skladby.Kapela=kapela_name.ID and  playlist.ID_uzivatele=user.ID and playlist.ID_playlist=playlist_name.ID) '
        . ' where playlist_name.ID="'.$IDplaylist.'"';

  //*************************Serazeni  ***************************************
  if(!empty($_GET['editplaylistasc']))$asc='asc';
    else $asc='desc'; 

  //Vkládá string pro serazeni
  if(!empty($_GET['orderplaylist']))  $sql=$sql." ORDER BY ".$_GET['orderplaylist']." ".$asc;
//slouzi pro vypis jmena playlistu 
$result=mysqli_query($_SESSION['conn'],$sql);
$sqlpozadavek = 'select playlist_name.Name,playlist_name.ID FROM playlist_name where playlist_name.ID="'.$IDplaylist.'"';
$vysl=mysqli_query($_SESSION['conn'],$sqlpozadavek);
$Nazev=mysqli_fetch_array($vysl);
echo "<h2>Výpis playlistu: ".$Nazev[0]."</h2>";
//*******************************************
//******************Pro zmenu nazvu playlistu********************************
if(!empty($_SESSION['report']))echo "<div id='reportstyl'><h3>".$_SESSION['report']."</h3></div>";
    
echo"<button type=\"button\" onclick=\"show(8)\" >Zpět na výpis</button> <br> <br>";
echo"<div id='box_pl_tlbuttonvissible'>
        <button type=\"button\" onclick='pl_extend(1,\"$Nazev[0]\")'>Upravit jméno playlistu</button>
    </div>
    \n\t\t\t";//pl_extend funkce ktera z vyditelni pole pro editaci nazvu playlistu

echo"<form action ='main.php?form=song10&edit_playlist_ID=$Nazev[1]' method='post'>\n\t\t\t
        <div id='box_pl_name'>
          Název: <input type='text' size='10' name='new_name_playlist' value=".$Nazev[0].">
          <button type=\"submit\" name='Editnameplaylist'>Save</button> 
          <button type=\"button\" onclick='pl_extend(0,0)'>Cancel</button>
        </div>\n\t\t\t 
    </form>";//parametr form=song10 zajistuje ze pri reloadu se zustane na aktualni strance.
             //parametr edit_playlist_ID udava ID editovaneho playlistu

//***************************************************************************
//Vypis obsahu playlistu do tabulky
echo"<table cellpadding='2' cellspacing='2' frame='bellow' style='text-align:left'>\n\t\t\t\t";
echo"<form action ='main.php?form=song10&edit_playlist_ID=$Nazev[1]' method='post'>\n\t\t\t";
echo vypis_medii(1)."<td><button type=\"submit\" name='Insert_to_media'>Přidat do media</button></td>\n
                    </form>";
echo"<td border='1'><button type=\"button\" onclick='show(09)'>Nový playlist</button></td>";//Tlacitko nove medium
echo "<tr style='background:white;'>\n\t\t\t\t\t";
//Vypisuje nazvy sloupcu ktere jsou primo vycteny z databaze a zaroven je generuje jako odkaz s parametrem pro serazeni
    for($i=0;$i<$result->field_count-4;$i++){
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
    //Slouzi pro výpis zaznamu do tabulky
    $pocet=0;//pomocna promenna ktera pocita pocet zaznamu pokud se bude rovnat 0 playlist je prazdny
    while($row=mysqli_fetch_array($result)) 
    {
      $pocet++;
        //get_IDsonginplaylist ID sonu ktery odstranujem
       // echo"<form action ='main.php?form=song10&get_IDsonginplaylist=$row[7]' method='post'>\n\t\t\t";
        echo"<tr>
            \t<td style=\"cursor: pointer\" onclick=\"get_song_info($row[7],1)\">$row[0]</td>
            \t<td>$row[1]</td>\n\t\t\t\t";
            if($row[2]==NULL | $row[2]=="") echo"<td>-</td>";
            else echo"<td>";secondMinute($row[2]);echo"</td>\n\t\t\t\t";
            echo"<td>$row[3]</td>\n\t\t\t\t<td>$row[4]</td> 
            \t<td><button type='button' onclick=\"get_IDsonginplaylist($IDplaylist,$row[7])\">Odstranit</button></td> 
       \t\t </tr>\n\t\t\t";//</form>
    }//<td><button type='submit'>Odstranit</button></td> 
      if(!$pocet)echo"<tr><td colspan='100%'><center>Playlist je prázdný</center></td></tr>";
    //echo"<tr></tr>";
    echo"</table>";
}

?>