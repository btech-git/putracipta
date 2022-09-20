import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['checkbox']

    checkAll(event) {
        for (checkbox of this.checkboxTargets) {
            checkbox.checked = true;
        }
    }

    uncheckAll(event) {
        for (checkbox of this.checkboxTargets) {
            checkbox.checked = false;
        }
    }

    checkElements(event) {
        for (checkbox of this.checkboxTargets) {
            if (event.detail.includes(checkbox.dataset.checkIndex)) {
                checkbox.checked = true;
            }
        }
    }

    uncheckElements(event) {
        for (checkbox of this.checkboxTargets) {
            if (event.detail.includes(checkbox.dataset.checkIndex)) {
                checkbox.checked = false;
            }
        }
    }
};
