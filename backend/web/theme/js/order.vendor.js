function HandleOrder() {

    this.create = function (modalId, boxId, button, data) {
        let modal = new ModalRemote(modalId, boxId);
        modal.remote(button, data);
    }
}