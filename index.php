<?php
require 'config/database.php';
$title = 'Home - The Wildflower Studio';

$result = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY id DESC");
if (!$result) $result = false;

$categories = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY id ASC");
if (!$categories) $categories = false;

require 'includes/header.php';
?>

<!-- TRUE FULL SCREEN HERO SECTION (#FAF7F1) -->
<section class="hero-section">
  <div class="hero-container">
    <!-- LEFT: CONTENT -->
    <div class="hero-left">
      <span class="hero-small-label">FRESH FLOWERS · THOUGHTFUL MOMENTS</span>
      <h1 class="hero-heading">Let Nature Say What Words Can't.</h1>
      <p class="hero-description">Beautifully arranged flowers to celebrate life's special moments. Find the perfect bouquet to express your love, share happiness, and make every moment unforgettable.</p>
      <div class="hero-action">
        <a href="products.php" class="hero-btn">
          <span>Shop Flowers</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
    
    <!-- RIGHT: PHOTO -->
    <div class="hero-right">
      <div class="hero-image-wrapper">
        <img src="images/Herosection.png" alt="Fresh Flowers Bouquet" class="hero-image">
      </div>
    </div>
  </div>
</section>

<div class="page"><!-- reopen .page for remaining content -->

<!-- SHOP FEATURES / TRUST BAR -->
<div class="features-bar">
  <div class="feature-item">
    <div class="feature-icon">🚚</div>
    <div>
      <div class="feature-title">Free Shipping</div>
      <div class="feature-desc">On orders above ₹999</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">🌸</div>
    <div>
      <div class="feature-title">100% Handcrafted</div>
      <div class="feature-desc">Made with artisan care</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">🔒</div>
    <div>
      <div class="feature-title">Secure Payments</div>
      <div class="feature-desc">Cash on Delivery, UPI &amp; Cards</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">✨</div>
    <div>
      <div class="feature-title">Custom Keepsakes</div>
      <div class="feature-desc">Personalized for loved ones</div>
    </div>
  </div>
</div>

<?php 
$catList = [];
if ($categories) {
  while ($row = $categories->fetch_assoc()) {
    $catList[] = $row;
  }
}
$catLoopCount = count($catList);
?>
<!-- CATEGORIES SECTION (SECTION 3) -->
<?php if (!empty($catList)): ?>
<section class="category-section">
  <div class="category-section-header">
    <span class="category-small-label">FIND YOUR FAVOURITE</span>
    <h2 class="category-heading">Shop by Category</h2>
    <p class="category-description">From timeless roses to seasonal blooms, find flowers for every feeling and occasion.</p>
  </div>

  <div class="category-slider-wrapper">
    <button class="cat-nav-btn cat-prev-btn" id="catPrevBtn" aria-label="Previous categories">
      <i class="bi bi-chevron-left"></i>
    </button>

    <div class="category-slider-track" id="catSliderTrack">
      <?php
        // Render items 3x for seamless infinite loop (middle set = real items)
        for ($rep = 0; $rep < 3; $rep++):
          foreach ($catList as $c):
      ?>
      <div class="category-item"<?= $rep !== 1 ? ' aria-hidden="true"' : '' ?>>
        <a href="products.php?category=<?= $c['id'] ?>" class="category-card-link">
          <div class="category-box">
            <div class="cat-img-wrap">
              <?php if (!empty($c['image'])): ?>
                <img src="<?= e(category_image_url($c['image'], 'images/no-image.svg')) ?>" alt="<?= e($c['name']) ?>" class="cat-img">
              <?php else: ?>
                <span class="cat-emoji">🌸</span>
              <?php endif; ?>
            </div>
            <h3 class="cat-title"><?= e($c['name']) ?></h3>
            <p class="cat-desc"><?= e($c['description'] ?: 'Artisan floral arrangement') ?></p>
            <span class="cat-explore-btn">
              <span>Explore</span>
              <i class="bi bi-arrow-right"></i>
            </span>
          </div>
        </a>
      </div>
      <?php endforeach; endfor; ?>
    </div>

    <button class="cat-nav-btn cat-next-btn" id="catNextBtn" aria-label="Next categories">
      <i class="bi bi-chevron-right"></i>
    </button>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var catTrack = document.getElementById('catSliderTrack');
  var catPrev  = document.getElementById('catPrevBtn');
  var catNext  = document.getElementById('catNextBtn');
  var CAT_TOTAL = <?= $catLoopCount ?>;

  if (!catTrack || CAT_TOTAL === 0) return;

  var catAutoTimer   = null;
  var catScrolling   = false;
  var catMoveQueue   = [];

  function catCardStep() {
    var item = catTrack.querySelector('.category-item');
    if (!item) return 280;
    var gap = parseFloat(window.getComputedStyle(catTrack).gap) || 20;
    return item.offsetWidth + gap;
  }

  function catSetScrollPosition(position) {
    var previousBehavior = catTrack.style.scrollBehavior;
    var previousSnap = catTrack.style.scrollSnapType;
    catTrack.style.scrollBehavior = 'auto';
    catTrack.style.scrollSnapType = 'none';
    catTrack.scrollLeft = position;
    catTrack.offsetWidth;
    catTrack.style.scrollBehavior = previousBehavior;
    catTrack.style.scrollSnapType = previousSnap;
  }

  // Jump to start of middle set without animation
  function catJumpMiddle() {
    catSetScrollPosition(catCardStep() * CAT_TOTAL);
  }
  catJumpMiddle();

  // On scroll: if we drift into clone sets, silently jump back to middle equivalent
  catTrack.addEventListener('scroll', function() {
    var step     = catCardStep();
    var setWidth = step * CAT_TOTAL;
    var pos      = catTrack.scrollLeft;

    if (pos >= setWidth * 2) {
      catSetScrollPosition(pos - setWidth);
    } else if (pos < setWidth - (step * 0.1)) {
      catSetScrollPosition(pos + setWidth);
    }
  }, { passive: true });

  function catMove(direction) {
    catMoveQueue.push(direction);
    catProcessQueue();
  }

  function catProcessQueue() {
    if (catScrolling || catMoveQueue.length === 0) return;
    catScrolling = true;
    catTrack.scrollBy({ left: catMoveQueue.shift() * catCardStep(), behavior: 'smooth' });
    setTimeout(function() {
      catScrolling = false;
      catProcessQueue();
    }, 450);
  }

  if (catPrev) catPrev.addEventListener('click', function() { catMove(1); catResetTimer(); });
  if (catNext) catNext.addEventListener('click', function() { catMove(-1); catResetTimer(); });

  function catStartAuto() {
    if (!catAutoTimer) {
      catAutoTimer = setInterval(function() {
        catMove(1);
      }, 2800);
    }
  }
  function catStopAuto() { clearInterval(catAutoTimer); catAutoTimer = null; }
  function catResetTimer() { catStopAuto(); catStartAuto(); }

  catStartAuto();
});
</script>
<?php endif; ?>

