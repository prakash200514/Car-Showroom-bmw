<?php
include 'partials/header.php';
include 'config/database.php';

// Filters
$where  = "1";
$params = [];

if (!empty($_GET['series'])) {
    $where   .= " AND name LIKE ?";
    $params[] = '%' . $_GET['series'] . '%';
}
if (!empty($_GET['body_type'])) {
    $where   .= " AND body_type = ?";
    $params[] = $_GET['body_type'];
}
if (!empty($_GET['fuel_type'])) {
    $where   .= " AND fuel_type = ?";
    $params[] = $_GET['fuel_type'];
}

// Pagination
$limit  = 9;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Cars + primary image
$sql = "SELECT c.*, ci.image_path
        FROM cars c
        LEFT JOIN car_images ci ON ci.car_id = c.id AND ci.is_primary = 1
        WHERE $where
        ORDER BY c.is_featured DESC, c.id ASC
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$countSql  = "SELECT COUNT(*) FROM cars WHERE $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalCars  = $countStmt->fetchColumn();
$totalPages = max(1, ceil($totalCars / $limit));

// Placeholder images if none stored – all real BMW
$fallbacks = [
    'Coupe'       => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=800&q=80',
    'SUV'         => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80',
    'Sedan'       => 'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=800&q=80',
    'Convertible' => 'https://images.unsplash.com/photo-1556189250-72ba954e96b5?auto=format&fit=crop&w=800&q=80',
    'Hatchback'   => 'https://images.unsplash.com/photo-1553440683-1b94dd08f6d8?auto=format&fit=crop&w=800&q=80',
];

function fuelIcon($type) {
    return match(strtolower($type)) {
        'electric' => '<i class="fas fa-bolt" title="Electric"></i>',
        'hybrid'   => '<i class="fas fa-leaf" title="Hybrid"></i>',
        default    => '<i class="fas fa-gas-pump" title="Gasoline/Diesel"></i>',
    };
}
function fuelLabel($type) {
    return match(strtolower($type)) {
        'electric' => 'Electric',
        'hybrid'   => 'Plug-in Hybrid',
        'diesel'   => 'Diesel',
        default    => 'Gasoline',
    };
}

// Rough monthly EMI calculator (₹ price = price*84 INR placeholder conversion @ 84)
function monthlyEmi($priceUsd) {
    $priceInr = $priceUsd * 84;
    // 7% p.a. for 60 months
    $r = 0.07 / 12;
    $n = 60;
    $emi = $priceInr * ($r * pow(1+$r,$n)) / (pow(1+$r,$n)-1);
    return '₹' . number_format(round($emi), 0, '.', ',');
}
function formatPriceInr($priceUsd) {
    $inr = $priceUsd * 84;
    return '₹' . number_format(round($inr), 0, '.', ',') . '.00';
}
?>

