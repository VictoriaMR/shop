if (typeof simpler_crawler_data === 'undefined') {
    simpler_crawler_data = false;
}
if (typeof shop_info_crawler_only === 'undefined') {
    shop_info_crawler_only = false;
}

function b_log(info) {
    (function () {
        var iframe = document.createElement('iframe');
        document.body.appendChild(iframe);
        b_log = iframe.contentWindow.console.log;
        b_log(info);
    }());
}
/*
 *callback function (code,data,msg){} //code=0 获取成功 获取的数据存在data中 {sku:sku,attr:attr,multi_sku:multi_sku,name:name,product_url: location.href,pdt_picture:pdt_picture}
 * */
function getCrawData(callback) {
    BAYHELPER_INIT.isVerifyPage(function(status){
        if (status) {
            callback(-1, {}, '页面需要验证!');
            return false;
        } else {
            //需要登录
            if (BAYHELPER_INIT.isLoginPage()) {
                callback(-1, {}, '页面需要登录!');
                return false;
            }
            //访问被墙
            if (BAYHELPER_INIT.isDenyPage()) {
                callback(-1, {}, '访问被拒绝!');
                return false;
            }
            let item_id = BAYHELPER_INIT.isItemPage();
            if (item_id) {
                switch (BAYHELPER_INIT.getDomain()) {
                    case '1688.com':
                        //检测是否下架页面
                        if (!isOffShelf1688(callback)) {
                            get1688(callback);
                        }
                        break;
                    case 'taobao.com':
                        //检测是否下架页面
                        if (!isOffShelfTaobao(callback)) {
                            getTaobao(function(code, data, msg) {
                                if (code == 0) {
                                    data.level = getLevelOrder(data.creditnumber, data.creditflag);
                                }
                                callback(code, data, msg);
                            });
                        }
                        break;
                    case 'tmall.com':
                        //检测是否下架页面
                        if (!isOffShelfTmall(callback)) {
                            getTmall(callback,0);
                        }
                        break;
                }
            } else {
                callback(-4, {}, '非产品详情页');
            }
        }
    });
}

/**
 * 获取等级数字
 * @param  {[type]} number [description]
 * @param  {[type]} flag   [description]
 * @return {[type]}        [description]
 */
function getLevelOrder(number, flag) {
    var level = 0;
    switch(flag) {
        case 'blue':
        case '钻级':
            level = 5;
            break;
        case 'cap':
        case 'cap1':
        case '冠级':
            level = 10;
            break;
        case 'crown':
            level = 15;
            break;
    }
    return level + number;
}

function generalUniqueAttrId(attr) {
    var name_start_idx = 10000;
    var value_start_idx = 20000;
    var attrName = {};
    var attrValue = {};
    for (let i in attr) {
        attrName[attr[i].attrName] = name_start_idx;
        for (let j in attr[i].attrValue) {
            attrValue[attr[i].attrValue[j].name] = value_start_idx;
            value_start_idx++
        }
        name_start_idx++;
    }

    return {
        attr_name: attrName,
        attr_value: attrValue
    }
}

