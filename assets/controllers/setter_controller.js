import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    copy(event) {
        this.element.value = event.detail[event.params.name];
    }
};
