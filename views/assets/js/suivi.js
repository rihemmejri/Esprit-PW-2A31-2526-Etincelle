// suivi.js - AJAX handling and validation for tracking entries

class SuiviFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.init();
    }

    init() {
        if (!this.form) return;

        // Real-time validation
        const inputs = this.form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.validateField(input));
        });

        this.form.addEventListener('submit', (e) => this.validateForm(e));
    }

    validateField(field) {
        const name = field.name;
        const value = field.value.trim();
        let error = '';

        switch (name) {
            case 'user_id':
                if (!value) error = 'L\'ID utilisateur est obligatoire';
                break;

            case 'id_objectif':
                if (!value || value === "") error = 'Veuillez sélectionner un objectif';
                break;

            case 'date':
                if (!value) {
                    error = 'La date est obligatoire';
                } else {
                    const now = new Date();
                    now.setHours(0, 0, 0, 0);
                    const selectedDate = new Date(value);
                    if (selectedDate > now) {
                        error = 'La date ne peut pas être dans le futur';
                    }
                }
                break;

            case 'poids':
                const poids = parseFloat(value);
                if (!value) {
                    error = 'Le poids est obligatoire';
                } else if (isNaN(poids) || poids < 30 || poids > 300) {
                    error = 'Le poids doit être entre 30kg et 300kg';
                }
                break;

            case 'calories_consommees':
                const cal = parseInt(value);
                if (!value) {
                    error = 'Les calories sont obligatoires';
                } else if (isNaN(cal) || cal < 0) {
                    error = 'Les calories ne peuvent pas être négatives';
                }
                break;

            case 'eau_bue':
                const eau = parseFloat(value);
                if (!value) {
                    error = 'L\'eau bue est obligatoire';
                } else if (isNaN(eau) || eau < 0) {
                    error = 'La quantité d\'eau ne peut pas être négative';
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
            if (field.value.trim()) {
                field.classList.add('valid');
            } else {
                field.classList.remove('valid');
            }
        }
    }

    validateForm(e) {
        e.preventDefault();

        let isValid = true;
        const fieldsToValidate = ['user_id', 'id_objectif', 'date', 'poids', 'calories_consommees', 'eau_bue'];

        fieldsToValidate.forEach(name => {
            const field = this.form.querySelector(`[name="${name}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });

        if (isValid) {
            this.submitForm();
        } else {
            const firstError = this.form.querySelector('.error');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    showMessage(message, isError = false) {
        const container = document.getElementById('messageContainer');
        const text = document.getElementById('messageText');
        if (!container || !text) {
            alert(message);
            return;
        }

        text.textContent = message;
        container.style.display = 'flex';
        container.style.background = isError ? '#ffebee' : '#e8f5e9';
        container.style.color = isError ? '#c62828' : '#2e7d32';
        container.style.borderColor = isError ? '#f44336' : '#4CAF50';
        container.querySelector('i').className = isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';

        window.scrollTo({ top: 0, behavior: 'smooth' });

        setTimeout(() => {
            container.style.display = 'none';
        }, 5000);
    }

    submitForm() {
        const formData = new FormData(this.form);
        const isUpdate = !!formData.get('id_suivi');
        formData.append('action', isUpdate ? 'update' : 'create');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    this.showMessage(data.message, true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showMessage('Une erreur est survenue', true);
            });
    }
}

class SuiviManager {
    constructor() {
        this.validator = new SuiviFormValidator('addSuiviForm');
        this.initEventListeners();
    }

    initEventListeners() {
        document.querySelectorAll('.edit-suivi-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.editSuivi(e));
        });

        document.querySelectorAll('.delete-suivi-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.deleteSuivi(e));
        });

        const cancelBtn = document.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelEdit());
        }
    }

    editSuivi(e) {
        const id = e.target.closest('button').dataset.id;
        fetch(`${window.location.href}?ajax=details&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.populateForm(data);
                    document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
                }
            });
    }

    populateForm(data) {
        const form = document.getElementById('addSuiviForm');
        form.querySelector('[name="id_suivi"]').value = data.id;
        form.querySelector('[name="id_objectif"]').value = data.id_objectif;
        form.querySelector('[name="date"]').value = data.date;
        form.querySelector('[name="poids"]').value = data.poids;
        form.querySelector('[name="calories_consommees"]').value = data.calories_consommees;
        form.querySelector('[name="calories_objectif"]').value = data.calories_objectif;
        form.querySelector('[name="eau_bue"]').value = data.eau_bue;
        form.querySelector('[name="eau_objectif"]').value = data.eau_objectif;

        form.querySelector('.form-title').innerHTML = '<i class="fas fa-edit"></i> Modifier le suivi';
        form.querySelector('.btn-submit').innerHTML = '<i class="fas fa-save"></i> Mettre à jour';
        form.querySelector('.cancel-btn').style.display = 'block';
    }

    cancelEdit() {
        const form = document.getElementById('addSuiviForm');
        form.reset();

        // Ensure date resets to today
        const today = new Date().toISOString().split('T')[0];
        form.querySelector('[name="date"]').value = today;

        form.querySelector('[name="id_suivi"]').value = '';
        form.querySelector('.form-title').innerHTML = '<i class="fas fa-plus-circle"></i> Nouvelle entrée de suivi';
        form.querySelector('.btn-submit').innerHTML = '<i class="fas fa-save"></i> Enregistrer';
        form.querySelector('.cancel-btn').style.display = 'none';

        // Clear all validation states
        form.querySelectorAll('.error, .valid').forEach(el => el.classList.remove('error', 'valid'));
        form.querySelectorAll('.field-error').forEach(el => el.remove());
    }

    deleteSuivi(e) {
        const id = e.target.closest('button').dataset.id;
        if (confirm('Supprimer cette entrée ?')) {
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
                        this.validator.showMessage(data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.validator.showMessage(data.message, true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.validator.showMessage('Une erreur est survenue', true);
                });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SuiviManager();
});
