import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    freeze(event) {
    }

    makeChoice(event) {
        const values = Array.from(this.element.options).map(opt => opt.value);
        if (!values.includes(event.detail[event.params.valueName].toString())) {
            const optionElement = document.createElement('option');
            optionElement.value = event.detail[event.params.valueName];
            optionElement.innerText = event.detail[event.params.labelName];
            this.element.appendChild(optionElement);
        }
    }

    setValue(event) {
        this.element.value = event.detail[event.params.valueName];
    }
};
