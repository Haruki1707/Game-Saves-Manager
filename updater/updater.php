<?php
//https://api.github.com/repos/Haruki1707/Game-Saves-Manager/releases/latest

$user = "Haruki1707";
$repo = "Game-Saves-Manager";

$json = Release($user, $repo);

$actual = file_get_contents("info.json");
$actual = json_decode($actual, true);

    if($json != "Error"){
        if($json['tag_name'] != $actual['tag_name'] && $json['prerelease'] == false){
            Download($user, $repo, $json['tag_name']);
        }
        else { 
            echo "Already Updated";
        }
    }
    else {
        echo "Unable to check";
    }

function Release($user, $repo){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.github.com/repos/$user/$repo/releases/latest");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


    $headers = array();
    $headers[] = "User-Agent: $repo";
    $headers[] = 'Accept: application/vnd.github.v3+json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $result = 'Error';
    }
    else {
        $result = json_decode($result, true);
        
    }
    curl_close($ch);
    return $result;
}

function Download($user, $repo, $tag_name){
    $f = file_put_contents("latest.zip", fopen("https://github.com/$user/$repo/archive/$tag_name.zip", 'r'), LOCK_EX);
        if(FALSE === $f)
            die("Couldn't write to file.");
        else
            extractzip($tag_name);
}

function extractzip($tag_name){
    $filestring = '<?php
    if(isset($_GET["version"])){
        $version = $_GET["version"];
        $version = substr($version, 1);
    }
    else{
        return;
    }
    $desdir = "../";
    $frodir = "Game-Saves-Manager-$version";

        $zip = new ZipArchive;
        $res = $zip->open("latest.zip");
        if ($res === TRUE) {
            Eraseall();
            $zip->extractTo(".");
            $zip->close();
            unlink("latest.zip");

            CopyAll($frodir);
            EraseUpdate($frodir);

            echo "Succesfull";
        } else {
            echo "Failed";
        }
    
        function EraseUpdate($dir){
            if(is_dir($dir)){
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        rmdir($dir."/".$object);
                    }
                }
                rmdir($dir);
                reset($objects);
            }
        }

        function Eraseall($dir = "../"){
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != ".." && $object != "updater" && $object != ".git" && $object != "Icons" && $object != "Users" && $object != "Rubbish") {
                        if (filetype($dir."/".$object) == "dir"){
                            Eraseall("../".$object);
                            rmdir("../".$object); 
                        }
                        else{
                            if ($dir == "../")
                                $dir2 = "../";
                            else 
                                $dir2 = $dir."/";
                            
                            unlink($dir2.$object);
                        }
                    }
                }
                reset($objects);
            }
        }

        function CopyAll($dir){
            global $desdir;
            global $frodir;

            if(is_dir($dir)){
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if($object != "." && $object != ".."){
                        if (filetype($dir."/".$object) == "dir") {
                            if(!is_dir("$desdir/$object"))
                                mkdir("$desdir/$object", 0777);

                            CopyAll($dir."/".$object);
                        }
                        else {
                            if($dir != $frodir){
                                $dir2 = str_replace($frodir."/", $desdir, $dir."/");
                            }
                            else {
                                $dir2 = $desdir;
                            }

                            if(!rename("$dir/$object", "$dir2$object")){
                                unlink("$dir2$object");
                                rename("$dir/$object", "$dir2$object");
                            }
                        }
                    }
                }
                reset($objects);
            }
        }
?>';

    if(file_exists("extract.php")){
        if(md5_file("extract.php") != md5($filestring)){
            unlink("extract.php");
            $file = fopen("extract.php", "w") or die("unable to open file");
            fwrite($file, $filestring);
        }
    }
    else{
        $file = fopen("extract.php", "w") or die("unable to open file");
        fwrite($file, $filestring);
    }

    //header("Location: extract.php?version=$tag_name");
}