function getTaobao(callback) {
    if (typeof Hub === 'undefined') {
        callback(-1, {}, '获取数据失败!请记录当前链接联系开发人员.');
        return false;
    }
    if (shop_info_crawler_only) {
        getTaoBaoData({}, callback);
        return false;
    }
    let multi_sku = 0;
    let name = getTaobaoProductName();
    let pdt_picture = getTaobaoPdtPicture();
    let skuDomList = document.querySelectorAll('.J_Prop');
    let attr = {};
    let sku = {};
    let attr_uid = {};
    if (Hub.config.config.sku.valItemInfo.skuMap) {
        //多sku产品
        //这里有延时加载
        let interval = setInterval(function () {
            if (g_config.dynStock && g_config.dynStock.sku) {
                clearInterval(interval);
                multi_sku = 1;
                let sort = 0;
                for (let k = 0; k < skuDomList.length; k++) {
                    let attrName = skuDomList[k].getElementsByTagName('dt')[0].innerText; //属性名
                    let attrValue = {};
                    let valueList = skuDomList[k].getElementsByTagName('dd')[0].getElementsByTagName('li'); //属性名
                    let attrNameId = 0;
                    for (let j = 0; j < valueList.length; j++) {
                        let value = valueList[j].attributes['data-value'].nodeValue.split(':');
                        if (!attrNameId) {
                            attrNameId = value[0];
                        }
                        let img = '';
                        if (typeof valueList[j].children[0].attributes['style'] !== 'undefined') {
                            let styleText = valueList[j].children[0].attributes['style'].nodeValue;
                            img = styleText.match(/background\:url\(((.+))_30x30/)[1];
                        }
                        let name = valueList[j].children[0].children[0].innerText;
                        attrValue[value[1]] = {
                            name: name,
                            img: img,
                            sort: sort
                        };
                        sort++;
                    }
                    attr[attrNameId] = {
                        attrName: attrName,
                        attrValue: attrValue
                    };
                }
                attr_uid = generalUniqueAttrId(attr);
                for (let k in Hub.config.config.sku.valItemInfo.skuMap) {
                    let item = Hub.config.config.sku.valItemInfo.skuMap[k];
                    let stock;
                    if (g_config.dynStock && typeof g_config.dynStock.sku !== 'undefined' && typeof g_config.dynStock.sku[k] !== 'undefined') {
                        stock = g_config.dynStock.sku[k].stock;
                    } else {
                        stock = item.stock;
                        continue;
                    }
                    let attr_arr = k.substr(1, k.length - 2).split(';');
                    let pvs = {};
                    let sku_img = '';
                    let sort;
                    for (let i in attr_arr) {
                        let attr_item = attr_arr[i].split(':');
                        if (!attr[attr_item[0]] || !attr[attr_item[0]]['attrValue'][attr_item[1]]) {
                            continue;
                        }
                        if (attr[attr_item[0]]['attrValue'][attr_item[1]].img) {
                            sku_img = attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                        }
                        sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                        let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                        pvs[attr[attr_item[0]].attrName] = {
                            attr_id: attr_uid['attr_value'][text],
                            text: text,
                            img: sku_img,
                            sort: sort
                        };
                    }
                    let price;
                    if (typeof g_config.promotion != 'undefined' && typeof g_config.promotion.promoData != 'undefined' && typeof g_config.promotion.promoData[k] != 'undefined') {
                        price = g_config.promotion.promoData[k][0].price;
                    } else if (typeof g_config.originalPrice != 'undefined') {
                        price = g_config.originalPrice[k].price;
                    } else {
                        price = item.price;
                    }
                    sku[item.skuId] = {
                        pvs: pvs,
                        price: price,
                        stock: stock,
                        sku_img: sku_img
                    };
                }
                let ret_data = {
                    sku: sku,
                    attr: attr,
                    name: name,
                    pdt_picture: pdt_picture,
                    multi_sku: multi_sku,
                    product_url: location.href,
                    item_id: BAYHELPER_INIT.isItemPage(),
                    attr_uid: attr_uid,
                    pdt_video: ''
                };
                getTaoBaoData(ret_data, callback);
            }
        }, 600);
    } else {
        if (g_config.price) {
            sku.price = g_config.price;
        } else {
            sku.price = 0;
            const priceObj = document.querySelector('#J_StrPrice .tb-rmb-num');
            if (priceObj) {
                sku.price = priceObj.innerText;
            }
        }
        if (g_config.dynStock) {
            sku.stock = g_config.dynStock.stock;
        } else {
            sku.stock = 0;
            let stockObj = document.querySelector('#J_SpanStock');
            if (stockObj) {
                sku.stock = stockObj.innerText;
            }
        }
        if (sku.price == 0 || sku.price == '' || sku.stock == 0 || sku.stock == '') {
            callback(-1, {}, '获取数据失败!没有获取到价格库存!');
            return false;
        }
        let ret_data = {
            sku: sku,
            attr: attr,
            name: name,
            pdt_picture: pdt_picture,
            multi_sku: multi_sku,
            product_url: location.href,
            item_id: BAYHELPER_INIT.isItemPage(),
            attr_uid: attr_uid,
            pdt_video: ''
        };
        getTaoBaoData(ret_data, callback);
    }
}

function getTaoBaoData(ret_data, callback) {
    let supplier_data = getTaobaoSupplier();
    if (supplier_data === false) {
        callback(-1, {}, '获取店铺地址失败, 请带上链接联系技术人员.');
        return false;
    }
    for (let i in supplier_data) {
        ret_data[i] = supplier_data[i];
    }
    //销量
    ret_data.sold_total = 0;
    if (typeof g_config.soldQuantity !== 'undefined') {
        if(typeof g_config.soldQuantity.confirmGoodsCount !== 'undefined') {
            ret_data.sold_total = g_config.soldQuantity.confirmGoodsCount;
        }else if(typeof g_config.soldQuantity.soldTotalCount !== 'undefined'){
            ret_data.sold_total = g_config.soldQuantity.soldTotalCount
        }
    }
    //评论数
    var reviewobj = document.getElementById('J_RateCounter');
    ret_data.review_total = 0;
    if (reviewobj) {
        ret_data.review_total = document.getElementById('J_RateCounter').innerText;
    }
    //信誉 服务分
    var shopobj = document.getElementById('J_ShopInfo');
    if (shopobj) {
        ret_data.creditscore = shopobj.getAttribute('data-creditscore'); //分数
        ret_data.creditflag = shopobj.getAttribute('data-creditflag'); //类型
        ret_data.creditnumber = shopobj.getElementsByClassName('tb-shop-rank')[0].getElementsByTagName('dd')[0].getElementsByTagName('i').length; //个数
        //服务分
        const obj = shopobj.querySelector('.tb-shop-info-bd');
        let service = {};
        if (obj) {
            const serviceobj = obj.querySelectorAll('dl');
            let tempObj;
            for (let i = 0; i < serviceobj.length; i++) {
                if (typeof serviceobj[i].getElementsByTagName === 'function') {
                    tempobj = serviceobj[i].querySelector('dt');
                    if (tempobj) {
                        const key = tempobj.innerText;
                        tempobj = serviceobj[i].querySelector('dd');
                        if (tempobj) {
                            service[key] = tempobj.innerText;
                        }
                    }
                }
            }
        }
        ret_data.service = service;
    } else {
        ret_data.creditscore = 0;
        ret_data.creditflag = '';
        ret_data.creditnumber = 0;
        shopobj = document.querySelector('.summary-popup');
        if (shopobj) {
            let tempobj = shopobj.querySelector('.rank-icon-v2');
            if (tempobj) {
                ret_data.creditnumber = tempobj.querySelectorAll('i').length;
                ret_data.creditflag = getFlagByName(tempobj.getAttribute('class'));
            } else {
                ret_data.creditnumber = 0;
                ret_data.creditflag = '';
            }
            let service = {};
            const serviceobj = document.querySelectorAll('.shop-service-info-item');
            for (let i = 0; i < serviceobj.length; i++) {
                if (typeof serviceobj[i].getElementsByClassName === 'function') {
                    tempobj = serviceobj[i].querySelector('.title');
                    if (tempobj) {
                        const key = tempobj.innerText;
                        tempobj = serviceobj[i].querySelector('.rateinfo em');
                        if (tempobj) {
                            service[key] = tempobj.innerText;
                        }

                    }
                }
            }
            ret_data.service = service;
        }
    }
    if (shop_info_crawler_only || simpler_crawler_data) {
        callback(0, ret_data, '获取成功!');
        return false;
    }
    if (typeof g_config !== 'undefined' && typeof g_config.rateCounterApi != 'undefined') {
        var rate_api_url = g_config.rateCounterApi;
        if (rate_api_url.substring(0, 2) == '//') {
            rate_api_url = 'https:' + rate_api_url;
        }
        var firstIdx = rate_api_url.indexOf('?');
        if (firstIdx!==-1) {
            rate_api_url = rate_api_url.substr(0, firstIdx);
        }
        var queryParam = {
            'itemId': g_config.itemId,
            '_ksTS=': new Date().getTime(),
        };
        ajax({
            url: rate_api_url,
            data: queryParam,
            dataType: 'jsonp',
            success: function (res) {
                ret_data.review_total = res.count; // 评论数据总数
                getTaobaoDesPic(callback, ret_data);
            },
            error: function (error) {
                getTaobaoDesPic(callback, ret_data);
                return false;
            }
        });
    } else {
        //发起请求在请求内容中提取图片
        getTaobaoDesPic(callback, ret_data);
    }
}

function getFlagByName(name) {
    // var reg = /[^tb-rank-].\s$/;
    var rst = name.match(/tb-rank-([^\s]*)\s/);
    if (rst) {
        return rst[1];
    } else {
        return '';
    }
}

function get1688(callback) {
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA === 'undefined') {
        callback(-1, {}, '获取数据失败!请记录当前链接联系开发人员.');
        return false;
    }
    let type = 1;
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA !== 'undefined') {
        //新版页面
        type = 2;
    }
    let multi_sku = 0;
    let name = get1688ProductName();
    let pdt_picture = get1688PdtPicture();
    let attr = {};
    let sku = {};
    let attr_uid = {};

    if (type == 1 && typeof iDetailData.sku !== 'undefined') {
        multi_sku = 1;
        let sort = 0;
        let skuProp = iDetailData.sku.skuProps;
        for (let attrNameId = 0; attrNameId < skuProp.length; attrNameId++) {
            let attrName = skuProp[attrNameId].prop;
            let attrValue = {};
            for (let attrValueId = 0; attrValueId < skuProp[attrNameId].value.length; attrValueId++) {
                let img = '';
                if (typeof skuProp[attrNameId].value[attrValueId].imageUrl != 'undefined' && skuProp[attrNameId].value[attrValueId].imageUrl) {
                    img = skuProp[attrNameId].value[attrValueId].imageUrl;
                }
                let attrValueName = skuProp[attrNameId].value[attrValueId].name;
                attrValue[attrValueId] = {
                    name: attrValueName,
                    img: img,
                    sort: sort
                };
                sort++;
            }
            attr[attrNameId] = {
                attrName: attrName,
                attrValue: attrValue
            };
        }
        attr_uid = generalUniqueAttrId(attr);
        let skuMap = iDetailData.sku.skuMap;
        for (let k in skuMap) {
            let item = skuMap[k];
            let stock;
            stock = item.canBookCount;
            let sku_attr = k.split('&gt;');
            let pvs = {};
            let sku_img = '';
            for (let j = 0; j < sku_attr.length; j++) {
                if (!sku_attr[j]) continue;
                let attrNameAndImg = get1688AttrNameAndImg(sku_attr[j], attr);
                pvs[attrNameAndImg['attrName']] = {
                    attr_id: attr_uid['attr_value'][attrNameAndImg['attrValue']],
                    text: attrNameAndImg['attrValue'],
                    img: attrNameAndImg['img'],
                    sort: attrNameAndImg['sort']
                };
                if (attrNameAndImg['img']) {
                    sku_img = attrNameAndImg['img'];
                }
            }
            let price = (typeof item.discountPrice != 'undefined') ? item.discountPrice : (typeof iDetailData.sku.priceRangeOriginal !== 'undefined' ? iDetailData.sku.priceRangeOriginal[0][1] : '');
            sku[item.skuId] = {
                pvs: pvs,
                price: price,
                stock: stock,
                sku_img: sku_img
            };
        }
    } else if (type == 2 && typeof window.__INIT_DATA.globalData.skuModel != 'undefined' && typeof window.__INIT_DATA.globalData.skuModel.skuProps != 'undefined') {
        if (typeof window.__INIT_DATA.globalData.channelType !== 'undefined') {
            if (window.__INIT_DATA.globalData.channelType.toLowerCase() == "jgdz") {
                callback(-3, {}, '产品不可购买');
                return false;
            }
        }
        multi_sku = 1;
        let sort = 0;
        let skuProp = window.__INIT_DATA.globalData.skuModel.skuProps;
        for (let attrNameId = 0; attrNameId < skuProp.length; attrNameId++) {
            let attrName = skuProp[attrNameId].prop;
            let attrValue = {};
            for (let attrValueId = 0; attrValueId < skuProp[attrNameId].value.length; attrValueId++) {
                let img = '';
                if (typeof skuProp[attrNameId].value[attrValueId].imageUrl != 'undefined' && skuProp[attrNameId].value[attrValueId].imageUrl) {
                    img = skuProp[attrNameId].value[attrValueId].imageUrl;
                }
                let attrValueName = skuProp[attrNameId].value[attrValueId].name;
                attrValue[attrValueId] = {
                    name: attrValueName,
                    img: img,
                    sort: sort
                };
                sort++;
            }
            attr[attrNameId] = {
                attrName: attrName,
                attrValue: attrValue
            };
        }
        attr_uid = generalUniqueAttrId(attr);
        let skuMap = window.__INIT_DATA.globalData.skuModel.skuInfoMap;
        let back_up_price = 0;
        var g_data = window.__INIT_DATA.globalData;
        if (typeof g_data.orderParamModel != 'undefined' && typeof g_data.orderParamModel.orderParam != 'undefined' && typeof g_data.orderParamModel.orderParam.skuParam != 'undefined') {
            if (typeof g_data.orderParamModel.orderParam.skuParam.skuPriceType != 'undefined' && g_data.orderParamModel.orderParam.skuParam.skuPriceType == 'rangePrice') {
                if (typeof g_data.orderParamModel.orderParam.skuParam.skuRangePrices != 'undefined' && g_data.orderParamModel.orderParam.skuParam.skuRangePrices.length > 0) {
                    back_up_price = g_data.orderParamModel.orderParam.skuParam.skuRangePrices[0].price;
                }
            }
        }
        for (let k in skuMap) {
            let item = skuMap[k];
            let stock;
            if (typeof item.canBookCount == 'undefined') {
                callback(-1, {}, '获取数据失败!购买数量获取失败');
                return false;
            }
            stock = item.canBookCount;
            let sku_attr = k.split('&gt;');
            let pvs = {};
            let sku_img = '';
            for (let j = 0; j < sku_attr.length; j++) {
                if (!sku_attr[j]) continue;
                let attrNameAndImg = get1688AttrNameAndImg(sku_attr[j], attr);
                if (!attrNameAndImg) continue;
                pvs[attrNameAndImg['attrName']] = {
                    attr_id: attr_uid['attr_value'][attrNameAndImg['attrValue']],
                    text: attrNameAndImg['attrValue'],
                    img: attrNameAndImg['img'],
                    sort: attrNameAndImg['sort']
                };
                if (attrNameAndImg['img']) {
                    sku_img = attrNameAndImg['img'];
                }
            }
            if (typeof window.__GLOBAL_DATA.offerDomain != 'undefined') {
                let offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
                if (typeof offerDomain.tradeModel.offerPriceModel.currentPrices != 'undefined') {
                    back_up_price = offerDomain.tradeModel.offerPriceModel.currentPrices[0].price;
                }
            }
            let price = (typeof item.discountPrice != 'undefined') ? item.discountPrice : (typeof item.price != 'undefined' ? item.price : back_up_price);
            sku[item.skuId] = {
                pvs: pvs,
                price: price,
                stock: stock,
                sku_img: sku_img
            };
        }
    } else {
        if (type == 2) {
            if (typeof window.__GLOBAL_DATA.offerDomain != 'undefined') {
                let offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
                if (typeof offerDomain.tradeModel == 'undefined' || typeof offerDomain.tradeModel.canBookedAmount == 'undefined') {
                    callback(-1, {}, '获取数据失败!');
                    return false;
                }
                let price = 0;
                if (typeof offerDomain.tradeModel.displayPrice != 'undefined') {
                    price = offerDomain.tradeModel.displayPrice;
                } else if (typeof offerDomain.tradeModel.maxPrice != 'undefined') {
                    price = offerDomain.tradeModel.maxPrice;
                } else {
                    callback(-1, {}, '获取数据失败!');
                    return false;
                }
                sku = {
                    price: price,
                    stock: offerDomain.tradeModel.canBookedAmount
                };
            } else if (typeof window.__GLOBAL_DATA.orderParamModel != 'undefined' && typeof window.__GLOBAL_DATA.orderParamModel.orderParam != 'undefined') {
                var sku_price = 0;
                var g_data = window.__GLOBAL_DATA;
                if (typeof g_data.orderParamModel.orderParam.skuParam != 'undefined') {
                    if (typeof g_data.orderParamModel.orderParam.skuParam.skuPriceType != 'undefined' && g_data.orderParamModel.orderParam.skuParam.skuPriceType == 'rangePrice') {
                        if (typeof g_data.orderParamModel.orderParam.skuParam.skuRangePrices != 'undefined' && g_data.orderParamModel.orderParam.skuParam.skuRangePrices.length > 0) {
                            sku_price = g_data.orderParamModel.orderParam.skuParam.skuRangePrices[0].price;
                        }
                    }
                }
                sku = {
                    price: sku_price,
                    stock: g_data.orderParamModel.orderParam.canBookedAmount
                };
            } else {
                callback(-1, {}, '获取数据失败!');  
                return false;
            }
        } else {
            let price = document.querySelectorAll('meta[property="og:product:price"]')[0].content;
            let stock;
            try {
                stock = (JSON.parse(document.querySelectorAll('.mod-detail-purchasing.mod-detail-purchasing-single')[0].getAttribute('data-mod-config')))['max'];
            } catch (e) {
                if (document.getElementById('pageName').value.substring(0, 7) == '大市场加工定制') {
                    stock = 0;
                } else {
                    //获取库存失败
                    // callback(-1,{},'获取1688库存失败!请记录当前链接联系开发人员.');
                    callback(-1, {}, '获取数据失败!获取1688库存失败!请记录当前链接联系开发人员.');
                    return false;
                }
            }
            sku = {
                price: price,
                stock: stock
            };
        }
    }
    let ret_data = {
        sku: sku,
        attr: attr,
        name: name,
        pdt_picture: pdt_picture,
        multi_sku: multi_sku,
        product_url: location.href,
        item_id: BAYHELPER_INIT.isItemPage(),
        attr_uid: attr_uid,
        pdt_video: ''
    };
    let supplier_data = get1688Supplier(callback);
    for (let i in supplier_data) {
        ret_data[i] = supplier_data[i];
    }
    //头部信息
    var obj = document.querySelectorAll('#header .app-topbar');
    if (obj.length > 0) {
        var dataViewConfig = obj[0].getAttribute('data-view-config');
        dataViewConfig = JSON.parse(dataViewConfig);
        if (dataViewConfig.astorePageData && dataViewConfig.astorePageData.components) {
            var service = {};
            var data = dataViewConfig.astorePageData.components;
            for (var i in data) {
                if (data[i].moduleData && data[i].moduleData.appData) {
                    for (j in data[i].moduleData.appData.serviceList) {
                        if (data[i].moduleData.appData.serviceList[j].serviceKey) {
                            service[get1688ServiceName(data[i].moduleData.appData.serviceList[j].serviceKey)] = data[i].moduleData.appData.serviceList[j].score;
                        }
                    }
                }
            }
            ret_data.service = service;
        }
    }
    //供应等级
    obj = document.querySelectorAll('.supply-grade');
    if (obj.length > 0) {
        obj = obj[0];
        if (obj.querySelectorAll('.image img').length > 0) {
            ret_data.creditflag = get1688Flag(obj.querySelectorAll('.image img')[0].getAttribute('src'));
            ret_data.creditnumber = obj.querySelectorAll('.image img').length;
        } else {
            ret_data.creditflag = '';
            ret_data.creditnumber = 0;
        }
    }
    //交易勋章
    if(typeof __STORE_DATA != 'undefined' ){
        if(typeof __STORE_DATA.components != 'undefined' && typeof __STORE_DATA.components['38229149'] != 'undefined' && typeof __STORE_DATA.components['38229149'].moduleData != 'undefined' && typeof __STORE_DATA.components['38229149'].moduleData.rateLogoUrl != 'undefined'){
            if(__STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/O1CN01T5zgtS1V1dhYutHVi_/) || __STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/member\/medal\/icon\/p5/)){
                ret_data.tradeGrade=5;
            }else if(__STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/O1CN013rsGQ71F7YyIpYNV9_/) || __STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/member\/medal\/icon\/p4/)){
                ret_data.tradeGrade=4;
            }else if(__STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/O1CN01Cajyip21870KaCypF_/) || __STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/member\/medal\/icon\/p3/)){
                ret_data.tradeGrade=3;
            }else if(__STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/O1CN01PTeVOc1oYatudXmMd_/) || __STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/member\/medal\/icon\/p2/)){
                ret_data.tradeGrade=2;
            }else if(__STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/O1CN01AUrNmz1Mb3n1wG6Z1_/) || __STORE_DATA.components['38229149'].moduleData.rateLogoUrl.match(/member\/medal\/icon\/p1/)){
                ret_data.tradeGrade=1;
            }else{
                ret_data.tradeGrade=0;
            }
        }
    }
    //诚信年限
    if(typeof __STORE_DATA != 'undefined' ){
        if(typeof __STORE_DATA.components != 'undefined' && typeof __STORE_DATA.components['38229149'] != 'undefined' && typeof __STORE_DATA.components['38229149'].moduleData != 'undefined' && typeof __STORE_DATA.components['38229149'].moduleData.tpYear != 'undefined'){
            ret_data.integrityYear=__STORE_DATA.components['38229149'].moduleData.tpYear;
        }
    }
    if (shop_info_crawler_only) {
        if (!ret_data.service) {
            obj = document.querySelector('[style="position: relative; display: flex; flex-direction: row;"]');
            if (obj) {
                var event = new MouseEvent('mouseover', {
                    'view': window,
                    'bubbles': true,
                    'cancelable': true
                });
                obj.dispatchEvent(event);
                let count = 0;
                let interval = setInterval(function(){
                    if (typeof window.__commonHeader__ != 'undefined') {
                        let info = window.__commonHeader__.lindormDataModel;
                        clearInterval(interval);
                        let service = {};
                        service['综合服务'] = info.customerStar;
                        for (let i=0; i<info.serviceStarList.length; i++) {
                            service[get1688ServiceName(info.serviceStarList[i].serviceKey)] = info.serviceStarList[i].score;
                        }
                        ret_data.service = service;
                        callback(0, ret_data, '获取成功!');
                    } else if (count > 10) {
                        clearInterval(interval);
                        callback(0, ret_data, '获取成功!');
                    }
                    count++;
                }, 1000);
            } else {
                callback(0, ret_data, '获取成功!');
            }
        } else {
            callback(0, ret_data, '获取成功!');
        }
        return;
    }
    get1688ContactMember(ret_data['shop_url'], function (contact, mobile) {
        if (ret_data['shop_url'] && ret_data['shop_url'].match(/(http[s]{0,1}:)?\/\/$/i)) {
            ret_data['shop_url'] = 'https://detail.1688.com';
        }
        ret_data.contact = contact;
        ret_data.mobile = mobile;
        if (type == 1) {
            var param = {
                offerId: window.iDetailConfig.offerid,
                pageId: window.iDetailConfig.pageid,
                data: 'offerdetail_ditto_offerSatisfaction'
            };
            ajax({
                url: window.iDetailConfig.staticUrl,
                data: param,
                dataType: 'jsonp',
                success: function (res) {
                    if (res.data) {
                        ret_data.sold_total = res.data.data.offerdetail_ditto_offerSatisfaction.saleTotal;
                        ret_data.review_total = res.data.data.offerdetail_ditto_offerSatisfaction.rateCount;
                    }
                    get1688DesPic(callback, ret_data);
                },
                error: function (error) {
                    // alert('获取销售/评价数据失败, 请记录当前链接联系开发人员');
                    // console.log(error, 'error');
                    callback(-1, {}, '获取数据失败!获取销售/评价数据失败, 请记录当前链接联系开发人员');
                }
            });
        } else if (type == 2) {
            ret_data.sold_total = 0;
            if (typeof window.__GLOBAL_DATA !== 'undefined' && typeof window.__GLOBAL_DATA.tempModel != 'undefined' && typeof window.__GLOBAL_DATA.tempModel.saledCount != 'undefined') {
                ret_data.sold_total = window.__GLOBAL_DATA.tempModel.saledCount;
            }
            var api_url = 'https://h5api.m.1688.com/h5/mtop.mbox.fc.common.gateway/1.0/';
            BAYHELPER_INIT.request({
                action: 'getCookie',
                host: api_url
            }, function (response) {
                if (typeof response.cookies !== 'undefined' && response.cookies) {
                    var cookieArr = response.cookies.split(';');
                    var cookiesMatch = '';
                    for (var i in cookieArr) {
                        if (typeof cookieArr[i] == 'string') {
                            var splitArr = cookieArr[i].split('=');
                            if (splitArr[0] == '_m_h5_tk') {
                                var cookiesMatch = splitArr;
                                break;
                            }
                        }
                    }
                    if (cookiesMatch) {
                        let memberId, sellerId, offerId;
                        if (typeof window.__GLOBAL_DATA !== 'undefined') {
                            memberId = window.__GLOBAL_DATA.offerBaseInfo.sellerMemberId;
                            sellerId = window.__GLOBAL_DATA.offerBaseInfo.sellerUserId;
                            offerId = window.__GLOBAL_DATA.offerBaseInfo.offerId;
                        }
                        var _m_h5_tk = cookiesMatch[1].split('_')[0];
                        var t = new Date().getTime();
                        var appKey = '12574478';
                        var data = JSON.stringify({
                            fcGroup: "offer-cbu",
                            fcName: "offerdetail-service",
                            fcArgs: JSON.stringify({
                                serviceName: "offerSatisfactionService",
                                params: {
                                    memberId: memberId,
                                    sellerId: sellerId,
                                    offerId: offerId,
                                    isSignedForTm: false
                                }
                            })
                        });
                        var paramStr = _m_h5_tk + '&' + t + '&' + appKey + '&' + data;
                        var queryParam = {
                            'jsv': '2.4.11',
                            'appKey': appKey,
                            't': t,
                            'sign': hex_md5(paramStr), // 获取sign,md5加密
                            'api': 'mtop.mbox.fc.common.gateway',
                            'v': '1.0',
                            'type': 'jsonp',
                            'isSec': 0,
                            'timeout': 20000,
                            'dataType': 'jsonp',
                            'data': data
                        };
                        ajax({
                            url: api_url,
                            data: queryParam,
                            dataType: 'jsonp',
                            success: function (res) {
                                if (typeof res.data.result.saleTotalStr!='undefined') {
                                    ret_data.sold_total = res.data.result.saleTotalStr;
                                }
                                ret_data.review_total = res.data.result.totalsStr;
                                get1688DesPic(callback, ret_data);
                            },
                            error: function (error) {
                                // alert('获取销售/评价数据失败, 请记录当前链接联系开发人员');
                                // alert(error + '获取销售/评价数据失败, 请记录当前链接联系开发人员', 'error');
                                callback(-1, {}, '获取数据失败!获取销售/评价数据失败, 请记录当前链接联系开发人员');
                                return false;
                            }
                        });
                    } else {
                        // alert('token获取失败,请刷新页面');
                        callback(-1, {}, '获取数据失败!token获取失败,请刷新页面');
                        return false;
                    }
                }

            });
        }
    });
}

