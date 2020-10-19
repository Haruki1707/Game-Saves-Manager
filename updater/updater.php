<?php
//https://api.github.com/repos/Haruki1707/SavDaC/releases/latest
//https://api.github.com/repos/KunoichiZ/lumaupdate/releases/latest

$user = "KunoichiZ";
$repo = "lumaupdate";

$json = Release($user, $repo);

$actual = file_get_contents("info.json");
$actual = json_decode($actual, true);

    if($json != "Error"){
        if($json['tag_name'] != $actual['tag_name']){
            Download();
        }
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

function Download(){
    $f = file_put_contents("updater/latest.zip", fopen("https://github.com/$user/$repo/archive/$tag_name.zip", 'r'), LOCK_EX);
        if(FALSE === $f)
            die("Couldn't write to file.");
        else
            extractzip();
}

function extractzip(){
    $filestring = '<?php
    $zip = new ZipArchive;
    $res = $zip->open("latest.zip");
    if ($res === TRUE) {
        Eraseall();
        $zip->extractTo("../");
        $zip->close();
    } else {
    
    }
    
    function Eraseall($dir = "../"){
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != "updater" && $object != ".git" && $object != "icons" && $object != "Users") {
                    if (filetype($dir."/".$object) == "dir"){
                        Eraseall($dir."/".$object);
                        rmdir($dir."/".$object); 
                    }
                    else 
                        unlink($dir."/".$object);
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

    header("Location: extract.php");
}