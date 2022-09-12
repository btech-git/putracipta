import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    confirm(event) {
        if (!window.confirm(event.params.message)) {
            event.preventDefault();
        }
    }
};
