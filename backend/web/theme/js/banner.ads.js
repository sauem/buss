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
    this.countRequest = async function (bannerKey, page = null, type = 'click') {
        return $.ajax({
            type: 'POST',
            data: {bannerKey, page, type},
            url: BASE_URL + '/api/default/counter',
            cache: false
        });
    }

    //counter banner shown
    this.countShown = async function (bannerId, page) {
        try {
            const res = await this.countRequest(bannerId, page, 'shown');
            console.log(res);
        } catch (e) {
            console.log(e);
            console.log(JSON.parse(e.responseText).message);
        }
    }
    //counter banner click
    this.countClick = async function (bannerId, page) {
        try {
            const res = await this.countRequest(bannerId, page, 'click');
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
        this.countShown(item.id, null);
        return iframe;
    }
    this.renderImage = function (item, getLink = false) {
        let {media, title, href, width, height} = item;

        let instance = this;
        let url = BASE_URL + media.media.url;
        let image = document.createElement('img');
        let link = document.createElement('a');
        image.setAttribute('src', url);
        image.setAttribute('class', 'img-fluid banner-ads');
        image.setAttribute('width', '100%');
        image.setAttribute('alt', title);
        if (height) {
            image.style.height = height + 'px';
        }
        if (width === -1) {
            image.style.width = '100%';
        }
        link.setAttribute('href', href ? href : '#');
        link.appendChild(image);
        this.countShown(item.id, null);
        link.addEventListener('click', function (evt) {
            evt.preventDefault();
            instance.countClick(item.id, null);
            return false;
        });
        if (getLink) {
            return url;
        }
        return link;
    }
    this.isEmpty = function (item) {
        if (typeof item !== "undefined" && item.length > 0) {
            return false;
        }
        return true;
    }
    this.setInnerPost = function (item) {
        let bellow_post = item.bellow_post && typeof item.below_post !== "undefined" ? parseInt(item.bellow_post) : 2;
        if (this.getUserAgent() === DEVICE_MOBILE) {
            let image = this.switchType(item, true);
            let parallax = document.createElement("div");
            let parallaxWrap = document.createElement("div");
            let contentParallaxWrap = document.createElement("div");
            let tag = document.createElement('a');
            contentParallaxWrap.setAttribute("class", "content-parallax-wrap");
            parallaxWrap.setAttribute("class", "parallax-wrap");


            tag.setAttribute("href", item.href);
            tag.setAttribute("target", "_blank");
            parallax.setAttribute("style", `background-image : url(${image})`);
            parallax.setAttribute("class", `parallax`);

            tag.appendChild(parallax);
            parallaxWrap.appendChild(tag);
            contentParallaxWrap.appendChild(parallaxWrap);

            $(".contain").find(`p:nth-child(${bellow_post})`).append(contentParallaxWrap);

        } else {
            $(".contain").find(`p:nth-child(${bellow_post})`).append(this.switchType(item));
        }
    }
    this.setItemSticky = function (item) {
        let sticky = document.createElement("div");
        sticky.appendChild(this.switchType(item));
        if (this.getUserAgent() !== DEVICE_MOBILE) {
            sticky.setAttribute("class", "sticky-banner");
            $("body").append(sticky);
        } else {
            let lux_event = $("body").find("section.lux_event");
            let position = Math.round(lux_event.length / 2) - 1;

            $(`section.lux_event:eq(${position})`).append(sticky);
        }

    }
    this.switchType = function (item, link = false) {
        switch (item.type) {
            case TYPE_VIDEO:
                return this.renderVideo(item);
            default:
                return this.renderImage(item, link)
        }
    }
    this.setPosition = function (data, element, prepend = true) {
        let group = this.groupBy(data, 'is_random');
        let _random = group[0];
        let _static = group[1];
        let item = null;
        if (typeof _static !== "undefined" && _static.length > 0) {
            item = _static[0];
        } else {
            if (typeof _random === "undefined") {
                return false;
            }
            item = this.getRandomObject(_random);
        }
        if (item.position === POSITION_CONTENT) {
            this.setInnerPost(item);
            return false;
        }
        if (getPage() === PAGE_ARCHIVE && item.position === POSITION_RIGHT) {
            this.setItemSticky(item);
            return false;
        }
        if (!prepend) {
            element.append(this.switchType(item));
        } else {
            element.prepend(this.switchType(item));
        }
    }
    //Generate and display banner to frontend view
    this.renderAds = function (page, data) {

        if (data.length <= 0) {
            console.warn('Không có quản cáo hiển thị!');
            return false;
        }
        let positions = this.groupBy(data, 'position');
        console.log(positions);
        const {top, bottom, right, left, content} = positions;
        if (!this.isEmpty(top)) {
            this.setPosition(top, $('#main'));
        }
        if (!this.isEmpty(right)) {
            this.setPosition(right, $('#sticky-sidebar'));
        }
        if (!this.isEmpty(content)) {
            this.setPosition(content, null);
        }
        if (!this.isEmpty(bottom)) {
            let _el = null,
                prep = true;
            switch (getPage()) {
                case PAGE_POST:
                    _el = $('.contain');
                    prep = false;
                    break;
                case PAGE_ARCHIVE:
                    _el = $('.lux_event').last();
                    break;
                default:
                    _el = $('.block_cate').last();
                    break;
            }
            this.setPosition(bottom, _el, prep);
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

function getPage() {
    let url = window.location.href;
    let page = PAGE_ARCHIVE;
    if (url == 'https://www.businessstyle.vn/' || url == 'https://businessstyle.vn') {
        page = PAGE_HOME;
    }
    if (url.includes('.html') || url.includes('.htm')) {
        page = PAGE_POST;
    }
    return page;
}

$(document).ready(function () {
    new initAds(getPage()).init();
});
