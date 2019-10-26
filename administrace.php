<?php
function registrace(){
    $sql = "INSERT INTO user 
            (Jmeno,
            Prijmeni,
            Email,
            Login,
            Heslo,
            Stav)
            VALUES ('".$_POST["Jmeno"]."',
                '".$_POST["Prijmeni"]."',
                '".$_POST["Email"]."',
                '".$_POST["Login"]."',
                '".$_POST["Heslo"]."',
                '0')";
    $result = mysqli_query($_SESSION['conn'],$sql);//pridani noveho uzivatele 
    
    $sql = 'SELECT user.ID '
        . ' FROM user '
        . ' WHERE user.Login="'.$_POST["Login"].'"';          
    //Ziskani ID noveho uzivatele        
    $result2=mysqli_query($_SESSION['conn'],$sql);
    $ID=$_SESSION['conn']->insert_id;
    $ID=mysqli_fetch_array($result2);

    //Pridani ID noveho uzivatele a prideleni role 
    $sql = "INSERT INTO role_user (ID_user,ID_role)
            VALUES ('".$ID[0]."','1');";
    if($result2=mysqli_query($_SESSION['conn'],$sql))
    
    {
        $_SESSION['report']="Registrace proběhla úspěšně";
    }
    else
    {
        $_SESSION['note']=mysqli_error($_SESSION['conn']);
        $_SESSION['note']="Uzivatel jiz existuje";
        //if(strrpos($_SESSION['report'],"Duplicate",0)>0)
           // $_SESSION['report']="Uzivatel s timto Loginem nebo heslem jiz existuje";
        //else
        //$_SESSION['note']="Jiz existuje";  
    }
   }  
//**********************************************************************************
function vypis_uzivatelu($dotaz,$lowlimit,$Maxlimit,$druh){
//function edit_user($dotaz,$lowlimit,$Maxlimit){
//$druh nabyva hodnot 0 vypisuje se na stranku vypis uzivatelu jako tabulka
//                    1 vypisuje se v karte vyhledavani jako rolovaci seznam 
    if(empty($dotaz)){
         $sql = 'SELECT '
        . ' role . Role_name , '
        . ' user . Jmeno , '
        . ' user . Prijmeni , '
        . ' user . email , '
        . ' user . Login , '
        . ' user . Heslo , '
        . ' user . STAV, '
        . ' user . ID, '
        . ' role . ID, '
        . ' user . STAV '
        . ' FROM role_user left join ( role , user ) '
        . ' on ( role_user . ID_user = user . ID and role_user . ID_role = role . ID )';
    }
    else{
        $sql=$dotaz;
    }
    //*************************Serazeni  ********************************
    if(!empty($_GET['userasc']))$asc='asc';
    else $asc='desc'; 

    if(!empty($_GET['userorder']))  $sql=$sql." ORDER BY ".$_GET['userorder']." ".$asc;
    //*************************Nastaveni limit********************************
    if($lowlimit>0 | $Maxlimit>0){
        if($Maxlimit>$lowlimit) $sql=$sql." Limit ".$lowlimit.",".$Maxlimit;
        else $sql=$sql." Limit ".$Maxlimit.",".$lowlimit;
    }
    //$_SESSION['filtr']=$sql;
    //}
    $result = mysqli_query($_SESSION['conn'],$sql);
    //echo"<form action ='main.php' method='post'>\n\t\t\t
    if(!$druh){
        echo"<table  cellpadding='2' cellspacing='2' frame='bellow' style='text-align:center' size='1'>\n\t\t\t";
        echo "<tr style='background:white;'>\n\t\t\t\t";
   
        for($i=0;$i<$result->field_count-3;$i++){
            if(!empty($_GET['userasc']))
                echo "<th>
                <a class=\"order\" href=\"?form=song20&cl=2&userorder=".$result->fetch_field_direct($i)->name."&userasc=0\">".$result->fetch_field_direct($i)->name."
                </a></th>\n\t\t\t";
            else 
                echo "<th><a class=\"order\" href=\"?form=song20&cl=2&userorder=".$result->fetch_field_direct($i)->name."&userasc=1\">".$result->fetch_field_direct($i)->name."</a></th>\n\t\t\t\t";   
            }
        echo "</tr>";
        while($row=mysqli_fetch_array($result)) 
        {
            //$radek=$row[0];echo"<tr>";
            echo"\n\t\t\t<form action ='main.php?form=song20&I1=$row[7]' method='post'>\n\t\t\t<tr>";
            if($row[9]>0)echo"<tr style='background:red'>";
            else echo"<tr style='background:green'>";
            get_role($row[8]);
            echo "<td>\n\t\t\t<input name='E_jmeno' value='".$row[1]."'maxlength='30' size='15'></td>";
                if($row[2]==NULL | $row[2]=="") echo"<td>-</td>";
                else echo"<td><input name='E_prijmeni' value='".$row[2]."'maxlength='15' size='9'></td>";
                echo"<td><input name='E_email' value='".$row[3]."'maxlength='30' size='15'></td>
                <td><input name='E_login' value='".$row[4]."' maxlength='15' size='4'></td>
                <td><input name='E_heslo' value='".$row[5]."' maxlength='30' size='4'></td>
                <td><input name='E_stav' value='".$row[6]."' maxlength='1' size='4'></td>
                <td><button type='submit' name='".$row[7]."'>Save</button></td>    
            </tr></form>";

        }
        echo"</table>";
    }
    else
     {
        echo"<select name=\"user_select_filtr\" size='1'>";
        echo"<option value=\"0\"></option>\n\t\t\t\t";
        while($row=mysqli_fetch_array($result))
        {
          echo"<option value=\"".$row[7]."\">$row[4]</option>\n\t\t\t\t";
        }
        echo"</select>"; 
     }
}
//******************************************************************************************
function vypis_roli(){
 //slouzi pro vypis informaci o uzivatelich   
    $sql = 'select role .Role_name , 
                   modul . Modul,
                   pravo.ID as Změna'
        . ' from pravo left join ( modul , role ) 
            on '. ' ( pravo . Role = role . ID and pravo . Modul = modul . ID ) LIMIT 0, 30 ';

    if($result = mysqli_query($_SESSION['conn'],$sql)){
        echo"<form action ='main.php?form=song20' method='post'>";
        echo"<table  cellpadding='2' cellspacing='2' frame='bellow' style='text-align:center' size='1'>";
        echo "<tr>";
        for($i=0;$i<$result->field_count-1;$i++){
            echo"<th>".$result->fetch_field_direct($i)->name."</th>";
        }
        echo "</tr>";
        
        while($row=mysqli_fetch_array($result)){
            //echo"<form action ='main.php?form=song20&deletepravo=$row[2]' method='post'>";
            echo"<tr>
                <td>".$row[0]."</td>
                <td>".$row[1]."</td>
                
                </tr>";
           // echo"</form>";<td><button type='submit' name='role_odstranit'>Odstranit</button></td>
        }
        echo"</table>";
        echo"</form>";
    }
}
//******************************************************************************************
 function get_role($selected){
  $sql = 'Select role.ID,'
        . ' role.Role_name '
        .  ' From role';
  if($result = mysqli_query($_SESSION['conn'],$sql)){
  //$_SESSION['kapela']= $result;
      echo"<td>";
      echo"<select name=\"role_select\" maxlength='15' size='1'>";
      while($row=mysqli_fetch_array($result))
      {
        if($row[0]==$selected)echo"<option value=\"".$row[0]."\" selected>$row[1]</option>\n\t\t\t\t";
        else
        echo"<option name='E_role' value=\"".$row[0]."\">$row[1]</option>\n\t\t\t\t";
      }
      echo"</select>"; 
      echo"</td>";
  }      
} 

