import { Controller } from "@hotwired/stimulus";
import { Modal } from "bootstrap";

export default class extends Controller {
    static targets = ['modal'];
    static values = {
        formUrl: String,
    }
    modal = null;
    connect() {
        this.dispatch('openModal')
    }
    async openModal(event) {
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
    }
}