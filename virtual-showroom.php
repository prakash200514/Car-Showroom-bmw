<?php
include 'config/database.php';

// Get Car ID logic BEFORE header output
if (!isset($_GET['id'])) {
    // Default to the first car if no ID provided for demo purposes
    $stmt = $pdo->query("SELECT id FROM cars LIMIT 1");
    $car = $stmt->fetch();
    if($car) {
        header("Location: virtual-showroom.php?id=" . $car['id']);
        exit;
    } else {
        header("Location: cars.php");
        exit;
    }
}

$car_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: cars.php");
    exit;
}

// Fetch 360 Frames
$stmt = $pdo->prepare("SELECT image_path FROM car_360_frames WHERE car_id = ? ORDER BY frame_no ASC");
$stmt->execute([$car_id]);
$frames = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Car Images for fallback
$stmt = $pdo->prepare("SELECT image_path FROM car_images WHERE car_id = ? ORDER BY is_primary DESC LIMIT 1");
$stmt->execute([$car_id]);
$car_image = $stmt->fetch(PDO::FETCH_ASSOC);

// If no frames, use the main image as a fallback (single frame) or a placeholder set
$hasFrames = count($frames) > 0;

include 'partials/header.php';
?>

<style>
    /* Virtual Showroom Specific Styles */
    body {
        overflow: hidden; /* Lock scroll for immersion */
        background: #000;
        color: #fff;
    }
    
    .navbar {
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .showroom-container {
        position: relative;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at center, #1a1a1a 0%, #000 100%);
    }

    /* 360 Viewer Canvas */
    .viewer-stage {
        width: 100%;
        height: 100%;
        position: relative;
        cursor: grab;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: transform 0.1s ease-out; /* Smooth drag */
    }
    
    .viewer-stage:active {
        cursor: grabbing;
    }

    #car-frame {
        max-width: 85%;
        max-height: 85vh;
        object-fit: contain;
        user-select: none;
        pointer-events: none;
        filter: drop-shadow(0 20px 50px rgba(0,0,0,0.8));
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    /* Loading Spinner */
    .loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 20;
    }

    /* UI Overlay Controls */
    .showroom-ui {
        position: absolute;
        bottom: 0px;
        left: 0;
        width: 100%;
        padding: 40px;
        background: linear-gradient(to top, rgba(0,0,0,0.95), transparent);
        z-index: 100;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .car-info h1 {
        font-size: 3.5rem;
        margin-bottom: 5px;
        background: linear-gradient(to right, #fff, #999);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }

    .car-info p {
        font-size: 1.2rem;
        color: var(--text-muted);
        max-width: 500px;
    }

    .controls {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .control-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .control-btn:hover {
        background: #fff;
        color: #000;
        transform: scale(1.1);
    }
    
    .action-btn {
        padding: 15px 40px;
        font-size: 1.1rem;
        border-radius: 4px; /* Matches clean luxury */
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        background: #fff;
        color: #000;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        background: #e0e0e0;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    /* Hotspots (Demo) */
    .hotspot {
        position: absolute;
        width: 20px;
        height: 20px;
        background: rgba(255,255,255,0.9);
        border-radius: 50%;
        cursor: pointer;
        animation: pulse 2s infinite;
        box-shadow: 0 0 0 rgba(255,255,255, 0.4);
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255,255,255, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255,255,255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255,255,255, 0); }
    }

    @media (max-width: 768px) {
        .showroom-ui {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .car-info h1 { font-size: 2rem; }
        .controls { margin-top: 20px; }
        #car-frame { max-width: 95%; max-height: 50vh; }
    }
</style>

<div class="showroom-container">
    <!-- Back Button -->
    <a href="cars.php" style="position: absolute; top: 100px; left: 40px; color: #fff; z-index: 100; text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 500;">
        <i class="fas fa-arrow-left"></i> Back to Fleet
    </a>

    <!-- Viewer Stage -->
    <div class="viewer-stage" id="viewer-stage" style="width: 100%; height: 100%;">
        <!-- 3D Canvas will be injected here -->
        <div id="loader" class="loader">
            <i class="fas fa-circle-notch fa-spin fa-3x" style="color: #fff;"></i>
            <p style="margin-top: 10px; font-weight: 300; letter-spacing: 1px;">Loading 3D Experience...</p>
        </div>
    </div>

    <!-- UI Overlay -->
    <div class="showroom-ui" data-aos="fade-up">
        <div class="car-info">
            <h5 style="color: var(--accent-color); text-transform: uppercase; letter-spacing: 2px;">Virtual Showroom</h5>
            <h1><?= htmlspecialchars($car['name']) ?></h1>
            <p>Interactive 3D Experience. Drag to rotate, Scroll to zoom.</p>
        </div>
        
        <div class="controls">
            <!-- <button class="control-btn" onclick="rotateLeft()"><i class="fas fa-chevron-left"></i></button> -->
            <button class="control-btn" id="auto-rotate-btn" onclick="toggleAutoRotate()"><i class="fas fa-pause"></i></button>
            <!-- <button class="control-btn" onclick="rotateRight()"><i class="fas fa-chevron-right"></i></button> -->
            <button class="control-btn" onclick="resetView()" title="Reset View"><i class="fas fa-compress-arrows-alt"></i></button>
            <button class="action-btn">Configure & Buy</button>
        </div>
    </div>
</div>

<!-- Three.js Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/RGBELoader.js"></script>
<!-- Add DRACOLoader just in case the model is compressed -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/DRACOLoader.js"></script>

<script>
    // Configuration
    const container = document.getElementById('viewer-stage');
    const loaderElement = document.getElementById('loader');
    
    // Scene Setup
    const scene = new THREE.Scene();
    // Use a dark background to match the showroom theme, or let the CSS gradient show through
    // scene.background = new THREE.Color(0x111111); 
    // If we want transparency (for CSS radial gradient), do NOT set background color.
    
    // Camera
    const camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(-6, 2, 6); // Good angle for the Ferrari
    
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.0;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    // Output encoding fix for colors
    renderer.outputEncoding = THREE.sRGBEncoding;
    container.appendChild(renderer.domElement);
    
    // Controls
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.minDistance = 3;
    controls.maxDistance = 15;
    controls.maxPolarAngle = Math.PI / 2 - 0.02; // Prevent going below ground
    controls.autoRotate = true;
    controls.autoRotateSpeed = 0.8;
    
    // Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
    scene.add(ambientLight);
    
    // Main directional light (Sun)
    const dirLight = new THREE.DirectionalLight(0xffffff, 1.0);
    dirLight.position.set(5, 10, 7);
    dirLight.castShadow = true;
    dirLight.shadow.mapSize.width = 2048;
    dirLight.shadow.mapSize.height = 2048;
    dirLight.shadow.camera.near = 0.1;
    dirLight.shadow.camera.far = 20;
    scene.add(dirLight);

    // Floor (Shadow catcher)
    const floorGeometry = new THREE.PlaneGeometry(30, 30);
    const floorMaterial = new THREE.MeshBasicMaterial({ 
        color: 0x000000, 
        transparent: true,
        opacity: 0.5
    });
    // Use ShadowMaterial for invisible shadow catcher
    const shadowMaterial = new THREE.ShadowMaterial({ opacity: 0.5 });
    
    const floor = new THREE.Mesh(floorGeometry, shadowMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);
    
    // Environment Map (Reflection)
    new THREE.RGBELoader()
        .setPath('assets/3d/')
        .load('venice_sunset_1k.hdr', function (texture) {
            texture.mapping = THREE.EquirectangularReflectionMapping;
            scene.environment = texture;
        });

    // -------------------------------------------------------------------------
    // LOAD A RELIABLE 3D CAR MODEL (LOCAL)
    // -------------------------------------------------------------------------
    
    // Setup GLTFLoader with DRACO
    const gltfLoader = new THREE.GLTFLoader();
    const dracoLoader = new THREE.DRACOLoader();
    dracoLoader.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.4.1/');
    gltfLoader.setDRACOLoader(dracoLoader);
    
    // Using LOCAL ASSET
    const modelUrl = 'assets/3d/Ferrari.glb'; 
    
    gltfLoader.load(modelUrl, function (gltf) {
        const carModel = gltf.scene;
        
        // The Ferrari model is quite small in scale usually, or correct.
        // Let's inspect box to center it.
        const box = new THREE.Box3().setFromObject(carModel);
        const center = box.getCenter(new THREE.Vector3());
        
        // Reset position to center
        carModel.position.x += (carModel.position.x - center.x);
        carModel.position.z += (carModel.position.z - center.z);
        carModel.position.y = 0; // On floor
        
        // Material adjustments for better look (Car Paint)
        carModel.traverse(function (child) {
            if (child.isMesh) {
                child.castShadow = true;
                child.receiveShadow = true;
                
                // If it's the body, make it shinier
                if (child.material.name.includes('Body') || child.name.includes('Body')) {
                    child.material.metalness = 0.9;
                    child.material.roughness = 0.1;
                    child.material.clearcoat = 1.0;
                    child.material.clearcoatRoughness = 0.03;
                    child.material.color.set(0xaa0000); // BMW Red/Orange style
                }
            }
        });
        
        scene.add(carModel);
        
        // Hide Loader
        if(loaderElement) loaderElement.style.display = 'none';
        
    }, 
    // On Progress
    function (xhr) {
        // console.log((xhr.loaded / xhr.total * 100) + '% loaded');
    }, 
    // On Error
    function (error) {
        console.error(error);
        if(loaderElement) {
            loaderElement.innerHTML = `
                <i class="fas fa-exclamation-triangle fa-3x" style="color: #ffcc00; margin-bottom: 20px;"></i>
                <p>Failed to load 3D Model.</p>
                <small style="color: #999;">${error.message || 'Network Error or Blocked'}</small>
                <div style="margin-top:20px">
                    <button class="btn btn-primary" onclick="window.location.reload()">Retry</button>
                    <a href="cars.php" class="btn btn-outline" style="margin-left:10px; color:white; border-color:white">Back</a>
                </div>
            `;
        }
    });

    // UI Logic
    let isAutoRotating = true;
    
    function toggleAutoRotate() {
        isAutoRotating = !isAutoRotating;
        controls.autoRotate = isAutoRotating;
        const btn = document.getElementById('auto-rotate-btn');
        if(btn) btn.innerHTML = isAutoRotating ? '<i class="fas fa-pause"></i>' : '<i class="fas fa-play"></i>';
    }
    
    window.resetView = function() {
        camera.position.set(-6, 2, 6);
        controls.target.set(0, 0, 0);
        controls.update();
    }
    
    window.toggleAutoRotate = toggleAutoRotate;

    // Animation Loop
    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }
    animate();

    // Window Resize
    window.addEventListener('resize', onWindowResize, false);
    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }
</script>

<?php include 'partials/footer.php'; ?>
