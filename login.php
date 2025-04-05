<?php

session_start();


if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit;
}

require_once __DIR__ . '/php/config.php';

$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null; 
$form_data = $_SESSION['form_data'] ?? []; 


unset($_SESSION['error_message'], $_SESSION['success_message'], $_SESSION['form_data']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Artisan Collective</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="css/styles.css">
</head>
<body>


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
                    <a href="login.php" class="action-link active" aria-label="Login / Register"> <i class="fas fa-user"></i> </a>
                <?php endif; ?>
                <a href="cart.php" class="action-link" aria-label="Shopping Cart"> <i class="fas fa-shopping-bag"></i> <span class="cart-count" aria-hidden="true"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span> </a>
            </div>
            <div class="search-bar"> <form action="/search" method="get"> <input type="search" name="query" placeholder="Search products & artisans..." aria-label="Search products and artisans"> <button type="submit">Search</button> </form> </div>
        </div>
    </header>

    <main>

        <section class="page-header section-padding bg-medium">
            <div class="container">
                <nav aria-label="breadcrumb" class="breadcrumbs">
                    <ol>
                        <li><a href="index.php">Home</a></li>
                        <li><span aria-current="page">Login</span></li>
                    </ol>
                </nav>
                <h1 class="page-title">Login</h1>
            </div>
        </section>

        <section class="auth-section section-padding">
            <div class="container">
                 <div class="auth-form-container">
                     <h2>Welcome Back!</h2>
                     <p>Log in to access your account dashboard and orders.</p>

                     
                    <?php if ($error_message): ?>
                        <div id="auth-status" class="error" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <?php if ($success_message): ?>
                        <div id="auth-status" class="success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>


                     <form id="login-form" action="php/process-login.php" method="POST" class="site-form">
                         
                         <div class="form-group visually-hidden">
                             <label for="login-honeypot">Leave empty</label>
                             <input type="text" id="login-honeypot" name="honeypot" tabindex="-1" autocomplete="off">
                         </div>

                         <div class="form-group">
                             <label for="login-email">Email Address <span class="required">*</span></label>
                             <input type="email" id="login-email" name="email" required class="form-control" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                         </div>

                         <div class="form-group">
                             <label for="login-password">Password <span class="required">*</span></label>
                             <input type="password" id="login-password" name="password" required class="form-control">
                         </div>

                         <div class="form-group form-meta">

                            <a href="forgot-password.php" class="forgot-password-link">Lost your password?</a>
                         </div>

                         <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Log In</button>
                         </div>

                         <p class="auth-switch-link text-center">
                             Don't have an account? <a href="register.php">Register here</a>
                         </p>
                     </form>
                 </div>
            </div>
        </section>

    </main>

    <footer class="site-footer section-padding">

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


    <script src="js/main.js"></script>

</body>
</html>