<?php
//phpinfo();

function vypis_songy($dotaz,$lowlimit,$Maxlimit){

    echo"<table  cellpadding='2' cellspacing='2' frame='bellow' style='text-align:center' size='1'>";
    //if(empty($dotaz)){
    //Spojeni tabulek skladby,kapela_name,user
        $sql = 'SELECT skladby.Nazev,'
        . ' kapela_name . K_name as Kapela,'
        . ' skladby . Delka ,'
        . ' skladby . Odkaz,'
        . ' user.Login as Vložil,'
        . ' skladby . ID'
        . ' FROM skladby left join (kapela_name,user) ON '
        . ' ( kapela_name . ID = skladby . Kapela and skladby.ID_uzivatel=user.ID )';
    //Podminka slouzi ke kontrole jestli byl zadán nějaký filtr pro vyhledávání
    if(empty($dotaz)){
        $sql = $sql." where skladby.STAV = 0 ";//Má se vybrat vše co není smazáno tady je ve stavu 0
    }
    else{
        $sql=$sql." where ".$dotaz." skladby.STAV = 0";//Má se vybrat vše co není smazáno tedy je ve stavu 0 a zárověn to co odpovídá filtru pro vyhledávání
    }
    //*************************Serazeni  ********************************
    if(!empty($_GET['ascsong']))$asc='asc';//rozpoznání jestli se mají záznamy seradit sestupně nebo vzestupně
    else $asc='desc'; 

    if(!empty($_GET['ordersong'])){//V parametru ordersong je informace podle jakého sloupce se mají záznamy seřadit lze vzdy pouze podle jednoho
       $sql=$sql." ORDER BY ".$_GET['ordersong']." ".$asc;
      $_SESSION['ascsong']=" ORDER BY ".$_GET['ordersong']." ".$asc;//ulozeni parametru ordersong + asc kvůli přechodu mezi stránkami
    }
    else{ 
        //ochrana proti chybam
        if(isset($_SESSION['ascsong']))$sql=$sql.$_SESSION['ascsong'];
    }
    //*************************Nastaveni limit********************************
    //Slouzi pro nastaveni poctu zaznamu na strance
    if($lowlimit>0 | $Maxlimit>0){
            $sql=$sql." Limit ".$Maxlimit." OFFSET ".$lowlimit;
    }
    //************************************************************************
    //}
    //<span>▲</span> <span>▼</span>
    //$_SESSION['note']=$sql;
    if($result = mysqli_query($_SESSION['conn'],$sql)){//provede se vytvoreny sql dotaz
        echo "<tr style='background:white;'>";
        //Vypisuje nazvy sloupcu ktere jsou primo vycteny z databaze a zároven je generuje jako odkaz s parametrem pro serazeni
        for($i=0;$i<$result->field_count-1;$i++){
            if(!empty($_GET['ascsong']))
                echo "<th><a class=\"order\" href=\"?cl=2&ordersong=".$result->fetch_field_direct($i)->name."&ascsong=0\">".$result->fetch_field_direct($i)->name." </a></th>";
            else 
                echo "<th><a class=\"order\" href=\"?cl=2&ordersong=".$result->fetch_field_direct($i)->name."&ascsong=1\">".$result->fetch_field_direct($i)->name."</a></th>";   
            }
        echo "</tr>";
        //style='background:red'

        //Slouzi pro výpis zaznamu do tabulky
        $pocet=0;//pomocna promenna ktera pocita pocet zaznamu pokud se bude rovnat 0 medium je prazdne
        while($row=mysqli_fetch_array($result)) 
        {
            $pocet++;
            //getsong_info dava informaci o vybranem songu do url adresy
            echo"<tr>
                <td onclick=\"get_song_info($row[5],0)\" style=\"cursor: pointer\">$row[0]</td>
                <td>$row[1]</td>";
                if($row[2]==NULL | $row[2]=="") echo"<td>-</td>";
                else {echo"<td>";secondMinute($row[2]);echo"</td>";}
                echo"<td><a href=\"$row[3]\">$row[3]</a></td>
                     <td>$row[4]</td>
            </tr>";
        }
        if(!$pocet)echo"<tr><td colspan='100%'><center>Žádné záznamy</center></td></tr>";  
    }
    else
    {
        echo"<tr>".mysqli_error($_SESSION['conn'])."</tr>";
    }
    echo"</table>";

}
//******************************************************************************************
function Vytvorit(){

//**************************************************************************************
//************************************VYTVORENI SONGU V DATABAZI************************
//**************************************************************************************
    if(isset($_POST['Add_song'])){//kontrola jestli byl stisknut button pro pridani songu do databaze
    //*********Zalozeni noveho jmena kapely**********//
        if($_POST["kapela"]==0 ){//kapela dava id vybrane moznosti v selectu kapel, 0 je indikace nove kapely
            //Kontrola jestli neni prazdny input ,pokud jsme chteli zadat novy nazev kapely
            if(!empty($_POST["jinakapela"])){//jinakapela je string z inputu ve kterem je nazev nove kapely
                $sql ="INSERT INTO kapela_name (ID,K_name)
                       VALUES(NULL,'".$_POST["jinakapela"]."')";
            }      
            else if(!empty($_POST["jinakapela2"])){
                $sql ="INSERT INTO kapela_name (ID,K_name)
                       VALUES(NULL,'".$_POST["jinakapela2"]."')";
            }
            else
            {
                //report slouzi jako vypis informaci pro uzivatele o stavu provedene operace
                $_SESSION['report']="Nezadali jste nazev nove kapely";
            }
             mysqli_query($_SESSION['conn'],$sql);
        }
        //$_SESSION['note']=$sql;
    //} 
    //else
          $_SESSION['report']="Nebyla vybra";
    //if(isset($_POST['Add_song']) && !empty($_POST['kapela'])){    
    //****************Vrati ID songu
        //Pokud byla vlozena nova kapela je treba zjistit jeji ID lze toto provest
        if($_POST["kapela"]==0 && !empty($_POST["jinakapela"])){
            $sql = 'SELECT kapela_name.ID '.
                    'FROM kapela_name 
                    WHERE K_name="'.$_POST["jinakapela"].'"';
            $index=mysqli_query($_SESSION['conn'],$sql);
            $row=mysqli_fetch_array($index);
        }
        else
        {
            //Pokud jsme vybrali jiz nactenou kapelu z databaze v $_POST["kapela"] je jiz ID kapely
            $row[0]=$_POST["kapela"];//Dava primo cislo
        }   

    //********************************************
        //Vlozenni noveho songu do databaze
        if(!empty($_POST["song_name"]) && !empty($_POST["url"])){
            $sql = "INSERT INTO skladby (ID,Nazev,Kapela,ID_uzivatel,Delka,Odkaz)
                    VALUES (NULL,'".$_POST["song_name"]."','".$row[0]."','".$_SESSION["logID"]."','".minuteSecond($_POST["delka"])."','".$_POST["url"]."')";
            if(mysqli_query($_SESSION['conn'],$sql))
                $_SESSION['report']="Song byl vložen";
            else
                $_SESSION['report']=mysqli_error($_SESSION['conn']);
        }
        else
        {
            //report slouzi jako vypis informaci pro uzivatele o stavu provedene operace
            if(empty($_SESSION['report']))$_SESSION['report']="Chybí Název nebo url";
            //else $_SESSION['report']=$_SESSION['report']." Chybí Název nebo url";
            //$sql = "INSERT INTO skladby (ID,Nazev,Kapela,Delka,Odkaz)
            //  VALUES (NULL,'".$_POST["song_name"]."','".$_POST["kapela"].",'".$_POST["delka"]."',".$_POST["url"]."')";

        }   
    }
    //else
    //	$_SESSION['report']="Vyplňte všechny údaje";

    //**************************************************************************************
    //************************************EDITACE SONGU V DATABAZI**************************
    //**************************************************************************************
  if(isset($_POST['Update'])){ //kontrola jestli byl stisknut button pro editaci songu v databazi
    if(!empty($_POST["editpolekapela"])){//kontrola jestli input pro editaci kapely neni prazdny nebo smazany
        $sql ="UPDATE  kapela_name 
               Set K_name='".$_POST["editpolekapela"]."'
               where ID='".$_POST["kapela"]."'
               ";
        if(mysqli_query($_SESSION['conn'],$sql))$_SESSION['report']="Úprava byla uložena";
    } 
    else
     $_SESSION['report']=$_SESSION['report']."Pole kapely je prázdné";

     if(!empty($_POST["song_name"]) && !empty($_POST["url"])){//kontrola jestli input pro editaci jmena songu a url neni prazdny
        $sql = 'UPDATE skladby'
            . ' Set kapela='.$_POST["kapela"].','
            . ' DELKA ='.minuteSecond($_POST["delka"]).','
            . ' Odkaz ="'.$_POST["url"].'",'
            . ' Nazev ="'.$_POST["song_name"].'"'
            . ' where ID='.$_GET['getsongdetail'].' ';
        if(mysqli_query($_SESSION['conn'],$sql))$_SESSION['report']="Úprava byla uložena";
     }
    }
 //**************************************************************************************
 //************************************Odstraneni SONGU V DATABAZI***********************
 //**************************************************************************************
    if(isset($_POST['Delete_song'])){//kontrola jestli byl stisknut button pro smazini songu v databazi
       // if(!empty($_POST["song_name"]) && !empty($_POST["url"])){
              $sql = 'UPDATE skladby'
                . ' Set STAV=1 where ID='.$_GET['getsongdetail'].' ';//Smazani songu je zajisteno do tak ze stav se nerovna 0                 
              if(mysqli_query($_SESSION['conn'],$sql))$_SESSION['report']="Song byl smazán z databaze";
              else $_SESSION['report']=mysqli_error($_SESSION['conn']);
       // }
    }  
   
}
 //**************************************************************************************
 //**************************************************************************************
 //**************************************************************************************