//**********************************************************************************         
 function edit_uzivatele($IDuser){
    $sql = 'UPDATE user '
           .' SET Jmeno ="'.$_POST['E_jmeno'].'",'
                 .'Prijmeni ="'.$_POST['E_prijmeni'].'",'
                 .'Email ="'.$_POST['E_email'].'",'
                 .'Login = "'.$_POST['E_login'].'",'
                 .'Heslo ="'.$_POST['E_heslo'].'",'
                 .'Stav ="'.$_POST['E_stav'].'"'
           .' WHERE  user.ID="'.$IDuser.'" ';     
    $result = mysqli_query($_SESSION['conn'],$sql);           
    $sql = 'UPDATE role_user'
            .' SET ID_role="'.$_POST['role_select'].'"'
            .' WHERE ID_user="'.$IDuser.'"';   
    $result = mysqli_query($_SESSION['conn'],$sql);     

}

//*************************************************************************************
function get_modul($stranka,$user,$heslo){
//prikaz vybere na zaklade login,hesla a stranky na ktere jsme co se ma zobrazit
    /*$sql = 'select modul.Modul'
        . ' from pravo left join (modul)'
        . ' on(pravo.modul = modul.ID) '
        . ' where (pravo.Role=(select role_user.ID_role'
        . ' from role_user left join (user)'
        . ' on(role_user.ID_user =user.ID ) '
        . ' where (user.Login="Tomas" and user.Heslo="148"))and modul.Page="song_detail")';
        */
    $sql = 'select modul.Modul,'.'modul.number'
        . ' from pravo left join (modul)'
        . ' on(pravo.modul = modul.ID) '
        . ' where (pravo.Role=(select role_user.ID_role'
        . ' from role_user left join (user)'
        . ' on(role_user.ID_user =user.ID ) '
        . ' where (user.Login="'.$user.'" and user.Heslo="'.$heslo.'")) and modul.Page ="' .$stranka.'")';
           //cislo modulu    
    if($result2 = mysqli_query($_SESSION['conn'],$sql)){
        $pocet=0; 
        while($row=mysqli_fetch_array($result2)){
        $pocet++;
         if($pocet>2)echo"<tr>" ;   
            if($row[1]){
                echo"<td id=\"modul\"><center><button type='button' name=".$row[0]."  onclick=show(".$row[1].")>".$row[0]."</button></center></td>\n\t\t\t\t";
            }
            else{
                echo"<td id=\"modul\"><center><button type='submit' name=\"".$row[0]."\">".$row[0]."</button></center></td>\n\t\t\t\t";               
            }
            if($pocet>1){echo"</tr>";$pocet=0;} 
        }
    }
}
//***********************************************************************************
?>