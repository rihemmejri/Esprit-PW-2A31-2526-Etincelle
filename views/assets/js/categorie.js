function validateCategorieForm() {
    const nom = document.getElementById('nom_categorie');
    const type = document.getElementById('type_categorie');
    const description = document.getElementsByName('description')[0];
    let isValid = true;

    // Reset error messages and styles
    document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(el => {
        el.style.borderColor = '#eee';
    });

    // Helper to show errors
    const showError = (id, message) => {
        const field = document.getElementById(id) || document.getElementsByName(id)[0];
        const errorText = document.getElementById(id + 'Error');
        if (errorText) {
            errorText.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
            errorText.style.color = '#f44336';
        }
        if (field) {
            field.style.borderColor = '#f44336';
            if (isValid) { // Scroll to first error
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        isValid = false;
    };

    // Helper for success style
    const showSuccess = (id) => {
        const field = document.getElementById(id) || document.getElementsByName(id)[0];
        if (field && field.value.trim() !== '') {
            field.style.borderColor = '#4CAF50';
        }
    };

    // 1. Nom de la catégorie
    const nomValue = nom.value.trim();
    const alphaRegex = /^[A-Za-zÀ-ÿ\s]+$/;
    if (nomValue === "") {
        showError('nom_categorie', 'Le nom de la catégorie est obligatoire et ne peut pas être vide.');
    } else if (nomValue.length < 3 || nomValue.length > 50) {
        showError('nom_categorie', 'La longueur du nom doit être comprise entre 3 et 50 caractères.');
    } else if (!alphaRegex.test(nomValue)) {
        showError('nom_categorie', 'Le nom ne doit contenir que des lettres et des espaces.');
    } else {
        showSuccess('nom_categorie');
    }

    // 3. Type
    if (!type.value || type.value === "" || type.value === "Choisir un type") {
        showError('type_categorie', 'Veuillez choisir un type valide.');
    } else {
        showSuccess('type_categorie');
    }

    // 4. Description
    const descValue = description.value.trim();
    if (descValue === "") {
        showError('description', 'La description est obligatoire.');
    } else if (descValue.length < 10) {
        showError('description', 'La description doit contenir au moins 10 caractères.');
    } 
    // Anti-spam check (simple check for repetitive characters like "aaaaaaa")
    else if (/(.)\1{4,}/.test(descValue)) {
        showError('description', 'La description semble contenir du spam ou des caractères répétitifs.');
    } else {
        showSuccess('description');
    }

    return isValid;
}

// Live validation on change
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['nom_categorie', 'type_categorie', 'description'];
    
    inputs.forEach(id => {
        const field = document.getElementById(id) || document.getElementsByName(id)[0];
        if (field) {
            field.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#4CAF50';
                    const errorText = document.getElementById(id + 'Error');
                    if (errorText) errorText.textContent = '';
                } else {
                    this.style.borderColor = '#eee';
                }
            });
        }
    });
});
