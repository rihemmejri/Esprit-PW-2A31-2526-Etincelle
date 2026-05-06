// ========== evenement.js - Contrôle de saisie formulaires événements ==========
// Style : recette.js (classe validator) pour add/edit Evenement

// ========== FONCTIONS POUR LES BOUTONS + ET - ==========

function updatePlaces(delta) {
    let input = document.getElementById('nb_places_max');
    if (!input) return;
    let value = parseInt(input.value) || 0;
    let newValue = value + delta;
    if (newValue >= 1) {
        input.value = newValue;
    } else {
        input.value = 1;
    }
    validatePlaces();
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

// Validation Titre (obligatoire)
function validateTitre() {
    const titre = document.getElementById('titre');
    if (!titre) return true;
    const value = titre.value.trim();

    if (!value) {
        showError(titre, 'Le titre est obligatoire');
        return false;
    } else if (value.length < 5) {
        showError(titre, 'Le titre doit contenir au moins 5 caractères');
        return false;
    } else if (value.length > 255) {
        showError(titre, 'Le titre ne doit pas dépasser 255 caractères');
        return false;
    } else {
        clearError(titre);
        return true;
    }
}

// Validation Description (obligatoire)
function validateDescription() {
    const description = document.getElementById('description');
    if (!description) return true;
    const value = description.value.trim();

    if (!value) {
        showError(description, 'La description est obligatoire');
        return false;
    } else if (value.length < 20) {
        showError(description, 'La description doit contenir au moins 20 caractères');
        return false;
    } else if (value.length > 2000) {
        showError(description, 'La description ne doit pas dépasser 2000 caractères');
        return false;
    } else {
        clearError(description);
        return true;
    }
}

// Validation Date (obligatoire + date future)
function validateDate() {
    const date = document.getElementById('date_evenement');
    if (!date) return true;
    const value = date.value;

    if (!value) {
        showError(date, 'La date est obligatoire');
        return false;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const chosen = new Date(value);

    if (chosen < today) {
        showError(date, 'La date doit être aujourd\'hui ou dans le futur');
        return false;
    } else {
        clearError(date);
        return true;
    }
}

// Validation Lieu (obligatoire)
function validateLieu() {
    const lieu = document.getElementById('lieu');
    if (!lieu) return true;
    const value = lieu.value.trim();

    if (!value) {
        showError(lieu, 'Le lieu est obligatoire');
        return false;
    } else if (value.length < 3) {
        showError(lieu, 'Le lieu doit contenir au moins 3 caractères');
        return false;
    } else if (value.length > 255) {
        showError(lieu, 'Le lieu ne doit pas dépasser 255 caractères');
        return false;
    } else {
        clearError(lieu);
        return true;
    }
}

// Validation Places (obligatoire)
function validatePlaces() {
    const places = document.getElementById('nb_places_max');
    if (!places) return true;
    const value = parseInt(places.value);

    if (isNaN(value) || value < 1) {
        showError(places, 'Le nombre de places doit être au moins 1');
        return false;
    } else if (value > 500) {
        showError(places, 'Le nombre de places ne peut pas dépasser 500');
        return false;
    } else {
        clearError(places);
        return true;
    }
}

// Validation Type événement (radio obligatoire)
function validateTypeEvenement() {
    const selected = document.querySelector('input[name="type_evenement"]:checked');
    const group = document.getElementById('typeEvenementGroup');
    if (!group) return true;

    if (!selected) {
        const errorDiv = document.getElementById('typeEvenementError');
        if (!errorDiv) {
            const error = document.createElement('div');
            error.id = 'typeEvenementError';
            error.className = 'error-field';
            error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un type d\'événement';
            group.parentNode.insertBefore(error, group.nextSibling);
        }
        return false;
    } else {
        const errorDiv = document.getElementById('typeEvenementError');
        if (errorDiv) errorDiv.remove();
        return true;
    }
}

// Validation Statut (radio obligatoire)
function validateStatut() {
    const selected = document.querySelector('input[name="statut"]:checked');
    const group = document.getElementById('statutGroup');
    if (!group) return true;

    if (!selected) {
        const errorDiv = document.getElementById('statutError');
        if (!errorDiv) {
            const error = document.createElement('div');
            error.id = 'statutError';
            error.className = 'error-field';
            error.innerHTML = '<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner un statut';
            group.parentNode.insertBefore(error, group.nextSibling);
        }
        return false;
    } else {
        const errorDiv = document.getElementById('statutError');
        if (errorDiv) errorDiv.remove();
        return true;
    }
}

// ========== RÉSUMÉ DES ERREURS ==========

function showErrorSummary(errors, form) {
    const oldSummary = document.querySelector('.error-summary');
    if (oldSummary) oldSummary.remove();

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

// ========== CLASSE VALIDATOR EVENEMENT (style RecetteFormValidator) ==========

class EvenementFormValidator {
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

        // Radios en temps réel
        this.form.querySelectorAll('input[name="type_evenement"]').forEach(radio => {
            radio.addEventListener('click', validateTypeEvenement);
        });
        this.form.querySelectorAll('input[name="statut"]').forEach(radio => {
            radio.addEventListener('click', validateStatut);
        });
    }

    validateField(field) {
        const name = field.name;
        const value = field.value.trim();
        let error = '';

        switch (name) {
            case 'titre':
                if (!value) {
                    error = '❌ Le titre est obligatoire';
                } else if (value.length < 5) {
                    error = '❌ Le titre doit contenir au moins 5 caractères';
                } else if (value.length > 255) {
                    error = '❌ Le titre ne doit pas dépasser 255 caractères';
                }
                break;

            case 'description':
                if (!value) {
                    error = '❌ La description est obligatoire';
                } else if (value.length < 20) {
                    error = `❌ La description doit contenir au moins 20 caractères (actuellement: ${value.length})`;
                } else if (value.length > 2000) {
                    error = '❌ La description ne doit pas dépasser 2000 caractères';
                }
                break;

            case 'date_evenement':
                if (!value) {
                    error = '❌ La date est obligatoire';
                } else {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const chosen = new Date(value);
                    if (chosen < today) {
                        error = '❌ La date doit être aujourd\'hui ou dans le futur';
                    }
                }
                break;

            case 'lieu':
                if (!value) {
                    error = '❌ Le lieu est obligatoire';
                } else if (value.length < 3) {
                    error = '❌ Le lieu doit contenir au moins 3 caractères';
                } else if (value.length > 255) {
                    error = '❌ Le lieu ne doit pas dépasser 255 caractères';
                }
                break;

            case 'nb_places_max':
                const places = parseInt(value);
                if (isNaN(places) || places < 1) {
                    error = '❌ Le nombre de places doit être au moins 1';
                } else if (places > 500) {
                    error = '❌ Le nombre de places ne peut pas dépasser 500';
                }
                break;
        }

        this.showError(field, error);
        return !error;
    }

    showError(field, message) {
        const parent = field.parentElement;
        let errorDiv = parent.querySelector('.field-error');

        if (errorDiv) errorDiv.remove();

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

        const textFields = ['titre', 'description', 'date_evenement', 'lieu', 'nb_places_max'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });

        if (!validateTypeEvenement()) isValid = false;
        if (!validateStatut()) isValid = false;

        if (isValid) {
            this.showSuccessMessage();
            setTimeout(() => {
                this.form.submit();
            }, 500);
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

// ========== CLASSE VALIDATOR EDIT EVENEMENT ==========

class EditEvenementValidator {
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

        this.form.querySelectorAll('input[name="type_evenement"]').forEach(radio => {
            radio.addEventListener('click', validateTypeEvenement);
        });
        this.form.querySelectorAll('input[name="statut"]').forEach(radio => {
            radio.addEventListener('click', validateStatut);
        });
    }

    validateField(field) {
        const name = field.name;
        const value = field.value?.trim() || '';
        let error = '';

        switch (name) {
            case 'titre':
                if (!value) {
                    error = '❌ Le titre est obligatoire';
                } else if (value.length < 5) {
                    error = '❌ Le titre doit contenir au moins 5 caractères';
                } else if (value.length > 255) {
                    error = '❌ Le titre ne doit pas dépasser 255 caractères';
                }
                break;

            case 'description':
                if (!value) {
                    error = '❌ La description est obligatoire';
                } else if (value.length < 20) {
                    error = `❌ La description doit contenir au moins 20 caractères (actuellement: ${value.length})`;
                } else if (value.length > 2000) {
                    error = '❌ La description ne doit pas dépasser 2000 caractères';
                }
                break;

            case 'lieu':
                if (!value) {
                    error = '❌ Le lieu est obligatoire';
                } else if (value.length < 3) {
                    error = '❌ Le lieu doit contenir au moins 3 caractères';
                }
                break;

            case 'nb_places_max':
                const places = parseInt(value);
                if (isNaN(places) || places < 1) {
                    error = '❌ Le nombre de places doit être au moins 1';
                } else if (places > 500) {
                    error = '❌ Le nombre de places ne peut pas dépasser 500';
                }
                break;
        }

        this.showError(field, error);
        return !error;
    }

    showError(element, message) {
        let parent = element.parentElement;
        if (parent.classList.contains('input-icon')) {
            parent = parent.parentElement;
        }

        let errorDiv = parent.querySelector('.field-error-edit');
        if (errorDiv) errorDiv.remove();

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

        const textFields = ['titre', 'description', 'lieu', 'nb_places_max'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });

        if (!validateTypeEvenement()) isValid = false;
        if (!validateStatut()) isValid = false;

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
        successDiv.id = 'tempMessageEdit';
        successDiv.innerHTML = `<i class="fas fa-check-circle"></i><span>✅ Formulaire valide ! Enregistrement en cours...</span>`;
        successDiv.style.cssText = 'background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;';

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
        errorDiv.id = 'tempMessageEdit';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${message}</span>`;
        errorDiv.style.cssText = 'background:#fee2e2;color:#991b1b;padding:12px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;';

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

// ========== FONCTIONS LISTE BACK OFFICE ==========

let currentPage = 1;
const rowsPerPage = 10;

function searchTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filter = input.value.toLowerCase();
    const table = document.getElementById('evenementsTable');
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

        row.style.display = found ? '' : 'none';
    }

    currentPage = 1;
    updatePagination();
}

function exportTable() {
    const table = document.querySelector('table');
    if (!table) return;
    const rows = Array.from(table.querySelectorAll('tr'));
    const csv = rows.map(r => {
        const cells = Array.from(r.querySelectorAll('th, td')).slice(0, -1);
        return cells.map(c => '"' + c.innerText.replace(/"/g, '""') + '"').join(',');
    }).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'evenements_export.csv';
    a.click();
    URL.revokeObjectURL(a.href);
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
        row.style.display = (index >= start && index < end) ? '' : 'none';
    });

    const pageBtns = document.querySelectorAll('.page-btn');
    pageBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent == currentPage) btn.classList.add('active');
    });
}

function animateTableRows() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.animation = `slideIn 0.3s ease-out ${index * 0.03}s both`;
    });
}

// ========== FONCTIONS SUPPRESSION ==========

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
        window.location.href = 'deleteEvenement.php?id=' + id + '&confirm=yes';
    }
}

// ========== FONCTIONS FRONT OFFICE ==========

function filterEvenements() {
    const searchTerm = document.getElementById('searchEvenement')?.value.toLowerCase() || '';
    const type = document.getElementById('filterType')?.value || '';
    const statut = document.getElementById('filterStatut')?.value || '';

    const cards = document.querySelectorAll('.event-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const titre = card.dataset.titre?.toLowerCase() || '';
        const cardType = card.dataset.type || '';
        const cardStatut = card.dataset.statut || '';

        let show = true;
        if (searchTerm && !titre.includes(searchTerm)) show = false;
        if (type && cardType !== type) show = false;
        if (statut && cardStatut !== statut) show = false;

        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    const noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}

function openModal(id) {
    const modal = document.getElementById('eventModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal() {
    const modal = document.getElementById('eventModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

document.addEventListener('click', function (event) {
    const modal = document.getElementById('eventModal');
    if (event.target === modal) closeModal();
});

// ========== CONFIRMATION RESET ==========

function confirmReset() {
    return confirm('⚠️ Êtes-vous sûr de vouloir réinitialiser le formulaire ?\n\nToutes les données saisies seront perdues.');
}

// ========== SETUP BEFORE UNLOAD (EDIT) ==========

function setupBeforeUnload() {
    let formChanged = false;
    const form = document.getElementById('editEvenementForm');
    if (!form) return;

    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(field => {
        field.addEventListener('change', () => { formChanged = true; });
        field.addEventListener('input',  () => { formChanged = true; });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '⚠️ Vous avez des modifications non enregistrées. Êtes-vous sûr de vouloir quitter ?';
            return e.returnValue;
        }
    });

    form.addEventListener('submit', () => { formChanged = false; });
}

// ========== INITIALISATION AU CHARGEMENT ==========

document.addEventListener('DOMContentLoaded', function () {
    // ADD form
    new EvenementFormValidator('addEvenementForm');

    // EDIT form
    const editForm = document.getElementById('editEvenementForm');
    if (editForm) {
        new EditEvenementValidator('editEvenementForm');
        setupBeforeUnload();
    }

    animateTableRows();

    // Bouton supprimer animation
    const deleteBtn = document.querySelector('.btn-danger');
    if (deleteBtn) {
        deleteBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        deleteBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    }

    // Focus effects
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function () {
            this.parentElement.classList.remove('focused');
        });
    });

    // Valider champs déjà remplis (page edit)
    setTimeout(function () {
        validateTypeEvenement();
        validateStatut();
    }, 100);
});