import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    static targets = ['rows', 'row']
    static values = {
        prototype: String,
        fieldInitializerName: {type: String, default: 'field-initializer'}
    }

    connect() {
        this.index = this.rowTargets.length;
    }

    addRow(event) {
        const template = document.createElement('template');
        const rowHtml = this.prototypeValue.replace(/__name__/g, this.index);
        template.innerHTML = rowHtml.trim();
        const row = template.content.firstChild;
        const items = row.querySelectorAll(`[data-${this.fieldInitializerNameValue}]`);
        for (const item of items) {
            putValueContent(item, getPropertyValue(event.detail, item.getAttribute(`data-${this.fieldInitializerNameValue}`)));
        }
        this.rowsTarget.appendChild(row);
        this.index++;
        this.dispatch('row-added');
    }

    removeRow(event) {
        this.rowTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove();
            }
        });
        this.dispatch('row-removed');
    }
};