<?php
$prodList = [];
if ($result) {
  while ($p = $result->fetch_assoc()) {
    $prodList[] = $p;
  }
}
$prodLoopCount = count($prodList);
?>
<!-- SECTION 4: HANDPICKED FOR YOU (FLOWERS WORTH FALLING FOR) -->
<?php if (!empty($prodList)): ?>
<section class="featured-products-section">
  <div class="category-section-header">
    <span class="category-small-label">HANDPICKED FOR YOU</span>
    <h2 class="category-heading">Flowers Worth Falling For</h2>
    <p class="category-description">Discover our most-loved bouquets, thoughtfully arranged to make every moment feel special.</p>
  </div>

  <div class="category-slider-wrapper">
    <button class="cat-nav-btn cat-prev-btn" id="prodPrevBtn" aria-label="Previous products">
      <i class="bi bi-chevron-left"></i>
    </button>

    <div class="category-slider-track" id="prodSliderTrack">
      <?php
        // Render items 3x for seamless infinite loop (middle set = real items)
        for ($rep = 0; $rep < 3; $rep++):
          foreach ($prodList as $p):
      ?>
      <div class="category-item"<?= $rep !== 1 ? ' aria-hidden="true"' : '' ?>>
        <div class="home-product-card">
          <a href="product-details.php?id=<?= $p['id'] ?>" class="home-product-img-wrap">
            <?php if (!empty($p['image'])): ?>
              <img src="<?= product_image_url($p['image']) ?>" alt="<?= e($p['name']) ?>" class="home-product-img">
            <?php else: ?>
              <span class="cat-emoji">🌸</span>
            <?php endif; ?>
            <?php if (!empty($p['discount']) && $p['discount'] > 0): ?>
              <span class="home-discount-badge">SAVE <?= price($p['discount']) ?></span>
            <?php endif; ?>
          </a>
          <div class="home-product-info">
            <span class="home-product-cat"><?= e($p['category'] ?? 'Fresh Flowers') ?></span>
            <h3 class="home-product-title">
              <a href="product-details.php?id=<?= $p['id'] ?>"><?= e($p['name']) ?></a>
            </h3>
            <div class="home-product-price">
              <span class="price-current"><?= price($p['price']) ?></span>
              <?php if (!empty($p['discount']) && $p['discount'] > 0): ?>
                <span class="price-old"><?= price($p['price'] + $p['discount']) ?></span>
              <?php endif; ?>
            </div>
            <a href="product-details.php?id=<?= $p['id'] ?>" class="home-view-btn">
              <span>View Bouquet</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; endfor; ?>
    </div>

    <button class="cat-nav-btn cat-next-btn" id="prodNextBtn" aria-label="Next products">
      <i class="bi bi-chevron-right"></i>
    </button>
  </div>

  <!-- ALL PRODUCTS BUTTON (LINKS TO SHOP / PRODUCTS.PHP) -->
  <div class="all-products-btn-wrap">
    <a href="products.php" class="all-products-btn">
      <span>Explore All Flowers</span>
      <i class="bi bi-arrow-right"></i>
    </a>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var pTrack = document.getElementById('prodSliderTrack');
  var pPrev  = document.getElementById('prodPrevBtn');
  var pNext  = document.getElementById('prodNextBtn');
  var PROD_TOTAL = <?= $prodLoopCount ?>;

  if (!pTrack || PROD_TOTAL === 0) return;

  var pAutoTimer  = null;
  var pScrolling  = false;
  var pMoveQueue  = [];

  function prodCardStep() {
    var item = pTrack.querySelector('.category-item');
    if (!item) return 280;
    var gap = parseFloat(window.getComputedStyle(pTrack).gap) || 20;
    return item.offsetWidth + gap;
  }

  function prodSetScrollPosition(position) {
    var previousBehavior = pTrack.style.scrollBehavior;
    var previousSnap = pTrack.style.scrollSnapType;
    pTrack.style.scrollBehavior = 'auto';
    pTrack.style.scrollSnapType = 'none';
    pTrack.scrollLeft = position;
    pTrack.offsetWidth;
    pTrack.style.scrollBehavior = previousBehavior;
    pTrack.style.scrollSnapType = previousSnap;
  }

  // Jump to start of middle set without animation
  function prodJumpMiddle() {
    prodSetScrollPosition(prodCardStep() * PROD_TOTAL);
  }
  prodJumpMiddle();

  // On scroll: if we drift into clone sets, silently jump back to middle equivalent
  pTrack.addEventListener('scroll', function() {
    var step     = prodCardStep();
    var setWidth = step * PROD_TOTAL;
    var pos      = pTrack.scrollLeft;

    if (pos >= setWidth * 2) {
      prodSetScrollPosition(pos - setWidth);
    } else if (pos < setWidth - (step * 0.1)) {
      prodSetScrollPosition(pos + setWidth);
    }
  }, { passive: true });

  function prodMove(direction) {
    pMoveQueue.push(direction);
    prodProcessQueue();
  }

  function prodProcessQueue() {
    if (pScrolling || pMoveQueue.length === 0) return;
    pScrolling = true;
    pTrack.scrollBy({ left: pMoveQueue.shift() * prodCardStep(), behavior: 'smooth' });
    setTimeout(function() {
      pScrolling = false;
      prodProcessQueue();
    }, 450);
  }

  if (pPrev) pPrev.addEventListener('click', function() { prodMove(1); prodResetTimer(); });
  if (pNext) pNext.addEventListener('click', function() { prodMove(-1); prodResetTimer(); });

  function prodStartAuto() {
    if (!pAutoTimer) {
      pAutoTimer = setInterval(function() {
        prodMove(1);
      }, 3200);
    }
  }
  function prodStopAuto() { clearInterval(pAutoTimer); pAutoTimer = null; }
  function prodResetTimer() { prodStopAuto(); prodStartAuto(); }

  prodStartAuto();
});
</script>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>
