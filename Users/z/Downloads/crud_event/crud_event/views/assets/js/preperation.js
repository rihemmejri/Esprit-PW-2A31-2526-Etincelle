// assets/js/preperation.js

// ========== FONCTIONS POUR LES BOUTONS + ET - ==========

function updateOrdre(delta) {
    let input = document.getElementById('ordre');
    if (!input) return;
    let value = parseInt(input.value) || 0;
    let newValue = value + delta;
    if (newValue >= 1) {
        input.value = newValue;
    } else if (delta > 0) {
        input.value = 1;
    }
    validateOrdre();
}

function updateDuree(delta) {
    let input = document.getElementById('duree');
    if (!input) return;
    let value = parseInt(input.value) || 0;
    let newValue = value + delta;
    if (newValue >= 0) {
        input.value = newValue;
    }
    validateDuree();
}

function updateTemperature(delta) {
    let input = document.getElementById('temperature');
    if (!input) return;
    let value = parseInt(input.value) || 0;
    let newValue = value + delta;
    if (newValue >= 0) {
        input.value = newValue;
    }
    validateTemperature();
}

// ========== FONCTIONS D'AFFICHAGE D'ERREUR ==========

function showError(input, message) {
    if (!input) return;
    clearError(input);
    input.style.borderColor = '#f44336';
    input.style.borderWidth = '2px';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-field';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    input.parentNode.insertBefore(errorDiv, input.nextSibling);
}

function clearError(input) {
    if (!input) return;
    input.style.borderColor = '#ddd';
    input.style.borderWidth = '1px';
    
    const nextSibling = input.nextSibling;
    if (nextSibling && nextSibling.className === 'error-field') {
        nextSibling.remove();
    }
}

// ========== VALIDATIONS DES CHAMPS ==========

// Validation Recette (obligatoire)
function validateRecette() {
    const recette = document.getElementById('recetteSelect');
    if (!recette) return true;
    if (!recette.value) {
        showError(recette, 'Veuillez sélectionner une recette');
        return false;
    } else {
        clearError(recette);
        return true;
    }
}