function get1688Flag(name) {
    if (name.match(/2422944_1490276829/)) {
        return '钻级';
    } else if (name.match(/2421892_1490276829/)) {
        return '星级';
    } else if (name.match(/2423877_1490276829/)) {
        return '冠级';
    }
    return '';
}

function get1688ServiceName(name) {
    var arr = {
        composite_star_new: "综合服务",
        lgt_group_value_new: "物流时效",
        rdf_group_value_new: "退换体验",
        dspt_group_value: "纠纷解决",
        goods_group_value: "品质体验",
        cst_group_value_new: "采购咨询"
    };
    if (typeof arr[name] === 'undefined') {
        return name;
    }
    return arr[name];
}

function get1688AttrNameAndImg(attrValue, attr) {
    for (let k in attr) {
        for (let j in attr[k]['attrValue']) {
            if (attr[k]['attrValue'][j].name == attrValue || attr[k]['attrValue'][j].name.replace(/&gt;$/, '') == attrValue) {
                return {
                    attrName: attr[k].attrName,
                    img: attr[k]['attrValue'][j].img,
                    sort: attr[k]['attrValue'][j].sort,
                    attrValue: attr[k]['attrValue'][j].name
                };
            }
        }
    }
}

function getTmall(callback,retry) {
    if(typeof KISSY != 'undefined' && typeof KISSY.version != 'undefined'){
        if(KISSY.version == '1.42')
        {
            let multi_sku = 0;
            let name = getTmallProductName();
            let pdt_picture = getTmallPdtPicture();
            let sku = {};
            let attr = {};
            let attr_uid = {};
            KISSY.use('detail-model/product', function (e, t) {
                let skuMap = t.instance()['__attrVals']['skuMap'];
                if (!skuMap || typeof skuMap == 'undefined') {
                    // callback(-1,{},'获取sku列表失败!请记录当前链接联系开发人员.');
                    getTmallTShopSetup(callback);
                    return false;
                }
                let skuDomList = document.querySelectorAll('.tb-prop.tm-sale-prop');
                var sortArr = [];
                if (skuMap) {
                    multi_sku = 1;
                    let tmpSort = 0;
                    for (let k = 0; k < skuDomList.length; k++) { // 记录属性的sort
                        let valueList = skuDomList[k].getElementsByTagName('dd')[0].getElementsByTagName('li'); //属性名
                        for (let j = 0; j < valueList.length; j++) {
                            let value = valueList[j].attributes['data-value'].nodeValue;
                            sortArr[value] = tmpSort;
                            tmpSort++;
                        }
                    }
                    let skuProp = t.instance()['__attrVals']['skuProp'];
                    let propertyPics = t.instance()['__attrVals']['propertyPics'];
                    if (typeof skuProp == 'undefined') {
                        callback(-1, {}, '获取数据失败!获取sku属性失败!请记录当前链接联系开发人员.');
                        return false;
                    }
                    for (let attrNameId in skuProp) {
                        let attrName;
                        let attrValue = {};
                        for (let attrValueId in skuProp[attrNameId]) {
                            let img = '';
                            if (propertyPics && typeof propertyPics[';' + attrNameId + ':' + attrValueId + ';'] != 'undefined' && typeof propertyPics[';' + attrNameId + ':' + attrValueId + ';'][0] != 'undefined') {
                                img = propertyPics[';' + attrNameId + ':' + attrValueId + ';'][0].replace('_40x40q90.jpg', '');
                            }
                            let sort = 0;
                            if (sortArr[attrNameId + ':' + attrValueId] != 'undefined') {
                                sort = sortArr[attrNameId + ':' + attrValueId];
                            }
                            attrName = skuProp[attrNameId][attrValueId].label;
                            attrValue[attrValueId] = {
                                name: skuProp[attrNameId][attrValueId].text,
                                img: img,
                                sort: sort
                            };
                        }
                        attr[attrNameId] = {
                            attrName: attrName,
                            attrValue: attrValue
                        };
                    }
                    attr_uid = generalUniqueAttrId(attr);
                    for (let k in skuMap) {
                        let item = skuMap[k];
                        let stock = t.instance()['__attrVals']['inventory']['skuQuantity'][item.skuId]['quantity'];
                        if (stock == 0) {
                            //跳过没库存产品
                            continue;
                        }
                        let attr_arr = k.substr(1, k.length - 2).split(';');
                        let pvs = {};
                        let sku_img = '';
                        let sort;
                        for (let i in attr_arr) {
                            let attr_item = attr_arr[i].split(':');
                            if (!attr[attr_item[0]] || !attr[attr_item[0]]['attrValue'][attr_item[1]]) {
                                continue;
                            }
                            if (attr[attr_item[0]]['attrValue'][attr_item[1]].img) {
                                sku_img = attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                            }
                            sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                            let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                            pvs[attr[attr_item[0]].attrName] = {
                                attr_id: attr_uid['attr_value'][text],
                                text: text,
                                img: sku_img,
                                sort: sort
                            };
                        }
                        let price = item.price;
                        sku[item.skuId] = {
                            pvs: pvs,
                            price: price,
                            stock: stock,
                            sku_img: sku_img
                        };
                        let priceInfo = t.instance()['__attrVals']['priceInfo'];
                        //有优惠价 覆盖
                        for (let k in priceInfo) {
                            if (typeof sku[k] != 'undefined' && priceInfo[k].promotionList) {
                                sku[k].price = priceInfo[k].promotionList[0].price;
                            }
                        }
                    }
                } else {
                    let price = (typeof t.instance()['__attrVals']['promoPrice'] != 'undefined' && t.instance()['__attrVals']['promoPrice']) ? t.instance()['__attrVals']['promoPrice']['str'] : t.instance()['__attrVals']['originalPrice']['str'];
                    if (typeof price === 'undefined' || price == 0 || price == '') {
                        callback(-1, {}, '获取数据失败!没有获取到价格库存!');
                        return false;
                    }
                    sku = {
                        price: price,
                        stock: t.instance()['__attrVals']['currentInventory'].quantity
                    };
                }
                let ret_data = {
                    sku: sku,
                    attr: attr,
                    name: name,
                    pdt_picture: pdt_picture,
                    multi_sku: multi_sku,
                    product_url: location.href,
                    item_id: BAYHELPER_INIT.isItemPage(),
                    attr_uid: attr_uid,
                    pdt_video: ''
                };
                let supplier_data = getTmallSupplier();
                for (let i in supplier_data) {
                    ret_data[i] = supplier_data[i];
                }
                let des_url = '';
                if (typeof t.instance()['__attrVals'].config.api.httpsDescUrl != 'undefined') {
                    des_url = t.instance()['__attrVals'].config.api.httpsDescUrl;
                }
                //tmall服务分
                getTmallService(ret_data, function (ret_data) {
                    if (simpler_crawler_data) {
                        callback(0, ret_data, '获取成功!');
                        return true;
                    }
                    //发起请求在请求内容中提取图片
                    getTmallDesPic(callback, ret_data, des_url);
                });
            });
        }else if(KISSY.version == '1.48')
        {
            var api_url = 'https://h5api.m.tmall.com/h5/mtop.taobao.pcdetail.data.get/1.0/';
            BAYHELPER_INIT.request({
                action: 'getCookie',
                host: api_url
            }, function (response) {
                if (typeof response.cookies !== 'undefined' && response.cookies) {
                    var cookieArr = response.cookies.split(';');
                    var cookiesMatch = '';
                    for (var i in cookieArr) {
                        if (typeof cookieArr[i] == 'string') {
                            var splitArr = cookieArr[i].split('=');
                            if (splitArr[0] == '_m_h5_tk') {
                                var cookiesMatch = splitArr;
                                break;
                            }
                        }
                    }
                    if (cookiesMatch)
                    {
                        var url = location.search;   //获取url中"?"符后的字串
                        var theRequest = new Object();
                        var str = '';
                        if (url.indexOf("?") != -1)
                        {
                            str = url.substr(1);
                            strs = str.split("&");
                            for (var i = 0; i < strs.length; i++)
                            {
                                theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                            }
                        }
                        var _m_h5_tk = cookiesMatch[1].split('_')[0];
                        var t = new Date().getTime();
                        var appKey = '12574478';
                        var data = JSON.stringify({
                            exParams: JSON.stringify({
                                id: theRequest.id,
                                spm: theRequest.spm,
                                queryParams: 'id='+theRequest.id+'&spm='+theRequest.spm,
                            }),
                            detail_v: "3.3.2",
                            id: theRequest.id
                        });
                        var paramStr = _m_h5_tk + '&' + t + '&' + appKey + '&' + data;
                        var queryParam = {
                            'jsv': '2.4.11',
                            'appKey': appKey,
                            't': t,
                            'sign': hex_md5(paramStr), // 获取sign,md5加密
                            'api': 'mtop.taobao.pcdetail.data.get',
                            'v': '1.0',
                            'type': 'jsonp',
                            'isSec': 0,
                            'timeout': 10000,
                            'ttid': '2022@taobao_litepc_9.17.0',
                            'AntiFlood': true,
                            'AntiCreep': true,
                            'dataType': 'jsonp',
                            'data': data
                        };
                        ajax({
                            url: api_url,
                            data: queryParam,
                            dataType: 'jsonp',
                            success: function (res) {
                                console.log(res);
                                if (typeof res.data == 'undefined' || typeof res.data.item == 'undefined') {
                                    callback(-1, {}, '获取数据失败, 请记录当前链接联系开发人员');
                                    return false;
                                }
                                var data = res.data;
                                let name = data.item.title;
                                let pdt_picture = data.item.images;
                                let multi_sku = 0;
                                let skuMap = data.skuBase.skus;
                                let skuCoreMap = data.skuCore.sku2info;
                                let skuItem = data.skuCore.skuItem;
                                let skuProps = data.skuBase.props;
                                let sku = {};
                                let attr = {};
                                let attr_uid = {};
                                let attrId,attrName,attrValues,attrImage;
                                for (var i in skuProps) {
                                    let attrValueArr = {};
                                    attrId = skuProps[i]['pid'];
                                    attrName = skuProps[i]['name'];
                                    attrValues = skuProps[i]['values'];
                                    for (var j in attrValues) {
                                        var attrValueId = attrValues[j]['vid'];
                                        attrImage = '';
                                        if (skuProps[i]['hasImage'] === 'true') {
                                            attrImage = attrValues[j]['image'];
                                        }
                                        attrValueArr[attrValueId] = {
                                            name: attrValues[j]['name'],
                                            img: attrImage,
                                            sort: attrValues[j]['sortOrder']
                                        };
                                    }
                                    attr[attrId] = {
                                        attrName: attrName,
                                        attrValue: attrValueArr
                                    };
                                }
                                attr_uid = generalUniqueAttrId(attr);
                                if (skuMap && skuMap.length > 0) {
                                    multi_sku = 1;
                                    for (let i in skuMap) {
                                        let pvs = {};
                                        let sku_img = '';
                                        let sort;
                                        let stock = 0, price = 0;
                                        let attr_arr = skuMap[i]['propPath'].split(';');
                                        for (let i in attr_arr) {
                                            let attr_item = attr_arr[i].split(':');
                                            if (!attr[attr_item[0]] || !attr[attr_item[0]]['attrValue'][attr_item[1]]) {
                                                continue;
                                            }
                                            if (attr[attr_item[0]]['attrValue'][attr_item[1]].img) {
                                                sku_img = attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                                            }
                                            sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                                            let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                                            pvs[attr[attr_item[0]].attrName] = {
                                                attr_id: attr_uid['attr_value'][text],
                                                text: text,
                                                img: sku_img,
                                                sort: sort
                                            };
                                        }
                                        let skuId = skuMap[i]['skuId'];
                                        stock = skuCoreMap[skuId]['quantity'];
                                        price = skuCoreMap[skuId]['price']['priceText'];
                                        sku[skuId] = {
                                            pvs: pvs,
                                            price: price,
                                            stock: stock,
                                            sku_img: sku_img
                                        };
                                    }
                                } else if (skuItem.renderSku === 'false'){
                                    sku = {
                                        price: skuCoreMap[0].price.priceText,
                                        stock: skuCoreMap[0].quantity,
                                    };
                                }
                                let ret_data = {
                                    sku: sku,
                                    attr: attr,
                                    name: name,
                                    pdt_picture: pdt_picture,
                                    multi_sku: multi_sku,
                                    product_url: location.href,
                                    item_id: BAYHELPER_INIT.isItemPage(),
                                    attr_uid: attr_uid,
                                    pdt_video: ''
                                };
                                let supplier_data = getTmallSupplier(data);
                                for (let i in supplier_data) {
                                    ret_data[i] = supplier_data[i];
                                }
                                //tmall服务分
                                getTmallService(ret_data, function (ret_data) {
                                    if (simpler_crawler_data) {
                                        callback(0, ret_data, '获取成功!');
                                        return true;
                                    }
                                    // //发起请求在请求内容中提取图片
                                    var count = 0;
                                    var descObj;
                                    let desc_picture = [];
                                    var interval = setInterval(function () {
                                        descObj = document.querySelectorAll('.desc-root .descV8-container img');
                                        if (descObj.length == 0) {
                                            descObj = document.querySelectorAll('#description .content img');
                                        }
                                        if (descObj || count > 10) {
                                            clearInterval(interval);
                                            for (let i = 0; i<descObj.length; i++) {
                                                var imgSrc = descObj[i].getAttribute('data-src');
                                                if (!imgSrc) {
                                                    imgSrc = descObj[i].getAttribute('src');
                                                }
                                                if (imgSrc) {
                                                    desc_picture.push(imgSrc);
                                                }
                                            }
                                            ret_data['des_picture'] = desc_picture;
                                            callback(0, ret_data, '获取成功!')
                                        }
                                        count++;
                                    }, 500);
                                }, data);
                            },
                            error: function (error) {
                                callback(-1, {}, '获取数据失败, 请记录当前链接联系开发人员');
                                return false;
                            }
                        });
                    } else {
                        callback(-1, {}, '没有获取到天猫有效cookie，请求失败!');
                        return false;
                    }
                }

            });

        }else{
            callback(-1, {}, '天猫页面版本更新，获取数据失败!');
            return false;
        }
    }else if (typeof window.detailRequest != 'undefined')
    {
        window.detailRequest.then(function(data){
            let name = data.item.title;
            let pdt_picture = data.item.images;
            let multi_sku = 0;
            let skuMap = data.skuBase.skus;
            let skuCoreMap = data.skuCore.sku2info;
            let skuItem = data.skuCore.skuItem;
            let skuProps = data.skuBase.props;
            let sku = {};
            let attr = {};
            let attr_uid = {};
            let attrId,attrName,attrValues,attrImage;
            for (var i in skuProps) {
                let attrValueArr = {};
                attrId = skuProps[i]['pid'];
                attrName = skuProps[i]['name'];
                attrValues = skuProps[i]['values'];
                for (var j in attrValues) {
                    var attrValueId = attrValues[j]['vid'];
                    attrImage = '';
                    if (skuProps[i]['hasImage'] === 'true') {
                        attrImage = attrValues[j]['image'];
                    }
                    attrValueArr[attrValueId] = {
                        name: attrValues[j]['name'],
                        img: attrImage,
                        sort: attrValues[j]['sortOrder']
                    };
                }
                attr[attrId] = {
                    attrName: attrName,
                    attrValue: attrValueArr
                };
            }
            attr_uid = generalUniqueAttrId(attr);
            if (skuMap && skuMap.length > 0) {
                multi_sku = 1;
                for (let i in skuMap) {
                    let pvs = {};
                    let sku_img = '';
                    let sort;
                    let stock = 0, price = 0;
                    let attr_arr = skuMap[i]['propPath'].split(';');
                    for (let i in attr_arr) {
                        let attr_item = attr_arr[i].split(':');
                        if (!attr[attr_item[0]] || !attr[attr_item[0]]['attrValue'][attr_item[1]]) {
                            continue;
                        }
                        if (attr[attr_item[0]]['attrValue'][attr_item[1]].img) {
                            sku_img = attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                        }
                        sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                        let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                        pvs[attr[attr_item[0]].attrName] = {
                            attr_id: attr_uid['attr_value'][text],
                            text: text,
                            img: sku_img,
                            sort: sort
                        };
                    }
                    let skuId = skuMap[i]['skuId'];
                    stock = skuCoreMap[skuId]['quantity'];
                    price = skuCoreMap[skuId]['price']['priceText'];
                    sku[skuId] = {
                        pvs: pvs,
                        price: price,
                        stock: stock,
                        sku_img: sku_img
                    };
                }
            } else if (skuItem.renderSku === 'false'){
                sku = {
                    price: skuCoreMap[0].price.priceText,
                    stock: skuCoreMap[0].quantity,
                };
            }
            let ret_data = {
                sku: sku,
                attr: attr,
                name: name,
                pdt_picture: pdt_picture,
                multi_sku: multi_sku,
                product_url: location.href,
                item_id: BAYHELPER_INIT.isItemPage(),
                attr_uid: attr_uid,
                pdt_video: ''
            };
            let supplier_data = getTmallSupplier(data);
            for (let i in supplier_data) {
                ret_data[i] = supplier_data[i];
            }
            //tmall服务分
            getTmallService(ret_data, function (ret_data) {
                if (simpler_crawler_data) {
                    callback(0, ret_data, '获取成功!');
                    return true;
                }
                // //发起请求在请求内容中提取图片
                var count = 0;
                var descObj;
                let desc_picture = [];
                var interval = setInterval(function () {
                    descObj = document.querySelectorAll('.desc-root .descV8-container img');
                    if (descObj.length==0) {
                        descObj = document.querySelectorAll('#description .content img');
                    }
                    if (descObj || count > 10) {
                        clearInterval(interval);
                        for (let i = 0; i<descObj.length; i++) {
                            var imgSrc = descObj[i].getAttribute('data-src');
                            if (!imgSrc) {
                                imgSrc = descObj[i].getAttribute('src');
                            }
                            if (imgSrc) {
                                desc_picture.push(imgSrc);
                            }
                        }
                        ret_data['des_picture'] = desc_picture;
                        callback(0, ret_data, '获取成功!')
                    }
                    count++;
                }, 500);
            }, data);
        });
    }else
    {
        if(retry>4){
            callback(-1, {}, '插件无法抓取到产品数据，请带上当前产品链接联系开发人员!');
            return false;
        }
        retry++;
        setTimeout(function(){
           getTmall(callback,retry);
        },500);
    }
}

