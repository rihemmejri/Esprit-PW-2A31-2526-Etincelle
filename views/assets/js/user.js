// views/assets/js/user.js - Validation complète

// Validation du nom (doit commencer par majuscule)
function validateNom(nom) {
    const regex = /^[A-Z][a-zA-Zéèêëàâôûç\-]{2,}$/;
    return regex.test(nom);
}

// Validation du prénom (doit commencer par majuscule)
function validatePrenom(prenom) {
    const regex = /^[A-Z][a-zA-Zéèêëàâôûç\-]{2,}$/;
    return regex.test(prenom);
}

// Validation de l'email
function validateEmail(email) {
    const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}

// Validation du mot de passe (lettres et chiffres, min 6 caractères)
function validatePassword(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
    return regex.test(password);
}

// Validation du rôle - Pour majuscules
function validateRole(role) {
    return role === 'USER' || role === 'ADMIN';
}

// Fonction pour afficher les erreurs
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    const oldError = field.parentNode.querySelector('.error-message');
    if (oldError) {
        oldError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = message;
    
    field.parentNode.appendChild(errorDiv);
    field.style.borderColor = '#f44336';
}

// Fonction pour effacer tous les messages d'erreur
function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.remove());
    
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.style.borderColor = '#e0e0e0';
    });
}

// Validation du formulaire d'ajout (BackOffice)
function validateAddForm(event) {
    if (event) event.preventDefault();
    
    let isValid = true;
    
    const nom = document.getElementById('nom')?.value.trim() || '';
    const prenom = document.getElementById('prenom')?.value.trim() || '';
    const email = document.getElementById('email')?.value.trim() || '';
    const mot_de_passe = document.getElementById('mot_de_passe')?.value || '';
    const role = document.getElementById('role')?.value || '';
    
    clearErrors();
    
    if (!validateNom(nom)) {
        showError('nom', 'Le nom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validatePrenom(prenom)) {
        showError('prenom', 'Le prénom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validateEmail(email)) {
        showError('email', 'Veuillez entrer un email valide (exemple@domaine.extension)');
        isValid = false;
    }
    
    if (!validatePassword(mot_de_passe)) {
        showError('mot_de_passe', 'Le mot de passe doit contenir au moins 6 caractères, des lettres et des chiffres');
        isValid = false;
    }
    
    if (!validateRole(role)) {
        showError('role', 'Veuillez sélectionner un rôle valide');
        isValid = false;
    }
    
    if (isValid && document.getElementById('addUserForm')) {
        document.getElementById('addUserForm').submit();
    }
    
    return false;
}

// Validation du formulaire de modification (BackOffice)
function validateEditForm(event) {
    if (event) event.preventDefault();
    
    let isValid = true;
    
    const nom = document.getElementById('nom')?.value.trim() || '';
    const prenom = document.getElementById('prenom')?.value.trim() || '';
    const email = document.getElementById('email')?.value.trim() || '';
    const role = document.getElementById('role')?.value || '';
    
    clearErrors();
    
    if (!validateNom(nom)) {
        showError('nom', 'Le nom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validatePrenom(prenom)) {
        showError('prenom', 'Le prénom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validateEmail(email)) {
        showError('email', 'Veuillez entrer un email valide');
        isValid = false;
    }
    
    if (!validateRole(role)) {
        showError('role', 'Veuillez sélectionner un rôle valide');
        isValid = false;
    }
    
    if (isValid && document.getElementById('editUserForm')) {
        document.getElementById('editUserForm').submit();
    }
    
    return false;
}

// Validation du formulaire de login
function validateLoginForm(event) {
    if (event) event.preventDefault();
    
    let isValid = true;
    
    const email = document.getElementById('email')?.value.trim() || '';
    const password = document.getElementById('mot_de_passe')?.value || '';
    
    clearErrors();
    
    if (!validateEmail(email)) {
        showError('email', 'Veuillez entrer un email valide');
        isValid = false;
    }
    
    if (password.length === 0) {
        showError('mot_de_passe', 'Veuillez entrer votre mot de passe');
        isValid = false;
    }
    
    if (isValid && document.getElementById('loginForm')) {
        document.getElementById('loginForm').submit();
    }
    
    return false;
}

// Validation du formulaire d'inscription
function validateRegisterForm(event) {
    if (event) event.preventDefault();
    
    let isValid = true;
    
    const nom = document.getElementById('nom')?.value.trim() || '';
    const prenom = document.getElementById('prenom')?.value.trim() || '';
    const email = document.getElementById('email')?.value.trim() || '';
    const mot_de_passe = document.getElementById('mot_de_passe')?.value || '';
    const confirm_password = document.getElementById('confirm_password')?.value || '';
    const acceptTerms = document.getElementById('acceptTerms')?.checked || false;
    
    clearErrors();
    
    if (!validateNom(nom)) {
        showError('nom', 'Le nom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validatePrenom(prenom)) {
        showError('prenom', 'Le prénom doit commencer par une majuscule et contenir au moins 3 lettres');
        isValid = false;
    }
    
    if (!validateEmail(email)) {
        showError('email', 'Veuillez entrer un email valide');
        isValid = false;
    }
    
    if (!validatePassword(mot_de_passe)) {
        showError('mot_de_passe', 'Le mot de passe doit contenir au moins 6 caractères, des lettres et des chiffres');
        isValid = false;
    }
    
    if (mot_de_passe !== confirm_password) {
        showError('confirm_password', 'Les mots de passe ne correspondent pas');
        isValid = false;
    }
    
    if (!acceptTerms) {
        showError('acceptTerms', 'Vous devez accepter les conditions d\'utilisation');
        isValid = false;
    }
    
    if (isValid && document.getElementById('registerForm')) {
        document.getElementById('registerForm').submit();
    }
    
    return false;
}

// Confirmation de suppression
function confirmDelete(userId, userName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
        window.location.href = `delete.php?id=${userId}`;
    }
}

// Validation en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            if (this.value.trim() && !validateNom(this.value.trim())) {
                showError('nom', 'Le nom doit commencer par une majuscule');
            } else {
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
                this.style.borderColor = '#4CAF50';
            }
        });
    }
    
    const prenomInput = document.getElementById('prenom');
    if (prenomInput) {
        prenomInput.addEventListener('input', function() {
            if (this.value.trim() && !validatePrenom(this.value.trim())) {
                showError('prenom', 'Le prénom doit commencer par une majuscule');
            } else {
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
                this.style.borderColor = '#4CAF50';
            }
        });
    }
    
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (this.value.trim() && !validateEmail(this.value.trim())) {
                showError('email', 'Email invalide');
            } else {
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
                this.style.borderColor = '#4CAF50';
            }
        });
    }
    
    const passwordInput = document.getElementById('mot_de_passe');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            if (this.value && !validatePassword(this.value)) {
                showError('mot_de_passe', 'Le mot de passe doit contenir lettres et chiffres (min 6 caractères)');
            } else {
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
                this.style.borderColor = '#4CAF50';
            }
        });
    }
    
    const confirmInput = document.getElementById('confirm_password');
    const pwdInput = document.getElementById('mot_de_passe');
    if (confirmInput && pwdInput) {
        confirmInput.addEventListener('input', function() {
            if (this.value !== pwdInput.value) {
                showError('confirm_password', 'Les mots de passe ne correspondent pas');
            } else {
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
                this.style.borderColor = '#4CAF50';
            }
        });
    }
});