// Validation Instruction (obligatoire + seulement lettres, espaces, ponctuation)
function validateInstruction() {
    const instruction = document.getElementById('instruction');
    if (!instruction) return true;
    const value = instruction.value.trim();
    const regex = /^[a-zA-ZÀ-ÿ\s\.,!?'\-]+$/;
    
    if (!value) {
        showError(instruction, 'L\'instruction est obligatoire');
        return false;
    } else if (value.length < 10) {
        showError(instruction, 'L\'instruction doit contenir au moins 10 caractères');
        return false;
    } else if (!regex.test(value)) {
        showError(instruction, 'L\'instruction ne doit contenir que des lettres, espaces, points, virgules, ! et ? (pas de chiffres ni symboles)');
        return false;
    } else {
        clearError(instruction);
        return true;
    }
}

// Validation Astuce (optionnelle mais si renseignée: seulement lettres, espaces, ponctuation)
function validateAstuce() {
    const astuce = document.getElementById('astuce');
    if (!astuce) return true;
    const value = astuce.value.trim();
    const regex = /^[a-zA-ZÀ-ÿ\s\.,!?'\-]*$/;
    
    if (value !== '' && !regex.test(value)) {
        showError(astuce, 'L\'astuce ne doit contenir que des lettres, espaces, points, virgules, ! et ? (pas de chiffres ni symboles)');
        return false;
    } else if (value !== '' && value.length < 5) {
        showError(astuce, 'L\'astuce doit contenir au moins 5 caractères');
        return false;
    } else {
        clearError(astuce);
        return true;
    }
}

// Validation Ordre (optionnel)
function validateOrdre() {
    const ordre = document.getElementById('ordre');
    if (!ordre) return true;
    if (ordre.value !== '') {
        const value = parseInt(ordre.value);
        if (isNaN(value) || value < 1) {
            showError(ordre, 'Le numéro d\'ordre doit être supérieur à 0');
            return false;
        } else {
            clearError(ordre);
            return true;
        }
    } else {
        clearError(ordre);
        return true;
    }
}

// Validation Durée (obligatoire)
function validateDuree() {
    const duree = document.getElementById('duree');
    if (!duree) return true;
    const value = parseInt(duree.value);
    if (isNaN(value) || value < 0) {
        showError(duree, 'La durée doit être un nombre positif');
        return false;
    } else {
        clearError(duree);
        return true;
    }
}

// Validation Température (obligatoire)
function validateTemperature() {
    const temp = document.getElementById('temperature');
    if (!temp) return true;
    const value = parseInt(temp.value);
    if (isNaN(value) || value < 0) {
        showError(temp, 'La température doit être un nombre positif');
        return false;
    } else if (value > 300) {
        showError(temp, 'La température ne peut pas dépasser 300°C');
        return false;
    } else {
        clearError(temp);
        return true;
    }
}

// Validation Quantité (optionnelle)
function validateQuantite() {
    const quantite = document.getElementById('quantite');
    if (!quantite) return true;
    if (quantite.value !== '' && quantite.value.length < 2) {
        showError(quantite, 'La quantité est trop courte');
        return false;
    } else {
        clearError(quantite);
        return true;
    }
}

// Validation Type d'action (obligatoire - radio)
function validateTypeAction() {
    const selected = document.querySelector('input[name="type_action"]:checked');
    const group = document.getElementById('actionGroup');
    if (!group) return true;
    
    if (!selected) {
        const errorDiv = document.getElementById('actionError');
        if (!errorDiv) {
            const error = document.createElement('div');
            error.id = 'actionError';
            error.className = 'error-field';
            error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un type d\'action';
            group.parentNode.insertBefore(error, group.nextSibling);
        }
        return false;
    } else {
        const errorDiv = document.getElementById('actionError');
        if (errorDiv) errorDiv.remove();
        return true;
    }
}

// Validation Outil utilisé (obligatoire - radio)
function validateOutil() {
    const selected = document.querySelector('input[name="outil_utilise"]:checked');
    const group = document.getElementById('outilGroup');
    if (!group) return true;
    
    if (!selected) {
        const errorDiv = document.getElementById('outilError');
        if (!errorDiv) {
            const error = document.createElement('div');
            error.id = 'outilError';
            error.className = 'error-field';
            error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un outil';
            group.parentNode.insertBefore(error, group.nextSibling);
        }
        return false;
    } else {
        const errorDiv = document.getElementById('outilError');
        if (errorDiv) errorDiv.remove();
        return true;
    }
}

// ========== AFFICHAGE DU RÉSUMÉ DES ERREURS ==========

function showErrorSummary(errors, form) {
    // Supprimer l'ancien résumé
    const oldSummary = document.querySelector('.error-summary');
    if (oldSummary) oldSummary.remove();
    
    // Créer le nouveau résumé
    const summaryDiv = document.createElement('div');
    summaryDiv.className = 'error-summary';
    let errorHtml = '<strong><i class="fas fa-times-circle"></i> Veuillez corriger les erreurs suivantes :</strong><ul>';
    errors.forEach(error => {
        errorHtml += `<li>${error}</li>`;
    });
    errorHtml += '</ul>';
    summaryDiv.innerHTML = errorHtml;
    form.insertBefore(summaryDiv, form.firstChild);
    summaryDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ========== INITIALISATION DES FORMULAIRES ==========

function initAddForm() {
    const form = document.getElementById('addPreperationForm');
    if (!form) return false;
    
    // Événements en temps réel
    const recetteSelect = document.getElementById('recetteSelect');
    const instruction = document.getElementById('instruction');
    const astuce = document.getElementById('astuce');
    const ordre = document.getElementById('ordre');
    const duree = document.getElementById('duree');
    const temperature = document.getElementById('temperature');
    const quantite = document.getElementById('quantite');
    
    if (recetteSelect) recetteSelect.addEventListener('change', validateRecette);
    if (instruction) instruction.addEventListener('input', validateInstruction);
    if (astuce) astuce.addEventListener('input', validateAstuce);
    if (ordre) ordre.addEventListener('input', validateOrdre);
    if (duree) duree.addEventListener('input', validateDuree);
    if (temperature) temperature.addEventListener('input', validateTemperature);
    if (quantite) quantite.addEventListener('input', validateQuantite);
    
    // Validation des radios
    document.querySelectorAll('input[name="type_action"]').forEach(radio => {
        radio.addEventListener('click', validateTypeAction);
    });
    document.querySelectorAll('input[name="outil_utilise"]').forEach(radio => {
        radio.addEventListener('click', validateOutil);
    });
    
    // Validation au submit
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errors = [];
        
        if (!validateRecette()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner une recette');
        }
        if (!validateInstruction()) {
            isValid = false;
            errors.push('❌ L\'instruction est obligatoire (minimum 10 caractères, uniquement lettres et espaces)');
        }
        if (!validateAstuce()) {
            isValid = false;
            errors.push('❌ L\'astuce ne doit contenir que des lettres et espaces');
        }
        if (!validateOrdre()) {
            isValid = false;
            errors.push('❌ Le numéro d\'ordre doit être supérieur à 0');
        }
        if (!validateDuree()) {
            isValid = false;
            errors.push('❌ La durée est obligatoire');
        }
        if (!validateTemperature()) {
            isValid = false;
            errors.push('❌ La température est obligatoire (0-300°C)');
        }
        if (!validateQuantite()) {
            isValid = false;
            errors.push('❌ La quantité ingrédient est trop courte');
        }
        if (!validateTypeAction()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un type d\'action');
        }
        if (!validateOutil()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un outil');
        }
        
        if (!isValid) {
            e.preventDefault();
            showErrorSummary(errors, form);
        }
    });
    
    return true;
}

