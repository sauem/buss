window.PHONES = [];


$(document).on('shown.bs.modal', '.modal', function () {
    $('.selectpicker').selectpicker({
        showIcon: true
    });
    initMaskMoney();
})

$(document).on('hide.bs.modal', '.modal', function () {
    window.ORDER_ITEMS = [];
})

function initMaskMoney() {
    $(document).ready(function () {
        $('[data-toggle="input-mask"]').each(function (a, e) {
            let t = $(e).data("maskFormat"), n = $(e).data("reverse");
            null != n ? $(e).mask(t, {reverse: n}) : $(e).mask(t)
        })
    }), jQuery(function (a) {
        a(".autonumber").autoNumeric("init")
    });
}

function initPicker() {
    $('.selectpicker').selectpicker();
}

function initMoney(number, options) {
    let dl = options.hash['decimalLength'] || 0;
    let ts = options.hash['thousandsSep'] || ',';
    let ds = options.hash['decimalSep'] || '.';

    // Parse to float
    let value = parseFloat(value);

    // The regex
    let re = '\\d(?=(\\d{3})+' + (dl > 0 ? '\\D' : '$') + ')';

    // Formats the number with the decimals
    let num = value.toFixed(Math.max(0, ~~dl));

    // Returns the formatted number
    return (ds ? num.replace('.', ds) : num).replace(new RegExp(re, 'g'), '$&' + ts);
}

function approvePhone() {
    this.phones = $('#w1.grid-view').find('input[type="checkbox"]:checked');
    this.modal = $('#approve-modal');
    this.form = $(this.modal).find('form#approveForm');
    this.phoneResult = $(this.modal).find('#phones-result');
    this.jsHTML = $('#phones-template').html();
    this.temp = Handlebars.compile(this.jsHTML);
    if (this.phones.length <= 0) {
        toastr.warning('Hãy chọn 1 số điện thoại!');
        return false;
    }

    this.hideModal = function () {
        $(this.modal).modal('hide');
    }
    this.showModal = function () {
        $(this.modal).modal({backdrop: 'static'});
    }
    this.render = function () {
        $(this.phoneResult).html(this.temp(PHONES));
    }
    this.setData = function () {
        this.phones.each(function (index) {
            let phone = $(this).data('phone'),
                country = $(this).data('country');
            if (typeof phone === "undefined") {
                return true;
            }
            PHONES.push({
                phone: phone,
                country: country
            });
        });
    }
    this.approve = function () {
        this.setData();
        this.showModal();
        this.render();
    }
    this.removePhone = function (phone) {
        let cof = confirm("Loại bỏ số điện thoại này?");
        let res = null;
        if (cof) {
            res = PHONES.filter(item => item.phone !== phone);
            $(this).parent().remove();
            window.PHONES = res;
        }
        if (PHONES.length <= 0) {
            this.hideModal();
        }
        this.render();
    }
    this.service = async function (saleID) {
        return $.ajax({
            type: 'POST',
            url: AJAX_PATH.assignPhone,
            data: {phones: window.PHONES, saleID: saleID},
            cache: false,
        });
    }
    this.requestServer = async function () {
        let saleID = $(this.form).find('select[name="user_id"]').val();
        try {
            const r = await this.service(saleID);
            console.log(r);
        } catch (e) {
            console.log(e);
        }
    }
}
