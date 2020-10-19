<head>
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class='fix mbar'>
<?php
include "db.php";
$conn = conectar();

if(isset($_GET["option"])){
    $option=$_GET["option"];
}else{
    header('Location: UI.php');
}

$bar = "
<div class='bar'>
<a href='UI.php'>
    <img src='Icons/back.png' class='cback'>
</a>";

$bar1 = "
<div class='bar'>
<a href='config.php?option=games'>
    <img src='Icons/back.png' class='cback'>
</a>";

$bar2 = "</div>";

switch($option){
    case "user":
        echo $bar."<span class='textbar'>User data</span>".$bar2;

        $sql = "SELECT data, value FROM data ORDER BY data ASC";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $userdata[] = array($row["data"],$row["value"]);
        }
        echo "<form action='config.php?option=userdb' method='POST'>";
        foreach ($userdata as $dat) {
            echo "<label for='$dat[0]' class='utype'>$dat[0]</label>";
            echo "<input name='$dat[0]' id='$dat[0]' type='text' class='uvalue' value='$dat[1]' autocomplete='off'>";
        }
        echo "<input type='submit' value='save' class='submit'>";
        echo "</form>";
        break;
    case "games":
        echo $bar."<span class='textbar'>Game list</span>".$bar2;

        $games[] = array("0","++++++<br>Introduce a new game!<br>++++++","newgame");

        $sql = "SELECT ID, game, process FROM games ORDER BY game ASC";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $games[] = array($row["ID"],$row["game"], $row["process"]);
        }

        echo "<div class='bodygame'>";

        foreach ($games as $game) {
            $md5 = md5_file("Icons/$game[2].jpg");
            echo "
            <a href='config.php?option=game&id=$game[0]' class='games'>
                <span class='gamebg'>
                    <span class='gamename'>$game[1]</span>
                </span>
                    <img src='Icons/$game[2].jpg?$md5'>
             </a>";
        }
        echo "</div>";
        break;
    case "game":
        $id = $_GET["id"];
        $game = "";
        $dir = "";
        $process = "";

        echo $bar1."<span class='textbar'>Game list</span>".$bar2;
        echo "<form action='config.php?option=gamesdb&id=$id' method='POST' enctype='multipart/form-data'>";

        $sql = "SELECT game, dir, process FROM games WHERE id =$id";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $game = $row["game"];
            $dir = $row["dir"];
            $process = $row["process"];
        }

        if ($id == 0)
            $img = "nologo";
        else
            $img = $process;
        
        $md5 = md5_file("Icons/$img.jpg");

        echo "<span class='editimgcontainer'>
        <img src='Icons/$img.jpg?$md5' class='editimg' id='acIMG'>";
        echo "<input type='file' name='gameIMG' id='gameIMG' accept='image/*' onchange='loadFile(event)'>
        <label for='gameIMG' class='gameeditbg'>
            <label for='gameIMG' id='gameIMGlab'>&#x270E;</label>
        </label>

        </span>";

        echo "<label for='game' class='utype'>Game name</label>";
        echo "<input name='game' id='game' type='text' class='uvalue' value=".'"'.$game.'"'." autocomplete='off'>";

        echo "<label for='dir' class='utype'>Save's dir</label>";
        echo "<input name='dir' id='dir' type='text' class='uvalue' value='$dir' autocomplete='off'>";

        echo "<label for='process' class='utype'>Process name</label>";
        echo "<input name='process' id='process' type='text' class='uvalue' value='$process' autocomplete='off'>";

        echo "<input type='submit' value='save' class='submit'>";
        echo "</form>";
        break;
    case "userdb":
        $sql = "SELECT data FROM data";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $id[] = $row["data"];
        }

        foreach ($id as $idvalue) {
            $sql = "UPDATE data SET value='".$_POST["$idvalue"]."' WHERE data='".$idvalue."'";
            if ($conn->query($sql) === FALSE)
                echo "Error updating record: " . $conn->error;
        }

        header('Location: UI.php');
        break;
    case "gamesdb":
        include "saves.php";

        $id = $_GET["id"];
        $game = $_POST["game"];
        $dir = $_POST["dir"];
        $process = $_POST["process"];
        $img = "Icons/$process.jpg";

        if(strpos($game, "'"))
            $game = str_replace("'", "\'", $game);
        if(strpos($dir, "\\"))
            $dir = str_replace("\\", "/", $dir);

        $shortcutes = shortcutes();

        foreach ($shortcutes as $short) {
            if(strpos($dir, $short[0]))
                $dir = str_replace($short[0], $short[1], $dir);
        }

        if($id != 0){
            $sql = "SELECT process FROM games WHERE id =$id";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()) {
                $dbprocess = $row["process"];
                $dbgame = $row["game"];
            }
            if($dbprocess != $process)
                rename("Icons/$dbprocess.jpg", $img);
            
            $sql = "UPDATE games SET game='$game', dir='$dir', process='$process' WHERE id=$id";
            echo $sql;
            if ($conn->query($sql) === FALSE)
                echo "Error updating record: " . $conn->error;
        }
        else{
            $sql = "INSERT INTO games (game,dir,process) VALUES ('$game','$dir','$process')";
            $conn->query($sql);
        }

        $check = @getimagesize($_FILES["gameIMG"]["tmp_name"]);
        if($check == false) {
            if (file_exists($img) == false) {
               copy("Icons/nologo.jpg",$img);
            }
        }
        else{
            $image = $_FILES["gameIMG"]["tmp_name"];

            $src_img=imagecreatefromstring(file_get_contents($image));

            $src_width = imagesx($src_img); //width of initial image
            $src_height = imagesy($src_img); //height of initial image

            $height = floor($src_width / 2.1395348837209302325581395348837);
            $src_x = $dst_x = $dst_y = 0;
            $src_y  = floor($src_height - $height)/2;

            $dst_width = $src_width;// width of new image
            $dst_height = $height + $src_y*2; //height of new image

            $dst_img = imagecreatetruecolor($src_width, $height);

            imagecopyresampled($dst_img, $src_img, 
                $dst_x ,$dst_y, $src_x, $src_y, 
                $dst_width, $dst_height, $src_width, $src_height);

            imagejpeg($dst_img, $img);
        }
        header('Location: config.php?option=games');
        break;
    case 'errors':
        echo "<label class='utype uerror uhead'>----User----</label>";
        echo "<label class='utype uerror uhead'>----Game----</label>";
        echo "<label class='utype uerror uhead'>----Error----</label>";
        echo "<br>";

        $sql = "SELECT user, game, error FROM errors";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $errors[] = array($row["user"], $row["game"], $row["error"]);
        }

        foreach ($errors as $error) {
            echo "<label class='utype uerror'>".$error['0']."</label>";
            echo "<label class='utype uerror'>".$error['1']."</label>";
            echo "<label class='utype uerror'>".$error['2']."</label>";
            echo "<br>";
        }
        
        echo "<a href='config.php?option=truncate'><button class='submit suberrors'>Erase errors</button></a>";
        break;
    case 'truncate':
        $sql = "TRUNCATE TABLE errors";
            echo $sql;
            if ($conn->query($sql) === FALSE)
                echo "Error updating record: " . $conn->error;

        header('Location: UI.php');
        break;
    default:
        header('Location: UI.php');
        break;
}

?>
</body>
<script>
  var loadFile = function(event) {
    var output = document.getElementById('acIMG');
    output.src = URL.createObjectURL(event.target.files[0]);
  };
</script>