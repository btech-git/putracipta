import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['rows', 'row']
    static values = {
        prototype: String
    }

    connect() {
        this.index = this.rowTargets.length;
    }

    addItem(event) {
        const template = document.createElement('template');
        const rowHtml = this.prototypeValue.replace(/__name__/g, this.index);
        template.innerHTML = rowHtml.trim();
        this.rowsTarget.appendChild(template.content.firstChild);
        this.index++;
    }

    removeItem(event) {
        this.rowTargets.forEach(element => {
            if (element.contains(event.target)) {
                element.remove();
            }
        });
    }
};
