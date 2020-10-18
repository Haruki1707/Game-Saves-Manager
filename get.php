<?php
if(isset($_GET["data"])){
    $data=$_GET["data"];
}else{
    $data=false;
}

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

    case 'user':
        $sql = "SELECT User FROM users WHERE AlreadyBackup = 0";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            echo $row["User"];
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

    default:
        if(file_exists("Icons/$data.jpg"))
            echo "Sincronizado";
        else
            echo 'Select correct data to request';
        break;
}

?>