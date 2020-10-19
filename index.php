<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Datos de guardado</title>
<style>
body {
  background-color: black;
  color: white;
}
</style>
</head>

<?php
$id = ""; $user = ""; $Backup = 0; $lastid = ""; $lastuser = "";

if(isset($_GET["User"])){
  $id=$_GET["User"];
}else{
  return;
}
if($id == ""){
return;
}

include 'db.php';
$conn = conectar();
include 'saves.php';
include 'create.php';

$sql = "SELECT  User, AlreadyBackup, steamID FROM users WHERE steamID = '$id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $id = $row['steamID'];
    $user = $row['User'];
    $Backup = $row['AlreadyBackup'];
    @Updateavatar($id, $user);
  }
} else {
  $created = CreateDN();
  if($created == true){
    header("Location: index.php?User=$id");
    exit;
  }
  else{
    return;
  }
}
$sql = "SELECT game, dir FROM games";
$result = $conn->query($sql);
$gamesarray = array();
    while($row = $result->fetch_assoc()) {
        $gamesarray[]=array($row["game"],$row["dir"].'/');
    }

$sql = "SELECT  User, steamID FROM users WHERE AlreadyBackup = '0'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $lastid = $row['steamID'];
    $lastuser = $row['User'];
  }
}

echo $lastid.' - '.$id."<br>";
if($lastid != $id && $lastid != ""){
  UpdateBackup(1,$lastid);
  if($lastuser != "BackupAll"){
    foreach($gamesarray as $game){
      DNsaves($lastid,$lastuser,$game[0],$game[1],"DN");
    }
    echo $lastuser."DN";
  }
}


if($Backup == 1 && $user != "BackupAll"){
  foreach($gamesarray as $game){
    DNsaves($id,$user,$game[0],$game[1],"Game");
  }
  echo $user."Game";
}
elseif($user != "BackupAll"){
  foreach($gamesarray as $game){
    DNsaves($id,$user,$game[0],$game[1],"ClosedGame");
  }
  echo $user."Closed";
}

UpdateBackup(0,$id);