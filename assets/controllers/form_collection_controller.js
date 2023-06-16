import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    static targets = ['items', 'item']
    static values = {
        startIndex: Number,
        prototype: String,
        itemFieldNameAttributeName: {type: String, default: 'data-item-field-name'},
        itemFieldActionAttributeName: {type: String, default: 'data-item-field-action'},
        itemIdentifierNameAttributeName: {type: String, default: 'data-item-identifier-name'},
        itemIdentifierValueAttributeName: {type: String, default: 'data-item-identifier-value'}
    }

    connect() {
        this.index = this.startIndexValue;
    }

    addCollectionItem(event) {
        if (event.params.addItemMatchingFieldPath === undefined ||
            event.params.addItemMatchingFieldPathValue === undefined ||
            getPropertyValue(event.detail, event.params.addItemMatchingFieldPath) === event.params.addItemMatchingFieldPathValue) {
            this.addItem(event.detail);
        }
    }

    addCollectionItems(event) {
        if (event.params.addItemsMatchingFieldPath === undefined ||
            event.params.addItemsMatchingFieldPathValue === undefined ||
            getPropertyValue(event.detail, event.params.addItemsMatchingFieldPath) === event.params.addItemsMatchingFieldPathValue) {
            for (const detailItem of event.detail) {
                this.addItem(detailItem);
            }
        }
    }

    removeCollectionItem(event) {
        if (event.params.removeItemMatchingFieldPath === undefined ||
            event.params.removeItemMatchingFieldPathValue === undefined ||
            getPropertyValue(event.detail, event.params.removeItemMatchingFieldPath) === event.params.removeItemMatchingFieldPathValue) {
            this.itemTargets.forEach(element => {
                if (element.contains(event.target)) {
                    element.remove();
                }
            });
            this.dispatch('collection-item-removed');
        }
    }

    clearCollectionItems(event) {
        if (event.params.clearItemsMatchingFieldPath === undefined ||
            event.params.clearItemsMatchingFieldPathValue === undefined ||
            getPropertyValue(event.detail, event.params.clearItemsMatchingFieldPath) === event.params.clearItemsMatchingFieldPathValue) {
            this.itemsTarget.replaceChildren();
        }
    }

    addItem(data) {
        for (const itemTarget of this.itemTargets) {
            const identifierName = itemTarget.getAttribute(this.itemIdentifierNameAttributeNameValue);
            const identifierValue = itemTarget.getAttribute(this.itemIdentifierValueAttributeNameValue);
            if (identifierName !== null && identifierValue !== null && identifierValue === data[identifierName].toString()) {
                return;
            }
        }
        const template = document.createElement('template');
        const rowHtml = this.prototypeValue.replace(/__name__/g, this.index);
        template.innerHTML = rowHtml.trim();
        const row = template.content.firstChild;
        if (row.getAttribute(this.itemIdentifierNameAttributeNameValue) !== null) {
            row.setAttribute(this.itemIdentifierValueAttributeNameValue, data[row.getAttribute(this.itemIdentifierNameAttributeNameValue)]);
        }
        const items = row.querySelectorAll(`[${this.itemFieldNameAttributeNameValue}]`);
        for (const item of items) {
            putValueContent(item, getPropertyValue(data, item.getAttribute(this.itemFieldNameAttributeNameValue)));
        }
        const elements = row.querySelectorAll(`[${this.itemFieldActionAttributeNameValue}]`);
        const $row = row;
        for (const element of elements) {
            const $element = element;
            eval(element.getAttribute(this.itemFieldActionAttributeNameValue));
        }
        this.itemsTarget.appendChild(row);
        this.index++;
        this.dispatch('collection-item-added');
    }
};
