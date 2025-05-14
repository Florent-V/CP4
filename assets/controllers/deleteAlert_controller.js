import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        function alertCounter()
        {
            console.log('Hello from alertCounter_controller.js')
            const alerts = document.getElementsByClassName("alert");
            for (let alert of alerts) {
                // alert.textContent += " || suppression dans 5 secondes";
                setTimeout(function () {
                    alert.remove(); }, 5000)
            }
        }
        alertCounter();
    }
}
