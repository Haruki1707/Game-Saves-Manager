<?php
class SteamUser{
    private $data = null;
    private $avatar = null;
    private $hash = null;

    public function SteamUser($id){
        $content = @file_get_contents("https://playerdb.co/api/player/steam/".$id);
        $data = json_decode($content, true);

        if($content != false && @$data['code'] == "player.found"){
            $this->data = $data;
            $this->avatar = file_get_contents($data['data']['player']['avatar']);
            $this->hash = md5($this->avatar);
        }
        else
            sqlerror($id,"Player not founded");
    }

    private function Getdata($option){
        $data = $this->data;
    
        switch($option){
            case "ID":
                return $data['data']['player']['id'];
            break;
            case "avatar":
                return $this->avatar;
            break;
            case "avatarMD5":
                return $this->hash;
            break;
            case "username":
                $username = $data['data']['player']['username'];
                return substr($username,0,6);
            break;
        }
    }

    public function CreateDN(){
        if($this->data != null)
            return false;
            
        $steamID= $this->Getdata("ID");    
        $User = $this->Getdata("username");
        
        $conn = conectar();
        $sql = "INSERT INTO users (User,AlreadyBackup,steamID) VALUES ('$User','1','$steamID')";
        $conn->query($sql);
    
        $avatar = $this->Getdata("avatar");
        $dir = "Users/$steamID - $User";
        if(!is_dir($dir) && !mkdir($dir, 0777, true)) {
            die('<span class="red">Fallo al crear las carpetas...</span>');
            return false;
        }
        file_put_contents($dir."/".$steamID.".jpg",$avatar);
        return true;
    }

    public function Updateavatar ($user){
        $id = $this->Getdata("ID");

        $local = @md5_file("Users/$id - $user/$id.jpg");
        $steam = $this->Getdata("avatarMD5");
    
        if($local != $steam){
            $avatar = $this->Getdata("avatar");
            @file_put_contents("Users/$id - $user/".$id.".jpg",$avatar);
        }
    }
}

function sqlerror($User, $Error){
    $conn = conectar();
    $sql = 'INSERT INTO errors (user,Game,error) VALUES ("'.$User.'","by STEAMID64","'.$Error.'")';
    $conn->query($sql);
}
?>