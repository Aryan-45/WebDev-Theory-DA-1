<?php
// register.php
session_start(); // Start session to access potential error messages

// If user is already logged in, redirect them away from registration
if (isset($_SESSION['user_id'])) {
    header('Location: account.php'); // Redirect to account page
    exit;
}

require_once __DIR__ . '/php/config.php'; // Include config if needed (e.g., for SITE_URL)

// Check for registration status messages stored in session
$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;
$form_data = $_SESSION['form_data'] ?? []; // Preserve form data on error

// Clear messages after displaying them once
unset($_SESSION['error_message'], $_SESSION['success_message'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Artisan Collective</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Our Stylesheet -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <!-- ============================================= -->
    <!-- HEADER & NAVIGATION (Modified for Login State) -->
    <!-- ============================================= -->
     <header class="site-header">
        <div class="container header-container">
            <div class="logo"> <a href="index.php">Artisan Collective</a> </div>
            <nav class="main-navigation" aria-label="Main Navigation">
                 <button class="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false"> <span class="sr-only">Menu</span> <i class="fas fa-bars hamburger-icon"></i> </button>
                <ul id="primary-navigation" class="nav-list">
                    <li class="nav-item dropdown"> <a href="shop-all.php" class="nav-link">Shop <i class="fas fa-chevron-down fa-xs"></i></a> <ul class="dropdown-menu"> <li><a href="shop-all.php">Shop All</a></li> <li><hr class="dropdown-divider"></li> <li><a href="category-pottery.php">Pottery</a></li> <li><a href="category-textiles.php">Textiles</a></li> <li><a href="category-jewelry.php">Jewelry</a></li> <li><a href="category-woodwork.php">Woodwork</a></li> </ul> </li>
                    <li class="nav-item"><a href="artisans.php" class="nav-link">Artisans</a></li>
                    <li class="nav-item"><a href="about.php" class="nav-link">Our Story</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <button class="search-toggle" aria-label="Search"> <i class="fas fa-search"></i> </button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="account.php" class="action-link" aria-label="My Account"> <i class="fas fa-user"></i> </a>
                <?php else: ?>
                    <a href="login.php" class="action-link" aria-label="Login / Register"> <i class="fas fa-user"></i> </a>
                <?php endif; ?>
                <a href="cart.php" class="action-link" aria-label="Shopping Cart"> <i class="fas fa-shopping-bag"></i> <span class="cart-count" aria-hidden="true"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span> </a>
            </div>
            <div class="search-bar"> <form action="/search" method="get"> <input type="search" name="query" placeholder="Search products & artisans..." aria-label="Search products and artisans"> <button type="submit">Search</button> </form> </div>
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
        <!-- BREADCRUMBS & TITLE                         -->
        <!-- ============================================= -->
        <section class="page-header section-padding bg-medium">
            <div class="container">
                <nav aria-label="breadcrumb" class="breadcrumbs">
                    <ol>
                        <li><a href="index.php">Home</a></li>
                        <li><span aria-current="page">Register</span></li>
                    </ol>
                </nav>
                <h1 class="page-title">Create Account</h1>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF BREADCRUMBS & TITLE                  -->
        <!-- ============================================= -->

        <!-- ============================================= -->
        <!-- REGISTRATION FORM SECTION                   -->
        <!-- ============================================= -->
        <section class="auth-section section-padding">
            <div class="container">
                <div class="auth-form-container">
                    <h2>Register</h2>
                    <p>Create your account to enjoy faster checkout and manage your orders.</p>

                    <!-- Display feedback messages -->
                    <?php if ($error_message): ?>
                        <div id="auth-status" class="error" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): // Should not happen often here, usually redirects ?>
                        <div id="auth-status" class="success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>


                    <form id="register-form" action="php/process-register.php" method="POST" class="site-form">
                         <!-- Honeypot -->
                         <div class="form-group visually-hidden">
                             <label for="reg-honeypot">Leave empty</label>
                             <input type="text" id="reg-honeypot" name="honeypot" tabindex="-1" autocomplete="off">
                         </div>

                         <div class="form-row">
                             <div class="form-group form-group-half">
                                <label for="reg-first-name">First Name <span class="required">*</span></label>
                                <input type="text" id="reg-first-name" name="first_name" required class="form-control" value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>">
                            </div>
                             <div class="form-group form-group-half">
                                <label for="reg-last-name">Last Name <span class="required">*</span></label>
                                <input type="text" id="reg-last-name" name="last_name" required class="form-control" value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reg-display-name">Display Name <span class="required">*</span></label>
                            <input type="text" id="reg-display-name" name="display_name" required class="form-control" placeholder="How your name appears in reviews" value="<?php echo htmlspecialchars($form_data['display_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="reg-email">Email Address <span class="required">*</span></label>
                            <input type="email" id="reg-email" name="email" required class="form-control" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="reg-password">Password <span class="required">*</span></label>
                            <input type="password" id="reg-password" name="password" required class="form-control" pattern=".{8,}" title="Password must be at least 8 characters long.">
                            <small>Must be at least 8 characters long.</small>
                        </div>

                        <div class="form-group">
                            <label for="reg-password-confirm">Confirm Password <span class="required">*</span></label>
                            <input type="password" id="reg-password-confirm" name="password_confirm" required class="form-control">
                        </div>

                        <div class="form-group">
                           <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>

                        <p class="auth-switch-link text-center">
                            Already have an account? <a href="login.php">Log in here</a>
                        </p>
                    </form>
                </div>
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF REGISTRATION FORM SECTION            -->
        <!-- ============================================= -->
    </main>
    <!-- ============================================= -->
    <!-- END OF MAIN CONTENT AREA                      -->
    <!-- ============================================= -->

    <!-- ============================================= -->
    <!-- FOOTER SECTION (Same as index.php)         -->
    <!-- ============================================= -->
    <footer class="site-footer section-padding">
        <!-- Footer content copied from index.php -->
         <div class="container footer-container">
             <div class="footer-column about-column"> <h4 class="footer-heading">Artisan Collective</h4> <p>Your source for authentic, locally crafted goods. Supporting artisans and celebrating creativity.</p> <div class="social-links"> <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a> <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a> <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a> </div> </div>
             <div class="footer-column links-column"> <h4 class="footer-heading">Quick Links</h4> <ul class="footer-list"> <li><a href="about.php">About Us</a></li> <li><a href="shop-all.php">Shop All</a></li> <li><a href="artisans.php">Our Artisans</a></li> <li><a href="contact.php">Contact Us</a></li> <li><a href="sell-with-us.php">Sell With Us</a></li> </ul> </div>
             <div class="footer-column links-column"> <h4 class="footer-heading">Customer Service</h4> <ul class="footer-list"> <li><a href="faq.php">FAQ</a></li> <li><a href="shipping.php">Shipping & Returns</a></li> <li><a href="privacy.php">Privacy Policy</a></li> <li><a href="terms.php">Terms & Conditions</a></li> </ul> </div>
             <div class="footer-column newsletter-column"> <h4 class="footer-heading">Stay Connected</h4> <p>Get updates on new arrivals and special promotions.</p> <form class="newsletter-form"> <input type="email" placeholder="Enter your email" aria-label="Enter your email for newsletter signup"> <button type="submit" class="btn btn-primary btn-sm">Subscribe</button> </form> </div>
        </div>
         <div class="container copyright-container">
             <p class="copyright-text">© <span id="current-year"></span> Artisan Collective. All Rights Reserved.</p>
         </div>
    </footer>
    <!-- ============================================= -->
    <!-- END OF FOOTER SECTION                         -->
    <!-- ============================================= -->

    <script src="js/main.js"></script>
    <!-- No specific JS needed for registration page currently -->
</body>
</html>