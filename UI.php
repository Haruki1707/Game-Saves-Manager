<?php
include 'db.php';
$conn = conectar();

if(isset($_GET["option"])){
    $Option=$_GET["option"];
}else{
    $Option = false;
}

if (isset($_GET["data"])) {
    $data=$_GET["data"];
}else{
    $data = false;
}

switch ($Option) {
    case "image":
        $file_out = "Icons/$data.jpg";
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

    case false:
        echo "<head>
        <link rel='stylesheet' href='style.css'>
        </head>\n";
        echo "<body class='fix'>";
        echo "\n<div class='cbox'>";
        echo "
        <a href='config.php?option=user' class='option'>
            <img src='Icons/config.png' class='clogo'>
            <span class='grey'>User data</span>
        </a>    
        ";
        echo "</div>";
        echo "\n<div class='cbox'>";
            echo "
            <a href='config.php?option=games' class='option'>
                <img src='Icons/gamelist.png' class='clogo'>
                <span class='grey'>Game list</span>
            </a>    
            ";
        echo "</div>";
        return;
        break;

    default:
        break;
}
?>