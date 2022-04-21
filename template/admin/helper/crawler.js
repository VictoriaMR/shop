var CRAWLER = {
    init: function(){
        this.domain = false;
        this.item_id = false;
        if (location.host.indexOf('1688.com') >= 0) {
            this.domain = '1688.com';
        } else if (location.host.indexOf('taobao.com') >= 0) {
            this.domain = 'taobao.com';
        } else if (location.host.indexOf('tmall.com') >= 0) {
            this.domain = 'tmall.com';
        }
        if (this.domain) {
            this.item_id = this.itemId();
        }
    },
    itemId: function() {
        var ret = false;
        var url = location.origin + location.pathname;
        switch (this.domain) {
            case '1688.com':
                let reg = /^https\:\/\/detail\.1688\.com\/offer\/(\d+)\.html(?:.)*/i;
                ret = url.match(reg);
                if (ret) {
                    ret = ret[1];
                }
                break;
            case 'taobao.com':
                if(url == 'https://item.taobao.com/item.htm'){
                    ret = this.itemIdStr(location.search);
                }
                break;
            case 'tmall.com':
                if(url == 'https://detail.tmall.com/item.htm'){
                    ret = this.itemIdStr(location.search);
                }
                break;
        }
        return ret;
    },
    itemIdStr: function(str) {
        str = str.substring(1);//去掉?
        var param = str.split('&');//以&切割
        for(var k in param){
            if(param[k].substring(0,3)=='id='){
                return param[k].substring(3);
            }
        }
        return false;
    },
    isVerify: function() {
        var ret = false;
        switch (this.domain) {
            case '1688.com':
                ret = document.querySelector('.captcha-tips').length > 0;
                break;
            case 'taobao.com':
                // todo
                break;
            case 'tmall.com':
                //todo
                break;
        }
        return ret;
    },
    isOffShelf: function() {
        var ret = false;
        switch (this.domain) {
            case '1688.com':
                ret = document.querySelector('.mod-detail-offline-title').length>0;
                break;
            case 'taobao.com':
                ret = document.querySelector('.tb-off-sale').length>0;
                if (!ret) {
                    ret = document.querySelector('.tb-btn-buy .tb-disabled').length>0;
                }
                break;
            case 'tmall.com':
                ret = document.querySelector('.sold-out-recommend').length>0;
                if (!ret) {
                    ret = document.querySelector('.tb-btn-wait').length>0;
                }
                if (!ret) {
                    ret = document.querySelector('.errorDetail').length>0;
                }
                break;
        }
        return ret;
    },
    data: function(callback) {
        this.init();
        if (this.domain && this.item_id) {
            if (this.isVerify()) {
               callback(-1, {}, '页面需要验证!');
               return false;
            }
            if (this.isOffShelf()) {
                callback(-2, {}, '产品已下架');
               return false;
            }
            switch (this.domain) {
                case '1688.com':
                    this.get1688(callback);
                    break;
                case 'taobao.com':
                    this.getTaobao(callback);
                    break;
                case 'tmall.com':
                    this.getTmall(callback);
                    break;
            }
        } else {
            callback(-4, {}, '非产品详情页');
        }
    },
    get1688: function(callback) {
        if (!iDetailData && !window.__INIT_DATA) {
            callback(-1, {}, '获取数据失败!请记录当前链接联系开发人员.');
            return false;
        }
        var _this = this;
        var type = 2;
        if (iDetailData) {
            type = 1;
        }

        var name = '';
        var pdt_picture = [];
        if (type == 2) {
            name = window.__GLOBAL_DATA.tempModel.offerTitle;
            for (var i in window.__INIT_DATA.globalData.images) {
                if (typeof window.__INIT_DATA.globalData.images[i].fullPathImageURI !== 'undefined' && window.__INIT_DATA.globalData.images[i].fullPathImageURI) {
                    pic.push(window.__INIT_DATA.globalData.images[i].fullPathImageURI);
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

        let multi_sku=0;
        let name = name;
        let pdt_picture = pdt_picture;
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
            attr_uid = generalUniqueAttrId(attr);
            let skuMap=iDetailData.sku.skuMap;
            for(let k in skuMap){
                let item=skuMap[k];
                let stock;
                stock=item.canBookCount;
                let sku_attr=k.split('&gt;');
                let pvs={};
                let sku_img='';
                for(let j=0;j<sku_attr.length;j++){
                    let attrNameAndImg=get1688AttrNameAndImg(sku_attr[j],attr);
                    pvs[attrNameAndImg['attrName']]={attr_id:attr_uid['attr_value'][sku_attr[j]],text:sku_attr[j],img:attrNameAndImg['img'],sort:attrNameAndImg['sort']};
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
            attr_uid = generalUniqueAttrId(attr);
            let skuMap=window.__INIT_DATA.globalData.skuModel.skuInfoMap;
            for(let k in skuMap){
                let item=skuMap[k];
                let stock=item.canBookCount;
                let sku_attr=k.split('&gt;');
                let pvs={};
                let sku_img='';
                for(let j=0;j<sku_attr.length;j++){
                    let attrNameAndImg=get1688AttrNameAndImg(sku_attr[j],attr);
                    pvs[attrNameAndImg['attrName']]={attr_id:attr_uid['attr_value'][sku_attr[j]],text:sku_attr[j],img:attrNameAndImg['img'],sort:attrNameAndImg['sort']};
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
        //获取描述图片
        var des_picture = [];
        ret_data['des_picture'] = des_picture;
        if (offer_details) {
            var des_pic_craw=offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
            for(let i=0;i<des_pic_craw.length;i++){
                var src=des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                if(_this.isDescPic(src)){
                    des_picture.push(src);
                }
            }
            ret_data['des_picture']=des_picture;
            callback(0, ret_data, '获取成功!');
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
                    var des_pic_craw=offer_details.content.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
                    for(let i=0;i<des_pic_craw.length;i++){
                        var src=des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
                        if(_this.isDescPic(src)){
                            des_picture.push(src);
                        }
                    }
                    ret_data['des_picture'] = des_picture;
                    callback(0, ret_data,'获取成功!')
                };
            } else {
                callback(0, ret_data, '获取成功!');
            }
        }
    },
    getTaobao: function(callback) {
        if (typeof Hub === 'undefined') {
            callback(-1, {}, '获取数据失败.');
            return false;
        }
        let multi_sku=0;
        let name = getTaobaoProductName();
        let pdt_picture = getTaobaoPdtPicture();
        let skuDomList=document.querySelectorAll('.J_Prop');
        let attr={};
        let sku={};
        let attr_uid = {};
        if(Hub.config.config.sku.valItemInfo.skuMap){
            //多sku产品
            let interval = setInterval(function() {
                if (g_config.dynStock.sku) {
                    clearInterval(interval);
                    multi_sku=1;
                    let sort = 0;
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
                    attr_uid = generalUniqueAttrId(attr);
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
                            pvs[attr[attr_item[0]].attrName]={attr_id:attr_uid['attr_value'][text],text:text,img:sku_img,sort:sort};
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
                    let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:isItemPage(),attr_uid:attr_uid,pdt_video:''};
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
                        ret_data.post_fee = obj.innerText.replace('快递 ¥', '');
                    }
                    getTaoBaoData(ret_data, callback);
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
            if (sku.price==0 || sku.price=='' || sku.stock==0 || sku.stock=='') {
                callback(-1, {}, '获取数据失败!没有获取到价格库存!');
                return false;
            }
            let ret_data={sku:sku,attr:attr,name:name,pdt_picture:pdt_picture,multi_sku:multi_sku,product_url: location.href,item_id:isItemPage(),attr_uid:attr_uid,pdt_video:''};
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
                ret_data.post_fee = obj.innerText.replace('快递 ¥', '');
            }
            getTaoBaoData(ret_data, callback);
        }
    },
    getTmall: function(callback) {

    },
    isDescPic: function(src) {
        var ignore=["img.taobao.com","ma.m.1688.com","amos.alicdn.com","alisoft.com","add_to_favorites.htm","img.alicdn.com/NewGualianyingxiao"];
        for(var i=0;i<ignore.length;i++){
            if(src.indexOf(ignore[i])!=-1){
                return false;
            }
        }
        return true;
    }
};