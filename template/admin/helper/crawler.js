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
        } else if (this.isOffShelf()) {
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
    getClassName: function(className, str){
        const match = str.match(new RegExp('(?!class=")'+className+'[0-9A-Za-z_-]+(\\s?|"?)'));
        return match ? match[0].replace(/(^\s*)|(\s*$)/g, '') : match;
    },
    isDescPic: function(src) {
        var ignore = ['img.taobao.com', 'ma.m.1688.com', 'amos.alicdn.com', 'alisoft.com', 'add_to_favorites.htm', 'img.alicdn.com/NewGualianyingxiao', '_.webp'];
        for(var i=0; i<ignore.length; i++){
            if(src.indexOf(ignore[i])!=-1){
                return false;
            }
        }
        return true;
    },
    data: function(callback) {
        switch (HELPERINIT.getDomain()) {
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
                callback(-1, {}, '未知渠道商品详情页面');
                break;
        }
    },
    get1688: function(callback) {
        if (typeof iDetailData === 'undefined' && typeof window.__INIT_DATA == 'undefined') {
            callback(-1, {}, '获取数据失败.');
            return false;
        }
        if (typeof iDetailData !== 'undefined') {
            this.get1688Data1(callback);
        } else if (typeof window.__INIT_DATA !== 'undefined' || typeof window.__GLOBAL_DATA !== 'undefined') {
            this.get1688Data2(callback);
        } else {
            callback(-1, {}, '获取数据失败.');
        }
        var _this = this;
        var type = 1;
        if (typeof iDetailData === 'undefined') {
            type = 2;
        }
        return false;

        let multi_sku=0;
        let pdt_picture = [];
        var name = '';
        if (type == 2) {
            name = window.__GLOBAL_DATA.tempModel.offerTitle;
            for (var i in window.__INIT_DATA.globalData.images) {
                if (typeof window.__INIT_DATA.globalData.images[i].fullPathImageURI !== 'undefined' && window.__INIT_DATA.globalData.images[i].fullPathImageURI) {
                    pdt_picture.push(window.__INIT_DATA.globalData.images[i].fullPathImageURI);
                }
            }
        } else {
            var obj = document.querySelector('#mod-detail-title h1');
            if (obj) {
                name = obj.innerText;
            }
            if (!name) {
                obj = document.querySelector('.title-text');
            }
            if (obj) {
                name = obj.innerText;
            }
        }

        
        if (pdt_picture.length == 0) {
            var obj = document.querySelectorAll('#dt-tab li');
            if (obj.length>0) {
                for (var i = 0; i < obj.length; i++) {
                    var imgdata = obj[i].getAttribute('data-imgs');
                    if (imgdata) {
                        imgdata = JSON.parse(imgdata);
                        pdt_picture.push(imgdata.original);
                    }
                }
            }
        }
        if (pdt_picture.length == 0) {
            obj=document.querySelectorAll('.img-list-wrapper img.detail-gallery-img');
            if (obj.length>0) {
                for (var i = 0; i < obj.length; i++) {
                    var imgdata = obj[i].getAttribute('src');
                    if (imgdata) {
                        pdt_picture.push(imgdata);
                    }
                }
            }
        }
        let attr={};
        let sku={};
        //多sku
        if (type == 1 && iDetailData.sku) {
            multi_sku = 1;
            let skuProp=iDetailData.sku.skuProps;
            for(let attrNameId=0;attrNameId<skuProp.length;attrNameId++){
                let attrName=skuProp[attrNameId].prop;
                let attrValue={};
                for(let attrValueId=0;attrValueId<skuProp[attrNameId].value.length;attrValueId++){
                    let img='';
                    if(typeof skuProp[attrNameId].value[attrValueId].imageUrl!='undefined' &&skuProp[attrNameId].value[attrValueId].imageUrl){
                        img=skuProp[attrNameId].value[attrValueId].imageUrl;
                    }
                    let attrValueName=skuProp[attrNameId].value[attrValueId].name;
                    attrValue[attrValueId]={name:attrValueName,img:img};
                }
                attr[attrNameId]={attrName:attrName,attrValue:attrValue};
            }
            let skuMap=iDetailData.sku.skuMap;
            for(let k in skuMap){
                let item=skuMap[k];
                let stock;
                stock=item.canBookCount;
                let sku_attr=k.split('&gt;');
                let pvs={};
                let sku_img='';
                for(let j=0;j<sku_attr.length;j++){
                    let attrNameAndImg=_this.get1688Attr(sku_attr[j],attr);
                    pvs[attrNameAndImg['attrName']]={text:sku_attr[j],img:attrNameAndImg['img'],sort:attrNameAndImg['sort']};
                    if(attrNameAndImg['img']){
                        sku_img=attrNameAndImg['img'];
                    }
                }
                let price=(typeof item.discountPrice!='undefined')?item.discountPrice:(typeof iDetailData.sku.priceRangeOriginal !== 'undefined' ? iDetailData.sku.priceRangeOriginal[0][1] : '');
                sku[item.skuId]={pvs:pvs,price:price,stock:stock,sku_img:sku_img};
            }
        } else if (type == 2 && window.__INIT_DATA.globalData.skuModel) {
            multi_sku = 1;
            let skuProp=window.__INIT_DATA.globalData.skuModel.skuProps;
            for(let attrNameId=0;attrNameId<skuProp.length;attrNameId++){
                let attrName=skuProp[attrNameId].prop;
                let attrValue={};
                for(let attrValueId=0;attrValueId<skuProp[attrNameId].value.length;attrValueId++){
                    let img='';
                    if(typeof skuProp[attrNameId].value[attrValueId].imageUrl!='undefined' &&skuProp[attrNameId].value[attrValueId].imageUrl){
                        img=skuProp[attrNameId].value[attrValueId].imageUrl;
                    }
                    let attrValueName=skuProp[attrNameId].value[attrValueId].name;
                    attrValue[attrValueId]={name:attrValueName,img:img};
                }
                attr[attrNameId]={attrName:attrName,attrValue:attrValue};
            }
            let skuMap=window.__INIT_DATA.globalData.skuModel.skuInfoMap;
            for(let k in skuMap){
                let item=skuMap[k];
                let stock=item.canBookCount;
                let sku_attr=k.split('&gt;');
                let pvs={};
                let sku_img='';
                for(let j=0;j<sku_attr.length;j++){
                    let attrNameAndImg=_this.get1688Attr(sku_attr[j],attr);
                    pvs[attrNameAndImg['attrName']]={text:sku_attr[j],img:attrNameAndImg['img'],sort:attrNameAndImg['sort']};
                    if(attrNameAndImg['img']){
                        sku_img=attrNameAndImg['img'];
                    }
                }
                let back_up_price=0;
                if(typeof window.__GLOBAL_DATA.offerDomain!='undefined'){
                    let offerDomain=JSON.parse(window.__GLOBAL_DATA.offerDomain);
                    if(typeof offerDomain.tradeModel.offerPriceModel.currentPrices != 'undefined'){
                        back_up_price=offerDomain.tradeModel.offerPriceModel.currentPrices[0].price;
                    }
                }
                let price=(typeof item.discountPrice!='undefined')?item.discountPrice:(typeof item.price!='undefined'?item.price:back_up_price);
                sku[item.skuId]={pvs:pvs,price:price,stock:stock,sku_img:sku_img};
            }
        } else {
            if (type == 2) {
                sku = {price:price, stock:offerDomain.tradeModel.canBookedAmount};
            } else {
                let price=document.querySelector('meta[property="og:product:price"]').content;
                let stock = 0;
                var obj = document.querySelector('.mod-detail-purchasing.mod-detail-purchasing-single').getAttribute('data-mod-config');
                if (obj) {
                    stock=(JSON.parse(obj.getAttribute('data-mod-config')))['max'];
                }
                sku={price:price,stock:stock};
            }
        }
        let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:_this.item_id};
        //获取描述信息
        let attributes = [];
        let attributesDom = document.getElementById('mod-detail-attributes');
        if (attributesDom) {
            const tempJson = JSON.parse(attributesDom.getAttribute('data-feature-json'));
            for (let i=0; i<tempJson.length; i++) {
                attributes.push({name:tempJson[i].name,value:tempJson[i].values.join(',')});
            }
        } else {
            attributesDom = document.querySelectorAll('.od-pc-attribute .offer-attr-list .offer-attr-item');
            for (let i=0; i<attributesDom.length; i++) {
                attributes.push({name:attributesDom[i].querySelector('.offer-attr-item-name').innerText,value:attributesDom[i].querySelector('.offer-attr-item-value').innerText});
            }
        }
        ret_data.attributes = attributes;
        //店铺属性
        var shop_name='',shop_id,shop_url='';
        if(typeof iDetailConfig != 'undefined'){
            var obj = document.querySelector('meta[property="og:product:nick"]');
            if(obj){
                shop_name=obj.content.match(/name=(.*);/)[1];
            }
            shop_id=iDetailConfig.memberid;
            shop_url=iDetailConfig.companySiteLink;
        }else if(typeof window.__GLOBAL_DATA !='undefined'){
            var globalData = JSON.parse(window.__GLOBAL_DATA.offerDomain);
            if (typeof globalData.sellerModel != 'undefined') {
                shop_name = globalData.sellerModel.companyName;
                shop_id = globalData.sellerModel.memberId;
                shop_url = globalData.sellerModel.winportUrl;
            }
        }
        ret_data.shop_name = shop_name;
        ret_data.shop_id = shop_id;
        ret_data.shop_url = shop_url;
        ret_data.post_fee = 0;
        var obj = document.querySelector('.logistics-express-price');
        if (obj) {
            ret_data.post_fee = obj.innerText;
        }

        //获取描述图片
        if (typeof offer_details != 'undefined') {
            _this.get1688DescPic(ret_data, callback);
        } else {
            var des_url = '';
            if (type == 2) {
                var offerDomain = JSON.parse(window.__GLOBAL_DATA.offerDomain);
                des_url = offerDomain.offerDetail.detailUrl;
            } else {
                var obj = document.getElementById('desc-lazyload-container');
                if (obj) {
                    des_url = obj.getAttribute('data-tfs-url');
                }
            }
            if (des_url) {
                var head = document.getElementsByTagName('head')[0];
                var script = document.createElement('script');
                script.src = des_url;
                script.type = 'text/javascript';
                script.charset = 'utf-8';
                head.appendChild(script);
                script.onload=script.onreadystatechange=function(){
                    _this.get1688DescPic(ret_data, callback);
                };
            } else {
                ret_data['des_picture'] = [];
                callback(0, ret_data, '获取成功!');
            }
        }
    },
    get1688Data1:function(callback) {

    },
    get1688Data2:function(callback) {
        let ret_data = {};
        let data = window.__INIT_DATA.globalData;
        if (!data) {
            data = window.__GLOBAL_DATA.globalData;
        }
        ret_data.name = data.tempModel.offerTitle;
        ret_data.url = location.href;
        ret_data.item_id = HELPERINIT.isItemPage();
        ret_data.picture = [];
        //图片
        for (var i in data.images) {
            if (data.images[i].fullPathImageURI) {
                ret_data.picture.push(data.images[i].fullPathImageURI);
            }
        }
        //sku属性
        let skuProp = data.skuModel.skuProps;
        ret_data.attr = {};
        for(let i=0; i<skuProp.length; i++){
            let attrName = skuProp[i].prop;
            let attrId = skuProp[i].fid;
            let attrValue = {};
            for(let j=0;j<skuProp[i].value.length;j++){
                let data = skuProp[i].value[j];
                attrValue[j] = {name:data.name,img:data.imageUrl};
            }
            ret_data.attr[attrId]={attrName:attrName,attrValue:attrValue};
        }
        //sku列表
        ret_data.sku = {};
        for(let k in data.skuModel.skuInfoMap){
            let item=data.skuModel.skuInfoMap[k];
            let stock=item.canBookCount;
            let price = item.discountPrice;
            if (!price && data.orderParamModel.orderParam.skuParam.skuRangePrices) {
                price = data.orderParamModel.orderParam.skuParam.skuRangePrices[0].price;
            }
            ret_data.sku[item.skuId]={
                price:parseFloat(price),
                stock:stock,
                attr:k.split('&gt;')
            };
        }
        //商户信息
        ret_data.seller = {
            unique_id: data.offerBaseInfo.sellerUserId,
            url: data.offerBaseInfo.sellerWinportUrl,
            name: data.tempModel.companyName,
        };
        if (window.__STORE_DATA.components && window.__STORE_DATA.components['38229149'] && window.__STORE_DATA.components['38229149'].moduleData.appData.serviceList) {
            let tempData = window.__STORE_DATA.components['38229149'].moduleData.appData.serviceList;
            ret_data.seller.service = {};
            for (let i=0; i<tempData.length; i++) {
                ret_data.seller.service[tempData[i].serviceKey.replace('_group_value_new', '').replace('_group_value', '')] = tempData[i].score;
            }
        }
        console.log(ret_data, 'ret_data')
        localStorage.setItem('ret_data', JSON.stringify(ret_data));
        callback(0, ret_data, '获取成功!');
    },
    get1688Attr: function(attrValue,attr) {
        for(let k in attr){
            for(let j in attr[k]['attrValue']){
                if(attr[k]['attrValue'][j].name==attrValue){
                    return {attrName:attr[k].attrName,img:attr[k]['attrValue'][j].img,sort:attr[k]['attrValue'][j].sort};
                }
            }
        }
    },
    get1688DescPic: function(ret_data, callback) {
        var des_picture = [];
        if (typeof offer_details === 'undefined') {
            ret_data['des_picture'] = des_picture;
            callback(0, ret_data,'获取成功!');
            return false;
        }
        var des_pic_craw = offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
        if (!des_pic_craw) {
            ret_data['des_picture'] = des_picture;
            callback(0, ret_data,'获取成功!');
            return false;
        }
        for(let i=0; i<des_pic_craw.length; i++){
            var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
            if (this.isDescPic(src)) {
                des_picture.push(src);
            }
        }
        ret_data['des_picture'] = des_picture;
        callback(0, ret_data,'获取成功!');
    },
    getTaobao: function(callback) {
        if (typeof Hub === 'undefined') {
            callback(-1, {}, '获取数据失败.');
            return false;
        }
        var _this = this;
        let multi_sku=0;
        let name = '';
        var obj = document.querySelector('#J_Title h3');
        if (obj) {
            name = obj.innerText;
        }
        let pdt_picture = [];
        obj = document.querySelectorAll('#J_UlThumb li img');
        for (var i = 0; i < obj.length; i++) {
            imgdata = obj[i].getAttribute('data-src');
            if (imgdata) {
                pdt_picture.push(imgdata.replace('_50x50.jpg', ''));
            }
        }
        let attr={};
        let sku={};
        if(Hub.config.config.sku.valItemInfo.skuMap){
            //多sku产品
            let interval = setInterval(function() {
                if (g_config.dynStock && g_config.dynStock.sku) {
                    clearInterval(interval);
                    multi_sku=1;
                    let sort = 0;
                    let skuDomList=document.querySelectorAll('.J_Prop');
                    for(let k=0;k<skuDomList.length;k++){
                        let attrName=skuDomList[k].getElementsByTagName('dt')[0].innerText;//属性名
                        let attrValue={};
                        let valueList=skuDomList[k].getElementsByTagName('dd')[0].getElementsByTagName('li');//属性名
                        let attrNameId=0;
                        for(let j=0;j<valueList.length;j++){
                            let value=valueList[j].attributes['data-value'].nodeValue.split(':');
                            if(!attrNameId){
                                attrNameId=value[0];
                            }
                            let img='';
                            if(typeof valueList[j].children[0].attributes['style']!=='undefined'){
                                let styleText=valueList[j].children[0].attributes['style'].nodeValue;
                                img=styleText.match(/background\:url\(((.+))_30x30/)[1];
                            }
                            attrValue[value[1]]={name:valueList[j].children[0].children[0].innerText,img:img,sort:sort};
                            sort++;
                        }
                        attr[attrNameId]={attrName:attrName,attrValue:attrValue};
                    }
                    for(let k in Hub.config.config.sku.valItemInfo.skuMap){
                        let item=Hub.config.config.sku.valItemInfo.skuMap[k];
                        let stock;
                        if(g_config.dynStock && typeof g_config.dynStock.sku !== 'undefined' && typeof g_config.dynStock.sku[k] !== 'undefined'){
                            stock=g_config.dynStock.sku[k].stock;
                        } else {
                            stock = item.stock;
                            continue;
                        }
                        let attr_arr=k.substr(1,k.length-2).split(';');
                        let pvs={};
                        let sku_img='';
                        let sort;
                        for(let i in attr_arr){
                            let attr_item=attr_arr[i].split(':');
                            if(!attr[attr_item[0]]||!attr[attr_item[0]]['attrValue'][attr_item[1]]){
                                continue;
                            }
                            if(attr[attr_item[0]]['attrValue'][attr_item[1]].img){
                                sku_img=attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                            }
                            sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                            let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                            pvs[attr[attr_item[0]].attrName]={text:text,img:sku_img,sort:sort};
                        }
                        let price;
                        if(typeof g_config.promotion !='undefined' && typeof g_config.promotion.promoData !='undefined' && typeof g_config.promotion.promoData[k] !='undefined'){
                            price=g_config.promotion.promoData[k][0].price;
                        }else if (typeof g_config.originalPrice !='undefined'){
                            price=g_config.originalPrice[k].price;
                        } else {
                            price = item.price;
                        }
                        sku[item.skuId]={pvs:pvs,price:price,stock:stock,sku_img:sku_img};
                    }
                    let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:_this.item_id};
                    const attributesDom = document.querySelectorAll('#attributes li');
                    let attributes = [];
                    for (let i=0; i<attributesDom.length;i++) {
                        const tempText = attributesDom[i].innerText.split(':');
                        attributes.push({name:tempText[0], value:tempText[1]});
                    }
                    ret_data.attributes = attributes;
                    var obj = document.querySelector('#J_WlServiceTitle')
                    ret_data.post_fee = 0;
                    if (obj) {
                        ret_data.post_fee = obj.innerText.replace('快递 ¥', '').replace('快递 免运费 ', '');
                    }
                    _this.getTaobaoDescPicData(ret_data, callback);
                }
            }, 600);
        }else{
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
            let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:_this.item_id};
            //获取描述属性
            const attributesDom = document.querySelectorAll('#attributes li');
            let attributes = [];
            for (let i=0; i<attributesDom.length;i++) {
                const tempText = attributesDom[i].innerText.split(':');
                attributes.push({name:tempText[0], value:tempText[1]});
            }
            ret_data.attributes = attributes;
            var obj = document.querySelector('#J_WlServiceTitle')
            ret_data.post_fee = 0;
            if (obj) {
                ret_data.post_fee = obj.innerText.replace('快递 ¥', '').replace('快递 免运费 ', '');
            }
            _this.getTaobaoDescPicData(ret_data, callback);
        }
    },
    getTaobaoDescPicData: function(ret_data, callback) {
        var _this = this;
        if (desc) {
            _this.getTaobaoDescPic(ret_data, callback);
        } else {
            var head = document.querySelector('head');
            var script = document.createElement('script');
            script.src = g_config.descUrl;
            script.type = 'text/javascript';
            script.charset = 'utf-8';
            head.appendChild(script);
            script.onload=script.onreadystatechange=function(){
                _this.getTaobaoDescPic(ret_data, callback);
            };
        }
    },
    getTaobaoDescPic: function(ret_data, callback){
        var des_picture = [];
        var des_pic_craw=desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
        for(let i=0;i<des_pic_craw.length;i++){
            var src=des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
            if(this.isDescPic(src)){
                des_picture.push(src);
            }
        }
        ret_data['des_picture'] = des_picture;
        callback(0, ret_data, '获取成功!');
    },
    getTmall: function(callback) {
        if(typeof KISSY == 'undefined'){
            callback(-1, {}, '获取数据失败!');
            return false;
        }
        let ret_data = {};
        if(KISSY.version == '1.42') {
            KISSY.use('detail-model/product', function (e, t) {
            });
        } else {
            console.log(window.g_config.baseInfo);
            ret_data.pdt_picture = window.g_config.baseInfo.item.images;
        }
        console.log(ret_data, 'ret_data')
        return false;
        KISSY.jsonp = function (d,a,c){
            console.log(d,a,c)
            if(typeof a==="function"){c=a;a=void 0}
            return i(d,a,c,"jsonp")
        }
        KISSY.Env.mods.event.runtime.use('detail-model/product', function (e, t) {
            console.log(e, t)
        })
        console.log('here')
        KISSY.use('detail-model/product',function(e,t){
            console.log(e, t)
        });
        return false;
        const _this = this;
        let multi_sku=0;
        let name = '';
        var obj = document.querySelector('#J_DetailMeta .tb-detail-hd h1');
        if (obj) {
            name = obj.innerText;
        }
        let pdt_picture = [];
        obj = document.querySelectorAll('#J_UlThumb li img');
        for (var i = 0; i < obj.length; i++) {
            var imgdata = obj[i].src.replace('_60x60q90.jpg', '');
            if (imgdata) {
                pdt_picture.push(imgdata);
            }
        }
        console.log(name, pdt_picture)
        let sku={};
        let attr={};
        KISSY.use('detail-model/product',function(e,t){
            let skuMap = t.instance()['__attrVals']['skuMap'];
            console.log(skuMap, 'skuMap')
            if(!skuMap){
                callback(-1, {}, '获取列表失败！');
                return false;
            }
            let skuProp = t.instance()['__attrVals']['skuProp'];
            if(typeof skuProp=='undefined'){
                callback(-1, {}, '获取数据失败！');
                return false;
            }
            let skuDomList=document.querySelectorAll('.tb-prop.tm-sale-prop dd li');
            var sortArr = [];
            multi_sku=1;
            let tmpSort = 0;
            for(let k=0;k<skuDomList.length;k++) { // 记录属性的sort
                let value = skuDomList[k].attributes['data-value'].nodeValue;
                sortArr[value] = tmpSort;
                tmpSort++;
            }
            
            let propertyPics=t.instance()['__attrVals']['propertyPics'];
            for(let attrNameId in skuProp){
                let attrName;
                let attrValue={};
                for(let attrValueId in skuProp[attrNameId]){
                    let img='';
                    if(propertyPics && typeof propertyPics[';'+attrNameId+':'+attrValueId+';'] !='undefined' && typeof propertyPics[';'+attrNameId+':'+attrValueId+';'][0] !='undefined'){
                        img=propertyPics[';'+attrNameId+':'+attrValueId+';'][0].replace('_40x40q90.jpg', '');
                    }
                    let sort=0;
                    if(sortArr[attrNameId+':'+attrValueId] !='undefined'){
                        sort=sortArr[attrNameId+':'+attrValueId];
                    }
                    attrName=skuProp[attrNameId][attrValueId].label;
                    attrValue[attrValueId]={name:skuProp[attrNameId][attrValueId].text,img:img,sort:sort};
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
                let sort;
                for(let i in attr_arr){
                    let attr_item=attr_arr[i].split(':');
                    if(!attr[attr_item[0]]||!attr[attr_item[0]]['attrValue'][attr_item[1]]){
                        continue;
                    }
                    if(attr[attr_item[0]]['attrValue'][attr_item[1]].img){
                        sku_img=attr[attr_item[0]]['attrValue'][attr_item[1]].img;
                    }
                    sort = attr[attr_item[0]]['attrValue'][attr_item[1]].sort;
                    let text = attr[attr_item[0]]['attrValue'][attr_item[1]].name;
                    pvs[attr[attr_item[0]].attrName]={text:text,img:sku_img,sort:sort};
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

            let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:_this.item_id};
            //描述属性
            let attributes = [];
            const attributesDom = document.querySelectorAll('#J_AttrUL li');
            for (let i=0; i<attributesDom.length; i++) {
                const tempText = attributesDom[i].innerText.split(':');
                attributes.push({name:tempText[0], value:tempText[1]});
            }
            ret_data.attributes = attributes;
            _this.getTmallDescPicData(ret_data, callback, t.instance()['__attrVals'].config.api.httpsDescUrl);
        });
    },
    getTmallProductName: function () {
        var obj = document.getElementById('J_DetailMeta');
        if (obj === null) {
            return '';
        }
        obj = obj.getElementsByClassName('tb-detail-hd')[0];
        return obj.getElementsByTagName('h1')[0].innerText;
    },
    getTmallDescPicData: function(ret_data, callback, url) {
        var _this = this;
        if(typeof desc === 'undefined'){
            var head = document.querySelector('head');
            var script = document.createElement('script');
            script.src = url;
            script.type = 'text/javascript';
            script.charset = 'utf-8';
            head.appendChild(script);
            script.onload=script.onreadystatechange=function(){
                _this.getTaobaoDescPic(ret_data, callback);
            };
        }else{
            _this.getTaobaoDescPic(ret_data, callback);
        }
    },
};