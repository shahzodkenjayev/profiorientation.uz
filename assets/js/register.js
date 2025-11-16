// Register sahifasi JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginTypeSelector = document.querySelectorAll('.login-btn');
    const loginTypeInput = document.getElementById('login_type');
    const phoneSection = document.getElementById('phone-section');
    const telegramSection = document.getElementById('telegram-section');
    const googleSection = document.getElementById('google-section');
    const verificationGroup = document.getElementById('verification-group');
    const phoneInput = document.querySelector('input[name="phone"]');
    const form = document.getElementById('registerForm');
    
    // Login type selector
    loginTypeSelector.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Remove active class
            loginTypeSelector.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Hide all sections
            phoneSection.style.display = 'none';
            telegramSection.style.display = 'none';
            googleSection.style.display = 'none';
            
            // Show selected section
            if (type === 'phone') {
                phoneSection.style.display = 'block';
            } else if (type === 'telegram') {
                telegramSection.style.display = 'block';
            } else if (type === 'google') {
                googleSection.style.display = 'block';
            }
            
            loginTypeInput.value = type;
        });
    });
    
    // Phone verification - show verification field after phone is entered
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            if (phone && phone.length >= 9) {
                // Show verification group when phone is entered
                if (verificationGroup) {
                    verificationGroup.style.display = 'block';
                }
            } else {
                if (verificationGroup) {
                    verificationGroup.style.display = 'none';
                }
            }
        });
    }
    
    // Google Sign-In
    if (document.getElementById('google-login-btn')) {
        window.onGoogleSignIn = function(response) {
            const credential = response.credential;
            
            // Decode JWT token (simplified - in production use proper JWT library)
            const payload = JSON.parse(atob(credential.split('.')[1]));
            
            document.getElementById('google_id').value = payload.sub;
            document.getElementById('google_email').value = payload.email;
            
            // Auto-fill name if available
            const nameInput = document.querySelector('input[name="full_name"]');
            if (nameInput && payload.name) {
                nameInput.value = payload.name;
            }
        };
        
        // Initialize Google Sign-In
        const googleClientId = document.getElementById('google_client_id')?.value || '';
        if (googleClientId) {
            google.accounts.id.initialize({
                client_id: googleClientId,
                callback: window.onGoogleSignIn
            });
        }
        
        document.getElementById('google-login-btn').addEventListener('click', function() {
            google.accounts.id.prompt();
        });
    }
    
    // Resend code
    const resendCode = document.getElementById('resend-code');
    if (resendCode) {
        resendCode.addEventListener('click', function() {
            // Trigger resend
            const phone = phoneInput.value;
            if (phone) {
                // Submit form to resend code
                const formData = new FormData(form);
                formData.set('resend_code', '1');
                // This will be handled by PHP
            }
        });
    }
});

