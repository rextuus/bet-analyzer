import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    static targets = ['modal'];
    static values = {
        url: String,
        isAjax: Boolean
    }
    modal = null;

    async openModal(event) {
        event.preventDefault()
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
    }

    submit(event) {
        if (this.isAjaxValue) {
            fetch(this.urlValue, {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
                .then(response => response.text())
                .then(json => {
                    window.location.reload();
                })
                .catch(error => {
                    console.log(error);
                });
        } else {
            window.location.href = this.urlValue;
        }
    }
}
