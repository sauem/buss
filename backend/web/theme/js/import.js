const MODULE_CONTACT = 'contact';
const MODULE_CONTACT_LOG = 'contact-log';
const MODULE_PRODUCT = 'product';

async function handleReadExcel(evt) {
    this.file = $(evt)[0].files[0];
    this.importWrap = $('.import-wrap');
    this.importArea = $(this.importWrap).find('.import-area');
    this.importNote = $(this.importWrap).find('.note');
    this.fileSizeText = $(this.importWrap).find('.fileSizeText');
    this.totalRowsText = $(this.importWrap).find('.totalRowsText');
    this.module = $(evt).data('module');

    this.config = {
        maxRow: 50000,
        loadingContent: '<div class="loading spinner-border avatar-lg text-success" role="status"></div>',
        fileType: ["xlsx", "csv", "slx", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"],
    }
    if (!this.config.fileType.includes(this.file.type)) {
        toastr.warning("Định dạng file không đúng!");
        return false;
    }
    this.showLoading = function () {
        $(this.importArea).append(this.config.loadingContent);
    }
    this.hideLoading = function () {
        $(this.importArea).find('.loading').remove();
    }
    this.showNote = function () {
        $(this.importNote).show();
    }
    this.hideNote = function () {
        $(this.importNote).hide();
    }
    this.setFileInfo = function (totalRow, fileSize) {
        $(this.totalRowsText).text(totalRow);
        $(this.fileSizeText).text(bytesToSize(fileSize));
    }
    this.setFileName = function () {
        this.showNote();
        $(this.importNote).find('p').text(this.file.name);
    }
    // read file data
    this.hideNote();
    this.showLoading();
    try {
        const rows = await getFileRows.call(this);
        renderTemplate(this.module, rows);
        window.DATA = rows;
    } catch (e) {
        toastr.warning(e.message);
    } finally {
        this.hideLoading();
        this.setFileName();
    }
}

async function actionSave(module) {
    let url = AJAX_PATH[`${module}Import`];
    let data = window.DATA;
    let index = 0,
        errorRows = 0,
        errorMessages = [];
    if (!data && data.length <= 0) {
        toastr.warning('Không tìm thấy dữ liệu!');
        return false;
    }
    try {
        while (typeof data[index] !== "undefined") {
            const res = await doPushData(url, data[index]);
            index++;
            initProgressData(index, data.length, errorRows);
        }
    } catch (e) {
        errorRows++;
        throw new Error(e.message);
    }
}

function initProgressData(currentRow, totalRows, errorRows = 0) {
    this.resultImport = $('#result-import');
    let progressPercent = Math.round(currentRow / totalRows * 100);
    let successRows = totalRows - errorRows;
    let progressTemp = $('#progress-template').html();
    let temp = Handlebars.compile(progressTemp);
    $(this.resultImport).html(temp({
        totalRows,
        successRows,
        errorRows,
        progressPercent
    }));
    if (progressPercent >= 100) {
        toastr.success('Import hoàn thành!');
    }
}

async function doPushData(url, row) {
    return $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        data: {row: row},
    });
}

async function getFileRows() {
    return new Promise(resolve => {
        let reader = new FileReader();
        let instance = this;

        reader.readAsBinaryString(instance.file);
        reader.onerror = function (stuff) {
            toastr.warning(stuff.currentTarget.error.message);
        }
        reader.onload = function (evt) {
            let data = evt.target.result;
            let workbook = XLSX.read(data, {
                type: 'binary',
                cellDates: true
            });

            let firstSheet = workbook.SheetNames[0];
            let sheet = workbook.Sheets[firstSheet];
            let range = XLSX.utils.decode_range(sheet['!ref']);
            let highestRow = range.e.r - range.s.r - 1;
            instance.setFileInfo(highestRow, instance.file.size);
            resolve(processRow(sheet));
        }
        reader.onloadend = function (event) {
            instance.hideLoading();
            instance.setFileName();
        }
    });
}

function renderTemplate(module, rows) {
    let htmlTemplate = $(`#${module}-template`).html();
    let template = Handlebars.compile(htmlTemplate);
    $('#file-view-result').html(template(rows));
    initDataTable(module);
}

function initDataTable(module) {
    $(`#${module}-table`).DataTable({
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        }, drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });
}

function processRow(sheet) {
    let rowIndex = 2;
    let columnLength = 10;
    let rows = [];
    switch (module) {
        case MODULE_CONTACT:
            columnLength = 15;
            break;
    }
    let row = getRow(sheet, rowIndex, columnLength);
    while (row !== null) {
        let item = mappingModel(module, row);
        rows.push(item);
        rowIndex++;
        row = getRow(sheet, rowIndex, columnLength);
    }
    return rows;
}

const mappingModel = (module, row) => {
    let item = null;
    switch (module) {
        case MODULE_CONTACT:
            item = contactModel();
            item.register_time = row[0] ? getTimer(row[0].v) : null;
            item.code = row[1] ? row[1].v : null;
            item.name = row[2] ? row[2].v : null;
            item.phone = row[3] ? row[3].v : null;
            item.address = row[4] ? row[4].v : null;
            item.zipcode = row[5] ? row[5].v : null;
            item.option = row[6] ? row[6].v : null;
            item.note = row[7] ? row[7].v : null;
            item.partner = row[8] ? row[8].v : null;
            item.utm_source = row[9] ? row[9].v : null;
            item.utm_medium = row[10] ? row[10].v : null;
            item.utm_campaign = row[11] ? row[11].v : null;
            item.utm_term = row[12] ? row[12].v : null;
            item.utm_content = row[13] ? row[13].v : null;
            item.type = row[14] ? toUnicode(row[14].v).toLowerCase() : null;
            break;
    }
    return item;
};

function contactModel() {
    return {
        register_time: null,
        code: null,
        phone: null,
        name: null,
        address: null,
        zipcode: null,
        option: null,
        note: null,
        partner: null,
        utm_source: null,
        utm_medium: null,
        utm_content: null,
        utm_term: null,
        utm_campaign: null,
        type: null,
    }
}

const getMaterial = () => {
    return [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        , 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        , 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
    ];
};

function getRow(sheet, index, columnLength) {
    let alphabets = getMaterial();
    let row = [];
    for (let i = 0; i < columnLength; i++) {
        let col = alphabets[i];
        let cell = col + index;
        if (i === 0 && sheet[cell] === undefined) {
            return row = null;
        }
        if (sheet[cell] !== undefined) {
            row.push(sheet[cell]);
        } else {
            row.push(null);
        }
    }
    return row;
}

function bytesToSize(bytes) {
    let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Byte';
    let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function getTimer(time) {
    if (typeof time === 'string') {
        return moment(time, 'DD/MM/YYYY').unix();
    }
    if (typeof time === 'object') {
        return Math.round(time.getTime() / 1000);
    }
    return null;
}
