import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['widget']
    static values = {
        formTarget: String
    }

    initialize() {
        this.fieldRef = {};
        this.widgetTargets.forEach(widget => {
            if (this.fieldRef[widget.dataset.widgetFieldName] === undefined) {
                this.fieldRef[widget.dataset.widgetFieldName] = [];
            }
            const index = parseInt(widget.dataset.widgetIndex);
            this.fieldRef[widget.dataset.widgetFieldName][index] = widget;
        });
    }

    sync(event) {
        Object.entries(event.detail).forEach(([field, values]) => {
            for (let i = 0; i < values.length; i++) {
                this.fieldRef[field][i].value = values[i];
            }
        });
        const form = document.querySelector(this.formTargetValue);
        form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
};
