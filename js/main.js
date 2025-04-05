document.addEventListener('DOMContentLoaded', () => {


    const currentYearSpan = document.getElementById('current-year');
    if (currentYearSpan) {
        currentYearSpan.textContent = new Date().getFullYear();
    }

    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const primaryNav = document.getElementById('primary-navigation');
    const body = document.body;

    if (mobileNavToggle && primaryNav) {
        mobileNavToggle.addEventListener('click', () => {
            
            const isExpanded = primaryNav.classList.contains('active'); 
            primaryNav.classList.toggle('active');

            mobileNavToggle.classList.toggle('active');

            body.classList.toggle('no-scroll');

           
            mobileNavToggle.setAttribute('aria-expanded', !isExpanded);
            primaryNav.setAttribute('aria-expanded', !isExpanded); 

             const icon = mobileNavToggle.querySelector('i');
             if(icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }


     const searchToggle = document.querySelector('.search-toggle');
     const searchBar = document.querySelector('.search-bar');

     if(searchToggle && searchBar) {
         searchToggle.addEventListener('click', () => {
             const isVisible = searchBar.style.display === 'block';
             searchBar.style.display = isVisible ? 'none' : 'block';
             if(!isVisible) {
                 searchBar.querySelector('input[type="search"]').focus();
             }
         });

         // Close search if clicked outside
         document.addEventListener('click', (event) => {
             if (!searchBar.contains(event.target) && !searchToggle.contains(event.target) && searchBar.style.display === 'block') {
                 searchBar.style.display = 'none';
             }
         });
     }


    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartCountSpan = document.querySelector('.cart-count');
    let cartItemCount = 0; // Dummy count

    addToCartButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const productId = event.target.dataset.productId;
            console.log("Add to cart button clicked for product ID:", productId);

            cartItemCount++;
            if (cartCountSpan) {
                cartCountSpan.textContent = cartItemCount;
                cartCountSpan.style.display = 'flex'; // Show count
            }

            // Change button text briefly
            button.textContent = 'Added!';
            button.disabled = true;
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.disabled = false;
            }, 1500);
        });
    });

    // Hide cart count if initially zero (won't be updated by PHP yet)
    if (cartCountSpan && cartItemCount === 0) {
         cartCountSpan.style.display = 'none';
    }


}); 