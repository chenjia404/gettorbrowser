<?php
function geturl($url){
    $headerArray =array("Content-type:application/json;","Accept:application/json");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_USERAGENT, 'chenjia404');
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
    $output = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($output,true);
    return $output;
}

$data = geturl("https://api.github.com/repos/TheTorProject/gettorbrowser/releases");
foreach($data as $release){
    $time = strtotime($release['published_at']);
    if($time  > time() - 86400 * 100 && !$release['draft']
    && !$release['prerelease']
    && count($release['assets']) == 2
    )
    {
        $tmp = explode("-",$release['tag_name'],9);
        $tag = end($tmp);
        if(!file_exists($tag))
        mkdir($tag);
        echo  $tag . "\n";
        foreach($release['assets'] as $assets)
        {
            echo $assets['browser_download_url'] . "\n";
            $file_name = "$tag/" . basename($assets['browser_download_url']);
            if(!file_exists($file_name))
            {
                exec("curl -LJ {$assets['browser_download_url']} -o {$file_name}");
            }
        }
    }
}
