import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    doNothing(event) {
    }

    appendHtml(event) {
        const doc = new DOMParser().parseFromString(event.params.appendHtmlTemplate, "text/html");
        const html = this.getNormalizedTemplate(doc.documentElement.textContent, event.detail);
        if (!this.element.innerHTML.includes(html)) {
            this.element.insertAdjacentHTML('beforeend', html);
        }
    }

    putContent(event) {
        const content = this.getNormalizedTemplate(event.params.putContentTemplate, event.detail);
        putValueContent(this.element, content);
    }

    getNormalizedTemplate(template, values) {
        return template.replace(/(?<=^|[^$])\$\{([^}]+)\}/g, function(match, p1) {
            return getPropertyValue(values, p1);
        });
    }
};
