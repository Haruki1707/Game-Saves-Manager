<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      Datos de guardado
    </title>
  <style>
    body {
      background-color: black;
      color: white;
    }
  </style>
</head>

<?php
  $id = "";
  $user = "";
  $Backup = 0;
  $lastid = "";
  $lastuser = "";
  $process = null;

  if(isset($_GET["User"]))
    $id=$_GET["User"];
  else
    return;
  
  if(isset($_GET["process"]))
    $process = $_GET["process"];
  
  if($id == "")
    return;

  include 'db.php';
  $conn = conectar();
  include 'saves.php';
  include 'SteamUser.php';

  $sql = "SELECT  User, AlreadyBackup, steamID FROM users WHERE steamID = '$id'";
  $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $id = $row['steamID'];
        $user = $row['User'];
        $Backup = $row['AlreadyBackup'];
        $steamuser =  new SteamUser($id);
        $steamuser->Updateavatar($user);
      }
    }
    else {
      $steamuser = new SteamUser($id);
      $created = $steamuser->CreateDN();
      if($created == true){
        header("Location: index.php?User=$id");
        exit;
      }
      else
        return;
    }

  $games = array();

  $sql = "SELECT game, dir, process FROM games";
  $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $games[] = new Game($row["game"],$row["dir"].'/', $row["process"]);
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
      foreach($games as $game){
        $game->game_to_DN_saves($lastid, $lastuser);
      }
      echo $lastuser."DN<br>";
    }
  }


  if($Backup == 1 && $user != "BackupAll"){
    foreach($games as $game){
      $game->DN_to_game_saves($id, $user);
    }
    echo $user."Game<br>";
  }
  elseif($user != "BackupAll"){
    foreach($games as $game){
      $game->closed_game_check_saves($id, $user, $process);
    }
    echo $user."Closed";
  }

UpdateBackup(0,$id);