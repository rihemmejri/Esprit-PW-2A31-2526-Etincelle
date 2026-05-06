// programme.js - Contrôles de saisie pour addProgramme.php et editProgramme.php

// ========== VALIDATEUR POUR LES FORMULAIRES DE PROGRAMME ==========

class ProgrammeFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => this.validateForm(e));
        
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
            case 'id_user':
                const idUser = parseInt(value);
                if (!value) {
                    error = "L'ID utilisateur est obligatoire";
                } else if (isNaN(idUser) || idUser <= 0) {
                    error = "L'ID utilisateur doit être un nombre positif";
                }
                break;

            case 'objectif':
                if (!value) {
                    error = "L'objectif est obligatoire";
                }
                break;

            case 'date_debut':
                if (!value) {
                    error = "La date de début est obligatoire";
                } else {
                    const dateDebut = new Date(value);
                    const anneeMin = new Date('2026-01-01');
                    
                    if (dateDebut < anneeMin) {
                        error = "La date de début ne peut pas être antérieure à 2026";
                    }
                }
                break;

            case 'date_fin':
                if (!value) {
                    error = "La date de fin est obligatoire";
                } else {
                    const dateFin = new Date(value);
                    const anneeMin = new Date('2026-01-01');
                    const dateDebut = this.form.querySelector('input[name="date_debut"]')?.value;
                    
                    if (dateFin < anneeMin) {
                        error = "La date de fin ne peut pas être antérieure à 2026";
                    } else if (dateDebut && new Date(value) < new Date(dateDebut)) {
                        error = "La date de fin doit être postérieure à la date de début";
                    }
                }
                break;
        }

        this.showError(field, error);
        return !error;
    }

    validateRepasSelection() {
        const repasSelects = this.form.querySelectorAll('.repas-select');
        let hasRepas = false;
        
        repasSelects.forEach(select => {
            if (select.value && select.value !== '') {
                hasRepas = true;
            }
        });
        
        if (!hasRepas) {
            const repasSelector = document.querySelector('.repas-selector');
            if (repasSelector) {
                let oldError = repasSelector.querySelector('.repas-error');
                if (oldError) oldError.remove();
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'repas-error field-error';
                errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; margin-top: 10px; text-align: center;';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez ajouter au moins un repas au programme';
                repasSelector.appendChild(errorDiv);
            }
            return false;
        }
        
        const repasSelector = document.querySelector('.repas-selector');
        if (repasSelector) {
            let oldError = repasSelector.querySelector('.repas-error');
            if (oldError) oldError.remove();
        }
        return true;
    }

    showError(field, message) {
        let parent = field.parentElement;
        
        if (parent.classList.contains('input-icon')) {
            parent = parent.parentElement;
        }
        
        let errorDiv = parent.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        
        if (message) {
            field.classList.add('error');
            field.classList.remove('valid');
            errorDiv = document.createElement('span');
            errorDiv.className = 'field-error';
            errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; display: block; margin-top: 5px;';
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
        
        const fields = ['id_user', 'objectif', 'date_debut', 'date_fin'];
        fields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        if (!this.validateRepasSelection()) {
            isValid = false;
        }
        
        if (isValid) {
            this.showSuccessMessage();
            this.form.submit();
        } else {
            this.showErrorMessage('Veuillez corriger les erreurs dans le formulaire');
            const firstError = this.form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    showSuccessMessage() {
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

// ========== FONCTIONS POUR AJOUTER/SUPPRIMER DES LIGNES DE REPAS ==========

let repasCount = 0;

function addRepasRow(repasId = '', jour = '', type = '') {
    const container = document.getElementById('repasContainer');
    if (!container) return;
    
    const row = document.createElement('div');
    row.className = 'repas-row';
    row.id = `repas-row-${repasCount}`;
    row.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 0.5fr; gap: 10px; margin-bottom: 10px; align-items: center;';
    
    const repasSelect = document.createElement('select');
    repasSelect.name = `repas[]`;
    repasSelect.className = 'repas-select';
    repasSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
    repasSelect.innerHTML = '<option value="">-- Choisir un repas --</option>';
    
    if (window.allRepas && window.allRepas.length > 0) {
        window.allRepas.forEach(repas => {
            repasSelect.innerHTML += `<option value="${repas.id_repas}" ${repas.id_repas == repasId ? 'selected' : ''}>${repas.nom} (${repas.calories} kcal) - ${repas.type}</option>`;
        });
    }
    
    repasSelect.addEventListener('change', function() {
        if (this.value) {
            this.style.borderColor = '#4CAF50';
        } else {
            this.style.borderColor = '#dc3545';
        }
    });
    
    const jourSelect = document.createElement('select');
    jourSelect.name = `jour_semaine[]`;
    jourSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
    jourSelect.innerHTML = `
        <option value="LUNDI" ${jour == 'LUNDI' ? 'selected' : ''}>Lundi</option>
        <option value="MARDI" ${jour == 'MARDI' ? 'selected' : ''}>Mardi</option>
        <option value="MERCREDI" ${jour == 'MERCREDI' ? 'selected' : ''}>Mercredi</option>
        <option value="JEUDI" ${jour == 'JEUDI' ? 'selected' : ''}>Jeudi</option>
        <option value="VENDREDI" ${jour == 'VENDREDI' ? 'selected' : ''}>Vendredi</option>
        <option value="SAMEDI" ${jour == 'SAMEDI' ? 'selected' : ''}>Samedi</option>
        <option value="DIMANCHE" ${jour == 'DIMANCHE' ? 'selected' : ''}>Dimanche</option>
    `;
    
    const typeSelect = document.createElement('select');
    typeSelect.name = `type_repas[]`;
    typeSelect.style.cssText = 'padding: 8px; border-radius: 8px; border: 1px solid #ddd;';
    typeSelect.innerHTML = `
        <option value="PETIT_DEJEUNER" ${type == 'PETIT_DEJEUNER' ? 'selected' : ''}>☕ Petit déjeuner</option>
        <option value="DEJEUNER" ${type == 'DEJEUNER' ? 'selected' : ''}>🍽️ Déjeuner</option>
        <option value="DINER" ${type == 'DINER' ? 'selected' : ''}>🌙 Dîner</option>
        <option value="COLLATION" ${type == 'COLLATION' ? 'selected' : ''}>🍎 Collation</option>
    `;
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove';
    removeBtn.style.cssText = 'background: #dc3545; color: white; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 0.8rem;';
    removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
    removeBtn.onclick = () => {
        row.remove();
        validateRepasSelectionAfterChange();
    };
    
    row.appendChild(repasSelect);
    row.appendChild(jourSelect);
    row.appendChild(typeSelect);
    row.appendChild(removeBtn);
    
    container.appendChild(row);
    repasCount++;
}

function validateRepasSelectionAfterChange() {
    const repasSelects = document.querySelectorAll('.repas-select');
    let hasRepas = false;
    repasSelects.forEach(select => {
        if (select.value && select.value !== '') {
            hasRepas = true;
        }
    });
    
    const repasSelector = document.querySelector('.repas-selector');
    if (repasSelector) {
        let oldError = repasSelector.querySelector('.repas-error');
        if (!hasRepas && !oldError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'repas-error field-error';
            errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; margin-top: 10px; text-align: center;';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez ajouter au moins un repas au programme';
            repasSelector.appendChild(errorDiv);
        } else if (hasRepas && oldError) {
            oldError.remove();
        }
    }
}

// ========== VALIDATION DES DATES EN TEMPS RÉEL ==========

function validateDates() {
    const dateDebut = document.querySelector('input[name="date_debut"]')?.value;
    const dateFin = document.querySelector('input[name="date_fin"]')?.value;
    const dateDebutField = document.querySelector('input[name="date_debut"]');
    const dateFinField = document.querySelector('input[name="date_fin"]');
    const anneeMin = '2026-01-01';
    
    // Validation date début
    if (dateDebut && dateDebutField) {
        if (dateDebut < anneeMin) {
            let parent = dateDebutField.parentElement;
            if (parent.classList.contains('input-icon')) parent = parent.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (!errorDiv) {
                errorDiv = document.createElement('span');
                errorDiv.className = 'field-error';
                errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; display: block; margin-top: 5px;';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> La date de début ne peut pas être antérieure à 2026';
                parent.appendChild(errorDiv);
            }
            dateDebutField.classList.add('error');
        } else {
            let parent = dateDebutField.parentElement;
            if (parent.classList.contains('input-icon')) parent = parent.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (errorDiv) errorDiv.remove();
            dateDebutField.classList.remove('error');
            dateDebutField.classList.add('valid');
        }
    }
    
    // Validation date fin
    if (dateFin && dateFinField) {
        if (dateFin < anneeMin) {
            let parent = dateFinField.parentElement;
            if (parent.classList.contains('input-icon')) parent = parent.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (!errorDiv) {
                errorDiv = document.createElement('span');
                errorDiv.className = 'field-error';
                errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; display: block; margin-top: 5px;';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> La date de fin ne peut pas être antérieure à 2026';
                parent.appendChild(errorDiv);
            }
            dateFinField.classList.add('error');
        } else if (dateDebut && dateFin < dateDebut) {
            let parent = dateFinField.parentElement;
            if (parent.classList.contains('input-icon')) parent = parent.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (!errorDiv) {
                errorDiv = document.createElement('span');
                errorDiv.className = 'field-error';
                errorDiv.style.cssText = 'color: #dc3545; font-size: 0.75rem; display: block; margin-top: 5px;';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> La date de fin doit être postérieure à la date de début';
                parent.appendChild(errorDiv);
            }
            dateFinField.classList.add('error');
        } else {
            let parent = dateFinField.parentElement;
            if (parent.classList.contains('input-icon')) parent = parent.parentElement;
            let errorDiv = parent.querySelector('.field-error');
            if (errorDiv) errorDiv.remove();
            dateFinField.classList.remove('error');
            dateFinField.classList.add('valid');
        }
    }
}

// ========== INITIALISATION AU CHARGEMENT ==========

document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('addProgrammeForm');
    if (addForm) {
        new ProgrammeFormValidator('addProgrammeForm');
    }
    
    const editForm = document.getElementById('editProgrammeForm');
    if (editForm) {
        new ProgrammeFormValidator('editProgrammeForm');
    }
    
    const dateDebutInput = document.querySelector('input[name="date_debut"]');
    const dateFinInput = document.querySelector('input[name="date_fin"]');
    
    if (dateDebutInput) {
        dateDebutInput.addEventListener('change', validateDates);
        dateDebutInput.addEventListener('input', validateDates);
    }
    if (dateFinInput) {
        dateFinInput.addEventListener('change', validateDates);
        dateFinInput.addEventListener('input', validateDates);
    }
    
    document.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
    
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
    
    validateRepasSelectionAfterChange();
});