import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    select(event) {
        console.log(event.params);
        document.querySelector('#purchase_order_header_supplier').value = event.params.value;
        document.querySelector('#abc').innerText = event.params.text;
        bootstrap.Modal.getInstance(this.element.closest('[role=dialog]')).hide();
    }
};
