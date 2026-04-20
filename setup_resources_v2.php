<?php
// setup_resources_v2.php
// Robust downloader using cURL

$assetsDir = __DIR__ . '/assets/3d';
if (!file_exists($assetsDir)) {
    mkdir($assetsDir, 0777, true);
}

$files = [
    'Ferrari.glb' => 'https://threejs.org/examples/models/gltf/Ferrari.glb',
    'venice_sunset_1k.hdr' => 'https://threejs.org/examples/textures/equirectangular/venice_sunset_1k.hdr',
    'draco_decoder.js' => 'https://www.gstatic.com/draco/versioned/decoders/1.4.1/draco_decoder.js',
    'draco_decoder.wasm' => 'https://www.gstatic.com/draco/versioned/decoders/1.4.1/draco_decoder.wasm',
    'draco_wasm_wrapper.js' => 'https://www.gstatic.com/draco/versioned/decoders/1.4.1/draco_wasm_wrapper.js'
];

function download($url, $dest) {
    echo "Downloading " . basename($dest) . "... ";
    $fp = fopen($dest, 'w+');
    $ch = curl_init(str_replace(" ", "%20", $url));
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Important for XAMPP/Windows if certs are missing
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    
    $data = curl_exec($ch);
    
    if(!$data) {
        echo "<span style='color:red'>cURL Error: " . curl_error($ch) . "</span><br>";
    } else {
        $info = curl_getinfo($ch);
        echo "<span style='color:green'>Success (HTTP " . $info['http_code'] . ", " . $info['download_content_length'] . " bytes)</span><br>";
    }
    
    curl_close($ch);
    fclose($fp);
}

foreach($files as $name => $url) {
    download($url, $assetsDir . '/' . $name);
}

echo "<h3>Download Complete</h3>";
?>
