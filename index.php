<?php include 'partials/header.php'; ?>

<style>
/* ── BMW India Banner System ── */
.bmw-banner {
    position: relative;
    width: 100%;
    height: 100vh;
    min-height: 560px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    background: #0a0a0a;
}
.bmw-banner__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 9s ease;
}
.bmw-banner:hover .bmw-banner__img { transform: scale(1.04); }

/* ── Mute Toggle Button ── */
.bmw-sound-btn {
    position: absolute;
    bottom: 80px;
    right: clamp(20px, 4vw, 60px);
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s, border-color 0.2s;
    font-family: inherit;
    user-select: none;
}
.bmw-sound-btn:hover {
    background: rgba(0,0,0,0.70);
    border-color: rgba(255,255,255,0.55);
}
.bmw-sound-btn i { font-size: 0.85rem; }
.bmw-banner__video-wrap {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 0;
}
.bmw-banner__video-wrap iframe,
.bmw-banner__video-wrap video {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 120%;
    min-height: 120%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    object-fit: cover;
    pointer-events: none;
    border: none;
}
/* Dark overlay when video is active — keep light so video is bright */
.bmw-banner--has-video .bmw-banner__grad-dark {
    background: rgba(0,0,0,0.20);
}
.bmw-banner--has-video .bmw-banner__grad-bottom {
    background: linear-gradient(
        to top,
        rgba(0,0,0,0.55) 0%,
        rgba(0,0,0,0.0) 45%
    );
}
.bmw-banner--has-video::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.08);
    z-index: 1;
    pointer-events: none;
}

.bmw-banner__grad-dark {
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(
        to right,
        rgba(0,0,0,0.72) 0%,
        rgba(0,0,0,0.25) 55%,
        rgba(0,0,0,0.05) 100%
    );
}
.bmw-banner__grad-bottom {
    position: absolute;
    inset: 0;
    z-index: 2;
    background: linear-gradient(
        to top,
        rgba(0,0,0,0.65) 0%,
        rgba(0,0,0,0.0) 50%
    );
}
.bmw-banner__grad-light {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to right,
        rgba(255,255,255,0.75) 0%,
        rgba(255,255,255,0.20) 60%,
        transparent 100%
    );
}
.bmw-banner__content {
    position: relative;
    z-index: 3;
    padding: 0 clamp(24px,5vw,90px) clamp(48px,6vh,88px);
    max-width: 560px;
}
/* HERO only – taller padding for first banner */
.bmw-banner--hero { height: 100vh; }
.bmw-banner--hero .bmw-banner__content {
    padding-bottom: clamp(60px,8vh,110px);
}

.bmw-banner__eyebrow {
    display: block;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.28em;
    text-transform: uppercase;
    margin-bottom: 6px;
    color: rgba(255,255,255,0.75);
}
.bmw-banner__eyebrow--dark { color: var(--bmw-grey); }

.bmw-banner__headline {
    font-size: clamp(2rem, 5.5vw, 4.2rem);
    font-weight: 800;
    line-height: 1.05;
    letter-spacing: -0.02em;
    color: #fff;
    margin: 0 0 8px;
}
.bmw-banner__headline--dark { color: var(--bmw-dark); }

/* BMW India signature: giant thin model number/name */
.bmw-banner__model {
    font-size: clamp(4rem, 13vw, 10rem);
    font-weight: 200;
    line-height: 0.85;
    letter-spacing: -0.04em;
    color: #fff;
    margin: 0 0 12px;
}
.bmw-banner__model--dark { color: var(--bmw-dark); }

.bmw-banner__sub {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.85);
    margin-bottom: 6px;
}
.bmw-banner__sub--dark { color: var(--bmw-dark); }

