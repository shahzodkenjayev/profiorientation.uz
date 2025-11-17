// Login sahifasi JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginTypeSelector = document.querySelectorAll('.login-btn');
    const loginTypeInput = document.getElementById('login_type');
    const phoneSection = document.getElementById('phone-section');
    const telegramSection = document.getElementById('telegram-section');
    const googleSection = document.getElementById('google-section');
    const verificationGroup = document.getElementById('verification-group');
    const phoneInput = document.querySelector('input[name="phone"]');
    
    // Login type selector
    loginTypeSelector.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            loginTypeSelector.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            phoneSection.style.display = 'none';
            telegramSection.style.display = 'none';
            googleSection.style.display = 'none';
            
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
    
    // Google Sign-In
    if (document.getElementById('google-login-btn')) {
        window.onGoogleSignIn = function(response) {
            const credential = response.credential;
            const payload = JSON.parse(atob(credential.split('.')[1]));
            
            document.getElementById('google_id').value = payload.sub;
            
            // Auto submit form
            document.getElementById('loginForm').submit();
        };
        
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
});