function getTmallTShopSetup(callback) {
    let returnData = {};
    let obj = document.querySelectorAll('script');
    let reg = /TShop\.Setup\(\n.*/;
    let data;
    for (let i = 0; i < obj.length; i++) {
        let text = obj[i].innerText;
        if (text.indexOf('TShop.Setup') >= 0) {
            text = text.match(reg);
            if (text[0]) {
                text = text[0].replace('TShop.Setup(\n\t  \t', '');
                if (text) {
                    data = JSON.parse(text);
                }
            }
            break;
        }
    }
    if (!data) {
        callback(-1, {}, '获取数据失败!');
        return false;
    }
    let attr = {},
        attrValue = {},
        attrValueImg = {};
    obj = document.querySelectorAll('dl.tm-sale-prop');
    reg = /background:url\(.*\)/;
    for (let i = 0; i < obj.length; i++) {
        const title = obj[i].querySelector('dt.tb-metatit');
        if (title) {
            const attrValueObj = obj[i].querySelectorAll('ul li');
            for (let j = 0; j < attrValueObj.length; j++) {
                const attrData = attrValueObj[j].getAttribute('data-value').split(':');
                if (j === 0) {
                    attr[attrData[0]] = {};
                    attr[attrData[0]].attrName = title.innerText;
                    attr[attrData[0]].attrValue = {};
                }
                attrValue[attrData[1]] = attrValueObj[j].innerText;
                //获取属性图片图片
                const style = attrValueObj[j].querySelector("a").getAttribute('style');
                if (style) {
                    attrValueImg[attrData[1]] = style.match(reg)[0].replace('background:url(', '').replace(')', '');
                } else {
                    attrValueImg[attrData[1]] = '';
                }
                attr[attrData[0]].attrValue[attrData[1]] = {
                    img: attrValueImg[attrData[1]],
                    name: attrValue[attrData[1]]
                };
            }
        }
    }
    returnData.attr = attr;
    //有sku
    let sku = {};
    if (data.itemDO.hasSku) {
        returnData.multi_sku = 1;
        const skuData = data.valItemInfo.skuMap;
        for (let i in skuData) {
            sku[skuData[i].skuId] = skuData[i];
            const attrData = i.split(';');
            let pvs = {};
            for (let j = 0; j < attrData.length; j++) {
                if (attrData[j]) {
                    const attribute = attrData[j].split(':');
                    pvs[attr[attribute[0]].attrName] = {
                        attr_id: attribute[1],
                        img: attrValueImg[attribute[1]],
                        text: attrValue[attribute[1]]
                    }
                }
            }
            sku[skuData[i].skuId].pvs = pvs;
        }
    } else {
        returnData.multi_sku = 0;
        //单个sku
        sku.price = 0;
        const priceObj = document.querySelector('#J_PromoPrice .tm-price');
        if (priceObj) {
            sku.price = priceObj.innerText;
        }
        sku.stock = 0;
        const stockObj = document.querySelector('#J_EmStock');
        if (stockObj) {
            sku.stock = stockObj.innerText.replace('库存', '').replace('件', '');
        }
    }
    returnData.sku = sku;
    returnData.item_id = data.itemDO.itemId;
    returnData.spu_id = data.itemDO.spuId;
    returnData.name = data.itemDO.title;
    returnData.cate_id = data.itemDO.categoryId;
    returnData.product_url = location.href;
    returnData.attr_uid = generalUniqueAttrId(attr);
    returnData.pdt_video = '';
    const supplier_data = getTmallSupplier();
    for (let i in supplier_data) {
        returnData[i] = supplier_data[i];
    }
    getTmallService(returnData, function (returnData) {
        if (simpler_crawler_data) {
            callback(0, ret_data, '获取成功!');
            return true;
        }
        if (data.propertyPics && data.propertyPics.default) {
            returnData.pdt_picture = data.propertyPics.default;
        } else {
            obj = document.querySelectorAll('#J_UlThumb li');
            returnData.pdt_picture = [];
            for (let i = 0; i < obj.length; i++) {
                returnData.pdt_picture.push(obj[i].querySelector('img').src.replace('_60x60q90.jpg', ''));
            }
        }
        getTmallDesPic(callback, returnData, data.api.descUrl);
    });
}

function getTmallService(data, callback, detailData) {
    let obj;
    //服务分
    let service = {};
    if (typeof detailData!='undefined') {
        var serviceList = detailData.seller.evaluates;
        for (var i in serviceList) {
            service[serviceList[i]['title']] = serviceList[i]['score'].replace(' ' , '');
        }
        data.service = service;
        //销量
        data.sold_total = 0;
        if (typeof detailData['item'] != 'undefined' && detailData.item.vagueSellCount) {
            data.sold_total = detailData.item.vagueSellCount.replace('+', '');
        }
        //评价数量
        data.review_total = 0;
        if (callback) {
            callback(data);
        }
    } else {
        const serviceobj = document.querySelectorAll('#shop-info .shopdsr-item');
        if (serviceobj.length > 0) {
            for (var i in serviceobj) {
                if (typeof serviceobj[i].getElementsByClassName == 'function') {
                    var key = serviceobj[i].getElementsByClassName('shopdsr-title')[0].innerText.replace(' ','');
                    var value = serviceobj[i].getElementsByClassName('shopdsr-score')[0].innerText;
                    service[key] = value;
                }
            }
        }
        data.service = service;
        //销量
        data.sold_total = 0;
        obj = document.querySelector('.tm-ind-sellCount .tm-count');
        if (obj) {
            data.sold_total = obj.innerText;
        }
        //评价数量
        data.review_total = 0;
        obj = document.querySelector('.tm-ind-reviewCount .tm-count');
        if (obj) {
            data.review_total = obj.innerText;
            if (callback) {
                callback(data);
            }
        } else {
            if (callback) {
                var count = 0;
                var interval = setInterval(function () {
                    obj = document.querySelector('.tm-ind-reviewCount .tm-count');
                    if (obj || count > 10) {
                        clearInterval(interval);
                        if (obj) {
                            data.review_total = obj.innerText;
                        }
                        callback(data);
                    }
                    count++;
                }, 500);
            }
        }
    }
    return data;
}

//获取产品标题
function get1688ProductName() {
    var name = '';
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA !== 'undefined') {
        // 新版页面
        if (window.__GLOBAL_DATA && window.__GLOBAL_DATA.tempModel) {
            name = window.__GLOBAL_DATA.tempModel.offerTitle;
        }
    }
    if (name == '') {
        var obj = [
            document.querySelector('#mod-detail-title h1'),
            document.querySelector('.title-text'),
        ];
        for (var k in obj) {
            if (obj[k] != null) {
                return obj[k].innerText;
            }
        }
    }
    return name;
}

