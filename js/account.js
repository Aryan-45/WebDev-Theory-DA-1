document.addEventListener('DOMContentLoaded', () => {
    console.log("Account page JS loaded.");

    const accountNavLinks = document.querySelectorAll('.account-nav-link');
    const accountPanels = document.querySelectorAll('.account-panel');
    const editAddressBtns = document.querySelectorAll('.edit-address-btn');
    const addressFormContainer = document.getElementById('address-edit-form-container');
    const addressForm = document.getElementById('address-form');
    const addressFormTitle = document.getElementById('address-form-title');
    const addressTypeInput = document.getElementById('address_type');
    const cancelEditBtn = document.querySelector('.cancel-edit-btn');
    const addressStatusDiv = document.getElementById('address-status');
    const accountStatusDiv = document.getElementById('account-status'); // For account details form

    // --- Tab Switching ---
    accountNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Prevent default only if it's linking to an ID (#)
            if (link.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetPanel = document.querySelector(targetId);

                // Remove active class from all links and panels
                accountNavLinks.forEach(l => l.classList.remove('active'));
                accountPanels.forEach(p => {
                    p.classList.remove('active');
                    p.style.display = 'none'; // Hide all panels
                });

                // Add active class to the clicked link and target panel
                link.classList.add('active');
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.style.display = 'block'; // Show the target panel

                     // If switching away from Addresses, hide the edit form and clear status
                    if (addressFormContainer && targetId !== '#addresses') {
                         addressFormContainer.style.display = 'none';
                         if(addressStatusDiv) addressStatusDiv.style.display = 'none';
                    }
                    // Clear account status if switching away from settings
                    if(accountStatusDiv && targetId !== '#settings'){
                         accountStatusDiv.style.display = 'none';
                    }
                }
                // Scroll to the top of the content area might be nice
                const contentArea = document.querySelector('.account-content');
                if(contentArea) {
                    // contentArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

            }
             // Allow default behavior for actual links like "Logout"
        });
    });

    // Function to trigger click on a nav link by href
    function showAccountPanel(panelId) {
        const targetLink = document.querySelector(`.account-nav-link[href="${panelId}"]`);
        if (targetLink) {
            targetLink.click();
        }
    }

    // Check URL hash on load to potentially open a specific tab
    if (window.location.hash) {
        const initialPanelId = window.location.hash;
        // Ensure the hash corresponds to a valid panel ID
        if (document.querySelector(initialPanelId) && document.querySelector(`.account-nav-link[href="${initialPanelId}"]`)) {
            showAccountPanel(initialPanelId);
        } else {
            // Default to dashboard if hash is invalid
            showAccountPanel('#dashboard');
        }
    }

    // Make links within dashboard text also switch tabs
    const dashboardLinks = document.querySelectorAll('#dashboard a[href^="#"]');
    dashboardLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const panelId = link.getAttribute('href');
            showAccountPanel(panelId);
        });
    });


    // --- Address Form Toggling ---
    if (editAddressBtns.length > 0 && addressFormContainer && addressForm) {
        editAddressBtns.forEach(button => {
            button.addEventListener('click', (e) => {
                const type = e.target.dataset.addressType; // 'billing' or 'shipping'
                addressFormTitle.textContent = `Edit ${type.charAt(0).toUpperCase() + type.slice(1)} Address`;
                addressTypeInput.value = type;

                // Clear previous status messages
                if(addressStatusDiv) addressStatusDiv.style.display = 'none';

                // --- IMPORTANT: Populate form with existing data ---
                // In a real app, you'd use AJAX here to fetch the current
                // address data for the given 'type' and populate the form fields.
                // Example placeholder:
                console.log(`Fetching data for ${type} address (Placeholder - Needs AJAX/PHP)`);
                // const currentAddressData = fetchAddressDataFromServer(type); // Hypothetical function
                // populateAddressForm(currentAddressData); // Hypothetical function
                // For now, just clear the form:
                 addressForm.reset(); // Simple reset for demo

                addressFormContainer.style.display = 'block';
                addressForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                // Focus the first input field
                addressForm.querySelector('input:not([type=hidden])').focus();
            });
        });

        if (cancelEditBtn) {
             cancelEditBtn.addEventListener('click', () => {
                 addressFormContainer.style.display = 'none';
                 if(addressStatusDiv) addressStatusDiv.style.display = 'none'; // Clear status on cancel
             });
         }
    }

    // --- Placeholder for Account/Address Form Submission (AJAX Recommended) ---
    // You would typically intercept the 'submit' event, prevent default,
    // use FormData or serialize the form, send via fetch/XHR to your PHP script,
    // handle the response (update UI, show success/error messages).

    function handleFormSubmission(formElement, statusDiv) {
        if (!formElement || !statusDiv) return;

        formElement.addEventListener('submit', (e) => {
            e.preventDefault(); // Prevent default synchronous submission
            console.log(`${formElement.id} submitted (Placeholder - Needs AJAX)`);

            statusDiv.style.display = 'none'; // Hide previous status
            statusDiv.className = ''; // Clear status classes
            const submitButton = formElement.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';

            const formData = new FormData(formElement);
            const actionUrl = formElement.action;

            // Simulate AJAX call
            setTimeout(() => {
                // --- Replace this timeout with actual fetch() call ---
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                    // Add headers if needed, e.g., for JSON:
                    // headers: { 'Accept': 'application/json' }
                })
                .then(response => {
                    if (!response.ok) {
                        // Handle HTTP errors (like 404, 500)
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    // Check content type if expecting JSON
                    const contentType = response.headers.get("content-type");
                     if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json(); // Parse JSON response from PHP
                    } else {
                        return response.text(); // Handle plain text or HTML response
                    }
                })
                .then(data => {
                     console.log("Server Response:", data);
                     // Assuming PHP returns { success: true/false, message: "...", updatedData: {...} }
                     if (data && data.success) {
                         statusDiv.textContent = data.message || 'Changes saved successfully!';
                         statusDiv.className = 'success'; // Add success class styling
                         statusDiv.style.display = 'block';

                         // Optionally update the displayed address/details on the page
                         if (formElement.id === 'address-form' && data.updatedData) {
                             // updateAddressDisplay(data.updatedData.type, data.updatedData); // Hypothetical function
                             addressFormContainer.style.display = 'none'; // Hide form on success
                         } else if (formElement.id === 'account-details-form' && data.updatedData) {
                             // updateUserDisplay(data.updatedData); // Hypothetical function
                             // Clear password fields after successful update
                             formElement.querySelector('#account-password-current').value = '';
                             formElement.querySelector('#account-password-new').value = '';
                             formElement.querySelector('#account-password-confirm').value = '';
                         }

                     } else {
                         // Show error message from server or a generic one
                         statusDiv.textContent = data.message || 'An error occurred. Please try again.';
                         statusDiv.className = 'error'; // Add error class styling
                         statusDiv.style.display = 'block';
                     }
                })
                .catch(error => {
                    console.error('Submission Error:', error);
                    statusDiv.textContent = 'A network error occurred. Please check your connection and try again.';
                    statusDiv.className = 'error';
                    statusDiv.style.display = 'block';
                })
                .finally(() => {
                    // Re-enable button and restore text regardless of outcome
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                });
                // --- End of fetch() block ---

            }, 1000); // Simulate network delay

        });
    }

    // Attach submission handlers
    handleFormSubmission(addressForm, addressStatusDiv);
    handleFormSubmission(document.getElementById('account-details-form'), accountStatusDiv);

});