.bmw-banner__tagline {
    font-size: clamp(0.9rem, 1.5vw, 1.1rem);
    font-weight: 400;
    color: rgba(255,255,255,0.88);
    margin: 0 0 28px;
    max-width: 420px;
    line-height: 1.5;
}
.bmw-banner__tagline--dark { color: #333; }

/* BMW India button – rectangular, no radius */
.bmw-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    border: 1.5px solid #fff;
    background: transparent;
    color: #fff;
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.25s, color 0.25s;
}
.bmw-cta:hover { background: #fff; color: #000; }

.bmw-cta--dark {
    border-color: #111;
    color: #111;
}
.bmw-cta--dark:hover { background: #111; color: #fff; }

.bmw-cta--blue {
    background: var(--bmw-blue);
    border-color: var(--bmw-blue);
    color: #fff;
}
.bmw-cta--blue:hover { background: var(--bmw-blue-dark); border-color: var(--bmw-blue-dark); }

/* divider white strip between banners */
.bmw-divider { height: 18px; background: #fff; }

/* ── Quicklinks Strip ── */
.bmw-quicklinks {
    background: #fff;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}
.bmw-ql-item {
    flex: 1;
    padding: 30px clamp(16px,3vw,56px);
    border-right: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background 0.25s;
    text-decoration: none;
    display: block;
}
.bmw-ql-item:last-child { border-right: none; }
.bmw-ql-item:hover { background: #f6f6f6; }
.bmw-ql-item h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111;
    margin-bottom: 3px;
    letter-spacing: -0.01em;
}

</style>



<?php
// ── DB-DRIVEN BANNERS ──────────────────────────────────────────────────────
// Load from site_banners table if it exists; otherwise use static fallback
$dbBanners = [];
try {
    global $pdo;
    if (!isset($pdo)) { include __DIR__ . '/config/database.php'; }
    $dbBanners = $pdo->query("SELECT * FROM site_banners WHERE is_active=1 ORDER BY position ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $_e) { $dbBanners = []; }

// Helper: resolve image src — supports legacy http URLs and new local paths
function frontBannerImgSrc(string $path): string {
    if ($path === '') return '';
    // Already an absolute URL or absolute server path — use as-is
    if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
        return htmlspecialchars($path);
    }
    // Local relative path from DB (e.g. uploads/banners/banner_xxx.jpg)
    return '/showroom/' . htmlspecialchars($path);
}

// Static fallback (same as original)
if (empty($dbBanners)) {
    $dbBanners = [
        ['position'=>1,'eyebrow'=>'LUXURY. FAST. FORWARD.','headline'=>'THE BMW','model_name'=>'7 RANGE.','sub_label'=>'','tagline'=>'','cta_text'=>'Discover now','cta_url'=>'car-reveal.php?id=1','cta_style'=>'outline','image_url'=>'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=2000&q=90','video_url'=>''],
        ['position'=>2,'eyebrow'=>'THE ALL-NEW','headline'=>'','model_name'=>'X3','sub_label'=>'MASTER EVERY MOMENT.','tagline'=>'','cta_text'=>'Discover now','cta_url'=>'car-reveal.php?id=2','cta_style'=>'outline','image_url'=>'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=2000&q=90','video_url'=>''],
        ['position'=>3,'eyebrow'=>'THE','headline'=>'','model_name'=>'iX1','sub_label'=>'LONG WHEELBASE','tagline'=>'DOMINATE EVERYDAY. YOUR WAY.','cta_text'=>'Discover now','cta_url'=>'car-reveal.php?id=3','cta_style'=>'outline','image_url'=>'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=2000&q=90','video_url'=>''],

        ['position'=>4,'eyebrow'=>'THE NEW','headline'=>'','model_name'=>'2','sub_label'=>'LEAVE BORING BEHIND.','tagline'=>'Drive Your Match at a special EMI of ₹29,999*.','cta_text'=>'Skip Boring','cta_url'=>'car-details-new.php','cta_style'=>'blue','image_url'=>'https://images.unsplash.com/photo-1553440683-1b94dd08f6d8?auto=format&fit=crop&w=2000&q=90','video_url'=>''],

        ['position'=>5,'eyebrow'=>'BMW M SERIES','headline'=>'','model_name'=>'M4','sub_label'=>'Competition.','tagline'=>'The most powerful M coupe ever. 503 hp. 0-100 km/h in 3.9s.','cta_text'=>'Configure &amp; Buy','cta_url'=>'cars.php','cta_style'=>'outline','image_url'=>'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=2000&q=90','video_url'=>''],
    ];
}



foreach ($dbBanners as $idx => $banner):
    $isHero    = ($idx === 0);
    $ctaClass  = 'bmw-cta' . ($banner['cta_style'] === 'blue' ? ' bmw-cta--blue' : ($banner['cta_style'] === 'dark' ? ' bmw-cta--dark' : ''));
    $videoUrl  = trim($banner['video_url'] ?? '');
    $hasVideo  = $videoUrl !== '';
    // Detect YouTube vs MP4
    $isYouTube = $hasVideo && (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be'));
    // Build YouTube embed URL with autoplay params
    if ($isYouTube) {
        // Use # as delimiter to avoid conflict with / inside the pattern
        preg_match('#(?:embed/|v=|youtu\.be/)([a-zA-Z0-9_-]{11})#', $videoUrl, $ytm);
        $ytId       = $ytm[1] ?? '';
        $ytEmbedUrl = 'https://www.youtube.com/embed/' . $ytId
                    . '?autoplay=1&mute=1&loop=1&controls=0&playlist=' . $ytId
                    . '&rel=0&modestbranding=1&playsinline=1&enablejsapi=1';
    }
?>
<section class="bmw-banner <?= $isHero ? 'bmw-banner--hero' : '' ?> <?= $hasVideo ? 'bmw-banner--has-video' : '' ?>" <?= $isHero ? 'id="hero"' : '' ?>>
    <?php if ($hasVideo): ?>
        <div class="bmw-banner__video-wrap">
            <?php if ($isYouTube): ?>
            <iframe id="bannerMedia_<?= $idx ?>" src="<?= htmlspecialchars($ytEmbedUrl) ?>"
                    title="Banner video"
                    frameborder="0"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen></iframe>
            <?php else: ?>
            <video id="bannerMedia_<?= $idx ?>" autoplay muted loop playsinline preload="auto">
                <source src="<?= htmlspecialchars($videoUrl) ?>" type="video/mp4">
            </video>
            <?php endif; ?>
        </div>


        <?php
            $btnId   = 'soundBtn_' . $idx;
            $mediaId = 'bannerMedia_' . $idx;
            $btnType = $isYouTube ? 'youtube' : 'mp4';
        ?>
        <button class="bmw-sound-btn" id="<?= $btnId ?>" data-type="<?= $btnType ?>" data-media="<?= $mediaId ?>" aria-label="Toggle sound">
            <i class="fas fa-volume-mute"></i>
            <span>Sound Off</span>
        </button>
    <?php else: ?>
    <img class="bmw-banner__img"
         src="<?= frontBannerImgSrc($banner['image_url']) ?>"
         alt="<?= htmlspecialchars($banner['model_name'] ?: $banner['eyebrow']) ?>"
         loading="<?= $isHero ? 'eager' : 'lazy' ?>">
    <?php endif; ?>
    <div class="bmw-banner__grad-dark"></div>
    <div class="bmw-banner__grad-bottom"></div>
    <div class="bmw-banner__content <?= $isHero ? '' : 'reveal-content' ?>" <?= $isHero ? 'style="animation:bmwReveal 1s ease forwards;"' : '' ?>>
        <?php if ($banner['eyebrow']): ?>
        <span class="bmw-banner__eyebrow"><?= htmlspecialchars($banner['eyebrow']) ?></span>
        <?php endif; ?>
        <?php if ($banner['headline']): ?>
        <span class="bmw-banner__headline"><?= htmlspecialchars($banner['headline']) ?></span>
        <?php endif; ?>
        <?php if ($banner['model_name']): ?>
        <div class="bmw-banner__model"><?= htmlspecialchars($banner['model_name']) ?></div>
        <?php endif; ?>
        <?php if ($banner['sub_label']): ?>
        <span class="bmw-banner__sub"><?= htmlspecialchars($banner['sub_label']) ?></span>
        <?php endif; ?>
        <?php if ($banner['tagline']): ?>
        <p class="bmw-banner__tagline"><?= $banner['tagline'] ?></p>
        <?php endif; ?>
        <div style="margin-top:24px;">
            <a href="<?= htmlspecialchars($banner['cta_url']) ?>" class="<?= $ctaClass ?>">
                <?= $banner['cta_text'] ?>
            </a>
        </div>
    </div>
    <?php if ($isHero): ?>
    <div style="position:absolute;bottom:28px;left:50%;transform:translateX(-50%);z-index:4;color:rgba(255,255,255,0.5);text-align:center;animation:bmwBounce 2s infinite;">
        <i class="fas fa-chevron-down" style="font-size:0.9rem;"></i>
    </div>
    <?php endif; ?>
</section>
<div class="bmw-divider"></div>
<?php endforeach; ?>

<!-- ═══════════════════════════════════════════
     FIND YOUR BMW — Quicklinks Strip
═══════════════════════════════════════════ -->
<section class="bmw-quicklinks">
    <div style="display:flex;max-width:var(--max-width);margin:0 auto;">
        <a href="cars.php" class="bmw-ql-item">
            <h3>Find a new car. <i class="fas fa-arrow-right bmw-ql-arrow"></i></h3>
            <p>Search available stock</p>
        </a>
        <a href="register.php" class="bmw-ql-item">
            <h3>Book a test drive. <i class="fas fa-arrow-right bmw-ql-arrow"></i></h3>
            <p>Request an appointment</p>
        </a>
        <a href="cars.php" class="bmw-ql-item">
            <h3>Build your own. <i class="fas fa-arrow-right bmw-ql-arrow"></i></h3>
            <p>Configure &amp; price</p>
        </a>
        <a href="showrooms.php" class="bmw-ql-item">
            <h3>Find a dealer. <i class="fas fa-arrow-right bmw-ql-arrow"></i></h3>
            <p>100+ centres across India</p>
        </a>
    </div>
</section>

<div class="bmw-divider"></div>


<!-- ═══════════════════════════════════════════
     MODEL GRID — 3 columns, BMW India style
═══════════════════════════════════════════ -->
<section style="background:#f6f6f6;padding:70px 0;">
    <div style="max-width:1300px;margin:0 auto;padding:0 clamp(16px,3vw,48px);">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:36px;" data-reveal>
            <div>
                <span style="display:block;font-size:0.68rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:#888;margin-bottom:6px;">Our Range</span>
                <h2 style="font-size:clamp(1.6rem,3vw,2.5rem);font-weight:800;letter-spacing:-0.02em;color:#111;margin:0;">Explore every model.</h2>
            </div>
            <a href="cars.php" style="font-size:0.8rem;font-weight:600;color:var(--bmw-blue);letter-spacing:0.06em;text-transform:uppercase;text-decoration:none;display:flex;align-items:center;gap:6px;transition:gap 0.2s;" onmouseover="this.style.gap='12px'" onmouseout="this.style.gap='6px'">
                View all <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:3px;" class="bmw-model-grid" data-reveal>

            <!-- BMW 7 Series -->
            <div style="background:#fff;overflow:hidden;cursor:pointer;transition:box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='none'">
                <div style="overflow:hidden;height:220px;background:#f4f4f4;">
                    <img src="https://images.unsplash.com/photo-1619767886558-efdc259cde1a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="BMW 7 Series" style="width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div style="padding:24px;">
                    <small style="font-size:0.65rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#888;">Sedan</small>
                    <h3 style="font-size:1.15rem;font-weight:800;color:#111;margin:4px 0 2px;letter-spacing:-0.01em;">BMW 7 Series</h3>
                    <p style="font-size:0.8rem;color:#888;margin:0 0 16px;">Starting from <strong style="color:var(--bmw-blue);">₹1,72,00,000*</strong></p>
                    <a href="cars.php" style="font-size:0.78rem;font-weight:600;color:var(--bmw-blue);letter-spacing:0.05em;text-transform:uppercase;display:flex;align-items:center;gap:5px;text-decoration:none;">
                        Discover now <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
                    </a>
                </div>
            </div>

            <!-- BMW X5 M -->
            <div style="background:#fff;overflow:hidden;cursor:pointer;transition:box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='none'">
                <div style="overflow:hidden;height:220px;background:#f4f4f4;">
                    <img src="https://images.unsplash.com/photo-1580274455191-1c62238fa1f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="BMW X5 M" style="width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div style="padding:24px;">
                    <small style="font-size:0.65rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#888;">SAV</small>
                    <h3 style="font-size:1.15rem;font-weight:800;color:#111;margin:4px 0 2px;letter-spacing:-0.01em;">BMW X5 M</h3>
                    <p style="font-size:0.8rem;color:#888;margin:0 0 16px;">Starting from <strong style="color:var(--bmw-blue);">₹1,95,00,000*</strong></p>
                    <a href="cars.php" style="font-size:0.78rem;font-weight:600;color:var(--bmw-blue);letter-spacing:0.05em;text-transform:uppercase;display:flex;align-items:center;gap:5px;text-decoration:none;">
                        Discover now <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
                    </a>
                </div>
            </div>

            <!-- BMW M4 Competition -->
            <div style="background:#fff;overflow:hidden;cursor:pointer;transition:box-shadow 0.3s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.boxShadow='none'">
                <div style="overflow:hidden;height:220px;background:#f4f4f4;">
                    <img src="https://images.unsplash.com/photo-1617788138017-80ad40651399?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                         alt="BMW M4 Competition" style="width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </div>
                <div style="padding:24px;">
                    <small style="font-size:0.65rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;color:#888;">M Coupe</small>
                    <h3 style="font-size:1.15rem;font-weight:800;color:#111;margin:4px 0 2px;letter-spacing:-0.01em;">BMW M4 Competition</h3>
                    <p style="font-size:0.8rem;color:#888;margin:0 0 16px;">Starting from <strong style="color:var(--bmw-blue);">₹1,35,00,000*</strong></p>
                    <a href="cars.php" style="font-size:0.78rem;font-weight:600;color:var(--bmw-blue);letter-spacing:0.05em;text-transform:uppercase;display:flex;align-items:center;gap:5px;text-decoration:none;">
                        Discover now <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<div class="bmw-divider" style="height:2px;background:#e0e0e0;"></div>

<!-- ═══════════════════════════════════════════
     SPECIAL OFFERS — dark section matching bmw.in
═══════════════════════════════════════════ -->
<section style="background:#111;padding:90px clamp(16px,5vw,90px);text-align:center;position:relative;overflow:hidden;" data-reveal>
    <div style="position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(28,105,212,0.15),transparent 70%);top:-180px;right:-100px;pointer-events:none;"></div>
    <div style="position:absolute;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(28,105,212,0.10),transparent 70%);bottom:-100px;left:-50px;pointer-events:none;"></div>
    <div style="position:relative;z-index:2;">
        <span style="display:block;font-size:0.68rem;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:12px;">Limited Time</span>
        <h2 style="font-size:clamp(2rem,4.5vw,3.5rem);font-weight:800;color:#fff;letter-spacing:-0.02em;margin-bottom:14px;">BMW Special Offers.</h2>
        <p style="max-width:540px;margin:0 auto 32px;font-size:1rem;color:rgba(255,255,255,0.65);line-height:1.7;">
            Monthly EMI starting from ₹29,999/Month*. Check out BMW Finance offers available across the full range.
        </p>
        <a href="cars.php" class="bmw-cta bmw-cta--blue" style="padding:14px 40px;">Know more</a>
    </div>
</section>

<div class="bmw-divider"></div>

<!-- ═══════════════════════════════════════════
     STATS STRIP
═══════════════════════════════════════════ -->
<section style="background:#fff;padding:70px 0;" data-reveal>
    <div style="max-width:1200px;margin:0 auto;padding:0 clamp(16px,3vw,48px);display:grid;grid-template-columns:repeat(4,1fr);gap:0;border:1px solid #eee;" class="stats-grid">
        <?php foreach([
            ['15,000+','Vehicles<br>Delivered'],
            ['120+',   'Awards &amp;<br>Accolades'],
            ['50,000+','Satisfied<br>Owners'],
            ['100+',   'Authorised<br>Centres'],
        ] as $i => [$num,$label]): ?>
        <div style="text-align:center;padding:40px 20px;<?= $i < 3 ? 'border-right:1px solid #eee;' : '' ?>">
            <div style="font-size:clamp(2.2rem,4vw,3.2rem);font-weight:900;color:var(--bmw-blue);letter-spacing:-0.03em;margin-bottom:8px;"><?= $num ?></div>
            <p style="font-size:0.7rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:#888;margin:0;line-height:1.8;"><?= $label ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="bmw-divider" style="height:2px;background:#eee;"></div>

<!-- ═══════════════════════════════════════════
     NEWSLETTER — matches bmw.in top-of-footer strip
═══════════════════════════════════════════ -->
<section style="background:#fff;padding:28px clamp(16px,3vw,60px);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;border-top:1px solid #e8e8e8;border-bottom:1px solid #e8e8e8;max-width:100%;">
    <div style="display:flex;align-items:center;gap:18px;">
        <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/BMW.svg" alt="BMW" style="width:36px;height:36px;opacity:0.8;">
        <span style="font-size:0.9rem;color:#333;font-weight:400;">Stay up to date with the latest news from BMW</span>
    </div>
    <a href="#" style="font-size:0.82rem;font-weight:600;color:#111;letter-spacing:0.05em;display:flex;align-items:center;gap:6px;text-decoration:none;border-bottom:1px solid #111;padding-bottom:2px;">
        Sign up <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
    </a>
</section>


<?php include 'partials/footer.php'; ?>

<style>
@keyframes bmwReveal {
    from { opacity:0; transform:translateY(30px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes bmwBounce {
    0%,100% { transform:translateX(-50%) translateY(0); }
    50%      { transform:translateX(-50%) translateY(8px); }
}
</style>

<script>
// ── Sound Toggle for Banner Videos ────────────────────────────────────────
document.querySelectorAll('.bmw-sound-btn').forEach(btn => {
    const type    = btn.dataset.type;   // 'mp4' or 'youtube'
    const mediaEl = document.getElementById(btn.dataset.media);
    let muted = true; // starts muted

    function updateBtn(isMuted) {
        const icon = btn.querySelector('i');
        const lbl  = btn.querySelector('span');
        if (isMuted) {
            icon.className = 'fas fa-volume-mute';
            lbl.textContent = 'Sound Off';
        } else {
            icon.className = 'fas fa-volume-up';
            lbl.textContent = 'Sound On';
        }
    }

    btn.addEventListener('click', () => {
        muted = !muted;

        if (type === 'mp4' && mediaEl) {
            mediaEl.muted = muted;
            // If unmuting, ensure it plays (some browsers pause on unmute)
            if (!muted) mediaEl.play().catch(() => {});
        } else if (type === 'youtube' && mediaEl) {
            // YouTube postMessage API
            const cmd = muted ? 'mute' : 'unMute';
            mediaEl.contentWindow.postMessage(
                JSON.stringify({ event: 'command', func: cmd, args: [] }),
                '*'
            );
        }

        updateBtn(muted);
    });
});
</script>

<script>
// Scroll-reveal for banner content
(function() {
    const els = document.querySelectorAll('.reveal-content, [data-reveal]');
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

    els.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        obs.observe(el);
    });

    // Responsive grid: 1 col on mobile
    const grid = document.querySelector('.bmw-model-grid');
    const statsGrid = document.querySelector('.stats-grid');
    function responsive() {
        if (window.innerWidth < 768) {
            if (grid) grid.style.gridTemplateColumns = '1fr';
            if (statsGrid) statsGrid.style.gridTemplateColumns = 'repeat(2,1fr)';
        } else if (window.innerWidth < 1024) {
            if (grid) grid.style.gridTemplateColumns = 'repeat(2,1fr)';
            if (statsGrid) statsGrid.style.gridTemplateColumns = 'repeat(4,1fr)';
        } else {
            if (grid) grid.style.gridTemplateColumns = 'repeat(3,1fr)';
        }
    }
    responsive();
    window.addEventListener('resize', responsive);
})();
</script>
