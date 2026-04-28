// recette.js - Contrôle de saisie pour les formulaires de recettes

class RecetteFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => this.validateForm(e));
        
        const inputs = this.form.querySelectorAll('input, textarea, select');
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
                    error = '❌ Le nom de la recette est obligatoire';
                } else if (value.length < 3) {
                    error = '❌ Le nom doit contenir au moins 3 caractères';
                } else if (value.length > 100) {
                    error = '❌ Le nom ne doit pas dépasser 100 caractères';
                } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
                    error = '❌ Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes';
                }
                break;

            case 'description':
                if (!value) {
                    error = '❌ La description est obligatoire';
                } else if (value.length < 20) {
                    error = '❌ La description doit contenir au moins 20 caractères';
                } else if (value.length > 1000) {
                    error = '❌ La description ne doit pas dépasser 1000 caractères';
                }
                break;

            case 'temps_preparation':
                const temps = parseInt(value);
                if (isNaN(temps) || temps < 1) {
                    error = '⏱️ Le temps doit être un nombre supérieur à 0';
                } else if (temps > 1440) {
                    error = '⏱️ Le temps ne doit pas dépasser 1440 minutes (24 heures)';
                }
                break;

            case 'nb_personne':
                const personnes = parseInt(value);
                if (isNaN(personnes) || personnes < 1) {
                    error = '👥 Le nombre de personnes doit être au moins 1';
                } else if (personnes > 100) {
                    error = '👥 Le nombre de personnes ne doit pas dépasser 100';
                }
                break;

            case 'origine':
                if (!value) {
                    error = "❌ L'origine est obligatoire";
                } else if (value.length > 50) {
                    error = "❌ L'origine ne doit pas dépasser 50 caractères";
                } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
                    error = "❌ L'origine ne doit contenir que des lettres, espaces, tirets et apostrophes";
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
        
        let errorMessage = '';
        if (name === 'type_repas') {
            errorMessage = '❌ Veuillez sélectionner un type de repas (Petit déjeuner, Déjeuner, Dîner ou Dessert)';
        } else if (name === 'difficulte') {
            errorMessage = '❌ Veuillez sélectionner un niveau de difficulté (Facile, Moyen ou Difficile)';
        }
        
        if (!isChecked) {
            const firstRadio = radios[0];
            if (firstRadio) {
                let parent = firstRadio.closest('.difficulte-group') || firstRadio.closest('.type-repas-group');
                if (!parent) parent = firstRadio.parentElement.parentElement;
                this.showError(parent, errorMessage);
            }
            return false;
        }
        
        const firstRadio = radios[0];
        if (firstRadio) {
            let parent = firstRadio.closest('.difficulte-group') || firstRadio.closest('.type-repas-group');
            if (!parent) parent = firstRadio.parentElement.parentElement;
            this.showError(parent, '');
        }
        return true;
    }

    getFieldLabel(name) {
        const labels = {
            'difficulte': 'la difficulté',
            'type_repas': 'le type de repas'
        };
        return labels[name] || name;
    }

    showError(field, message) {
        const parent = field.parentElement;
        let errorDiv = parent.querySelector('.field-error');
        
        if (errorDiv) {
            errorDiv.remove();
        }
        
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
        
        const textFields = ['nom', 'description', 'temps_preparation', 'nb_personne', 'origine'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        if (!this.validateRadioGroup('difficulte')) isValid = false;
        if (!this.validateRadioGroup('type_repas')) isValid = false;
        
        if (isValid) {
            this.showSuccessMessage();
            this.form.submit();
        } else {
            this.showErrorMessage('⚠️ Veuillez corriger les erreurs dans le formulaire');
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
            <span>✅ Formulaire valide ! Redirection en cours...</span>
        `;
        
        const header = document.querySelector('.header');
        if (header) header.insertAdjacentElement('afterend', successDiv);
        
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
        
        const header = document.querySelector('.header');
        if (header) header.insertAdjacentElement('afterend', errorDiv);
        
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

// ========== FONCTIONS UTILITAIRES GLOBALES ==========

function updateTemps(delta) {
    const input = document.getElementById('temps_preparation');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 1440) value = 1440;
        input.value = value;
        const event = new Event('blur');
        input.dispatchEvent(event);
    }
}

function updatePersonnes(delta) {
    const input = document.getElementById('nb_personne');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 1) value = 1;
        if (value > 100) value = 100;
        input.value = value;
        const event = new Event('blur');
        input.dispatchEvent(event);
    }
}

// ========== INITIALISATION PAGE AJOUT ==========
document.addEventListener('DOMContentLoaded', function() {
    new RecetteFormValidator('addRecetteForm');
    
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});

// ========== FONCTIONS POUR LA LISTE DES RECETTES ==========

let currentPage = 1;
const rowsPerPage = 10;

function searchTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase();
    const table = document.getElementById('recettesTable');
    if (!table) return;
    
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length - 1; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        if (found) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
    
    currentPage = 1;
    updatePagination();
}

function exportTable() {
    alert('Fonctionnalité d\'export à venir !');
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        updatePagination();
    }
}

function nextPage() {
    const rows = document.querySelectorAll('tbody tr');
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const totalPages = Math.ceil(visibleRows.length / rowsPerPage);
    
    if (currentPage < totalPages) {
        currentPage++;
        updatePagination();
    }
}

function updatePagination() {
    const rows = document.querySelectorAll('tbody tr');
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    
    visibleRows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    const pageBtns = document.querySelectorAll('.page-btn');
    pageBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent == currentPage) {
            btn.classList.add('active');
        }
    });
}

function animateTableRows() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.animation = `slideIn 0.3s ease-out ${index * 0.03}s both`;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    animateTableRows();
});

// ========== FONCTIONS POUR LA PAGE DE SUPPRESSION ==========
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtn = document.querySelector('.btn-danger');
    if (deleteBtn) {
        deleteBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        deleteBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    }
    
    const card = document.querySelector('.confirmation-card');
    if (card) {
        card.style.animation = 'slideIn 0.5s ease-out';
    }
    
    const confirmDeleteBtn = document.querySelector('.btn-danger');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'translateY(-2px) scale(1)';
            }, 200);
        });
    }
});

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ? Cette action est irréversible.')) {
        window.location.href = 'deleteRecette.php?id=' + id + '&confirm=yes';
    }
}

// ========== FONCTIONS POUR LA PAGE D'AFFICHAGE CLIENT ==========

let allRecettes = [];

function filterRecettes() {
    const searchTerm = document.getElementById('searchRecette')?.value.toLowerCase() || '';
    const difficulte = document.getElementById('filterDifficulte')?.value || '';
    const typeRepas = document.getElementById('filterTypeRepas')?.value || '';
    
    const cards = document.querySelectorAll('.recipe-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const titre = card.dataset.titre?.toLowerCase() || '';
        const cardDifficulte = card.dataset.difficulte || '';
        const cardTypeRepas = card.dataset.type || '';
        
        let show = true;
        
        if (searchTerm && !titre.includes(searchTerm)) show = false;
        if (difficulte && cardDifficulte !== difficulte) show = false;
        if (typeRepas && cardTypeRepas !== typeRepas) show = false;
        
        if (show) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function openModal(id) {
    fetch(`afficherRecette.php?ajax=details&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let typeText = '';
                switch(data.type_repas) {
                    case 'PETIT_DEJEUNER': typeText = 'Petit déjeuner'; break;
                    case 'DEJEUNER': typeText = 'Déjeuner'; break;
                    case 'DINER': typeText = 'Dîner'; break;
                    case 'DESSERT': typeText = 'Dessert'; break;
                    default: typeText = data.type_repas;
                }
                
                let message = `🍽️ ${data.nom.toUpperCase()} 🍽️\n\n`;
                message += `⏰ Temps: ${data.temps_preparation} minutes\n`;
                message += `📊 Difficulté: ${data.difficulte}\n`;
                message += `🍴 Type: ${typeText}\n`;
                message += `👥 Pour: ${data.nb_personne} personnes\n`;
                if (data.origine) message += `🌍 Origine: ${data.origine}\n`;
                message += `🆔 ID: #${data.id}\n\n`;
                message += `📝 DESCRIPTION:\n${data.description}\n`;
                
                alert(message);
            } else {
                alert('Erreur: Recette non trouvée');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur de chargement');
        });
}

