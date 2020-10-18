<?php
$userdata = @file_get_contents("https://playerdb.co/api/player/steam/".$id);
$data = json_decode($userdata, true);

$dbid = "Empty";
$sql = "SELECT steamID FROM users WHERE steamID = '$id'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    $dbid = $row['steamID'];
}

$continue = false;
if($userdata != false && @$data['code'] != "player.found" && $dbid != "Empty"){
    sqlerror($id,"Player not founded");
    $continue = true;
}

function Getdata($option){
    global $data;

    switch($option){
        case "ID":
            return $data['data']['player']['id'];
        break;
        case "avatar":
            return $data['data']['player']['avatar'];
        break;
        case "avatarMD5":
            $hash = file_get_contents($data['data']['player']['avatar']);
            $hash = md5($hash);
            return $hash;
        break;
        case "username":
            $username = $data['data']['player']['username'];
            return substr($username,0,6);
        break;
    }

}

function CreateDN(){
    global $continue;
    if($continue == false)
        return false;
        
    $steamID= Getdata("ID");    
    $User = Getdata("username");
    
    $conn = conectar();
    $sql = "INSERT INTO users (User,AlreadyBackup,steamID) VALUES ('$User','1','$steamID')";
    $conn->query($sql);

    $avatar = Getdata("avatar");
    $avatar = file_get_contents($avatar);
    $dir = "Users/$steamID - $User";
    if(!is_dir($dir) && !mkdir($dir, 0777, true)) {
        die('<span class="red">Fallo al crear las carpetas...</span>');
        return false;
    }
    file_put_contents($dir."/".$steamID.".jpg",$avatar);
    return true;
}

function Updateavatar ($id, $user){
    $local = md5_file("Users/$id - $user/$id.jpg");
    $steam = Getdata("avatarMD5");

    if($local != $steam){
        $avatar = Getdata("avatar");
        $avatar = file_get_contents($avatar);
        $dir = "Users/$id - $user";
        file_put_contents($dir."/".$id.".jpg",$avatar);
    }
}

function sqlerror($User, $Error){
    $conn = conectar();
    $sql = 'INSERT INTO errors (user,Game,error) VALUES ("'.$User.'","'.$Error.'","by STEAMID64")';
    $conn->query($sql);
}
?>