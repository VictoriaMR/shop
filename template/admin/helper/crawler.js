var CRAWLER = {
    init: function(callback){
        const _this = this;
        if (!HELPERINIT.isItemPage()) {
            callback(-2, {}, '非产品详情页面!');
        } else if (HELPERINIT.isLoginPage()) {
            callback(-1, {}, '页面需要登录!');
        } else if (HELPERINIT.isVerifyPage()) {
            callback(-1, {}, '页面需要验证!');
        } else if (HELPERINIT.isDenyPage()) {
            callback(-1, {}, '页面被阻止访问!');
        } else if (HELPERINIT.isErrorPage()) {
            callback(-2, {}, HELPERINIT.isErrorPage());
        } else if (_this.isOffShelf()) {
            callback(-2, {}, '产品已下架!');
        } else {
            setTimeout(function(){
                _this.data(callback);
            }, Math.random()*3000 + 1000);
        }
    },
    isOffShelf: function() {
        let obj = document.querySelector('.mod-detail-offline .mod-detail-offline-title');
        if (!obj) {
            obj = document.querySelector('.tb-off-sale .tb-hint strong');
        }
        if (!obj) {
            obj = document.querySelector('.sold-out-recommend');
        }
        return obj;
    },
    data: function(callback) {
        const _this = this;
        switch (HELPERINIT.getDomain()) {
            case '1688.com':
                _this.get1688(callback);
                break;
            case 'taobao.com':
            case 'tmall.com':
                var h = document.documentElement.scrollHeight;
                var iH = window.innerHeight;
                var onScroll = function() {
                    var scrollY = window.scrollY || window.pageYOffset;
                    if (scrollY > h - iH - 100) {
                        window.removeEventListener('scroll', onScroll)
                        if (HELPERINIT.getDomain() == 'taobao.com') {
                            _this.getTaobao(callback);
                        } else {
                            _this.getTmall(callback);
                        }
                    }
                };
                window.addEventListener('scroll', onScroll);
                window.scrollTo({top:h, left:0, behavior:'smooth'});
                break;
            default:
                callback(-1, {}, '未知渠道商品详情页面');
                break;
        }
    },
    get1688: function(callback) {
        if (typeof __INIT_DATA === 'undefined') {
            callback(-1, {}, '获取数据失败!');
            return false;
        }
        let ret_data = {};
    },
    getTaobao: function(callback) {
        if (typeof Hub === 'undefined') {
            callback(-1, {}, '获取数据失败!');
            return false;
        }
        let ret_data = {};
        ret_data.channel_id = 6051;
        ret_data.item_id = g_config.idata.item.id;
        ret_data.name = g_config.idata.item.title;
        ret_data.seller = {
            shop_id: g_config.shopId,
            shop_name: g_config.shopName,
            shop_url: g_config.idata.shop.url,
            service: {},
        };
        let obj = document.querySelectorAll('.tb-shop-rate dl');
        var descMap = {'描述':'desc', '物流':'post', '服务':'serv'};
        for (var i=0; i<obj.length; i++) {
            var name = obj[i].querySelector('dt').innerText;
            var value = obj[i].querySelector('dd').innerText;
            ret_data.seller.service[descMap[name]] = value;
        }
        ret_data.attr = {};
        let attrObj = document.querySelectorAll('dl.J_Prop');
        for (let k = 0; k < attrObj.length; k++) {
            let attrName = attrObj[k].querySelector('dt').innerText; //属性名
            let attrValue = {};
            let valueList = attrObj[k].querySelector('dd').getElementsByTagName('li'); //属性名
            let attrNameId = 0;
            for (let j = 0; j < valueList.length; j++) {
                let value = valueList[j].attributes['data-value'].nodeValue.split(':');
                if (!attrNameId) {
                    attrNameId = value[0];
                }
                let name = valueList[j].children[0].children[0].innerText;
                attrValue[value[1]] = {
                    name: name
                };
                if (typeof valueList[j].children[0].attributes['style'] !== 'undefined') {
                    let styleText = valueList[j].children[0].attributes['style'].nodeValue;
                    attrValue[value[1]].img = styleText.match(/background\:url\(((.+))_30x30/)[1];
                }
            }
            ret_data.attr[attrNameId] = {
                name: attrName,
                value: attrValue
            };
        }
        if (Hub.config.config.sku.valItemInfo.skuMap) {
            ret_data.sku = {};
            for (let k in Hub.config.config.sku.valItemInfo.skuMap) {
                let item = Hub.config.config.sku.valItemInfo.skuMap[k];
                if (item.skuId != '0') {
                    let tempMap = k.substr(1, k.length - 2);
                    var attr = tempMap.split(';');
                    let pvs = {};
                    for (let r in attr) {
                        let attr_item = attr[r].split(':');
                        pvs[attr_item[0]] = attr_item[1];
                    }
                    ret_data.sku[item.skuId] = {
                        pvs: pvs,
                        sku_map: tempMap,
                        price: item.price,
                        stock: item.stock
                    };
                }
            }
        }
        // 描述
        ret_data.detail = [];
        obj = document.querySelectorAll('.attributes-list li');
        for (var i=0; i<obj.length; i++) {
            var item = obj[i].innerText.split(': ');
            ret_data.detail.push({name:item[0], value:item[1]});
        }
        ret_data.desc_picture = [];
        obj = document.querySelectorAll('.descV8-container .descV8-singleImage>img');
        for (var i=0; i<obj.length; i++) {
            ret_data.desc_picture.push(obj[i].src);
        }
        callback(0, ret_data, '获取成功!');
    },
    getTmall: function(callback) {
        const _this = this;
        if(typeof KISSY == 'undefined'){
            callback(-1, {}, '获取数据失败!');
            return false;
        }
        let ret_data = {};
        if(KISSY.version == '1.42') {
            KISSY.use('detail-model/product', function (e, t) {
            });
        } else {
            var info = window.g_config.baseInfo;
            ret_data.item_id = info.item.itemId;
            ret_data.name = info.item.title;
            ret_data.channel_id = 6052;

            ret_data.pdt_picture = info.item.images;
            ret_data.seller = {
                shop_id: info.seller.shopId,
                shop_name: info.seller.shopName,
                shop_url: info.seller.pcShopUrl,
                service: {},
            };
            for (var i=0; i<info.seller.evaluates.length; i++) {
                ret_data.seller.service[info.seller.evaluates[i].type] = info.seller.evaluates[i].score;
            }
            ret_data.attr = {};
            for (var i=0; i<info.skuBase.props.length; i++) {
                var item = info.skuBase.props[i];
                ret_data.attr[item.pid] = {
                    name: item.name,
                    value: {},
                };
                for (var j=0; j<item.values.length; j++) {
                    ret_data.attr[item.pid].value[item.values[j].vid] = {
                        name: item.values[j].name,
                    }
                    if (item.values[j].image) {
                       ret_data.attr[item.pid].value[item.values[j].vid].img =  item.values[j].image;
                    }
                }
            }
            ret_data.sku = {};
            let skuMap = {};
            for (var i=0; i<info.skuBase.skus.length; i++) {
                var item = info.skuBase.skus[i];
                skuMap[item.skuId] = item.propPath;
            }
            for (var i in info.skuCore.sku2info) {
                var item = info.skuCore.sku2info[i];
                if (i != '0') {
                    var attr = skuMap[i].split(';');
                    let pvs = {};
                    for (let r in attr) {
                        let attr_item = attr[r].split(':');
                        pvs[attr_item[0]] = attr_item[1];
                    }
                    ret_data.sku[i] = {
                        pvs: pvs,
                        sku_map: skuMap[i],
                        price: item.price.priceText,
                        stock: item.quantity,
                    };
                }
            }
            ret_data.detail = [];
            let obj = document.querySelectorAll('.ItemDetail--attrs--3t-mTb3 .Attrs--attr--33ShB6X');
            for (var i=0; i<obj.length; i++) {
                var item = obj[i].innerText.split('：');
                ret_data.detail.push({name:item[0], value:item[1]});
            }
            obj = document.querySelectorAll('.descV8-container .descV8-singleImage>img');
            ret_data.desc_picture = [];
            for (var i=0; i<obj.length; i++) {
                ret_data.desc_picture.push(obj[i].src);
            }
            callback(0, ret_data, '获取成功!');
        }
    },
};