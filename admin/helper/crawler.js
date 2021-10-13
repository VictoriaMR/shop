const CRAWLER = {
    getData: function(callback) {
        const domain = HELPERINIT.getDomain();
        switch (domain) {
            case '1688.com':
                this.get1688(callback);
                break;
            case 'taobao.com':
                this.getTaobao(callback);
                break;
            case 'tmall.com':
                this.getTmall(callback);
                break;
            default:
                callback({code:10001, data:false, message:'没有适配动作'});
                break;
        }
    },
    get1688: function(callback) {
        if (typeof iDetailData === 'undefined') {
            callback({code:10002, data:false, message:'获取数据失败,没有找到相关变量'});
            return false;
        }
        const _this = this;
        let multi_sku = iDetailConfig.isSKUOffer === 'true' ? 1 : 0;
        let name = _this.get1688Name();
        let pdt_picture = _this.get1688Picture();
        let attr = {};
        let sku = {};
        if (multi_sku) {
            let skuProp = iDetailData.sku.skuProps;
            for(let attrNameId=0; attrNameId<skuProp.length; attrNameId++){
                let attrName = skuProp[attrNameId].prop;
                let attrValue = {};
                for(let attrValueId=0; attrValueId<skuProp[attrNameId].value.length;attrValueId++){
                    let img='';
                    if (typeof skuProp[attrNameId].value[attrValueId].imageUrl!='undefined' &&skuProp[attrNameId].value[attrValueId].imageUrl){
                        img=skuProp[attrNameId].value[attrValueId].imageUrl;
                    }
                    let attrValueName=skuProp[attrNameId].value[attrValueId].name;
                    attrValue[attrValueId]={name:attrValueName,img:img};
                }
                attr[attrNameId]={attrName:attrName,attrValue:attrValue};
            }
            let skuMap=iDetailData.sku.skuMap;
            for(let k in skuMap){
                let item = skuMap[k];
                let stock= item.canBookCount;
                let sku_attr = k.split('&gt;');
                let pvs = {};
                let sku_img='';
                for (let j=0; j<sku_attr.length; j++) {
                    let attrNameAndImg = _this.get1688Attr(sku_attr[j],attr);
                    pvs[attrNameAndImg['attrName']] = {text:sku_attr[j], img:attrNameAndImg['img']};
                    if (attrNameAndImg['img']) {
                        sku_img = attrNameAndImg['img'];
                    }
                }
                let price = (typeof item.discountPrice!='undefined')?item.discountPrice:iDetailData.sku.priceRangeOriginal[0][1];
                sku[item.skuId] = {pvs:pvs, price:price, stock:stock, sku_img:sku_img};
            }
        } else {
            const price = document.querySelector('meta[property="og:product:price"]').content;
            const tempConfigDom = document.querySelector('.mod-detail-purchasing.mod-detail-purchasing-single');
            let stock = 0;
            if (iDetailData.registeredData.stock) {
                stock = iDetailData.registeredData.stoc;
            } else {
                if (tempConfigDom) {
                    stock = JSON.parse(tempConfigDom.getAttribute('data-mod-config')).max
                }
            }
            sku[iDetailConfig.feedbackInfoId] = {price:price, stock:stock}
        }
        let shop_name = '';
        if (document.querySelector('a.company-name')) {
            shop_name = document.querySelector('a.company-name').innerText;
        }
        let ret_data = {sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url:location.href,item_id:iDetailConfig.feedbackInfoId,shop_name:shop_name,shop_id:iDetailConfig.memberid,shop_url:iDetailConfig.companySiteLink};
        //获取介绍属性
        let attributes = [];
        const attributesDom = document.getElementById('mod-detail-attributes');
        if (attributesDom) {
            const tempJson = JSON.parse(attributesDom.getAttribute('data-feature-json'));
            for (let i=0; i<tempJson.length; i++) {
                attributes.push({name:tempJson[i].name,value:tempJson[i].values.join(',')});
            }
        }
        ret_data.attributes = attributes;
        _this.get1688DesPic(ret_data, callback);
    },
    get1688Name: function() {
        const obj = document.querySelector('#mod-detail-title .d-title');
        if (obj) {
            return obj.innerText;
        } else {
            return '';
        }
    },
    get1688Picture: function() {
        let pic = [];
        const obj = document.querySelectorAll('#dt-tab li');
        for (let i=0; i<obj.length;i++) {
            let imgdata = obj[i].getAttribute('data-imgs');
            if (imgdata) {
                imgdata = JSON.parse(imgdata);
                pic.push(imgdata.preview.replace('.64x64', ''));
            }
        }
        return pic;
    },
    get1688Attr: function(attrValue,attr) {
        for(let k in attr){
            for(let j in attr[k]['attrValue']){
                if(attr[k]['attrValue'][j].name==attrValue){
                    return {attrName:attr[k].attrName,img:attr[k]['attrValue'][j].img};
                }
            }
        }
        return {};
    },
    get1688DesPic: function(data, callback) {
        const _this = this;
        if (typeof offer_details === 'undefined') {
            const obj = document.getElementById('desc-lazyload-container');
            if (obj) {
                const des_url = document.getElementById('desc-lazyload-container').getAttribute('data-tfs-url');
                HELPERINIT.loadStaticUrl('js', des_url, '', function(){
                    _this.get1688DesPicUrl(data, callback);
                });
            } else {
                _this.get1688DesPicUrl({}, callback);
            }
        } else {
            _this.get1688DesPicUrl(data, callback);
        }
    },
    get1688DesPicUrl: function(data, callback) {
        let des_picture = [];
        if (typeof offer_details !== 'undefined') {
            const des_pic_craw = offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            for(let i=0; i<des_pic_craw.length; i++){
                const src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                if (this.filterUrl(src)){
                    des_picture.push(src);
                }
            }
        }
        data['des_picture'] = des_picture;
        callback({code:200, data:data, message:'获取成功'});
    },
    getTaobao: function(callback) {
        if (typeof Hub === 'undefined') {
            callback({code:10002, data:false, message:'获取数据失败,没有找到相关变量'});
            return false;
        }
        const _this = this;
        _this.timeId = _this.waitTime(function() {
            if (Hub && g_config) {
                _this.clearTime(_this.timeId);
                let multi_sku = 0;
                let name = _this.getTaobaoName();
                let pdt_picture = _this.getTaobaoPicture();
                let sku = {};
                let attr = {};
                if (Hub.config.config.sku.sku) {
                    const skuMap = Hub.config.config.sku.valItemInfo.skuMap;
                    multi_sku = 1;
                    //属性切分
                    const skuDomList = document.querySelectorAll('.J_Prop');
                    for (let i=0; i<skuDomList.length; i++) {
                        let attrValue = {};
                        const attrName = skuDomList[i].querySelector('dt').innerText;
                        const valueDomList = skuDomList[i].querySelectorAll('dd li');
                        let attrNameId = 0;
                        for (let j=0; j<valueDomList.length; j++) {
                            let value = valueDomList[j].getAttribute('data-value').split(':');
                            if (j === 0) {
                                attrNameId = value[0];
                            }
                            let img = '';
                            const style = valueDomList[j].children[0].getAttribute('style');
                            if (style) {
                                img = style.match(/background\:url\(((.+))_30x30/)[1];
                            }
                            attrValue[value[1]]={name:valueDomList[j].children[0].children[0].innerText,img:img};
                        }
                        attr[attrNameId]={attrName:attrName,attrValue:attrValue};
                    }
                    const skuList = g_config.dynStock.sku;
                    for (let k in skuList) {
                        const stock = skuList[k].stock;
                        let price, original_price;
                        if (g_config.promotion.promoData[k]) {
                            price = g_config.promotion.promoData[k][0].price;
                            originalPrice = g_config.originalPrice[k].price;
                        } else {
                            price = originalPrice = g_config.originalPrice[k].price;
                        }
                        let attr_arr = k.substr(1,k.length-2).split(';');
                        let pvs = {};
                        let sku_img = '';
                        for (let i in attr_arr) {
                            let attr_item = attr_arr[i].split(':');
                            if (attr[attr_item[0]]['attrValue'][attr_item[1]].img) {
                                sku_img = attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                            }
                            pvs[attr[attr_item[0]].attrName] = {text:attr[attr_item[0]]['attrValue'][attr_item[1]].name,img:sku_img};
                        }
                        sku[skuMap[k].skuId]={pvs:pvs, price:price, original_price:originalPrice, stock:stock, sku_img:sku_img};
                    }
                } else {
                    sku[g_config.itemId] = {price:g_config.price, stock:g_config.dynStock.stock};
                }
                const ret_data = {sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url:location.href,item_id:g_config.itemId,shop_name:g_config.shopName,shop_id:g_config.shopId,shop_url:g_config.idata.shop.url};
                //获取描述属性
                const attributesDom = document.querySelectorAll('#attributes li');
                let attributes = [];
                for (let i=0; i<attributesDom.length;i++) {
                    const tempText = attributesDom[i].innerText.split(':');
                    attributes.push({name:tempText[0], value:tempText[1]});
                }
                ret_data.attributes = attributes;
                _this.getTaobaoDesPic(ret_data, callback);
            }
        }, 1000, false, function() {
            callback({code:10003, data:false, message:'获取数据超时'});
        });
    },
    getTaobaoName: function() {
        const obj = document.querySelector('#J_Title .tb-main-title');
        if (obj) {
            return obj.getAttribute('data-title');
        }
        return '';
    },
    getTaobaoPicture: function() {
        let pic = [];
        const obj = document.querySelectorAll('#J_UlThumb li');
        for (let i=0; i<obj.length; i++) {
            pic.push(obj[i].querySelector('img').src.replace('_50x50.jpg_.webp', ''));
        }
        return pic;
    },
    getTaobaoDesPic: function(data, callback) {
        const _this = this;
        if (typeof desc === 'undefined') {
            HELPERINIT.loadStaticUrl('js', g_config.descUrl, '', function(){
                _this.getTaobaoDesPicUrl(data, callback);
            });
        } else {
            _this.getTaobaoDesPicUrl(data, callback);
        }
    },
    getTaobaoDesPicUrl: function(data, callback) {
        let des_picture = [];
        const des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
        for (let i=0; i<des_pic_craw.length; i++) {
            const src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
            if (this.filterUrl(src) ){
                des_picture.push(src);
            }
        }
        data['des_picture'] = des_picture;
        callback({code:200, data:data, message:'获取成功'});
    },
    getTmall: function(callback) {
        const _this = this;
        const timeId = _this.waitTime(function() {
            if (typeof KISSY !== 'undefined') {
                _this.clearTime(timeId);
                let multi_sku = 0;
                let name = _this.getTmallName();
                let pdt_picture = _this.getTmallPicture();
                let sku = {};
                let attr = {};
                KISSY.use('detail-model/product', function(e,t) {
                    let skuMap = t.instance()['__attrVals']['skuMap'];
                    if (typeof skuMap === 'undefined') {
                        callback({code:10004, data:false, message:'获取数据失败'});
                        return false;
                    }
                    if(skuMap){
                        multi_sku=1;
                        let skuProp=t.instance()['__attrVals']['skuProp'];
                        let propertyPics=t.instance()['__attrVals']['propertyPics'];
                        if(typeof skuProp=='undefined'){
                            callback(1,{},'获取sku属性失败!请记录当前链接联系开发人员.');
                            return false;
                        }
                        for(let attrNameId in skuProp){
                            let attrName;
                            let attrValue={};
                            for(let attrValueId in skuProp[attrNameId]){
                                let img='';
                                if(propertyPics && typeof propertyPics[';'+attrNameId+':'+attrValueId+';'] !='undefined' && typeof propertyPics[';'+attrNameId+':'+attrValueId+';'][0] !='undefined'){
                                    img=propertyPics[';'+attrNameId+':'+attrValueId+';'][0];
                                }
                                attrName=skuProp[attrNameId][attrValueId].label;
                                attrValue[attrValueId]={name:skuProp[attrNameId][attrValueId].text,img:img};
                            }
                            attr[attrNameId]={attrName:attrName,attrValue:attrValue};
                        }
                        for(let k in skuMap){
                            let item=skuMap[k];
                            let stock=t.instance()['__attrVals']['inventory']['skuQuantity'][item.skuId]['quantity'];
                            if(stock == 0){
                                //跳过没库存产品
                                continue;
                            }
                            let attr_arr=k.substr(1,k.length-2).split(';');
                            let pvs={};
                            let sku_img='';
                            for(let i in attr_arr){
                                let attr_item=attr_arr[i].split(':');
                                if(!attr[attr_item[0]]||!attr[attr_item[0]]['attrValue'][attr_item[1]]){
                                    continue;
                                }
                                if(attr[attr_item[0]]['attrValue'][attr_item[1]].img){
                                    sku_img=attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                                }
                                pvs[attr[attr_item[0]].attrName]={text:attr[attr_item[0]]['attrValue'][attr_item[1]].name,img:sku_img};
                            }
                            let price=item.price;
                            sku[item.skuId]={pvs:pvs,price:price,stock:stock,sku_img:sku_img};
                            let priceInfo=t.instance()['__attrVals']['priceInfo'];
                            //有优惠价 覆盖
                            for(let k in priceInfo){
                                if(typeof sku[k] !='undefined' && priceInfo[k].promotionList){
                                    sku[k].price=priceInfo[k].promotionList[0].price;
                                }
                            }
                        }
                    }else{
                        let price=(typeof t.instance()['__attrVals']['promoPrice']!='undefined' && t.instance()['__attrVals']['promoPrice'])?t.instance()['__attrVals']['promoPrice']['str']:t.instance()['__attrVals']['originalPrice']['str'];
                        sku={price:price,stock:t.instance()['__attrVals']['currentInventory'].quantity};
                    }
                    let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:document.getElementById('LineZing').getAttribute('itemId'),shop_name:document.querySelector('#shopExtra .slogo-shopname').innerText,shop_id:g_config.shopId,shop_url:g_config.shopUrl};
                    let des_url='';
                    if(typeof t.instance()['__attrVals'].config.api.httpsDescUrl !== 'undefined'){
                        des_url=t.instance()['__attrVals'].config.api.httpsDescUrl;
                    }
                    //描述属性
                    let attributes = [];
                    const attributesDom = document.querySelectorAll('#J_AttrUL li');
                    for (let i=0; i<attributesDom.length; i++) {
                        const tempText = attributesDom[i].innerText.split(':');
                        attributes.push({name:tempText[0], value:tempText[1]});
                    }
                    ret_data.attributes = attributes;
                    //发起请求在请求内容中提取图片
                    _this.getTmallDesPic(ret_data, callback, des_url);
                });
            }
        }, 1000, false, function() {
            callback({code:10003, data:false, message:'获取数据超时'});
        });
    },
    getTmallName: function() {
        const obj = document.querySelector('#J_DetailMeta .tb-detail-hd h1');
        if (obj) {
            return obj.innerText;
        }
        return '';
    },
    getTmallPicture: function() {
        let pic = [];
        const obj = document.querySelectorAll('#J_UlThumb li');
        for (let i=0; i<obj.length; i++) {
            pic.push(obj[i].querySelector('img').src.replace('_60x60q90.jpg', ''));
        }
        return pic;
    },
    getTmallDesPic: function(data, callback, des_url) {
        const _this = this;
        if (!des_url) {
            callback({code:200, data:data, message:'获取数据成功'});
        } else {
            if (typeof desc === 'undefined') {
                HELPERINIT.loadStaticUrl('js', des_url, '', function(){
                    _this.getTmallDesPicUrl(data, callback);
                });
            } else {
                _this.getTmallDesPicUrl(data, callback);
            }
        }
    },
    getTmallDesPicUrl: function(data, callback) {
        let des_picture = [];
        const des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
        for (let i=0; i<des_pic_craw.length; i++) {
            const src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
            if (this.filterUrl(src)) {
                des_picture.push(src);
            }
        }
        data['des_picture'] = des_picture;
        callback({code:200, data:data, message:'获取数据成功'});
    },
    jsonp: function(url, params, callback) {
        const callbackName = params.jsonp ? params.jsonp : 'jsonp_'+this.random();
        const head = document.querySelector('head');
        params.callback = callbackName;
        const urlParams = this.urlFormatParams(params);
        const script = document.createElement('script');
        head.appendChild(script);
        //创建jsonp回调函数
        window[callbackName] = function(json) {
            head.removeChild(script);
            window[callbackName] = null;
            clearTimeout(script.timer);
            if (callback) {
                json.code = 0;
                json.msg = '获取成功';
                callback(json);
            }
        };
        //发送请求
        script.src = url + '?' + urlParams;
        //超时处理
        script.timer = setTimeout(function() {
            head.removeChild(script);
            clearTimeout(script.timer);
            window[callbackName] = null;
            if (callback) {
                callback({code: 1, data: false, msg: '获取超时'});
            }
        }, 10000);
    },
    urlFormatParams: function(params) {
        let arr = [];
        for(let name in params) {
            arr.push(encodeURIComponent(name) + '=' + encodeURIComponent(params[name]));
        };
        return arr.join('&');  
    },
    random: function() {
        return Math.floor(Math.random() * 10000 + 500);
    },
    waitTime: function(callback, time, noStop, falseCallback) {
        const _this = this;
        if (!time) {
            time = 500;
        }
        const id = _this.createId(8);
        let timeCount = 0;
        _this[id] = setInterval(function() {
            ++timeCount;
            if (!noStop && timeCount > 10) {
                clearInterval(_this[id]);
                if (falseCallback) {
                    falseCallback();
                }
            }
            if (callback) {
                callback();
            }
        }, time);
        return id;
    },
    clearTime: function(timeId) {
        return clearInterval(this[timeId]);
    },
    createId: function(len) {
        let arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        let str='';
        for (let i=0; i<len; ++i) {
            str += arr[Math.round(Math.random()*(arr.length-1))];
        }
        return str;
    },
    filterUrl: function(url) {
        const ignore = ["img.taobao.com","ma.m.1688.com","amos.alicdn.com","alisoft.com","add_to_favorites.htm",".gif"];
        for (let i=0;i<ignore.length;i++) {
            if (url.indexOf(ignore[i]) >= 0) {
                return false;
            }
        }
        return true;
    }
}