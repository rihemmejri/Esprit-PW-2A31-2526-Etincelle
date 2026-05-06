// ========== participation.js - Contrôle de saisie formulaires participation ==========
// Style : preperation.js (fonctions simples) pour add/edit Participation

// ========== FONCTIONS POUR LES BOUTONS + ET - ==========

function updateNote(delta) {
    let input = document.getElementById('note');
    if (!input) return;
    let value = parseInt(input.value) || 0;
    let newValue = value + delta;
    if (newValue >= 1 && newValue <= 5) {
        input.value = newValue;
    } else if (newValue < 1) {
        input.value = 1;
    } else if (newValue > 5) {
        input.value = 5;
    }
    validateNote();
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

// Validation Événement (select obligatoire)
function validateEvenement() {
    const evenement = document.getElementById('evenementSelect');
    if (!evenement) return true;
    if (!evenement.value) {
        showError(evenement, 'Veuillez sélectionner un événement');
        return false;
    } else {
        clearError(evenement);
        return true;
    }
}

// Validation ID User (obligatoire, nombre entier positif)
function validateIdUser() {
    const idUser = document.getElementById('id_user');
    if (!idUser) return true;
    const value = parseInt(idUser.value);

    if (!idUser.value || isNaN(value)) {
        showError(idUser, 'L\'ID utilisateur est obligatoire');
        return false;
    } else if (value < 1) {
        showError(idUser, 'L\'ID utilisateur doit être un nombre positif');
        return false;
    } else {
        clearError(idUser);
        return true;
    }
}

// Validation Statut (radio obligatoire)
function validateStatutParticipation() {
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

// Validation Note (optionnelle, entre 1 et 5)
function validateNote() {
    const note = document.getElementById('note');
    if (!note) return true;

    if (note.value !== '') {
        const value = parseInt(note.value);
        if (isNaN(value) || value < 1 || value > 5) {
            showError(note, 'La note doit être comprise entre 1 et 5');
            return false;
        } else {
            clearError(note);
            return true;
        }
    } else {
        clearError(note);
        return true;
    }
}

// Validation Feedback (optionnel, minimum 5 caractères si renseigné)
function validateFeedback() {
    const feedback = document.getElementById('feedback');
    if (!feedback) return true;
    const value = feedback.value.trim();

    if (value !== '' && value.length < 5) {
        showError(feedback, 'Le feedback doit contenir au moins 5 caractères');
        return false;
    } else {
        clearError(feedback);
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

// ========== INITIALISATION FORMULAIRE ADD ==========

function initAddForm() {
    const form = document.getElementById('addParticipationForm');
    if (!form) return false;

    const evenementSelect = document.getElementById('evenementSelect');
    const idUser          = document.getElementById('id_user');
    const note            = document.getElementById('note');
    const feedback        = document.getElementById('feedback');

    if (evenementSelect) evenementSelect.addEventListener('change', validateEvenement);
    if (idUser)          idUser.addEventListener('input', validateIdUser);
    if (note)            note.addEventListener('input', validateNote);
    if (feedback)        feedback.addEventListener('input', validateFeedback);

    document.querySelectorAll('input[name="statut"]').forEach(radio => {
        radio.addEventListener('click', validateStatutParticipation);
    });

    form.addEventListener('submit', function (e) {
        let isValid = true;
        let errors = [];

        if (!validateEvenement()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un événement');
        }
        if (!validateIdUser()) {
            isValid = false;
            errors.push('❌ L\'ID utilisateur est obligatoire et doit être un nombre positif');
        }
        if (!validateStatutParticipation()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un statut');
        }
        if (!validateNote()) {
            isValid = false;
            errors.push('❌ La note doit être comprise entre 1 et 5');
        }
        if (!validateFeedback()) {
            isValid = false;
            errors.push('❌ Le feedback doit contenir au moins 5 caractères');
        }

        if (!isValid) {
            e.preventDefault();
            showErrorSummary(errors, form);
        }
    });

    return true;
}

// ========== INITIALISATION FORMULAIRE EDIT ==========

function initEditForm() {
    const form = document.getElementById('editParticipationForm');
    if (!form) return false;

    const note     = document.getElementById('note');
    const feedback = document.getElementById('feedback');

    if (note)     note.addEventListener('input', validateNote);
    if (feedback) feedback.addEventListener('input', validateFeedback);

    document.querySelectorAll('input[name="statut"]').forEach(radio => {
        radio.addEventListener('click', validateStatutParticipation);
    });

    form.addEventListener('submit', function (e) {
        let isValid = true;
        let errors = [];

        if (!validateStatutParticipation()) {
            isValid = false;
            errors.push('❌ Veuillez sélectionner un statut');
        }
        if (!validateNote()) {
            isValid = false;
            errors.push('❌ La note doit être comprise entre 1 et 5');
        }
        if (!validateFeedback()) {
            isValid = false;
            errors.push('❌ Le feedback doit contenir au moins 5 caractères');
        }

        if (!isValid) {
            e.preventDefault();
            showErrorSummary(errors, form);
        }
    });

    return true;
}

// ========== FONCTIONS LISTE BACK OFFICE ==========

let currentPage = 1;
const rowsPerPage = 10;

function searchTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filter = input.value.toLowerCase();
    const table  = document.getElementById('participationsTable');
    if (!table) return;

    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const row   = rows[i];
        const cells = row.getElementsByTagName('td');
        let found   = false;

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
    const csv  = rows.map(r => {
        const cells = Array.from(r.querySelectorAll('th, td')).slice(0, -1);
        return cells.map(c => '"' + c.innerText.replace(/"/g, '""') + '"').join(',');
    }).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'participations_export.csv';
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
    const rows        = document.querySelectorAll('tbody tr');
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const totalPages  = Math.ceil(visibleRows.length / rowsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updatePagination();
    }
}

function updatePagination() {
    const rows        = document.querySelectorAll('tbody tr');
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');

    const start = (currentPage - 1) * rowsPerPage;
    const end   = start + rowsPerPage;

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
    if (confirm('Êtes-vous sûr de vouloir supprimer cette participation ? Cette action est irréversible.')) {
        window.location.href = 'deleteParticipation.php?id=' + id + '&confirm=yes';
    }
}

// ========== CONFIRMATION RESET ==========

function confirmReset() {
    return confirm('⚠️ Êtes-vous sûr de vouloir réinitialiser le formulaire ?\n\nToutes les données saisies seront perdues.');
}

// ========== INITIALISATION AU CHARGEMENT ==========

document.addEventListener('DOMContentLoaded', function () {
    initAddForm();
    initEditForm();

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
        validateStatutParticipation();
        validateNote();
    }, 100);
}); 