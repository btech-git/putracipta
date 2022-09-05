import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        name: String,
        url: String
    }

    connect() {
        const bodyElement = document.querySelector('body');
        bodyElement.addEventListener('submit', e => {
            const formElement = document.querySelector('form[name=' + this.nameValue + ']');
            if (formElement && formElement.contains(e.target)) {
                e.preventDefault();
                const formData = new FormData(formElement);
                fetch(this.urlValue, {method: 'POST', body: new URLSearchParams(formData)})
                        .then(response => response.json())
                        .then(json => {
                            this.element.innerHTML = json.html[this.nameValue];
                });
            }
        }, false);
    }
};