function Edit_form($index){
//Vycita konkretni záznam z tabulek skladby a kapela_name  podle ID kapely ktere ve vstupnim parametru funkce 
$sql = 'SELECT skladby . Nazev, '
        . ' kapela_name . K_name as Kapela,'
        . ' skladby . Delka ,'
        . ' skladby . Odkaz,'
        . ' skladby . ID'
        . ' FROM skladby left join kapela_name ON ( kapela_name . ID = skladby . Kapela )';

if(!empty($index))$_SESSION['lastindex']=$index;//uklada se posledni ID songu ktery jsme si prohlizeli v detailu pro pripad reloadu stranky
else $index=$_SESSION['lastindex'];

$sql=$sql." WHERE skladby.ID=".$index." and skladby.STAV = 0";//vlozeni ID songu do sql dotazu       
if($result=mysqli_query($_SESSION['conn'],$sql))$_SESSION['report']="Problem" ;
echo"<center><form action ='main.php?form=song04&getsongdetail=".$index."' method='post'>\n\t\t\t";

echo"<table  cellpadding='2' cellspacing='2'  style='text-align:left;'>\n\t\t\t\t";
//echo"<td colspan='2'><center><button type='button' onclick='show(1)'>Zpět na výpis</button></center></td>";


//Slouzi pro prechod mezi playlistem a detailem songu
if(!empty($_GET['comeback'])){
    echo"<td colspan='2'><center><button type='button' onclick='show(1)'>Zpět na výpis</button>   ";
    echo" <button type='button' onclick='show(10)'>Zpět do playlistu</button></center></td>";
}
else
    echo"<td colspan='2'><center><button type='button' onclick='show(1)'>Zpět na výpis</button></center></td>";
//rozdelo do dalsi funkce kvuli prehlednosti
vypis_playlist(1);//funkce ktera vypise nazvy playlistu do selectu v detailu songu parametr 1 udava ze vystupu chceme jako select
//Generuje html vystup detailu songu
$odkaz;
while($row=mysqli_fetch_array($result)){
echo"
            <tr>
                <th>Název</th>
                <th><input type='text' name='song_name' size='25' value='$row[0]'></th>
            </tr>
            <tr>
                <th>Kapela</th>
                <th><select style='width: 173px;' id='selectkapela' onchange=\"editKapela(this),addKapela(this)\" name=\"kapela\" size='5'>";
                //funkce ktera vypise nazvy kapel do selectu v detailu songu
                get_items($row[1]);// parametr udava ktera kapela se ma vybrat. row[1]= nazev kapely ktery je vycten z databaze
                echo"</select></th>
                <th id=\"extend2\"></th>
            </tr>
            <tr><th>Kapela edit</th>
                <th><input type='text' id='editpolekapela'size='25' name='editpolekapela' value='$row[1]'></th>
            </tr>
            <tr>
                <th>Délka(v minutach)</th>
                <th><input type='va
                lue' name='delka' size='25' value='";secondMinute($row[2]);
                echo"'></th>
            </tr>
            
                <th>Odkaz(Youtube)</th>
                <th><input type='text' name='url' size='25' value='$row[3]'></th>
            </tr>";
            //$_SESSION['parametr']=$row[4];
            $odkaz=$row[3];
}
            echo"
            <tr>";
            $_SESSION['namepage']="song_detail"; 
            get_modul($_SESSION["namepage"],$_SESSION["loguser"],$_SESSION["heslo"]);//funkce ktera na zaklade stranky a uzivetele generuje buttony pro pripadne operace v detailu songu
            echo"</tr>"; 
   
            echo"</table></form>";    
            if (strpos($odkaz, 'https://') !== false) {
                echo"<div id='video'>" ; 
                $url = $odkaz;
                preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
                $id = $matches[1];
                $width = '500px';
                $height = '377px';
             
                echo"<iframe id=\"ytplayer\" type=\"text/html\" width=\"$width\" height=\"$height\"
                src=\"https://www.youtube.com/embed/$id?rel=0&showinfo=0&color=white&iv_load_policy=3\"
                frameborder=\"0\" onclick=\"Video();\" allowfullscreen ></iframe></center>"; 
               echo"</div>" ; 
            }
}
//**************************************************************************************
//Funkce slouzi pro vypis nazvu kapel do detailu songu 
//rozdeleno z duvodu prehlednosti
function get_items($select){

    $sql = 'Select kapela_name . ID,kapela_name . K_name'
            . ' from kapela_name order by kapela_name. K_name asc';
    $result = mysqli_query($_SESSION['conn'],$sql);
    $_SESSION['kapela']= $result;
    while($row=mysqli_fetch_array($result))
    {   
            //Podminka slouzi aby se zvyraznila moznost ktera je vyctena z databaze
            if($row[1]==$select | $row[0]==$select )echo"<option value=\"".$row[0]."\" selected style=\"width: 100%;\">$row[1]</option>\n\t\t\t\t"; //do value se uklada ID kapely
            else echo"<option value=\"".$row[0]."\">$row[1]</option>\n\t\t\t\t"; 
    }
    echo"<option value=\"0\">Jiný</option>\n\t\t\t\t"; 
    
}

