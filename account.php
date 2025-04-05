<?php
// account.php
session_start();

// Check if the user is logged in, if not then redirect to login page
// if (!isset($_SESSION["user_id"])) {
//     $_SESSION['error_message'] = "Please log in to access your account.";
//     header("location: login.php");
//     exit;
// }

// Include necessary files
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/includes/db_connect.php';

$user_id = 1;
$orders = []; // Initialize orders array
$addresses = [
    'billing' => null,
    'shipping' => null
]; // Initialize address array
$user_data = []; // Initialize user data array

// --- Fetch User's Orders ---
$pdo = getDbConnection();
if ($pdo) {
    try {
        // Fetch basic order info
        $sql_orders = "SELECT order_id, order_date, order_status, total_amount
                       FROM orders
                       WHERE user_id = :user_id
                       ORDER BY order_date DESC";
        $stmt_orders = $pdo->prepare($sql_orders);
        $stmt_orders->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_orders->execute();
        $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

        $sql_addresses = "SELECT * FROM user_addresses WHERE user_id = :user_id";
        $stmt_addresses = $pdo->prepare($sql_addresses);
        $stmt_addresses->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_addresses->execute();
        $fetched_addresses = $stmt_addresses->fetchAll(PDO::FETCH_ASSOC);

        $sql_user = "SELECT first_name, last_name, display_name, email FROM users WHERE user_id = :user_id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT); // $user_id comes from $_SESSION['user_id']
        $stmt_user->execute();
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC); // Fetches the data into $user_data array

        // Optional: Fetch item count for each order (more complex query)
        // You could do this in a separate query loop or join, but for display simplicity,
        // we'll stick to the basic info for now. The "X items" can be added later.
        // Organize fetched addresses by type
        foreach ($fetched_addresses as $addr) {
            if ($addr['address_type'] === 'billing') {
                $addresses['billing'] = $addr;
            } elseif ($addr['address_type'] === 'shipping') {
                $addresses['shipping'] = $addr;
            }
        }

    } catch (PDOException $e) {
        error_log("Database Error fetching orders for user {$user_id}: " . $e->getMessage());
        // Handle error gracefully, maybe set an error message variable to display
        $order_fetch_error = "Could not retrieve order history at this time.";
    }
} else {
    $order_fetch_error = "Database connection error.";
}

