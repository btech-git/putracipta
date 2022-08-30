import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        url: String
    }

    select(event) {
        const prototypeElement = document.querySelector('#prototype');
        const index = prototypeElement.getAttribute('data-cs-index');
        const prototype = prototypeElement.getAttribute('data-cs-prototype').replace(/__name__/g, index);
        console.log(prototype);
        prototypeElement.innerHTML = prototype;
        document.querySelector('#purchase_order_header_purchaseOrderDetails_' + index +'_product').value = event.params.value;
        bootstrap.Modal.getInstance(this.element.closest('[role=dialog]')).hide();
        const formElement = document.querySelector('form[name=purchase_order_header]');
        const formData = new FormData(formElement);
        fetch(this.urlValue, {method: 'POST', body: formData})
                .then(response => response.json())
                .then(json => {
                    document.querySelector('#ajax').innerHTML = json.html.ajax;
        });
    }
};