//**************************************************************************************
function Count_record($Tabulka,$filtr,$vypis){
    if(empty($filtr))
        $sql = 'SELECT COUNT(*) AS count_record FROM '.$Tabulka.' where STAV=0'; 
    else
       //$sql = 'SELECT COUNT(*) AS count_record FROM '.$Tabulka.' where '.$filtr.'STAV=0';
       $sql = 'SELECT COUNT(*) AS count_record FROM  skladby left join (kapela_name,user) ON 
             ( kapela_name . ID = skladby.Kapela and skladby.ID_uzivatel=user.ID )
         where '.$filtr.' skladby.STAV=0';  
    
    $pocet = mysqli_query($_SESSION['conn'],$sql);//hodnota poctu zaznam; z databaze
    $pocet=mysqli_fetch_array($pocet);
    //Vypocet poctu stranek
    $pocetstranek=ceil($pocet[0]/$_SESSION['lastnumberzobraz']);

    if(!empty($_GET['sidenumber'])){//cislo stranky
        $_SESSION['sidenumber']=$_GET['sidenumber'];
        if($pocetstranek==0)$_SESSION['limitmin']=0;
        else
        {   
            $_SESSION['limitmin']=($_SESSION['lastnumberzobraz']* $_SESSION['sidenumber'])-$_SESSION['lastnumberzobraz'];//Vypocete prvniho vybraneho zaznamu z database
        }
    }
    else{
        if(empty($_SESSION['limitmin']))
            $_SESSION['limitmin']=0;//Pokud nebylo stisknuto tlacitko pro vyber stranky jsme na prvni strance a limit je 0
    }
    if($vypis==0)return $pocet[0];//vrati pocet zaznamu v tabulce skladeb
    if($vypis==1){//vypise cisla stranek
        echo "<table><tr><td style=\"border:none\">Stránka:</td>";
        for($i=1;$i<=$pocetstranek;$i++){
            //if(!empty($_GET['sidenumber'])){
                if($i==$_SESSION['sidenumber'])//slouzi pro zvýrazneni aktualne prohlžené stránky
                    echo "<th><a class=\"selected_side\" href=\"?sidenumber=".$i."\"> <span>".$i."</span></a></th>";
                    //echo "<th><div class=\"selected_side\" onclick=\"loadD_new_records(".$i.")\"> <span>".$i."</span></div></th>";
               else    
                echo "<th><a class=\"unselected_side\" href=\"?sidenumber=".$i."\"> <span>".$i."</span></a></th>";
                // echo "<th><div class=\"unselected_side\" onclick=\"loadD_new_records(".$i.")\"> <span>".$i."</span></div></th>";
        }
        echo "</tr></table>";
    }     
} 
//*************************************************************************************
function secondMinute($seconds){
     /// get minutes
    $minResult = floor($seconds/60);
    
    /// if minutes is between 0-9, add a "0" --> 00-09
    if($minResult < 10){$minResult = 0 . $minResult;}
    
    /// get sec
    $secResult = ($seconds/60 - $minResult)*60;
    
    /// if secondes is between 0-9, add a "0" --> 00-09
    if($secResult < 10){$secResult = 0 . $secResult;}
    
    /// return result
    echo $minResult,":",$secResult;
}

function minuteSecond($minute){
    $time = $minute;
    $parsed = date_parse($time);
    //$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
    $seconds = $parsed['hour'] * 60 + $parsed['minute'];
    return $seconds;
}  
?>