function getTaobaoProductName() {
    var obj = document.getElementById('J_Title');
    if (obj === null) {
        return '';
    }
    return obj.getElementsByTagName('h3')[0].innerText;
}

function getTmallProductName() {
    var obj = document.getElementById('J_DetailMeta');
    if (obj === null) {
        return '';
    }
    obj = obj.getElementsByClassName('tb-detail-hd')[0];
    return obj.getElementsByTagName('h1')[0].innerText;
}

//获取产品图片
function get1688PdtPicture() {
    var pic = [];
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA !== 'undefined') {
        if (typeof window.__INIT_DATA.globalData !== 'undefined' && typeof window.__INIT_DATA.globalData.images !== 'undefined') {
            for (var i in window.__INIT_DATA.globalData.images) {
                if (typeof window.__INIT_DATA.globalData.images[i].fullPathImageURI !== 'undefined' && window.__INIT_DATA.globalData.images[i].fullPathImageURI) {
                    pic.push(window.__INIT_DATA.globalData.images[i].fullPathImageURI);
                }
            }
        }
    }
    if (pic.length > 0) {
        return pic;
    }
    var obj = document.querySelectorAll('#dt-tab li');
    if (obj.length > 0) {
        for (var i = 0; i < obj.length; i++) {
            var imgdata = obj[i].getAttribute('data-imgs');
            if (imgdata) {
                imgdata = JSON.parse(imgdata);
                pic.push(imgdata.original);
            }
        }
    } else {
        obj = document.querySelectorAll('.img-list-wrapper img.detail-gallery-img');
        if (obj.length > 0) {
            for (var i = 0; i < obj.length; i++) {
                var imgdata = obj[i].getAttribute('src');
                if (imgdata) {
                    pic.push(imgdata);
                }
            }
        }
    }
    return pic;
}
// 获取1688窗橱视频
function get1688PdtVideo() {
    var path = '';
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA !== 'undefined') {
        //新版页面
        if (typeof window.__GLOBAL_DATA.offerDomain !== 'undefined') {
            var offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
            var videoUrls = offerDomain.offerDetail.wirelessVideo.videoUrls;
            for (let i in videoUrls) {
                if (videoUrls[i].length) {
                    path = videoUrls[i];
                    break;
                }
            }
        }
    }
    if (path === '') {
        var obj = document.querySelector('#detail-main-video-content');
        if (obj === null) {
            return path;
        }
        obj = obj.getElementsByTagName('source').length > 0 ? obj.getElementsByTagName('source') : obj.getElementsByTagName('video');
        if (obj.length > 0) {
            path = obj[0].getAttribute('src');
        }
    }
    return path;
}

