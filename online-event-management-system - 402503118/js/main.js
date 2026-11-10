// main.js - Front-end interactivity (Requirement 6)
// JavaScript validation, image preview, live search.

document.addEventListener('DOMContentLoaded', () => {
    initImagePreview();
    initFormValidation();
    initLiveSearch();
});

/* --------------------------------------------------------------
   Image preview before upload (Requirement 6b)
   -------------------------------------------------------------- */
function initImagePreview() {
    const inputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    inputs.forEach(input => {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // Validate file type client-side
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                this.value = '';
                return;
            }

            // Validate file size (max 2 MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File too large. Maximum size is 2 MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                // Remove existing preview if any
                const existing = this.parentElement.querySelector('.preview-img');
                if (existing) existing.remove();

                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img';
                img.style.cssText = 'margin-top:0.5rem;width:150px;height:150px;border-radius:8px;object-fit:cover;border:2px solid var(--gray-200);display:block;';
                this.parentElement.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
}

/* --------------------------------------------------------------
   Form validation (Requirement 6a)
   -------------------------------------------------------------- */
function initFormValidation() {
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let valid = true;
            const requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'var(--danger)';
                    field.focus();
                } else {
                    field.style.borderColor = '';
                }
            });

            // Password match check
            const password = form.querySelector('input[name="password"]');
            const confirm = form.querySelector('input[name="confirm_password"]');
            if (password && confirm && password.value !== confirm.value) {
                valid = false;
                confirm.style.borderColor = 'var(--danger)';
                confirm.focus();
                alert('Passwords do not match.');
            }

            // Email format check
            const email = form.querySelector('input[type="email"]');
            if (email && email.value) {
                const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRe.test(email.value)) {
                    valid = false;
                    email.style.borderColor = 'var(--danger)';
                    email.focus();
                    alert('Please enter a valid email address.');
                }
            }

            if (!valid) {
                e.preventDefault();
            }
        });

        // Reset border colors on input
        form.querySelectorAll('input, textarea').forEach(field => {
            field.addEventListener('input', () => {
                field.style.borderColor = '';
            });
        });
    });
}

/* --------------------------------------------------------------
   Live search (Requirement 6c)
   -------------------------------------------------------------- */
function initLiveSearch() {
    const searchInput = document.querySelector('.search-form input[name="q"]');
    const resultsContainer = document.querySelector('.search-results');

    if (!searchInput || !resultsContainer) return;

    let timeout = null;

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim();
        clearTimeout(timeout);

        // Clear previous results
        resultsContainer.innerHTML = '';

        if (q.length < 2) return;

        timeout = setTimeout(async () => {
            try {
                const res = await fetch(`search.php?q=${encodeURIComponent(q)}`);
                const html = await res.text();

                // Extract only the results portion from the response
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const results = temp.querySelector('.search-results');
                if (results) {
                    resultsContainer.innerHTML = results.innerHTML;
                } else {
                    resultsContainer.innerHTML = '<p class="empty">No results found.</p>';
                }
            } catch (err) {
                resultsContainer.innerHTML = '<p class="empty">Search unavailable.</p>';
            }
        }, 400);
    });
}