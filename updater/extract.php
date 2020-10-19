<?php
    /*$zip = new ZipArchive;
    $res = $zip->open("latest.zip");
    if ($res === TRUE) {
        Eraseall();
        $zip->extractTo("../");
        $zip->close();
    } else {
    
    }*/
    Eraseall();

    function Eraseall($dir = "../"){
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != "updater" && $object != ".git" && $object != "Icons" && $object != "Users") {
                    if (filetype($dir."/".$object) == "dir"){
                        echo "----"."/".$object."<br>";
                        Eraseall("../".$object);
                        //rmdir("/".$object); 
                    }
                    else
                        echo "/".$object."<br>"; 
                        //unlink("/".$object);
                }
            }
            reset($objects);
        }
    }
    ?>