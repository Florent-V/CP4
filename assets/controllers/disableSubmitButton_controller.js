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
    connect()
    {
        console.log('Hello from disableSubmitButton_controller.js');

        document.querySelector('form').addEventListener('submit', function (e) {
            document.querySelector('button[type="submit"]').disabled = true;
            document.querySelector('button[type="submit"]').innerHTML = 'Traitement en cours...';
        });
    }
}