function initEditForm() {
    const form = document.getElementById('editPreperationForm');
    if (!form) return false;
    
    // Événements en temps réel
    const recetteSelect = document.getElementById('recetteSelect');
    const instruction = document.getElementById('instruction');
    const astuce = document.getElementById('astuce');
    const ordre = document.getElementById('ordre');
    const duree = document.getElementById('duree');
    const temperature = document.getElementById('temperature');
    const quantite = document.getElementById('quantite');
    
    if (recetteSelect) recetteSelect.addEventListener('change', validateRecette);
    if (instruction) instruction.addEventListener('input', validateInstruction);
    if (astuce) astuce.addEventListener('input', validateAstuce);
    if (ordre) ordre.addEventListener('input', validateOrdre);
    if (duree) duree.addEventListener('input', validateDuree);
    if (temperature) temperature.addEventListener('input', validateTemperature);
    if (quantite) quantite.addEventListener('input', validateQuantite);
    
    // Validation des radios
    document.querySelectorAll('input[name="type_action"]').forEach(radio => {
        radio.addEventListener('click', validateTypeAction);
    });
    document.querySelectorAll('input[name="outil_utilise"]').forEach(radio => {
        radio.addEventListener('click', validateOutil);
    });
    
    // Validation au submit
    form.addEventListener('submit', function(e) {
        let isValid = true;
        let errors = [];
        
        if (!validateRecette()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner une recette');
        }
        if (!validateInstruction()) {
            isValid = false;
            errors.push('❌ L\'instruction est obligatoire (minimum 10 caractères, uniquement lettres et espaces)');
        }
        if (!validateAstuce()) {
            isValid = false;
            errors.push('❌ L\'astuce ne doit contenir que des lettres et espaces');
        }
        if (!validateOrdre()) {
            isValid = false;
            errors.push('❌ Le numéro d\'ordre doit être supérieur à 0');
        }
        if (!validateDuree()) {
            isValid = false;
            errors.push('❌ La durée est obligatoire');
        }
        if (!validateTemperature()) {
            isValid = false;
            errors.push('❌ La température est obligatoire (0-300°C)');
        }
        if (!validateQuantite()) {
            isValid = false;
            errors.push('❌ La quantité ingrédient est trop courte');
        }
        if (!validateTypeAction()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un type d\'action');
        }
        if (!validateOutil()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un outil');
        }
        
        if (!isValid) {
            e.preventDefault();
            showErrorSummary(errors, form);
        }
    });
    
    return true;
}

// ========== INITIALISATION AU CHARGEMENT ==========
document.addEventListener('DOMContentLoaded', function() {
    initAddForm();
    initEditForm();
    
    // Valider les champs déjà remplis (pour edit)
    setTimeout(function() {
        validateRecette();
        validateInstruction();
        validateDuree();
        validateTemperature();
        validateTypeAction();
        validateOutil();
    }, 100);
});

// Fonction pour confirmation de réinitialisation
function confirmReset() {
    return confirm('⚠️ Êtes-vous sûr de vouloir réinitialiser le formulaire ?\n\nToutes les données saisies seront perdues.');
}