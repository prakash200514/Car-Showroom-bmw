<?php
$url = 'https://videos.pexels.com/video-files/9017647/9017647-uhd_2560_1440_30fps.mp4';
$file = __DIR__ . '/assets/video/hero.mp4';

echo "Downloading video from $url to $file...\n";

$fp = fopen($file, 'w+');
$ch = curl_init(str_replace(" ", "%20", $url));
curl_setopt($ch, CURLOPT_TIMEOUT, 300);
curl_setopt($ch, CURLOPT_FILE, $fp); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
curl_exec($ch); 
if(curl_error($ch)) {
    echo "Error: " . curl_error($ch) . "\n";
} else {
    echo "Download complete. File size: " . filesize($file) . " bytes\n";
}
curl_close($ch);
fclose($fp);
?>
