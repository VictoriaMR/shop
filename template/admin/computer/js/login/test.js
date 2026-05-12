const commonjsGlobal = window.G;
!function() {
    const e = document.createElement("link").relList;
    if (!(e && e.supports && e.supports("modulepreload"))) {
        for (const e of document.querySelectorAll('link[rel="modulepreload"]'))
            t(e);
        new MutationObserver((e => {
            for (const r of e)
                if ("childList" === r.type)
                    for (const e of r.addedNodes)
                        "LINK" === e.tagName && "modulepreload" === e.rel && t(e)
        }
        )).observe(document, {
            childList: !0,
            subtree: !0
        })
    }
    function t(e) {
        if (e.ep)
            return;
        e.ep = !0;
        const t = function(e) {
            const t = {};
            return e.integrity && (t.integrity = e.integrity),
            e.referrerpolicy && (t.referrerPolicy = e.referrerpolicy),
            "use-credentials" === e.crossorigin ? t.credentials = "include" : "anonymous" === e.crossorigin ? t.credentials = "omit" : t.credentials = "same-origin",
            t
        }(e);
        fetch(e.href, t)
    }
}();
const _export_sfc = (e, t) => {
    const r = e.__vccOpts || e;
    for (const [o,n] of t)
        r[o] = n;
    return r
}
;
function getPublisher() {
    const e = new Map
      , t = "BROADCAST"
      , r = "SYSTEM_CONNECT";
    function o(t, r, o) {
        for (const n of t)
            e.has(n) || e.set(n, {
                last: 0,
                fnCall: {}
            }),
            e.get(n).fnCall[r] = o
    }
    function n(t, r) {
        for (const o of r)
            e.has(o) && (delete e.get(o).fnCall[t],
            0 === Object.keys(e.get(o).fnCall).length && e.delete(o))
    }
    function i(t, r=!1) {
        const {code: o, quoteTime: n} = t;
        if (e.has(o)) {
            const i = e.get(o);
            if (i.last < n || r) {
                i.last = n;
                for (let e of Object.keys(i.fnCall))
                    i.fnCall[e](t),
                    i.data = t
            }
        }
    }
    return {
        listCodesByNamespace: function(r) {
            const o = [];
            for (let[n,i] of e.entries())
                i !== t && i.fnCall[r] && o.push(n);
            return o
        },
        hasSubscribe: function(t) {
            return e.has(t) && e.get(t).last > 0
        },
        unsubscribe: n,
        subscribe: o,
        broadcastSubscribe: function(e, r) {
            o([t], e, r)
        },
        broadcastUnsubscribe: function(e) {
            n(e, [t])
        },
        publish: i,
        notify: function(r=null, ...o) {
            try {
                if (o && o.length) {
                    const {response: r} = o[0]
                      , [n] = r
                      , {quotation: s=[], tradeStatus: l} = n;
                    if (l)
                        !function(r) {
                            if (e.has(t)) {
                                const o = e.get(t);
                                for (let e of Object.keys(o.fnCall))
                                    o.fnCall[e](r)
                            }
                        }(l);
                    else
                        for (const e of s)
                            i(e)
                }
            } catch (n) {}
        },
        triggerNotify: function(t) {
            if (e.has(t)) {
                const r = e.get(t).data;
                r && i(r, !0)
            }
        },
        notifyWsStatus: function(e) {
            i({
                code: r,
                data: e
            }, !0)
        },
        connectStatusSubscribe: function(e) {
            o([r], "SYSTEM", e)
        }
    }
}
var minimal$1 = {
    exports: {}
}
  , indexMinimal = {}
  , minimal = {}
  , aspromise = asPromise;
function asPromise(e, t) {
    for (var r = new Array(arguments.length - 1), o = 0, n = 2, i = !0; n < arguments.length; )
        r[o++] = arguments[n++];
    return new Promise((function(n, s) {
        r[o] = function(e) {
            if (i)
                if (i = !1,
                e)
                    s(e);
                else {
                    for (var t = new Array(arguments.length - 1), r = 0; r < t.length; )
                        t[r++] = arguments[r];
                    n.apply(null, t)
                }
        }
        ;
        try {
            e.apply(t || null, r)
        } catch (l) {
            i && (i = !1,
            s(l))
        }
    }
    ))
}
var base64$1 = {};
!function(e) {
    var t = base64$1;
    t.length = function(e) {
        var t = e.length;
        if (!t)
            return 0;
        for (var r = 0; --t % 4 > 1 && "=" === e.charAt(t); )
            ++r;
        return Math.ceil(3 * e.length) / 4 - r
    }
    ;
    for (var r = new Array(64), o = new Array(123), n = 0; n < 64; )
        o[r[n] = n < 26 ? n + 65 : n < 52 ? n + 71 : n < 62 ? n - 4 : n - 59 | 43] = n++;
    t.encode = function(e, t, o) {
        for (var n, i = null, s = [], l = 0, u = 0; t < o; ) {
            var a = e[t++];
            switch (u) {
            case 0:
                s[l++] = r[a >> 2],
                n = (3 & a) << 4,
                u = 1;
                break;
            case 1:
                s[l++] = r[n | a >> 4],
                n = (15 & a) << 2,
                u = 2;
                break;
            case 2:
                s[l++] = r[n | a >> 6],
                s[l++] = r[63 & a],
                u = 0
            }
            l > 8191 && ((i || (i = [])).push(String.fromCharCode.apply(String, s)),
            l = 0)
        }
        return u && (s[l++] = r[n],
        s[l++] = 61,
        1 === u && (s[l++] = 61)),
        i ? (l && i.push(String.fromCharCode.apply(String, s.slice(0, l))),
        i.join("")) : String.fromCharCode.apply(String, s.slice(0, l))
    }
    ;
    var i = "invalid encoding";
    t.decode = function(e, t, r) {
        for (var n, s = r, l = 0, u = 0; u < e.length; ) {
            var a = e.charCodeAt(u++);
            if (61 === a && l > 1)
                break;
            if (void 0 === (a = o[a]))
                throw Error(i);
            switch (l) {
            case 0:
                n = a,
                l = 1;
                break;
            case 1:
                t[r++] = n << 2 | (48 & a) >> 4,
                n = a,
                l = 2;
                break;
            case 2:
                t[r++] = (15 & n) << 4 | (60 & a) >> 2,
                n = a,
                l = 3;
                break;
            case 3:
                t[r++] = (3 & n) << 6 | a,
                l = 0
            }
        }
        if (1 === l)
            throw Error(i);
        return r - s
    }
    ,
    t.test = function(e) {
        return /^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$/.test(e)
    }
}();
var eventemitter = EventEmitter;
function EventEmitter() {
    this._listeners = {}
}
EventEmitter.prototype.on = function(e, t, r) {
    return (this._listeners[e] || (this._listeners[e] = [])).push({
        fn: t,
        ctx: r || this
    }),
    this
}
,
EventEmitter.prototype.off = function(e, t) {
    if (void 0 === e)
        this._listeners = {};
    else if (void 0 === t)
        this._listeners[e] = [];
    else
        for (var r = this._listeners[e], o = 0; o < r.length; )
            r[o].fn === t ? r.splice(o, 1) : ++o;
    return this
}
,
EventEmitter.prototype.emit = function(e) {
    var t = this._listeners[e];
    if (t) {
        for (var r = [], o = 1; o < arguments.length; )
            r.push(arguments[o++]);
        for (o = 0; o < t.length; )
            t[o].fn.apply(t[o++].ctx, r)
    }
    return this
}
;
var float = factory(factory);
function factory(e) {
    return "undefined" != typeof Float32Array ? function() {
        var t = new Float32Array([-0])
          , r = new Uint8Array(t.buffer)
          , o = 128 === r[3];
        function n(e, o, n) {
            t[0] = e,
            o[n] = r[0],
            o[n + 1] = r[1],
            o[n + 2] = r[2],
            o[n + 3] = r[3]
        }
        function i(e, o, n) {
            t[0] = e,
            o[n] = r[3],
            o[n + 1] = r[2],
            o[n + 2] = r[1],
            o[n + 3] = r[0]
        }
        function s(e, o) {
            return r[0] = e[o],
            r[1] = e[o + 1],
            r[2] = e[o + 2],
            r[3] = e[o + 3],
            t[0]
        }
        function l(e, o) {
            return r[3] = e[o],
            r[2] = e[o + 1],
            r[1] = e[o + 2],
            r[0] = e[o + 3],
            t[0]
        }
        e.writeFloatLE = o ? n : i,
        e.writeFloatBE = o ? i : n,
        e.readFloatLE = o ? s : l,
        e.readFloatBE = o ? l : s
    }() : function() {
        function t(e, t, r, o) {
            var n = t < 0 ? 1 : 0;
            if (n && (t = -t),
            0 === t)
                e(1 / t > 0 ? 0 : 2147483648, r, o);
            else if (isNaN(t))
                e(2143289344, r, o);
            else if (t > 34028234663852886e22)
                e((n << 31 | 2139095040) >>> 0, r, o);
            else if (t < 11754943508222875e-54)
                e((n << 31 | Math.round(t / 1401298464324817e-60)) >>> 0, r, o);
            else {
                var i = Math.floor(Math.log(t) / Math.LN2);
                e((n << 31 | i + 127 << 23 | 8388607 & Math.round(t * Math.pow(2, -i) * 8388608)) >>> 0, r, o)
            }
        }
        function r(e, t, r) {
            var o = e(t, r)
              , n = 2 * (o >> 31) + 1
              , i = o >>> 23 & 255
              , s = 8388607 & o;
            return 255 === i ? s ? NaN : n * (1 / 0) : 0 === i ? 1401298464324817e-60 * n * s : n * Math.pow(2, i - 150) * (s + 8388608)
        }
        e.writeFloatLE = t.bind(null, writeUintLE),
        e.writeFloatBE = t.bind(null, writeUintBE),
        e.readFloatLE = r.bind(null, readUintLE),
        e.readFloatBE = r.bind(null, readUintBE)
    }(),
    "undefined" != typeof Float64Array ? function() {
        var t = new Float64Array([-0])
          , r = new Uint8Array(t.buffer)
          , o = 128 === r[7];
        function n(e, o, n) {
            t[0] = e,
            o[n] = r[0],
            o[n + 1] = r[1],
            o[n + 2] = r[2],
            o[n + 3] = r[3],
            o[n + 4] = r[4],
            o[n + 5] = r[5],
            o[n + 6] = r[6],
            o[n + 7] = r[7]
        }
        function i(e, o, n) {
            t[0] = e,
            o[n] = r[7],
            o[n + 1] = r[6],
            o[n + 2] = r[5],
            o[n + 3] = r[4],
            o[n + 4] = r[3],
            o[n + 5] = r[2],
            o[n + 6] = r[1],
            o[n + 7] = r[0]
        }
        function s(e, o) {
            return r[0] = e[o],
            r[1] = e[o + 1],
            r[2] = e[o + 2],
            r[3] = e[o + 3],
            r[4] = e[o + 4],
            r[5] = e[o + 5],
            r[6] = e[o + 6],
            r[7] = e[o + 7],
            t[0]
        }
        function l(e, o) {
            return r[7] = e[o],
            r[6] = e[o + 1],
            r[5] = e[o + 2],
            r[4] = e[o + 3],
            r[3] = e[o + 4],
            r[2] = e[o + 5],
            r[1] = e[o + 6],
            r[0] = e[o + 7],
            t[0]
        }
        e.writeDoubleLE = o ? n : i,
        e.writeDoubleBE = o ? i : n,
        e.readDoubleLE = o ? s : l,
        e.readDoubleBE = o ? l : s
    }() : function() {
        function t(e, t, r, o, n, i) {
            var s = o < 0 ? 1 : 0;
            if (s && (o = -o),
            0 === o)
                e(0, n, i + t),
                e(1 / o > 0 ? 0 : 2147483648, n, i + r);
            else if (isNaN(o))
                e(0, n, i + t),
                e(2146959360, n, i + r);
            else if (o > 17976931348623157e292)
                e(0, n, i + t),
                e((s << 31 | 2146435072) >>> 0, n, i + r);
            else {
                var l;
                if (o < 22250738585072014e-324)
                    e((l = o / 5e-324) >>> 0, n, i + t),
                    e((s << 31 | l / 4294967296) >>> 0, n, i + r);
                else {
                    var u = Math.floor(Math.log(o) / Math.LN2);
                    1024 === u && (u = 1023),
                    e(4503599627370496 * (l = o * Math.pow(2, -u)) >>> 0, n, i + t),
                    e((s << 31 | u + 1023 << 20 | 1048576 * l & 1048575) >>> 0, n, i + r)
                }
            }
        }
        function r(e, t, r, o, n) {
            var i = e(o, n + t)
              , s = e(o, n + r)
              , l = 2 * (s >> 31) + 1
              , u = s >>> 20 & 2047
              , a = 4294967296 * (1048575 & s) + i;
            return 2047 === u ? a ? NaN : l * (1 / 0) : 0 === u ? 5e-324 * l * a : l * Math.pow(2, u - 1075) * (a + 4503599627370496)
        }
        e.writeDoubleLE = t.bind(null, writeUintLE, 0, 4),
        e.writeDoubleBE = t.bind(null, writeUintBE, 4, 0),
        e.readDoubleLE = r.bind(null, readUintLE, 0, 4),
        e.readDoubleBE = r.bind(null, readUintBE, 4, 0)
    }(),
    e
}
function writeUintLE(e, t, r) {
    t[r] = 255 & e,
    t[r + 1] = e >>> 8 & 255,
    t[r + 2] = e >>> 16 & 255,
    t[r + 3] = e >>> 24
}
function writeUintBE(e, t, r) {
    t[r] = e >>> 24,
    t[r + 1] = e >>> 16 & 255,
    t[r + 2] = e >>> 8 & 255,
    t[r + 3] = 255 & e
}
function readUintLE(e, t) {
    return (e[t] | e[t + 1] << 8 | e[t + 2] << 16 | e[t + 3] << 24) >>> 0
}
function readUintBE(e, t) {
    return (e[t] << 24 | e[t + 1] << 16 | e[t + 2] << 8 | e[t + 3]) >>> 0
}
var inquire_1 = inquire;
function inquire(moduleName) {
    try {
        var mod = eval("quire".replace(/^/, "re"))(moduleName);
        if (mod && (mod.length || Object.keys(mod).length))
            return mod
    } catch (e) {}
    return null
}
var utf8$2 = {}, utf82;
utf82 = utf8$2,
utf82.length = function(e) {
    for (var t = 0, r = 0, o = 0; o < e.length; ++o)
        (r = e.charCodeAt(o)) < 128 ? t += 1 : r < 2048 ? t += 2 : 55296 == (64512 & r) && 56320 == (64512 & e.charCodeAt(o + 1)) ? (++o,
        t += 4) : t += 3;
    return t
}
,
utf82.read = function(e, t, r) {
    if (r - t < 1)
        return "";
    for (var o, n = null, i = [], s = 0; t < r; )
        (o = e[t++]) < 128 ? i[s++] = o : o > 191 && o < 224 ? i[s++] = (31 & o) << 6 | 63 & e[t++] : o > 239 && o < 365 ? (o = ((7 & o) << 18 | (63 & e[t++]) << 12 | (63 & e[t++]) << 6 | 63 & e[t++]) - 65536,
        i[s++] = 55296 + (o >> 10),
        i[s++] = 56320 + (1023 & o)) : i[s++] = (15 & o) << 12 | (63 & e[t++]) << 6 | 63 & e[t++],
        s > 8191 && ((n || (n = [])).push(String.fromCharCode.apply(String, i)),
        s = 0);
    return n ? (s && n.push(String.fromCharCode.apply(String, i.slice(0, s))),
    n.join("")) : String.fromCharCode.apply(String, i.slice(0, s))
}
,
utf82.write = function(e, t, r) {
    for (var o, n, i = r, s = 0; s < e.length; ++s)
        (o = e.charCodeAt(s)) < 128 ? t[r++] = o : o < 2048 ? (t[r++] = o >> 6 | 192,
        t[r++] = 63 & o | 128) : 55296 == (64512 & o) && 56320 == (64512 & (n = e.charCodeAt(s + 1))) ? (o = 65536 + ((1023 & o) << 10) + (1023 & n),
        ++s,
        t[r++] = o >> 18 | 240,
        t[r++] = o >> 12 & 63 | 128,
        t[r++] = o >> 6 & 63 | 128,
        t[r++] = 63 & o | 128) : (t[r++] = o >> 12 | 224,
        t[r++] = o >> 6 & 63 | 128,
        t[r++] = 63 & o | 128);
    return r - i
}
;
var pool_1 = pool, longbits, hasRequiredLongbits, hasRequiredMinimal;
function pool(e, t, r) {
    var o = r || 8192
      , n = o >>> 1
      , i = null
      , s = o;
    return function(r) {
        if (r < 1 || r > n)
            return e(r);
        s + r > o && (i = e(o),
        s = 0);
        var l = t.call(i, s, s += r);
        return 7 & s && (s = 1 + (7 | s)),
        l
    }
}
function requireLongbits() {
    if (hasRequiredLongbits)
        return longbits;
    hasRequiredLongbits = 1,
    longbits = t;
    var e = requireMinimal();
    function t(e, t) {
        this.lo = e >>> 0,
        this.hi = t >>> 0
    }
    var r = t.zero = new t(0,0);
    r.toNumber = function() {
        return 0
    }
    ,
    r.zzEncode = r.zzDecode = function() {
        return this
    }
    ,
    r.length = function() {
        return 1
    }
    ;
    var o = t.zeroHash = "\0\0\0\0\0\0\0\0";
    t.fromNumber = function(e) {
        if (0 === e)
            return r;
        var o = e < 0;
        o && (e = -e);
        var n = e >>> 0
          , i = (e - n) / 4294967296 >>> 0;
        return o && (i = ~i >>> 0,
        n = ~n >>> 0,
        ++n > 4294967295 && (n = 0,
        ++i > 4294967295 && (i = 0))),
        new t(n,i)
    }
    ,
    t.from = function(o) {
        if ("number" == typeof o)
            return t.fromNumber(o);
        if (e.isString(o)) {
            if (!e.Long)
                return t.fromNumber(parseInt(o, 10));
            o = e.Long.fromString(o)
        }
        return o.low || o.high ? new t(o.low >>> 0,o.high >>> 0) : r
    }
    ,
    t.prototype.toNumber = function(e) {
        if (!e && this.hi >>> 31) {
            var t = 1 + ~this.lo >>> 0
              , r = ~this.hi >>> 0;
            return t || (r = r + 1 >>> 0),
            -(t + 4294967296 * r)
        }
        return this.lo + 4294967296 * this.hi
    }
    ,
    t.prototype.toLong = function(t) {
        return e.Long ? new e.Long(0 | this.lo,0 | this.hi,Boolean(t)) : {
            low: 0 | this.lo,
            high: 0 | this.hi,
            unsigned: Boolean(t)
        }
    }
    ;
    var n = String.prototype.charCodeAt;
    return t.fromHash = function(e) {
        return e === o ? r : new t((n.call(e, 0) | n.call(e, 1) << 8 | n.call(e, 2) << 16 | n.call(e, 3) << 24) >>> 0,(n.call(e, 4) | n.call(e, 5) << 8 | n.call(e, 6) << 16 | n.call(e, 7) << 24) >>> 0)
    }
    ,
    t.prototype.toHash = function() {
        return String.fromCharCode(255 & this.lo, this.lo >>> 8 & 255, this.lo >>> 16 & 255, this.lo >>> 24, 255 & this.hi, this.hi >>> 8 & 255, this.hi >>> 16 & 255, this.hi >>> 24)
    }
    ,
    t.prototype.zzEncode = function() {
        var e = this.hi >> 31;
        return this.hi = ((this.hi << 1 | this.lo >>> 31) ^ e) >>> 0,
        this.lo = (this.lo << 1 ^ e) >>> 0,
        this
    }
    ,
    t.prototype.zzDecode = function() {
        var e = -(1 & this.lo);
        return this.lo = ((this.lo >>> 1 | this.hi << 31) ^ e) >>> 0,
        this.hi = (this.hi >>> 1 ^ e) >>> 0,
        this
    }
    ,
    t.prototype.length = function() {
        var e = this.lo
          , t = (this.lo >>> 28 | this.hi << 4) >>> 0
          , r = this.hi >>> 24;
        return 0 === r ? 0 === t ? e < 16384 ? e < 128 ? 1 : 2 : e < 2097152 ? 3 : 4 : t < 16384 ? t < 128 ? 5 : 6 : t < 2097152 ? 7 : 8 : r < 128 ? 9 : 10
    }
    ,
    longbits
}
function requireMinimal() {
    return hasRequiredMinimal || (hasRequiredMinimal = 1,
    function(e) {
        var t = minimal;
        function r(e, t, r) {
            for (var o = Object.keys(t), n = 0; n < o.length; ++n)
                void 0 !== e[o[n]] && r || (e[o[n]] = t[o[n]]);
            return e
        }
        function o(e) {
            function t(e, o) {
                if (!(this instanceof t))
                    return new t(e,o);
                Object.defineProperty(this, "message", {
                    get: function() {
                        return e
                    }
                }),
                Error.captureStackTrace ? Error.captureStackTrace(this, t) : Object.defineProperty(this, "stack", {
                    value: (new Error).stack || ""
                }),
                o && r(this, o)
            }
            return t.prototype = Object.create(Error.prototype, {
                constructor: {
                    value: t,
                    writable: !0,
                    enumerable: !1,
                    configurable: !0
                },
                name: {
                    get: () => e,
                    set: void 0,
                    enumerable: !1,
                    configurable: !0
                },
                toString: {
                    value() {
                        return this.name + ": " + this.message
                    },
                    writable: !0,
                    enumerable: !1,
                    configurable: !0
                }
            }),
            t
        }
        t.asPromise = aspromise,
        t.base64 = base64$1,
        t.EventEmitter = eventemitter,
        t.float = float,
        t.inquire = inquire_1,
        t.utf8 = utf8$2,
        t.pool = pool_1,
        t.LongBits = requireLongbits(),
        t.isNode = Boolean(void 0 !== commonjsGlobal && commonjsGlobal && commonjsGlobal.process && commonjsGlobal.process.versions && commonjsGlobal.process.versions.node),
        t.global = t.isNode && commonjsGlobal || "undefined" != typeof window && window || "undefined" != typeof self && self || commonjsGlobal,
        t.emptyArray = Object.freeze ? Object.freeze([]) : [],
        t.emptyObject = Object.freeze ? Object.freeze({}) : {},
        t.isInteger = Number.isInteger || function(e) {
            return "number" == typeof e && isFinite(e) && Math.floor(e) === e
        }
        ,
        t.isString = function(e) {
            return "string" == typeof e || e instanceof String
        }
        ,
        t.isObject = function(e) {
            return e && "object" == typeof e
        }
        ,
        t.isset = t.isSet = function(e, t) {
            var r = e[t];
            return !(null == r || !e.hasOwnProperty(t)) && ("object" != typeof r || (Array.isArray(r) ? r.length : Object.keys(r).length) > 0)
        }
        ,
        t.Buffer = function() {
            try {
                var e = t.inquire("buffer").Buffer;
                return e.prototype.utf8Write ? e : null
            } catch (r) {
                return null
            }
        }(),
        t._Buffer_from = null,
        t._Buffer_allocUnsafe = null,
        t.newBuffer = function(e) {
            return "number" == typeof e ? t.Buffer ? t._Buffer_allocUnsafe(e) : new t.Array(e) : t.Buffer ? t._Buffer_from(e) : "undefined" == typeof Uint8Array ? e : new Uint8Array(e)
        }
        ,
        t.Array = "undefined" != typeof Uint8Array ? Uint8Array : Array,
        t.Long = t.global.dcodeIO && t.global.dcodeIO.Long || t.global.Long || t.inquire("long"),
        t.key2Re = /^true|false|0|1$/,
        t.key32Re = /^-?(?:0|[1-9][0-9]*)$/,
        t.key64Re = /^(?:[\\x00-\\xff]{8}|-?(?:0|[1-9][0-9]*))$/,
        t.longToHash = function(e) {
            return e ? t.LongBits.from(e).toHash() : t.LongBits.zeroHash
        }
        ,
        t.longFromHash = function(e, r) {
            var o = t.LongBits.fromHash(e);
            return t.Long ? t.Long.fromBits(o.lo, o.hi, r) : o.toNumber(Boolean(r))
        }
        ,
        t.merge = r,
        t.lcFirst = function(e) {
            return e.charAt(0).toLowerCase() + e.substring(1)
        }
        ,
        t.newError = o,
        t.ProtocolError = o("ProtocolError"),
        t.oneOfGetter = function(e) {
            for (var t = {}, r = 0; r < e.length; ++r)
                t[e[r]] = 1;
            return function() {
                for (var e = Object.keys(this), r = e.length - 1; r > -1; --r)
                    if (1 === t[e[r]] && void 0 !== this[e[r]] && null !== this[e[r]])
                        return e[r]
            }
        }
        ,
        t.oneOfSetter = function(e) {
            return function(t) {
                for (var r = 0; r < e.length; ++r)
                    e[r] !== t && delete this[e[r]]
            }
        }
        ,
        t.toJSONOptions = {
            longs: String,
            enums: String,
            bytes: String,
            json: !0
        },
        t._configure = function() {
            var e = t.Buffer;
            e ? (t._Buffer_from = e.from !== Uint8Array.from && e.from || function(t, r) {
                return new e(t,r)
            }
            ,
            t._Buffer_allocUnsafe = e.allocUnsafe || function(t) {
                return new e(t)
            }
            ) : t._Buffer_from = t._Buffer_allocUnsafe = null
        }
    }()),
    minimal
}
var writer = Writer$1, util$4 = requireMinimal(), BufferWriter$1, LongBits$1 = util$4.LongBits, base64 = util$4.base64, utf8$1 = util$4.utf8;
function Op(e, t, r) {
    this.fn = e,
    this.len = t,
    this.next = void 0,
    this.val = r
}
function noop() {}
function State(e) {
    this.head = e.head,
    this.tail = e.tail,
    this.len = e.len,
    this.next = e.states
}
function Writer$1() {
    this.len = 0,
    this.head = new Op(noop,0,0),
    this.tail = this.head,
    this.states = null
}
var create$1 = function() {
    return util$4.Buffer ? function() {
        return (Writer$1.create = function() {
            return new BufferWriter$1
        }
        )()
    }
    : function() {
        return new Writer$1
    }
};
function writeByte(e, t, r) {
    t[r] = 255 & e
}
function writeVarint32(e, t, r) {
    for (; e > 127; )
        t[r++] = 127 & e | 128,
        e >>>= 7;
    t[r] = e
}
function VarintOp(e, t) {
    this.len = e,
    this.next = void 0,
    this.val = t
}
function writeVarint64(e, t, r) {
    for (; e.hi; )
        t[r++] = 127 & e.lo | 128,
        e.lo = (e.lo >>> 7 | e.hi << 25) >>> 0,
        e.hi >>>= 7;
    for (; e.lo > 127; )
        t[r++] = 127 & e.lo | 128,
        e.lo = e.lo >>> 7;
    t[r++] = e.lo
}
function writeFixed32(e, t, r) {
    t[r] = 255 & e,
    t[r + 1] = e >>> 8 & 255,
    t[r + 2] = e >>> 16 & 255,
    t[r + 3] = e >>> 24
}
Writer$1.create = create$1(),
Writer$1.alloc = function(e) {
    return new util$4.Array(e)
}
,
util$4.Array !== Array && (Writer$1.alloc = util$4.pool(Writer$1.alloc, util$4.Array.prototype.subarray)),
Writer$1.prototype._push = function(e, t, r) {
    return this.tail = this.tail.next = new Op(e,t,r),
    this.len += t,
    this
}
,
VarintOp.prototype = Object.create(Op.prototype),
VarintOp.prototype.fn = writeVarint32,
Writer$1.prototype.uint32 = function(e) {
    return this.len += (this.tail = this.tail.next = new VarintOp((e >>>= 0) < 128 ? 1 : e < 16384 ? 2 : e < 2097152 ? 3 : e < 268435456 ? 4 : 5,e)).len,
    this
}
,
Writer$1.prototype.int32 = function(e) {
    return e < 0 ? this._push(writeVarint64, 10, LongBits$1.fromNumber(e)) : this.uint32(e)
}
,
Writer$1.prototype.sint32 = function(e) {
    return this.uint32((e << 1 ^ e >> 31) >>> 0)
}
,
Writer$1.prototype.uint64 = function(e) {
    var t = LongBits$1.from(e);
    return this._push(writeVarint64, t.length(), t)
}
,
Writer$1.prototype.int64 = Writer$1.prototype.uint64,
Writer$1.prototype.sint64 = function(e) {
    var t = LongBits$1.from(e).zzEncode();
    return this._push(writeVarint64, t.length(), t)
}
,
Writer$1.prototype.bool = function(e) {
    return this._push(writeByte, 1, e ? 1 : 0)
}
,
Writer$1.prototype.fixed32 = function(e) {
    return this._push(writeFixed32, 4, e >>> 0)
}
,
Writer$1.prototype.sfixed32 = Writer$1.prototype.fixed32,
Writer$1.prototype.fixed64 = function(e) {
    var t = LongBits$1.from(e);
    return this._push(writeFixed32, 4, t.lo)._push(writeFixed32, 4, t.hi)
}
,
Writer$1.prototype.sfixed64 = Writer$1.prototype.fixed64,
Writer$1.prototype.float = function(e) {
    return this._push(util$4.float.writeFloatLE, 4, e)
}
,
Writer$1.prototype.double = function(e) {
    return this._push(util$4.float.writeDoubleLE, 8, e)
}
;
var writeBytes = util$4.Array.prototype.set ? function(e, t, r) {
    t.set(e, r)
}
: function(e, t, r) {
    for (var o = 0; o < e.length; ++o)
        t[r + o] = e[o]
}
;
Writer$1.prototype.bytes = function(e) {
    var t = e.length >>> 0;
    if (!t)
        return this._push(writeByte, 1, 0);
    if (util$4.isString(e)) {
        var r = Writer$1.alloc(t = base64.length(e));
        base64.decode(e, r, 0),
        e = r
    }
    return this.uint32(t)._push(writeBytes, t, e)
}
,
Writer$1.prototype.string = function(e) {
    var t = utf8$1.length(e);
    return t ? this.uint32(t)._push(utf8$1.write, t, e) : this._push(writeByte, 1, 0)
}
,
Writer$1.prototype.fork = function() {
    return this.states = new State(this),
    this.head = this.tail = new Op(noop,0,0),
    this.len = 0,
    this
}
,
Writer$1.prototype.reset = function() {
    return this.states ? (this.head = this.states.head,
    this.tail = this.states.tail,
    this.len = this.states.len,
    this.states = this.states.next) : (this.head = this.tail = new Op(noop,0,0),
    this.len = 0),
    this
}
,
Writer$1.prototype.ldelim = function() {
    var e = this.head
      , t = this.tail
      , r = this.len;
    return this.reset().uint32(r),
    r && (this.tail.next = e.next,
    this.tail = t,
    this.len += r),
    this
}
,
Writer$1.prototype.finish = function() {
    for (var e = this.head.next, t = this.constructor.alloc(this.len), r = 0; e; )
        e.fn(e.val, t, r),
        r += e.len,
        e = e.next;
    return t
}
,
Writer$1._configure = function(e) {
    BufferWriter$1 = e,
    Writer$1.create = create$1(),
    BufferWriter$1._configure()
}
;
var writer_buffer = BufferWriter
  , Writer = writer;
