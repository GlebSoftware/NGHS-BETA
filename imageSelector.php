<?php

$folder = opendir('backgrounds');

$i = 0;
while(false !=($file = readdir($folder))){
if($file != "." && $file != ".."){
    $images[$i]= $file;
    $i++;
    }
}

$random_img=rand(0,count($images)-1);

$imageSelected = 'http://nghsbeta.com/backgrounds/'.$images[$random_img];

?>