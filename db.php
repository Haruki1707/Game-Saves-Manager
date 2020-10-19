<?php
    //!!!!!
    //Here are server things
    //Don't touch anything!!!!!!!!!!!!! (Unless you know what you are doing)
    //!!!!!

    function conectar(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "dnproject";

        $conectar = mysqli_connect($host, $user, $pass, $db);
        if (mysqli_connect_errno()) {
        $conectar = printf("Conexión fallida: %s\n", mysqli_connect_error());
        exit();
        }
	return $conectar;
    }


?>