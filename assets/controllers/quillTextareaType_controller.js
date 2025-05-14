// File path in Symfony project : assets/controllers/forms/quill-textarea-type_controller.js

import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';

export default class extends Controller {
    static targets = ["editor", "input"];

    connect() {
        console.log('QuillTextareaTypeController connected');
        this.editor = new Quill(this.editorTarget, this.quillOption());
        console.log('this.inputTarget.value', this.inputTarget.value);

        let value = this.inputTarget.value;
        console.debug(value);

        this.editor.setContents(this.editor.clipboard.convert({
            html: value
        }));

        this.editor.on('text-change', this.onQuilTextChange.bind(this));
    }

    disconnect() {
        this.editor.off('text-change', this.onQuilTextChange.bind(this));
    }

    onQuilTextChange() {
        this.inputTarget.value = this.editor.root.innerHTML;
    }

    quillOption() {
        return {
            theme: 'snow',
            placeholder: 'Écris ici...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image']
                ]
            }
        };
    }
}
