<?php
$numero = 0;

if(isset($_REQUEST["cap"])){
    setcookie("Capitulo", $_REQUEST["cap"], time() + (86400 * 30), "/");
    $numero = $_REQUEST["cap"];
    $nose = "http://aracelynovelas.blogspot.com/2017/10/las-vias-del-amor-capitulo-$numero.html";
    echo "<script>setTimeout(getback, 1);
        function getback() {
            window.location.href = '$nose';
    }
    </script>";
}

if(isset($_COOKIE["Capitulo"])) {
    $numero = $_COOKIE["Capitulo"];
}

$numero++;

    $string = "
    <body>
    <center>
    <form method='post' action='test.php' style='font-size: 150px'>
    <br><br>Numero capitulo:<br><br><input type='number' name='cap' value='$numero' style='font-size: 150px; width:100%; background-color: grey;'><br><br>
    <input type='submit' value='Ir a capitulo' style='font-size: 120px'>
    </form>
    </center>
    </body>";

    echo $string;
?>