(BufferWriter.prototype = Object.create(Writer.prototype)).constructor = BufferWriter;
var util$3 = requireMinimal();
function BufferWriter() {
    Writer.call(this)
}
function writeStringBuffer(e, t, r) {
    e.length < 40 ? util$3.utf8.write(e, t, r) : t.utf8Write ? t.utf8Write(e, r) : t.write(e, r)
}
BufferWriter._configure = function() {
    BufferWriter.alloc = util$3._Buffer_allocUnsafe,
    BufferWriter.writeBytesBuffer = util$3.Buffer && util$3.Buffer.prototype instanceof Uint8Array && "set" === util$3.Buffer.prototype.set.name ? function(e, t, r) {
        t.set(e, r)
    }
    : function(e, t, r) {
        if (e.copy)
            e.copy(t, r, 0, e.length);
        else
            for (var o = 0; o < e.length; )
                t[r++] = e[o++]
    }
}
,
BufferWriter.prototype.bytes = function(e) {
    util$3.isString(e) && (e = util$3._Buffer_from(e, "base64"));
    var t = e.length >>> 0;
    return this.uint32(t),
    t && this._push(BufferWriter.writeBytesBuffer, t, e),
    this
}
,
BufferWriter.prototype.string = function(e) {
    var t = util$3.Buffer.byteLength(e);
    return this.uint32(t),
    t && this._push(writeStringBuffer, t, e),
    this
}
,
BufferWriter._configure();
var reader = Reader$1, util$2 = requireMinimal(), BufferReader$1, LongBits = util$2.LongBits, utf8 = util$2.utf8;
function indexOutOfRange(e, t) {
    return RangeError("index out of range: " + e.pos + " + " + (t || 1) + " > " + e.len)
}
function Reader$1(e) {
    this.buf = e,
    this.pos = 0,
    this.len = e.length
}
var create_array = "undefined" != typeof Uint8Array ? function(e) {
    if (e instanceof Uint8Array || Array.isArray(e))
        return new Reader$1(e);
    throw Error("illegal buffer")
}
: function(e) {
    if (Array.isArray(e))
        return new Reader$1(e);
    throw Error("illegal buffer")
}
, create = function() {
    return util$2.Buffer ? function(e) {
        return (Reader$1.create = function(e) {
            return util$2.Buffer.isBuffer(e) ? new BufferReader$1(e) : create_array(e)
        }
        )(e)
    }
    : create_array
}, value;
function readLongVarint() {
    var e = new LongBits(0,0)
      , t = 0;
    if (!(this.len - this.pos > 4)) {
        for (; t < 3; ++t) {
            if (this.pos >= this.len)
                throw indexOutOfRange(this);
            if (e.lo = (e.lo | (127 & this.buf[this.pos]) << 7 * t) >>> 0,
            this.buf[this.pos++] < 128)
                return e
        }
        return e.lo = (e.lo | (127 & this.buf[this.pos++]) << 7 * t) >>> 0,
        e
    }
    for (; t < 4; ++t)
        if (e.lo = (e.lo | (127 & this.buf[this.pos]) << 7 * t) >>> 0,
        this.buf[this.pos++] < 128)
            return e;
    if (e.lo = (e.lo | (127 & this.buf[this.pos]) << 28) >>> 0,
    e.hi = (e.hi | (127 & this.buf[this.pos]) >> 4) >>> 0,
    this.buf[this.pos++] < 128)
        return e;
    if (t = 0,
    this.len - this.pos > 4) {
        for (; t < 5; ++t)
            if (e.hi = (e.hi | (127 & this.buf[this.pos]) << 7 * t + 3) >>> 0,
            this.buf[this.pos++] < 128)
                return e
    } else
        for (; t < 5; ++t) {
            if (this.pos >= this.len)
                throw indexOutOfRange(this);
            if (e.hi = (e.hi | (127 & this.buf[this.pos]) << 7 * t + 3) >>> 0,
            this.buf[this.pos++] < 128)
                return e
        }
    throw Error("invalid varint encoding")
}
function readFixed32_end(e, t) {
    return (e[t - 4] | e[t - 3] << 8 | e[t - 2] << 16 | e[t - 1] << 24) >>> 0
}
function readFixed64() {
    if (this.pos + 8 > this.len)
        throw indexOutOfRange(this, 8);
    return new LongBits(readFixed32_end(this.buf, this.pos += 4),readFixed32_end(this.buf, this.pos += 4))
}
Reader$1.create = create(),
Reader$1.prototype._slice = util$2.Array.prototype.subarray || util$2.Array.prototype.slice,
Reader$1.prototype.uint32 = (value = 4294967295,
function() {
    if (value = (127 & this.buf[this.pos]) >>> 0,
    this.buf[this.pos++] < 128)
        return value;
    if (value = (value | (127 & this.buf[this.pos]) << 7) >>> 0,
    this.buf[this.pos++] < 128)
        return value;
    if (value = (value | (127 & this.buf[this.pos]) << 14) >>> 0,
    this.buf[this.pos++] < 128)
        return value;
    if (value = (value | (127 & this.buf[this.pos]) << 21) >>> 0,
    this.buf[this.pos++] < 128)
        return value;
    if (value = (value | (15 & this.buf[this.pos]) << 28) >>> 0,
    this.buf[this.pos++] < 128)
        return value;
    if ((this.pos += 5) > this.len)
        throw this.pos = this.len,
        indexOutOfRange(this, 10);
    return value
}
),
Reader$1.prototype.int32 = function() {
    return 0 | this.uint32()
}
,
Reader$1.prototype.sint32 = function() {
    var e = this.uint32();
    return e >>> 1 ^ -(1 & e) | 0
}
,
Reader$1.prototype.bool = function() {
    return 0 !== this.uint32()
}
,
Reader$1.prototype.fixed32 = function() {
    if (this.pos + 4 > this.len)
        throw indexOutOfRange(this, 4);
    return readFixed32_end(this.buf, this.pos += 4)
}
,
Reader$1.prototype.sfixed32 = function() {
    if (this.pos + 4 > this.len)
        throw indexOutOfRange(this, 4);
    return 0 | readFixed32_end(this.buf, this.pos += 4)
}
,
Reader$1.prototype.float = function() {
    if (this.pos + 4 > this.len)
        throw indexOutOfRange(this, 4);
    var e = util$2.float.readFloatLE(this.buf, this.pos);
    return this.pos += 4,
    e
}
,
Reader$1.prototype.double = function() {
    if (this.pos + 8 > this.len)
        throw indexOutOfRange(this, 4);
    var e = util$2.float.readDoubleLE(this.buf, this.pos);
    return this.pos += 8,
    e
}
,
Reader$1.prototype.bytes = function() {
    var e = this.uint32()
      , t = this.pos
      , r = this.pos + e;
    if (r > this.len)
        throw indexOutOfRange(this, e);
    return this.pos += e,
    Array.isArray(this.buf) ? this.buf.slice(t, r) : t === r ? new this.buf.constructor(0) : this._slice.call(this.buf, t, r)
}
,
Reader$1.prototype.string = function() {
    var e = this.bytes();
    return utf8.read(e, 0, e.length)
}
,
Reader$1.prototype.skip = function(e) {
    if ("number" == typeof e) {
        if (this.pos + e > this.len)
            throw indexOutOfRange(this, e);
        this.pos += e
    } else
        do {
            if (this.pos >= this.len)
                throw indexOutOfRange(this)
        } while (128 & this.buf[this.pos++]);
    return this
}
,
Reader$1.prototype.skipType = function(e) {
    switch (e) {
    case 0:
        this.skip();
        break;
    case 1:
        this.skip(8);
        break;
    case 2:
        this.skip(this.uint32());
        break;
    case 3:
        for (; 4 != (e = 7 & this.uint32()); )
            this.skipType(e);
        break;
    case 5:
        this.skip(4);
        break;
    default:
        throw Error("invalid wire type " + e + " at offset " + this.pos)
    }
    return this
}
,
Reader$1._configure = function(e) {
    BufferReader$1 = e,
    Reader$1.create = create(),
    BufferReader$1._configure();
    var t = util$2.Long ? "toLong" : "toNumber";
    util$2.merge(Reader$1.prototype, {
        int64: function() {
            return readLongVarint.call(this)[t](!1)
        },
        uint64: function() {
            return readLongVarint.call(this)[t](!0)
        },
        sint64: function() {
            return readLongVarint.call(this).zzDecode()[t](!1)
        },
        fixed64: function() {
            return readFixed64.call(this)[t](!0)
        },
        sfixed64: function() {
            return readFixed64.call(this)[t](!1)
        }
    })
}
;
var reader_buffer = BufferReader
  , Reader = reader;
(BufferReader.prototype = Object.create(Reader.prototype)).constructor = BufferReader;
var util$1 = requireMinimal();
function BufferReader(e) {
    Reader.call(this, e)
}
BufferReader._configure = function() {
    util$1.Buffer && (BufferReader.prototype._slice = util$1.Buffer.prototype.slice)
}
,
BufferReader.prototype.string = function() {
    var e = this.uint32();
    return this.buf.utf8Slice ? this.buf.utf8Slice(this.pos, this.pos = Math.min(this.pos + e, this.len)) : this.buf.toString("utf-8", this.pos, this.pos = Math.min(this.pos + e, this.len))
}
,
BufferReader._configure();
var rpc = {}
  , service = Service
  , util = requireMinimal();
function Service(e, t, r) {
    if ("function" != typeof e)
        throw TypeError("rpcImpl must be a function");
    util.EventEmitter.call(this),
    this.rpcImpl = e,
    this.requestDelimited = Boolean(t),
    this.responseDelimited = Boolean(r)
}
(Service.prototype = Object.create(util.EventEmitter.prototype)).constructor = Service,
Service.prototype.rpcCall = function e(t, r, o, n, i) {
    if (!n)
        throw TypeError("request must be specified");
    var s = this;
    if (!i)
        return util.asPromise(e, s, t, r, o, n);
    if (s.rpcImpl)
        try {
            return s.rpcImpl(t, r[s.requestDelimited ? "encodeDelimited" : "encode"](n).finish(), (function(e, r) {
                if (e)
                    return s.emit("error", e, t),
                    i(e);
                if (null !== r) {
                    if (!(r instanceof o))
                        try {
                            r = o[s.responseDelimited ? "decodeDelimited" : "decode"](r)
                        } catch (n) {
                            return s.emit("error", n, t),
                            i(n)
                        }
                    return s.emit("data", r, t),
                    i(null, r)
                }
                s.end(!0)
            }
            ))
        } catch (l) {
            return s.emit("error", l, t),
            void setTimeout((function() {
                i(l)
            }
            ), 0)
        }
    else
        setTimeout((function() {
            i(Error("already ended"))
        }
        ), 0)
}
,
Service.prototype.end = function(e) {
    return this.rpcImpl && (e || this.rpcImpl(null, null, null),
    this.rpcImpl = null,
    this.emit("end").off()),
    this
}
,
rpc.Service = service;
var roots = {};
!function(e) {
    var t = indexMinimal;
    function r() {
        t.util._configure(),
        t.Writer._configure(t.BufferWriter),
        t.Reader._configure(t.BufferReader)
    }
    t.build = "minimal",
    t.Writer = writer,
    t.BufferWriter = writer_buffer,
    t.Reader = reader,
    t.BufferReader = reader_buffer,
    t.util = requireMinimal(),
    t.rpc = rpc,
    t.roots = roots,
    t.configure = r,
    r()
}(),
minimal$1.exports = indexMinimal;
const $Reader = minimal$1.exports.Reader
  , $Writer = minimal$1.exports.Writer
  , $util = minimal$1.exports.util
  , $root = minimal$1.exports.roots.default || (minimal$1.exports.roots.default = {});
