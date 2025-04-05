document.addEventListener('DOMContentLoaded', () => {
    console.log("Application page JS loaded.");

    const fileInput = document.getElementById('app-portfolio-images');
    const fileFeedback = document.getElementById('file-feedback');
    const form = document.getElementById('artisan-application-form');
    const applicationStatusDiv = document.getElementById('application-status');

    // --- Configuration ---
    const MAX_FILES = 5;
    const MAX_SIZE_MB = 2; // Max size per file
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
    // You could also add a total max size if desired
    // const TOTAL_MAX_SIZE_MB = 10;
    // const TOTAL_MAX_SIZE_BYTES = TOTAL_MAX_SIZE_MB * 1024 * 1024;
    const ALLOWED_TYPES = ['image/jpeg', 'image/png']; // MIME types

    // Attach listener to file input
    if (fileInput && fileFeedback) {
        fileInput.addEventListener('change', handleFileValidation);
    }

    // Attach listener to form submit
    if (form) {
        form.addEventListener('submit', (e) => {
            // Re-validate files on submit, prevent if invalid
            if (!handleFileValidation()) {
                e.preventDefault(); // Stop form submission
                // alert(`Please fix the errors in the file uploads before submitting.`); // Optional alert
                fileFeedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
                applicationStatusDiv.textContent = 'Please fix the errors in the file uploads.';
                applicationStatusDiv.className = 'error'; // Add error styling
                applicationStatusDiv.style.display = 'block';
                return; // Stop further processing
            }

            // --- Placeholder for AJAX Submission ---
             e.preventDefault(); // ALWAYS prevent default synchronous submission if using AJAX
             console.log('Application form submitted (Placeholder - Needs AJAX)');
             applicationStatusDiv.style.display = 'none'; // Hide previous status
             applicationStatusDiv.className = ''; // Clear status class
             const submitButton = form.querySelector('button[type="submit"]');
             const originalButtonText = submitButton.textContent;
             submitButton.disabled = true;
             submitButton.textContent = 'Submitting...';

             const formData = new FormData(form); // Automatically collects all fields including files

             // Simulate AJAX
             setTimeout(() => {
                // --- Replace with actual fetch() call ---
                 fetch(form.action, {
                     method: 'POST',
                     body: formData,
                     // IMPORTANT: Do NOT set 'Content-Type' header when sending FormData,
                     // the browser will set it correctly including the boundary.
                     headers: {
                        'Accept': 'application/json' // Tell PHP we prefer JSON back
                     }
                 })
                 .then(response => {
                     if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                     return response.json(); // Expecting JSON { success: true/false, message: "..." }
                 })
                 .then(data => {
                     console.log("Server Response:", data);
                     if (data && data.success) {
                         applicationStatusDiv.textContent = data.message || 'Application submitted successfully! We will review it and get back to you.';
                         applicationStatusDiv.className = 'success';
                         form.reset(); // Clear form on success
                         fileFeedback.textContent = ''; // Clear file feedback
                         fileFeedback.className = 'file-feedback';
                     } else {
                         applicationStatusDiv.textContent = data.message || 'An error occurred while submitting the application.';
                         applicationStatusDiv.className = 'error';
                     }
                 })
                 .catch(error => {
                     console.error('Submission Error:', error);
                     applicationStatusDiv.textContent = 'A network or server error occurred. Please try again later.';
                     applicationStatusDiv.className = 'error';
                 })
                 .finally(() => {
                     applicationStatusDiv.style.display = 'block';
                     submitButton.disabled = false;
                     submitButton.textContent = originalButtonText;
                     // Scroll to status message
                     applicationStatusDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                 });
                 // --- End of fetch() block ---
            }, 1500); // Simulate delay
            // --- End of AJAX Placeholder ---

        });
    }

    // --- File Validation Logic ---
    function handleFileValidation() {
        if (!fileInput || !fileFeedback) return true; // Skip if elements not found

        const files = fileInput.files;
        let errors = [];
        let totalSize = 0;
        fileFeedback.textContent = ''; // Clear previous feedback
        fileFeedback.className = 'file-feedback'; // Reset class

        // Check if required and no files selected (though browser might handle this first)
        if (files.length === 0 && fileInput.required) {
            // errors.push('At least one portfolio image is required.');
            // Let browser handle 'required' validation on submit for now.
            return true; // Don't show error just on change if empty initially
        }

        if (files.length > MAX_FILES) {
            errors.push(`You can upload a maximum of ${MAX_FILES} files. You selected ${files.length}.`);
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            totalSize += file.size;

            // Check Type
            if (!ALLOWED_TYPES.includes(file.type)) {
                errors.push(`File "${file.name}" is not allowed. Only JPG and PNG images please.`);
            }

            // Check Size
            if (file.size > MAX_SIZE_BYTES) {
                errors.push(`File "${file.name}" (${(file.size / 1024 / 1024).toFixed(1)} MB) is too large. Max ${MAX_SIZE_MB} MB per file.`);
            }
        }

        // Optional: Check total size
        // if (totalSize > TOTAL_MAX_SIZE_BYTES) {
        //     errors.push(`Total size of all files (${(totalSize / 1024 / 1024).toFixed(1)} MB) exceeds the limit of ${TOTAL_MAX_SIZE_MB} MB.`);
        // }


        if (errors.length > 0) {
            fileFeedback.innerHTML = errors.join('<br>'); // Display errors
            fileFeedback.classList.add('error'); // Add error styling
             // Optionally clear the file input's value if validation fails severely
             // fileInput.value = ''; // This resets the selection
            return false; // Indicates validation failed
        } else if (files.length > 0) {
            // Provide positive feedback if files are selected and valid
            fileFeedback.textContent = `${files.length} file(s) selected. Ready to upload!`;
            fileFeedback.classList.add('success'); // Add success styling
            return true; // Indicates validation passed
        }

        // Return true if no files selected (and not required) or if valid
        return true;
    }

});