const PAGE_HOME = 'home';
const PAGE_ARCHIVE = 'archive';
const PAGE_POST = 'post';
const POSITION_TOP = 'top';
const POSITION_BOTTOM = 'bottom';
const POSITION_LEFT = 'left';
const POSITION_RIGHT = 'right';
const POSITION_CONTENT = 'content';
const TYPE_IMAGE = 'image';
const TYPE_VIDEO = 'video';
const STYLE_RANDOM = 'random';
const STYLE_STATIC = 'static';
const DEVICE_MOBILE = 'mobile';
const DEVICE_DESKTOP = 'desktop';
const API_URL = 'http://buss.local/ajax/get-banner';
const BASE_URL = 'http://buss.local';


function initAds(page = 'home') {
    let header = document.getElementsByTagName('header');

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
    this.getBanner = async () => {
        const instance = this;
        let url = new URL(API_URL);
        url.search = new URLSearchParams({
            device: instance.getUserAgent(),
            page: page
        });
        return fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json'},
        }).then(response => response.json()).catch(e => {
            throw e.message;
        });
    }
    this.groupBy = (items, key) => items.reduce(
        (result, item) => ({
            ...result,
            [item[key]]: [
                ...(result[item[key]] || []),
                item,
            ],
        }), {});
    this.renderAds = function (page, data) {

        if (data.length <= 0) {
            console.warn('Không có quản cáo hiển thị!');
            return false;
        }

        let obj = this.groupBy(data, 'is_random');
        
        Object.values(obj).forEach((item, index) => {
            let {media, href, title, id, height, width, position, is_random, bellow_post, youtube_url} = item;
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
            switch (position) {
                case POSITION_TOP:
                    // header[0].insertBefore(link, null);
                    return true;
            }
        });
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
