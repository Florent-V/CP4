import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'fileName', 'buttonText'];

    connect() {
        this.inputTarget.addEventListener('change', this.updateUI.bind(this));
    }

    disconnect() {
        this.inputTarget.removeEventListener('change', this.updateUI.bind(this));
    }

    updateUI() {
        if (this.inputTarget.files.length > 0) {
            this.fileNameTarget.textContent = this.inputTarget.files[0].name;
            this.buttonTextTarget.textContent = 'Changer';
        } else {
            this.fileNameTarget.textContent = '';
            this.buttonTextTarget.textContent = 'Choisir';
        }
    }

    open(event) {
        event.preventDefault();
        this.inputTarget.click();
    }
}