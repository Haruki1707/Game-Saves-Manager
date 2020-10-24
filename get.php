<?php
if(isset($_GET["data"]))
    $data=$_GET["data"];
else
    $data=false;

if(isset($_GET{"image"}))
    $image = $_GET["image"];

include 'db.php';
$conn = conectar();

switch ($data) {
    case 'errors':
        $sql = "SELECT error FROM errors";
        $result = $conn->query($sql);
        $count = 0;
        while($row = $result->fetch_assoc()) {
            $count++;
        }
    break;

    case 'games':
        $lastposition = -1;

        $sql = "SELECT process FROM games";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $lastposition++;
            $games[] = $row["process"];
        }
        foreach ($games as $game) {
            echo $game;
            if($game != $games[$lastposition])
                echo "|";
        }  
    break;

    case 'Box':
        $sql = "SELECT value FROM data WHERE data = 'box_seconds'";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $seconds = $row["value"];
        }

        echo $seconds;
    break;

    case 'Notification':
        $sql = "SELECT value FROM data WHERE data = 'notify_seconds'";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $seconds = $row["value"];
        }

        echo $seconds;
    break;

    case 'Min':
        $sql = "SELECT value FROM data WHERE data = 'game_check/min'";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $min = $row["value"];
        }
    
        echo $min;
    break;

    case "image":
        $file_out = "Icons/$image.jpg";

        if (!file_exists($file_out)) {
            $sql = "SELECT User, steamID FROM users WHERE AlreadyBackup = 0";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                $user = $row["User"];
                $ID = $row["steamID"];
            }
            $file_out = "Users/$ID - $user/$ID.jpg";
        }

        $image_info = getimagesize($file_out);

        //Set the content-type header as appropriate
        header('Content-Type: ' . $image_info['mime']);

        //Write the image bytes to the client
        echo file_get_contents($file_out);
        return;
    break;

    case "check":
        if($image == "user"){
            $sql = "SELECT User FROM users WHERE AlreadyBackup = 0";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                echo $row["User"];
            }
        }
        elseif (file_exists("Icons/$image.jpg")) {
            echo "Sincronizado";
        }
        else
            echo "!Exist";
    break;
    
    default:
        echo 'Select correct data to request';
    break;
}

?>