document.addEventListener('DOMContentLoaded', () => {
    console.log("Cart page JS loaded.");

    
    const quantityInputs = document.querySelectorAll('.quantity-input input[type="number"]');
    const quantityButtons = document.querySelectorAll('.quantity-input .qty-btn');

    quantityButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const input = e.target.closest('.quantity-input').querySelector('input');
            let currentValue = parseInt(input.value);
            if (e.target.classList.contains('plus')) {
                currentValue++;
            } else if (e.target.classList.contains('minus')) {
                currentValue = Math.max(1, currentValue - 1); // Prevent going below 1
            }
            input.value = currentValue;
            console.log("Quantity changed to:", currentValue, "(Placeholder - needs AJAX)");
         
        });
    });


     const removeButtons = document.querySelectorAll('.remove-item-btn');
     removeButtons.forEach(button => {
         button.addEventListener('click', (e) => {
             const cartItemRow = e.target.closest('.cart-item');
             console.log("Remove button clicked for item:", cartItemRow.querySelector('.cart-item-title').textContent, "(Placeholder - needs AJAX)");
           
             cartItemRow.style.opacity = '0.5'; // Visual feedback placeholder
             
         });
     });

});