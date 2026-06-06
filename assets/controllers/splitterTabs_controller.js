import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['addButton'];
    static values = {
        initialized: { type: Boolean, default: false }
    }

    connect() {
        document.addEventListener('turbo:load', () => {
            if (!this.initializedValue) {
                this.initializedValue = true;
                this.initializeTabs();
            }
        });
    }

    initializeTabs() {
        const tabs = document.querySelectorAll('.tab');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');

                tabs.forEach(t => t.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tabId).classList.add('active');

                this.#toggleAddButton(tabId);
            });
        });
    }

    #toggleAddButton(activeTabId) {
        if (!this.hasAddButtonTarget) return;
        this.addButtonTarget.hidden = activeTabId !== 'depenses';
    }
}
