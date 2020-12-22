const PAGE_HOME = 'home';
const PAGE_ARCHIVE = 'archive';
const PAGE_POST = 'post';
const POSITION_TOP = 'top';
const POSITION_BOTTOM = 'bottom';
const POSITION_LEFT = 'left';
const POSITION_RIGHT = 'right';
const POSITION_CONTENT = 'content';
const TYPE_IMAGE = "1";
const TYPE_VIDEO = "2";
const STYLE_RANDOM = "1";
const STYLE_STATIC = "0";
const DEVICE_MOBILE = 'mobile';
const DEVICE_DESKTOP = 'desktop';
const API_URL = 'https://ads.businessstyle.vn/api/default/get-banner';
const BASE_URL = 'https://ads.businessstyle.vn';


function initAds(page = 'home') {
    let header = document.getElementsByTagName('header')[0];
    let block_cate = document.getElementsByTagName('block_cate')[0];
    // detected user device
    this.getUserAgent = function () {
        const ua = navigator.userAgent;
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
            return "tablet";
        }
        if (
            /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(
                ua
            )
        ) {
            return "mobile";
        }
        return "desktop";
    }
    // api request count
    this.countRequest = async function (bannerKey, page, type = 'click') {
        return $.ajax({
            type: 'POST',
            data: {bannerKey, page, type},
            url: BASE_URL + '/ajax/counter',
            cache: false
        });
    }

    //counter banner shown
    this.countShown = async function (bannerId, page) {
        try {
            const res = await this.countRequest(bannerId, page, 'shown');
        } catch (e) {
            console.log(e);
            console.log(JSON.parse(e.responseText).message);
        }
    }
    //counter banner click
    this.countClick = async function (bannerId, page) {
        try {
            const res = await this.countRequest(bannerId, page, 'shown');
            if (res.success) {
                window.location.href = res.redirect;
            }
        } catch (e) {
            console.log(e);
            console.log(JSON.parse(e.responseText).message);
        }
    }
    // get list banner available
    this.getBanner = async () => {
        const instance = this;
        let url = new URL(API_URL);
        url.search = new URLSearchParams({
            device: instance.getUserAgent(),
            page: page
        });
        return $.ajax({
            type: 'GET',
            url: url,
            cache: false,
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Origin': '*'
            },
        });
    }
    // get random item from array image random
    this.getRandomObject = (object) => {
        let rand = Math.floor(Math.random() * object.length);
        return object[rand];
    }
    // gorup array by key
    this.groupBy = (items, key) => items.reduce(
        (result, item) => ({
            ...result,
            [item[key]]: [
                ...(result[item[key]] || []),
                item,
            ],
        }), {});
    this.renderVideo = function (item) {
        let {youtube_url, width, height} = item;
        let url = `https://www.youtube.com/embed/${youtube_url}?control=0`;
        let iframe = document.createElement('iframe');
        iframe.setAttribute('src', url);
        iframe.setAttribute('with', width ? width : '100%');
        iframe.setAttribute('height', height ? height : '315');
        iframe.setAttribute('frameborder', '0');
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
        iframe.setAttribute('allowfullscreen', '1');
        return iframe;
    }
    this.renderImage = function (item) {
        let {media, title, href, width, height} = item;
        let url = BASE_URL + media.media.url;
        let image = document.createElement('img');
        let link = document.createElement('a');
        image.setAttribute('src', url);
        image.setAttribute('class', 'img-fluid');
        image.setAttribute('alt', title);
        if (height) {
            image.style.height = height + 'px';
        }
        if (width === -1) {
            image.style.width = '100%';
        }
        link.setAttribute('href', href ? href : '#');
        link.appendChild(image);
        return link;
    }
    //Generate and display banner to frontend view
    this.renderAds = function (page, data) {

        if (data.length <= 0) {
            console.warn('Không có quản cáo hiển thị!');
            return false;
        }
        let positions = this.groupBy(data, 'position');
        const {top, bottom, right, left, content} = positions;
        if (typeof top !== "undefined" && top.length > 0) {
            let group = this.groupBy(top, 'is_random');
            let _random = group[0];
            let _static = group[1];
            if (typeof _static !== "undefined" && _static.length > 0) {
                header.insertBefore(this.renderImage(_static[0]), null);
            } else {
                if (typeof _random === "undefined") {
                    return false;
                }
                let item = this.getRandomObject(_random);
                $("header").prepend(this.renderImage(item));
            }
        }
    }
    this.init = async function () {
        try {
            const res = await this.getBanner();
            this.renderAds(page, res);
        } catch (e) {
            console.log('error ads:', e);
        }
    }

}

new initAds('home').init();