function getTaobaoPdtPicture() {
    var pic = [];
    var obj = document.getElementById('J_UlThumb');
    if (obj === null) {
        return pic;
    }
    obj = obj.getElementsByTagName('li');
    for (var i = 0; i < obj.length; i++) {
        // var imgdata = obj[i].getElementsByTagName('img')[0].src.replace('_50x50.jpg_.webp', '');
        // pic.push(imgdata);
        imgdata = obj[i].getElementsByTagName('img')[0].getAttribute('data-src');
        if (imgdata) {
            imgdata = imgdata.replace('_50x50.jpg', '');
            pic.push(imgdata);
        }
    }
    return pic;
}

function getTmallPdtPicture() {
    var pic = [];
    var obj = document.getElementById('J_UlThumb');
    if (obj === null) {
        return pic;
    }
    obj = obj.getElementsByTagName('li');
    for (var i = 0; i < obj.length; i++) {
        var imgdata = obj[i].getElementsByTagName('img')[0].src.replace('_60x60q90.jpg', '');
        pic.push(imgdata);
    }
    return pic;
}

function getTaobaoSupplier() {
    var shop_name = g_config.shopName;
    var shop_id = g_config.shopId;
    var shop_url = g_config.idata.shop.url.replace(/\/$/, '');
    var cate_id = g_config.idata.item.cid;
    var im = g_config.sellerNick;
    var item_no = '';
    var obj = document.querySelectorAll('ul.attributes-list>li');
    for (var i = 0; i < obj.length; i++) {
        var flag = obj[i].innerText.substr(0, 2);
        if (flag == '型号' || flag == '货号') {
            item_no = obj[i].getAttribute('title');
        }
    }
    if (shop_url) {
        if (shop_url.substring(0, 2) == '//') {
            shop_url = 'https:' + shop_url;
        }
        var rst = shop_url.match(/^https\:\/\/(.+)\.(taobao|tmall)\.com(?:.)*/i);
        var invalid_domain = ['www'];
        if (!rst || invalid_domain.indexOf(rst[1]) !== -1) {
            return false;
        }
        //手机端链接
        if (shop_url.match(/^https\:\/\/(.*\.)?m\.(.+)\.(taobao|tmall)\.com(?:.)*/i)) {
            return false;
        }
    }

    return {
        shop_name: shop_name,
        shop_id: shop_id,
        shop_url: shop_url,
        im: im,
        cate_id: cate_id,
        item_no: item_no
    };
}

function getTmallSupplier(detailData) {
    if (typeof detailData !== 'undefined') {
        var shop_id = detailData.seller.shopId;
        var shop_name = detailData.seller.shopName;
        var shop_url = detailData.seller.pcShopUrl;
        var im = '';
        var cate_id = '';
        if(typeof detailData.pcTrade != 'undefined'){
            if(typeof detailData.pcTrade.pcBuyParams != 'undefined'){
                if(typeof detailData.pcTrade.pcBuyParams.seller_nickname != 'undefined'){
                    im = decodeURI(detailData.pcTrade.pcBuyParams.seller_nickname);
                }
                if(typeof detailData.pcTrade.pcBuyParams.rootCatId != 'undefined'){
                    cate_id = detailData.pcTrade.pcBuyParams.rootCatId;
                }
            }
        }
        var item_no = '';
        if(typeof detailData.componentsVO != 'undefined' && typeof detailData.componentsVO.extensionInfoVO != 'undefined' && typeof detailData.componentsVO.extensionInfoVO.infos != 'undefined'){
            var infoObj = detailData.componentsVO.extensionInfoVO.infos;
            for (var i in infoObj) {
                if (infoObj[i]['title'] == '参数') {
                    for (var j in infoObj[i]['items']) {
                        if (infoObj[i]['items'][j]['title'] == '货号' || infoObj[i]['items'][j]['title'] == '型号号') {
                            item_no = infoObj[i]['items'][j]['text'].join(',');
                        }
                    }
                }
            }
        }
    } else {
        var shopobj = document.querySelector('a.slogo-shopname>strong');
        if (shopobj) {
            var shop_name = shopobj.innerText;
        }
        var shop_id = g_config.shopId;
        var shop_url = g_config.shopUrl;
        var cate_id = g_config.categoryId;
        var im = decodeURI(g_config.sellerNickName);
        var item_no = '';
        var obj = document.querySelectorAll('ul#J_AttrUL>li');
        for (var i = 0; i < obj.length; i++) {
            var flag = obj[i].innerText.substr(0, 2);
            if (flag == '型号' || flag == '货号') {
                item_no = obj[i].getAttribute('title').replace(/(^\s*)|(\s*$)/gi, '');
            }
        }
    }
    return {
        shop_name: shop_name,
        shop_id: shop_id,
        shop_url: shop_url,
        im: im,
        cate_id: cate_id,
        item_no: item_no
    };
}

