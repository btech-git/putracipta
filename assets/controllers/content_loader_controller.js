import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String,
        method: {type: String, default: 'GET'}
    }

    connect() {
        this.fetchContent(this.urlValue, {method: this.methodValue});
    }

    load(event) {
        const formElement = document.querySelector(event.params.formTarget);
        const formData = new FormData(formElement);
        if (this.methodValue === 'GET' || this.methodValue === 'get') {
            this.fetchContent(this.urlValue + '?' + new URLSearchParams(formData).toString(), {method: this.methodValue});
        } else if (this.methodValue === 'POST' || this.methodValue === 'post') {
            this.fetchContent(this.urlValue, {method: this.methodValue, body: formData});
        }
    }

    fetchContent(url, options) {
        fetch(url, options)
                .then(response => response.text())
                .then(html => this.element.innerHTML = html);
    }
};
