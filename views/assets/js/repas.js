// repas.js - Contrôles de saisie pour addRepas.php et editRepas.php

// ========== VALIDATEUR POUR LES FORMULAIRES DE REPAS ==========

class RepasFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        
        // Ajouter les écouteurs d'événements
        this.form.addEventListener('submit', (e) => this.validateForm(e));
        
        // Validation en temps réel
        const inputs = this.form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.validateField(input));
        });
    }

    validateField(field) {
        const name = field.name;
        const value = field.value.trim();
        let error = '';

        switch(name) {
            case 'nom':
                if (!value) {
                    error = 'Le nom du repas est obligatoire';
                } else if (value.length < 2) {
                    error = 'Le nom doit contenir au moins 2 caractères';
                } else if (value.length > 100) {
                    error = 'Le nom ne doit pas dépasser 100 caractères';
                } else if (!/^[a-zA-ZÀ-ÿ\s\-'éèêëïîôöùûç]+$/.test(value)) {
                    error = 'Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes';
                }
                break;

            case 'calories':
                const calories = parseInt(value);
                if (isNaN(calories)) {
                    error = 'Les calories doivent être un nombre valide';
                } else if (calories < 0) {
                    error = 'Les calories ne peuvent pas être négatives';
                } else if (calories > 2000) {
                    error = 'Les calories ne doivent pas dépasser 2000 kcal';
                }
                break;

            case 'proteines':
                const proteines = parseFloat(value);
                if (isNaN(proteines)) {
                    error = 'Les protéines doivent être un nombre valide';
                } else if (proteines < 0) {
                    error = 'Les protéines ne peuvent pas être négatives';
                } else if (proteines > 200) {
                    error = 'Les protéines ne doivent pas dépasser 200 g';
                }
                break;

            case 'glucides':
                const glucides = parseFloat(value);
                if (isNaN(glucides)) {
                    error = 'Les glucides doivent être un nombre valide';
                } else if (glucides < 0) {
                    error = 'Les glucides ne peuvent pas être négatifs';
                } else if (glucides > 300) {
                    error = 'Les glucides ne doivent pas dépasser 300 g';
                }
                break;

            case 'lipides':
                const lipides = parseFloat(value);
                if (isNaN(lipides)) {
                    error = 'Les lipides doivent être un nombre valide';
                } else if (lipides < 0) {
                    error = 'Les lipides ne peuvent pas être négatifs';
                } else if (lipides > 150) {
                    error = 'Les lipides ne doivent pas dépasser 150 g';
                }
                break;
        }

        this.showError(field, error);
        return !error;
    }

    validateRadioGroup(name) {
        const radios = this.form.querySelectorAll(`input[name="${name}"]`);
        let isChecked = false;
        
        radios.forEach(radio => {
            if (radio.checked) isChecked = true;
        });
        
        if (!isChecked) {
            const error = `Veuillez sélectionner un type de repas`;
            const firstRadio = radios[0];
            if (firstRadio) {
                this.showError(firstRadio.parentElement, error);
            }
            return false;
        }
        
        // Supprimer l'erreur si tout va bien
        const firstRadio = radios[0];
        if (firstRadio) {
            this.showError(firstRadio.parentElement, '');
        }
        return true;
    }

    showError(field, message) {
        // Trouver le conteneur parent approprié
        let parent = field.parentElement;
        
        // Pour les champs normaux, le parent est .input-icon ou .personne-input
        if (parent.classList.contains('input-icon') || parent.classList.contains('personne-input')) {
            parent = parent.parentElement;
        }
        
        // Supprimer l'ancien message d'erreur
        let errorDiv = parent.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        
        // Ajouter/supprimer les classes CSS
        if (message) {
            field.classList.add('error');
            field.classList.remove('valid');
            errorDiv = document.createElement('span');
            errorDiv.className = 'field-error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            parent.appendChild(errorDiv);
        } else {
            field.classList.remove('error');
            field.classList.add('valid');
        }
    }

    validateForm(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Valider les champs texte et nombres
        const fields = ['nom', 'calories', 'proteines', 'glucides', 'lipides'];
        fields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        // Valider le groupe radio du type
        if (!this.validateRadioGroup('type')) {
            isValid = false;
        }
        
        if (isValid) {
            this.showSuccessMessage();
            this.form.submit();
        } else {
            this.showErrorMessage('Veuillez corriger les erreurs dans le formulaire');
            // Faire défiler jusqu'au premier champ en erreur
            const firstError = this.form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    showSuccessMessage() {
        // Supprimer les anciens messages
        this.removeMessages();
        
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.id = 'tempMessage';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>Formulaire valide ! Redirection en cours...</span>
        `;
        
        const formCard = document.querySelector('.form-card');
        const header = document.querySelector('.header');
        if (header && formCard) {
            header.insertAdjacentElement('afterend', successDiv);
        }
        
        setTimeout(() => {
            const msg = document.getElementById('tempMessage');
            if (msg) msg.remove();
        }, 3000);
    }

    showErrorMessage(message) {
        // Supprimer les anciens messages
        this.removeMessages();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.id = 'tempMessage';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        
        const formCard = document.querySelector('.form-card');
        const header = document.querySelector('.header');
        if (header && formCard) {
            header.insertAdjacentElement('afterend', errorDiv);
        }
        
        setTimeout(() => {
            const msg = document.getElementById('tempMessage');
            if (msg) msg.remove();
        }, 5000);
    }

    removeMessages() {
        const tempMsg = document.getElementById('tempMessage');
        if (tempMsg) tempMsg.remove();
    }
}

// ========== FONCTIONS POUR LES BOUTONS + ET - ==========

function updateCalories(delta) {
    const input = document.getElementById('calories');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 2000) value = 2000;
        input.value = value;
        // Déclencher la validation
        const event = new Event('blur');
        input.dispatchEvent(event);
        updateNutritionSummary();
    }
}

function updateProteines(delta) {
    const input = document.getElementById('proteines');
    if (input) {
        let value = parseFloat(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 200) value = 200;
        input.value = value;
        // Déclencher la validation
        const event = new Event('blur');
        input.dispatchEvent(event);
        updateNutritionSummary();
    }
}

function updateGlucides(delta) {
    const input = document.getElementById('glucides');
    if (input) {
        let value = parseFloat(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 300) value = 300;
        input.value = value;
        // Déclencher la validation
        const event = new Event('blur');
        input.dispatchEvent(event);
        updateNutritionSummary();
    }
}

function updateLipides(delta) {
    const input = document.getElementById('lipides');
    if (input) {
        let value = parseFloat(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 150) value = 150;
        input.value = value;
        // Déclencher la validation
        const event = new Event('blur');
        input.dispatchEvent(event);
        updateNutritionSummary();
    }
}

// ========== MISE À JOUR DU RÉSUMÉ NUTRITIONNEL ==========

function updateNutritionSummary() {
    const calories = document.getElementById('calories')?.value || 0;
    const proteines = document.getElementById('proteines')?.value || 0;
    const glucides = document.getElementById('glucides')?.value || 0;
    const lipides = document.getElementById('lipides')?.value || 0;
    
    const totalCaloriesSpan = document.getElementById('totalCalories');
    const totalProteinesSpan = document.getElementById('totalProteines');
    const totalGlucidesSpan = document.getElementById('totalGlucides');
    const totalLipidesSpan = document.getElementById('totalLipides');
    
    if (totalCaloriesSpan) totalCaloriesSpan.textContent = calories;
    if (totalProteinesSpan) totalProteinesSpan.textContent = proteines;
    if (totalGlucidesSpan) totalGlucidesSpan.textContent = glucides;
    if (totalLipidesSpan) totalLipidesSpan.textContent = lipides;
    
    // Calculer les calories théoriques
    const caloriesCalculees = (parseFloat(proteines) * 4) + (parseFloat(glucides) * 4) + (parseFloat(lipides) * 9);
    const ecart = Math.abs(parseFloat(calories) - caloriesCalculees);
    
    // Afficher ou mettre à jour l'indice
    let hint = document.getElementById('caloriesHint');
    const summaryDiv = document.querySelector('.nutrition-summary');
    
    if (summaryDiv) {
        if (!hint) {
            hint = document.createElement('div');
            hint.id = 'caloriesHint';
            hint.style.cssText = 'margin-top: 10px; font-size: 0.75rem; text-align: center;';
            summaryDiv.appendChild(hint);
        }
        
        if (parseFloat(calories) > 0) {
            hint.innerHTML = `<i class="fas fa-calculator"></i> Calories calculées: ${Math.round(caloriesCalculees)} kcal | Écart: ${Math.round(ecart)} kcal`;
            
            if (ecart > 100) {
                hint.style.color = '#e65100';
                hint.style.fontWeight = 'bold';
            } else {
                hint.style.color = '#666';
                hint.style.fontWeight = 'normal';
            }
        }
    }
}

// ========== INITIALISATION AU CHARGEMENT ==========

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le validateur pour addRepas.php
    const addForm = document.getElementById('addRepasForm');
    if (addForm) {
        new RepasFormValidator('addRepasForm');
    }
    
    // Initialiser le validateur pour editRepas.php
    const editForm = document.getElementById('editRepasForm');
    if (editForm) {
        new RepasFormValidator('editRepasForm');
    }
    
    // Initialiser le résumé nutritionnel
    updateNutritionSummary();
    
    // Ajouter des écouteurs pour mettre à jour le résumé
    const nutriInputs = ['calories', 'proteines', 'glucides', 'lipides'];
    nutriInputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', updateNutritionSummary);
        }
    });
    
    // Animation des champs au focus
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
    // Confirmation avant de quitter avec modifications (uniquement pour edit)
    if (editForm) {
        let formChanged = false;
        const inputs = editForm.querySelectorAll('input, select');
        inputs.forEach(field => {
            field.addEventListener('change', function() {
                formChanged = true;
            });
        });
        
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?';
            }
        });
    }
});