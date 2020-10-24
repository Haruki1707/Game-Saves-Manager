<?php
//https://api.github.com/repos/Haruki1707/SavDaC/releases/latest

$user = "Haruki1707";
$repo = "lumaupdate";

$json = Release($user, $repo);

$actual = file_get_contents("info.json");
$actual = json_decode($actual, true);

    if($json != "Error"){
        if($json['tag_name'] != $actual['tag_name']){
            Download($user, $repo, $json['tag_name']);
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

function Download($user, $repo, $tag_name){
    $f = file_put_contents("latest.zip", fopen("https://github.com/$user/$repo/archive/$tag_name.zip", 'r'), LOCK_EX);
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
        unlink("latest.zip");
    } else {
    
    }

    function Eraseall($dir = "../"){
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != "updater" && $object != ".git" && $object != "Icons" && $object != "Users") {
                    if (filetype($dir."/".$object) == "dir"){
                        echo "../".$object."<br>";
                        Eraseall("../".$object);
                        rmdir("../".$object); 
                    }
                    else{
                        if ($dir == "../")
                            $dir2 = "../";
                        else 
                            $dir2 = $dir."/";
                    
                        echo $dir2.$object."<br>"; 
                        unlink($dir2.$object);
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

    //header("Location: extract.php");
}