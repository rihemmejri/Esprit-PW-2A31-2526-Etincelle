// objectif.js - Contrôle de saisie et AJAX pour les objectifs nutritionnels

class ObjectifFormValidator {
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
            case 'poids_cible':
                const poids = parseFloat(value);
                if (!value) {
                    error = 'Le poids cible est obligatoire';
                } else if (isNaN(poids) || poids <= 0) {
                    error = 'Le poids doit être un nombre positif';
                } else if (poids > 300) {
                    error = 'Le poids ne doit pas dépasser 300 kg';
                } else if (poids < 30) {
                    error = 'Le poids doit être au minimum 30 kg';
                }
                break;

            case 'calories_objectif':
                const calories = parseInt(value);
                if (!value) {
                    error = 'L\'objectif de calories est obligatoire';
                } else if (isNaN(calories) || calories <= 0) {
                    error = 'Les calories doivent être un nombre positif';
                } else if (calories < 500) {
                    error = 'Les calories doivent être au minimum 500 kcal';
                } else if (calories > 10000) {
                    error = 'Les calories ne doivent pas dépasser 10000 kcal';
                }
                break;

            case 'eau_objectif':
                const eau = parseFloat(value);
                if (!value) {
                    error = 'L\'objectif d\'eau est obligatoire';
                } else if (isNaN(eau) || eau <= 0) {
                    error = 'L\'eau doit être un nombre positif';
                } else if (eau < 0.5) {
                    error = 'L\'eau doit être au minimum 0.5 litres';
                } else if (eau > 20) {
                    error = 'L\'eau ne doit pas dépasser 20 litres';
                }
                break;

            case 'date_debut':
                if (!value) {
                    error = 'La date de début est obligatoire';
                } else {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const selectedDate = new Date(value);
                    if (selectedDate < today) {
                        error = 'La date de début ne peut pas être dans le passé';
                    }
                }
                break;

            case 'date_fin':
                if (!value) {
                    error = 'La date de fin est obligatoire';
                } else {
                    const dateDebut = this.form.querySelector('[name="date_debut"]');
                    if (dateDebut && dateDebut.value) {
                        const debut = new Date(dateDebut.value);
                        const fin = new Date(value);
                        if (fin <= debut) {
                            error = 'La date de fin doit être après la date de début';
                        }
                    }
                }
                break;
        }

        this.showError(field, error);
        return !error;
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
        
        const textFields = ['poids_cible', 'calories_objectif', 'eau_objectif', 'date_debut', 'date_fin'];
        textFields.forEach(fieldName => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });
        
        if (isValid) {
            this.submitForm();
        } else {
            this.showErrorMessage('Veuillez corriger les erreurs dans le formulaire');
            const firstError = this.form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    submitForm() {
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData);

        // Determine if it's an add or update
        const isUpdate = !!data.id_objectif;
        const action = isUpdate ? 'update' : 'create';

        // Add the action to the data
        formData.append('action', action);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSuccessMessage(data.message || 'Opération effectuée avec succès !');
                this.form.reset();
                // Recharger la page après 2 secondes
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                this.showErrorMessage(data.message || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showErrorMessage('Erreur de connexion');
        });
    }

    showSuccessMessage(message) {
        this.removeMessages();
        
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.id = 'tempMessage';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        
        const formCard = document.querySelector('.form-card');
        if (formCard) {
            formCard.insertAdjacentElement('beforebegin', successDiv);
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
        if (formCard) {
            formCard.insertAdjacentElement('beforebegin', errorDiv);
        }

        setTimeout(() => {
            const msg = document.getElementById('tempMessage');
            if (msg) msg.remove();
        }, 3000);
    }

    removeMessages() {
        const tempMsg = document.getElementById('tempMessage');
        if (tempMsg) tempMsg.remove();
    }
}

// CRUD Operations via AJAX
class ObjectifManager {
    constructor() {
        this.validator = new ObjectifFormValidator('addObjectifForm');
        this.initEventListeners();
    }

    initEventListeners() {
        // Boutons d'édition
        document.querySelectorAll('.edit-objectif-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.editObjectif(e));
        });

        // Boutons de suppression
        document.querySelectorAll('.delete-objectif-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.deleteObjectif(e));
        });

        // Bouton annuler
        const cancelBtn = document.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelEdit());
        }
    }

    editObjectif(e) {
        const id = e.target.closest('button').dataset.id;
        
        fetch(`${window.location.href}?ajax=details&id=${id}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.populateForm(data);
                this.scrollToForm();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur de chargement');
        });
    }

    populateForm(objectif) {
        const form = document.getElementById('addObjectifForm');
        if (!form) return;

        form.querySelector('[name="id_objectif"]').value = objectif.id || '';
        form.querySelector('[name="poids_cible"]').value = objectif.poids_cible || '';
        form.querySelector('[name="calories_objectif"]').value = objectif.calories_objectif || '';
        form.querySelector('[name="eau_objectif"]').value = objectif.eau_objectif || '';
        form.querySelector('[name="date_debut"]').value = objectif.date_debut || '';
        form.querySelector('[name="date_fin"]').value = objectif.date_fin || '';

        // Changer le titre
        const formTitle = form.querySelector('.form-title');
        if (formTitle) {
            formTitle.textContent = 'Modifier l\'objectif';
        }

        // Changer le bouton
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Modifier';
            submitBtn.classList.add('edit-mode');
        }

        // Afficher le bouton annuler
        const cancelBtn = form.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.style.display = 'inline-block';
        }
    }

    cancelEdit() {
        const form = document.getElementById('addObjectifForm');
        if (!form) return;

        form.reset();
        form.querySelector('[name="id_objectif"]').value = '';

        const formTitle = form.querySelector('.form-title');
        if (formTitle) {
            formTitle.textContent = 'Ajouter un nouvel objectif';
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.textContent = 'Enregistrer';
            submitBtn.classList.remove('edit-mode');
        }

        const cancelBtn = form.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.style.display = 'none';
        }
    }

    deleteObjectif(e) {
        const id = e.target.closest('button').dataset.id;
        
        if (confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Objectif supprimé avec succès !');
                    location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur de suppression');
            });
        }
    }

    scrollToForm() {
        const form = document.querySelector('.form-card');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', () => {
    new ObjectifManager();
});
