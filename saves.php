<?php
function values($data){
    $sql = "SELECT value FROM data WHERE data = $data";
    global $conn;
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $optiondata = $row['value'];
    }
return $optiondata;
}

function shortcutes(){
    global $optiondata;

    $array_short[] = array("Users/".values("pc_user")."/AppData/Local", "%appdata%");
    $array_short[] = array("Program Files (x86)/Ubisoft/Ubisoft Game Launcher/savegames/".values("uplay_id"), "%uplay%");
    
    return $array_short;
}

function DNsaves($id,$User,$Game,$route,$Option){    
    $Copyver = true;
    $Erasever = true;

    $shortcutes = shortcutes();
    
    foreach ($shortcutes as $short) {
        if(strpos($route, $short[1]))
            $route = str_replace($short[1], $short[0], $route);
    }
    echo "$route <br>";
    if(!file_exists($route))
        return;
    
    $DNsaves='Users/'.$id.' - '.$User.'/'.$Game.'/';
    $DNbackup = 'Users/Erased saves/'.$id.' - '.$User.'/'.$Game.'/';
    $GamesavesCheck = glob($route."*.*");
    $DNsavesCheck = glob($DNsaves."*.*");
    
    if(Count($GamesavesCheck) > 0){
        if(!is_dir($DNsaves) && !mkdir($DNsaves, 0777, true)) {
            Error($User, $Game, "Fallo al crear las carpetas");
            die('<span class="red">Fallo al crear las carpetas...</span>');
            return;
        }
    }
    
    if ($Option != "Game") {
        foreach ($DNsavesCheck as $DNsave) {
            $Comparision= str_replace($DNsaves, $route, $DNsave);
            if(!file_exists($Comparision)){
                if(!is_dir($DNbackup) && !mkdir($DNbackup, 0777, true)) {
                    Error($User, $Game, "Fallo al crear las carpetas de Backup");
                    die('<span class="red">Fallo al crear las carpetas de backup...</span>'.$User);
                    return;
                }
                $Save_Backup= str_replace($DNsaves, $DNbackup, $DNsave);
                rename($DNsave, $Save_Backup);
            }
        }
    }

    switch ($Option) {
        //A la nube, eliminando archivos del juego
        case "DN":
            foreach ($GamesavesCheck as $Save){
                $Save_cloud= str_replace($route, $DNsaves, $Save);
                if(file_exists($Save_cloud)){
                    if(md5_file($Save) != md5_file($Save_cloud)){
                        rename($Save, $Save_cloud);
                    }
                    else {
                        unlink($Save);
                    }
                }
                else{
                    rename($Save, $Save_cloud);
                }
        
                if(file_exists($Save))
                    $Erasever = false;
                if(!file_exists($Save_cloud))
                    $Copyver = false;
            }
        
            if($Erasever == false){
                Error($User, $Game, "Saves no tachados");
                echo "<b>".$Game."</b> - <span class='red'>Saves no tachados</span><br>";
            }
        
            if ($Copyver == false){
                Error($User, $Game, "Error al sincronizar Saves");
                echo "<b>".$Game."</b> - <span class='red'>Error al sincronizar Saves</span><br>";
            }   
            break;
        //Al juego, copiando de la nube
        case "Game":
            foreach ($DNsavesCheck as $Save){
                //Primer parametro: Cosa a quitar; Segundo: Cosa a poner; Tercero: A que quitar y poner cosa
                $CopytoGame= str_replace($DNsaves, $route, $Save);
                copy($Save, $CopytoGame);
        
                if(!file_exists($CopytoGame))
                    $Copyver = false;
            }
        
            if ($Copyver == false){
                Error($User, $Game, "Error al recuperar Saves");
                echo "<b>".$Game."</b> - <span class='red'>Error al recuperar Saves</span><br>";
            }   
            break;
        //A la nube cerrar el juego, sin eliminar archivos
        case "ClosedGame":
            echo "$Game<br>";
            foreach ($GamesavesCheck as $Save){
                $Save_cloud= str_replace($route, $DNsaves, $Save);
                if(file_exists($Save_cloud)){
                    if(md5_file($Save) != md5_file($Save_cloud)){
                        copy($Save, $Save_cloud);
                    }
                }
                else{
                    copy($Save, $Save_cloud);
                }
        
                if(!file_exists($Save_cloud))
                    $Copyver = false;
            }
            if ($Copyver == false){
                Error($User, $Game, "Error al copiar ClosedGame");
            }  
            break;
        default:
            echo "Elige una opcion posible";
            break;
    }     
}

function Error($User, $Game, $Error){
    $conn = conectar();
    $sql = 'INSERT INTO errors (user,Game,error) VALUES ("'.$User.'","'.$Game.'","'.$Error.'")';
    $conn->query($sql);
}

function UpdateBackup($change, $id){
    $conn = conectar();
    $sql = "UPDATE users SET AlreadyBackup=$change WHERE steamID=".$id;
    if ($conn->query($sql) === FALSE)
        echo "Error updating record: " . $conn->error;
  }
?>