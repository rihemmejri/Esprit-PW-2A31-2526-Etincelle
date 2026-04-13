// event.js - Contrôle de saisie pour les formulaires d'événements

class EventFormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.init();
    }

    init() {
        if (!this.form) return;

        // submit
        this.form.addEventListener('submit', (e) => this.validateForm(e));

        // realtime validation
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

            case 'titre':
                if (!value) {
                    error = "Le titre est obligatoire";
                } else if (value.length < 3) {
                    error = "Minimum 3 caractères";
                } else if (value.length > 100) {
                    error = "Maximum 100 caractères";
                }
                break;

            case 'description':
                if (!value) {
                    error = "Description obligatoire";
                } else if (value.length < 10) {
                    error = "Minimum 10 caractères";
                }
                break;

            case 'lieu':
                if (!value) {
                    error = "Lieu obligatoire";
                } else if (value.length < 3) {
                    error = "Lieu trop court";
                }
                break;

            case 'nb_places_max':
                const nb = parseInt(value);
                if (isNaN(nb) || nb <= 0) {
                    error = "Nombre invalide";
                } else if (nb > 1000) {
                    error = "Max 1000 places";
                }
                break;

            case 'date_evenement':
                const today = new Date().toISOString().split("T")[0];
                if (!value) {
                    error = "Date obligatoire";
                } else if (value < today) {
                    error = "Date invalide (passé)";
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

        const fields = ['titre', 'description', 'lieu', 'nb_places_max', 'date_evenement'];

        fields.forEach(name => {
            const field = this.form.querySelector(`[name="${name}"]`);
            if (field && !this.validateField(field)) {
                isValid = false;
            }
        });

        if (isValid) {
            this.showSuccess();
            this.form.submit();
        } else {
            this.showErrorGlobal();
            const firstError = this.form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    }

    showSuccess() {
        this.removeMessage();

        const div = document.createElement('div');
        div.className = 'success-message';
        div.id = 'tempMessage';
        div.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Event ajouté avec succès !
        `;

        document.querySelector('.header').after(div);

        setTimeout(() => div.remove(), 3000);
    }

    showErrorGlobal() {
        this.removeMessage();

        const div = document.createElement('div');
        div.className = 'error-message';
        div.id = 'tempMessage';
        div.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            Vérifiez les champs !
        `;

        document.querySelector('.header').after(div);

        setTimeout(() => div.remove(), 4000);
    }

    removeMessage() {
        const msg = document.getElementById('tempMessage');
        if (msg) msg.remove();
    }
}

// INIT
document.addEventListener('DOMContentLoaded', function() {
    new EventFormValidator('eventForm');

    // animation focus
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        field.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});