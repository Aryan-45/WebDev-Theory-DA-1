<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Collective - Locally Crafted Handmade Goods</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Our Stylesheet -->
    <link rel="stylesheet" href="css/styles.css">

    <!-- Add favicon links here later -->

</head>
<body>

    <!-- ============================================= -->
    <!-- HEADER & NAVIGATION                           -->
    <!-- ============================================= -->
    <header class="site-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.html">Artisan Collective</a>
                <!-- <a href="index.html"><img src="images/logo.png" alt="Artisan Collective"></a> -->
            </div>

            <nav class="main-navigation" aria-label="Main Navigation">
                 <button class="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false">
                    <span class="sr-only">Menu</span>
                    <i class="fas fa-bars hamburger-icon"></i>
                 </button>

                <ul id="primary-navigation" class="nav-list">
                    <li class="nav-item dropdown">
                        <!-- Removed aria-haspopup -->
                        <a href="shop.html" class="nav-link">Shop <i class="fas fa-chevron-down fa-xs"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="shop-all.html">Shop All</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="category-pottery.html">Pottery</a></li>
                            <li><a href="category-textiles.html">Textiles</a></li>
                            <li><a href="category-jewelry.html">Jewelry</a></li>
                            <li><a href="category-woodwork.html">Woodwork</a></li>
                            <!-- PHP will add more later -->
                        </ul>
                    </li>
                    <li class="nav-item"><a href="artisans.html" class="nav-link">Artisans</a></li>
                    <li class="nav-item"><a href="about.html" class="nav-link">Our Story</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <button class="search-toggle" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
                <a href="account.php" class="action-link" aria-label="My Account">
                    <i class="fas fa-user"></i>
                </a>
                <a href="cart.html" class="action-link" aria-label="Shopping Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count" aria-hidden="true">0</span>
                </a>
            </div>

            <div class="search-bar">
                 <form action="/search" method="get">
                     <input type="search" name="query" placeholder="Search products & artisans..." aria-label="Search products and artisans">
                     <button type="submit">Search</button>
                 </form>
             </div>
        </div>
    </header>
    <!-- ============================================= -->
    <!-- END OF HEADER & NAVIGATION                   -->
    <!-- ============================================= -->


    <!-- ============================================= -->
    <!-- MAIN CONTENT AREA                             -->
    <!-- ============================================= -->
    <main>

        <!-- ============================================= -->
        <!-- HERO SECTION                                -->
        <!-- ============================================= -->
        <section class="hero">
            <div class="container hero-content">
                <h1>Discover Authentic Local Craftsmanship</h1>
                <p>Unique, handmade treasures crafted with passion by artisans in your community.</p>
                <a href="shop-all.html" class="btn btn-primary btn-lg">Explore All Products</a>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF HERO SECTION                         -->
        <!-- ============================================= -->


        <!-- ============================================= -->
        <!-- CATEGORIES SHOWCASE SECTION                 -->
        <!-- ============================================= -->
        <section class="categories-showcase section-padding">
            <div class="container">
                <h2 class="section-title text-center">Browse by Category</h2>
                <div class="category-grid">
                    <a href="category-pottery.html" class="category-card">
                        <img src="https://clayandglaze.in/wp-content/uploads/slider/cache/d49b99a3a4348bc92b5caa7f09d19aa0/045A0758-min-scaled.jpg" alt="Handmade pottery">
                        <h3>Pottery</h3>
                    </a>
                    <a href="category-textiles.html" class="category-card">
                        <img src="https://cdn.shopify.com/s/files/1/0641/4450/7125/files/W4_480x480.webp?v=1726059964" alt="Woven textiles">
                        <h3>Textiles</h3>
                    </a>
                    <a href="category-jewelry.html" class="category-card">
                        <img src="https://diybaazar.com/publicuploads/seller/products/handmade-jewelry-1-2-3-4-5-1965-2761-1_diybaazar629ef9a91022d.jpg" alt="Handcrafted jewelry">
                        <h3>Jewelry</h3>
                    </a>
                     <a href="category-woodwork.html" class="category-card">
                        <img src="https://st4.depositphotos.com/19463018/30997/i/450/depositphotos_309975800-stock-photo-photo-traditional-handicraft-woodwork-wooden.jpg" alt="Carved woodwork">
                        <h3>Woodwork</h3>
                    </a>
                    <!-- Add more category cards here -->
                </div>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF CATEGORIES SHOWCASE SECTION          -->
        <!-- ============================================= -->


        <!-- ============================================= -->
        <!-- FEATURED PRODUCTS SECTION                   -->
        <!-- ============================================= -->
        <section class="featured-products section-padding bg-medium">
             <div class="container">
                <h2 class="section-title text-center">New Arrivals</h2>
                <div class="product-grid">
                    <!-- Product Card Example 1 -->
                    <article class="product-card">
                        <a href="product-detail.html" class="product-link">
                            <img src="https://rareplanet.com/cdn/shop/files/ssyykOSr_1.jpg?v=1720687709" alt="Hand-thrown Ceramic Mug" class="product-image">
                            <div class="product-info">
                                <h3 class="product-title">Hand-thrown Ceramic Mug</h3>
                                <p class="product-artisan">by Artisan Name A</p>
                                <p class="product-price">₹35.00</p>
                            </div>
                        </a>
                        <button class="btn btn-secondary btn-sm add-to-cart-btn" data-product-id="1">Add to Cart</button>
                    </article>

                    <!-- Product Card Example 2 -->
                     <article class="product-card">
                        <a href="product-detail.html" class="product-link">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSPAwA59vmoTBvyJ8tKSeYwNq_cPndddo1ZUg&s" alt="Woven Cotton Scarf" class="product-image">
                             <div class="product-info">
                                <h3 class="product-title">Woven Cotton Scarf</h3>
                                <p class="product-artisan">by Artisan Name B</p>
                                <p class="product-price">₹60.00</p>
                            </div>
                        </a>
                         <button class="btn btn-secondary btn-sm add-to-cart-btn" data-product-id="2">Add to Cart</button>
                    </article>

                    <!-- Product Card Example 3 -->
                    <article class="product-card">
                         <a href="product-detail.html" class="product-link">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKu4BDsrxsdpSUt8KNqWFVWhrHmbUzZ2aPaw&s" alt="Silver Leaf Earrings" class="product-image">
                            <div class="product-info">
                                <h3 class="product-title">Silver Leaf Earrings</h3>
                                <p class="product-artisan">by Artisan Name C</p>
                                <p class="product-price">₹85.00</p>
                            </div>
                        </a>
                        <button class="btn btn-secondary btn-sm add-to-cart-btn" data-product-id="3">Add to Cart</button>
                    </article>

                    <!-- Product Card Example 4 -->
                    <article class="product-card">
                        <a href="product-detail.html" class="product-link">
                           <img src="https://www.kashmirbox.com/cdn/shop/files/kb6155_1.jpg?v=1740553289" alt="Hand Carved Bowl" class="product-image">
                           <div class="product-info">
                               <h3 class="product-title">Hand Carved Bowl</h3>
                               <p class="product-artisan">by Artisan Name D</p>
                               <p class="product-price">₹120.00</p>
                           </div>
                       </a>
                       <button class="btn btn-secondary btn-sm add-to-cart-btn" data-product-id="4">Add to Cart</button>
                   </article>

                    <!-- More product cards here -->
                </div>
                <div class="text-center view-all-link">
                    <a href="shop-all.html" class="btn btn-outline">View All Products</a>
                </div>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF FEATURED PRODUCTS SECTION            -->
        <!-- ============================================= -->


        <!-- ============================================= -->
        <!-- ARTISAN SPOTLIGHT / MISSION SECTION         -->
        <!-- ============================================= -->
        <section class="mission-spotlight section-padding">
            <div class="container mission-container">
                 <div class="mission-image">
                     <img src="https://clayandglaze.in/wp-content/uploads/slider/cache/d49b99a3a4348bc92b5caa7f09d19aa0/045A0758-min-scaled.jpg" alt="Artisan working on crafts">
                </div>
                 <div class="mission-text">
                    <h2 class="section-title">Connecting Community & Craft</h2>
                    <p>We believe in the power of handmade. Our platform connects you directly with talented local artisans, fostering creativity and supporting small businesses right in your neighborhood.</p>
                    <p>Every piece tells a story. Discover unique items made with skill, care, and a passion for quality.</p>
                    <a href="artisans.html" class="btn btn-primary">Meet the Artisans</a>
                 </div>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF ARTISAN SPOTLIGHT / MISSION SECTION  -->
        <!-- ============================================= -->

    </main>
    <!-- ============================================= -->
    <!-- END OF MAIN CONTENT AREA                      -->
    <!-- ============================================= -->


    <!-- ============================================= -->
    <!-- FOOTER SECTION                                -->
    <!-- ============================================= -->
    <footer class="site-footer section-padding">
        <div class="container footer-container">
            <div class="footer-column about-column">
                <h4 class="footer-heading">Artisan Collective</h4>
                <p>Your source for authentic, locally crafted goods. Supporting artisans and celebrating creativity.</p>
                 <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                 </div>
            </div>

            <div class="footer-column links-column">
                 <h4 class="footer-heading">Quick Links</h4>
                 <ul class="footer-list">
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="shop-all.html">Shop All</a></li>
                    <li><a href="artisans.html">Our Artisans</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="sell-with-us.html">Sell With Us</a></li>
                 </ul>
            </div>

            <div class="footer-column links-column">
                 <h4 class="footer-heading">Customer Service</h4>
                 <ul class="footer-list">
                    <li><a href="faq.html">FAQ</a></li>
                    <li><a href="shipping.html">Shipping & Returns</a></li>
                    <li><a href="privacy.html">Privacy Policy</a></li>
                    <li><a href="terms.html">Terms & Conditions</a></li>
                 </ul>
            </div>

             <div class="footer-column newsletter-column">
                <h4 class="footer-heading">Stay Connected</h4>
                <p>Get updates on new arrivals and special promotions.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" aria-label="Enter your email for newsletter signup">
                    <button type="submit" class="btn btn-primary btn-sm">Subscribe</button>
                </form>
            </div>

        </div>
         <div class="container copyright-container">
             <p class="copyright-text">© <span id="current-year"></span> Artisan Collective. All Rights Reserved.</p>
         </div>
    </footer>
    <!-- ============================================= -->
    <!-- END OF FOOTER SECTION                         -->
    <!-- ============================================= -->

    <script src="js/main.js"></script>
</body>
</html>