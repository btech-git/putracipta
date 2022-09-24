import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    static targets = ['items', 'item']
    static values = {
        startIndex: Number,
        prototype: String,
        itemFieldNameAttributeName: {type: String, default: 'data-item-field-name'},
        itemIdentifierNameAttributeName: {type: String, default: 'data-item-identifier-name'},
        itemIdentifierValueAttributeName: {type: String, default: 'data-item-identifier-value'}
    }

    connect() {
        this.index = this.startIndexValue;
    }

    addCollectionItem(event) {
        for (const itemTarget of this.itemTargets) {
            const identifierName = itemTarget.getAttribute(this.itemIdentifierNameAttributeNameValue);
            const identifierValue = itemTarget.getAttribute(this.itemIdentifierValueAttributeNameValue);
            if (identifierName !== null && identifierValue !== null && identifierValue === event.detail[identifierName].toString()) {
                return;
            }
        }
        const template = document.createElement('template');
        const rowHtml = this.prototypeValue.replace(/__name__/g, this.index);
        template.innerHTML = rowHtml.trim();
        const row = template.content.firstChild;
        if (row.getAttribute(this.itemIdentifierNameAttributeNameValue) !== null) {
            row.setAttribute(this.itemIdentifierValueAttributeNameValue, event.detail[row.getAttribute(this.itemIdentifierNameAttributeNameValue)]);
        }
        const items = row.querySelectorAll(`[${this.itemFieldNameAttributeNameValue}]`);
        for (const item of items) {
            putValueContent(item, getPropertyValue(event.detail, item.getAttribute(`${this.itemFieldNameAttributeNameValue}`)));
        }
        this.itemsTarget.appendChild(row);
        this.index++;
        this.dispatch('collection-item-added');
    }

    removeCollectionItem(event) {
        this.itemTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove();
            }
        });
        this.dispatch('collection-item-removed');
    }
};
