import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';

export default class extends Controller {
    static targets = ['modal', 'modalBody'];
    static values = {
        formUrl: String,
    }
    modal = null;

    async openModal(event) {
        event.preventDefault()
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        this.modal.show();

        this.loadFromUrl(this.formUrlValue)
    }

    async loadFromUrl(url) {
        fetch(url, {
            method: 'GET',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
            .then(response => response.text())
            .then(html => {
                this.modalBodyTarget.innerHTML = html;
            })
            .catch(error => {
                console.log(error);
            });
    }

    async submit(event) {
        event.preventDefault();
        const form = this.modalBodyTarget.getElementsByTagName('form')[0];

        const response = await fetch(form.action, {
            method: 'post',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: new FormData(form)
        });

        if (!response.ok) {
            this.modalBodyTarget.innerHTML = await response.text();
        } else {
            if (response.headers.has('x-redirect-uri')) {
                this.loadFromUrl(response.headers.get('x-redirect-uri'));
            } else {
                //close form modal
                this.modal.hide();
                window.location.reload();
            }
        }
    }
}
