import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur Stimulus pour la page de saisie du code de partage
 * Gère l'auto-focus, le formatage du code, la validation et la soumission automatique
 * @property {boolean} hasCodeInputTarget - Généré automatiquement par Stimulus
 * @property {HTMLInputElement} codeInputTarget - Généré automatiquement par Stimulus
 * @property {boolean} hasFormTarget - Généré automatiquement par Stimulus
 * @property {HTMLFormElement} formTarget - Généré automatiquement par Stimulus
 */
export default class extends Controller {
    static targets = ["codeInput", "form"];

    connect()
    {
        console.log('Share code controller connected');
        // Auto-focus sur le champ de code
        if (this.hasCodeInputTarget) {
            this.codeInputTarget.focus();
        }

        // Validation côté client pour les formulaires avec needs-validation
        this.setupFormValidation();
    }

    // Formatage automatique du code pendant la saisie
    formatCode(event)
    {
        let value = event.target.value.replace(/\D/g, '').substring(0, 6);
        event.target.value = value;
    }

    // Soumission automatique quand 6 chiffres sont saisis
    checkAutoSubmit(event)
    {
        if (event.target.value.length === 6 && /^\d{6}$/.test(event.target.value)) {
            // Petite pause pour une meilleure UX
            setTimeout(() => {
                if (this.hasFormTarget) {
                    this.formTarget.submit();
                }
            }, 500);
        }
    }

    // Configuration de la validation côté client
    setupFormValidation()
    {
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.forEach.call(forms, (form) => {
            form.addEventListener('submit', (event) => {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }
}
