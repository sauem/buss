function removeImage(url) {


    let del = confirm('Xoá ảnh này?');
    if (del) {
        $.ajax({
            data: {url: url},
            cache: false,
            type: 'POST',
            url: AJAX_PATH.removeFile,
            success: function (res) {
                $('.upload-wraper').find('.image-view').empty();
                $('.upload-wraper').find('.note').show();
                $('.upload-wraper').find('.loading').hide();
                $('.upload-wraper').find('.input-model').val('');
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

}

function handleChange(evt) {

    this.config = {
        maxSize: 2000000,
        uploadURL: AJAX_PATH.uploadURL,
        type: ['image/jpeg', 'image/jpg', 'image/png'],
        loadingContent: '<div class="loading spinner-border text-secondary m-2" role="status"></div>'
    }
    this.file = $(evt)[0].files[0];
    this.size = $(evt).data('size');
    this.type = $(evt).data('type');
    this.config.size = this.size !== "undefined" | null || "" ? this.size : this.config.size;
    this.config.type = this.type !== "undefined" | null || "" ? this.type : this.config.type;

    this.uploadWrap = $('.upload-wraper');
    this.uploadPicker = $(this.uploadWrap).find('.file-upload');
    this.noteText = $(this.uploadWrap).find('.note');
    this.imageView = $(this.uploadWrap).find('.image-view');
    this.inputModel = $(this.uploadWrap).find('.input-model');
    this.clear = (function () {
        this.removeImage();
        this.hideLoading();
        $(this.inputModel).val('');
    });

    this.validInput = function () {
        if (!this.file) {
            this.clear();
        }
        if (this.file.size > this.config.size) {
            alert(`File upload phải nhỏ hơn ${this.config.size}`);
            this.clear();
            return false;
        }
        if (!this.config.type.includes(this.file.type)) {
            alert(`File upload không đúng định dạng!`);
            this.clear();
            return false;
        }
        return true;
    }

    this.hideNote = function () {
        $(this.noteText).hide();
    }
    this.showNote = function () {
        $(this.noteText).show();
    }
    this.setLoading = function () {
        $(this.uploadPicker).append(this.config.loadingContent);
    }
    this.hideLoading = function () {
        $(this.uploadPicker).find('.loading').remove();
    }
    this.setImage = function (url) {
        this.hideLoading();
        $(this.imageView).append(`<img src="${url}" class="img-fluid">`);
    }
    this.removeImage = function () {
        $(this.imageView).empty();
        $(this.imageView).find('img').remove();
        this.showNote();
    }

    function beforeUpload() {
        this.hideNote();
        this.setLoading();
        this.validInput();
    }

    this.setInputValue = function (val) {
        $(this.inputModel).val(val);
    }

    function uploadFileSuccess(response) {
        let instance = this;
        this.setImage(response.url);
        this.setInputValue(response.id);

        let button = document.createElement('button');
        button.innerHTML = '<i class="fe-trash"></i>';
        button.setAttribute('class', 'btn btn-xs');
        button.setAttribute('type', 'button');
        button.setAttribute('style', 'position:absolute;top:0;right:0;bottom:0;left:0;margin:auto;');
        button.addEventListener('click', function () {
            let del = confirm('Xoá ảnh này?');
            if (del) {
                $.ajax({
                    data: {url: response.url},
                    cache: false,
                    type: 'POST',
                    url: AJAX_PATH.removeFile,
                    success: function (res) {
                        instance.clear();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });
        $(this.imageView).append(button);
    }


    this.doUpload = function () {
        let instance = this;
        let formData = new FormData();
        formData.append('file', this.file);
        try {
            $.ajax({
                type: 'POST',
                url: this.config.uploadURL,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    return beforeUpload.call(instance);
                },
                success: function (response) {
                    uploadFileSuccess.call(instance, response);
                },
                error: function (response) {
                    uploadFileError.call(instance, response);
                }
            });
        } catch (e) {
            console.warn(e.responseText);
        }
    }
    this.clear();
    this.doUpload();
}