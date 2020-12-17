//window.ORDER_ITEMS = [];
Number.prototype.format = function (n, x) {
    let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
}

function orderSaleForm() {
    let html = $('#sku-template').html();
    let template = Handlebars.compile(html);
    let orderItemsResult = $('#result-sku-list');
    let totalShip = $('#total-ship-text');
    let totalBill = $('#total-bill-text');
    let totalBillInput = $('input[name="total_bill"]');
    let totalPriceInput = $('#total-price-input');
    let productPriceInput = $('input.input-product-price');
    let productQtyInput = $('input.input-product-qty');

    let ORDER_ITEMS = {
        total: {
            total_bill: 0,
            total_price: 0,
            total_ship: 0
        },
        items: []
    };

    this.getProductInfo = async function (sku) {
        return $.ajax({
            url: AJAX_PATH.getProductInfo,
            data: {sku: sku},
            cache: false,
            type: 'POST'
        })
    }
    this.appendTemplateItem = function (item) {
        orderItemsResult.append(template(item));
        initMaskMoney();
    }
    this.removeItemTemp = function (element) {
        $(element).closest('.order-item').remove();
    }
    this.setOrderItems = function (item) {
        const {sku, id} = item;
        if (ORDER_ITEMS.items.some(value => value.sku !== sku)) {
            toastr.warning('Sản phẩm đã tồn tại');
            return false;
        }
        ORDER_ITEMS.items.push({
            sku: sku,
            price: 0,
            qty: 0
        });
        console.log('SET ITEM:', ORDER_ITEMS);
        return true;
    }
    this.removeOrderItems = function (key) {
        if (!ORDER_ITEMS.items.some(value => value.sku === key)) {
            toastr.warning('Sản phẩm không tồn tại!');
            return false;
        }
        let items = ORDER_ITEMS.items;
        ORDER_ITEMS.items = items.filter(id => key !== id);
        return true;
    }
    this.addProductItem = async function (id) {
        try {
            const res = await this.getProductInfo(id);
            if (this.setOrderItems(res)) {
                this.appendTemplateItem(res);
            }
        } catch (e) {
            toastr.warning(e.message);
        }
    }
    this.setShippingCost = function (number) {
        totalShip.text(number);
        ORDER_ITEMS.total.total_ship = parseFloat(number.replaceAll(',', ''));

        this.sumTotal();
    }
    this.sumTotal = function (overwrite = false) {
        let total = 0, price = 0;

        ORDER_ITEMS.items.map(item => {
            price += parseFloat(item.price);
        });
        if (!overwrite)
            ORDER_ITEMS.total.total_price = parseFloat(price);

        total = ORDER_ITEMS.total.total_price + ORDER_ITEMS.total.total_ship;
        ORDER_ITEMS.total.total_bill = total;

        totalPriceInput.val(overwrite ? ORDER_ITEMS.total.total_price : price.format());

        totalBill.text(total.format());
    }
    this.setTotalPrice = function (value) {
        value = parseFloat(value.replaceAll(',', ''));
        ORDER_ITEMS.total.total_price = value;
        this.sumTotal(true);
    }
    this.setItemPrice = function (sku, price) {
        let index = ORDER_ITEMS.items.findIndex(item => item.sku === sku);
        ORDER_ITEMS.items[index].price = price;
        this.sumTotal();
    };
    this.removeProductItem = function (sku, element) {
        if (this.removeOrderItems(sku)) {
            this.removeItemTemp(element);
        }
    }
}

async function changeStatus(model, status, element = null) {
    let ids = $('.grid-view').yiiGridView("getSelectedRows");
    if (element) {
        ids = $(element).data('key');
    }
    await swal.fire({
        text: `Đổi trạng thái lựa chọn sang ${status}`,
        type: 'info',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Đồng ý',
    }).then(res => {
        if (res.value) {
            try {
                const res = service(model, status, ids);
                $.pjax.reload('div[id*="-box"]', {});
            } catch (e) {
                toastr.warning(e.message);
            }
        }
    })

    async function service(model, status, ids) {
        return $.ajax({
            url: AJAX_PATH.changeStatus,
            data: {model, status, ids},
            type: 'POST',
            cache: false
        });
    }


}