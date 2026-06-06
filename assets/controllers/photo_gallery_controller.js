import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { pageSize: { type: Number, default: 12 } };
    static targets = ['item', 'prev', 'next', 'info'];

    #page = 1;

    connect() {
        this.#render();
    }

    prev() {
        if (this.#page > 1) {
            this.#page--;
            this.#render();
        }
    }

    next() {
        if (this.#page < this.#totalPages()) {
            this.#page++;
            this.#render();
        }
    }

    #totalPages() {
        return Math.ceil(this.itemTargets.length / this.pageSizeValue);
    }

    #render() {
        const start = (this.#page - 1) * this.pageSizeValue;
        const end = start + this.pageSizeValue;

        this.itemTargets.forEach((item, i) => {
            item.hidden = i < start || i >= end;
        });

        if (this.hasPrevTarget) this.prevTarget.disabled = this.#page === 1;
        if (this.hasNextTarget) this.nextTarget.disabled = this.#page === this.#totalPages();
        if (this.hasInfoTarget) {
            this.infoTarget.textContent = `Page ${this.#page} / ${this.#totalPages()}`;
        }
    }
}