function closeModal() {
    const modal = document.getElementById('recipeModal');
    if (modal) modal.classList.remove('active');
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('recipeModal');
    if (event.target === modal) {
        closeModal();
    }
});

function showDetails(recette) {
    let typeText = '';
    switch(recette.type_repas) {
        case 'PETIT_DEJEUNER': typeText = 'Petit déjeuner'; break;
        case 'DEJEUNER': typeText = 'Déjeuner'; break;
        case 'DINER': typeText = 'Dîner'; break;
        case 'DESSERT': typeText = 'Dessert'; break;
        default: typeText = recette.type_repas;
    }
    
    let message = `🍽️ ${recette.nom.toUpperCase()} 🍽️\n\n`;
    message += `⏰ Temps: ${recette.temps_preparation} minutes\n`;
    message += `📊 Difficulté: ${recette.difficulte}\n`;
    message += `🍴 Type: ${typeText}\n`;
    message += `👥 Pour: ${recette.nb_personne} personnes\n`;
    if (recette.origine) message += `🌍 Origine: ${recette.origine}\n`;
    message += `🆔 ID: #${recette.id}\n\n`;
    message += `📝 DESCRIPTION:\n${recette.description}\n`;
    
    alert(message);
}

// ========== VALIDATION POUR LA PAGE MODIFICATION (editRecette.php) ==========

class EditRecetteValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => this.validateForm(e));
        
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.validateField(input));
        });
    }

    validateField(field) {
        const name = field.name;
        const value = field.value?.trim() || '';
        let error = '';

        switch(name) {
            case 'nom':
                if (!value) {
                    error = '❌ Le nom de la recette est obligatoire';
                } else if (value.length < 3) {
                    error = '❌ Le nom doit contenir au moins 3 caractères';
                } else if (value.length > 10) {
                    error = '❌ Le nom ne doit pas dépasser 100 caractères';
                } else if (!/^[a-zA-ZÀ-ÿ0-9\s\-']+$/.test(value)) {
                    error = '❌ Le nom ne doit contenir que des lettres, chiffres, espaces, tirets et apostrophes';
                }
                break;

            case 'description':
                if (!value) {
                    error = '❌ La description est obligatoire';
                } else if (value.length < 20) {
                    error = `❌ La description doit contenir au moins 20 caractères (actuellement: ${value.length})`;
                } else if (value.length > 200) {
                    error = '❌ La description ne doit pas dépasser 2000 caractères';
                }
                break;

            case 'temps_preparation':
                const temps = parseInt(value);
                if (isNaN(temps)) {
                    error = '❌ Le temps doit être un nombre valide';
                } else if (temps < 1) {
                    error = '❌ Le temps doit être supérieur à 0';
                } else if (temps > 1440) {
                    error = '❌ Le temps ne doit pas dépasser 1440 minutes (24 heures)';
                }
                break;

            case 'nb_personne':
                const personnes = parseInt(value);
                if (isNaN(personnes)) {
                    error = '❌ Le nombre de personnes doit être un nombre valide';
                } else if (personnes < 1) {
                    error = '❌ Le nombre de personnes doit être au moins 1';
                } else if (personnes > 10) {
                    error = '❌ Le nombre de personnes ne doit pas dépasser 100';
                }
                break;

            case 'origine':
                if (!value) {
                    error = "❌ L'origine est obligatoire";
                } else if (value.length > 50) {
                    error = "❌ L'origine ne doit pas dépasser 50 caractères";
                } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
                    error = "❌ L'origine ne doit contenir que des lettres, espaces, tirets et apostrophes";
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
        
        let errorMessage = '';
        if (name === 'type_repas') {
            errorMessage = '❌ Veuillez sélectionner un type de repas (Petit déjeuner, Déjeuner, Dîner ou Dessert)';
        } else if (name === 'difficulte') {
            errorMessage = '❌ Veuillez sélectionner un niveau de difficulté (Facile, Moyen ou Difficile)';
        }
        
        if (!isChecked) {
            const firstRadio = radios[0];
            if (firstRadio) {
                let parent = firstRadio.closest('.difficulte-group') || firstRadio.closest('.type-repas-group');
                if (!parent) parent = firstRadio.parentElement.parentElement;
                this.showError(parent, errorMessage);
            }
            return false;
        }
        
        const firstRadio = radios[0];
        if (firstRadio) {
            let parent = firstRadio.closest('.difficulte-group') || firstRadio.closest('.type-repas-group');
            if (!parent) parent = firstRadio.parentElement.parentElement;
            this.showError(parent, '');
        }
        return true;
    }

    getFieldLabel(name) {
        const labels = {
            'difficulte': 'la difficulté',
            'type_repas': 'le type de repas'
        };
        return labels[name] || name;
    }

    showError(element, message) {
        let parent = element.parentElement;
        if (parent.classList.contains('input-icon')) {
            parent = parent.parentElement;
        }
        
        let errorDiv = parent.querySelector('.field-error-edit');
        if (errorDiv) {
            errorDiv.remove();
        }
        
        if (message) {
            element.classList.add('error-edit');
            element.classList.remove('valid-edit');
            errorDiv = document.createElement('span');
            errorDiv.className = 'field-error-edit';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            errorDiv.style.cssText = 'display: block; color: #dc3545; font-size: 0.75rem; margin-top: 5px;';
            parent.appendChild(errorDiv);
        } else {
            element.classList.remove('error-edit');
            element.classList.add('valid-edit');
        }
    }

    validateForm(e) {
        e.preventDefault();
        
        let isValid = true;
        
        const textFields = ['nom', 'description', 'temps_preparation', 'nb_personne', 'origine'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        if (!this.validateRadioGroup('difficulte')) isValid = false;
        if (!this.validateRadioGroup('type_repas')) isValid = false;
        
        if (isValid) {
            this.showSuccessMessage();
            setTimeout(() => {
                this.form.submit();
            }, 500);
        } else {
            this.showErrorMessage('⚠️ Veuillez corriger les erreurs dans le formulaire');
            const firstError = this.form.querySelector('.error-edit');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    showSuccessMessage() {
        this.removeMessages();
        
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message-edit';
        successDiv.id = 'tempMessageEdit';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>✅ Formulaire valide ! Enregistrement en cours...</span>
        `;
        successDiv.style.cssText = `
            background: #d1fae5;
            color: #065f46;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        `;
        
        const header = document.querySelector('.header');
        if (header) header.insertAdjacentElement('afterend', successDiv);
        
        setTimeout(() => {
            const msg = document.getElementById('tempMessageEdit');
            if (msg) msg.remove();
        }, 3000);
    }

    showErrorMessage(message) {
        this.removeMessages();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message-edit';
        errorDiv.id = 'tempMessageEdit';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        errorDiv.style.cssText = `
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        `;
        
        const header = document.querySelector('.header');
        if (header) header.insertAdjacentElement('afterend', errorDiv);
        
        setTimeout(() => {
            const msg = document.getElementById('tempMessageEdit');
            if (msg) msg.remove();
        }, 5000);
    }

    removeMessages() {
        const tempMsg = document.getElementById('tempMessageEdit');
        if (tempMsg) tempMsg.remove();
    }
}

// ========== FONCTIONS POUR LES BOUTONS + ET - (PAGE EDIT) ==========

function updateTempsEdit(delta) {
    const input = document.getElementById('temps_preparation');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (isNaN(value)) value = 0;
        if (value < 0) value = 0;
        if (value > 1440) value = 1440;
        input.value = value;
        
        const blurEvent = new Event('blur');
        input.dispatchEvent(blurEvent);
    }
}

function updatePersonnesEdit(delta) {
    const input = document.getElementById('nb_personne');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (isNaN(value)) value = 1;
        if (value < 1) value = 1;
        if (value > 100) value = 100;
        input.value = value;
        
        const blurEvent = new Event('blur');
        input.dispatchEvent(blurEvent);
    }
}

// ========== CONFIRMATION DE SORTIE SANS SAUVEGARDE ==========

function setupBeforeUnload() {
    let formChanged = false;
    const form = document.getElementById('editRecetteForm');
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(field => {
        field.addEventListener('change', () => {
            formChanged = true;
        });
        field.addEventListener('input', () => {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '⚠️ Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?';
            return e.returnValue;
        }
    });
    
    form.addEventListener('submit', () => {
        formChanged = false;
    });
}

// ========== INITIALISATION POUR LA PAGE EDIT ==========

document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editRecetteForm');
    if (editForm) {
        new EditRecetteValidator('editRecetteForm');
        setupBeforeUnload();
        
        window.updateTemps = updateTempsEdit;
        window.updatePersonnes = updatePersonnesEdit;
    }
    
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});