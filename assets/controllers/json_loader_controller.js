import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    static targets = ['placeholder'];
    static values = {
        url: String,
        method: {type: String, default: 'GET'},
        formTarget: {type: String, default: 'form'},
        autoLoad: Boolean
    }

    connect() {
        if (this.autoLoadValue) {
            this.loadContent(this.urlValue, this.methodValue, this.formTargetValue);
        }
    }

    load(event) {
        const url = event.params.url !== undefined ? event.params.url : this.urlValue;
        const method = event.params.method !== undefined ? event.params.method : this.methodValue;
        const formTarget = event.params.formTarget !== undefined ? event.params.formTarget : this.formTargetValue;
        this.loadContent(url, method, formTarget);
    }

    loadContent(url, method, formTarget) {
        const formElement = document.querySelector(formTarget);
        if (formElement !== null) {
            const formData = new FormData(formElement);
            if (method === 'GET' || method === 'get') {
                this.fetchContent(url + '?' + new URLSearchParams(formData).toString(), {method});
            } else if (method === 'POST' || method === 'post') {
                this.fetchContent(url, {method, body: formData});
            }
        } else {
            this.fetchContent(url, {method});
        }
    }

    fetchContent(url, options) {
        fetch(url, options)
                .then(response => response.json())
                .then(json => {
                    this.dispatch('complete', {detail: json});
                    for (const placeholder of this.placeholderTargets) {
                        putValueContent(placeholder, getPropertyValue(json, placeholder.dataset.propertyPath));
                    }
                });
    }
};