$root.jadegold = ( () => {
    const e = {};
    return e.msg = function() {
        const e = {};
        return e.quotation = function() {
            const e = {};
            return e.pbv2 = function() {
                const e = {};
                return e.QuoteMsgID = function() {
                    const e = {}
                      , t = Object.create(e);
                    return t[e[0] = "quotation_broadcast"] = 0,
                    t[e[1] = "status_broadcast"] = 1,
                    t[e[16] = "heart_beat"] = 16,
                    t[e[18] = "latestQuotation"] = 18,
                    t[e[20] = "qryQuotation"] = 20,
                    t[e[24] = "unsubscribe"] = 24,
                    t[e[28] = "qry_status"] = 28,
                    t[e[30] = "qry_gold_delivery"] = 30,
                    t[e[32] = "auth"] = 32,
                    t[e[34] = "waring"] = 34,
                    t[e[36] = "qry_waring"] = 36,
                    t[e[64] = "codes_category_json"] = 64,
                    t[e[66] = "codes_info_json"] = 66,
                    t[e[68] = "codes_f10_json"] = 68,
                    t[e[70] = "qry_all_settle"] = 70,
                    t[e[80] = "qry_option_info"] = 80,
                    t[e[82] = "qry_cbond_yield"] = 82,
                    t
                }(),
                e.QuotationMsg = function() {
                    function e(e) {
                        if (this.response = [],
                        e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.msgid = 0,
                    e.prototype.seq = 0,
                    e.prototype.request = null,
                    e.prototype.response = $util.emptyArray,
                    e.prototype.jsonReq = "",
                    e.prototype.jsonResp = "",
                    e.prototype.errMsg = "",
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        if (t || (t = $Writer.create()),
                        null != e.msgid && Object.hasOwnProperty.call(e, "msgid") && t.uint32(8).int32(e.msgid),
                        null != e.seq && Object.hasOwnProperty.call(e, "seq") && t.uint32(16).sint32(e.seq),
                        null != e.request && Object.hasOwnProperty.call(e, "request") && $root.jadegold.msg.quotation.pbv2.QuotationRequest.encode(e.request, t.uint32(34).fork()).ldelim(),
                        null != e.response && e.response.length)
                            for (let r = 0; r < e.response.length; ++r)
                                $root.jadegold.msg.quotation.pbv2.QuotationResponse.encode(e.response[r], t.uint32(42).fork()).ldelim();
                        return null != e.jsonReq && Object.hasOwnProperty.call(e, "jsonReq") && t.uint32(66).string(e.jsonReq),
                        null != e.jsonResp && Object.hasOwnProperty.call(e, "jsonResp") && t.uint32(74).string(e.jsonResp),
                        null != e.errMsg && Object.hasOwnProperty.call(e, "errMsg") && t.uint32(138).string(e.errMsg),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.QuotationMsg;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.msgid = e.int32();
                                break;
                            case 2:
                                o.seq = e.sint32();
                                break;
                            case 4:
                                o.request = $root.jadegold.msg.quotation.pbv2.QuotationRequest.decode(e, e.uint32());
                                break;
                            case 5:
                                o.response && o.response.length || (o.response = []),
                                o.response.push($root.jadegold.msg.quotation.pbv2.QuotationResponse.decode(e, e.uint32()));
                                break;
                            case 8:
                                o.jsonReq = e.string();
                                break;
                            case 9:
                                o.jsonResp = e.string();
                                break;
                            case 17:
                                o.errMsg = e.string();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.msgid && e.hasOwnProperty("msgid"))
                            switch (e.msgid) {
                            default:
                                return "msgid: enum value expected";
                            case 0:
                            case 1:
                            case 16:
                            case 18:
                            case 20:
                            case 24:
                            case 28:
                            case 30:
                            case 32:
                            case 34:
                            case 36:
                            case 64:
                            case 66:
                            case 68:
                            case 70:
                            case 80:
                            case 82:
                            }
                        if (null != e.seq && e.hasOwnProperty("seq") && !$util.isInteger(e.seq))
                            return "seq: integer expected";
                        if (null != e.request && e.hasOwnProperty("request")) {
                            let t = $root.jadegold.msg.quotation.pbv2.QuotationRequest.verify(e.request);
                            if (t)
                                return "request." + t
                        }
                        if (null != e.response && e.hasOwnProperty("response")) {
                            if (!Array.isArray(e.response))
                                return "response: array expected";
                            for (let t = 0; t < e.response.length; ++t) {
                                let r = $root.jadegold.msg.quotation.pbv2.QuotationResponse.verify(e.response[t]);
                                if (r)
                                    return "response." + r
                            }
                        }
                        return null != e.jsonReq && e.hasOwnProperty("jsonReq") && !$util.isString(e.jsonReq) ? "jsonReq: string expected" : null != e.jsonResp && e.hasOwnProperty("jsonResp") && !$util.isString(e.jsonResp) ? "jsonResp: string expected" : null != e.errMsg && e.hasOwnProperty("errMsg") && !$util.isString(e.errMsg) ? "errMsg: string expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.QuotationMsg)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.QuotationMsg;
                        switch (e.msgid) {
                        case "quotation_broadcast":
                        case 0:
                            t.msgid = 0;
                            break;
                        case "status_broadcast":
                        case 1:
                            t.msgid = 1;
                            break;
                        case "heart_beat":
                        case 16:
                            t.msgid = 16;
                            break;
                        case "latestQuotation":
                        case 18:
                            t.msgid = 18;
                            break;
                        case "qryQuotation":
                        case 20:
                            t.msgid = 20;
                            break;
                        case "unsubscribe":
                        case 24:
                            t.msgid = 24;
                            break;
                        case "qry_status":
                        case 28:
                            t.msgid = 28;
                            break;
                        case "qry_gold_delivery":
                        case 30:
                            t.msgid = 30;
                            break;
                        case "auth":
                        case 32:
                            t.msgid = 32;
                            break;
                        case "waring":
                        case 34:
                            t.msgid = 34;
                            break;
                        case "qry_waring":
                        case 36:
                            t.msgid = 36;
                            break;
                        case "codes_category_json":
                        case 64:
                            t.msgid = 64;
                            break;
                        case "codes_info_json":
                        case 66:
                            t.msgid = 66;
                            break;
                        case "codes_f10_json":
                        case 68:
                            t.msgid = 68;
                            break;
                        case "qry_all_settle":
                        case 70:
                            t.msgid = 70;
                            break;
                        case "qry_option_info":
                        case 80:
                            t.msgid = 80;
                            break;
                        case "qry_cbond_yield":
                        case 82:
                            t.msgid = 82
                        }
                        if (null != e.seq && (t.seq = 0 | e.seq),
                        null != e.request) {
                            if ("object" != typeof e.request)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationMsg.request: object expected");
                            t.request = $root.jadegold.msg.quotation.pbv2.QuotationRequest.fromObject(e.request)
                        }
                        if (e.response) {
                            if (!Array.isArray(e.response))
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationMsg.response: array expected");
                            t.response = [];
                            for (let r = 0; r < e.response.length; ++r) {
                                if ("object" != typeof e.response[r])
                                    throw TypeError(".jadegold.msg.quotation.pbv2.QuotationMsg.response: object expected");
                                t.response[r] = $root.jadegold.msg.quotation.pbv2.QuotationResponse.fromObject(e.response[r])
                            }
                        }
                        return null != e.jsonReq && (t.jsonReq = String(e.jsonReq)),
                        null != e.jsonResp && (t.jsonResp = String(e.jsonResp)),
                        null != e.errMsg && (t.errMsg = String(e.errMsg)),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if ((t.arrays || t.defaults) && (r.response = []),
                        t.defaults && (r.msgid = t.enums === String ? "quotation_broadcast" : 0,
                        r.seq = 0,
                        r.request = null,
                        r.jsonReq = "",
                        r.jsonResp = "",
                        r.errMsg = ""),
                        null != e.msgid && e.hasOwnProperty("msgid") && (r.msgid = t.enums === String ? $root.jadegold.msg.quotation.pbv2.QuoteMsgID[e.msgid] : e.msgid),
                        null != e.seq && e.hasOwnProperty("seq") && (r.seq = e.seq),
                        null != e.request && e.hasOwnProperty("request") && (r.request = $root.jadegold.msg.quotation.pbv2.QuotationRequest.toObject(e.request, t)),
                        e.response && e.response.length) {
                            r.response = [];
                            for (let o = 0; o < e.response.length; ++o)
                                r.response[o] = $root.jadegold.msg.quotation.pbv2.QuotationResponse.toObject(e.response[o], t)
                        }
                        return null != e.jsonReq && e.hasOwnProperty("jsonReq") && (r.jsonReq = e.jsonReq),
                        null != e.jsonResp && e.hasOwnProperty("jsonResp") && (r.jsonResp = e.jsonResp),
                        null != e.errMsg && e.hasOwnProperty("errMsg") && (r.errMsg = e.errMsg),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.SubscribeFlag = function() {
                    const e = {}
                      , t = Object.create(e);
                    return t[e[0] = "SUBSCRIBE"] = 0,
                    t[e[1] = "KEEP"] = 1,
                    t
                }(),
                e.QuotationRequest = function() {
                    function e(e) {
                        if (this.codes = [],
                        this.freq = [],
                        e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.codes = $util.emptyArray,
                    e.prototype.freq = $util.emptyArray,
                    e.prototype.queryCondition = null,
                    e.prototype.subscribeFlag = 0,
                    e.prototype.auth = null,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        if (t || (t = $Writer.create()),
                        null != e.codes && e.codes.length)
                            for (let r = 0; r < e.codes.length; ++r)
                                t.uint32(10).string(e.codes[r]);
                        if (null != e.freq && e.freq.length) {
                            t.uint32(18).fork();
                            for (let r = 0; r < e.freq.length; ++r)
                                t.int32(e.freq[r]);
                            t.ldelim()
                        }
                        return null != e.queryCondition && Object.hasOwnProperty.call(e, "queryCondition") && $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition.encode(e.queryCondition, t.uint32(26).fork()).ldelim(),
                        null != e.subscribeFlag && Object.hasOwnProperty.call(e, "subscribeFlag") && t.uint32(32).int32(e.subscribeFlag),
                        null != e.auth && Object.hasOwnProperty.call(e, "auth") && $root.jadegold.msg.quotation.pbv2.AuthReq.encode(e.auth, t.uint32(42).fork()).ldelim(),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.QuotationRequest;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.codes && o.codes.length || (o.codes = []),
                                o.codes.push(e.string());
                                break;
                            case 2:
                                if (o.freq && o.freq.length || (o.freq = []),
                                2 == (7 & t)) {
                                    let t = e.uint32() + e.pos;
                                    for (; e.pos < t; )
                                        o.freq.push(e.int32())
                                } else
                                    o.freq.push(e.int32());
                                break;
                            case 3:
                                o.queryCondition = $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition.decode(e, e.uint32());
                                break;
                            case 4:
                                o.subscribeFlag = e.int32();
                                break;
                            case 5:
                                o.auth = $root.jadegold.msg.quotation.pbv2.AuthReq.decode(e, e.uint32());
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.codes && e.hasOwnProperty("codes")) {
                            if (!Array.isArray(e.codes))
                                return "codes: array expected";
                            for (let t = 0; t < e.codes.length; ++t)
                                if (!$util.isString(e.codes[t]))
                                    return "codes: string[] expected"
                        }
                        if (null != e.freq && e.hasOwnProperty("freq")) {
                            if (!Array.isArray(e.freq))
                                return "freq: array expected";
                            for (let t = 0; t < e.freq.length; ++t)
                                switch (e.freq[t]) {
                                default:
                                    return "freq: enum value[] expected";
                                case 0:
                                case 1:
                                case 2:
                                case 3:
                                case 4:
                                case 5:
                                case 6:
                                case 7:
                                case 8:
                                case 9:
                                case 10:
                                case 11:
                                case 12:
                                }
                        }
                        if (null != e.queryCondition && e.hasOwnProperty("queryCondition")) {
                            let t = $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition.verify(e.queryCondition);
                            if (t)
                                return "queryCondition." + t
                        }
                        if (null != e.subscribeFlag && e.hasOwnProperty("subscribeFlag") && !$util.isInteger(e.subscribeFlag))
                            return "subscribeFlag: integer expected";
                        if (null != e.auth && e.hasOwnProperty("auth")) {
                            let t = $root.jadegold.msg.quotation.pbv2.AuthReq.verify(e.auth);
                            if (t)
                                return "auth." + t
                        }
                        return null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.QuotationRequest)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.QuotationRequest;
                        if (e.codes) {
                            if (!Array.isArray(e.codes))
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationRequest.codes: array expected");
                            t.codes = [];
                            for (let r = 0; r < e.codes.length; ++r)
                                t.codes[r] = String(e.codes[r])
                        }
                        if (e.freq) {
                            if (!Array.isArray(e.freq))
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationRequest.freq: array expected");
                            t.freq = [];
                            for (let r = 0; r < e.freq.length; ++r)
                                switch (e.freq[r]) {
                                default:
                                case "REALTIME":
                                case 0:
                                    t.freq[r] = 0;
                                    break;
                                case "INFO":
                                case 1:
                                    t.freq[r] = 1;
                                    break;
                                case "TICK":
                                case 2:
                                    t.freq[r] = 2;
                                    break;
                                case "MIN1":
                                case 3:
                                    t.freq[r] = 3;
                                    break;
                                case "MIN5":
                                case 4:
                                    t.freq[r] = 4;
                                    break;
                                case "MIN15":
                                case 5:
                                    t.freq[r] = 5;
                                    break;
                                case "MIN30":
                                case 6:
                                    t.freq[r] = 6;
                                    break;
                                case "MIN60":
                                case 7:
                                    t.freq[r] = 7;
                                    break;
                                case "MIN120":
                                case 8:
                                    t.freq[r] = 8;
                                    break;
                                case "MIN240":
                                case 9:
                                    t.freq[r] = 9;
                                    break;
                                case "DAY1":
                                case 10:
                                    t.freq[r] = 10;
                                    break;
                                case "WEEK1":
                                case 11:
                                    t.freq[r] = 11;
                                    break;
                                case "MONTH1":
                                case 12:
                                    t.freq[r] = 12
                                }
                        }
                        if (null != e.queryCondition) {
                            if ("object" != typeof e.queryCondition)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationRequest.queryCondition: object expected");
                            t.queryCondition = $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition.fromObject(e.queryCondition)
                        }
                        if (null != e.subscribeFlag && (t.subscribeFlag = 0 | e.subscribeFlag),
                        null != e.auth) {
                            if ("object" != typeof e.auth)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationRequest.auth: object expected");
                            t.auth = $root.jadegold.msg.quotation.pbv2.AuthReq.fromObject(e.auth)
                        }
                        return t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if ((t.arrays || t.defaults) && (r.codes = [],
                        r.freq = []),
                        t.defaults && (r.queryCondition = null,
                        r.subscribeFlag = 0,
                        r.auth = null),
                        e.codes && e.codes.length) {
                            r.codes = [];
                            for (let t = 0; t < e.codes.length; ++t)
                                r.codes[t] = e.codes[t]
                        }
                        if (e.freq && e.freq.length) {
                            r.freq = [];
                            for (let o = 0; o < e.freq.length; ++o)
                                r.freq[o] = t.enums === String ? $root.jadegold.msg.quotation.pbv2.QuotationFreq[e.freq[o]] : e.freq[o]
                        }
                        return null != e.queryCondition && e.hasOwnProperty("queryCondition") && (r.queryCondition = $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition.toObject(e.queryCondition, t)),
                        null != e.subscribeFlag && e.hasOwnProperty("subscribeFlag") && (r.subscribeFlag = e.subscribeFlag),
                        null != e.auth && e.hasOwnProperty("auth") && (r.auth = $root.jadegold.msg.quotation.pbv2.AuthReq.toObject(e.auth, t)),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.QuotationResponse = function() {
                    function e(e) {
                        if (this.quotation = [],
                        e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.quotation = $util.emptyArray,
                    e.prototype.earliestfreq = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.errCode = 0,
                    e.prototype.auth = null,
                    e.prototype.tradeStatus = null,
                    e.prototype.msg = "",
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        if (t || (t = $Writer.create()),
                        null != e.quotation && e.quotation.length)
                            for (let r = 0; r < e.quotation.length; ++r)
                                $root.jadegold.msg.quotation.pbv2.QuotationField.encode(e.quotation[r], t.uint32(10).fork()).ldelim();
                        return null != e.earliestfreq && Object.hasOwnProperty.call(e, "earliestfreq") && t.uint32(16).int64(e.earliestfreq),
                        null != e.errCode && Object.hasOwnProperty.call(e, "errCode") && t.uint32(24).sint32(e.errCode),
                        null != e.auth && Object.hasOwnProperty.call(e, "auth") && $root.jadegold.msg.quotation.pbv2.AuthResp.encode(e.auth, t.uint32(42).fork()).ldelim(),
                        null != e.tradeStatus && Object.hasOwnProperty.call(e, "tradeStatus") && $root.jadegold.msg.quotation.pbv2.TradeStatusMsg.encode(e.tradeStatus, t.uint32(50).fork()).ldelim(),
                        null != e.msg && Object.hasOwnProperty.call(e, "msg") && t.uint32(130).string(e.msg),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.QuotationResponse;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.quotation && o.quotation.length || (o.quotation = []),
                                o.quotation.push($root.jadegold.msg.quotation.pbv2.QuotationField.decode(e, e.uint32()));
                                break;
                            case 2:
                                o.earliestfreq = e.int64();
                                break;
                            case 3:
                                o.errCode = e.sint32();
                                break;
                            case 5:
                                o.auth = $root.jadegold.msg.quotation.pbv2.AuthResp.decode(e, e.uint32());
                                break;
                            case 6:
                                o.tradeStatus = $root.jadegold.msg.quotation.pbv2.TradeStatusMsg.decode(e, e.uint32());
                                break;
                            case 16:
                                o.msg = e.string();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.quotation && e.hasOwnProperty("quotation")) {
                            if (!Array.isArray(e.quotation))
                                return "quotation: array expected";
                            for (let t = 0; t < e.quotation.length; ++t) {
                                let r = $root.jadegold.msg.quotation.pbv2.QuotationField.verify(e.quotation[t]);
                                if (r)
                                    return "quotation." + r
                            }
                        }
                        if (null != e.earliestfreq && e.hasOwnProperty("earliestfreq") && !($util.isInteger(e.earliestfreq) || e.earliestfreq && $util.isInteger(e.earliestfreq.low) && $util.isInteger(e.earliestfreq.high)))
                            return "earliestfreq: integer|Long expected";
                        if (null != e.errCode && e.hasOwnProperty("errCode") && !$util.isInteger(e.errCode))
                            return "errCode: integer expected";
                        if (null != e.auth && e.hasOwnProperty("auth")) {
                            let t = $root.jadegold.msg.quotation.pbv2.AuthResp.verify(e.auth);
                            if (t)
                                return "auth." + t
                        }
                        if (null != e.tradeStatus && e.hasOwnProperty("tradeStatus")) {
                            let t = $root.jadegold.msg.quotation.pbv2.TradeStatusMsg.verify(e.tradeStatus);
                            if (t)
                                return "tradeStatus." + t
                        }
                        return null != e.msg && e.hasOwnProperty("msg") && !$util.isString(e.msg) ? "msg: string expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.QuotationResponse)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.QuotationResponse;
                        if (e.quotation) {
                            if (!Array.isArray(e.quotation))
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationResponse.quotation: array expected");
                            t.quotation = [];
                            for (let r = 0; r < e.quotation.length; ++r) {
                                if ("object" != typeof e.quotation[r])
                                    throw TypeError(".jadegold.msg.quotation.pbv2.QuotationResponse.quotation: object expected");
                                t.quotation[r] = $root.jadegold.msg.quotation.pbv2.QuotationField.fromObject(e.quotation[r])
                            }
                        }
                        if (null != e.earliestfreq && ($util.Long ? (t.earliestfreq = $util.Long.fromValue(e.earliestfreq)).unsigned = !1 : "string" == typeof e.earliestfreq ? t.earliestfreq = parseInt(e.earliestfreq, 10) : "number" == typeof e.earliestfreq ? t.earliestfreq = e.earliestfreq : "object" == typeof e.earliestfreq && (t.earliestfreq = new $util.LongBits(e.earliestfreq.low >>> 0,e.earliestfreq.high >>> 0).toNumber())),
                        null != e.errCode && (t.errCode = 0 | e.errCode),
                        null != e.auth) {
                            if ("object" != typeof e.auth)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationResponse.auth: object expected");
                            t.auth = $root.jadegold.msg.quotation.pbv2.AuthResp.fromObject(e.auth)
                        }
                        if (null != e.tradeStatus) {
                            if ("object" != typeof e.tradeStatus)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationResponse.tradeStatus: object expected");
                            t.tradeStatus = $root.jadegold.msg.quotation.pbv2.TradeStatusMsg.fromObject(e.tradeStatus)
                        }
                        return null != e.msg && (t.msg = String(e.msg)),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if ((t.arrays || t.defaults) && (r.quotation = []),
                        t.defaults) {
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.earliestfreq = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.earliestfreq = t.longs === String ? "0" : 0;
                            r.errCode = 0,
                            r.auth = null,
                            r.tradeStatus = null,
                            r.msg = ""
                        }
                        if (e.quotation && e.quotation.length) {
                            r.quotation = [];
                            for (let o = 0; o < e.quotation.length; ++o)
                                r.quotation[o] = $root.jadegold.msg.quotation.pbv2.QuotationField.toObject(e.quotation[o], t)
                        }
                        return null != e.earliestfreq && e.hasOwnProperty("earliestfreq") && ("number" == typeof e.earliestfreq ? r.earliestfreq = t.longs === String ? String(e.earliestfreq) : e.earliestfreq : r.earliestfreq = t.longs === String ? $util.Long.prototype.toString.call(e.earliestfreq) : t.longs === Number ? new $util.LongBits(e.earliestfreq.low >>> 0,e.earliestfreq.high >>> 0).toNumber() : e.earliestfreq),
                        null != e.errCode && e.hasOwnProperty("errCode") && (r.errCode = e.errCode),
                        null != e.auth && e.hasOwnProperty("auth") && (r.auth = $root.jadegold.msg.quotation.pbv2.AuthResp.toObject(e.auth, t)),
                        null != e.tradeStatus && e.hasOwnProperty("tradeStatus") && (r.tradeStatus = $root.jadegold.msg.quotation.pbv2.TradeStatusMsg.toObject(e.tradeStatus, t)),
                        null != e.msg && e.hasOwnProperty("msg") && (r.msg = e.msg),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.TradeStatus = function() {
                    const e = {}
                      , t = Object.create(e);
                    return t[e[0] = "INIT"] = 0,
                    t[e[7] = "INIT_FINISH"] = 7,
                    t[e[10] = "OPEN"] = 10,
                    t[e[20] = "CALL_AUCTION"] = 20,
                    t[e[27] = "CALL_AUCTION_FINISH"] = 27,
                    t[e[30] = "TRADING"] = 30,
                    t[e[40] = "PAUSE"] = 40,
                    t[e[50] = "DELIVERY_CALL"] = 50,
                    t[e[60] = "DELIVERY"] = 60,
                    t[e[67] = "DELIVERY_FINISH"] = 67,
                    t[e[70] = "NEUTRAL_WAREHOUSE"] = 70,
                    t[e[99] = "CLOSE"] = 99,
                    t
                }(),
                e.TradeStatusMsg = function() {
                    function e(e) {
                        if (this.codes = [],
                        this.commodites = [],
                        this.markets = [],
                        e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.tradeStatus = 0,
                    e.prototype.codes = $util.emptyArray,
                    e.prototype.commodites = $util.emptyArray,
                    e.prototype.markets = $util.emptyArray,
                    e.prototype.quotetime = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        if (t || (t = $Writer.create()),
                        null != e.tradeStatus && Object.hasOwnProperty.call(e, "tradeStatus") && t.uint32(8).int32(e.tradeStatus),
                        null != e.codes && e.codes.length)
                            for (let r = 0; r < e.codes.length; ++r)
                                t.uint32(18).string(e.codes[r]);
                        if (null != e.commodites && e.commodites.length)
                            for (let r = 0; r < e.commodites.length; ++r)
                                t.uint32(26).string(e.commodites[r]);
                        if (null != e.markets && e.markets.length)
                            for (let r = 0; r < e.markets.length; ++r)
                                t.uint32(34).string(e.markets[r]);
                        return null != e.quotetime && Object.hasOwnProperty.call(e, "quotetime") && t.uint32(40).int64(e.quotetime),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.TradeStatusMsg;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.tradeStatus = e.int32();
                                break;
                            case 2:
                                o.codes && o.codes.length || (o.codes = []),
                                o.codes.push(e.string());
                                break;
                            case 3:
                                o.commodites && o.commodites.length || (o.commodites = []),
                                o.commodites.push(e.string());
                                break;
                            case 4:
                                o.markets && o.markets.length || (o.markets = []),
                                o.markets.push(e.string());
                                break;
                            case 5:
                                o.quotetime = e.int64();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.tradeStatus && e.hasOwnProperty("tradeStatus"))
                            switch (e.tradeStatus) {
                            default:
                                return "tradeStatus: enum value expected";
                            case 0:
                            case 7:
                            case 10:
                            case 20:
                            case 27:
                            case 30:
                            case 40:
                            case 50:
                            case 60:
                            case 67:
                            case 70:
                            case 99:
                            }
                        if (null != e.codes && e.hasOwnProperty("codes")) {
                            if (!Array.isArray(e.codes))
                                return "codes: array expected";
                            for (let t = 0; t < e.codes.length; ++t)
                                if (!$util.isString(e.codes[t]))
                                    return "codes: string[] expected"
                        }
                        if (null != e.commodites && e.hasOwnProperty("commodites")) {
                            if (!Array.isArray(e.commodites))
                                return "commodites: array expected";
                            for (let t = 0; t < e.commodites.length; ++t)
                                if (!$util.isString(e.commodites[t]))
                                    return "commodites: string[] expected"
                        }
                        if (null != e.markets && e.hasOwnProperty("markets")) {
                            if (!Array.isArray(e.markets))
                                return "markets: array expected";
                            for (let t = 0; t < e.markets.length; ++t)
                                if (!$util.isString(e.markets[t]))
                                    return "markets: string[] expected"
                        }
                        return null != e.quotetime && e.hasOwnProperty("quotetime") && !($util.isInteger(e.quotetime) || e.quotetime && $util.isInteger(e.quotetime.low) && $util.isInteger(e.quotetime.high)) ? "quotetime: integer|Long expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.TradeStatusMsg)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.TradeStatusMsg;
                        switch (e.tradeStatus) {
                        case "INIT":
                        case 0:
                            t.tradeStatus = 0;
                            break;
                        case "INIT_FINISH":
                        case 7:
                            t.tradeStatus = 7;
                            break;
                        case "OPEN":
                        case 10:
                            t.tradeStatus = 10;
                            break;
                        case "CALL_AUCTION":
                        case 20:
                            t.tradeStatus = 20;
                            break;
                        case "CALL_AUCTION_FINISH":
                        case 27:
                            t.tradeStatus = 27;
                            break;
                        case "TRADING":
                        case 30:
                            t.tradeStatus = 30;
                            break;
                        case "PAUSE":
                        case 40:
                            t.tradeStatus = 40;
                            break;
                        case "DELIVERY_CALL":
                        case 50:
                            t.tradeStatus = 50;
                            break;
                        case "DELIVERY":
                        case 60:
                            t.tradeStatus = 60;
                            break;
                        case "DELIVERY_FINISH":
                        case 67:
                            t.tradeStatus = 67;
                            break;
                        case "NEUTRAL_WAREHOUSE":
                        case 70:
                            t.tradeStatus = 70;
                            break;
                        case "CLOSE":
                        case 99:
                            t.tradeStatus = 99
                        }
                        if (e.codes) {
                            if (!Array.isArray(e.codes))
                                throw TypeError(".jadegold.msg.quotation.pbv2.TradeStatusMsg.codes: array expected");
                            t.codes = [];
                            for (let r = 0; r < e.codes.length; ++r)
                                t.codes[r] = String(e.codes[r])
                        }
                        if (e.commodites) {
                            if (!Array.isArray(e.commodites))
                                throw TypeError(".jadegold.msg.quotation.pbv2.TradeStatusMsg.commodites: array expected");
                            t.commodites = [];
                            for (let r = 0; r < e.commodites.length; ++r)
                                t.commodites[r] = String(e.commodites[r])
                        }
                        if (e.markets) {
                            if (!Array.isArray(e.markets))
                                throw TypeError(".jadegold.msg.quotation.pbv2.TradeStatusMsg.markets: array expected");
                            t.markets = [];
                            for (let r = 0; r < e.markets.length; ++r)
                                t.markets[r] = String(e.markets[r])
                        }
                        return null != e.quotetime && ($util.Long ? (t.quotetime = $util.Long.fromValue(e.quotetime)).unsigned = !1 : "string" == typeof e.quotetime ? t.quotetime = parseInt(e.quotetime, 10) : "number" == typeof e.quotetime ? t.quotetime = e.quotetime : "object" == typeof e.quotetime && (t.quotetime = new $util.LongBits(e.quotetime.low >>> 0,e.quotetime.high >>> 0).toNumber())),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if ((t.arrays || t.defaults) && (r.codes = [],
                        r.commodites = [],
                        r.markets = []),
                        t.defaults)
                            if (r.tradeStatus = t.enums === String ? "INIT" : 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.quotetime = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.quotetime = t.longs === String ? "0" : 0;
                        if (null != e.tradeStatus && e.hasOwnProperty("tradeStatus") && (r.tradeStatus = t.enums === String ? $root.jadegold.msg.quotation.pbv2.TradeStatus[e.tradeStatus] : e.tradeStatus),
                        e.codes && e.codes.length) {
                            r.codes = [];
                            for (let t = 0; t < e.codes.length; ++t)
                                r.codes[t] = e.codes[t]
                        }
                        if (e.commodites && e.commodites.length) {
                            r.commodites = [];
                            for (let t = 0; t < e.commodites.length; ++t)
                                r.commodites[t] = e.commodites[t]
                        }
                        if (e.markets && e.markets.length) {
                            r.markets = [];
                            for (let t = 0; t < e.markets.length; ++t)
                                r.markets[t] = e.markets[t]
                        }
                        return null != e.quotetime && e.hasOwnProperty("quotetime") && ("number" == typeof e.quotetime ? r.quotetime = t.longs === String ? String(e.quotetime) : e.quotetime : r.quotetime = t.longs === String ? $util.Long.prototype.toString.call(e.quotetime) : t.longs === Number ? new $util.LongBits(e.quotetime.low >>> 0,e.quotetime.high >>> 0).toNumber() : e.quotetime),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.QuoteQueryCondition = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.size = 0,
                    e.prototype.begintime = 0,
                    e.prototype.endtime = 0,
                    e.prototype.infoDays = 0,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.size && Object.hasOwnProperty.call(e, "size") && t.uint32(8).int32(e.size),
                        null != e.begintime && Object.hasOwnProperty.call(e, "begintime") && t.uint32(21).fixed32(e.begintime),
                        null != e.endtime && Object.hasOwnProperty.call(e, "endtime") && t.uint32(29).fixed32(e.endtime),
                        null != e.infoDays && Object.hasOwnProperty.call(e, "infoDays") && t.uint32(32).int32(e.infoDays),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.size = e.int32();
                                break;
                            case 2:
                                o.begintime = e.fixed32();
                                break;
                            case 3:
                                o.endtime = e.fixed32();
                                break;
                            case 4:
                                o.infoDays = e.int32();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        return "object" != typeof e || null === e ? "object expected" : null != e.size && e.hasOwnProperty("size") && !$util.isInteger(e.size) ? "size: integer expected" : null != e.begintime && e.hasOwnProperty("begintime") && !$util.isInteger(e.begintime) ? "begintime: integer expected" : null != e.endtime && e.hasOwnProperty("endtime") && !$util.isInteger(e.endtime) ? "endtime: integer expected" : null != e.infoDays && e.hasOwnProperty("infoDays") && !$util.isInteger(e.infoDays) ? "infoDays: integer expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.QuoteQueryCondition;
                        return null != e.size && (t.size = 0 | e.size),
                        null != e.begintime && (t.begintime = e.begintime >>> 0),
                        null != e.endtime && (t.endtime = e.endtime >>> 0),
                        null != e.infoDays && (t.infoDays = 0 | e.infoDays),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        return t.defaults && (r.size = 0,
                        r.begintime = 0,
                        r.endtime = 0,
                        r.infoDays = 0),
                        null != e.size && e.hasOwnProperty("size") && (r.size = e.size),
                        null != e.begintime && e.hasOwnProperty("begintime") && (r.begintime = e.begintime),
                        null != e.endtime && e.hasOwnProperty("endtime") && (r.endtime = e.endtime),
                        null != e.infoDays && e.hasOwnProperty("infoDays") && (r.infoDays = e.infoDays),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.AuthReq = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.apptype = "",
                    e.prototype.token = $util.newBuffer([]),
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.apptype && Object.hasOwnProperty.call(e, "apptype") && t.uint32(10).string(e.apptype),
                        null != e.token && Object.hasOwnProperty.call(e, "token") && t.uint32(18).bytes(e.token),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.AuthReq;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.apptype = e.string();
                                break;
                            case 2:
                                o.token = e.bytes();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        return "object" != typeof e || null === e ? "object expected" : null != e.apptype && e.hasOwnProperty("apptype") && !$util.isString(e.apptype) ? "apptype: string expected" : null != e.token && e.hasOwnProperty("token") && !(e.token && "number" == typeof e.token.length || $util.isString(e.token)) ? "token: buffer expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.AuthReq)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.AuthReq;
                        return null != e.apptype && (t.apptype = String(e.apptype)),
                        null != e.token && ("string" == typeof e.token ? $util.base64.decode(e.token, t.token = $util.newBuffer($util.base64.length(e.token)), 0) : e.token.length && (t.token = e.token)),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        return t.defaults && (r.apptype = "",
                        t.bytes === String ? r.token = "" : (r.token = [],
                        t.bytes !== Array && (r.token = $util.newBuffer(r.token)))),
                        null != e.apptype && e.hasOwnProperty("apptype") && (r.apptype = e.apptype),
                        null != e.token && e.hasOwnProperty("token") && (r.token = t.bytes === String ? $util.base64.encode(e.token, 0, e.token.length) : t.bytes === Array ? Array.prototype.slice.call(e.token) : e.token),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.AuthResp = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.serverInfo = $util.newBuffer([]),
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.serverInfo && Object.hasOwnProperty.call(e, "serverInfo") && t.uint32(10).bytes(e.serverInfo),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.AuthResp;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            if (t >>> 3 == 1)
                                o.serverInfo = e.bytes();
                            else
                                e.skipType(7 & t)
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        return "object" != typeof e || null === e ? "object expected" : null != e.serverInfo && e.hasOwnProperty("serverInfo") && !(e.serverInfo && "number" == typeof e.serverInfo.length || $util.isString(e.serverInfo)) ? "serverInfo: buffer expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.AuthResp)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.AuthResp;
                        return null != e.serverInfo && ("string" == typeof e.serverInfo ? $util.base64.decode(e.serverInfo, t.serverInfo = $util.newBuffer($util.base64.length(e.serverInfo)), 0) : e.serverInfo.length && (t.serverInfo = e.serverInfo)),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        return t.defaults && (t.bytes === String ? r.serverInfo = "" : (r.serverInfo = [],
                        t.bytes !== Array && (r.serverInfo = $util.newBuffer(r.serverInfo)))),
                        null != e.serverInfo && e.hasOwnProperty("serverInfo") && (r.serverInfo = t.bytes === String ? $util.base64.encode(e.serverInfo, 0, e.serverInfo.length) : t.bytes === Array ? Array.prototype.slice.call(e.serverInfo) : e.serverInfo),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.QuotationFreq = function() {
                    const e = {}
                      , t = Object.create(e);
                    return t[e[0] = "REALTIME"] = 0,
                    t[e[1] = "INFO"] = 1,
                    t[e[2] = "TICK"] = 2,
                    t[e[3] = "MIN1"] = 3,
                    t[e[4] = "MIN5"] = 4,
                    t[e[5] = "MIN15"] = 5,
                    t[e[6] = "MIN30"] = 6,
                    t[e[7] = "MIN60"] = 7,
                    t[e[8] = "MIN120"] = 8,
                    t[e[9] = "MIN240"] = 9,
                    t[e[10] = "DAY1"] = 10,
                    t[e[11] = "WEEK1"] = 11,
                    t[e[12] = "MONTH1"] = 12,
                    t
                }(),
                e.RealtimeField = function() {
                    function e(e) {
                        if (this.askPrice = [],
                        this.askVol = [],
                        this.bidPrice = [],
                        this.bidVol = [],
                        e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.last = 0,
                    e.prototype.askPrice = $util.emptyArray,
                    e.prototype.askVol = $util.emptyArray,
                    e.prototype.bidPrice = $util.emptyArray,
                    e.prototype.bidVol = $util.emptyArray,
                    e.prototype.tag = 0,
                    e.prototype.posiDelta = 0,
                    e.prototype.highLimit = 0,
                    e.prototype.lowLimit = 0,
                    e.prototype.tickVolume = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.updown = 0,
                    e.prototype.updownRate = 0,
                    e.prototype.average = 0,
                    e.prototype.tradeday = 0,
                    e.prototype.infoVolume = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        if (t || (t = $Writer.create()),
                        null != e.last && Object.hasOwnProperty.call(e, "last") && t.uint32(9).double(e.last),
                        null != e.askPrice && e.askPrice.length) {
                            t.uint32(18).fork();
                            for (let r = 0; r < e.askPrice.length; ++r)
                                t.double(e.askPrice[r]);
                            t.ldelim()
                        }
                        if (null != e.askVol && e.askVol.length) {
                            t.uint32(26).fork();
                            for (let r = 0; r < e.askVol.length; ++r)
                                t.int64(e.askVol[r]);
                            t.ldelim()
                        }
                        if (null != e.bidPrice && e.bidPrice.length) {
                            t.uint32(34).fork();
                            for (let r = 0; r < e.bidPrice.length; ++r)
                                t.double(e.bidPrice[r]);
                            t.ldelim()
                        }
                        if (null != e.bidVol && e.bidVol.length) {
                            t.uint32(42).fork();
                            for (let r = 0; r < e.bidVol.length; ++r)
                                t.int64(e.bidVol[r]);
                            t.ldelim()
                        }
                        return null != e.tag && Object.hasOwnProperty.call(e, "tag") && t.uint32(48).int32(e.tag),
                        null != e.posiDelta && Object.hasOwnProperty.call(e, "posiDelta") && t.uint32(57).double(e.posiDelta),
                        null != e.highLimit && Object.hasOwnProperty.call(e, "highLimit") && t.uint32(65).double(e.highLimit),
                        null != e.lowLimit && Object.hasOwnProperty.call(e, "lowLimit") && t.uint32(73).double(e.lowLimit),
                        null != e.tickVolume && Object.hasOwnProperty.call(e, "tickVolume") && t.uint32(80).int64(e.tickVolume),
                        null != e.updown && Object.hasOwnProperty.call(e, "updown") && t.uint32(89).double(e.updown),
                        null != e.updownRate && Object.hasOwnProperty.call(e, "updownRate") && t.uint32(97).double(e.updownRate),
                        null != e.average && Object.hasOwnProperty.call(e, "average") && t.uint32(105).double(e.average),
                        null != e.tradeday && Object.hasOwnProperty.call(e, "tradeday") && t.uint32(112).int32(e.tradeday),
                        null != e.infoVolume && Object.hasOwnProperty.call(e, "infoVolume") && t.uint32(120).int64(e.infoVolume),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.RealtimeField;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.last = e.double();
                                break;
                            case 2:
                                if (o.askPrice && o.askPrice.length || (o.askPrice = []),
                                2 == (7 & t)) {
                                    let t = e.uint32() + e.pos;
                                    for (; e.pos < t; )
                                        o.askPrice.push(e.double())
                                } else
                                    o.askPrice.push(e.double());
                                break;
                            case 3:
                                if (o.askVol && o.askVol.length || (o.askVol = []),
                                2 == (7 & t)) {
                                    let t = e.uint32() + e.pos;
                                    for (; e.pos < t; )
                                        o.askVol.push(e.int64())
                                } else
                                    o.askVol.push(e.int64());
                                break;
                            case 4:
                                if (o.bidPrice && o.bidPrice.length || (o.bidPrice = []),
                                2 == (7 & t)) {
                                    let t = e.uint32() + e.pos;
                                    for (; e.pos < t; )
                                        o.bidPrice.push(e.double())
                                } else
                                    o.bidPrice.push(e.double());
                                break;
                            case 5:
                                if (o.bidVol && o.bidVol.length || (o.bidVol = []),
                                2 == (7 & t)) {
                                    let t = e.uint32() + e.pos;
                                    for (; e.pos < t; )
                                        o.bidVol.push(e.int64())
                                } else
                                    o.bidVol.push(e.int64());
                                break;
                            case 6:
                                o.tag = e.int32();
                                break;
                            case 7:
                                o.posiDelta = e.double();
                                break;
                            case 8:
                                o.highLimit = e.double();
                                break;
                            case 9:
                                o.lowLimit = e.double();
                                break;
                            case 10:
                                o.tickVolume = e.int64();
                                break;
                            case 11:
                                o.updown = e.double();
                                break;
                            case 12:
                                o.updownRate = e.double();
                                break;
                            case 13:
                                o.average = e.double();
                                break;
                            case 14:
                                o.tradeday = e.int32();
                                break;
                            case 15:
                                o.infoVolume = e.int64();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.last && e.hasOwnProperty("last") && "number" != typeof e.last)
                            return "last: number expected";
                        if (null != e.askPrice && e.hasOwnProperty("askPrice")) {
                            if (!Array.isArray(e.askPrice))
                                return "askPrice: array expected";
                            for (let t = 0; t < e.askPrice.length; ++t)
                                if ("number" != typeof e.askPrice[t])
                                    return "askPrice: number[] expected"
                        }
                        if (null != e.askVol && e.hasOwnProperty("askVol")) {
                            if (!Array.isArray(e.askVol))
                                return "askVol: array expected";
                            for (let t = 0; t < e.askVol.length; ++t)
                                if (!($util.isInteger(e.askVol[t]) || e.askVol[t] && $util.isInteger(e.askVol[t].low) && $util.isInteger(e.askVol[t].high)))
                                    return "askVol: integer|Long[] expected"
                        }
                        if (null != e.bidPrice && e.hasOwnProperty("bidPrice")) {
                            if (!Array.isArray(e.bidPrice))
                                return "bidPrice: array expected";
                            for (let t = 0; t < e.bidPrice.length; ++t)
                                if ("number" != typeof e.bidPrice[t])
                                    return "bidPrice: number[] expected"
                        }
                        if (null != e.bidVol && e.hasOwnProperty("bidVol")) {
                            if (!Array.isArray(e.bidVol))
                                return "bidVol: array expected";
                            for (let t = 0; t < e.bidVol.length; ++t)
                                if (!($util.isInteger(e.bidVol[t]) || e.bidVol[t] && $util.isInteger(e.bidVol[t].low) && $util.isInteger(e.bidVol[t].high)))
                                    return "bidVol: integer|Long[] expected"
                        }
                        return null != e.tag && e.hasOwnProperty("tag") && !$util.isInteger(e.tag) ? "tag: integer expected" : null != e.posiDelta && e.hasOwnProperty("posiDelta") && "number" != typeof e.posiDelta ? "posiDelta: number expected" : null != e.highLimit && e.hasOwnProperty("highLimit") && "number" != typeof e.highLimit ? "highLimit: number expected" : null != e.lowLimit && e.hasOwnProperty("lowLimit") && "number" != typeof e.lowLimit ? "lowLimit: number expected" : null != e.tickVolume && e.hasOwnProperty("tickVolume") && !($util.isInteger(e.tickVolume) || e.tickVolume && $util.isInteger(e.tickVolume.low) && $util.isInteger(e.tickVolume.high)) ? "tickVolume: integer|Long expected" : null != e.updown && e.hasOwnProperty("updown") && "number" != typeof e.updown ? "updown: number expected" : null != e.updownRate && e.hasOwnProperty("updownRate") && "number" != typeof e.updownRate ? "updownRate: number expected" : null != e.average && e.hasOwnProperty("average") && "number" != typeof e.average ? "average: number expected" : null != e.tradeday && e.hasOwnProperty("tradeday") && !$util.isInteger(e.tradeday) ? "tradeday: integer expected" : null != e.infoVolume && e.hasOwnProperty("infoVolume") && !($util.isInteger(e.infoVolume) || e.infoVolume && $util.isInteger(e.infoVolume.low) && $util.isInteger(e.infoVolume.high)) ? "infoVolume: integer|Long expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.RealtimeField)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.RealtimeField;
                        if (null != e.last && (t.last = Number(e.last)),
                        e.askPrice) {
                            if (!Array.isArray(e.askPrice))
                                throw TypeError(".jadegold.msg.quotation.pbv2.RealtimeField.askPrice: array expected");
                            t.askPrice = [];
                            for (let r = 0; r < e.askPrice.length; ++r)
                                t.askPrice[r] = Number(e.askPrice[r])
                        }
                        if (e.askVol) {
                            if (!Array.isArray(e.askVol))
                                throw TypeError(".jadegold.msg.quotation.pbv2.RealtimeField.askVol: array expected");
                            t.askVol = [];
                            for (let r = 0; r < e.askVol.length; ++r)
                                $util.Long ? (t.askVol[r] = $util.Long.fromValue(e.askVol[r])).unsigned = !1 : "string" == typeof e.askVol[r] ? t.askVol[r] = parseInt(e.askVol[r], 10) : "number" == typeof e.askVol[r] ? t.askVol[r] = e.askVol[r] : "object" == typeof e.askVol[r] && (t.askVol[r] = new $util.LongBits(e.askVol[r].low >>> 0,e.askVol[r].high >>> 0).toNumber())
                        }
                        if (e.bidPrice) {
                            if (!Array.isArray(e.bidPrice))
                                throw TypeError(".jadegold.msg.quotation.pbv2.RealtimeField.bidPrice: array expected");
                            t.bidPrice = [];
                            for (let r = 0; r < e.bidPrice.length; ++r)
                                t.bidPrice[r] = Number(e.bidPrice[r])
                        }
                        if (e.bidVol) {
                            if (!Array.isArray(e.bidVol))
                                throw TypeError(".jadegold.msg.quotation.pbv2.RealtimeField.bidVol: array expected");
                            t.bidVol = [];
                            for (let r = 0; r < e.bidVol.length; ++r)
                                $util.Long ? (t.bidVol[r] = $util.Long.fromValue(e.bidVol[r])).unsigned = !1 : "string" == typeof e.bidVol[r] ? t.bidVol[r] = parseInt(e.bidVol[r], 10) : "number" == typeof e.bidVol[r] ? t.bidVol[r] = e.bidVol[r] : "object" == typeof e.bidVol[r] && (t.bidVol[r] = new $util.LongBits(e.bidVol[r].low >>> 0,e.bidVol[r].high >>> 0).toNumber())
                        }
                        return null != e.tag && (t.tag = 0 | e.tag),
                        null != e.posiDelta && (t.posiDelta = Number(e.posiDelta)),
                        null != e.highLimit && (t.highLimit = Number(e.highLimit)),
                        null != e.lowLimit && (t.lowLimit = Number(e.lowLimit)),
                        null != e.tickVolume && ($util.Long ? (t.tickVolume = $util.Long.fromValue(e.tickVolume)).unsigned = !1 : "string" == typeof e.tickVolume ? t.tickVolume = parseInt(e.tickVolume, 10) : "number" == typeof e.tickVolume ? t.tickVolume = e.tickVolume : "object" == typeof e.tickVolume && (t.tickVolume = new $util.LongBits(e.tickVolume.low >>> 0,e.tickVolume.high >>> 0).toNumber())),
                        null != e.updown && (t.updown = Number(e.updown)),
                        null != e.updownRate && (t.updownRate = Number(e.updownRate)),
                        null != e.average && (t.average = Number(e.average)),
                        null != e.tradeday && (t.tradeday = 0 | e.tradeday),
                        null != e.infoVolume && ($util.Long ? (t.infoVolume = $util.Long.fromValue(e.infoVolume)).unsigned = !1 : "string" == typeof e.infoVolume ? t.infoVolume = parseInt(e.infoVolume, 10) : "number" == typeof e.infoVolume ? t.infoVolume = e.infoVolume : "object" == typeof e.infoVolume && (t.infoVolume = new $util.LongBits(e.infoVolume.low >>> 0,e.infoVolume.high >>> 0).toNumber())),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if ((t.arrays || t.defaults) && (r.askPrice = [],
                        r.askVol = [],
                        r.bidPrice = [],
                        r.bidVol = []),
                        t.defaults) {
                            if (r.last = 0,
                            r.tag = 0,
                            r.posiDelta = 0,
                            r.highLimit = 0,
                            r.lowLimit = 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.tickVolume = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.tickVolume = t.longs === String ? "0" : 0;
                            if (r.updown = 0,
                            r.updownRate = 0,
                            r.average = 0,
                            r.tradeday = 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.infoVolume = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.infoVolume = t.longs === String ? "0" : 0
                        }
                        if (null != e.last && e.hasOwnProperty("last") && (r.last = t.json && !isFinite(e.last) ? String(e.last) : e.last),
                        e.askPrice && e.askPrice.length) {
                            r.askPrice = [];
                            for (let o = 0; o < e.askPrice.length; ++o)
                                r.askPrice[o] = t.json && !isFinite(e.askPrice[o]) ? String(e.askPrice[o]) : e.askPrice[o]
                        }
                        if (e.askVol && e.askVol.length) {
                            r.askVol = [];
                            for (let o = 0; o < e.askVol.length; ++o)
                                "number" == typeof e.askVol[o] ? r.askVol[o] = t.longs === String ? String(e.askVol[o]) : e.askVol[o] : r.askVol[o] = t.longs === String ? $util.Long.prototype.toString.call(e.askVol[o]) : t.longs === Number ? new $util.LongBits(e.askVol[o].low >>> 0,e.askVol[o].high >>> 0).toNumber() : e.askVol[o]
                        }
                        if (e.bidPrice && e.bidPrice.length) {
                            r.bidPrice = [];
                            for (let o = 0; o < e.bidPrice.length; ++o)
                                r.bidPrice[o] = t.json && !isFinite(e.bidPrice[o]) ? String(e.bidPrice[o]) : e.bidPrice[o]
                        }
                        if (e.bidVol && e.bidVol.length) {
                            r.bidVol = [];
                            for (let o = 0; o < e.bidVol.length; ++o)
                                "number" == typeof e.bidVol[o] ? r.bidVol[o] = t.longs === String ? String(e.bidVol[o]) : e.bidVol[o] : r.bidVol[o] = t.longs === String ? $util.Long.prototype.toString.call(e.bidVol[o]) : t.longs === Number ? new $util.LongBits(e.bidVol[o].low >>> 0,e.bidVol[o].high >>> 0).toNumber() : e.bidVol[o]
                        }
                        return null != e.tag && e.hasOwnProperty("tag") && (r.tag = e.tag),
                        null != e.posiDelta && e.hasOwnProperty("posiDelta") && (r.posiDelta = t.json && !isFinite(e.posiDelta) ? String(e.posiDelta) : e.posiDelta),
                        null != e.highLimit && e.hasOwnProperty("highLimit") && (r.highLimit = t.json && !isFinite(e.highLimit) ? String(e.highLimit) : e.highLimit),
                        null != e.lowLimit && e.hasOwnProperty("lowLimit") && (r.lowLimit = t.json && !isFinite(e.lowLimit) ? String(e.lowLimit) : e.lowLimit),
                        null != e.tickVolume && e.hasOwnProperty("tickVolume") && ("number" == typeof e.tickVolume ? r.tickVolume = t.longs === String ? String(e.tickVolume) : e.tickVolume : r.tickVolume = t.longs === String ? $util.Long.prototype.toString.call(e.tickVolume) : t.longs === Number ? new $util.LongBits(e.tickVolume.low >>> 0,e.tickVolume.high >>> 0).toNumber() : e.tickVolume),
                        null != e.updown && e.hasOwnProperty("updown") && (r.updown = t.json && !isFinite(e.updown) ? String(e.updown) : e.updown),
                        null != e.updownRate && e.hasOwnProperty("updownRate") && (r.updownRate = t.json && !isFinite(e.updownRate) ? String(e.updownRate) : e.updownRate),
                        null != e.average && e.hasOwnProperty("average") && (r.average = t.json && !isFinite(e.average) ? String(e.average) : e.average),
                        null != e.tradeday && e.hasOwnProperty("tradeday") && (r.tradeday = e.tradeday),
                        null != e.infoVolume && e.hasOwnProperty("infoVolume") && ("number" == typeof e.infoVolume ? r.infoVolume = t.longs === String ? String(e.infoVolume) : e.infoVolume : r.infoVolume = t.longs === String ? $util.Long.prototype.toString.call(e.infoVolume) : t.longs === Number ? new $util.LongBits(e.infoVolume.low >>> 0,e.infoVolume.high >>> 0).toNumber() : e.infoVolume),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.ExtraField = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.preSettle = 0,
                    e.prototype.sequenceNo = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.amplitude = 0,
                    e.prototype.currDelta = 0,
                    e.prototype.preDelta = 0,
                    e.prototype.market = "",
                    e.prototype.exchangeId = "",
                    e.prototype.prePosi = 0,
                    e.prototype.name = "",
                    e.prototype.commodityCode = "",
                    e.prototype.tradeStatus = 0,
                    e.prototype.openTime = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.closeTime = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.goldDelivery = null,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.preSettle && Object.hasOwnProperty.call(e, "preSettle") && t.uint32(9).double(e.preSettle),
                        null != e.sequenceNo && Object.hasOwnProperty.call(e, "sequenceNo") && t.uint32(16).int64(e.sequenceNo),
                        null != e.amplitude && Object.hasOwnProperty.call(e, "amplitude") && t.uint32(25).double(e.amplitude),
                        null != e.currDelta && Object.hasOwnProperty.call(e, "currDelta") && t.uint32(33).double(e.currDelta),
                        null != e.preDelta && Object.hasOwnProperty.call(e, "preDelta") && t.uint32(41).double(e.preDelta),
                        null != e.market && Object.hasOwnProperty.call(e, "market") && t.uint32(50).string(e.market),
                        null != e.exchangeId && Object.hasOwnProperty.call(e, "exchangeId") && t.uint32(58).string(e.exchangeId),
                        null != e.prePosi && Object.hasOwnProperty.call(e, "prePosi") && t.uint32(65).double(e.prePosi),
                        null != e.name && Object.hasOwnProperty.call(e, "name") && t.uint32(74).string(e.name),
                        null != e.commodityCode && Object.hasOwnProperty.call(e, "commodityCode") && t.uint32(82).string(e.commodityCode),
                        null != e.tradeStatus && Object.hasOwnProperty.call(e, "tradeStatus") && t.uint32(88).int32(e.tradeStatus),
                        null != e.openTime && Object.hasOwnProperty.call(e, "openTime") && t.uint32(136).int64(e.openTime),
                        null != e.closeTime && Object.hasOwnProperty.call(e, "closeTime") && t.uint32(144).int64(e.closeTime),
                        null != e.goldDelivery && Object.hasOwnProperty.call(e, "goldDelivery") && $root.jadegold.msg.quotation.pbv2.GoldDeliveryField.encode(e.goldDelivery, t.uint32(514).fork()).ldelim(),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.ExtraField;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.preSettle = e.double();
                                break;
                            case 2:
                                o.sequenceNo = e.int64();
                                break;
                            case 3:
                                o.amplitude = e.double();
                                break;
                            case 4:
                                o.currDelta = e.double();
                                break;
                            case 5:
                                o.preDelta = e.double();
                                break;
                            case 6:
                                o.market = e.string();
                                break;
                            case 7:
                                o.exchangeId = e.string();
                                break;
                            case 8:
                                o.prePosi = e.double();
                                break;
                            case 9:
                                o.name = e.string();
                                break;
                            case 10:
                                o.commodityCode = e.string();
                                break;
                            case 11:
                                o.tradeStatus = e.int32();
                                break;
                            case 17:
                                o.openTime = e.int64();
                                break;
                            case 18:
                                o.closeTime = e.int64();
                                break;
                            case 64:
                                o.goldDelivery = $root.jadegold.msg.quotation.pbv2.GoldDeliveryField.decode(e, e.uint32());
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.preSettle && e.hasOwnProperty("preSettle") && "number" != typeof e.preSettle)
                            return "preSettle: number expected";
                        if (null != e.sequenceNo && e.hasOwnProperty("sequenceNo") && !($util.isInteger(e.sequenceNo) || e.sequenceNo && $util.isInteger(e.sequenceNo.low) && $util.isInteger(e.sequenceNo.high)))
                            return "sequenceNo: integer|Long expected";
                        if (null != e.amplitude && e.hasOwnProperty("amplitude") && "number" != typeof e.amplitude)
                            return "amplitude: number expected";
                        if (null != e.currDelta && e.hasOwnProperty("currDelta") && "number" != typeof e.currDelta)
                            return "currDelta: number expected";
                        if (null != e.preDelta && e.hasOwnProperty("preDelta") && "number" != typeof e.preDelta)
                            return "preDelta: number expected";
                        if (null != e.market && e.hasOwnProperty("market") && !$util.isString(e.market))
                            return "market: string expected";
                        if (null != e.exchangeId && e.hasOwnProperty("exchangeId") && !$util.isString(e.exchangeId))
                            return "exchangeId: string expected";
                        if (null != e.prePosi && e.hasOwnProperty("prePosi") && "number" != typeof e.prePosi)
                            return "prePosi: number expected";
                        if (null != e.name && e.hasOwnProperty("name") && !$util.isString(e.name))
                            return "name: string expected";
                        if (null != e.commodityCode && e.hasOwnProperty("commodityCode") && !$util.isString(e.commodityCode))
                            return "commodityCode: string expected";
                        if (null != e.tradeStatus && e.hasOwnProperty("tradeStatus"))
                            switch (e.tradeStatus) {
                            default:
                                return "tradeStatus: enum value expected";
                            case 0:
                            case 7:
                            case 10:
                            case 20:
                            case 27:
                            case 30:
                            case 40:
                            case 50:
                            case 60:
                            case 67:
                            case 70:
                            case 99:
                            }
                        if (null != e.openTime && e.hasOwnProperty("openTime") && !($util.isInteger(e.openTime) || e.openTime && $util.isInteger(e.openTime.low) && $util.isInteger(e.openTime.high)))
                            return "openTime: integer|Long expected";
                        if (null != e.closeTime && e.hasOwnProperty("closeTime") && !($util.isInteger(e.closeTime) || e.closeTime && $util.isInteger(e.closeTime.low) && $util.isInteger(e.closeTime.high)))
                            return "closeTime: integer|Long expected";
                        if (null != e.goldDelivery && e.hasOwnProperty("goldDelivery")) {
                            let t = $root.jadegold.msg.quotation.pbv2.GoldDeliveryField.verify(e.goldDelivery);
                            if (t)
                                return "goldDelivery." + t
                        }
                        return null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.ExtraField)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.ExtraField;
                        switch (null != e.preSettle && (t.preSettle = Number(e.preSettle)),
                        null != e.sequenceNo && ($util.Long ? (t.sequenceNo = $util.Long.fromValue(e.sequenceNo)).unsigned = !1 : "string" == typeof e.sequenceNo ? t.sequenceNo = parseInt(e.sequenceNo, 10) : "number" == typeof e.sequenceNo ? t.sequenceNo = e.sequenceNo : "object" == typeof e.sequenceNo && (t.sequenceNo = new $util.LongBits(e.sequenceNo.low >>> 0,e.sequenceNo.high >>> 0).toNumber())),
                        null != e.amplitude && (t.amplitude = Number(e.amplitude)),
                        null != e.currDelta && (t.currDelta = Number(e.currDelta)),
                        null != e.preDelta && (t.preDelta = Number(e.preDelta)),
                        null != e.market && (t.market = String(e.market)),
                        null != e.exchangeId && (t.exchangeId = String(e.exchangeId)),
                        null != e.prePosi && (t.prePosi = Number(e.prePosi)),
                        null != e.name && (t.name = String(e.name)),
                        null != e.commodityCode && (t.commodityCode = String(e.commodityCode)),
                        e.tradeStatus) {
                        case "INIT":
                        case 0:
                            t.tradeStatus = 0;
                            break;
                        case "INIT_FINISH":
                        case 7:
                            t.tradeStatus = 7;
                            break;
                        case "OPEN":
                        case 10:
                            t.tradeStatus = 10;
                            break;
                        case "CALL_AUCTION":
                        case 20:
                            t.tradeStatus = 20;
                            break;
                        case "CALL_AUCTION_FINISH":
                        case 27:
                            t.tradeStatus = 27;
                            break;
                        case "TRADING":
                        case 30:
                            t.tradeStatus = 30;
                            break;
                        case "PAUSE":
                        case 40:
                            t.tradeStatus = 40;
                            break;
                        case "DELIVERY_CALL":
                        case 50:
                            t.tradeStatus = 50;
                            break;
                        case "DELIVERY":
                        case 60:
                            t.tradeStatus = 60;
                            break;
                        case "DELIVERY_FINISH":
                        case 67:
                            t.tradeStatus = 67;
                            break;
                        case "NEUTRAL_WAREHOUSE":
                        case 70:
                            t.tradeStatus = 70;
                            break;
                        case "CLOSE":
                        case 99:
                            t.tradeStatus = 99
                        }
                        if (null != e.openTime && ($util.Long ? (t.openTime = $util.Long.fromValue(e.openTime)).unsigned = !1 : "string" == typeof e.openTime ? t.openTime = parseInt(e.openTime, 10) : "number" == typeof e.openTime ? t.openTime = e.openTime : "object" == typeof e.openTime && (t.openTime = new $util.LongBits(e.openTime.low >>> 0,e.openTime.high >>> 0).toNumber())),
                        null != e.closeTime && ($util.Long ? (t.closeTime = $util.Long.fromValue(e.closeTime)).unsigned = !1 : "string" == typeof e.closeTime ? t.closeTime = parseInt(e.closeTime, 10) : "number" == typeof e.closeTime ? t.closeTime = e.closeTime : "object" == typeof e.closeTime && (t.closeTime = new $util.LongBits(e.closeTime.low >>> 0,e.closeTime.high >>> 0).toNumber())),
                        null != e.goldDelivery) {
                            if ("object" != typeof e.goldDelivery)
                                throw TypeError(".jadegold.msg.quotation.pbv2.ExtraField.goldDelivery: object expected");
                            t.goldDelivery = $root.jadegold.msg.quotation.pbv2.GoldDeliveryField.fromObject(e.goldDelivery)
                        }
                        return t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if (t.defaults) {
                            if (r.preSettle = 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.sequenceNo = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.sequenceNo = t.longs === String ? "0" : 0;
                            if (r.amplitude = 0,
                            r.currDelta = 0,
                            r.preDelta = 0,
                            r.market = "",
                            r.exchangeId = "",
                            r.prePosi = 0,
                            r.name = "",
                            r.commodityCode = "",
                            r.tradeStatus = t.enums === String ? "INIT" : 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.openTime = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.openTime = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.closeTime = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.closeTime = t.longs === String ? "0" : 0;
                            r.goldDelivery = null
                        }
                        return null != e.preSettle && e.hasOwnProperty("preSettle") && (r.preSettle = t.json && !isFinite(e.preSettle) ? String(e.preSettle) : e.preSettle),
                        null != e.sequenceNo && e.hasOwnProperty("sequenceNo") && ("number" == typeof e.sequenceNo ? r.sequenceNo = t.longs === String ? String(e.sequenceNo) : e.sequenceNo : r.sequenceNo = t.longs === String ? $util.Long.prototype.toString.call(e.sequenceNo) : t.longs === Number ? new $util.LongBits(e.sequenceNo.low >>> 0,e.sequenceNo.high >>> 0).toNumber() : e.sequenceNo),
                        null != e.amplitude && e.hasOwnProperty("amplitude") && (r.amplitude = t.json && !isFinite(e.amplitude) ? String(e.amplitude) : e.amplitude),
                        null != e.currDelta && e.hasOwnProperty("currDelta") && (r.currDelta = t.json && !isFinite(e.currDelta) ? String(e.currDelta) : e.currDelta),
                        null != e.preDelta && e.hasOwnProperty("preDelta") && (r.preDelta = t.json && !isFinite(e.preDelta) ? String(e.preDelta) : e.preDelta),
                        null != e.market && e.hasOwnProperty("market") && (r.market = e.market),
                        null != e.exchangeId && e.hasOwnProperty("exchangeId") && (r.exchangeId = e.exchangeId),
                        null != e.prePosi && e.hasOwnProperty("prePosi") && (r.prePosi = t.json && !isFinite(e.prePosi) ? String(e.prePosi) : e.prePosi),
                        null != e.name && e.hasOwnProperty("name") && (r.name = e.name),
                        null != e.commodityCode && e.hasOwnProperty("commodityCode") && (r.commodityCode = e.commodityCode),
                        null != e.tradeStatus && e.hasOwnProperty("tradeStatus") && (r.tradeStatus = t.enums === String ? $root.jadegold.msg.quotation.pbv2.TradeStatus[e.tradeStatus] : e.tradeStatus),
                        null != e.openTime && e.hasOwnProperty("openTime") && ("number" == typeof e.openTime ? r.openTime = t.longs === String ? String(e.openTime) : e.openTime : r.openTime = t.longs === String ? $util.Long.prototype.toString.call(e.openTime) : t.longs === Number ? new $util.LongBits(e.openTime.low >>> 0,e.openTime.high >>> 0).toNumber() : e.openTime),
                        null != e.closeTime && e.hasOwnProperty("closeTime") && ("number" == typeof e.closeTime ? r.closeTime = t.longs === String ? String(e.closeTime) : e.closeTime : r.closeTime = t.longs === String ? $util.Long.prototype.toString.call(e.closeTime) : t.longs === Number ? new $util.LongBits(e.closeTime.low >>> 0,e.closeTime.high >>> 0).toNumber() : e.closeTime),
                        null != e.goldDelivery && e.hasOwnProperty("goldDelivery") && (r.goldDelivery = $root.jadegold.msg.quotation.pbv2.GoldDeliveryField.toObject(e.goldDelivery, t)),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.ServerInnerField = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.beginVolume = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.beginTurnover = 0,
                    e.prototype.tradeUnit = 0,
                    e.prototype.priorno = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.tradeDay = "",
                    e.prototype.updateTime = "",
                    e.prototype.updateMs = 0,
                    e.prototype.actionDay = "",
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.beginVolume && Object.hasOwnProperty.call(e, "beginVolume") && t.uint32(8).int64(e.beginVolume),
                        null != e.beginTurnover && Object.hasOwnProperty.call(e, "beginTurnover") && t.uint32(17).double(e.beginTurnover),
                        null != e.tradeUnit && Object.hasOwnProperty.call(e, "tradeUnit") && t.uint32(25).double(e.tradeUnit),
                        null != e.priorno && Object.hasOwnProperty.call(e, "priorno") && t.uint32(33).fixed64(e.priorno),
                        null != e.tradeDay && Object.hasOwnProperty.call(e, "tradeDay") && t.uint32(66).string(e.tradeDay),
                        null != e.updateTime && Object.hasOwnProperty.call(e, "updateTime") && t.uint32(74).string(e.updateTime),
                        null != e.updateMs && Object.hasOwnProperty.call(e, "updateMs") && t.uint32(80).int32(e.updateMs),
                        null != e.actionDay && Object.hasOwnProperty.call(e, "actionDay") && t.uint32(90).string(e.actionDay),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.ServerInnerField;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.beginVolume = e.int64();
                                break;
                            case 2:
                                o.beginTurnover = e.double();
                                break;
                            case 3:
                                o.tradeUnit = e.double();
                                break;
                            case 4:
                                o.priorno = e.fixed64();
                                break;
                            case 8:
                                o.tradeDay = e.string();
                                break;
                            case 9:
                                o.updateTime = e.string();
                                break;
                            case 10:
                                o.updateMs = e.int32();
                                break;
                            case 11:
                                o.actionDay = e.string();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        return "object" != typeof e || null === e ? "object expected" : null != e.beginVolume && e.hasOwnProperty("beginVolume") && !($util.isInteger(e.beginVolume) || e.beginVolume && $util.isInteger(e.beginVolume.low) && $util.isInteger(e.beginVolume.high)) ? "beginVolume: integer|Long expected" : null != e.beginTurnover && e.hasOwnProperty("beginTurnover") && "number" != typeof e.beginTurnover ? "beginTurnover: number expected" : null != e.tradeUnit && e.hasOwnProperty("tradeUnit") && "number" != typeof e.tradeUnit ? "tradeUnit: number expected" : null != e.priorno && e.hasOwnProperty("priorno") && !($util.isInteger(e.priorno) || e.priorno && $util.isInteger(e.priorno.low) && $util.isInteger(e.priorno.high)) ? "priorno: integer|Long expected" : null != e.tradeDay && e.hasOwnProperty("tradeDay") && !$util.isString(e.tradeDay) ? "tradeDay: string expected" : null != e.updateTime && e.hasOwnProperty("updateTime") && !$util.isString(e.updateTime) ? "updateTime: string expected" : null != e.updateMs && e.hasOwnProperty("updateMs") && !$util.isInteger(e.updateMs) ? "updateMs: integer expected" : null != e.actionDay && e.hasOwnProperty("actionDay") && !$util.isString(e.actionDay) ? "actionDay: string expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.ServerInnerField)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.ServerInnerField;
                        return null != e.beginVolume && ($util.Long ? (t.beginVolume = $util.Long.fromValue(e.beginVolume)).unsigned = !1 : "string" == typeof e.beginVolume ? t.beginVolume = parseInt(e.beginVolume, 10) : "number" == typeof e.beginVolume ? t.beginVolume = e.beginVolume : "object" == typeof e.beginVolume && (t.beginVolume = new $util.LongBits(e.beginVolume.low >>> 0,e.beginVolume.high >>> 0).toNumber())),
                        null != e.beginTurnover && (t.beginTurnover = Number(e.beginTurnover)),
                        null != e.tradeUnit && (t.tradeUnit = Number(e.tradeUnit)),
                        null != e.priorno && ($util.Long ? (t.priorno = $util.Long.fromValue(e.priorno)).unsigned = !1 : "string" == typeof e.priorno ? t.priorno = parseInt(e.priorno, 10) : "number" == typeof e.priorno ? t.priorno = e.priorno : "object" == typeof e.priorno && (t.priorno = new $util.LongBits(e.priorno.low >>> 0,e.priorno.high >>> 0).toNumber())),
                        null != e.tradeDay && (t.tradeDay = String(e.tradeDay)),
                        null != e.updateTime && (t.updateTime = String(e.updateTime)),
                        null != e.updateMs && (t.updateMs = 0 | e.updateMs),
                        null != e.actionDay && (t.actionDay = String(e.actionDay)),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if (t.defaults) {
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.beginVolume = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.beginVolume = t.longs === String ? "0" : 0;
                            if (r.beginTurnover = 0,
                            r.tradeUnit = 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.priorno = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.priorno = t.longs === String ? "0" : 0;
                            r.tradeDay = "",
                            r.updateTime = "",
                            r.updateMs = 0,
                            r.actionDay = ""
                        }
                        return null != e.beginVolume && e.hasOwnProperty("beginVolume") && ("number" == typeof e.beginVolume ? r.beginVolume = t.longs === String ? String(e.beginVolume) : e.beginVolume : r.beginVolume = t.longs === String ? $util.Long.prototype.toString.call(e.beginVolume) : t.longs === Number ? new $util.LongBits(e.beginVolume.low >>> 0,e.beginVolume.high >>> 0).toNumber() : e.beginVolume),
                        null != e.beginTurnover && e.hasOwnProperty("beginTurnover") && (r.beginTurnover = t.json && !isFinite(e.beginTurnover) ? String(e.beginTurnover) : e.beginTurnover),
                        null != e.tradeUnit && e.hasOwnProperty("tradeUnit") && (r.tradeUnit = t.json && !isFinite(e.tradeUnit) ? String(e.tradeUnit) : e.tradeUnit),
                        null != e.priorno && e.hasOwnProperty("priorno") && ("number" == typeof e.priorno ? r.priorno = t.longs === String ? String(e.priorno) : e.priorno : r.priorno = t.longs === String ? $util.Long.prototype.toString.call(e.priorno) : t.longs === Number ? new $util.LongBits(e.priorno.low >>> 0,e.priorno.high >>> 0).toNumber() : e.priorno),
                        null != e.tradeDay && e.hasOwnProperty("tradeDay") && (r.tradeDay = e.tradeDay),
                        null != e.updateTime && e.hasOwnProperty("updateTime") && (r.updateTime = e.updateTime),
                        null != e.updateMs && e.hasOwnProperty("updateMs") && (r.updateMs = e.updateMs),
                        null != e.actionDay && e.hasOwnProperty("actionDay") && (r.actionDay = e.actionDay),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.QuotationField = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.code = "",
                    e.prototype.freq = 0,
                    e.prototype.quoteTime = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.volume = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.freqTime = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.turnOver = 0,
                    e.prototype.rt = null,
                    e.prototype.open = 0,
                    e.prototype.high = 0,
                    e.prototype.low = 0,
                    e.prototype.close = 0,
                    e.prototype.posi = 0,
                    e.prototype.preClose = 0,
                    e.prototype.settle = 0,
                    e.prototype.extra = null,
                    e.prototype.inner = null,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.code && Object.hasOwnProperty.call(e, "code") && t.uint32(10).string(e.code),
                        null != e.freq && Object.hasOwnProperty.call(e, "freq") && t.uint32(16).int32(e.freq),
                        null != e.quoteTime && Object.hasOwnProperty.call(e, "quoteTime") && t.uint32(25).fixed64(e.quoteTime),
                        null != e.volume && Object.hasOwnProperty.call(e, "volume") && t.uint32(32).int64(e.volume),
                        null != e.freqTime && Object.hasOwnProperty.call(e, "freqTime") && t.uint32(40).int64(e.freqTime),
                        null != e.turnOver && Object.hasOwnProperty.call(e, "turnOver") && t.uint32(49).double(e.turnOver),
                        null != e.rt && Object.hasOwnProperty.call(e, "rt") && $root.jadegold.msg.quotation.pbv2.RealtimeField.encode(e.rt, t.uint32(58).fork()).ldelim(),
                        null != e.open && Object.hasOwnProperty.call(e, "open") && t.uint32(65).double(e.open),
                        null != e.high && Object.hasOwnProperty.call(e, "high") && t.uint32(73).double(e.high),
                        null != e.low && Object.hasOwnProperty.call(e, "low") && t.uint32(81).double(e.low),
                        null != e.close && Object.hasOwnProperty.call(e, "close") && t.uint32(89).double(e.close),
                        null != e.posi && Object.hasOwnProperty.call(e, "posi") && t.uint32(97).double(e.posi),
                        null != e.preClose && Object.hasOwnProperty.call(e, "preClose") && t.uint32(105).double(e.preClose),
                        null != e.settle && Object.hasOwnProperty.call(e, "settle") && t.uint32(113).double(e.settle),
                        null != e.extra && Object.hasOwnProperty.call(e, "extra") && $root.jadegold.msg.quotation.pbv2.ExtraField.encode(e.extra, t.uint32(122).fork()).ldelim(),
                        null != e.inner && Object.hasOwnProperty.call(e, "inner") && $root.jadegold.msg.quotation.pbv2.ServerInnerField.encode(e.inner, t.uint32(1026).fork()).ldelim(),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.QuotationField;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.code = e.string();
                                break;
                            case 2:
                                o.freq = e.int32();
                                break;
                            case 3:
                                o.quoteTime = e.fixed64();
                                break;
                            case 4:
                                o.volume = e.int64();
                                break;
                            case 5:
                                o.freqTime = e.int64();
                                break;
                            case 6:
                                o.turnOver = e.double();
                                break;
                            case 7:
                                o.rt = $root.jadegold.msg.quotation.pbv2.RealtimeField.decode(e, e.uint32());
                                break;
                            case 8:
                                o.open = e.double();
                                break;
                            case 9:
                                o.high = e.double();
                                break;
                            case 10:
                                o.low = e.double();
                                break;
                            case 11:
                                o.close = e.double();
                                break;
                            case 12:
                                o.posi = e.double();
                                break;
                            case 13:
                                o.preClose = e.double();
                                break;
                            case 14:
                                o.settle = e.double();
                                break;
                            case 15:
                                o.extra = $root.jadegold.msg.quotation.pbv2.ExtraField.decode(e, e.uint32());
                                break;
                            case 128:
                                o.inner = $root.jadegold.msg.quotation.pbv2.ServerInnerField.decode(e, e.uint32());
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        if ("object" != typeof e || null === e)
                            return "object expected";
                        if (null != e.code && e.hasOwnProperty("code") && !$util.isString(e.code))
                            return "code: string expected";
                        if (null != e.freq && e.hasOwnProperty("freq"))
                            switch (e.freq) {
                            default:
                                return "freq: enum value expected";
                            case 0:
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                            case 6:
                            case 7:
                            case 8:
                            case 9:
                            case 10:
                            case 11:
                            case 12:
                            }
                        if (null != e.quoteTime && e.hasOwnProperty("quoteTime") && !($util.isInteger(e.quoteTime) || e.quoteTime && $util.isInteger(e.quoteTime.low) && $util.isInteger(e.quoteTime.high)))
                            return "quoteTime: integer|Long expected";
                        if (null != e.volume && e.hasOwnProperty("volume") && !($util.isInteger(e.volume) || e.volume && $util.isInteger(e.volume.low) && $util.isInteger(e.volume.high)))
                            return "volume: integer|Long expected";
                        if (null != e.freqTime && e.hasOwnProperty("freqTime") && !($util.isInteger(e.freqTime) || e.freqTime && $util.isInteger(e.freqTime.low) && $util.isInteger(e.freqTime.high)))
                            return "freqTime: integer|Long expected";
                        if (null != e.turnOver && e.hasOwnProperty("turnOver") && "number" != typeof e.turnOver)
                            return "turnOver: number expected";
                        if (null != e.rt && e.hasOwnProperty("rt")) {
                            let t = $root.jadegold.msg.quotation.pbv2.RealtimeField.verify(e.rt);
                            if (t)
                                return "rt." + t
                        }
                        if (null != e.open && e.hasOwnProperty("open") && "number" != typeof e.open)
                            return "open: number expected";
                        if (null != e.high && e.hasOwnProperty("high") && "number" != typeof e.high)
                            return "high: number expected";
                        if (null != e.low && e.hasOwnProperty("low") && "number" != typeof e.low)
                            return "low: number expected";
                        if (null != e.close && e.hasOwnProperty("close") && "number" != typeof e.close)
                            return "close: number expected";
                        if (null != e.posi && e.hasOwnProperty("posi") && "number" != typeof e.posi)
                            return "posi: number expected";
                        if (null != e.preClose && e.hasOwnProperty("preClose") && "number" != typeof e.preClose)
                            return "preClose: number expected";
                        if (null != e.settle && e.hasOwnProperty("settle") && "number" != typeof e.settle)
                            return "settle: number expected";
                        if (null != e.extra && e.hasOwnProperty("extra")) {
                            let t = $root.jadegold.msg.quotation.pbv2.ExtraField.verify(e.extra);
                            if (t)
                                return "extra." + t
                        }
                        if (null != e.inner && e.hasOwnProperty("inner")) {
                            let t = $root.jadegold.msg.quotation.pbv2.ServerInnerField.verify(e.inner);
                            if (t)
                                return "inner." + t
                        }
                        return null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.QuotationField)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.QuotationField;
                        switch (null != e.code && (t.code = String(e.code)),
                        e.freq) {
                        case "REALTIME":
                        case 0:
                            t.freq = 0;
                            break;
                        case "INFO":
                        case 1:
                            t.freq = 1;
                            break;
                        case "TICK":
                        case 2:
                            t.freq = 2;
                            break;
                        case "MIN1":
                        case 3:
                            t.freq = 3;
                            break;
                        case "MIN5":
                        case 4:
                            t.freq = 4;
                            break;
                        case "MIN15":
                        case 5:
                            t.freq = 5;
                            break;
                        case "MIN30":
                        case 6:
                            t.freq = 6;
                            break;
                        case "MIN60":
                        case 7:
                            t.freq = 7;
                            break;
                        case "MIN120":
                        case 8:
                            t.freq = 8;
                            break;
                        case "MIN240":
                        case 9:
                            t.freq = 9;
                            break;
                        case "DAY1":
                        case 10:
                            t.freq = 10;
                            break;
                        case "WEEK1":
                        case 11:
                            t.freq = 11;
                            break;
                        case "MONTH1":
                        case 12:
                            t.freq = 12
                        }
                        if (null != e.quoteTime && ($util.Long ? (t.quoteTime = $util.Long.fromValue(e.quoteTime)).unsigned = !1 : "string" == typeof e.quoteTime ? t.quoteTime = parseInt(e.quoteTime, 10) : "number" == typeof e.quoteTime ? t.quoteTime = e.quoteTime : "object" == typeof e.quoteTime && (t.quoteTime = new $util.LongBits(e.quoteTime.low >>> 0,e.quoteTime.high >>> 0).toNumber())),
                        null != e.volume && ($util.Long ? (t.volume = $util.Long.fromValue(e.volume)).unsigned = !1 : "string" == typeof e.volume ? t.volume = parseInt(e.volume, 10) : "number" == typeof e.volume ? t.volume = e.volume : "object" == typeof e.volume && (t.volume = new $util.LongBits(e.volume.low >>> 0,e.volume.high >>> 0).toNumber())),
                        null != e.freqTime && ($util.Long ? (t.freqTime = $util.Long.fromValue(e.freqTime)).unsigned = !1 : "string" == typeof e.freqTime ? t.freqTime = parseInt(e.freqTime, 10) : "number" == typeof e.freqTime ? t.freqTime = e.freqTime : "object" == typeof e.freqTime && (t.freqTime = new $util.LongBits(e.freqTime.low >>> 0,e.freqTime.high >>> 0).toNumber())),
                        null != e.turnOver && (t.turnOver = Number(e.turnOver)),
                        null != e.rt) {
                            if ("object" != typeof e.rt)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationField.rt: object expected");
                            t.rt = $root.jadegold.msg.quotation.pbv2.RealtimeField.fromObject(e.rt)
                        }
                        if (null != e.open && (t.open = Number(e.open)),
                        null != e.high && (t.high = Number(e.high)),
                        null != e.low && (t.low = Number(e.low)),
                        null != e.close && (t.close = Number(e.close)),
                        null != e.posi && (t.posi = Number(e.posi)),
                        null != e.preClose && (t.preClose = Number(e.preClose)),
                        null != e.settle && (t.settle = Number(e.settle)),
                        null != e.extra) {
                            if ("object" != typeof e.extra)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationField.extra: object expected");
                            t.extra = $root.jadegold.msg.quotation.pbv2.ExtraField.fromObject(e.extra)
                        }
                        if (null != e.inner) {
                            if ("object" != typeof e.inner)
                                throw TypeError(".jadegold.msg.quotation.pbv2.QuotationField.inner: object expected");
                            t.inner = $root.jadegold.msg.quotation.pbv2.ServerInnerField.fromObject(e.inner)
                        }
                        return t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if (t.defaults) {
                            if (r.code = "",
                            r.freq = t.enums === String ? "REALTIME" : 0,
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.quoteTime = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.quoteTime = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.volume = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.volume = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.freqTime = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.freqTime = t.longs === String ? "0" : 0;
                            r.turnOver = 0,
                            r.rt = null,
                            r.open = 0,
                            r.high = 0,
                            r.low = 0,
                            r.close = 0,
                            r.posi = 0,
                            r.preClose = 0,
                            r.settle = 0,
                            r.extra = null,
                            r.inner = null
                        }
                        return null != e.code && e.hasOwnProperty("code") && (r.code = e.code),
                        null != e.freq && e.hasOwnProperty("freq") && (r.freq = t.enums === String ? $root.jadegold.msg.quotation.pbv2.QuotationFreq[e.freq] : e.freq),
                        null != e.quoteTime && e.hasOwnProperty("quoteTime") && ("number" == typeof e.quoteTime ? r.quoteTime = t.longs === String ? String(e.quoteTime) : e.quoteTime : r.quoteTime = t.longs === String ? $util.Long.prototype.toString.call(e.quoteTime) : t.longs === Number ? new $util.LongBits(e.quoteTime.low >>> 0,e.quoteTime.high >>> 0).toNumber() : e.quoteTime),
                        null != e.volume && e.hasOwnProperty("volume") && ("number" == typeof e.volume ? r.volume = t.longs === String ? String(e.volume) : e.volume : r.volume = t.longs === String ? $util.Long.prototype.toString.call(e.volume) : t.longs === Number ? new $util.LongBits(e.volume.low >>> 0,e.volume.high >>> 0).toNumber() : e.volume),
                        null != e.freqTime && e.hasOwnProperty("freqTime") && ("number" == typeof e.freqTime ? r.freqTime = t.longs === String ? String(e.freqTime) : e.freqTime : r.freqTime = t.longs === String ? $util.Long.prototype.toString.call(e.freqTime) : t.longs === Number ? new $util.LongBits(e.freqTime.low >>> 0,e.freqTime.high >>> 0).toNumber() : e.freqTime),
                        null != e.turnOver && e.hasOwnProperty("turnOver") && (r.turnOver = t.json && !isFinite(e.turnOver) ? String(e.turnOver) : e.turnOver),
                        null != e.rt && e.hasOwnProperty("rt") && (r.rt = $root.jadegold.msg.quotation.pbv2.RealtimeField.toObject(e.rt, t)),
                        null != e.open && e.hasOwnProperty("open") && (r.open = t.json && !isFinite(e.open) ? String(e.open) : e.open),
                        null != e.high && e.hasOwnProperty("high") && (r.high = t.json && !isFinite(e.high) ? String(e.high) : e.high),
                        null != e.low && e.hasOwnProperty("low") && (r.low = t.json && !isFinite(e.low) ? String(e.low) : e.low),
                        null != e.close && e.hasOwnProperty("close") && (r.close = t.json && !isFinite(e.close) ? String(e.close) : e.close),
                        null != e.posi && e.hasOwnProperty("posi") && (r.posi = t.json && !isFinite(e.posi) ? String(e.posi) : e.posi),
                        null != e.preClose && e.hasOwnProperty("preClose") && (r.preClose = t.json && !isFinite(e.preClose) ? String(e.preClose) : e.preClose),
                        null != e.settle && e.hasOwnProperty("settle") && (r.settle = t.json && !isFinite(e.settle) ? String(e.settle) : e.settle),
                        null != e.extra && e.hasOwnProperty("extra") && (r.extra = $root.jadegold.msg.quotation.pbv2.ExtraField.toObject(e.extra, t)),
                        null != e.inner && e.hasOwnProperty("inner") && (r.inner = $root.jadegold.msg.quotation.pbv2.ServerInnerField.toObject(e.inner, t)),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e.GoldDeliveryField = function() {
                    function e(e) {
                        if (e)
                            for (let t = Object.keys(e), r = 0; r < t.length; ++r)
                                null != e[t[r]] && (this[t[r]] = e[t[r]])
                    }
                    return e.prototype.date = "",
                    e.prototype.direction = "",
                    e.prototype.buy = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.sell = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.midBuy = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.prototype.midSell = $util.Long ? $util.Long.fromBits(0, 0, !1) : 0,
                    e.create = function(t) {
                        return new e(t)
                    }
                    ,
                    e.encode = function(e, t) {
                        return t || (t = $Writer.create()),
                        null != e.date && Object.hasOwnProperty.call(e, "date") && t.uint32(10).string(e.date),
                        null != e.direction && Object.hasOwnProperty.call(e, "direction") && t.uint32(18).string(e.direction),
                        null != e.buy && Object.hasOwnProperty.call(e, "buy") && t.uint32(24).int64(e.buy),
                        null != e.sell && Object.hasOwnProperty.call(e, "sell") && t.uint32(32).int64(e.sell),
                        null != e.midBuy && Object.hasOwnProperty.call(e, "midBuy") && t.uint32(40).int64(e.midBuy),
                        null != e.midSell && Object.hasOwnProperty.call(e, "midSell") && t.uint32(48).int64(e.midSell),
                        t
                    }
                    ,
                    e.encodeDelimited = function(e, t) {
                        return this.encode(e, t).ldelim()
                    }
                    ,
                    e.decode = function(e, t) {
                        e instanceof $Reader || (e = $Reader.create(e));
                        let r = void 0 === t ? e.len : e.pos + t
                          , o = new $root.jadegold.msg.quotation.pbv2.GoldDeliveryField;
                        for (; e.pos < r; ) {
                            let t = e.uint32();
                            switch (t >>> 3) {
                            case 1:
                                o.date = e.string();
                                break;
                            case 2:
                                o.direction = e.string();
                                break;
                            case 3:
                                o.buy = e.int64();
                                break;
                            case 4:
                                o.sell = e.int64();
                                break;
                            case 5:
                                o.midBuy = e.int64();
                                break;
                            case 6:
                                o.midSell = e.int64();
                                break;
                            default:
                                e.skipType(7 & t)
                            }
                        }
                        return o
                    }
                    ,
                    e.decodeDelimited = function(e) {
                        return e instanceof $Reader || (e = new $Reader(e)),
                        this.decode(e, e.uint32())
                    }
                    ,
                    e.verify = function(e) {
                        return "object" != typeof e || null === e ? "object expected" : null != e.date && e.hasOwnProperty("date") && !$util.isString(e.date) ? "date: string expected" : null != e.direction && e.hasOwnProperty("direction") && !$util.isString(e.direction) ? "direction: string expected" : null != e.buy && e.hasOwnProperty("buy") && !($util.isInteger(e.buy) || e.buy && $util.isInteger(e.buy.low) && $util.isInteger(e.buy.high)) ? "buy: integer|Long expected" : null != e.sell && e.hasOwnProperty("sell") && !($util.isInteger(e.sell) || e.sell && $util.isInteger(e.sell.low) && $util.isInteger(e.sell.high)) ? "sell: integer|Long expected" : null != e.midBuy && e.hasOwnProperty("midBuy") && !($util.isInteger(e.midBuy) || e.midBuy && $util.isInteger(e.midBuy.low) && $util.isInteger(e.midBuy.high)) ? "midBuy: integer|Long expected" : null != e.midSell && e.hasOwnProperty("midSell") && !($util.isInteger(e.midSell) || e.midSell && $util.isInteger(e.midSell.low) && $util.isInteger(e.midSell.high)) ? "midSell: integer|Long expected" : null
                    }
                    ,
                    e.fromObject = function(e) {
                        if (e instanceof $root.jadegold.msg.quotation.pbv2.GoldDeliveryField)
                            return e;
                        let t = new $root.jadegold.msg.quotation.pbv2.GoldDeliveryField;
                        return null != e.date && (t.date = String(e.date)),
                        null != e.direction && (t.direction = String(e.direction)),
                        null != e.buy && ($util.Long ? (t.buy = $util.Long.fromValue(e.buy)).unsigned = !1 : "string" == typeof e.buy ? t.buy = parseInt(e.buy, 10) : "number" == typeof e.buy ? t.buy = e.buy : "object" == typeof e.buy && (t.buy = new $util.LongBits(e.buy.low >>> 0,e.buy.high >>> 0).toNumber())),
                        null != e.sell && ($util.Long ? (t.sell = $util.Long.fromValue(e.sell)).unsigned = !1 : "string" == typeof e.sell ? t.sell = parseInt(e.sell, 10) : "number" == typeof e.sell ? t.sell = e.sell : "object" == typeof e.sell && (t.sell = new $util.LongBits(e.sell.low >>> 0,e.sell.high >>> 0).toNumber())),
                        null != e.midBuy && ($util.Long ? (t.midBuy = $util.Long.fromValue(e.midBuy)).unsigned = !1 : "string" == typeof e.midBuy ? t.midBuy = parseInt(e.midBuy, 10) : "number" == typeof e.midBuy ? t.midBuy = e.midBuy : "object" == typeof e.midBuy && (t.midBuy = new $util.LongBits(e.midBuy.low >>> 0,e.midBuy.high >>> 0).toNumber())),
                        null != e.midSell && ($util.Long ? (t.midSell = $util.Long.fromValue(e.midSell)).unsigned = !1 : "string" == typeof e.midSell ? t.midSell = parseInt(e.midSell, 10) : "number" == typeof e.midSell ? t.midSell = e.midSell : "object" == typeof e.midSell && (t.midSell = new $util.LongBits(e.midSell.low >>> 0,e.midSell.high >>> 0).toNumber())),
                        t
                    }
                    ,
                    e.toObject = function(e, t) {
                        t || (t = {});
                        let r = {};
                        if (t.defaults) {
                            if (r.date = "",
                            r.direction = "",
                            $util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.buy = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.buy = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.sell = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.sell = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.midBuy = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.midBuy = t.longs === String ? "0" : 0;
                            if ($util.Long) {
                                let e = new $util.Long(0,0,!1);
                                r.midSell = t.longs === String ? e.toString() : t.longs === Number ? e.toNumber() : e
                            } else
                                r.midSell = t.longs === String ? "0" : 0
                        }
                        return null != e.date && e.hasOwnProperty("date") && (r.date = e.date),
                        null != e.direction && e.hasOwnProperty("direction") && (r.direction = e.direction),
                        null != e.buy && e.hasOwnProperty("buy") && ("number" == typeof e.buy ? r.buy = t.longs === String ? String(e.buy) : e.buy : r.buy = t.longs === String ? $util.Long.prototype.toString.call(e.buy) : t.longs === Number ? new $util.LongBits(e.buy.low >>> 0,e.buy.high >>> 0).toNumber() : e.buy),
                        null != e.sell && e.hasOwnProperty("sell") && ("number" == typeof e.sell ? r.sell = t.longs === String ? String(e.sell) : e.sell : r.sell = t.longs === String ? $util.Long.prototype.toString.call(e.sell) : t.longs === Number ? new $util.LongBits(e.sell.low >>> 0,e.sell.high >>> 0).toNumber() : e.sell),
                        null != e.midBuy && e.hasOwnProperty("midBuy") && ("number" == typeof e.midBuy ? r.midBuy = t.longs === String ? String(e.midBuy) : e.midBuy : r.midBuy = t.longs === String ? $util.Long.prototype.toString.call(e.midBuy) : t.longs === Number ? new $util.LongBits(e.midBuy.low >>> 0,e.midBuy.high >>> 0).toNumber() : e.midBuy),
                        null != e.midSell && e.hasOwnProperty("midSell") && ("number" == typeof e.midSell ? r.midSell = t.longs === String ? String(e.midSell) : e.midSell : r.midSell = t.longs === String ? $util.Long.prototype.toString.call(e.midSell) : t.longs === Number ? new $util.LongBits(e.midSell.low >>> 0,e.midSell.high >>> 0).toNumber() : e.midSell),
                        r
                    }
                    ,
                    e.prototype.toJSON = function() {
                        return this.constructor.toObject(this, minimal$1.exports.util.toJSONOptions)
                    }
                    ,
                    e
                }(),
                e
            }(),
            e
        }(),
        e
    }(),
    e
}
)();
/*! *****************************************************************************
Copyright (c) Microsoft Corporation. All rights reserved.
Licensed under the Apache License, Version 2.0 (the "License"); you may not use
this file except in compliance with the License. You may obtain a copy of the
License at http://www.apache.org/licenses/LICENSE-2.0

THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
MERCHANTABLITY OR NON-INFRINGEMENT.

See the Apache Version 2.0 License for specific language governing permissions
and limitations under the License.
***************************************************************************** */
var extendStatics = function(e, t) {
    return (extendStatics = Object.setPrototypeOf || {
        __proto__: []
    }instanceof Array && function(e, t) {
        e.__proto__ = t
    }
    || function(e, t) {
        for (var r in t)
            t.hasOwnProperty(r) && (e[r] = t[r])
    }
    )(e, t)
};
function __extends(e, t) {
    function r() {
        this.constructor = e
    }
    extendStatics(e, t),
    e.prototype = null === t ? Object.create(t) : (r.prototype = t.prototype,
    new r)
}
function __values(e) {
    var t = "function" == typeof Symbol && e[Symbol.iterator]
      , r = 0;
    return t ? t.call(e) : {
        next: function() {
            return e && r >= e.length && (e = void 0),
            {
                value: e && e[r++],
                done: !e
            }
        }
    }
}
function __read(e, t) {
    var r = "function" == typeof Symbol && e[Symbol.iterator];
    if (!r)
        return e;
    var o, n, i = r.call(e), s = [];
    try {
        for (; (void 0 === t || t-- > 0) && !(o = i.next()).done; )
            s.push(o.value)
    } catch (l) {
        n = {
            error: l
        }
    } finally {
        try {
            o && !o.done && (r = i.return) && r.call(i)
        } finally {
            if (n)
                throw n.error
        }
    }
    return s
}
function __spread() {
    for (var e = [], t = 0; t < arguments.length; t++)
        e = e.concat(__read(arguments[t]));
    return e
}
var Event = function(e, t) {
    this.target = t,
    this.type = e
}
  , ErrorEvent = function(e) {
    function t(t, r) {
        var o = e.call(this, "error", r) || this;
        return o.message = t.message,
        o.error = t,
        o
    }
    return __extends(t, e),
    t
}(Event)
  , CloseEvent = function(e) {
    function t(t, r, o) {
        void 0 === t && (t = 1e3),
        void 0 === r && (r = "");
        var n = e.call(this, "close", o) || this;
        return n.wasClean = !0,
        n.code = t,
        n.reason = r,
        n
    }
    return __extends(t, e),
    t
}(Event)
  , getGlobalWebSocket = function() {
    if ("undefined" != typeof WebSocket)
        return WebSocket
}
  , isWebSocket = function(e) {
    return void 0 !== e && !!e && 2 === e.CLOSING
}
  , DEFAULT = {
    maxReconnectionDelay: 1e4,
    minReconnectionDelay: 1e3 + 4e3 * Math.random(),
    minUptime: 5e3,
    reconnectionDelayGrowFactor: 1.3,
    connectionTimeout: 4e3,
    maxRetries: 1 / 0,
    maxEnqueuedMessages: 1 / 0,
    startClosed: !1,
    debug: !1
}
  , ReconnectingWebSocket = function() {
    function e(e, t, r) {
        var o = this;
        void 0 === r && (r = {}),
        this._listeners = {
            error: [],
            message: [],
            open: [],
            close: []
        },
        this._retryCount = -1,
        this._shouldReconnect = !0,
        this._connectLock = !1,
        this._binaryType = "blob",
        this._closeCalled = !1,
        this._messageQueue = [],
        this.onclose = null,
        this.onerror = null,
        this.onmessage = null,
        this.onopen = null,
        this._handleOpen = function(e) {
            o._debug("open event");
            var t = o._options.minUptime
              , r = void 0 === t ? DEFAULT.minUptime : t;
            clearTimeout(o._connectTimeout),
            o._uptimeTimeout = setTimeout((function() {
                return o._acceptOpen()
            }
            ), r),
            o._ws.binaryType = o._binaryType,
            o._messageQueue.forEach((function(e) {
                return o._ws.send(e)
            }
            )),
            o._messageQueue = [],
            o.onopen && o.onopen(e),
            o._listeners.open.forEach((function(t) {
                return o._callEventListener(e, t)
            }
            ))
        }
        ,
        this._handleMessage = function(e) {
            o._debug("message event"),
            o.onmessage && o.onmessage(e),
            o._listeners.message.forEach((function(t) {
                return o._callEventListener(e, t)
            }
            ))
        }
        ,
        this._handleError = function(e) {
            o._debug("error event", e.message),
            o._disconnect(void 0, "TIMEOUT" === e.message ? "timeout" : void 0),
            o.onerror && o.onerror(e),
            o._debug("exec error listeners"),
            o._listeners.error.forEach((function(t) {
                return o._callEventListener(e, t)
            }
            )),
            o._connect()
        }
        ,
        this._handleClose = function(e) {
            o._debug("close event"),
            o._clearTimeouts(),
            o._shouldReconnect && o._connect(),
            o.onclose && o.onclose(e),
            o._listeners.close.forEach((function(t) {
                return o._callEventListener(e, t)
            }
            ))
        }
        ,
        this._url = e,
        this._protocols = t,
        this._options = r,
        this._options.startClosed && (this._shouldReconnect = !1),
        this._connect()
    }
    return Object.defineProperty(e, "CONNECTING", {
        get: function() {
            return 0
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e, "OPEN", {
        get: function() {
            return 1
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e, "CLOSING", {
        get: function() {
            return 2
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e, "CLOSED", {
        get: function() {
            return 3
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "CONNECTING", {
        get: function() {
            return e.CONNECTING
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "OPEN", {
        get: function() {
            return e.OPEN
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "CLOSING", {
        get: function() {
            return e.CLOSING
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "CLOSED", {
        get: function() {
            return e.CLOSED
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "binaryType", {
        get: function() {
            return this._ws ? this._ws.binaryType : this._binaryType
        },
        set: function(e) {
            this._binaryType = e,
            this._ws && (this._ws.binaryType = e)
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "retryCount", {
        get: function() {
            return Math.max(this._retryCount, 0)
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "bufferedAmount", {
        get: function() {
            return this._messageQueue.reduce((function(e, t) {
                return "string" == typeof t ? e += t.length : t instanceof Blob ? e += t.size : e += t.byteLength,
                e
            }
            ), 0) + (this._ws ? this._ws.bufferedAmount : 0)
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "extensions", {
        get: function() {
            return this._ws ? this._ws.extensions : ""
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "protocol", {
        get: function() {
            return this._ws ? this._ws.protocol : ""
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "readyState", {
        get: function() {
            return this._ws ? this._ws.readyState : this._options.startClosed ? e.CLOSED : e.CONNECTING
        },
        enumerable: !0,
        configurable: !0
    }),
    Object.defineProperty(e.prototype, "url", {
        get: function() {
            return this._ws ? this._ws.url : ""
        },
        enumerable: !0,
        configurable: !0
    }),
    e.prototype.close = function(e, t) {
        void 0 === e && (e = 1e3),
        this._closeCalled = !0,
        this._shouldReconnect = !1,
        this._clearTimeouts(),
        this._ws ? this._ws.readyState !== this.CLOSED ? this._ws.close(e, t) : this._debug("close: already closed") : this._debug("close enqueued: no ws instance")
    }
    ,
    e.prototype.reconnect = function(e, t) {
        this._shouldReconnect = !0,
        this._closeCalled = !1,
        this._retryCount = -1,
        this._ws && this._ws.readyState !== this.CLOSED ? (this._disconnect(e, t),
        this._connect()) : this._connect()
    }
    ,
    e.prototype.send = function(e) {
        if (this._ws && this._ws.readyState === this.OPEN)
            this._debug("send", e),
            this._ws.send(e);
        else {
            var t = this._options.maxEnqueuedMessages
              , r = void 0 === t ? DEFAULT.maxEnqueuedMessages : t;
            this._messageQueue.length < r && (this._debug("enqueue", e),
            this._messageQueue.push(e))
        }
    }
    ,
    e.prototype.addEventListener = function(e, t) {
        this._listeners[e] && this._listeners[e].push(t)
    }
    ,
    e.prototype.dispatchEvent = function(e) {
        var t, r, o = this._listeners[e.type];
        if (o)
            try {
                for (var n = __values(o), i = n.next(); !i.done; i = n.next()) {
                    var s = i.value;
                    this._callEventListener(e, s)
                }
            } catch (l) {
                t = {
                    error: l
                }
            } finally {
                try {
                    i && !i.done && (r = n.return) && r.call(n)
                } finally {
                    if (t)
                        throw t.error
                }
            }
        return !0
    }
    ,
    e.prototype.removeEventListener = function(e, t) {
        this._listeners[e] && (this._listeners[e] = this._listeners[e].filter((function(e) {
            return e !== t
        }
        )))
    }
    ,
    e.prototype._debug = function() {
        for (var e = [], t = 0; t < arguments.length; t++)
            e[t] = arguments[t];
        this._options.debug
    }
    ,
    e.prototype._getNextDelay = function() {
        var e = this._options
          , t = e.reconnectionDelayGrowFactor
          , r = void 0 === t ? DEFAULT.reconnectionDelayGrowFactor : t
          , o = e.minReconnectionDelay
          , n = void 0 === o ? DEFAULT.minReconnectionDelay : o
          , i = e.maxReconnectionDelay
          , s = void 0 === i ? DEFAULT.maxReconnectionDelay : i
          , l = 0;
        return this._retryCount > 0 && (l = n * Math.pow(r, this._retryCount - 1)) > s && (l = s),
        this._debug("next delay", l),
        l
    }
    ,
    e.prototype._wait = function() {
        var e = this;
        return new Promise((function(t) {
            setTimeout(t, e._getNextDelay())
        }
        ))
    }
    ,
    e.prototype._getNextUrl = function(e) {
        if ("string" == typeof e)
            return Promise.resolve(e);
        if ("function" == typeof e) {
            var t = e();
            if ("string" == typeof t)
                return Promise.resolve(t);
            if (t.then)
                return t
        }
        throw Error("Invalid URL")
    }
    ,
    e.prototype._connect = function() {
        var e = this;
        if (!this._connectLock && this._shouldReconnect) {
            this._connectLock = !0;
            var t = this._options
              , r = t.maxRetries
              , o = void 0 === r ? DEFAULT.maxRetries : r
              , n = t.connectionTimeout
              , i = void 0 === n ? DEFAULT.connectionTimeout : n
              , s = t.WebSocket
              , l = void 0 === s ? getGlobalWebSocket() : s;
            if (this._retryCount >= o)
                this._debug("max retries reached", this._retryCount, ">=", o);
            else {
                if (this._retryCount++,
                this._debug("connect", this._retryCount),
                this._removeListeners(),
                !isWebSocket(l))
                    throw Error("No valid WebSocket class provided");
                this._wait().then((function() {
                    return e._getNextUrl(e._url)
                }
                )).then((function(t) {
                    e._closeCalled || (e._debug("connect", {
                        url: t,
                        protocols: e._protocols
                    }),
                    e._ws = e._protocols ? new l(t,e._protocols) : new l(t),
                    e._ws.binaryType = e._binaryType,
                    e._connectLock = !1,
                    e._addListeners(),
                    e._connectTimeout = setTimeout((function() {
                        return e._handleTimeout()
                    }
                    ), i))
                }
                ))
            }
        }
    }
    ,
    e.prototype._handleTimeout = function() {
        this._debug("timeout event"),
        this._handleError(new ErrorEvent(Error("TIMEOUT"),this))
    }
    ,
    e.prototype._disconnect = function(e, t) {
        if (void 0 === e && (e = 1e3),
        this._clearTimeouts(),
        this._ws) {
            this._removeListeners();
            try {
                this._ws.close(e, t),
                this._handleClose(new CloseEvent(e,t,this))
            } catch (r) {}
        }
    }
    ,
    e.prototype._acceptOpen = function() {
        this._debug("accept open"),
        this._retryCount = 0
    }
    ,
    e.prototype._callEventListener = function(e, t) {
        "handleEvent"in t ? t.handleEvent(e) : t(e)
    }
    ,
    e.prototype._removeListeners = function() {
        this._ws && (this._debug("removeListeners"),
        this._ws.removeEventListener("open", this._handleOpen),
        this._ws.removeEventListener("close", this._handleClose),
        this._ws.removeEventListener("message", this._handleMessage),
        this._ws.removeEventListener("error", this._handleError))
    }
    ,
    e.prototype._addListeners = function() {
        this._ws && (this._debug("addListeners"),
        this._ws.addEventListener("open", this._handleOpen),
        this._ws.addEventListener("close", this._handleClose),
        this._ws.addEventListener("message", this._handleMessage),
        this._ws.addEventListener("error", this._handleError))
    }
    ,
    e.prototype._clearTimeouts = function() {
        clearTimeout(this._connectTimeout),
        clearTimeout(this._uptimeTimeout)
    }
    ,
    e
}()
  , blowfish = {
    exports: {}
};
blowfish.exports = function() {
    function e(e, t) {
        for (var r = 0; r < t.length; r++) {
            var o = t[r];
            o.enumerable = o.enumerable || !1,
            o.configurable = !0,
            "value"in o && (o.writable = !0),
            Object.defineProperty(e, o.key, o)
        }
    }
    var t = {
        ECB: 0,
        CBC: 1
    }
      , r = {
        PKCS5: 0,
        ONE_AND_ZEROS: 1,
        LAST_BYTE: 2,
        NULL: 3,
        SPACES: 4
    }
      , o = {
        STRING: 0,
        UINT8_ARRAY: 1,
        JSON_OBJECT: 2
    }
      , n = [608135816, 2242054355, 320440878, 57701188, 2752067618, 698298832, 137296536, 3964562569, 1160258022, 953160567, 3193202383, 887688300, 3232508343, 3380367581, 1065670069, 3041331479, 2450970073, 2306472731]
      , i = [3509652390, 2564797868, 805139163, 3491422135, 3101798381, 1780907670, 3128725573, 4046225305, 614570311, 3012652279, 134345442, 2240740374, 1667834072, 1901547113, 2757295779, 4103290238, 227898511, 1921955416, 1904987480, 2182433518, 2069144605, 3260701109, 2620446009, 720527379, 3318853667, 677414384, 3393288472, 3101374703, 2390351024, 1614419982, 1822297739, 2954791486, 3608508353, 3174124327, 2024746970, 1432378464, 3864339955, 2857741204, 1464375394, 1676153920, 1439316330, 715854006, 3033291828, 289532110, 2706671279, 2087905683, 3018724369, 1668267050, 732546397, 1947742710, 3462151702, 2609353502, 2950085171, 1814351708, 2050118529, 680887927, 999245976, 1800124847, 3300911131, 1713906067, 1641548236, 4213287313, 1216130144, 1575780402, 4018429277, 3917837745, 3693486850, 3949271944, 596196993, 3549867205, 258830323, 2213823033, 772490370, 2760122372, 1774776394, 2652871518, 566650946, 4142492826, 1728879713, 2882767088, 1783734482, 3629395816, 2517608232, 2874225571, 1861159788, 326777828, 3124490320, 2130389656, 2716951837, 967770486, 1724537150, 2185432712, 2364442137, 1164943284, 2105845187, 998989502, 3765401048, 2244026483, 1075463327, 1455516326, 1322494562, 910128902, 469688178, 1117454909, 936433444, 3490320968, 3675253459, 1240580251, 122909385, 2157517691, 634681816, 4142456567, 3825094682, 3061402683, 2540495037, 79693498, 3249098678, 1084186820, 1583128258, 426386531, 1761308591, 1047286709, 322548459, 995290223, 1845252383, 2603652396, 3431023940, 2942221577, 3202600964, 3727903485, 1712269319, 422464435, 3234572375, 1170764815, 3523960633, 3117677531, 1434042557, 442511882, 3600875718, 1076654713, 1738483198, 4213154764, 2393238008, 3677496056, 1014306527, 4251020053, 793779912, 2902807211, 842905082, 4246964064, 1395751752, 1040244610, 2656851899, 3396308128, 445077038, 3742853595, 3577915638, 679411651, 2892444358, 2354009459, 1767581616, 3150600392, 3791627101, 3102740896, 284835224, 4246832056, 1258075500, 768725851, 2589189241, 3069724005, 3532540348, 1274779536, 3789419226, 2764799539, 1660621633, 3471099624, 4011903706, 913787905, 3497959166, 737222580, 2514213453, 2928710040, 3937242737, 1804850592, 3499020752, 2949064160, 2386320175, 2390070455, 2415321851, 4061277028, 2290661394, 2416832540, 1336762016, 1754252060, 3520065937, 3014181293, 791618072, 3188594551, 3933548030, 2332172193, 3852520463, 3043980520, 413987798, 3465142937, 3030929376, 4245938359, 2093235073, 3534596313, 375366246, 2157278981, 2479649556, 555357303, 3870105701, 2008414854, 3344188149, 4221384143, 3956125452, 2067696032, 3594591187, 2921233993, 2428461, 544322398, 577241275, 1471733935, 610547355, 4027169054, 1432588573, 1507829418, 2025931657, 3646575487, 545086370, 48609733, 2200306550, 1653985193, 298326376, 1316178497, 3007786442, 2064951626, 458293330, 2589141269, 3591329599, 3164325604, 727753846, 2179363840, 146436021, 1461446943, 4069977195, 705550613, 3059967265, 3887724982, 4281599278, 3313849956, 1404054877, 2845806497, 146425753, 1854211946]
      , s = [1266315497, 3048417604, 3681880366, 3289982499, 290971e4, 1235738493, 2632868024, 2414719590, 3970600049, 1771706367, 1449415276, 3266420449, 422970021, 1963543593, 2690192192, 3826793022, 1062508698, 1531092325, 1804592342, 2583117782, 2714934279, 4024971509, 1294809318, 4028980673, 1289560198, 2221992742, 1669523910, 35572830, 157838143, 1052438473, 1016535060, 1802137761, 1753167236, 1386275462, 3080475397, 2857371447, 1040679964, 2145300060, 2390574316, 1461121720, 2956646967, 4031777805, 4028374788, 33600511, 2920084762, 1018524850, 629373528, 3691585981, 3515945977, 2091462646, 2486323059, 586499841, 988145025, 935516892, 3367335476, 2599673255, 2839830854, 265290510, 3972581182, 2759138881, 3795373465, 1005194799, 847297441, 406762289, 1314163512, 1332590856, 1866599683, 4127851711, 750260880, 613907577, 1450815602, 3165620655, 3734664991, 3650291728, 3012275730, 3704569646, 1427272223, 778793252, 1343938022, 2676280711, 2052605720, 1946737175, 3164576444, 3914038668, 3967478842, 3682934266, 1661551462, 3294938066, 4011595847, 840292616, 3712170807, 616741398, 312560963, 711312465, 1351876610, 322626781, 1910503582, 271666773, 2175563734, 1594956187, 70604529, 3617834859, 1007753275, 1495573769, 4069517037, 2549218298, 2663038764, 504708206, 2263041392, 3941167025, 2249088522, 1514023603, 1998579484, 1312622330, 694541497, 2582060303, 2151582166, 1382467621, 776784248, 2618340202, 3323268794, 2497899128, 2784771155, 503983604, 4076293799, 907881277, 423175695, 432175456, 1378068232, 4145222326, 3954048622, 3938656102, 3820766613, 2793130115, 2977904593, 26017576, 3274890735, 3194772133, 1700274565, 1756076034, 4006520079, 3677328699, 720338349, 1533947780, 354530856, 688349552, 3973924725, 1637815568, 332179504, 3949051286, 53804574, 2852348879, 3044236432, 1282449977, 3583942155, 3416972820, 4006381244, 1617046695, 2628476075, 3002303598, 1686838959, 431878346, 2686675385, 1700445008, 1080580658, 1009431731, 832498133, 3223435511, 2605976345, 2271191193, 2516031870, 1648197032, 4164389018, 2548247927, 300782431, 375919233, 238389289, 3353747414, 2531188641, 2019080857, 1475708069, 455242339, 2609103871, 448939670, 3451063019, 1395535956, 2413381860, 1841049896, 1491858159, 885456874, 4264095073, 4001119347, 1565136089, 3898914787, 1108368660, 540939232, 1173283510, 2745871338, 3681308437, 4207628240, 3343053890, 4016749493, 1699691293, 1103962373, 3625875870, 2256883143, 3830138730, 1031889488, 3479347698, 1535977030, 4236805024, 3251091107, 2132092099, 1774941330, 1199868427, 1452454533, 157007616, 2904115357, 342012276, 595725824, 1480756522, 206960106, 497939518, 591360097, 863170706, 2375253569, 3596610801, 1814182875, 2094937945, 3421402208, 1082520231, 3463918190, 2785509508, 435703966, 3908032597, 1641649973, 2842273706, 3305899714, 1510255612, 2148256476, 2655287854, 3276092548, 4258621189, 236887753, 3681803219, 274041037, 1734335097, 3815195456, 3317970021, 1899903192, 1026095262, 4050517792, 356393447, 2410691914, 3873677099, 3682840055]
      , l = [3913112168, 2491498743, 4132185628, 2489919796, 1091903735, 1979897079, 3170134830, 3567386728, 3557303409, 857797738, 1136121015, 1342202287, 507115054, 2535736646, 337727348, 3213592640, 1301675037, 2528481711, 1895095763, 1721773893, 3216771564, 62756741, 2142006736, 835421444, 2531993523, 1442658625, 3659876326, 2882144922, 676362277, 1392781812, 170690266, 3921047035, 1759253602, 3611846912, 1745797284, 664899054, 1329594018, 3901205900, 3045908486, 2062866102, 2865634940, 3543621612, 3464012697, 1080764994, 553557557, 3656615353, 3996768171, 991055499, 499776247, 1265440854, 648242737, 3940784050, 980351604, 3713745714, 1749149687, 3396870395, 4211799374, 3640570775, 1161844396, 3125318951, 1431517754, 545492359, 4268468663, 3499529547, 1437099964, 2702547544, 3433638243, 2581715763, 2787789398, 1060185593, 1593081372, 2418618748, 4260947970, 69676912, 2159744348, 86519011, 2512459080, 3838209314, 1220612927, 3339683548, 133810670, 1090789135, 1078426020, 1569222167, 845107691, 3583754449, 4072456591, 1091646820, 628848692, 1613405280, 3757631651, 526609435, 236106946, 48312990, 2942717905, 3402727701, 1797494240, 859738849, 992217954, 4005476642, 2243076622, 3870952857, 3732016268, 765654824, 3490871365, 2511836413, 1685915746, 3888969200, 1414112111, 2273134842, 3281911079, 4080962846, 172450625, 2569994100, 980381355, 4109958455, 2819808352, 2716589560, 2568741196, 3681446669, 3329971472, 1835478071, 660984891, 3704678404, 4045999559, 3422617507, 3040415634, 1762651403, 1719377915, 3470491036, 2693910283, 3642056355, 3138596744, 1364962596, 2073328063, 1983633131, 926494387, 3423689081, 2150032023, 4096667949, 1749200295, 3328846651, 309677260, 2016342300, 1779581495, 3079819751, 111262694, 1274766160, 443224088, 298511866, 1025883608, 3806446537, 1145181785, 168956806, 3641502830, 3584813610, 1689216846, 3666258015, 3200248200, 1692713982, 2646376535, 4042768518, 1618508792, 1610833997, 3523052358, 4130873264, 2001055236, 3610705100, 2202168115, 4028541809, 2961195399, 1006657119, 2006996926, 3186142756, 1430667929, 3210227297, 1314452623, 4074634658, 4101304120, 2273951170, 1399257539, 3367210612, 3027628629, 1190975929, 2062231137, 2333990788, 2221543033, 2438960610, 1181637006, 548689776, 2362791313, 3372408396, 3104550113, 3145860560, 296247880, 1970579870, 3078560182, 3769228297, 1714227617, 3291629107, 3898220290, 166772364, 1251581989, 493813264, 448347421, 195405023, 2709975567, 677966185, 3703036547, 1463355134, 2715995803, 1338867538, 1343315457, 2802222074, 2684532164, 233230375, 2599980071, 2000651841, 3277868038, 1638401717, 4028070440, 3237316320, 6314154, 819756386, 300326615, 590932579, 1405279636, 3267499572, 3150704214, 2428286686, 3959192993, 3461946742, 1862657033, 1266418056, 963775037, 2089974820, 2263052895, 1917689273, 448879540, 3550394620, 3981727096, 150775221, 3627908307, 1303187396, 508620638, 2975983352, 2726630617, 1817252668, 1876281319, 1457606340, 908771278, 3720792119, 3617206836, 2455994898, 1729034894, 1080033504]
      , u = [976866871, 3556439503, 2881648439, 1522871579, 1555064734, 1336096578, 3548522304, 2579274686, 3574697629, 3205460757, 3593280638, 3338716283, 3079412587, 564236357, 2993598910, 1781952180, 1464380207, 3163844217, 3332601554, 1699332808, 1393555694, 1183702653, 3581086237, 1288719814, 691649499, 2847557200, 2895455976, 3193889540, 2717570544, 1781354906, 1676643554, 2592534050, 3230253752, 1126444790, 2770207658, 2633158820, 2210423226, 2615765581, 2414155088, 3127139286, 673620729, 2805611233, 1269405062, 4015350505, 3341807571, 4149409754, 1057255273, 2012875353, 2162469141, 2276492801, 2601117357, 993977747, 3918593370, 2654263191, 753973209, 36408145, 2530585658, 25011837, 3520020182, 2088578344, 530523599, 2918365339, 1524020338, 1518925132, 3760827505, 3759777254, 1202760957, 3985898139, 3906192525, 674977740, 4174734889, 2031300136, 2019492241, 3983892565, 4153806404, 3822280332, 352677332, 2297720250, 60907813, 90501309, 3286998549, 1016092578, 2535922412, 2839152426, 457141659, 509813237, 4120667899, 652014361, 1966332200, 2975202805, 55981186, 2327461051, 676427537, 3255491064, 2882294119, 3433927263, 1307055953, 942726286, 933058658, 2468411793, 3933900994, 4215176142, 1361170020, 2001714738, 2830558078, 3274259782, 1222529897, 1679025792, 2729314320, 3714953764, 1770335741, 151462246, 3013232138, 1682292957, 1483529935, 471910574, 1539241949, 458788160, 3436315007, 1807016891, 3718408830, 978976581, 1043663428, 3165965781, 1927990952, 4200891579, 2372276910, 3208408903, 3533431907, 1412390302, 2931980059, 4132332400, 1947078029, 3881505623, 4168226417, 2941484381, 1077988104, 1320477388, 886195818, 18198404, 3786409e3, 2509781533, 112762804, 3463356488, 1866414978, 891333506, 18488651, 661792760, 1628790961, 3885187036, 3141171499, 876946877, 2693282273, 1372485963, 791857591, 2686433993, 3759982718, 3167212022, 3472953795, 2716379847, 445679433, 3561995674, 3504004811, 3574258232, 54117162, 3331405415, 2381918588, 3769707343, 4154350007, 1140177722, 4074052095, 668550556, 3214352940, 367459370, 261225585, 2610173221, 4209349473, 3468074219, 3265815641, 314222801, 3066103646, 3808782860, 282218597, 3406013506, 3773591054, 379116347, 1285071038, 846784868, 2669647154, 3771962079, 3550491691, 2305946142, 453669953, 1268987020, 3317592352, 3279303384, 3744833421, 2610507566, 3859509063, 266596637, 3847019092, 517658769, 3462560207, 3443424879, 370717030, 4247526661, 2224018117, 4143653529, 4112773975, 2788324899, 2477274417, 1456262402, 2901442914, 1517677493, 1846949527, 2295493580, 3734397586, 2176403920, 1280348187, 1908823572, 3871786941, 846861322, 1172426758, 3287448474, 3383383037, 1655181056, 3139813346, 901632758, 1897031941, 2986607138, 3066810236, 3447102507, 1393639104, 373351379, 950779232, 625454576, 3124240540, 4148612726, 2007998917, 544563296, 2244738638, 2330496472, 2058025392, 1291430526, 424198748, 50039436, 29584100, 3605783033, 2429876329, 2791104160, 1057563949, 3255363231, 3075367218, 3463963227, 1469046755, 985887462];
    function a(e) {
        for (var t = 0, r = ""; t < e.length; ) {
            var o = e[t++];
            if (127 < o)
                if (191 < o && o < 224) {
                    if (t >= e.length)
                        return r;
                    o = (31 & o) << 6 | 63 & e[t++]
                } else if (223 < o && o < 240) {
                    if (t + 1 >= e.length)
                        return r;
                    o = (15 & o) << 12 | (63 & e[t++]) << 6 | 63 & e[t++]
                } else {
                    if (!(239 < o && o < 248))
                        return r;
                    if (t + 2 >= e.length)
                        return r;
                    o = (7 & o) << 18 | (63 & e[t++]) << 12 | (63 & e[t++]) << 6 | 63 & e[t++]
                }
            if (o <= 65535)
                r += String.fromCharCode(o);
            else {
                if (!(o <= 1114111))
                    return r;
                o -= 65536,
                r = (r += String.fromCharCode(o >> 10 | 55296)) + String.fromCharCode(1023 & o | 56320)
            }
        }
        return r
    }
    function c(e) {
        e = a(e);
        try {
            return JSON.parse(e)
        } catch (r) {
            try {
                var t = e.replace(/(\w+:)|(\w+ :)/g, (function(e) {
                    return '"' + e.substring(0, e.length - 1) + '":'
                }
                )).replace(/'/g, '"');
                return 125 === (t = 125 === t[0].charCodeAt(0) ? t.slice(0, 1) : t)[t.length - 2].charCodeAt(0) && (t = t.slice(0, -2) + "}"),
                JSON.parse(t)
            } catch (o) {
                throw new Error("Invalid JSON")
            }
        }
    }
    function d(e) {
        return e >>> 0
    }
    function p(e, t) {
        return d(e ^ t)
    }
    function f(e, t) {
        return d(e + t | 0)
    }
    function g(e, t, r, o) {
        return d(e << 24 | t << 16 | r << 8 | o)
    }
    function h(e) {
        return [e >>> 24 & 255, e >>> 16 & 255, e >>> 8 & 255, 255 & e]
    }
    function m(e) {
        return "string" == typeof e
    }
    function b(e) {
        return "object" == typeof e && "byteLength"in e
    }
    function y(e) {
        return e instanceof Uint8Array
    }
    function w(e) {
        return m(e) || b(e)
    }
    function v(e, t) {
        var r = !1;
        return Object.keys(e).forEach((function(o) {
            e[o] === t && (r = !0)
        }
        )),
        r
    }
    function q(e) {
        if (m(e)) {
            for (var t = e, r = new Uint8Array(4 * t.length), o = 0, n = 0; n !== t.length; n++) {
                var i = t.charCodeAt(n);
                if (i < 128)
                    r[o++] = i;
                else {
                    if (i < 2048)
                        r[o++] = i >> 6 | 192;
                    else {
                        if (55295 < i && i < 56320) {
                            if (++n >= t.length)
                                return r.subarray(0, o);
                            var s = t.charCodeAt(n);
                            if (s < 56320 || 57343 < s)
                                return r.subarray(0, o);
                            r[o++] = (i = 65536 + ((1023 & i) << 10) + (1023 & s)) >> 18 | 240,
                            r[o++] = i >> 12 & 63 | 128
                        } else
                            r[o++] = i >> 12 | 224;
                        r[o++] = i >> 6 & 63 | 128
                    }
                    r[o++] = 63 & i | 128
                }
            }
            return r.subarray(0, o)
        }
        if (b(e))
            return new Uint8Array(e);
        if (y(e))
            return e;
        throw new Error("Unsupported type")
    }
    function O(e, t) {
        for (var r, o, n, i = e.replace(/[^A-Za-z0-9+/]/g, ""), s = i.length, l = t ? Math.ceil((3 * s + 1 >> 2) / t) * t : 3 * s + 1 >> 2, u = new Uint8Array(l), a = 0, c = 0, d = 0; d < s; d++)
            if (o = 3 & d,
            a |= (64 < (n = i.charCodeAt(d)) && n < 91 ? n - 65 : 96 < n && n < 123 ? n - 71 : 47 < n && n < 58 ? n + 4 : 43 === n ? 62 : 47 === n ? 63 : 0) << 6 * (3 - o),
            3 == o || s - d == 1) {
                for (r = 0; r < 3 && c < l; )
                    u[c] = a >>> (16 >>> r & 24) & 255,
                    r++,
                    c++;
                a = 0
            }
        return u
    }
    return function() {
        function d(e, o, a) {
            if (void 0 === o && (o = t.ECB),
            void 0 === a && (a = r.PKCS5),
            !w(e))
                throw new Error("Key should be a string or an ArrayBuffer / Buffer");
            if (!v(t, o))
                throw new Error("Unsupported mode");
            if (!v(r, a))
                throw new Error("Unsupported padding");
            this.mode = o,
            this.padding = a,
            this.iv = null,
            this.p = n.slice(),
            this.s = [i.slice(), s.slice(), l.slice(), u.slice()],
            e = function(e) {
                if (72 <= e.length)
                    return e;
                for (var t = []; t.length < 72; )
                    for (var r = 0; r < e.length; r++)
                        t.push(e[r]);
                return new Uint8Array(t)
            }(q(e));
            for (var c = 0, d = 0; c < 18; c++,
            d += 4) {
                var f = g(e[d], e[d + 1], e[d + 2], e[d + 3]);
                this.p[c] = p(this.p[c], f)
            }
            for (var h = 0, m = 0, b = 0; b < 18; b += 2) {
                var y = this._encryptBlock(h, m);
                h = y[0],
                m = y[1],
                this.p[b] = h,
                this.p[b + 1] = m
            }
            for (var O = 0; O < 4; O++)
                for (var $ = 0; $ < 256; $ += 2) {
                    var S = this._encryptBlock(h, m);
                    h = S[0],
                    m = S[1],
                    this.s[O][$] = h,
                    this.s[O][$ + 1] = m
                }
        }
        var m, b, $ = d.prototype;
        return $.setIv = function(e) {
            if (!w(e))
                throw new Error("IV should be a string or an ArrayBuffer / Buffer");
            if (8 !== (e = q(e)).length)
                throw new Error("IV should be 8 byte length");
            this.iv = e
        }
        ,
        $.encode = function(e) {
            if (!w(e))
                throw new Error("Encode data should be a string or an ArrayBuffer / Buffer");
            if (this.mode === t.ECB || this.iv)
                return e = function(e, t) {
                    var o = 8 - e.length % 8;
                    if (8 == o && 0 < e.length && t !== r.PKCS5)
                        return e;
                    var n = new Uint8Array(e.length + o)
                      , i = []
                      , s = o
                      , l = 0;
                    switch (t) {
                    case r.PKCS5:
                        l = o;
                        break;
                    case r.ONE_AND_ZEROS:
                        i.push(128),
                        s--;
                        break;
                    case r.SPACES:
                        l = 32
                    }
                    for (; 0 < s; ) {
                        if (t === r.LAST_BYTE && 1 === s) {
                            i.push(o);
                            break
                        }
                        i.push(l),
                        s--
                    }
                    return n.set(e),
                    n.set(i, e.length),
                    n
                }(q(e), this.padding),
                this.mode === t.ECB ? this._encodeECB(e) : this.mode === t.CBC ? this._encodeCBC(e) : void 0;
            throw new Error("IV is not set")
        }
        ,
        $.encodeToBase64 = function(e) {
            return this.encodeToBuffer(e).toString("base64")
        }
        ,
        $.encodeToBuffer = function(e) {
            return Buffer.from(this.encode(e))
        }
        ,
        $._decodeB64 = function(e) {
            return e = 32 < e.length && "string" == typeof e ? O(e) : e,
            "object" != typeof (e = this.decode(e)) && "string" == typeof e && ("{" === e[0] && "}" === e[e.length - 1] || "[" === e[0] && "]" === e[e.length - 1]) ? JSON.parse(e) : e
        }
        ,
        $.decode = function(e, n) {
            if (void 0 === n && (n = o.STRING),
            !w(e))
                throw new Error("Decode data should be a string or an ArrayBuffer / Buffer");
            if (this.mode !== t.ECB && !this.iv)
                throw new Error("IV is not set");
            if ((e = (n !== o.JSON_OBJECT || y(e) ? q : O)(e)).length % 8 != 0)
                throw new Error("Decoded data should be multiple of 8 bytes");
            switch (this.mode) {
            case t.ECB:
                e = this._decodeECB(e);
                break;
            case t.CBC:
                e = this._decodeCBC(e)
            }
            switch (e = function(e, t) {
                var o = 0;
                switch (t) {
                case r.LAST_BYTE:
                case r.PKCS5:
                    var n = e[e.length - 1];
                    n <= 8 && (o = n);
                    break;
                case r.ONE_AND_ZEROS:
                    for (var i = 1; i <= 8; ) {
                        var s = e[e.length - i];
                        if (128 === s) {
                            o = i;
                            break
                        }
                        if (0 !== s)
                            break;
                        i++
                    }
                    break;
                case r.NULL:
                case r.SPACES:
                    for (var l = t === r.SPACES ? 32 : 0, u = 1; u <= 8; ) {
                        if (e[e.length - u] !== l) {
                            o = u - 1;
                            break
                        }
                        u++
                    }
                }
                return e.subarray(0, e.length - o)
            }(e, this.padding),
            n) {
            case o.UINT8_ARRAY:
                return e;
            case o.STRING:
                return a(e);
            case o.JSON_OBJECT:
                return c(e);
            default:
                throw new Error("Unsupported return type")
            }
        }
        ,
        $._encryptBlock = function(e, t) {
            for (var r = 0; r < 16; r++) {
                e = p(e, this.p[r]);
                var o = [t = p(t, this._f(e)), e];
                e = o[0],
                t = o[1]
            }
            var n = [t, e];
            return t = p(t = n[1], this.p[16]),
            [e = p(e = n[0], this.p[17]), t]
        }
        ,
        $._decryptBlock = function(e, t) {
            for (var r = 17; 1 < r; r--) {
                e = p(e, this.p[r]);
                var o = [t = p(t, this._f(e)), e];
                e = o[0],
                t = o[1]
            }
            var n = [t, e];
            return t = p(t = n[1], this.p[1]),
            [e = p(e = n[0], this.p[0]), t]
        }
        ,
        $._f = function(e) {
            var t = f(this.s[0][e >>> 24 & 255], this.s[1][e >>> 16 & 255]);
            return f(p(t, this.s[2][e >>> 8 & 255]), this.s[3][255 & e])
        }
        ,
        $._encodeECB = function(e) {
            for (var t = new Uint8Array(e.length), r = 0; r < e.length; r += 8) {
                var o = g(e[r], e[r + 1], e[r + 2], e[r + 3])
                  , n = g(e[r + 4], e[r + 5], e[r + 6], e[r + 7])
                  , i = this._encryptBlock(o, n);
                o = i[0],
                n = i[1],
                t.set(h(o), r),
                t.set(h(n), r + 4)
            }
            return t
        }
        ,
        $._encodeCBC = function(e) {
            for (var t = new Uint8Array(e.length), r = g(this.iv[0], this.iv[1], this.iv[2], this.iv[3]), o = g(this.iv[4], this.iv[5], this.iv[6], this.iv[7]), n = 0; n < e.length; n += 8) {
                var i = g(e[n], e[n + 1], e[n + 2], e[n + 3])
                  , s = g(e[n + 4], e[n + 5], e[n + 6], e[n + 7])
                  , l = [p(r, i), p(o, s)];
                i = (l = this._encryptBlock(l[0], l[1]))[0],
                o = s = l[1],
                t.set(h(r = i), n),
                t.set(h(s), n + 4)
            }
            return t
        }
        ,
        $._decodeECB = function(e) {
            for (var t = new Uint8Array(e.length), r = 0; r < e.length; r += 8) {
                var o = g(e[r], e[r + 1], e[r + 2], e[r + 3])
                  , n = g(e[r + 4], e[r + 5], e[r + 6], e[r + 7])
                  , i = this._decryptBlock(o, n);
                o = i[0],
                n = i[1],
                t.set(h(o), r),
                t.set(h(n), r + 4)
            }
            return t
        }
        ,
        $._decodeCBC = function(e) {
            for (var t = new Uint8Array(e.length), r = g(this.iv[0], this.iv[1], this.iv[2], this.iv[3]), o = g(this.iv[4], this.iv[5], this.iv[6], this.iv[7]), n = 0; n < e.length; n += 8) {
                var i, s = u = g(e[n], e[n + 1], e[n + 2], e[n + 3]), l = a = g(e[n + 4], e[n + 5], e[n + 6], e[n + 7]), u = (i = this._decryptBlock(u, a))[0], a = i[1];
                a = (i = [p(r, u), p(o, a)])[1],
                r = s,
                o = l,
                t.set(h(i[0]), n),
                t.set(h(a), n + 4)
            }
            return t
        }
        ,
        $ = d,
        b = [{
            key: "MODE",
            get: function() {
                return t
            }
        }, {
            key: "PADDING",
            get: function() {
                return r
            }
        }, {
            key: "TYPE",
            get: function() {
                return o
            }
        }],
        (m = null) && e($.prototype, m),
        b && e($, b),
        Object.defineProperty($, "prototype", {
            writable: !1
        }),
        d
    }()
}();
const Blowfish = blowfish.exports;
!function() {
    function e() {
        return new Promise((e => {
            let t = new FileReader;
            t.onload = () => {
                e(t.result)
            }
            ,
            t.readAsArrayBuffer(this)
        }
        ))
    }
    "File"in self && (File.prototype.arrayBuffer = File.prototype.arrayBuffer || e),
    "Blob"in self && (Blob.prototype.arrayBuffer = Blob.prototype.arrayBuffer || e)
}();
const AUTH_DATA = {
    apptype: "rtj",
    verifycode: "plaintract",
    key: "tdc5%y4yaU@xFi",
    initializationVector: "5X4f$^hp"
}
  , callbackMap = {};
let heartbeatTimer;
const pushPublisher = getPublisher()
  , {QuoteMsgID: QuoteMsgID, QuotationFreq: QuotationFreq, QuotationMsg: QuotationMsg} = $root.jadegold.msg.quotation.pbv2;
function initSocket({url: e, onInit: t, onMessage: r, onclose: o=null, onerror: n=null}) {
    const i = new ReconnectingWebSocket(e);
    return i.onopen = function() {
        t && t()
    }
    ,
    i.onmessage = function(e) {
        r && r(e)
    }
    ,
    i.onclose = function(e) {
        o && o(e)
    }
    ,
    i.onerror = function(e) {}
    ,
    i
}
function initConnection(e) {
    const t = function() {
        return initSocket({
            url: e,
            onInit: async () => {
                const e = new Blowfish(AUTH_DATA.key,Blowfish.MODE.CBC,Blowfish.PADDING.PKCS5);
                console.log(AUTH_DATA, 'AUTH_DATA')
                e.setIv(AUTH_DATA.initializationVector);
                const t = e.encode(`${AUTH_DATA.verifycode}${AUTH_DATA.apptype}${Date.now()}`)
                  , [r] = await l(QuoteMsgID.auth, {
                    auth: {
                        apptype: AUTH_DATA.apptype,
                        token: t
                    }
                });
                r.auth && (publisher.notifyWsStatus({
                    status: 1,
                    msg: "connect"
                }),
                heartbeatTimer && clearInterval(heartbeatTimer),
                heartbeatTimer = setInterval(( () => l(QuoteMsgID.heart_beat, {})), 2e4))
            }
            ,
            onMessage: async ({data: e}) => {
                const t = await e.arrayBuffer()
                  , o = r(t);
                let {msgid: n, seq: i, response: s=[], jsonResp: l} = o;
                console.log(e, QuoteMsgID, 'data')
                try {
                    switch (n) {
                    case QuoteMsgID.latestQuotation:
                    case QuoteMsgID.heart_beat:
                        break;
                    default:
                        i = QuoteMsgID.auth === n ? 0 : Math.abs(i),
                        console.log(i, callbackMap)
                        callbackMap[i] ? (callbackMap[i](l ? JSON.parse(l) : s),
                        Reflect.deleteProperty(callbackMap, i)) : publisher.notify(null, o)
                    }
                } catch (u) {}
            }
            ,
            onclose: async () => {
                publisher.notifyWsStatus({
                    status: -1,
                    msg: "socket close"
                })
            }
        })
    }
      , r = e => QuotationMsg.toObject(QuotationMsg.decode(new Uint8Array(e)), {
        longs: Number
    });
    let o = t();
    const n = () => {
        try {
            o.close()
        } catch (e) {}
    }
      , i = ( () => {
        let e = 0;
        return function(t, r, n=null, i=null) {
            const s = {
                msgid: t,
                seq: e++,
                request: r,
                jsonReq: n ? JSON.stringify(n) : null
            }
              , l = QuotationMsg.verify(s);
            if (l)
                throw Error(l);
            const u = QuotationMsg.create(s)
              , a = QuotationMsg.encode(u).finish();
            t != QuoteMsgID.heart_beat && t != QuoteMsgID.latestQuotation && i && (callbackMap[t === QuoteMsgID.auth ? 0 : e - 1] = i),
            o.send(a)
        }
    }
    )()
      , s = function(e, t, {msgid: r, request: n, json: l}) {
        1 == o.readyState ? i(r, n, l, (t => {
            try {
                e(t)
            } catch (r) {}
        }
        )) : setTimeout(s, 50, ...arguments)
    }
      , l = function(e, t={}) {
        return new Promise(( (r, o) => s(r, o, {
            msgid: e,
            request: t
        })))
    };
    return {
        doRequest: l,
        doJsonRequest: function(e, t={}) {
            return new Promise(( (r, o) => s(r, o, {
                msgid: e,
                json: t
            })))
        },
        reConnect: () => {
            n(),
            o = t()
        }
        ,
        closeConnect: n
    }
}
const {doRequest: doRequest, doJsonRequest: doJsonRequest, publisher: publisher=pushPublisher, reConnect: reConnect, closeConnect: closeConnect} = ( () => {
    let e = {};
    return location.href.indexOf("/article") > 0 || (e = initConnection("wss://rtjwbqt.jzj9999.com:8443/gateway")),
    e
}
)()
  , restartWs = () => {
    reConnect()
}
  , closeWs = () => closeConnect()
  , _contractSubscribe = (e, t) => {
    if (!e || !e.length)
        return;
    const r = [];
    for (const o of e)
        publisher.hasSubscribe(o) ? publisher.triggerNotify(o) : r.push(o);
    return r.length ? doRequest(QuoteMsgID.latestQuotation, {
        codes: r,
        freq: [QuotationFreq.REALTIME]
    }) : void 0
}
  , _contractUnsubscribe = e => {
    if (e && e.length)
        return doRequest(QuoteMsgID.unsubscribe, {
            codes: e,
            freq: [QuotationFreq.INFO]
        })
}
  , quotationSubscribe = ({namespace: e, codes: t, callback: r}) => {
    e && t && (r && publisher.subscribe(t, e, r),
    _contractSubscribe(t))
}
  , quotationUnsubscribe = ({namespace: e, codes: t=null}) => {
    if (e) {
        t && t.length || (t = publisher.listCodesByNamespace(e)),
        publisher.unsubscribe(e, t);
        try {
            _contractUnsubscribe(t)
        } catch (r) {}
    }
}
  , broadcastSubscribe = ({namespace: e, callback: t}) => {
    e && t && publisher.broadcastSubscribe(e, t)
}
  , connectStatusSubscribe = e => {
    publisher.connectStatusSubscribe(e)
}
  , getContractBaseInfo = () => doJsonRequest(QuoteMsgID.codes_info_json, {})
  , getContractCategory = () => doJsonRequest(QuoteMsgID.codes_category_json, {})
  , getMinuteHistory = (e, t=0) => doRequest(QuoteMsgID.qryQuotation, {
    codes: [e],
    freq: [QuotationFreq.INFO],
    queryCondition: {
        infoDays: t
    }
})
  , getDayLineHistory = (e, t=100, r, o) => doRequest(QuoteMsgID.qryQuotation, {
    codes: [e],
    freq: [QuotationFreq[r]],
    queryCondition: {
        size: t,
        endtime: o
    }
})
  , getTick = (e, t=1) => doRequest(QuoteMsgID.qryQuotation, {
    codes: [e],
    freq: [QuotationFreq.TICK],
    queryCondition: {
        size: t
    }
})
  , getQryStatus = () => doRequest(QuoteMsgID.qry_status, {
    codes: ["RTJ"]
})
  , DINfont = "";
// export {_export_sfc as _, getContractCategory as a, broadcastSubscribe as b, connectStatusSubscribe as c, getQryStatus as d, closeWs as e, quotationUnsubscribe as f, getContractBaseInfo as g, getTick as h, getMinuteHistory as i, getDayLineHistory as j, quotationSubscribe as q, restartWs as r};
window.$ = Ss;
window.A = xr;
window.B = ji;
window.C = pr;
window.D = Ta;
window.E = Ki;
window.F = Ao;
window.G = Aa;
window.H = Ra;
window.I = S;
window.J = Vn;
window.K = mn;
window.L = gn;
window.M = jo;
window.N = Lo;
window.O = Wo;
window.P = n;
window.Q = Zo;
window.R = a;
window.S = p;
window.T = qs;
window.U = Nt;
window.V = Sr;
window.W = Bo;
window.X = vn;
window.Y = er;
window.Z = Er;
window._ = At;
window.a = ft;
window.a0 = kr;
window.a1 = l;
window.a2 = qo;
window.a3 = Tr;
window.a4 = ee;
window.a5 = bt;
window.a6 = mt;
window.a7 = _t;
window.a8 = ne;
window.a9 = re;
window.aa = Ol;
window.ab = Rr;
window.ac = Go;
window.ad = Rl;
window.ae = Pi;
window.b = hr;
window.c = bs;
window.d = nr;
window.e = rr;
window.f = fr;
window.g = xt;
window.h = as;
window.i = Pn;
window.j = Uo;
window.k = Ko;
window.l = Yn;
window.m = Fn;
window.n = Zt;
window.o = mr;
window.p = kn;
window.q = ts;
window.r = Ot;
window.s = br;
window.t = xo;
window.u = kt;
window.v = Ni;
window.w = Mn;
window.x = dr;
window.y = Yo;
window.z = Hi;