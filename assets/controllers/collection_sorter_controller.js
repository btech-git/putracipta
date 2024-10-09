import { Controller } from '@hotwired/stimulus';
import { putValueContent, getPropertyValue } from '../helpers';

export default class extends Controller {
    static targets = ['ordinal']

    moveUp(event) {
        const currentElement = event.target.closest('[data-item-sortable]');
        if (currentElement.previousElementSibling) {
            const parentElement = currentElement.parentNode;
            parentElement.insertBefore(currentElement, currentElement.previousElementSibling);
            this.reorderElements();
        }
    }

    moveDown(event) {
        const currentElement = event.target.closest('[data-item-sortable]');
        if (currentElement.nextElementSibling) {
            const parentElement = currentElement.parentNode;
            parentElement.insertBefore(currentElement.nextElementSibling, currentElement);
            this.reorderElements();
        }
    }

    reorder(event) {
        this.reorderElements();
    }

    reorderElements() {
        let i = 0;
        for (const ordinalElement of this.ordinalTargets) {
            const itemElement = ordinalElement.closest('[data-item-sortable]');
            if (getComputedStyle(itemElement).display !== 'none') {
                i++;
            }
            putValueContent(ordinalElement, i);
        }
    }
};
