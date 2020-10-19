<?php

//https://api.github.com/repos/Haruki1707/SavDaC/releases/latest

$content = @file_get_contents("https://api.github.com/repos/KunoichiZ/lumaupdate/releases/latest");
$data = json_decode($content, true);

echo $data;