<?php
// setup_resources.php
// Purpose: Download 3D assets locally to avoid CORS and Network issues

$assetsDir = __DIR__ . '/assets/3d';
if (!file_exists($assetsDir)) {
    mkdir($assetsDir, 0777, true);
}

// URLs to download (Official Three.js examples)
$files = [
    // Model
    'Ferrari.glb' => 'https://threejs.org/examples/models/gltf/Ferrari.glb',
    
    // Environment Map (HDR)
    'venice_sunset_1k.hdr' => 'https://threejs.org/examples/textures/equirectangular/venice_sunset_1k.hdr',
];

echo "<h2>Downloading Assets...</h2>";

// options for context
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);

foreach ($files as $filename => $url) {
    echo "Downloading $filename... ";
    $destination = $assetsDir . '/' . $filename;
    
    try {
        $content = file_get_contents($url, false, $context);
        if ($content !== false) {
            file_put_contents($destination, $content);
            echo "<span style='color:green'>Success (" . strlen($content) . " bytes)</span><br>";
        } else {
            echo "<span style='color:red'>Failed (Empty content)</span><br>";
        }
    } catch (Exception $e) {
        echo "<span style='color:red'>Error: " . $e->getMessage() . "</span><br>";
    }
}

echo "<h3>Downloads Complete.</h3>";
echo "<a href='virtual-showroom.php?id=1'>Go to Virtual Showroom</a>";
?>
