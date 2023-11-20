!function(a, b) {
    console.log(a, 'a')
    console.log(b, 'b')
    function c() {
        var a = {}
          , b = new q(function(b, c) {
            a.resolve = b,
            a.reject = c
        }
        );
        return a.promise = b,
        a
    }
    function d(a, b) {
        console.log(a, 'd a')
         console.log(b, 'd b')
        for (var c in b)
            void 0 === a[c] && (a[c] = b[c]);
        return a
    }
    function e(a) {
        var b = document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0] || document.firstElementChild || document;
        b.appendChild(a)
    }
    function f(a) {
        var b = [];
        for (var c in a)
            a[c] && b.push(c + "=" + encodeURIComponent(a[c]));
        return b.join("&")
    }
    function g(a) {
        try {
            return ".com" !== a.substring(a.lastIndexOf(".")) ? (a.split(".") || []).length <= 3 ? a : a.split(".").slice(1).join(".") : a.substring(a.lastIndexOf(".", a.lastIndexOf(".") - 1) + 1)
        } catch (b) {
            return a.substring(a.lastIndexOf(".", a.lastIndexOf(".") - 1) + 1)
        }
    }
    function h(a) {
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
    function i(a) {
        return "[object Object]" == {}.toString.call(a)
    }
    function j(a, b, c) {
        var d = c || {};
        document.cookie = a.replace(/[^+#$&^`|]/g, encodeURIComponent).replace("(", "%28").replace(")", "%29") + "=" + b.replace(/[^+#$&\/:<-\[\]-}]/g, encodeURIComponent) + (d.domain ? ";domain=" + d.domain : "") + (d.path ? ";path=" + d.path : "") + (d.secure ? ";secure" : "") + (d.httponly ? ";HttpOnly" : "") + (d.sameSite ? ";Samesite=" + d.sameSite : "")
    }
    function k(a) {
        var b = new RegExp("(?:^|;\\s*)" + a + "\\=([^;]+)(?:;\\s*|$)").exec(document.cookie);
        return b ? b[1] : void 0
    }
    function l(a, b, c) {
        var d = new Date;
        d.setTime(d.getTime() - 864e5);
        var e = "/";
        document.cookie = a + "=;path=" + e + ";domain=." + b + ";expires=" + d.toGMTString(),
        document.cookie = a + "=;path=" + e + ";domain=." + c + "." + b + ";expires=" + d.toGMTString()
    }
    function m(a, b) {
        for (var c = a.split("."), d = b.split("."), e = 3, f = 0; e > f; f++) {
            var g = Number(c[f])
              , h = Number(d[f]);
            if (g > h)
                return 1;
            if (h > g)
                return -1;
            if (!isNaN(g) && isNaN(h))
                return 1;
            if (isNaN(g) && !isNaN(h))
                return -1
        }
        return 0
    }
    function n() {
        var b = a.location.hostname;
        if (!b) {
            var c = a.parent.location.hostname;
            c && ~c.indexOf("zebra.alibaba-inc.com") && (b = c)
        }
        var d = ["taobao.net", "taobao.com", "tmall.com", "tmall.hk", "alibaba-inc.com"]
          , e = new RegExp("([^.]*?)\\.?((?:" + d.join(")|(?:").replace(/\./g, "\\.") + "))","i")
          , f = b.match(e) || []
          , g = f[2] || "taobao.com"
          , h = f[1] || "m";
        "taobao.net" !== g || "x" !== h && "waptest" !== h && "daily" !== h ? "taobao.net" === g && "demo" === h ? h = "demo" : "alibaba-inc.com" === g && "zebra" === h ? h = "zebra" : "waptest" !== h && "wapa" !== h && "m" !== h && (h = "m") : h = "waptest";
        var i = "h5api";
        "taobao.net" === g && "waptest" === h && (i = "acs"),
        s.mainDomain = g,
        s.subDomain = h,
        s.prefix = i
    }
    function o() {
        var b = a.navigator.userAgent
          , c = b.match(/WindVane[\/\s]([\d\.\_]+)/);
        c && (s.WindVaneVersion = c[1]);
        var d = b.match(/AliApp\(([^\/]+)\/([\d\.\_]+)\)/i);
        d && (s.AliAppName = d[1],
        s.AliAppVersion = d[2]);
        var e = b.match(/AMapClient\/([\d\.\_]+)/i);
        e && (s.AliAppName = "AMAP",
        s.AliAppVersion = e[1])
    }
    function p(a) {
        this.id = "" + (new Date).getTime() + ++y,
        this.params = d(a || {}, {
            v: "*",
            data: {},
            type: "get",
            dataType: "jsonp"
        }),
        this.params.type = this.params.type.toLowerCase(),
        "object" == typeof this.params.data && (this.params.data = JSON.stringify(this.params.data)),
        this.middlewares = t.slice(0)
    }
    var q = a.Promise
      , r = (q || {
        resolve: function() {
            return void 0
        }
    }).resolve();
    String.prototype.trim || (String.prototype.trim = function() {
        return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, "")
    }
    );
    var s = {
        useJsonpResultType: !1,
        safariGoLogin: !0,
        useAlipayJSBridge: !1
    }
      , t = []
      , u = {
        ERROR: -1,
        SUCCESS: 0,
        TOKEN_EXPIRED: 1,
        SESSION_EXPIRED: 2
    };
    n(),
    o();
    var v = /[Android|Adr]/.test(a.navigator.userAgent)
      , w = "AP" === s.AliAppName
      , x = w && m(s.AliAppVersion, "10.1.2") >= 0 || "KB" === s.AliAppName && m(s.AliAppVersion, "7.1.62") >= 0 || v && "AMAP" === s.AliAppName && m(s.AliAppVersion, "1.0.1") >= 0
      , y = 0
      , z = "2.6.1";
    p.prototype.use = function(a) {
        if (!a)
            throw new Error("middleware is undefined");
        return this.middlewares.push(a),
        this
    }
    ,
    p.prototype.__processRequestMethod = function(a) {
        var b = this.params
          , c = this.options;
        "get" === b.type && "jsonp" === b.dataType ? c.getJSONP = !0 : "get" === b.type && "originaljsonp" === b.dataType ? c.getOriginalJSONP = !0 : "get" === b.type && "json" === b.dataType ? c.getJSON = !0 : "post" === b.type && (c.postJSON = !0),
        a()
    }
    ,
    p.prototype.__processRequestType = function(c) {
        var d = this
          , e = this.params
          , f = this.options;
        if (s.H5Request === !0 && (f.H5Request = !0),
        s.WindVaneRequest === !0 && (f.WindVaneRequest = !0),
        f.H5Request === !1 && f.WindVaneRequest === !0) {
            if (!x && (!b.windvane || parseFloat(f.WindVaneVersion) < 5.4))
                throw new Error("WINDVANE_NOT_FOUND::缂哄皯WindVane鐜");
            if (x && !a.AlipayJSBridge)
                throw new Error("ALIPAY_NOT_READY::鏀粯瀹濋€氶亾鏈噯澶囧ソ锛屾敮浠樺疂璇疯 https://lark.alipay.com/mtbsdkdocs/mtopjssdkdocs/pucq6z")
        } else if (f.H5Request === !0)
            f.WindVaneRequest = !1;
        else if ("undefined" == typeof f.WindVaneRequest && "undefined" == typeof f.H5Request) {
            if (b.windvane && parseFloat(f.WindVaneVersion) >= 5.4 ? f.WindVaneRequest = !0 : f.H5Request = !0,
            x) {
                if (f.WindVaneRequest = f.H5Request = void 0,
                a.AlipayJSBridge)
                    if (i(e.data))
                        f.WindVaneRequest = !0;
                    else
                        try {
                            i(JSON.parse(e.data)) ? f.WindVaneRequest = !0 : f.H5Request = !0
                        } catch (g) {
                            f.H5Request = !0
                        }
                else
                    f.H5Request = !0;
                "AMAP" !== s.AliAppName || e.useNebulaJSbridgeWithAMAP || (f.WindVaneRequest = f.H5Request = void 0,
                f.H5Request = !0)
            }
            window.self !== window.top && (f.H5Request = !0)
        }
        var h = a.navigator.userAgent.toLowerCase();
        return h.indexOf("youku") > -1 && f.mainDomain.indexOf("youku.com") < 0 && (f.WindVaneRequest = !1,
        f.H5Request = !0),
        f.mainDomain.indexOf("youku.com") > -1 && h.indexOf("youku") < 0 && (f.WindVaneRequest = !1,
        f.H5Request = !0),
        c ? c().then(function() {
            var a = f.retJson.ret;
            if (a instanceof Array && (a = a.join(",")),
            f.WindVaneRequest === !0 && x && f.retJson.error || !a || a.indexOf("PARAM_PARSE_ERROR") > -1 || a.indexOf("HY_FAILED") > -1 || a.indexOf("HY_NO_HANDLER") > -1 || a.indexOf("HY_CLOSED") > -1 || a.indexOf("HY_EXCEPTION") > -1 || a.indexOf("HY_NO_PERMISSION") > -1) {
                if (!x || !isNaN(f.retJson.error) || -1 !== f.retJson.error.indexOf("FAIL_SYS_ACCESS_DENIED"))
                    return x && i(e.data) && (e.data = JSON.stringify(e.data)),
                    s.H5Request = !0,
                    d.__sequence([d.__processRequestType, d.__processToken, d.__processRequestUrl, d.middlewares, d.__processRequest]);
                "undefined" == typeof f.retJson.api && "undefined" == typeof f.retJson.v && (f.retJson.api = e.api,
                f.retJson.v = e.v,
                f.retJson.ret = [f.retJson.error + "::" + f.retJson.errorMessage],
                f.retJson.data = {})
            }
        }) : void 0
    }
    ;
    var A = "_m_h5_c"
      , B = "_m_h5_tk"
      , C = "_m_h5_tk_enc";
    p.prototype.__getTokenFromAlipay = function() {
        var b = c()
          , d = this.options
          , e = (a.navigator.userAgent,
        !!location.protocol.match(/^https?\:$/));
        return d.useAlipayJSBridge === !0 && !e && x && a.AlipayJSBridge && a.AlipayJSBridge.call ? a.AlipayJSBridge.call("getMtopToken", function(a) {
            a && a.token && (d.token = a.token),
            b.resolve()
        }, function() {
            b.resolve()
        }) : b.resolve(),
        b.promise
    }
    ,
    p.prototype.__getTokenFromCookie = function() {
        var a = this.options;
        return a.CDR && k(A) ? a.token = k(A).split(";")[0] : a.token = a.token || k(B),
        a.token && (a.token = a.token.split("_")[0]),
        q.resolve()
    }
    ,
    p.prototype.__waitWKWebViewCookie = function(b) {
        var c = this.options;
        c.waitWKWebViewCookieFn && c.H5Request && a.webkit && a.webkit.messageHandlers ? c.waitWKWebViewCookieFn(b) : b()
    }
    ,
    p.prototype.__processToken = function(a) {
        var b = this
          , c = this.options;
        this.params;
        return c.token && delete c.token,
        c.WindVaneRequest !== !0 ? r.then(function() {
            return b.__getTokenFromAlipay()
        }).then(function() {
            return b.__getTokenFromCookie()
        }).then(a).then(function() {
            var a = c.retJson
              , d = a.ret;
            if (d instanceof Array && (d = d.join(",")),
            d.indexOf("TOKEN_EMPTY") > -1 || (c.CDR === !0 || c.syncCookieMode === !0) && d.indexOf("ILLEGAL_ACCESS") > -1 || d.indexOf("TOKEN_EXOIRED") > -1) {
                if (c.maxRetryTimes = c.maxRetryTimes || 5,
                c.failTimes = c.failTimes || 0,
                c.H5Request && ++c.failTimes < c.maxRetryTimes) {
                    var e = [b.__waitWKWebViewCookie, b.__processToken, b.__processRequestUrl, b.middlewares, b.__processRequest];
                    if (c.syncCookieMode === !0 && b.constructor.__cookieProcessorId !== b.id)
                        if (b.constructor.__cookieProcessor) {
                            var f = function(a) {
                                var c = function() {
                                    b.constructor.__cookieProcessor = null,
                                    b.constructor.__cookieProcessorId = null,
                                    a()
                                };
                                b.constructor.__cookieProcessor ? b.constructor.__cookieProcessor.then(c)["catch"](c) : a()
                            };
                            e = [f, b.__waitWKWebViewCookie, b.__processToken, b.__processRequestUrl, b.middlewares, b.__processRequest]
                        } else
                            b.constructor.__cookieProcessor = b.__requestProcessor,
                            b.constructor.__cookieProcessorId = b.id;
                    return b.__sequence(e)
                }
                c.maxRetryTimes > 0 && (l(A, c.pageDomain, "*"),
                l(B, c.mainDomain, c.subDomain),
                l(C, c.mainDomain, c.subDomain)),
                a.retType = u.TOKEN_EXPIRED
            }
        }) : void a()
    }
    ,
    p.prototype.__processRequestUrl = function(b) {
        var c = this.params
          , d = this.options;
        if (d.hostSetting && d.hostSetting[a.location.hostname]) {
            var e = d.hostSetting[a.location.hostname];
            e.prefix && (d.prefix = e.prefix),
            e.subDomain && (d.subDomain = e.subDomain),
            e.mainDomain && (d.mainDomain = e.mainDomain)
        }
        if (d.H5Request === !0) {
            var f = "//" + (d.prefix ? d.prefix + "." : "") + (d.subDomain ? d.subDomain + "." : "") + d.mainDomain + "/h5/" + c.api.toLowerCase() + "/" + c.v.toLowerCase() + "/"
              , g = c.appKey || ("waptest" === d.subDomain ? "4272" : "12574478")
              , i = (new Date).getTime()
              , j = h(d.token + "&" + i + "&" + g + "&" + c.data)
              , k = {
                jsv: z,
                appKey: g,
                t: i,
                sign: j
            }
              , l = {
                data: c.data,
                ua: c.ua
            };
            Object.keys(c).forEach(function(a) {
                "undefined" == typeof k[a] && "undefined" == typeof l[a] && "headers" !== a && "ext_headers" !== a && "ext_querys" !== a && (k[a] = c[a])
            }),
            c.ext_querys && Object.keys(c.ext_querys).forEach(function(a) {
                k[a] = c.ext_querys[a]
            }),
            d.getJSONP ? k.type = "jsonp" : d.getOriginalJSONP ? k.type = "originaljsonp" : (d.getJSON || d.postJSON) && (k.type = "originaljson"),
            "undefined" != typeof c.valueType && ("original" === c.valueType ? d.getJSONP || d.getOriginalJSONP ? k.type = "originaljsonp" : (d.getJSON || d.postJSON) && (k.type = "originaljson") : "string" === c.valueType && (d.getJSONP || d.getOriginalJSONP ? k.type = "jsonp" : (d.getJSON || d.postJSON) && (k.type = "json"))),
            d.useJsonpResultType === !0 && "originaljson" === k.type && delete k.type,
            d.dangerouslySetProtocol && (f = d.dangerouslySetProtocol + ":" + f),
            d.querystring = k,
            d.postdata = l,
            d.path = f
        }
        b()
    }
    ,
    p.prototype.__processUnitPrefix = function(a) {
        a()
    }
    ;
    var D = 0;
    p.prototype.__requestJSONP = function(a) {
        console.log(a, '__requestJSONP a')
        function b(a) {
            console.log(j, '__requestJSONP')
            if (k && clearTimeout(k),
            l.parentNode && l.parentNode.removeChild(l),
            "TIMEOUT" === a)
                window[j] = function() {
                    window[j] = void 0;
                    try {
                        delete window[j]
                    } catch (a) {}
                }
                ;
            else {
                window[j] = void 0;
                try {
                    delete window[j]
                } catch (b) {}
            }
        }
        var d = c()
          , g = this.params
          , h = this.options
          , i = g.timeout || 2e4
          , j = "mtopjsonp" + (g.jsonpIncPrefix || "") + ++D
          , k = setTimeout(function() {
            a(h.timeoutErrMsg || "TIMEOUT::鎺ュ彛瓒呮椂"),
            b("TIMEOUT")
        }, i);
        h.querystring.callback = j;
        var l = document.createElement("script");
        return l.src = h.path + "?" + f(h.querystring) + "&" + f(h.postdata),
        l.async = !0,
        l.onerror = function() {
            b("ABORT"),
            a(h.abortErrMsg || "ABORT::鎺ュ彛寮傚父閫€鍑�")
        }
        ,
        window[j] = function() {
            // if (typeof window['crawler_jsonp'] == 'undefined') {
            //     window['crawler_jsonp'] = {};
            // }
            // if (typeof window['crawler_jsonp'][j] == 'undefined') {
            //     window['crawler_jsonp'][j] = {};
            // }
            // window['crawler_jsonp'][j] = Array.prototype.slice.call(arguments),
            h.result = Array.prototype.slice.call(arguments),
            b(),
            d.resolve()
        }
        ,
        e(l),
        d.promise
    }
    ,
    p.prototype.__requestJSON = function(b) {
        function d(a) {
            l && clearTimeout(l),
            "TIMEOUT" === a && i.abort()
        }
        var e = c()
          , g = this.params
          , h = this.options
          , i = new a.XMLHttpRequest
          , j = g.timeout || 2e4
          , l = setTimeout(function() {
            b(h.timeoutErrMsg || "TIMEOUT::鎺ュ彛瓒呮椂"),
            d("TIMEOUT")
        }, j);
        h.CDR && k(A) && (h.querystring.c = decodeURIComponent(k(A))),
        i.onreadystatechange = function() {
            if (4 == i.readyState) {
                var a, c, f = i.status;
                if (f >= 200 && 300 > f || 304 == f) {
                    d(),
                    a = i.responseText,
                    c = i.getAllResponseHeaders() || "";
                    try {
                        a = /^\s*$/.test(a) ? {} : JSON.parse(a),
                        a.responseHeaders = c,
                        h.results = [a],
                        e.resolve()
                    } catch (g) {
                        b("PARSE_JSON_ERROR::瑙ｆ瀽JSON澶辫触")
                    }
                } else
                    d("ABORT"),
                    b(h.abortErrMsg || "ABORT::鎺ュ彛寮傚父閫€鍑�")
            }
        }
        ;
        var m, n, o = h.path + "?" + f(h.querystring);
        h.getJSON ? (m = "GET",
        o += "&" + f(h.postdata)) : h.postJSON && (m = "POST",
        n = f(h.postdata)),
        i.open(m, o, !0),
        i.withCredentials = !0,
        i.setRequestHeader("Accept", "application/json"),
        i.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        var p = g.ext_headers || g.headers;
        if (p)
            for (var q in p)
                i.setRequestHeader(q, p[q]);
        return i.send(n),
        e.promise
    }
    ,
    p.prototype.__requestWindVane = function(a) {
        function d(a) {
            g.results = [a],
            e.resolve()
        }
        var e = c()
          , f = this.params
          , g = this.options
          , h = f.data
          , i = f.api
          , j = f.v
          , k = g.postJSON ? 1 : 0
          , l = g.getJSON || g.postJSON || g.getOriginalJSONP ? "originaljson" : "";
        "undefined" != typeof f.valueType && ("original" === f.valueType ? l = "originaljson" : "string" === f.valueType && (l = "")),
        g.useJsonpResultType === !0 && (l = "");
        var m, n, o = "https" === location.protocol ? 1 : 0, p = f.isSec || 0, q = f.sessionOption || "AutoLoginOnly", r = f.ecode || 0, s = f.ext_headers || {}, t = f.ext_querys || {};
        n = "undefined" != typeof f.timer ? parseInt(f.timer) : "undefined" != typeof f.timeout ? parseInt(f.timeout) : 2e4,
        m = 2 * n,
        f.needLogin === !0 && "undefined" == typeof f.sessionOption && (q = "AutoLoginAndManualLogin"),
        "undefined" != typeof f.secType && "undefined" == typeof f.isSec && (p = f.secType);
        var u = {
            api: i,
            v: j,
            post: String(k),
            type: l,
            isHttps: String(o),
            ecode: String(r),
            isSec: String(p),
            param: JSON.parse(h),
            timer: n,
            sessionOption: q,
            ext_headers: s,
            ext_querys: t
        };
        f.ttid && g.dangerouslySetWVTtid === !0 && (u.ttid = f.ttid),
        Object.assign && f.dangerouslySetWindvaneParams && Object.assign(u, f.dangerouslySetWindvaneParams);
        var v = "MtopWVPlugin";
        return "string" == typeof f.customWindVaneClassName && (v = f.customWindVaneClassName),
        b.windvane.call(v, "send", u, d, d, m),
        e.promise
    }
    ,
    p.prototype.__requestAlipay = function(b) {
        function d(a) {
            g.results = [a],
            e.resolve()
        }
        var e = c()
          , f = this.params
          , g = this.options
          , h = {
            apiName: f.api,
            apiVersion: f.v,
            needEcodeSign: "1" === String(f.ecode),
            headers: f.ext_headers || {},
            usePost: !!g.postJSON
        };
        i(f.data) || (f.data = JSON.parse(f.data)),
        h.data = f.data,
        f.ttid && g.dangerouslySetWVTtid === !0 && (h.ttid = f.ttid),
        (g.getJSON || g.postJSON || g.getOriginalJSONP) && (h.type = "originaljson"),
        "undefined" != typeof f.valueType && ("original" === f.valueType ? h.type = "originaljson" : "string" === f.valueType && delete h.type),
        g.useJsonpResultType === !0 && delete h.type,
        Object.assign && f.dangerouslySetAlipayParams && Object.assign(h, f.dangerouslySetAlipayParams);
        var j = "mtop";
        return "string" == typeof f.customAlipayJSBridgeApi && (j = f.customAlipayJSBridgeApi),
        a.AlipayJSBridge.call(j, h, d),
        e.promise
    }
    ,
    p.prototype.__processRequest = function(a, b) {
        var c = this;
        return r.then(function() {
            var a = c.options;
            if (a.H5Request && (a.getJSONP || a.getOriginalJSONP))
                return c.__requestJSONP(b);
            if (a.H5Request && (a.getJSON || a.postJSON))
                return c.__requestJSON(b);
            if (a.WindVaneRequest)
                return x ? c.__requestAlipay(b) : c.__requestWindVane(b);
            throw new Error("UNEXCEPT_REQUEST::閿欒鐨勮姹傜被鍨�")
        }).then(a).then(function() {
            var a = c.options
              , b = (c.params,
            a.results[0])
              , d = b && b.ret || [];
            b.ret = d,
            d instanceof Array && (d = d.join(","));
            var e = b.c;
            a.CDR && e && j(A, e, {
                domain: a.pageDomain,
                path: "/",
                secure: a.secure,
                sameSite: a.sameSite
            }),
            d.indexOf("SUCCESS") > -1 ? b.retType = u.SUCCESS : b.retType = u.ERROR,
            a.retJson = b
        })
    }
    ,
    p.prototype.__sequence = function(a) {
        function b(a) {
            if (a instanceof Array)
                a.forEach(b);
            else {
                var g, h = c(), i = c();
                e.push(function() {
                    return h = c(),
                    g = a.call(d, function(a) {
                        return h.resolve(a),
                        i.promise
                    }, function(a) {
                        return h.reject(a),
                        i.promise
                    }),
                    g && (g = g["catch"](function(a) {
                        h.reject(a)
                    })),
                    h.promise
                }),
                f.push(function(a) {
                    return i.resolve(a),
                    g
                })
            }
        }
        var d = this
          , e = []
          , f = [];
        a.forEach(b);
        for (var g, h = r; g = e.shift(); )
            h = h.then(g);
        for (; g = f.pop(); )
            h = h.then(g);
        return h
    }
    ;
    var E = function(a) {
        a()
    }
      , F = function(a) {
        a()
    };
    p.prototype.request = function(c) {
        var e = this;
        if (this.options = d(c || {}, s),
        !q) {
            var f = "褰撳墠娴忚鍣ㄤ笉鏀寔Promise锛岃鍦╳indows瀵硅薄涓婃寕杞絇romise瀵硅薄";
            throw b.mtop = {
                ERROR: f
            },
            new Error(f)
        }
        var h = q.resolve([E, F]).then(function(a) {
            var b = a[0]
              , c = a[1];
            return e.__sequence([b, e.__processRequestMethod, e.__processRequestType, e.__processToken, e.__processRequestUrl, e.middlewares, e.__processRequest, c])
        }).then(function() {
            var a = e.options.retJson;
            return a.retType !== u.SUCCESS ? q.reject(a) : e.options.successCallback ? void e.options.successCallback(a) : q.resolve(a)
        })["catch"](function(a) {
            var c;
            return a instanceof Error ? (console.error(a.stack),
            c = {
                ret: [a.message],
                stack: [a.stack],
                retJson: u.ERROR
            }) : c = "string" == typeof a ? {
                ret: [a],
                retJson: u.ERROR
            } : void 0 !== a ? a : e.options.retJson,
            b.mtop.errorListener && b.mtop.errorListener({
                api: e.params.api,
                data: e.params.data,
                v: e.params.v,
                retJson: c
            }),
            e.options.failureCallback ? void e.options.failureCallback(c) : q.reject(c)
        });
        return this.__processRequestType(),
        e.options.H5Request && (e.constructor.__firstProcessor || (e.constructor.__firstProcessor = h),
        E = function(a) {
            e.constructor.__firstProcessor.then(a)["catch"](a)
        }
        ),
        ("get" === this.params.type && "json" === this.params.dataType || "post" === this.params.type) && (c.pageDomain = c.pageDomain || g(a.location.hostname),
        c.mainDomain !== c.pageDomain && (c.maxRetryTimes = 4,
        c.CDR = !0)),
        this.__requestProcessor = h,
        h
    }
    ,
    b.mtop = function(a) {
        return new p(a)
    }
    ,
    b.mtop.request = function(a, b, c) {
        var d = {
            H5Request: a.H5Request,
            WindVaneRequest: a.WindVaneRequest,
            LoginRequest: a.LoginRequest,
            AntiCreep: a.AntiCreep,
            AntiFlood: a.AntiFlood,
            successCallback: b,
            failureCallback: c || b
        };
        return new p(a).request(d)
    }
    ,
    b.mtop.H5Request = function(a, b, c) {
        var d = {
            H5Request: !0,
            successCallback: b,
            failureCallback: c || b
        };
        return new p(a).request(d)
    }
    ,
    b.mtop.middlewares = t,
    b.mtop.config = s,
    b.mtop.RESPONSE_TYPE = u,
    b.mtop.CLASS = p
}(window, window.lib || (window.lib = {})),
function(a, b) {
    function c(a) {
        return a.preventDefault(),
        !1
    }
    function d(a) {
        var b = new RegExp("(?:^|;\\s*)" + a + "\\=([^;]+)(?:;\\s*|$)").exec(document.cookie);
        return b ? b[1] : void 0
    }
    function e(b, d) {
        var e = this
          , f = a.dpr || 1
          , g = document.createElement("div")
          , h = document.documentElement.getBoundingClientRect()
          , i = Math.max(h.width, window.innerWidth) / f
          , j = Math.max(h.height, window.innerHeight) / f;
        g.style.cssText = ["-webkit-transform:scale(" + f + ") translateZ(0)", "-ms-transform:scale(" + f + ") translateZ(0)", "transform:scale(" + f + ") translateZ(0)", "-webkit-transform-origin:0 0", "-ms-transform-origin:0 0", "transform-origin:0 0", "width:" + i + "px", "height:" + j + "px", "z-index:2147483647", "position:" + (i > 800 ? "fixed" : "absolute"), "left:0", "top:0px", "background:" + (i > 800 ? "rgba(0,0,0,.5)" : "#FFF"), "display:none"].join(";");
        var k = document.createElement("div");
        k.style.cssText = ["width:100%", "height:52px", "background:#EEE", "line-height:52px", "text-align:left", "box-sizing:border-box", "padding-left:20px", "position:absolute", "left:0", "top:0", "font-size:16px", "font-weight:bold", "color:#333"].join(";"),
        k.innerText = b;
        var l = navigator.userAgent.match(/.*(iPhone|iPad|Android|ios|SymbianOS|Windows Phone).*/i)
          , m = document.createElement("img");
        m.style.cssText = ["display:block", "position:absolute", "margin-top:15px", "right:0", "top:0", "height:15px", "line-height:52px", "padding:0 20px", "color:#999"].join(";"),
        m.src = "https://gw.alicdn.com/tfs/TB1QZN.CYj1gK0jSZFuXXcrHpXa-200-200.png";
        var n = document.createElement("iframe");
        n.style.cssText = ["width:100%", "height:100%", "border:0", "overflow:hidden"].join(";"),
        l ? (k.appendChild(m),
        g.appendChild(k)) : (m.style.cssText = ["position:absolute", "width:15px", "height:15px", "top:102px", "left:" + (i / 2 - 185 + 390) + "px", "cursor: pointer", "border:0", "z-index:1", "overflow:hidden"].join(";"),
        g.appendChild(m),
        n.style.cssText = ["position:absolute", "top:92px", "left:" + (i / 2 - 185) + "px", "width:420px", "height:320px", "border:0", "background:#FFF", "overflow:hidden"].join(";")),
        g.appendChild(n),
        g.className = "J_MIDDLEWARE_FRAME_WIDGET",
        document.body.appendChild(g),
        n.src = d,
        m.addEventListener("click", function() {
            e.hide();
            var a = document.createEvent("HTMLEvents");
            a.initEvent("close", !1, !1),
            g.dispatchEvent(a)
        }, !1),
        this.addEventListener = function() {
            g.addEventListener.apply(g, arguments)
        }
        ,
        this.removeEventListener = function() {
            g.removeEventListener.apply(g, arguments)
        }
        ,
        this.show = function() {
            document.addEventListener("touchmove", c, !1),
            g.style.display = "block",
            window.scrollTo(0, 0)
        }
        ,
        this.hide = function() {
            document.removeEventListener("touchmove", c),
            window.scrollTo(0, -h.top),
            g.parentNode && g.parentNode.removeChild(g)
        }
    }
    function f(a) {
        var c = this
          , d = this.options
          , e = this.params;
        return a().then(function() {
            var a = d.retJson
              , f = a.ret
              , g = navigator.userAgent.toLowerCase()
              , h = g.indexOf("safari") > -1 && g.indexOf("chrome") < 0 && g.indexOf("qqbrowser") < 0;
            if (f instanceof Array && (f = f.join(",")),
            (f.indexOf("SESSION_EXPIRED") > -1 || f.indexOf("SID_INVALID") > -1 || f.indexOf("AUTH_REJECT") > -1 || f.indexOf("NEED_LOGIN") > -1) && (a.retType = l.SESSION_EXPIRED,
            !d.WindVaneRequest && (k.LoginRequest === !0 || d.LoginRequest === !0 || e.needLogin === !0))) {
                if (!b.login)
                    throw new Error("LOGIN_NOT_FOUND::缂哄皯lib.login");
                if (d.safariGoLogin !== !0 || !h || "taobao.com" === d.pageDomain)
                    return b.login.goLoginAsync().then(function(a) {
                        return c.__sequence([c.__processToken, c.__processRequestUrl, c.__processUnitPrefix, c.middlewares, c.__processRequest])
                    })["catch"](function(a) {
                        throw "CANCEL" === a ? new Error("LOGIN_CANCEL::鐢ㄦ埛鍙栨秷鐧诲綍") : new Error("LOGIN_FAILURE::鐢ㄦ埛鐧诲綍澶辫触")
                    });
                b.login.goLogin()
            }
        })
    }
    function g(a) {
        var b = this.options;
        this.params;
        return b.H5Request !== !0 || k.AntiFlood !== !0 && b.AntiFlood !== !0 ? void a() : a().then(function() {
            var a = b.retJson
              , c = a.ret;
            c instanceof Array && (c = c.join(",")),
            c.indexOf("FAIL_SYS_USER_VALIDATE") > -1 && a.data.url && (b.AntiFloodReferer ? location.href = a.data.url.replace(/(http_referer=).+/, "$1" + b.AntiFloodReferer) : location.href = a.data.url)
        })
    }
    function h(b) {
        var c = this
          , f = this.options
          , g = this.params;
        return f.AntiCreep !== !1 && (f.AntiCreep = !0),
        g.forceAntiCreep !== !0 && f.H5Request !== !0 || k.AntiCreep !== !0 && f.AntiCreep !== !0 ? void b() : b().then(function() {
            var b = f.retJson
              , h = b.ret;
            if (h instanceof Array && (h = h.join(",")),
            (h.indexOf("RGV587_ERROR::SM") > -1 || h.indexOf("ASSIST_FLAG") > -1) && b.data.url) {
                var j = "_m_h5_smt"
                  , k = d(j)
                  , l = !1;
                if (f.saveAntiCreepToken === !0 && k) {
                    k = JSON.parse(k);
                    for (var m in k)
                        g[m] && (l = !0)
                }
                if (f.saveAntiCreepToken === !0 && k && !l) {
                    for (var m in k)
                        g[m] = k[m];
                    return c.__sequence([c.__processToken, c.__processRequestUrl, c.__processUnitPrefix, c.middlewares, c.__processRequest])
                }
                return new i(function(d, h) {
                    function i() {
                        m.removeEventListener("close", i),
                        a.removeEventListener("message", k),
                        h("USER_INPUT_CANCEL::鐢ㄦ埛鍙栨秷杈撳叆")
                    }
                    function k(b) {
                        var e;
                        try {
                            e = JSON.parse(b.data) || {}
                        } catch (l) {}
                        if (e && "child" === e.type) {
                            m.removeEventListener("close", i),
                            a.removeEventListener("message", k),
                            m.hide();
                            var n;
                            try {
                                n = JSON.parse(decodeURIComponent(e.content)),
                                "string" == typeof n && (n = JSON.parse(n));
                                for (var o in n)
                                    g[o] = n[o];
                                f.saveAntiCreepToken === !0 ? (document.cookie = j + "=" + JSON.stringify(n) + ";",
                                a.location.reload()) : c.__sequence([c.__processToken, c.__processRequestUrl, c.__processUnitPrefix, c.middlewares, c.__processRequest]).then(d)
                            } catch (l) {
                                h("USER_INPUT_FAILURE::鐢ㄦ埛杈撳叆澶辫触")
                            }
                        }
                    }
                    var l = b.data.url
                      , m = new e("",l);
                    m.addEventListener("close", i, !1),
                    a.addEventListener("message", k, !1),
                    m.show()
                }
                )
            }
        })
    }
    if (!b || !b.mtop || b.mtop.ERROR)
        throw new Error("Mtop 鍒濆鍖栧け璐ワ紒");
    var i = a.Promise
      , j = b.mtop.CLASS
      , k = b.mtop.config
      , l = b.mtop.RESPONSE_TYPE;
    b.mtop.middlewares.push(f),
    b.mtop.loginRequest = function(a, b, c) {
        var d = {
            LoginRequest: !0,
            H5Request: !0,
            successCallback: b,
            failureCallback: c || b
        };
        return new j(a).request(d)
    }
    ,
    b.mtop.antiFloodRequest = function(a, b, c) {
        var d = {
            AntiFlood: !0,
            successCallback: b,
            failureCallback: c || b
        };
        return new j(a).request(d)
    }
    ,
    b.mtop.middlewares.push(g),
    b.mtop.antiCreepRequest = function(a, b, c) {
        var d = {
            AntiCreep: !0,
            successCallback: b,
            failureCallback: c || b
        };
        return new j(a).request(d)
    }
    ,
    b.mtop.middlewares.push(h)
}(window, window.lib || (window.lib = {}));
