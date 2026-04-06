import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['splitType', 'sharesSection', 'sharesCollection', 'amount', 'sumDisplay', 'sumStatus', 'beneficiaries'];

    connect() {
        this.nextIndex = parseInt(this.sharesCollectionTarget.dataset.index, 10) || 0;
        this.syncVisibility();
    }

    // -- Getters -----------------------------------------------------------------

    getSplitType() {
        return this.splitTypeTarget.value;
    }

    getTotalAmount() {
        return parseFloat(this.amountTarget?.value) || 0;
    }

    getShareRows() {
        return [...this.sharesCollectionTarget.querySelectorAll('.js-share-row')];
    }

    getCheckedBeneficiaries() {
        return [...this.beneficiariesTarget.querySelectorAll('input[type="checkbox"]:checked')]
            .map(cb => ({
                id: cb.value,
                label: cb.closest('.form-check')?.querySelector('label')?.textContent.trim() ?? cb.value,
            }));
    }

    getExistingRowIds() {
        return this.getShareRows().map(row => row.dataset.memberId);
    }

    // -- Row management ----------------------------------------------------------

    addShareRow(memberId, memberLabel) {
        const prototype = this.sharesCollectionTarget.dataset.prototype;
        if (!prototype) return;

        const html = prototype.replace(/__name__/g, this.nextIndex++);
        this.sharesCollectionTarget.dataset.index = String(this.nextIndex);

        const temp = document.createElement('div');
        temp.innerHTML = html;

        const row = document.createElement('div');
        row.className = 'js-share-row mb-2';
        row.dataset.memberId = String(memberId);

        const inner = document.createElement('div');
        inner.className = 'd-flex align-items-center gap-2';

        const nameSpan = document.createElement('span');
        nameSpan.className = 'fw-medium';
        nameSpan.style.minWidth = '100px';
        nameSpan.textContent = memberLabel;
        inner.appendChild(nameSpan);

        const memberSelect = temp.querySelector('select');
        if (memberSelect) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = memberSelect.name;
            hidden.value = memberId;
            inner.appendChild(hidden);
        }

        const shareInput = [...temp.querySelectorAll('input')]
            .find(inp => inp.name?.includes('[share]'));
        if (shareInput) {
            shareInput.className = 'form-control form-control-sm js-share-input';
            inner.appendChild(shareInput);
        }

        const unitSpan = document.createElement('span');
        unitSpan.className = 'js-share-unit text-muted';
        unitSpan.textContent = this.getSplitType() === 'percentage' ? '%' : '€';
        inner.appendChild(unitSpan);

        row.appendChild(inner);
        this.sharesCollectionTarget.appendChild(row);
    }

    removeShareRow(memberId) {
        this.getShareRows()
            .filter(row => row.dataset.memberId === String(memberId))
            .forEach(row => row.remove());
    }

    syncRowsWithBeneficiaries() {
        const checked = this.getCheckedBeneficiaries();
        const checkedIds = checked.map(b => b.id);
        const existingIds = this.getExistingRowIds();

        checked
            .filter(b => !existingIds.includes(b.id))
            .forEach(b => this.addShareRow(b.id, b.label));

        existingIds
            .filter(id => !checkedIds.includes(id))
            .forEach(id => this.removeShareRow(id));
    }

    // -- Computation -------------------------------------------------------------

    autoFill() {
        const rows = this.getShareRows();
        if (!rows.length) return;

        const splitType = this.getSplitType();
        if (splitType === 'equal') return;

        const total = splitType === 'percentage' ? 100 : this.getTotalAmount();
        const share = Math.round((total / rows.length) * 100) / 100;

        rows.forEach((row, i) => {
            const input = row.querySelector('.js-share-input');
            if (!input) return;
            input.value = i === rows.length - 1
                ? Math.round((total - share * (rows.length - 1)) * 100) / 100
                : share;
        });
    }

    updateUnits() {
        const unit = this.getSplitType() === 'percentage' ? '%' : '€';
        this.sharesCollectionTarget.querySelectorAll('.js-share-unit')
            .forEach(el => (el.textContent = unit));
    }

    setSumStatus(valid, diff, unit) {
        this.sumStatusTarget.className = `badge ${valid ? 'bg-success' : 'bg-danger'}`;
        if (valid) {
            this.sumStatusTarget.textContent = '✓';
            this.sumStatusTarget.title = '';
        } else {
            const sign = diff > 0 ? '+' : '';
            const label = diff > 0 ? 'excédent' : 'manquant';
            this.sumStatusTarget.textContent = `${sign}${diff.toFixed(2)} ${unit}`;
            this.sumStatusTarget.title = `${Math.abs(diff).toFixed(2)} ${unit} ${label}`;
        }
    }

    updateSum() {
        const splitType = this.getSplitType();
        if (splitType === 'equal') {
            this.sumDisplayTarget.textContent = '';
            this.sumStatusTarget.className = 'badge';
            this.sumStatusTarget.textContent = '';
            this.sumStatusTarget.title = '';
            return;
        }

        const sum = Math.round(
            this.getShareRows().reduce((acc, row) => {
                return acc + (parseFloat(row.querySelector('.js-share-input')?.value) || 0);
            }, 0) * 100
        ) / 100;

        if (splitType === 'percentage') {
            const diff = Math.round((sum - 100) * 100) / 100;
            this.sumDisplayTarget.textContent = `Total : ${sum.toFixed(2)} %`;
            this.setSumStatus(Math.abs(diff) <= 0.01, diff, '%');
        } else {
            const total = this.getTotalAmount();
            const diff = Math.round((sum - total) * 100) / 100;
            this.sumDisplayTarget.textContent = `Total : ${sum.toFixed(2)} €`;
            this.setSumStatus(Math.abs(diff) <= 0.01, diff, '€');
        }
    }

    syncVisibility() {
        if (this.getSplitType() === 'equal') {
            this.sharesSectionTarget.classList.add('d-none');
            return;
        }
        this.sharesSectionTarget.classList.remove('d-none');
        this.syncRowsWithBeneficiaries();
        this.autoFill();
        this.updateUnits();
        this.updateSum();
    }

    // -- Validation --------------------------------------------------------------

    validate() {
        const errors = [];
        const splitType = this.getSplitType();

        const amount = this.getTotalAmount();
        if (!amount || amount <= 0) {
            errors.push('Le montant doit être supérieur à 0.');
        }

        if (splitType !== 'equal') {
            const negativeShares = this.getShareRows().filter(row => {
                const val = parseFloat(row.querySelector('.js-share-input')?.value);
                return isNaN(val) || val < 0;
            });
            if (negativeShares.length > 0) {
                errors.push('Les parts ne peuvent pas être négatives.');
                negativeShares.forEach(row => {
                    row.querySelector('.js-share-input')?.classList.add('is-invalid');
                });
            }

            const sum = Math.round(
                this.getShareRows().reduce((acc, row) => {
                    return acc + (parseFloat(row.querySelector('.js-share-input')?.value) || 0);
                }, 0) * 100
            ) / 100;

            if (splitType === 'percentage' && Math.abs(sum - 100) > 0.01) {
                errors.push(`La somme des parts doit être égale à 100 % (actuellement ${sum.toFixed(2)} %).`);
            } else if (splitType === 'amount' && Math.abs(sum - amount) > 0.01) {
                errors.push(`La somme des parts doit être égale au montant total (${amount.toFixed(2)} €, actuellement ${sum.toFixed(2)} €).`);
            }
        }

        return errors;
    }

    showFormErrors(errors) {
        this.clearFormErrors();
        if (!errors.length) return;

        const container = document.createElement('div');
        container.className = 'alert alert-danger mt-2 js-form-errors';
        container.setAttribute('role', 'alert');

        const list = document.createElement('ul');
        list.className = 'mb-0 ps-3';
        errors.forEach(msg => {
            const li = document.createElement('li');
            li.textContent = msg;
            list.appendChild(li);
        });
        container.appendChild(list);

        this.element.insertBefore(container, this.element.querySelector('.centerBtn'));
    }

    clearFormErrors() {
        this.element.querySelectorAll('.js-form-errors').forEach(el => el.remove());
        this.getShareRows().forEach(row => {
            row.querySelector('.js-share-input')?.classList.remove('is-invalid');
        });
    }

    // -- Event handlers ----------------------------------------------------------

    onSubmit(event) {
        this.clearFormErrors();
        const errors = this.validate();
        if (errors.length) {
            event.preventDefault();
            this.showFormErrors(errors);
            this.element.querySelector('.js-form-errors')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    onSplitTypeChange() {
        this.syncVisibility();
    }

    onBeneficiaryChange({ target }) {
        if (target.type !== 'checkbox' || this.getSplitType() === 'equal') return;

        if (target.checked) {
            const label = target.closest('.form-check')?.querySelector('label')?.textContent.trim() ?? target.value;
            this.addShareRow(target.value, label);
        } else {
            this.removeShareRow(target.value);
        }
        this.autoFill();
        this.updateSum();
    }

    onAmountInput() {
        if (this.getSplitType() === 'amount') {
            this.autoFill();
            this.updateSum();
        }
    }

    onShareInput({ target }) {
        if (target.classList.contains('js-share-input')) this.updateSum();
    }
}

