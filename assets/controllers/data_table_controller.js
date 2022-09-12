import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    select(event) {
        this.dispatch('select', {detail: event.params.values});
    }
};