function get1688Supplier(callback) {
    var shop_name = '',
        shop_id = '', shop_url = '', cate_id = '', im = '', item_no = '';
    if (typeof iDetailConfig != 'undefined') {
        if (document.querySelectorAll('meta[property="og:product:nick"]').length > 0) {
            var text = document.querySelectorAll('meta[property="og:product:nick"]')[0].content;
            shop_name = text.match(/name=(.*);/)[1];
        }
        shop_id = iDetailConfig.memberid;
        shop_url = iDetailConfig.companySiteLink;
        cate_id = iDetailConfig.catid;
        im = iDetailConfig.loginId;
        var obj = document.querySelectorAll('.de-feature.de-feature-key');
        for (var i = 0; i < obj.length; i++) {
            var flag = obj[i].innerText.substr(0, 2);
            if (flag == '型号' || flag == '货号') {
                item_no = obj[i].nextElementSibling.getAttribute('title');
            }
        }
    } else if (typeof window.__GLOBAL_DATA != 'undefined') {
        // var shop_name,shop_id,shop_url,im,cate_id,item_no;
        if (typeof window.__GLOBAL_DATA.offerDomain !== 'undefined') {
            var globalData = JSON.parse(window.__GLOBAL_DATA.offerDomain);
            if (typeof globalData.offerDetail != 'undefined') {
                cate_id = globalData.offerDetail.leafCategoryId;
                if (typeof globalData.offerDetail.featureAttributes) {
                    for (var i in globalData.offerDetail.featureAttributes) {
                        var flagName = globalData.offerDetail.featureAttributes[i].name;
                        if (flagName == '型号' || flagName == '货号') {
                            item_no = globalData.offerDetail.featureAttributes[i].values.join(',');
                            break;
                        }
                    }
                }
            }
            if (typeof globalData.sellerModel != 'undefined') {
                shop_name = globalData.sellerModel.companyName;
                shop_id = globalData.sellerModel.memberId;
                shop_url = globalData.sellerModel.winportUrl;
                im = globalData.sellerModel.loginId;
            }
        } else if (typeof window.__GLOBAL_DATA.tempModel != 'undefined') {
            if (typeof window.__GLOBAL_DATA.tempModel.postCategoryId != 'undefined') {
                cate_id = window.__GLOBAL_DATA.tempModel.postCategoryId;
            }
            if (typeof window.__GLOBAL_DATA.tempModel.companyName != 'undefined') {
                shop_name = window.__GLOBAL_DATA.tempModel.companyName;
            }
            if (typeof window.__GLOBAL_DATA.tempModel.sellerMemberId != 'undefined') {
                shop_id = window.__GLOBAL_DATA.tempModel.sellerMemberId;
            }
            if (typeof window.__GLOBAL_DATA.offerBaseInfo != 'undefined' && typeof window.__GLOBAL_DATA.offerBaseInfo.sellerWinportUrl != 'undefined') {
                shop_url = window.__GLOBAL_DATA.offerBaseInfo.sellerWinportUrl;
            } else if (typeof window.__GLOBAL_DATA.tempModel.winportUrl != 'undefined') {
                shop_url = window.__GLOBAL_DATA.tempModel.winportUrl;
            }
            if (typeof window.__GLOBAL_DATA.tempModel.sellerLoginId != 'undefined') {
                im = window.__GLOBAL_DATA.tempModel.sellerLoginId;
            }
        }
        if (item_no === '') {
            var obj = document.querySelectorAll('.offer-attr-item');
            for (var i = 0; i < obj.length; i++) {
                let tempObj = obj[i].querySelector('.offer-attr-item-name');
                if (!tempObj) {
                    continue;
                }
                var flag = tempObj.innerText.substr(0, 2);
                if (flag == '型号' || flag == '货号') {
                    tempObj = obj[i].querySelector('.offer-attr-item-value');
                    if (tempObj) {
                        item_no = tempObj.innerText;
                    }
                }
            }
        }
    }

    if (!shop_url) {
        callback(-1, {}, '获取店铺地址失败, 请联系技术人员.');
    }
    var rst = shop_url.match(/^https\:\/\/(.+)\.1688\.com(?:.)*/i);
    var invalid_domain = ['winport.m', 'www'];
    if (!rst || invalid_domain.indexOf(rst[1]) !== -1) {
        callback(-1, {}, '获取店铺地址失败, 请带上链接联系技术人员.');
        return false;
    }
    // 如果 rst[1] 中有.cn结尾的, 则替换.cn为空字符串
    var res = rst[1].match(/([^\.]+)\.cn$/);
    if (res) {
        shop_url = shop_url.replace(res[0], res[1]);
    }

    return {
        shop_name: shop_name,
        shop_id: shop_id,
        shop_url: shop_url,
        im: im,
        cate_id: cate_id,
        item_no: item_no
    };
}

function get1688ContactMember(url, callback) {
    if (!url) {
        callback('', '');
        return false;
    }
    if (url.match(/(http[s]{0,1}:)?\/\/$/i)) {
        callback('', '');
        return false;
    }
    if (url.substring(0, 2) == '//') {
        url = 'https:' + url;
    }
    url = url + '/page/creditdetail.htm';
    BAYHELPER_INIT.request({
        action: 'requestChannel',
        type: 'GET',
        value: url,
        dataType: 'html'
    }, function (response) {
        let contact = '';
        let mobile = '';
        if (typeof response.code != 'undefined' && response.code == -1) {
            callback(contact, mobile);
            return false;
        }
        if (typeof response == 'object') {
            callback(contact, mobile);
            return false;
        }
        var nameMatch = response.match(/contactList\:\[([\s\r\n]+)\{name\:"([^"]+)"\}/);
        if (nameMatch) {
            contact = nameMatch[2];
        }
        var mobileMatch = response.match(/<input type="hidden" value="(\d{11})" name="hiddenMobileNo"/);
        if (mobileMatch) {
            mobile = mobileMatch[1];
        }
        callback(contact, mobile);
        return false;
    });
}

function ajax(params) {
    params = params || {};
    params.data = params.data || {};
    var json = params.dataType === 'jsonp' ? jsonp(params) : json(params);
    // jsonp请求
    function jsonp(params) {
        //创建script标签并加入到页面中
        var callbackName = params.jsonp ? params.jsonp : 'jsonp_' + random();
        var head = document.getElementsByTagName('head')[0];
        // 设置传递给后台的回调参数名
        params.data['callback'] = callbackName;
        var data = formatParams(params.data);
        var script = document.createElement('script');
        head.appendChild(script);
        //创建jsonp回调函数
        window[callbackName] = function (json) {
            head.removeChild(script);
            clearTimeout(script.timer);
            window[callbackName] = null;
            params.success && params.success(json);
        };
        //发送请求
        script.src = params.url + '?' + data;
        //为了得知此次请求是否成功，设置超时处理
        if (params.time) {
            script.timer = setTimeout(function () {
                window[callbackName] = null;
                head.removeChild(script);
                params.error && params.error({
                    message: '超时'
                });
            }, time);
        }
    };
    //普通json请求
    function json(params) {
        params.type = (params.type || 'GET').toUpperCase();
        params.data = formatParams(params.data);
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                var status = xhr.status;
                if (status >= 200 && status < 300) {
                    var response = '';
                    var type = xhr.getResponseHeader('Content-type');
                    if (type.indexOf('xml') !== -1 && xhr.responseXML) {
                        response = xhr.responseXML; //Document对象响应
                    } else if (type === 'application/json') {
                        response = JSON.parse(xhr.responseText); //JSON响应
                    } else {
                        response = xhr.responseText; //字符串响应
                    };
                    params.success && params.success(response);
                } else {
                    params.error && params.error(status);
                }
            };
        };
        //请求方式，默认是GET
        if (params.type == 'GET') {
            xhr.open(params.type, params.url + '?' + params.data, true);
            xhr.send(null);
        } else {
            xhr.open(params.type, params.url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.send(params.data);
        }
    }
    //格式化参数方法
    function formatParams(data) {
        var arr = [];
        for (var name in data) {
            arr.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
        };
        // 添加一个随机数，防止缓存
        arr.push('v=' + random());
        return arr.join('&');
    }
    // 获取随机数方法 
    function random() {
        return Math.floor(Math.random() * 10000 + 500);
    }
}

function getTaobaoDesPic(callback, data) {
    var des_picture = [];
    var des_url = g_config.descUrl;
    if (des_url && typeof desc == 'undefined') {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.src = des_url;
        script.type = 'text/javascript';
        script.charset = 'utf-8';
        head.appendChild(script);
        script.onload = script.onreadystatechange = function () {
            //taobao 将数据存到desc变量中 js来引入防止跨域
            if (typeof desc !== 'undefined') {
                var des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
                if (des_pic_craw) {
                    for (let i = 0; i < des_pic_craw.length; i++) {
                        var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                        if (filterIgnoreDesPic(src)) {
                            des_picture.push(src);
                        }
                    }
                }
            }
            data['des_picture'] = des_picture;
            callback(0, data, '获取成功!')
        };
        //延时触发
        setTimeout(function () {
            if (des_picture.length == 0) {
                data['des_picture'] = des_picture;
                callback(0, data, '获取成功!')
            }
        }, 2000);
    } else {
        //已经加载过 不重复加载
        if (desc) {
            var des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            if (des_pic_craw) {
                for (let i = 0; i < des_pic_craw.length; i++) {
                    var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                    if (filterIgnoreDesPic(src)) {
                        des_picture.push(src);
                    }
                }
            }
            data['des_picture'] = des_picture;
        }
        data['des_picture'] = des_picture;
        callback(0, data, '获取成功!');
    }
}

function getTmallDesPic(callback, data, des_url) {
    var des_picture = [];
    if (des_url && typeof desc == 'undefined') {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.src = des_url;
        script.type = 'text/javascript';
        script.charset = 'utf-8';
        head.appendChild(script);
        script.onload = script.onreadystatechange = function () {
            //天猫 将数据存到desc变量中 js来引入防止跨域
            var des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            if (des_pic_craw) {
                for (let i = 0; i < des_pic_craw.length; i++) {
                    var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                    if (filterIgnoreDesPic(src)) {
                        des_picture.push(src);
                    }
                }
            }
            data['des_picture'] = des_picture;
            callback(0, data, '获取成功!')
        };
    } else {
        //已经加载过 不重复加载
        if (desc) {
            var des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            if (des_pic_craw) {
                for (let i = 0; i < des_pic_craw.length; i++) {
                    var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                    if (filterIgnoreDesPic(src)) {
                        des_picture.push(src);
                    }
                }
            }
            data['des_picture'] = des_picture;
        }
        data['des_picture'] = des_picture;
        callback(0, data, '获取成功!')
    }
}

function get1688DesPic(callback, data) {
    if (simpler_crawler_data) {
        callback(0, data, '获取成功!')
        return true;
    }
    var des_picture = [];
    var des_url = '';
    if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA !== 'undefined') {
        //新版页面
        if (typeof window.__GLOBAL_DATA.offerDomain !== 'undefined') {
            var offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
            des_url = offerDomain.offerDetail.detailUrl;
        }
    } else {
        des_url = document.getElementById('desc-lazyload-container') != null ? document.getElementById('desc-lazyload-container').getAttribute('data-tfs-url') : '';
    }
    if (des_url && typeof offer_details == 'undefined') {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.src = des_url;
        script.type = 'text/javascript';
        script.charset = 'utf-8';
        head.appendChild(script);
        script.onload = script.onreadystatechange = function () {
            if (typeof offer_details == 'undefined') {
                if (typeof desc != 'undefined') {
                    offer_details = {};
                    offer_details.content = desc;
                }
            }
            if (typeof offer_details !== 'undefined') {
                var des_pic_craw = offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
                if (des_pic_craw) {
                    for (let i = 0; i < des_pic_craw.length; i++) {
                        var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                        if (filterIgnoreDesPic(src)) {
                            des_picture.push(src);
                        }
                    }
                }
            }
            data['des_picture'] = des_picture;
            data['pdt_video'] = get1688PdtVideo();
            callback(0, data, '获取成功!')
        };
    } else {
        //已经加载过 不重复加载
        if (typeof offer_details == 'undefined') {
            if (typeof desc != 'undefined') {
                offer_details = {};
                offer_details.content = desc;
            }
        }
        if (typeof offer_details != 'undefined' && offer_details.content) {
            var des_pic_craw = offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            if (des_pic_craw) {
                for (let i = 0; i < des_pic_craw.length; i++) {
                    var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                    if (filterIgnoreDesPic(src)) {
                        des_picture.push(src);
                    }
                }
            }
            data['des_picture'] = des_picture;
        }
        data['des_picture'] = des_picture;
        data['pdt_video'] = get1688PdtVideo();
        callback(0, data, '获取成功!')
    }
}
//过滤一些占位图
function filterIgnoreDesPic(src) {
    // var ignore=["img.taobao.com","ma.m.1688.com","amos.alicdn.com","alisoft.com","add_to_favorites.htm","img.alicdn.com/NewGualianyingxiao","assets.alicdn.com/kissy/1.0.0/build/imglazyload/spaceball.gif"];
    var ignore = ["img.taobao.com", "ma.m.1688.com", "amos.alicdn.com", "alisoft.com", "add_to_favorites.htm", "img.alicdn.com/NewGualianyingxiao"];
    for (var i = 0; i < ignore.length; i++) {
        if (src.indexOf(ignore[i]) != -1) {
            return false;
        }
    }
    return true;

}

