import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    open() {
        const modal = bootstrap.Modal.getInstance(this.element);
        if (modal !== null) {
            modal.show();
        }
    }

    close() {
        const modal = bootstrap.Modal.getInstance(this.element);
        if (modal !== null) {
            modal.hide();
        }
    }
};
