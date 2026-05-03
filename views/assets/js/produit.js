/**
 * Produit Form Validation
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
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
<<<<<<< HEAD
=======
=======
 * Validates all form fields for produit add/edit forms
 */

function validateForm() {
    // Clear all error messages
    document.getElementById('nomError').textContent = '';
    document.getElementById('categorieError').textContent = '';
    document.getElementById('origineError').textContent = '';
    document.getElementById('distanceError').textContent = '';
    document.getElementById('saisonError').textContent = '';

    // Validate Nom (Product Name)
    const nom = document.getElementById('nom').value.trim();
    if (!nom) {
        document.getElementById('nomError').textContent = 'Le nom du produit est obligatoire';
        return false;
    }

    if (nom.length < 3) {
        document.getElementById('nomError').textContent = 'Le nom doit contenir au moins 3 caractères';
        return false;
    }

    // Check if only alphabetic characters and spaces
    if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nom)) {
        document.getElementById('nomError').textContent = 'Le nom doit contenir uniquement des lettres';
        return false;
    }

    // Validate Catégorie (Category)
    const categorie = document.getElementById('id_categorie').value;
    if (!categorie) {
        document.getElementById('categorieError').textContent = 'Veuillez sélectionner une catégorie';
        return false;
    }

    const validCategories = ['1', '2', '3', '4', '5'];
    if (!validCategories.includes(categorie)) {
        document.getElementById('categorieError').textContent = 'Catégorie invalide';
        return false;
    }

    // Validate Origine (Origin)
    const origine = document.getElementById('origine').value;
    if (!origine) {
        document.getElementById('origineError').textContent = 'Veuillez sélectionner une origine';
        return false;
    }

    // Validate Distance Transport
    const distance = document.getElementById('distance_transport').value;
    if (distance === '') {
        document.getElementById('distanceError').textContent = 'La distance transport est obligatoire';
        return false;
    }

    const distanceNum = parseFloat(distance);
    if (isNaN(distanceNum)) {
        document.getElementById('distanceError').textContent = 'La distance doit être un nombre';
        return false;
    }

    if (distanceNum < 0) {
        document.getElementById('distanceError').textContent = 'La distance doit être supérieure à 0 km';
        return false;
    }

    // Validate Saison (Season)
    const saison = document.getElementById('saison').value;
    if (!saison) {
        document.getElementById('saisonError').textContent = 'Veuillez sélectionner une saison';
        return false;
    }

    // All validations passed
    return true;
}

// Add real-time validation feedback (optional, but improves UX)
document.addEventListener('DOMContentLoaded', function() {
    const nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('blur', function() {
            const nom = this.value.trim();
            if (nom && (nom.length < 3 || !/^[a-zA-ZÀ-ÿ\s]+$/.test(nom))) {
                document.getElementById('nomError').textContent = 
                    nom.length < 3 ? 'Le nom doit contenir au moins 3 caractères' : 'Le nom doit contenir uniquement des lettres';
            } else {
                document.getElementById('nomError').textContent = '';
            }
        });
    }

    const distanceInput = document.getElementById('distance_transport');
    if (distanceInput) {
        distanceInput.addEventListener('blur', function() {
            const distance = this.value;
            if (distance !== '') {
                const distanceNum = parseFloat(distance);
                if (isNaN(distanceNum) || distanceNum < 0) {
                    document.getElementById('distanceError').textContent = 'La distance doit être un nombre positif';
                } else {
                    document.getElementById('distanceError').textContent = '';
                }
            }
        });
    }

    const categorieSelect = document.getElementById('id_categorie');
    if (categorieSelect) {
        categorieSelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('categorieError').textContent = '';
            }
        });
    }

    const origineSelect = document.getElementById('origine');
    if (origineSelect) {
        origineSelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('origineError').textContent = '';
            }
        });
    }

    const saisonSelect = document.getElementById('saison');
    if (saisonSelect) {
        saisonSelect.addEventListener('change', function() {
            if (this.value) {
                document.getElementById('saisonError').textContent = '';
            }
        });
    }
});
>>>>>>> 250cc12bdb995fa9e0d9c0b1489e043850f7f44c
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
