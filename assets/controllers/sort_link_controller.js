import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        panelTarget: String
    }

    post(event) {
        const valueIndex = event.params.valueList.indexOf(event.currentTarget.dataset.sortOrder);
        const panel = document.querySelector(this.panelTargetValue);
        const fieldName = event.params.fieldName;
        const resetFieldNames = event.params.resetFieldNames;
        const valueList = event.params.valueList;
        const nextIndex = (valueIndex + 1) % valueList.length;
        const values = [valueList[nextIndex]];
        event.currentTarget.dataset.sortOrder = valueList[nextIndex];
        const detail = {};
        for (const resetFieldName of resetFieldNames) {
            detail[resetFieldName] = [''];
        }
        detail[fieldName] = values;
        this.dispatch('sync', {prefix: 'sort-panel', target: panel, detail});
    }
};
