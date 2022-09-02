import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['link']
    static values = {
        formId: String,
        fieldId: String
    }

    post(event) {
        event.preventDefault();
        const fieldElement = document.querySelector('#' + this.fieldIdValue);
        const formElement = document.querySelector('#' + this.formIdValue).closest('form');
        fieldElement.value = event.params.number;
        formElement.submit();
    }
};
