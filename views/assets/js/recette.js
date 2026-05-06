// recette.js - Contrôle de saisie pour les formulaires de recettes

class RecetteFormValidator {
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
                    error = 'Le nom de la recette est obligatoire';
                } else if (value.length < 3) {
                    error = 'Le nom doit contenir au moins 3 caractères';
                } else if (value.length > 100) {
                    error = 'Le nom ne doit pas dépasser 100 caractères';
                } else if (!/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
                    error = 'Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes';
                }
                break;

            case 'description':
                if (!value) {
                    error = 'La description est obligatoire';
                } else if (value.length < 20) {
                    error = 'La description doit contenir au moins 20 caractères';
                } else if (value.length > 1000) {
                    error = 'La description ne doit pas dépasser 1000 caractères';
                }
                break;

            case 'temps_preparation':
                const temps = parseInt(value);
                if (isNaN(temps) || temps < 0) {
                    error = 'Le temps doit être un nombre positif';
                } else if (temps > 1440) {
                    error = 'Le temps ne doit pas dépasser 1440 minutes (24 heures)';
                }
                break;

            case 'nb_personne':
                const personnes = parseInt(value);
                if (isNaN(personnes) || personnes < 1) {
                    error = 'Le nombre de personnes doit être au moins 1';
                } else if (personnes > 100) {
                    error = 'Le nombre de personnes ne doit pas dépasser 100';
                }
                break;

            case 'origine':
                if (value && !/^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)) {
                    error = "L'origine ne doit contenir que des lettres, espaces, tirets et apostrophes";
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
            const error = `Veuillez sélectionner une option pour ${this.getFieldLabel(name)}`;
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

    getFieldLabel(name) {
        const labels = {
            'difficulte': 'la difficulté',
            'type_repas': 'le type de repas'
        };
        return labels[name] || name;
    }

    showError(field, message) {
        // Supprimer l'ancien message d'erreur
        const parent = field.parentElement;
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
        
        // Valider les champs texte
        const textFields = ['nom', 'description', 'temps_preparation', 'nb_personne', 'origine'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        // Valider les groupes radio
        if (!this.validateRadioGroup('difficulte')) isValid = false;
        if (!this.validateRadioGroup('type_repas')) isValid = false;
        
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
        header.insertAdjacentElement('afterend', successDiv);
        
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
        header.insertAdjacentElement('afterend', errorDiv);
        
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

// Fonctions utilitaires globales
function updateTemps(delta) {
    const input = document.getElementById('temps_preparation');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 0) value = 0;
        if (value > 1440) value = 1440;
        input.value = value;
        // Déclencher la validation
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
        // Déclencher la validation
        const event = new Event('blur');
        input.dispatchEvent(event);
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    new RecetteFormValidator('addRecetteForm');
    
    // Animation des champs
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
// Fonctions pour la liste des recettes (ajouter à la fin du fichier)

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
    
    // Update active page button
    const pageBtns = document.querySelectorAll('.page-btn');
    pageBtns.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent == currentPage) {
            btn.classList.add('active');
        }
    });
}

// Animation des lignes pour la liste
function animateTableRows() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.animation = `slideIn 0.3s ease-out ${index * 0.03}s both`;
    });
}

// Initialisation pour la page de liste
document.addEventListener('DOMContentLoaded', function() {
    animateTableRows();
});
// ========== FONCTIONS POUR LA PAGE DE SUPPRESSION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Animation pour le bouton de suppression
    const deleteBtn = document.querySelector('.btn-danger');
    if (deleteBtn) {
        deleteBtn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        deleteBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(-2px) scale(1)';
        });
    }
    
    // Animation pour la carte de confirmation
    const card = document.querySelector('.confirmation-card');
    if (card) {
        card.style.animation = 'slideIn 0.5s ease-out';
    }
    
    // Confirmation supplémentaire avant suppression
    const confirmDelete = document.querySelector('.btn-danger');
    if (confirmDelete) {
        confirmDelete.addEventListener('click', function(e) {
            // Animation de clic
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
    modal.classList.remove('active');
}

// Fermer le modal en cliquant en dehors
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
// Fonctions pour editRecette.php
function updateTemps(delta) {
    const input = document.getElementById('temps_preparation');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 0) value = 0;
        input.value = value;
    }
}

function updatePersonnes(delta) {
    const input = document.getElementById('nb_personne');
    if (input) {
        let value = parseInt(input.value) + delta;
        if (value < 1) value = 1;
        input.value = value;
    }
}

// Validation du formulaire d'édition
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editRecetteForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const nom = document.querySelector('input[name="nom"]').value.trim();
            const description = document.querySelector('textarea[name="description"]').value.trim();
            const typeRepas = document.querySelector('input[name="type_repas"]:checked');
            
            if (!nom || !description || !typeRepas) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    }
    
    // Confirmation avant de quitter sans sauvegarder
    let formChanged = false;
    const inputs = document.querySelectorAll('#editRecetteForm input, #editRecetteForm textarea, #editRecetteForm select');
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
});
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ? Cette action est irréversible.')) {
        window.location.href = 'deleteRecette.php?id=' + id + '&confirm=yes';
    }
}