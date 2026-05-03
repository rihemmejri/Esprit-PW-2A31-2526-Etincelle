/**
 * Produit Form Validation
 * Validates all form fields for produit add/edit forms with aesthetic feedback
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProduitForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => validateField(input));
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) isValid = false;
        });

        if (!isValid) {
            e.preventDefault();
            const firstError = form.querySelector('.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});

function validateField(field) {
    const name = field.name;
    const value = field.value.trim();
    let error = '';

    if (name === 'nom') {
        if (!value) error = "Le nom du produit est obligatoire";
        else if (value.length < 3) error = "Le nom doit contenir au moins 3 caractères";
        else if (!/^[a-zA-ZÀ-ÿ\s0-9]+$/.test(value)) error = "Le nom contient des caractères invalides";
    }

    if (name === 'id_categorie' && !value) error = "Veuillez sélectionner une catégorie";
    if (name === 'origine' && !value) error = "Veuillez sélectionner une origine";
    if (name === 'saison' && !value) error = "Veuillez sélectionner une saison";

    if (name === 'distance_transport') {
        if (value === '') error = "La distance transport est obligatoire";
        else {
            const distanceNum = parseFloat(value);
            if (isNaN(distanceNum)) error = "La distance doit être un nombre";
            else if (distanceNum < 0) error = "La distance doit être positive";
        }
    }

    showError(field, error);
    return !error;
}

function showError(field, message) {
    const parent = field.parentElement;
    let errorDiv = parent.querySelector('.error-text');
    
    // Find or create error element
    if (!errorDiv) {
        errorDiv = document.createElement('small');
        errorDiv.className = 'error-text';
        parent.appendChild(errorDiv);
    }

    if (message) {
        field.classList.add('error');
        field.classList.remove('valid');
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        errorDiv.style.color = '#dc3545';
        errorDiv.style.display = 'block';
    } else {
        field.classList.remove('error');
        if (valueExists(field)) {
            field.classList.add('valid');
        }
        errorDiv.textContent = '';
        errorDiv.style.display = 'none';
    }
}

function valueExists(field) {
    if (field.type === 'file') return field.files.length > 0;
    return field.value.trim() !== '';
}

// Global function for old calls if any
function validateForm() {
    const form = document.getElementById('addProduitForm');
    if (!form) return true;
    let isValid = true;
    form.querySelectorAll('input, select').forEach(input => {
        if (!validateField(input)) isValid = false;
    });
    return isValid;
}
