// assets/controllers/form-collection_controller.js

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect()
    {
        console.log('form-collection_controller')
        const addFormToCollection = (e) => {
            const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

            const item = document.createElement('li');

            item.innerHTML = collectionHolder
                .dataset
                .prototype
                .replace(
                    /__name__/g,
                    collectionHolder.dataset.index
                );

            collectionHolder.appendChild(item);

            collectionHolder.dataset.index++;
        };

        document
            .querySelectorAll('.add_item_link')
            .forEach(btn => {
                btn.addEventListener("click", addFormToCollection)
            });

    }
}