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
// Validation du formulaire d'inscription
function validateRegisterForm(event) {
    // Empêcher l'envoi du formulaire si validation échoue
    const form = document.getElementById('registerForm');
    const nom = document.getElementById('nom');
    const prenom = document.getElementById('prenom');
    const email = document.getElementById('email');
    const motDePasse = document.getElementById('mot_de_passe');
    const confirmPassword = document.getElementById('confirm_password');
    const acceptTerms = document.getElementById('acceptTerms');
    
    // Réinitialiser les messages d'erreur
    clearErrors();
    
    let isValid = true;
    
    // Validation du nom (doit commencer par une majuscule)
    const nomRegex = /^[A-Z][a-zA-ZÀ-ÿ\s-]*$/;
    if (!nom.value.trim()) {
        showError(nom, 'Le nom est requis');
        isValid = false;
    } else if (!nomRegex.test(nom.value.trim())) {
        showError(nom, 'Le nom doit commencer par une majuscule (ex: Dupont)');
        isValid = false;
    }
    
    // Validation du prénom (doit commencer par une majuscule)
    if (!prenom.value.trim()) {
        showError(prenom, 'Le prénom est requis');
        isValid = false;
    } else if (!nomRegex.test(prenom.value.trim())) {
        showError(prenom, 'Le prénom doit commencer par une majuscule (ex: Jean)');
        isValid = false;
    }
    
    // Validation de l'email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim()) {
        showError(email, 'L\'email est requis');
        isValid = false;
    } else if (!emailRegex.test(email.value.trim())) {
        showError(email, 'Veuillez entrer un email valide (exemple@domaine.com)');
        isValid = false;
    }
    
    // Validation du mot de passe
    if (!motDePasse.value) {
        showError(motDePasse, 'Le mot de passe est requis');
        isValid = false;
    }
    
    // Validation de la confirmation du mot de passe
    if (motDePasse.value !== confirmPassword.value) {
        showError(confirmPassword, 'Les mots de passe ne correspondent pas');
        isValid = false;
    }
    
    // Validation des conditions d'utilisation
    if (!acceptTerms.checked) {
        alert('Vous devez accepter les conditions d\'utilisation');
        isValid = false;
    }
    
    if (!isValid) {
        event.preventDefault();
    }
    
    return isValid;
}

// Fonction pour afficher une erreur
function showError(input, message) {
    const formGroup = input.closest('.form-group');
    if (formGroup) {
        // Supprimer l'erreur existante
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        // Ajouter la nouvelle erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#f44336';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        errorDiv.innerText = message;
        formGroup.appendChild(errorDiv);
        
        // Ajouter une classe pour styliser l'input en erreur
        input.style.borderColor = '#f44336';
    }
}

// Fonction pour effacer toutes les erreurs
function clearErrors() {
    const errors = document.querySelectorAll('.error-message');
    errors.forEach(error => error.remove());
    
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.style.borderColor = '#e0e0e0';
    });
}

// Affichage dynamique de la force du mot de passe
document.addEventListener('DOMContentLoaded', function() {
    const motDePasseInput = document.getElementById('mot_de_passe');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    if (motDePasseInput) {
        // Créer l'élément d'indicateur de force
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength-container';
        strengthContainer.innerHTML = `
            <div class="strength-bar">
                <div class="strength-progress"></div>
            </div>
            <span class="strength-text"></span>
        `;
        
        // Insérer après le champ mot de passe
        motDePasseInput.parentNode.insertBefore(strengthContainer, motDePasseInput.nextSibling);
        
        const strengthProgress = strengthContainer.querySelector('.strength-progress');
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        // Fonction pour évaluer la force du mot de passe
     function checkPasswordStrength(password) {
    let message = '';
    let className = '';
    let progressWidth = 0;
    
    if (password.length === 0) {
        return { message: '', className: '', progressWidth: 0 };
    }
    
    // Vérifier les lettres
    const hasLetters = /[a-zA-Z]/.test(password);
    // Vérifier les chiffres
    const hasNumbers = /[0-9]/.test(password);
    // Vérifier les symboles (TOUS les caractères qui ne sont ni lettre ni chiffre)
    const hasSymbols = /[^a-zA-Z0-9]/.test(password);
    
    // Compter combien de types sont présents
    let typeCount = 0;
    if (hasLetters) typeCount++;
    if (hasNumbers) typeCount++;
    if (hasSymbols) typeCount++;
    
    // Déterminer le niveau
    if (typeCount === 1) {
        message = '🔒 Mot de passe faible';
        className = 'strength-faible text-faible';
        progressWidth = 33;
    } else if (typeCount === 2) {
        message = '⚠️ Mot de passe moyen';
        className = 'strength-moyen text-moyen';
        progressWidth = 66;
    } else {
        message = '✅ Mot de passe fort';
        className = 'strength-fort text-fort';
        progressWidth = 100;
    }
    
    return { message, className, progressWidth };
}
        
        // Écouter les événements de saisie
        motDePasseInput.addEventListener('input', function() {
            const password = this.value;
            const result = checkPasswordStrength(password);
            
            // Mettre à jour la barre de progression
            strengthProgress.style.width = result.progressWidth + '%';
            strengthProgress.className = 'strength-progress ' + (result.className ? result.className.split(' ')[0] : '');
            
            // Mettre à jour le texte
            strengthText.textContent = result.message;
            strengthText.className = 'strength-text';
            if (result.className) {
                const textClass = result.className.split(' ')[1];
                if (textClass) strengthText.classList.add(textClass);
            }
            
            // Vérifier la confirmation en temps réel
            if (confirmPasswordInput && confirmPasswordInput.value) {
                checkPasswordMatch();
            }
        });
    }
    
    // Fonction pour vérifier la correspondance des mots de passe
    function checkPasswordMatch() {
        const motDePasse = document.getElementById('mot_de_passe');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (motDePasse && confirmPassword) {
            const matchMessage = document.getElementById('matchMessage');
            
            if (confirmPassword.value && motDePasse.value !== confirmPassword.value) {
                if (!matchMessage) {
                    const msg = document.createElement('div');
                    msg.id = 'matchMessage';
                    msg.style.color = '#f44336';
                    msg.style.fontSize = '12px';
                    msg.style.marginTop = '5px';
                    msg.innerText = '❌ Les mots de passe ne correspondent pas';
                    confirmPassword.parentNode.appendChild(msg);
                }
                confirmPassword.style.borderColor = '#f44336';
            } else {
                if (matchMessage) {
                    matchMessage.remove();
                }
                if (confirmPassword.value && motDePasse.value === confirmPassword.value) {
                    const successMsg = document.getElementById('successMatch');
                    if (!successMsg && confirmPassword.value) {
                        const msg = document.createElement('div');
                        msg.id = 'successMatch';
                        msg.style.color = '#4caf50';
                        msg.style.fontSize = '12px';
                        msg.style.marginTop = '5px';
                        msg.innerText = '✓ Les mots de passe correspondent';
                        confirmPassword.parentNode.appendChild(msg);
                    } else if (successMsg && (!confirmPassword.value || motDePasse.value !== confirmPassword.value)) {
                        successMsg.remove();
                    }
                }
                confirmPassword.style.borderColor = '#e0e0e0';
            }
        }
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }
});