import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    forward(event) {
        const attributeList = event.params.attributesList[event.type];
        const attributes = Array.isArray(attributeList) ? attributeList : [attributeList];
        for (const attribute of attributes) {
            const prefix = event.target.getAttribute(attribute);
            this.dispatch(event.type, {detail: event.detail, target: event.target, prefix, bubbles: event.bubbles, cancelable: event.cancelable});
        }
    }
};