<style>
/* BMW Vehicle Finder Page */
.vf-page { background:#f6f6f6; padding-top: var(--navbar-height); min-height:100vh; }

.vf-header {
    background:#fff;
    padding: 36px clamp(16px,3vw,56px) 0;
    border-bottom: 1px solid #e0e0e0;
}
.vf-header h1 {
    font-size: 1.9rem;
    font-weight: 700;
    color: #111;
    letter-spacing: -0.02em;
    margin-bottom: 24px;
}

/* Filter bar – bmw.in style pill buttons */
.vf-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-bottom: 0;
    align-items: center;
}
.vf-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid #cbcbcb;
    background: #fff;
    font-size: 0.80rem;
    font-weight: 500;
    color: #111;
    cursor: pointer;
    border-radius: 0;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition: border-color 0.2s, background 0.2s;
}
.vf-filter-pill:hover,
.vf-filter-pill.active { border-color: #111; background: #111; color: #fff; }
.vf-filter-pill i { font-size: 0.7rem; }

/* Sort row */
.vf-sort-row {
    background: #f6f6f6;
    padding: 14px clamp(16px,3vw,56px);
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.vf-sort-row select {
    padding: 9px 36px 9px 14px;
    border: 1.5px solid #cbcbcb;
    background: #fff;
    font-size: 0.8rem;
    font-weight: 500;
    color: #111;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%23333' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    cursor: pointer;
}
.vf-count { font-size: 0.8rem; color: #666; }

/* ── Vehicle Cards – exact BMW Vehicle Finder style ── */
.vf-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 3px;
    padding: 3px clamp(16px,3vw,56px) 56px;
}

.vf-card {
    background: #fff;
    position: relative;
    display: flex;
    flex-direction: column;
}

.vf-card__wishlist {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 2;
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    font-size: 1.1rem;
    padding: 4px;
    transition: color 0.2s;
}
.vf-card__wishlist:hover { color: #c00; }
.vf-card__wishlist.wishlisted { color: #c00; }

.vf-card__img-wrap {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f8f8;
    overflow: hidden;
    padding: 10px;
}
.vf-card__img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.5s ease;
}
.vf-card:hover .vf-card__img-wrap img { transform: scale(1.04); }

.vf-card__body { padding: 20px 20px 0; flex:1; }

.vf-card__name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111;
    letter-spacing: -0.01em;
    margin-bottom: 2px;
    line-height: 1.3;
}
.vf-card__tag {
    font-size: 0.72rem;
    color: #888;
    font-weight: 400;
    margin-bottom: 14px;
    display: block;
}

/* Price info row */
.vf-card__price-row {
    display: flex;
    gap: 0;
    margin-bottom: 14px;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    padding: 12px 0;
}
.vf-card__price-col {
    flex: 1;
    border-right: 1px solid #eee;
    padding-right: 14px;
    margin-right: 14px;
}
.vf-card__price-col:last-child { border-right: none; padding-right: 0; margin-right: 0; }
.vf-card__price-label {
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #aaa;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.vf-card__price-label i { font-size: 0.6rem; color: #bbb; cursor: help; }
.vf-card__price-value {
    font-size: 0.88rem;
    font-weight: 700;
    color: #111;
}

/* Specs strip */
.vf-card__specs {
    display: flex;
    gap: 20px;
    margin-bottom: 10px;
    align-items: center;
}
.vf-card__spec {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    font-size: 0.7rem;
    color: #666;
}
.vf-card__spec i { font-size: 1.1rem; color: #555; }

.vf-card__perf {
    font-size: 0.72rem;
    color: #666;
    margin-bottom: 4px;
}

/* Dealer */
.vf-card__dealer {
    font-size: 0.72rem;
    color: #888;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 10px 0;
    border-top: 1px solid #f0f0f0;
}

/* CTA Button */
.vf-card__cta {
    display: block;
    width: calc(100% + 40px);
    margin: 0 -20px;
    padding: 16px;
    background: transparent;
    border: none;
    border-top: 1.5px solid #e0e0e0;
    font-size: 0.82rem;
    font-weight: 600;
    color: #111;
    letter-spacing: 0.05em;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    margin-top: auto;
}
.vf-card__cta:hover { background: #111; color: #fff; }

/* Pagination */
.vf-pagination { display: flex; justify-content: center; gap: 4px; padding: 0 0 56px; }
.vf-page-btn {
    width: 38px; height: 38px;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid #ddd;
    background: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    color: #111;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}
.vf-page-btn:hover,
.vf-page-btn.active { background: #111; color: #fff; border-color: #111; }

@media (max-width: 1024px) { .vf-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 640px)  { .vf-grid { grid-template-columns: 1fr; } }
</style>

<div class="vf-page">

    <!-- Page Header + Filters -->
    <div class="vf-header">
        <div style="max-width:1400px;margin:0 auto;">
            <h1>BMW Vehicle Finder</h1>

            <!-- Filter pills -->
            <div class="vf-filters" style="padding-bottom:0;">
                <!-- Active filter badge -->
                <?php if (!empty($_GET['series']) || !empty($_GET['body_type']) || !empty($_GET['fuel_type'])): ?>
                <a href="cars.php" class="vf-filter-pill" style="background:#111;color:#fff;border-color:#111;">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
                <?php endif; ?>

                <!-- Body type pills -->
                <?php foreach(['Sedan','SUV','Coupe','Convertible'] as $type): ?>
                <a href="?body_type=<?= urlencode($type) ?>"
                   class="vf-filter-pill <?= ($_GET['body_type'] ?? '') === $type ? 'active' : '' ?>">
                    <?= $type ?>
                </a>
                <?php endforeach; ?>

                <!-- Fuel type pills -->
                <?php foreach(['Petrol'=>'Gasoline','Diesel'=>'Diesel','Electric'=>'Electric','Hybrid'=>'Hybrid'] as $val=>$label): ?>
                <a href="?fuel_type=<?= urlencode($val) ?>"
                   class="vf-filter-pill <?= ($_GET['fuel_type'] ?? '') === $val ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Tab-style series filters -->
            <div style="display:flex;gap:0;margin-top:16px;border-top:1px solid #e8e8e8;overflow-x:auto;">
                <?php
                $series = ['All'=>'','X Series'=>'X','M Series'=>'M','7 Series'=>'7','5 Series'=>'5','3 Series'=>'3','2 Series'=>'2','i Series'=>'i'];
                foreach($series as $label=>$val):
                    $isActive = ($label === 'All' && empty($_GET['series'])) || (!empty($_GET['series']) && str_contains($_GET['series'], $val));
                ?>
                <a href="<?= $val ? '?series='.urlencode($val) : 'cars.php' ?>"
                   style="padding:14px 20px;font-size:0.78rem;font-weight:600;letter-spacing:0.04em;color:<?= $isActive ? '#111' : '#888' ?>;border-bottom:<?= $isActive ? '3px solid #111' : '3px solid transparent' ?>;white-space:nowrap;text-decoration:none;transition:all 0.2s;">
                    <?= $label ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sort Row -->
    <div class="vf-sort-row" style="max-width:100%;">
        <span class="vf-count"><?= number_format($totalCars) ?> Vehicle<?= $totalCars!==1?'s':'' ?> found</span>
        <select onchange="if(this.value)window.location.href=this.value" style="cursor:pointer;">
            <option value="">Sort by Production Date (Descending)</option>
            <option value="cars.php?sort=price_asc">Price (Low to High)</option>
            <option value="cars.php?sort=price_desc">Price (High to Low)</option>
            <option value="cars.php?sort=name_asc">Model Name (A-Z)</option>
        </select>
    </div>

    <!-- Vehicle Grid -->
    <div style="max-width:1400px;margin:0 auto;">
        <div class="vf-grid">
            <?php if (count($cars) > 0): ?>
                <?php foreach ($cars as $idx => $car): ?>
                <?php
                    $imgUrl    = $car['image_path'] ?: ($fallbacks[$car['body_type']] ?? 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=800&q=80');
                    $emi       = monthlyEmi($car['price']);
                    $priceInr  = formatPriceInr($car['price']);
                    $fuelIco   = fuelIcon($car['fuel_type']);
                    $fuelLbl   = fuelLabel($car['fuel_type']);
                    $perfStr   = ($car['power_hp'] ?? '') ? 'Performance ' . $car['engine_cc'] . ' (' . $car['power_hp'] . ')' : '';
                    $fuelTypeStr = 'Fuel Type ' . $car['fuel_type'];
                ?>
                <div class="vf-card">
                    <!-- Wishlist Heart -->
                    <button class="vf-card__wishlist" onclick="toggleWishlist(this)" title="Save">
                        <i class="far fa-heart"></i>
                    </button>

                    <!-- Car Image (studio / clean style) -->
                    <div class="vf-card__img-wrap">
                        <img src="<?= htmlspecialchars($imgUrl) ?>"
                             alt="<?= htmlspecialchars($car['name']) ?>"
                             loading="<?= $idx < 3 ? 'eager' : 'lazy' ?>">
                    </div>

                    <div class="vf-card__body">
                        <!-- Model Name -->
                        <div class="vf-card__name"><?= htmlspecialchars($car['name']) ?></div>
                        <span class="vf-card__tag">New Car</span>

                        <!-- Price Row -->
                        <div class="vf-card__price-row">
                            <div class="vf-card__price-col">
                                <div class="vf-card__price-label">
                                    Monthly Instalment <i class="fas fa-info-circle" title="Indicative EMI at 7% p.a., 60 months"></i>
                                </div>
                                <div class="vf-card__price-value"><?= $emi ?></div>
                            </div>
                            <div class="vf-card__price-col">
                                <div class="vf-card__price-label">
                                    Price Information <i class="fas fa-info-circle" title="Ex-showroom price (approx)"></i>
                                </div>
                                <div class="vf-card__price-value"><?= $priceInr ?></div>
                            </div>
                        </div>

                        <!-- Fuel + Transmission icons -->
                        <div class="vf-card__specs">
                            <div class="vf-card__spec">
                                <?= $fuelIco ?>
                                <span><?= $fuelLbl ?></span>
                            </div>
                            <div class="vf-card__spec">
                                <i class="fas fa-cog" title="Transmission"></i>
                                <span><?= htmlspecialchars($car['transmission']) ?></span>
                            </div>
                            <?php if (!empty($car['seats'])): ?>
                            <div class="vf-card__spec">
                                <i class="fas fa-users" title="Seats"></i>
                                <span><?= $car['seats'] ?> Seats</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Perf specs -->
                        <?php if ($perfStr): ?>
                        <div class="vf-card__perf"><?= htmlspecialchars($perfStr) ?></div>
                        <div class="vf-card__perf"><?= htmlspecialchars($fuelTypeStr) ?></div>
                        <?php endif; ?>

                        <!-- Dealer -->
                        <div class="vf-card__dealer">
                            <i class="fas fa-map-marker-alt" style="color:var(--bmw-blue);font-size:0.85rem;"></i>
                            BMW Showroom – Authorised Dealership
                        </div>
                    </div>

                    <!-- Full-width CTA -->
                    <a href="car-details.php?id=<?= $car['id'] ?>" class="vf-card__cta">
                        Show vehicle details
                    </a>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div style="grid-column:1/-1;text-align:center;padding:80px 20px;">
                    <i class="fas fa-car" style="font-size:3rem;color:#ddd;margin-bottom:16px;"></i>
                    <h3 style="color:#888;font-weight:400;">No vehicles found matching your criteria.</h3>
                    <a href="cars.php" class="vf-filter-pill" style="margin-top:20px;display:inline-flex;">Clear Filters</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="vf-pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>" class="vf-page-btn"><i class="fas fa-chevron-left" style="font-size:0.7rem;"></i></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="vf-page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>" class="vf-page-btn"><i class="fas fa-chevron-right" style="font-size:0.7rem;"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</div><!-- /vf-page -->

<?php include 'partials/footer.php'; ?>

<script>
function toggleWishlist(btn) {
    const icon = btn.querySelector('i');
    icon.classList.toggle('far');
    icon.classList.toggle('fas');
    btn.classList.toggle('wishlisted');
}
</script>