function isOffShelf1688(callback) {
    if (document.querySelectorAll('.mod-detail-offline-title').length > 0) {
        callback(-2, {}, '产品已下架');
        return true;
    } else {
        if (typeof window.__GLOBAL_DATA !== 'undefined' && typeof window.__GLOBAL_DATA.offerDomain !== 'undefined') {
            var offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
            if (typeof offerDomain.tradeModel != 'undefined' && typeof offerDomain.tradeModel.canBookedAmount != 'undefined') {
                if (offerDomain.tradeModel.canBookedAmount == 0) {
                    callback(-2, {}, '产品不可购买');
                    return true;
                }
            }
        }
        return false;
    }
}

function isOffShelfTaobao(callback) {
    if (document.querySelectorAll('.tb-off-sale').length > 0) {
        callback(-2, {}, '产品已下架');
        return true;
    } else {
        if (document.querySelectorAll('.tb-btn-buy .tb-disabled').length > 0) {
            callback(-3, {}, '产品不可购买');
            return true;
        }
        return false;
    }
}

function isOffShelfTmall(callback) {
    if (document.querySelectorAll('.sold-out-recommend').length > 0) {
        callback(-2, {}, '产品已下架');
        return true;
    }
    if (document.querySelectorAll('.tb-btn-wait').length > 0) {
        callback(-3, {}, '产品不可购买');
        return true;
    }
    if (document.querySelector('.errorDetail')) {
        callback(-5, {}, '产品不存在');
        return true;
    }
    return false;
}
// md5加密 - 1688请求时需要
function hex_md5(a) {
    function b(a, b) {
        return a << b | a >>> 32 - b
    }
    function c(a, b) {
        var c, d, e, f, g;
        return e = 2147483648 & a,
        f = 2147483648 & b,
        c = 1073741824 & a,
        d = 1073741824 & b,
        g = (1073741823 & a) + (1073741823 & b),
        c & d ? 2147483648 ^ g ^ e ^ f : c | d ? 1073741824 & g ? 3221225472 ^ g ^ e ^ f : 1073741824 ^ g ^ e ^ f : g ^ e ^ f
    }
    function d(a, b, c) {
        return a & b | ~a & c
    }
    function e(a, b, c) {
        return a & c | b & ~c
    }
    function f(a, b, c) {
        return a ^ b ^ c
    }
    function g(a, b, c) {
        return b ^ (a | ~c)
    }
    function h(a, e, f, g, h, i, j) {
        return a = c(a, c(c(d(e, f, g), h), j)),
        c(b(a, i), e)
    }
    function i(a, d, f, g, h, i, j) {
        return a = c(a, c(c(e(d, f, g), h), j)),
        c(b(a, i), d)
    }
    function j(a, d, e, g, h, i, j) {
        return a = c(a, c(c(f(d, e, g), h), j)),
        c(b(a, i), d)
    }
    function k(a, d, e, f, h, i, j) {
        return a = c(a, c(c(g(d, e, f), h), j)),
        c(b(a, i), d)
    }
    function l(a) {
        for (var b, c = a.length, d = c + 8, e = (d - d % 64) / 64, f = 16 * (e + 1), g = new Array(f - 1), h = 0, i = 0; c > i; )
            b = (i - i % 4) / 4,
            h = i % 4 * 8,
            g[b] = g[b] | a.charCodeAt(i) << h,
            i++;
        return b = (i - i % 4) / 4,
        h = i % 4 * 8,
        g[b] = g[b] | 128 << h,
        g[f - 2] = c << 3,
        g[f - 1] = c >>> 29,
        g
    }
    function m(a) {
        var b, c, d = "", e = "";
        for (c = 0; 3 >= c; c++)
            b = a >>> 8 * c & 255,
            e = "0" + b.toString(16),
            d += e.substr(e.length - 2, 2);
        return d
    }
    function n(a) {
        a = a.replace(/\r\n/g, "\n");
        for (var b = "", c = 0; c < a.length; c++) {
            var d = a.charCodeAt(c);
            128 > d ? b += String.fromCharCode(d) : d > 127 && 2048 > d ? (b += String.fromCharCode(d >> 6 | 192),
            b += String.fromCharCode(63 & d | 128)) : (b += String.fromCharCode(d >> 12 | 224),
            b += String.fromCharCode(d >> 6 & 63 | 128),
            b += String.fromCharCode(63 & d | 128))
        }
        return b
    }
    var o, p, q, r, s, t, u, v, w, x = [], y = 7, z = 12, A = 17, B = 22, C = 5, D = 9, E = 14, F = 20, G = 4, H = 11, I = 16, J = 23, K = 6, L = 10, M = 15, N = 21;
    for (a = n(a),
    x = l(a),
    t = 1732584193,
    u = 4023233417,
    v = 2562383102,
    w = 271733878,
    o = 0; o < x.length; o += 16)
        p = t,
        q = u,
        r = v,
        s = w,
        t = h(t, u, v, w, x[o + 0], y, 3614090360),
        w = h(w, t, u, v, x[o + 1], z, 3905402710),
        v = h(v, w, t, u, x[o + 2], A, 606105819),
        u = h(u, v, w, t, x[o + 3], B, 3250441966),
        t = h(t, u, v, w, x[o + 4], y, 4118548399),
        w = h(w, t, u, v, x[o + 5], z, 1200080426),
        v = h(v, w, t, u, x[o + 6], A, 2821735955),
        u = h(u, v, w, t, x[o + 7], B, 4249261313),
        t = h(t, u, v, w, x[o + 8], y, 1770035416),
        w = h(w, t, u, v, x[o + 9], z, 2336552879),
        v = h(v, w, t, u, x[o + 10], A, 4294925233),
        u = h(u, v, w, t, x[o + 11], B, 2304563134),
        t = h(t, u, v, w, x[o + 12], y, 1804603682),
        w = h(w, t, u, v, x[o + 13], z, 4254626195),
        v = h(v, w, t, u, x[o + 14], A, 2792965006),
        u = h(u, v, w, t, x[o + 15], B, 1236535329),
        t = i(t, u, v, w, x[o + 1], C, 4129170786),
        w = i(w, t, u, v, x[o + 6], D, 3225465664),
        v = i(v, w, t, u, x[o + 11], E, 643717713),
        u = i(u, v, w, t, x[o + 0], F, 3921069994),
        t = i(t, u, v, w, x[o + 5], C, 3593408605),
        w = i(w, t, u, v, x[o + 10], D, 38016083),
        v = i(v, w, t, u, x[o + 15], E, 3634488961),
        u = i(u, v, w, t, x[o + 4], F, 3889429448),
        t = i(t, u, v, w, x[o + 9], C, 568446438),
        w = i(w, t, u, v, x[o + 14], D, 3275163606),
        v = i(v, w, t, u, x[o + 3], E, 4107603335),
        u = i(u, v, w, t, x[o + 8], F, 1163531501),
        t = i(t, u, v, w, x[o + 13], C, 2850285829),
        w = i(w, t, u, v, x[o + 2], D, 4243563512),
        v = i(v, w, t, u, x[o + 7], E, 1735328473),
        u = i(u, v, w, t, x[o + 12], F, 2368359562),
        t = j(t, u, v, w, x[o + 5], G, 4294588738),
        w = j(w, t, u, v, x[o + 8], H, 2272392833),
        v = j(v, w, t, u, x[o + 11], I, 1839030562),
        u = j(u, v, w, t, x[o + 14], J, 4259657740),
        t = j(t, u, v, w, x[o + 1], G, 2763975236),
        w = j(w, t, u, v, x[o + 4], H, 1272893353),
        v = j(v, w, t, u, x[o + 7], I, 4139469664),
        u = j(u, v, w, t, x[o + 10], J, 3200236656),
        t = j(t, u, v, w, x[o + 13], G, 681279174),
        w = j(w, t, u, v, x[o + 0], H, 3936430074),
        v = j(v, w, t, u, x[o + 3], I, 3572445317),
        u = j(u, v, w, t, x[o + 6], J, 76029189),
        t = j(t, u, v, w, x[o + 9], G, 3654602809),
        w = j(w, t, u, v, x[o + 12], H, 3873151461),
        v = j(v, w, t, u, x[o + 15], I, 530742520),
        u = j(u, v, w, t, x[o + 2], J, 3299628645),
        t = k(t, u, v, w, x[o + 0], K, 4096336452),
        w = k(w, t, u, v, x[o + 7], L, 1126891415),
        v = k(v, w, t, u, x[o + 14], M, 2878612391),
        u = k(u, v, w, t, x[o + 5], N, 4237533241),
        t = k(t, u, v, w, x[o + 12], K, 1700485571),
        w = k(w, t, u, v, x[o + 3], L, 2399980690),
        v = k(v, w, t, u, x[o + 10], M, 4293915773),
        u = k(u, v, w, t, x[o + 1], N, 2240044497),
        t = k(t, u, v, w, x[o + 8], K, 1873313359),
        w = k(w, t, u, v, x[o + 15], L, 4264355552),
        v = k(v, w, t, u, x[o + 6], M, 2734768916),
        u = k(u, v, w, t, x[o + 13], N, 1309151649),
        t = k(t, u, v, w, x[o + 4], K, 4149444226),
        w = k(w, t, u, v, x[o + 11], L, 3174756917),
        v = k(v, w, t, u, x[o + 2], M, 718787259),
        u = k(u, v, w, t, x[o + 9], N, 3951481745),
        t = c(t, p),
        u = c(u, q),
        v = c(v, r),
        w = c(w, s);
    var O = m(t) + m(u) + m(v) + m(w);
    return O.toLowerCase()
}