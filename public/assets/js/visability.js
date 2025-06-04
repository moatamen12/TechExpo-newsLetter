document.addEventListener("DOMContentLoaded", function(){
    const toggleButton = document.querySelectorAll('.toggle-password-button');

    if(toggleButton.length > 0) {
        toggleButton.forEach(button=> {
            button.addEventListener('click', function() {
                const inputGroup = this.closest('.input-group');
                if (!inputGroup) return; // Ensure inputGroup is found

                const passwordInput = inputGroup.querySelector('input');
                const icon = this.querySelector('i');

                if(passwordInput && icon){
                    const currentType = passwordInput.getAttribute('type');
                    passwordInput.setAttribute('type', currentType === 'password' ? 'text' : 'password');
    
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            })
        })
    }
    else {
        console.warn("No toggle password buttons found with class '.toggle-password-button'");
    }
})