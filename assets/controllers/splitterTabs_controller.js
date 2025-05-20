import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        initialized: { type: Boolean, default: false }
    }
    connect()
    {
        //this.initializeTabs();
        document.addEventListener('turbo:load', () => {
            if (!this.initializedValue) {
                this.initializedValue = true;
                this.initializeTabs();
            }
        });
    }
    initializeTabs()
    {
        const tabs = document.querySelectorAll('.tab');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const tabId = this.getAttribute('data-tab');

                // Remove active class from all tabs and tab panes
                tabs.forEach(t => t.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Add active class to the clicked tab and corresponding tab pane
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    }
}