// --- Fetch User Data (for account details panel - example) ---
$user_data = [];
if($pdo) {
    try {
        $sql_user = "SELECT first_name, last_name, display_name, email FROM users WHERE user_id = :user_id";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_user->execute();
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
        // Update session display name if it differs from DB (optional consistency check)
        if ($user_data && ($_SESSION['display_name'] ?? '') !== $user_data['display_name']) {
             $_SESSION['display_name'] = $user_data['display_name'];
        }

    } catch (PDOException $e) {
         error_log("Database Error fetching user data for user {$user_id}: " . $e->getMessage());
         // Handle error
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Artisan Collective</title>

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
    <!-- HEADER & NAVIGATION (Check Login State)     -->
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
                    <a href="account.php" class="action-link active" aria-label="My Account"> <i class="fas fa-user"></i> </a>
                <?php else: ?>
                    <a href="login.php" class="action-link" aria-label="Login / Register"> <i class="fas fa-user"></i> </a>
                <?php endif; ?>
                <a href="cart.php" class="action-link" aria-label="Shopping Cart"> <i class="fas fa-shopping-bag"></i> <span class="cart-count" aria-hidden="true"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span> </a>
            </div>
            <div class="search-bar"> <form action="search.php" method="get"> <input type="search" name="query" placeholder="Search products & artisans..." aria-label="Search products and artisans"> <button type="submit">Search</button> </form> </div>
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
                        <li><span aria-current="page">My Account</span></li>
                    </ol>
                </nav>
                <h1 class="page-title">My Account</h1>
                 <!-- Optional: Display username here if needed -->
                 <!-- <p style="margin-top: 5px;">Welcome back, <?php //echo htmlspecialchars($_SESSION['display_name'] ?? 'User'); ?>!</p> -->
            </div>
        </section>
        <!-- ============================================= -->
        <!-- END OF BREADCRUMBS & TITLE                  -->
        <!-- ============================================= -->


        <!-- ============================================= -->
        <!-- ACCOUNT AREA                                -->
        <!-- ============================================= -->
        <section class="account-section section-padding">
            <div class="container account-container">

                <aside class="account-navigation">
                    <h2 class="account-nav-title">Navigation</h2>
                    <ul>
                        <!-- Dashboard link removed -->
                        <li><a href="#orders" class="account-nav-link active">Orders</a></li> <!-- Orders is now active by default -->
                        <li><a href="#addresses" class="account-nav-link">Addresses</a></li>
                        <li><a href="#settings" class="account-nav-link">Account Details</a></li>
                        <li><a href="logout.php" class="account-nav-link">Logout</a></li>
                    </ul>
                </aside>

                <section class="account-content">

                    <!-- DASHBOARD PANEL REMOVED -->

                    <!-- ORDERS PANEL (NOW DEFAULT ACTIVE) -->
                    <div id="orders" class="account-panel active"> <!-- Removed style="display: none;" and added active class -->
                        <h2 class="account-panel-title">Orders</h2>

                        <?php if (isset($order_fetch_error)): ?>
                            <p class="error"><?php echo htmlspecialchars($order_fetch_error); ?></p>
                        <?php elseif (empty($orders)): ?>
                            <p>You have placed no orders yet.</p>
                            <a href="shop-all.php" class="btn btn-primary" style="margin-top: 10px;">Browse Products</a>
                        <?php else: ?>
                            <p>Here you can see your past orders.</p>
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($order['order_status'])); ?></td>
                                        <td><?php echo '₹' . number_format($order['total_amount'], 2); // Assuming INR symbol ?>
                                        </td>
                                        <td>
                                            <a href="order-details.php?id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn btn-outline btn-sm">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div> <!-- End Orders Panel -->


                    <!-- ADDRESSES PANEL -->
                    <div id="addresses" class="account-panel" style="display: none;"> <!-- Still hidden initially -->
                        <h2 class="account-panel-title">Addresses</h2>

                        <?php if (isset($address_fetch_error)): ?>
                            <p class="error"><?php echo htmlspecialchars($address_fetch_error); ?></p>
                        <?php else: ?>
                            <p>The following addresses will be used on the checkout page by default.</p>
                            <div class="address-grid">
                                <div class="address-column">
                                    <h3>Billing Address</h3>
                                    <?php if (!empty($addresses['billing'])): $addr = $addresses['billing']; ?>
                                        <address>
                                            <?php echo htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']); ?><br>
                                            <?php if (!empty($addr['company'])) echo htmlspecialchars($addr['company']) . '<br>'; ?>
                                            <?php echo htmlspecialchars($addr['street_address_1']); ?><br>
                                            <?php if (!empty($addr['street_address_2'])) echo htmlspecialchars($addr['street_address_2']) . '<br>'; ?>
                                            <?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['state']) . ' ' . htmlspecialchars($addr['postcode']); ?><br>
                                            <?php echo htmlspecialchars($addr['country_code']); ?>
                                        </address>
                                    <?php else: ?>
                                        <address>You have not set a billing address yet.</address>
                                    <?php endif; ?>
                                    <button class="btn btn-outline btn-sm edit-address-btn" data-address-type="billing">
                                        <?php echo !empty($addresses['billing']) ? 'Edit Billing Address' : 'Add Billing Address'; ?>
                                    </button>
                                </div>
                                <div class="address-column">
                                    <h3>Shipping Address</h3>
                                     <?php if (!empty($addresses['shipping'])): $addr = $addresses['shipping']; ?>
                                        <address>
                                             <?php echo htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']); ?><br>
                                            <?php if (!empty($addr['company'])) echo htmlspecialchars($addr['company']) . '<br>'; ?>
                                            <?php echo htmlspecialchars($addr['street_address_1']); ?><br>
                                            <?php if (!empty($addr['street_address_2'])) echo htmlspecialchars($addr['street_address_2']) . '<br>'; ?>
                                            <?php echo htmlspecialchars($addr['city']) . ', ' . htmlspecialchars($addr['state']) . ' ' . htmlspecialchars($addr['postcode']); ?><br>
                                            <?php echo htmlspecialchars($addr['country_code']); ?>
                                        </address>
                                     <?php else: ?>
                                        <address>You have not set a shipping address yet.</address>
                                    <?php endif; ?>
                                    <button class="btn btn-outline btn-sm edit-address-btn" data-address-type="shipping">
                                         <?php echo !empty($addresses['shipping']) ? 'Edit Shipping Address' : 'Add Shipping Address'; ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden Form for Editing Addresses (Shown by JS) -->
                            <div id="address-edit-form-container" style="display: none; margin-top: 30px;">
                                <h3 id="address-form-title">Edit Address</h3>
                                <form id="address-form" action="php/update-address.php" method="POST" class="site-form">
                                    <input type="hidden" name="address_type" id="address_type" value="">
                                    <div class="form-row">
                                         <div class="form-group form-group-half">
                                            <label for="address-first-name">First Name <span class="required">*</span></label>
                                            <input type="text" id="address-first-name" name="first_name" required class="form-control">
                                        </div>
                                         <div class="form-group form-group-half">
                                            <label for="address-last-name">Last Name <span class="required">*</span></label>
                                            <input type="text" id="address-last-name" name="last_name" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address-company">Company Name (Optional)</label>
                                        <input type="text" id="address-company" name="company" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="address-street1">Street Address <span class="required">*</span></label>
                                        <input type="text" id="address-street1" name="street1" required class="form-control" placeholder="House number and street name">
                                    </div>
                                    <div class="form-group">
                                         <label for="address-street2" class="sr-only">Street Address Line 2</label>
                                         <input type="text" id="address-street2" name="street2" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                     </div>
                                     <div class="form-group">
                                        <label for="address-city">Town / City <span class="required">*</span></label>
                                        <input type="text" id="address-city" name="city" required class="form-control">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group form-group-half">
                                           <label for="address-state">State / County <span class="required">*</span></label>
                                           <input type="text" id="address-state" name="state" required class="form-control">
                                       </div>
                                       <div class="form-group form-group-half">
                                           <label for="address-zip">Postcode / ZIP <span class="required">*</span></label>
                                           <input type="text" id="address-zip" name="zip" required class="form-control">
                                       </div>
                                   </div>
                                    <div class="form-group">
                                        <label for="address-country">Country <span class="required">*</span></label>
                                        <select id="address-country" name="country" required class="form-control">
                                            <option value="">Select a country...</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="GB">United Kingdom</option>
                                            <option value="IN">India</option> <!-- Added India example -->
                                            <!-- Add more countries -->
                                        </select>
                                    </div>
                                     <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Save Address</button>
                                        <button type="button" class="btn btn-outline cancel-edit-btn">Cancel</button>
                                    </div>
                                    <div id="address-status" role="alert" aria-live="polite"></div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div> <!-- End Addresses Panel -->


                <!-- ACCOUNT DETAILS PANEL -->
                <div id="settings" class="account-panel" style="display: none;"> <!-- Still hidden initially -->
                    <h2 class="account-panel-title">Account Details</h2>
                    <?php // Check if there was an error fetching data ?>
                    <?php if (isset($user_data_error)): ?>
                        <p class="error"><?php echo htmlspecialchars($user_data_error); ?></p>
                    <?php // Check if data was fetched but is somehow empty (e.g., user deleted?) ?>
                    <?php elseif (empty($user_data)): ?>
                        <p class="error">Could not load user details.</p>
                    <?php // If no errors and data exists, display the form ?>
                    <?php else: ?>
                        <form id="account-details-form" action="php/update-account.php" method="POST" class="site-form">
                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="account-first-name">First Name <span class="required">*</span></label>
                                    <?php // Use the fetched data in the 'value' attribute ?>
                                    <input type="text" id="account-first-name" name="first_name" required class="form-control" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>">
                                </div>
                                <div class="form-group form-group-half">
                                    <label for="account-last-name">Last Name <span class="required">*</span></label>
                                    <?php // Use the fetched data in the 'value' attribute ?>
                                    <input type="text" id="account-last-name" name="last_name" required class="form-control" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="account-display-name">Display Name <span class="required">*</span></label>
                                <?php // Use the fetched data in the 'value' attribute ?>
                                <input type="text" id="account-display-name" name="display_name" required class="form-control" value="<?php echo htmlspecialchars($user_data['display_name'] ?? ''); ?>">
                                <small>This will be how your name will be displayed in the account section and in reviews</small>
                            </div>
                            <div class="form-group">
                                <label for="account-email">Email Address <span class="required">*</span></label>
                                <?php // Use the fetched data in the 'value' attribute ?>
                                <input type="email" id="account-email" name="email" required class="form-control" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
                            </div>
                            <fieldset class="password-change-fieldset">
                                <legend>Password change</legend>
                                <div class="form-group">
                                    <label for="account-password-current">Current password (leave blank to leave unchanged)</label>
                                    <input type="password" id="account-password-current" name="password_current" class="form-control" autocomplete="current-password">
                                </div>
                                <div class="form-group">
                                    <label for="account-password-new">New password (leave blank to leave unchanged)</label>
                                    <input type="password" id="account-password-new" name="password_new" class="form-control" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label for="account-password-confirm">Confirm new password</label>
                                    <input type="password" id="account-password-confirm" name="password_confirm" class="form-control" autocomplete="new-password">
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                            <div id="account-status" role="alert" aria-live="polite"></div>
                        </form>
                    <?php endif; ?>
                </div> <!-- End Settings Panel -->

            </div> <!-- End account-container -->
        </section>
        <!-- ============================================= -->
        <!-- END OF ACCOUNT AREA                         -->
        <!-- ============================================= -->


    </main>
    <!-- ============================================= -->
    <!-- END OF MAIN CONTENT AREA                      -->
    <!-- ============================================= -->


    <!-- ============================================= -->
    <!-- FOOTER SECTION (Check Links)                -->
    <!-- ============================================= -->
    <footer class="site-footer section-padding">
        <div class="container footer-container">
             <div class="footer-column about-column"> <h4 class="footer-heading">Artisan Collective</h4> <p>Your source for authentic, locally crafted goods. Supporting artisans and celebrating creativity.</p> <div class="social-links"> <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a> <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a> <a href="#" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a> </div> </div>
             <div class="footer-column links-column"> <h4 class="footer-heading">Quick Links</h4> <ul class="footer-list"> <li><a href="about.php">About Us</a></li> <li><a href="shop-all.php">Shop All</a></li> <li><a href="artisans.php">Our Artisans</a></li> <li><a href="contact.php">Contact Us</a></li> <li><a href="sell-with-us.php">Sell With Us</a></li> </ul> </div>
             <div class="footer-column links-column"> <h4 class="footer-heading">Customer Service</h4> <ul class="footer-list"> <li><a href="faq.php">FAQ</a></li> <li><a href="shipping.php">Shipping & Returns</a></li> <li><a href="privacy.php">Privacy Policy</a></li> <li><a href="terms.php">Terms & Conditions</a></li> </ul> </div>
             <div class="footer-column newsletter-column"> <h4 class="footer-heading">Stay Connected</h4> <p>Get updates on new arrivals and special promotions.</p> <form class="newsletter-form" action="php/subscribe.php" method="post"> <input type="email" name="email" placeholder="Enter your email" required aria-label="Enter your email for newsletter signup"> <button type="submit" class="btn btn-primary btn-sm">Subscribe</button> </form> </div>
        </div>
         <div class="container copyright-container">
             <p class="copyright-text">© <span id="current-year"></span> Artisan Collective. All Rights Reserved.</p>
         </div>
    </footer>
    <!-- ============================================= -->
    <!-- END OF FOOTER SECTION                         -->
    <!-- ============================================= -->

    <script src="js/main.js"></script>
    <script src="js/account.js"></script>
</body>
</html>