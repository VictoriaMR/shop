function e(e, t) {
    const n = Object.create(null)
      , r = e.split(",");
    for (let o = 0; o < r.length; o++)
        n[r[o]] = !0;
    return t ? e => !!n[e.toLowerCase()] : e => !!n[e]
}
const t = e("Infinity,undefined,NaN,isFinite,isNaN,parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,BigInt");
function n(e) {
    if (O(e)) {
        const t = {};
        for (let r = 0; r < e.length; r++) {
            const o = e[r]
              , s = P(o) ? i(o) : n(o);
            if (s)
                for (const e in s)
                    t[e] = s[e]
        }
        return t
    }
    return P(e) || j(e) ? e : void 0
}
const r = /;(?![^(]*\))/g
  , o = /:([^]+)/
  , s = new RegExp("\\/\\*.*?\\*\\/","gs");
function i(e) {
    const t = {};
    return e.replace(s, "").split(r).forEach((e => {
        if (e) {
            const n = e.split(o);
            n.length > 1 && (t[n[0].trim()] = n[1].trim())
        }
    }
    )),
    t
}
function a(e) {
    let t = "";
    if (P(e))
        t = e;
    else if (O(e))
        for (let n = 0; n < e.length; n++) {
            const r = a(e[n]);
            r && (t += r + " ")
        }
    else if (j(e))
        for (const n in e)
            e[n] && (t += n + " ");
    return t.trim()
}
function l(e) {
    if (!e)
        return null;
    let {class: t, style: r} = e;
    return t && !P(t) && (e.class = a(t)),
    r && (e.style = n(r)),
    e
}
const c = e("itemscope,allowfullscreen,formnovalidate,ismap,nomodule,novalidate,readonly");
function u(e) {
    return !!e || "" === e
}
function f(e, t) {
    if (e === t)
        return !0;
    let n = T(e)
      , r = T(t);
    if (n || r)
        return !(!n || !r) && e.getTime() === t.getTime();
    if (n = F(e),
    r = F(t),
    n || r)
        return e === t;
    if (n = O(e),
    r = O(t),
    n || r)
        return !(!n || !r) && function(e, t) {
            if (e.length !== t.length)
                return !1;
            let n = !0;
            for (let r = 0; n && r < e.length; r++)
                n = f(e[r], t[r]);
            return n
        }(e, t);
    if (n = j(e),
    r = j(t),
    n || r) {
        if (!n || !r)
            return !1;
        if (Object.keys(e).length !== Object.keys(t).length)
            return !1;
        for (const n in e) {
            const r = e.hasOwnProperty(n)
              , o = t.hasOwnProperty(n);
            if (r && !o || !r && o || !f(e[n], t[n]))
                return !1
        }
    }
    return String(e) === String(t)
}
function d(e, t) {
    return e.findIndex((e => f(e, t)))
}
const p = e => P(e) ? e : null == e ? "" : O(e) || j(e) && (e.toString === M || !k(e.toString)) ? JSON.stringify(e, h, 2) : String(e)
  , h = (e, t) => t && t.__v_isRef ? h(e, t.value) : A(t) ? {
    [`Map(${t.size})`]: [...t.entries()].reduce(( (e, [t,n]) => (e[`${t} =>`] = n,
    e)), {})
} : R(t) ? {
    [`Set(${t.size})`]: [...t.values()]
} : !j(t) || O(t) || D(t) ? t : String(t)
  , m = {}
  , g = []
  , v = () => {}
  , y = () => !1
  , _ = /^on[^a-z]/
  , b = e => _.test(e)
  , w = e => e.startsWith("onUpdate:")
  , S = Object.assign
  , C = (e, t) => {
    const n = e.indexOf(t);
    n > -1 && e.splice(n, 1)
}
  , E = Object.prototype.hasOwnProperty
  , x = (e, t) => E.call(e, t)
  , O = Array.isArray
  , A = e => "[object Map]" === $(e)
  , R = e => "[object Set]" === $(e)
  , T = e => "[object Date]" === $(e)
  , k = e => "function" == typeof e
  , P = e => "string" == typeof e
  , F = e => "symbol" == typeof e
  , j = e => null !== e && "object" == typeof e
  , N = e => j(e) && k(e.then) && k(e.catch)
  , M = Object.prototype.toString
  , $ = e => M.call(e)
  , D = e => "[object Object]" === $(e)
  , L = e => P(e) && "NaN" !== e && "-" !== e[0] && "" + parseInt(e, 10) === e
  , B = e(",key,ref,ref_for,ref_key,onVnodeBeforeMount,onVnodeMounted,onVnodeBeforeUpdate,onVnodeUpdated,onVnodeBeforeUnmount,onVnodeUnmounted")
  , U = e => {
    const t = Object.create(null);
    return n => t[n] || (t[n] = e(n))
}
  , I = /-(\w)/g
  , V = U((e => e.replace(I, ( (e, t) => t ? t.toUpperCase() : ""))))
  , H = /\B([A-Z])/g
  , z = U((e => e.replace(H, "-$1").toLowerCase()))
  , W = U((e => e.charAt(0).toUpperCase() + e.slice(1)))
  , K = U((e => e ? `on${W(e)}` : ""))
  , q = (e, t) => !Object.is(e, t)
  , J = (e, t) => {
    for (let n = 0; n < e.length; n++)
        e[n](t)
}
  , Y = (e, t, n) => {
    Object.defineProperty(e, t, {
        configurable: !0,
        enumerable: !1,
        value: n
    })
}
  , G = e => {
    const t = parseFloat(e);
    return isNaN(t) ? e : t
}
;
let Z;
let X;
class Q {
    constructor(e=!1) {
        this.detached = e,
        this.active = !0,
        this.effects = [],
        this.cleanups = [],
        this.parent = X,
        !e && X && (this.index = (X.scopes || (X.scopes = [])).push(this) - 1)
    }
    run(e) {
        if (this.active) {
            const t = X;
            try {
                return X = this,
                e()
            } finally {
                X = t
            }
        }
    }
    on() {
        X = this
    }
    off() {
        X = this.parent
    }
    stop(e) {
        if (this.active) {
            let t, n;
            for (t = 0,
            n = this.effects.length; t < n; t++)
                this.effects[t].stop();
            for (t = 0,
            n = this.cleanups.length; t < n; t++)
                this.cleanups[t]();
            if (this.scopes)
                for (t = 0,
                n = this.scopes.length; t < n; t++)
                    this.scopes[t].stop(!0);
            if (!this.detached && this.parent && !e) {
                const e = this.parent.scopes.pop();
                e && e !== this && (this.parent.scopes[this.index] = e,
                e.index = this.index)
            }
            this.parent = void 0,
            this.active = !1
        }
    }
}
function ee(e) {
    return new Q(e)
}
function te(e, t=X) {
    t && t.active && t.effects.push(e)
}
function ne() {
    return X
}
function re(e) {
    X && X.cleanups.push(e)
}
const oe = e => {
    const t = new Set(e);
    return t.w = 0,
    t.n = 0,
    t
}
  , se = e => (e.w & ce) > 0
  , ie = e => (e.n & ce) > 0
  , ae = new WeakMap;
let le = 0
  , ce = 1;
let ue;
const fe = Symbol("")
  , de = Symbol("");
class pe {
    constructor(e, t=null, n) {
        this.fn = e,
        this.scheduler = t,
        this.active = !0,
        this.deps = [],
        this.parent = void 0,
        te(this, n)
    }
    run() {
        if (!this.active)
            return this.fn();
        let e = ue
          , t = me;
        for (; e; ) {
            if (e === this)
                return;
            e = e.parent
        }
        try {
            return this.parent = ue,
            ue = this,
            me = !0,
            ce = 1 << ++le,
            le <= 30 ? ( ({deps: e}) => {
                if (e.length)
                    for (let t = 0; t < e.length; t++)
                        e[t].w |= ce
            }
            )(this) : he(this),
            this.fn()
        } finally {
            le <= 30 && (e => {
                const {deps: t} = e;
                if (t.length) {
                    let n = 0;
                    for (let r = 0; r < t.length; r++) {
                        const o = t[r];
                        se(o) && !ie(o) ? o.delete(e) : t[n++] = o,
                        o.w &= ~ce,
                        o.n &= ~ce
                    }
                    t.length = n
                }
            }
            )(this),
            ce = 1 << --le,
            ue = this.parent,
            me = t,
            this.parent = void 0,
            this.deferStop && this.stop()
        }
    }
    stop() {
        ue === this ? this.deferStop = !0 : this.active && (he(this),
        this.onStop && this.onStop(),
        this.active = !1)
    }
}
function he(e) {
    const {deps: t} = e;
    if (t.length) {
        for (let n = 0; n < t.length; n++)
            t[n].delete(e);
        t.length = 0
    }
}
let me = !0;
const ge = [];
function ve() {
    ge.push(me),
    me = !1
}
function ye() {
    const e = ge.pop();
    me = void 0 === e || e
}
function _e(e, t, n) {
    if (me && ue) {
        let t = ae.get(e);
        t || ae.set(e, t = new Map);
        let r = t.get(n);
        r || t.set(n, r = oe()),
        be(r)
    }
}
function be(e, t) {
    let n = !1;
    le <= 30 ? ie(e) || (e.n |= ce,
    n = !se(e)) : n = !e.has(ue),
    n && (e.add(ue),
    ue.deps.push(e))
}
function we(e, t, n, r, o, s) {
    const i = ae.get(e);
    if (!i)
        return;
    let a = [];
    if ("clear" === t)
        a = [...i.values()];
    else if ("length" === n && O(e)) {
        const e = G(r);
        i.forEach(( (t, n) => {
            ("length" === n || n >= e) && a.push(t)
        }
        ))
    } else
        switch (void 0 !== n && a.push(i.get(n)),
        t) {
        case "add":
            O(e) ? L(n) && a.push(i.get("length")) : (a.push(i.get(fe)),
            A(e) && a.push(i.get(de)));
            break;
        case "delete":
            O(e) || (a.push(i.get(fe)),
            A(e) && a.push(i.get(de)));
            break;
        case "set":
            A(e) && a.push(i.get(fe))
        }
    if (1 === a.length)
        a[0] && Se(a[0]);
    else {
        const e = [];
        for (const t of a)
            t && e.push(...t);
        Se(oe(e))
    }
}
function Se(e, t) {
    const n = O(e) ? e : [...e];
    for (const r of n)
        r.computed && Ce(r);
    for (const r of n)
        r.computed || Ce(r)
}
function Ce(e, t) {
    (e !== ue || e.allowRecurse) && (e.scheduler ? e.scheduler() : e.run())
}
const Ee = e("__proto__,__v_isRef,__isVue")
  , xe = new Set(Object.getOwnPropertyNames(Symbol).filter((e => "arguments" !== e && "caller" !== e)).map((e => Symbol[e])).filter(F))
  , Oe = Fe()
  , Ae = Fe(!1, !0)
  , Re = Fe(!0)
  , Te = Fe(!0, !0)
  , ke = Pe();
function Pe() {
    const e = {};
    return ["includes", "indexOf", "lastIndexOf"].forEach((t => {
        e[t] = function(...e) {
            const n = _t(this);
            for (let t = 0, o = this.length; t < o; t++)
                _e(n, 0, t + "");
            const r = n[t](...e);
            return -1 === r || !1 === r ? n[t](...e.map(_t)) : r
        }
    }
    )),
    ["push", "pop", "shift", "unshift", "splice"].forEach((t => {
        e[t] = function(...e) {
            ve();
            const n = _t(this)[t].apply(this, e);
            return ye(),
            n
        }
    }
    )),
    e
}
function Fe(e=!1, t=!1) {
    return function(n, r, o) {
        if ("__v_isReactive" === r)
            return !e;
        if ("__v_isReadonly" === r)
            return e;
        if ("__v_isShallow" === r)
            return t;
        if ("__v_raw" === r && o === (e ? t ? ct : lt : t ? at : it).get(n))
            return n;
        const s = O(n);
        if (!e && s && x(ke, r))
            return Reflect.get(ke, r, o);
        const i = Reflect.get(n, r, o);
        return (F(r) ? xe.has(r) : Ee(r)) ? i : (e || _e(n, 0, r),
        t ? i : xt(i) ? s && L(r) ? i : i.value : j(i) ? e ? pt(i) : ft(i) : i)
    }
}
function je(e=!1) {
    return function(t, n, r, o) {
        let s = t[n];
        if (gt(s) && xt(s) && !xt(r))
            return !1;
        if (!e && (vt(r) || gt(r) || (s = _t(s),
        r = _t(r)),
        !O(t) && xt(s) && !xt(r)))
            return s.value = r,
            !0;
        const i = O(t) && L(n) ? Number(n) < t.length : x(t, n)
          , a = Reflect.set(t, n, r, o);
        return t === _t(o) && (i ? q(r, s) && we(t, "set", n, r) : we(t, "add", n, r)),
        a
    }
}
const Ne = {
    get: Oe,
    set: je(),
    deleteProperty: function(e, t) {
        const n = x(e, t);
        e[t];
        const r = Reflect.deleteProperty(e, t);
        return r && n && we(e, "delete", t, void 0),
        r
    },
    has: function(e, t) {
        const n = Reflect.has(e, t);
        return F(t) && xe.has(t) || _e(e, 0, t),
        n
    },
    ownKeys: function(e) {
        return _e(e, 0, O(e) ? "length" : fe),
        Reflect.ownKeys(e)
    }
}
  , Me = {
    get: Re,
    set: (e, t) => !0,
    deleteProperty: (e, t) => !0
}
  , $e = S({}, Ne, {
    get: Ae,
    set: je(!0)
})
  , De = S({}, Me, {
    get: Te
})
  , Le = e => e
  , Be = e => Reflect.getPrototypeOf(e);
function Ue(e, t, n=!1, r=!1) {
    const o = _t(e = e.__v_raw)
      , s = _t(t);
    n || (t !== s && _e(o, 0, t),
    _e(o, 0, s));
    const {has: i} = Be(o)
      , a = r ? Le : n ? St : wt;
    return i.call(o, t) ? a(e.get(t)) : i.call(o, s) ? a(e.get(s)) : void (e !== o && e.get(t))
}
function Ie(e, t=!1) {
    const n = this.__v_raw
      , r = _t(n)
      , o = _t(e);
    return t || (e !== o && _e(r, 0, e),
    _e(r, 0, o)),
    e === o ? n.has(e) : n.has(e) || n.has(o)
}
function Ve(e, t=!1) {
    return e = e.__v_raw,
    !t && _e(_t(e), 0, fe),
    Reflect.get(e, "size", e)
}
function He(e) {
    e = _t(e);
    const t = _t(this);
    return Be(t).has.call(t, e) || (t.add(e),
    we(t, "add", e, e)),
    this
}
function ze(e, t) {
    t = _t(t);
    const n = _t(this)
      , {has: r, get: o} = Be(n);
    let s = r.call(n, e);
    s || (e = _t(e),
    s = r.call(n, e));
    const i = o.call(n, e);
    return n.set(e, t),
    s ? q(t, i) && we(n, "set", e, t) : we(n, "add", e, t),
    this
}
function We(e) {
    const t = _t(this)
      , {has: n, get: r} = Be(t);
    let o = n.call(t, e);
    o || (e = _t(e),
    o = n.call(t, e)),
    r && r.call(t, e);
    const s = t.delete(e);
    return o && we(t, "delete", e, void 0),
    s
}
function Ke() {
    const e = _t(this)
      , t = 0 !== e.size
      , n = e.clear();
    return t && we(e, "clear", void 0, void 0),
    n
}
function qe(e, t) {
    return function(n, r) {
        const o = this
          , s = o.__v_raw
          , i = _t(s)
          , a = t ? Le : e ? St : wt;
        return !e && _e(i, 0, fe),
        s.forEach(( (e, t) => n.call(r, a(e), a(t), o)))
    }
}
function Je(e, t, n) {
    return function(...r) {
        const o = this.__v_raw
          , s = _t(o)
          , i = A(s)
          , a = "entries" === e || e === Symbol.iterator && i
          , l = "keys" === e && i
          , c = o[e](...r)
          , u = n ? Le : t ? St : wt;
        return !t && _e(s, 0, l ? de : fe),
        {
            next() {
                const {value: e, done: t} = c.next();
                return t ? {
                    value: e,
                    done: t
                } : {
                    value: a ? [u(e[0]), u(e[1])] : u(e),
                    done: t
                }
            },
            [Symbol.iterator]() {
                return this
            }
        }
    }
}
function Ye(e) {
    return function(...t) {
        return "delete" !== e && this
    }
}
function Ge() {
    const e = {
        get(e) {
            return Ue(this, e)
        },
        get size() {
            return Ve(this)
        },
        has: Ie,
        add: He,
        set: ze,
        delete: We,
        clear: Ke,
        forEach: qe(!1, !1)
    }
      , t = {
        get(e) {
            return Ue(this, e, !1, !0)
        },
        get size() {
            return Ve(this)
        },
        has: Ie,
        add: He,
        set: ze,
        delete: We,
        clear: Ke,
        forEach: qe(!1, !0)
    }
      , n = {
        get(e) {
            return Ue(this, e, !0)
        },
        get size() {
            return Ve(this, !0)
        },
        has(e) {
            return Ie.call(this, e, !0)
        },
        add: Ye("add"),
        set: Ye("set"),
        delete: Ye("delete"),
        clear: Ye("clear"),
        forEach: qe(!0, !1)
    }
      , r = {
        get(e) {
            return Ue(this, e, !0, !0)
        },
        get size() {
            return Ve(this, !0)
        },
        has(e) {
            return Ie.call(this, e, !0)
        },
        add: Ye("add"),
        set: Ye("set"),
        delete: Ye("delete"),
        clear: Ye("clear"),
        forEach: qe(!0, !0)
    };
    return ["keys", "values", "entries", Symbol.iterator].forEach((o => {
        e[o] = Je(o, !1, !1),
        n[o] = Je(o, !0, !1),
        t[o] = Je(o, !1, !0),
        r[o] = Je(o, !0, !0)
    }
    )),
    [e, n, t, r]
}
const [Ze,Xe,Qe,et] = Ge();
function tt(e, t) {
    const n = t ? e ? et : Qe : e ? Xe : Ze;
    return (t, r, o) => "__v_isReactive" === r ? !e : "__v_isReadonly" === r ? e : "__v_raw" === r ? t : Reflect.get(x(n, r) && r in t ? n : t, r, o)
}
const nt = {
    get: tt(!1, !1)
}
  , rt = {
    get: tt(!1, !0)
}
  , ot = {
    get: tt(!0, !1)
}
  , st = {
    get: tt(!0, !0)
}
  , it = new WeakMap
  , at = new WeakMap
  , lt = new WeakMap
  , ct = new WeakMap;
function ut(e) {
    return e.__v_skip || !Object.isExtensible(e) ? 0 : function(e) {
        switch (e) {
        case "Object":
        case "Array":
            return 1;
        case "Map":
        case "Set":
        case "WeakMap":
        case "WeakSet":
            return 2;
        default:
            return 0
        }
    }((e => $(e).slice(8, -1))(e))
}
function ft(e) {
    return gt(e) ? e : ht(e, !1, Ne, nt, it)
}
function dt(e) {
    return ht(e, !1, $e, rt, at)
}
function pt(e) {
    return ht(e, !0, Me, ot, lt)
}
function ht(e, t, n, r, o) {
    if (!j(e))
        return e;
    if (e.__v_raw && (!t || !e.__v_isReactive))
        return e;
    const s = o.get(e);
    if (s)
        return s;
    const i = ut(e);
    if (0 === i)
        return e;
    const a = new Proxy(e,2 === i ? r : n);
    return o.set(e, a),
    a
}
function mt(e) {
    return gt(e) ? mt(e.__v_raw) : !(!e || !e.__v_isReactive)
}
function gt(e) {
    return !(!e || !e.__v_isReadonly)
}
function vt(e) {
    return !(!e || !e.__v_isShallow)
}
function yt(e) {
    return mt(e) || gt(e)
}
function _t(e) {
    const t = e && e.__v_raw;
    return t ? _t(t) : e
}
function bt(e) {
    return Y(e, "__v_skip", !0),
    e
}
const wt = e => j(e) ? ft(e) : e
  , St = e => j(e) ? pt(e) : e;
function Ct(e) {
    me && ue && be((e = _t(e)).dep || (e.dep = oe()))
}
function Et(e, t) {
    (e = _t(e)).dep && Se(e.dep)
}
function xt(e) {
    return !(!e || !0 !== e.__v_isRef)
}
function Ot(e) {
    return Rt(e, !1)
}
function At(e) {
    return Rt(e, !0)
}
function Rt(e, t) {
    return xt(e) ? e : new Tt(e,t)
}
class Tt {
    constructor(e, t) {
        this.__v_isShallow = t,
        this.dep = void 0,
        this.__v_isRef = !0,
        this._rawValue = t ? e : _t(e),
        this._value = t ? e : wt(e)
    }
    get value() {
        return Ct(this),
        this._value
    }
    set value(e) {
        const t = this.__v_isShallow || vt(e) || gt(e);
        e = t ? e : _t(e),
        q(e, this._rawValue) && (this._rawValue = e,
        this._value = t ? e : wt(e),
        Et(this))
    }
}
function kt(e) {
    return xt(e) ? e.value : e
}
const Pt = {
    get: (e, t, n) => kt(Reflect.get(e, t, n)),
    set: (e, t, n, r) => {
        const o = e[t];
        return xt(o) && !xt(n) ? (o.value = n,
        !0) : Reflect.set(e, t, n, r)
    }
};
function Ft(e) {
    return mt(e) ? e : new Proxy(e,Pt)
}
class jt {
    constructor(e) {
        this.dep = void 0,
        this.__v_isRef = !0;
        const {get: t, set: n} = e(( () => Ct(this)), ( () => Et(this)));
        this._get = t,
        this._set = n
    }
    get value() {
        return this._get()
    }
    set value(e) {
        this._set(e)
    }
}
function Nt(e) {
    const t = O(e) ? new Array(e.length) : {};
    for (const n in e)
        t[n] = $t(e, n);
    return t
}
class Mt {
    constructor(e, t, n) {
        this._object = e,
        this._key = t,
        this._defaultValue = n,
        this.__v_isRef = !0
    }
    get value() {
        const e = this._object[this._key];
        return void 0 === e ? this._defaultValue : e
    }
    set value(e) {
        this._object[this._key] = e
    }
}
function $t(e, t, n) {
    const r = e[t];
    return xt(r) ? r : new Mt(e,t,n)
}
var Dt;
class Lt {
    constructor(e, t, n, r) {
        this._setter = t,
        this.dep = void 0,
        this.__v_isRef = !0,
        this[Dt] = !1,
        this._dirty = !0,
        this.effect = new pe(e,( () => {
            this._dirty || (this._dirty = !0,
            Et(this))
        }
        )),
        this.effect.computed = this,
        this.effect.active = this._cacheable = !r,
        this.__v_isReadonly = n
    }
    get value() {
        const e = _t(this);
        return Ct(e),
        !e._dirty && e._cacheable || (e._dirty = !1,
        e._value = e.effect.run()),
        e._value
    }
    set value(e) {
        this._setter(e)
    }
}
function Bt(e, t, n, r) {
    let o;
    try {
        o = r ? e(...r) : e()
    } catch (s) {
        It(s, t, n)
    }
    return o
}
function Ut(e, t, n, r) {
    if (k(e)) {
        const o = Bt(e, t, n, r);
        return o && N(o) && o.catch((e => {
            It(e, t, n)
        }
        )),
        o
    }
    const o = [];
    for (let s = 0; s < e.length; s++)
        o.push(Ut(e[s], t, n, r));
    return o
}
function It(e, t, n, r=!0) {
    t && t.vnode;
    if (t) {
        let r = t.parent;
        const o = t.proxy
          , s = n;
        for (; r; ) {
            const t = r.ec;
            if (t)
                for (let n = 0; n < t.length; n++)
                    if (!1 === t[n](e, o, s))
                        return;
            r = r.parent
        }
        const i = t.appContext.config.errorHandler;
        if (i)
            return void Bt(i, null, 10, [e, o, s])
    }
}
Dt = "__v_isReadonly";
let Vt = !1
  , Ht = !1;
const zt = [];
let Wt = 0;
const Kt = [];
let qt = null
  , Jt = 0;
const Yt = Promise.resolve();
let Gt = null;
function Zt(e) {
    const t = Gt || Yt;
    return e ? t.then(this ? e.bind(this) : e) : t
}
function Xt(e) {
    zt.length && zt.includes(e, Vt && e.allowRecurse ? Wt + 1 : Wt) || (null == e.id ? zt.push(e) : zt.splice(function(e) {
        let t = Wt + 1
          , n = zt.length;
        for (; t < n; ) {
            const r = t + n >>> 1;
            rn(zt[r]) < e ? t = r + 1 : n = r
        }
        return t
    }(e.id), 0, e),
    Qt())
}
function Qt() {
    Vt || Ht || (Ht = !0,
    Gt = Yt.then(sn))
}
function en(e) {
    O(e) ? Kt.push(...e) : qt && qt.includes(e, e.allowRecurse ? Jt + 1 : Jt) || Kt.push(e),
    Qt()
}
function tn(e, t=(Vt ? Wt + 1 : 0)) {
    for (; t < zt.length; t++) {
        const e = zt[t];
        e && e.pre && (zt.splice(t, 1),
        t--,
        e())
    }
}
function nn(e) {
    if (Kt.length) {
        const e = [...new Set(Kt)];
        if (Kt.length = 0,
        qt)
            return void qt.push(...e);
        for (qt = e,
        qt.sort(( (e, t) => rn(e) - rn(t))),
        Jt = 0; Jt < qt.length; Jt++)
            qt[Jt]();
        qt = null,
        Jt = 0
    }
}
const rn = e => null == e.id ? 1 / 0 : e.id
  , on = (e, t) => {
    const n = rn(e) - rn(t);
    if (0 === n) {
        if (e.pre && !t.pre)
            return -1;
        if (t.pre && !e.pre)
            return 1
    }
    return n
}
;
function sn(e) {
    Ht = !1,
    Vt = !0,
    zt.sort(on);
    try {
        for (Wt = 0; Wt < zt.length; Wt++) {
            const e = zt[Wt];
            e && !1 !== e.active && Bt(e, null, 14)
        }
    } finally {
        Wt = 0,
        zt.length = 0,
        nn(),
        Vt = !1,
        Gt = null,
        (zt.length || Kt.length) && sn()
    }
}
let an, ln = [];
function cn(e, t, ...n) {
    if (e.isUnmounted)
        return;
    const r = e.vnode.props || m;
    let o = n;
    const s = t.startsWith("update:")
      , i = s && t.slice(7);
    if (i && i in r) {
        const e = `${"modelValue" === i ? "model" : i}Modifiers`
          , {number: t, trim: s} = r[e] || m;
        s && (o = n.map((e => P(e) ? e.trim() : e))),
        t && (o = n.map(G))
    }
    let a, l = r[a = K(t)] || r[a = K(V(t))];
    !l && s && (l = r[a = K(z(t))]),
    l && Ut(l, e, 6, o);
    const c = r[a + "Once"];
    if (c) {
        if (e.emitted) {
            if (e.emitted[a])
                return
        } else
            e.emitted = {};
        e.emitted[a] = !0,
        Ut(c, e, 6, o)
    }
}
function un(e, t, n=!1) {
    const r = t.emitsCache
      , o = r.get(e);
    if (void 0 !== o)
        return o;
    const s = e.emits;
    let i = {}
      , a = !1;
    if (!k(e)) {
        const r = e => {
            const n = un(e, t, !0);
            n && (a = !0,
            S(i, n))
        }
        ;
        !n && t.mixins.length && t.mixins.forEach(r),
        e.extends && r(e.extends),
        e.mixins && e.mixins.forEach(r)
    }
    return s || a ? (O(s) ? s.forEach((e => i[e] = null)) : S(i, s),
    j(e) && r.set(e, i),
    i) : (j(e) && r.set(e, null),
    null)
}
function fn(e, t) {
    return !(!e || !b(t)) && (t = t.slice(2).replace(/Once$/, ""),
    x(e, t[0].toLowerCase() + t.slice(1)) || x(e, z(t)) || x(e, t))
}
let dn = null
  , pn = null;
function hn(e) {
    const t = dn;
    return dn = e,
    pn = e && e.type.__scopeId || null,
    t
}
function mn(e) {
    pn = e
}
function gn() {
    pn = null
}
function vn(e, t=dn, n) {
    if (!t)
        return e;
    if (e._n)
        return e;
    const r = (...n) => {
        r._d && $o(-1);
        const o = hn(t);
        let s;
        try {
            s = e(...n)
        } finally {
            hn(o),
            r._d && $o(1)
        }
        return s
    }
    ;
    return r._n = !0,
    r._c = !0,
    r._d = !0,
    r
}
function yn(e) {
    const {type: t, vnode: n, proxy: r, withProxy: o, props: s, propsOptions: [i], slots: a, attrs: l, emit: c, render: u, renderCache: f, data: d, setupState: p, ctx: h, inheritAttrs: m} = e;
    let g, v;
    const y = hn(e);
    try {
        if (4 & n.shapeFlag) {
            const e = o || r;
            g = Xo(u.call(e, e, f, s, p, d, h)),
            v = l
        } else {
            const e = t;
            0,
            g = Xo(e.length > 1 ? e(s, {
                attrs: l,
                slots: a,
                emit: c
            }) : e(s, null)),
            v = t.props ? l : _n(l)
        }
    } catch (b) {
        Po.length = 0,
        It(b, e, 1),
        g = Ko(To)
    }
    let _ = g;
    if (v && !1 !== m) {
        const e = Object.keys(v)
          , {shapeFlag: t} = _;
        e.length && 7 & t && (i && e.some(w) && (v = bn(v, i)),
        _ = Jo(_, v))
    }
    return n.dirs && (_ = Jo(_),
    _.dirs = _.dirs ? _.dirs.concat(n.dirs) : n.dirs),
    n.transition && (_.transition = n.transition),
    g = _,
    hn(y),
    g
}
const _n = e => {
    let t;
    for (const n in e)
        ("class" === n || "style" === n || b(n)) && ((t || (t = {}))[n] = e[n]);
    return t
}
  , bn = (e, t) => {
    const n = {};
    for (const r in e)
        w(r) && r.slice(9)in t || (n[r] = e[r]);
    return n
}
;
function wn(e, t, n) {
    const r = Object.keys(t);
    if (r.length !== Object.keys(e).length)
        return !0;
    for (let o = 0; o < r.length; o++) {
        const s = r[o];
        if (t[s] !== e[s] && !fn(n, s))
            return !0
    }
    return !1
}
function Sn({vnode: e, parent: t}, n) {
    for (; t && t.subTree === e; )
        (e = t.vnode).el = n,
        t = t.parent
}
const Cn = e => e.__isSuspense
  , En = {
    name: "Suspense",
    __isSuspense: !0,
    process(e, t, n, r, o, s, i, a, l, c) {
        null == e ? function(e, t, n, r, o, s, i, a, l) {
            const {p: c, o: {createElement: u}} = l
              , f = u("div")
              , d = e.suspense = On(e, o, r, t, f, n, s, i, a, l);
            c(null, d.pendingBranch = e.ssContent, f, null, r, d, s, i),
            d.deps > 0 ? (xn(e, "onPending"),
            xn(e, "onFallback"),
            c(null, e.ssFallback, t, n, r, null, s, i),
            Tn(d, e.ssFallback)) : d.resolve()
        }(t, n, r, o, s, i, a, l, c) : function(e, t, n, r, o, s, i, a, {p: l, um: c, o: {createElement: u}}) {
            const f = t.suspense = e.suspense;
            f.vnode = t,
            t.el = e.el;
            const d = t.ssContent
              , p = t.ssFallback
              , {activeBranch: h, pendingBranch: m, isInFallback: g, isHydrating: v} = f;
            if (m)
                f.pendingBranch = d,
                Io(d, m) ? (l(m, d, f.hiddenContainer, null, o, f, s, i, a),
                f.deps <= 0 ? f.resolve() : g && (l(h, p, n, r, o, null, s, i, a),
                Tn(f, p))) : (f.pendingId++,
                v ? (f.isHydrating = !1,
                f.activeBranch = m) : c(m, o, f),
                f.deps = 0,
                f.effects.length = 0,
                f.hiddenContainer = u("div"),
                g ? (l(null, d, f.hiddenContainer, null, o, f, s, i, a),
                f.deps <= 0 ? f.resolve() : (l(h, p, n, r, o, null, s, i, a),
                Tn(f, p))) : h && Io(d, h) ? (l(h, d, n, r, o, f, s, i, a),
                f.resolve(!0)) : (l(null, d, f.hiddenContainer, null, o, f, s, i, a),
                f.deps <= 0 && f.resolve()));
            else if (h && Io(d, h))
                l(h, d, n, r, o, f, s, i, a),
                Tn(f, d);
            else if (xn(t, "onPending"),
            f.pendingBranch = d,
            f.pendingId++,
            l(null, d, f.hiddenContainer, null, o, f, s, i, a),
            f.deps <= 0)
                f.resolve();
            else {
                const {timeout: e, pendingId: t} = f;
                e > 0 ? setTimeout(( () => {
                    f.pendingId === t && f.fallback(p)
                }
                ), e) : 0 === e && f.fallback(p)
            }
        }(e, t, n, r, o, i, a, l, c)
    },
    hydrate: function(e, t, n, r, o, s, i, a, l) {
        const c = t.suspense = On(t, r, n, e.parentNode, document.createElement("div"), null, o, s, i, a, !0)
          , u = l(e, c.pendingBranch = t.ssContent, n, c, s, i);
        0 === c.deps && c.resolve();
        return u
    },
    create: On,
    normalize: function(e) {
        const {shapeFlag: t, children: n} = e
          , r = 32 & t;
        e.ssContent = An(r ? n.default : n),
        e.ssFallback = r ? An(n.fallback) : Ko(To)
    }
};
function xn(e, t) {
    const n = e.props && e.props[t];
    k(n) && n()
}
function On(e, t, n, r, o, s, i, a, l, c, u=!1) {
    const {p: f, m: d, um: p, n: h, o: {parentNode: m, remove: g}} = c
      , v = G(e.props && e.props.timeout)
      , y = {
        vnode: e,
        parent: t,
        parentComponent: n,
        isSVG: i,
        container: r,
        hiddenContainer: o,
        anchor: s,
        deps: 0,
        pendingId: 0,
        timeout: "number" == typeof v ? v : -1,
        activeBranch: null,
        pendingBranch: null,
        isInFallback: !0,
        isHydrating: u,
        isUnmounted: !1,
        effects: [],
        resolve(e=!1) {
            const {vnode: t, activeBranch: n, pendingBranch: r, pendingId: o, effects: s, parentComponent: i, container: a} = y;
            if (y.isHydrating)
                y.isHydrating = !1;
            else if (!e) {
                const e = n && r.transition && "out-in" === r.transition.mode;
                e && (n.transition.afterLeave = () => {
                    o === y.pendingId && d(r, a, t, 0)
                }
                );
                let {anchor: t} = y;
                n && (t = h(n),
                p(n, i, y, !0)),
                e || d(r, a, t, 0)
            }
            Tn(y, r),
            y.pendingBranch = null,
            y.isInFallback = !1;
            let l = y.parent
              , c = !1;
            for (; l; ) {
                if (l.pendingBranch) {
                    l.effects.push(...s),
                    c = !0;
                    break
                }
                l = l.parent
            }
            c || en(s),
            y.effects = [],
            xn(t, "onResolve")
        },
        fallback(e) {
            if (!y.pendingBranch)
                return;
            const {vnode: t, activeBranch: n, parentComponent: r, container: o, isSVG: s} = y;
            xn(t, "onFallback");
            const i = h(n)
              , c = () => {
                y.isInFallback && (f(null, e, o, i, r, null, s, a, l),
                Tn(y, e))
            }
              , u = e.transition && "out-in" === e.transition.mode;
            u && (n.transition.afterLeave = c),
            y.isInFallback = !0,
            p(n, r, null, !0),
            u || c()
        },
        move(e, t, n) {
            y.activeBranch && d(y.activeBranch, e, t, n),
            y.container = e
        },
        next: () => y.activeBranch && h(y.activeBranch),
        registerDep(e, t) {
            const n = !!y.pendingBranch;
            n && y.deps++;
            const r = e.vnode.el;
            e.asyncDep.catch((t => {
                It(t, e, 0)
            }
            )).then((o => {
                if (e.isUnmounted || y.isUnmounted || y.pendingId !== e.suspenseId)
                    return;
                e.asyncResolved = !0;
                const {vnode: s} = e;
                ms(e, o, !1),
                r && (s.el = r);
                const a = !r && e.subTree.el;
                t(e, s, m(r || e.subTree.el), r ? null : h(e.subTree), y, i, l),
                a && g(a),
                Sn(e, s.el),
                n && 0 == --y.deps && y.resolve()
            }
            ))
        },
        unmount(e, t) {
            y.isUnmounted = !0,
            y.activeBranch && p(y.activeBranch, n, e, t),
            y.pendingBranch && p(y.pendingBranch, n, e, t)
        }
    };
    return y
}
function An(e) {
    let t;
    if (k(e)) {
        const n = Mo && e._c;
        n && (e._d = !1,
        jo()),
        e = e(),
        n && (e._d = !0,
        t = Fo,
        No())
    }
    if (O(e)) {
        const t = function(e) {
            let t;
            for (let n = 0; n < e.length; n++) {
                const r = e[n];
                if (!Uo(r))
                    return;
                if (r.type !== To || "v-if" === r.children) {
                    if (t)
                        return;
                    t = r
                }
            }
            return t
        }(e);
        e = t
    }
    return e = Xo(e),
    t && !e.dynamicChildren && (e.dynamicChildren = t.filter((t => t !== e))),
    e
}
function Rn(e, t) {
    t && t.pendingBranch ? O(e) ? t.effects.push(...e) : t.effects.push(e) : en(e)
}
function Tn(e, t) {
    e.activeBranch = t;
    const {vnode: n, parentComponent: r} = e
      , o = n.el = t.el;
    r && r.subTree === n && (r.vnode.el = o,
    Sn(r, o))
}
function kn(e, t) {
    if (is) {
        let n = is.provides;
        const r = is.parent && is.parent.provides;
        r === n && (n = is.provides = Object.create(r)),
        n[e] = t
    } else
        ;
}
function Pn(e, t, n=!1) {
    const r = is || dn;
    if (r) {
        const o = null == r.parent ? r.vnode.appContext && r.vnode.appContext.provides : r.parent.provides;
        if (o && e in o)
            return o[e];
        if (arguments.length > 1)
            return n && k(t) ? t.call(r.proxy) : t
    }
}
function Fn(e, t) {
    return $n(e, null, t)
}
function jn(e, t) {
    return $n(e, null, {
        flush: "post"
    })
}
const Nn = {};
function Mn(e, t, n) {
    return $n(e, t, n)
}
function $n(e, t, {immediate: n, deep: r, flush: o, onTrack: s, onTrigger: i}=m) {
    const a = is;
    let l, c, u = !1, f = !1;
    if (xt(e) ? (l = () => e.value,
    u = vt(e)) : mt(e) ? (l = () => e,
    r = !0) : O(e) ? (f = !0,
    u = e.some((e => mt(e) || vt(e))),
    l = () => e.map((e => xt(e) ? e.value : mt(e) ? Bn(e) : k(e) ? Bt(e, a, 2) : void 0))) : l = k(e) ? t ? () => Bt(e, a, 2) : () => {
        if (!a || !a.isUnmounted)
            return c && c(),
            Ut(e, a, 3, [p])
    }
    : v,
    t && r) {
        const e = l;
        l = () => Bn(e())
    }
    let d, p = e => {
        c = _.onStop = () => {
            Bt(e, a, 4)
        }
    }
    ;
    if (ps) {
        if (p = v,
        t ? n && Ut(t, a, 3, [l(), f ? [] : void 0, p]) : l(),
        "sync" !== o)
            return v;
        {
            const e = Es();
            d = e.__watcherHandles || (e.__watcherHandles = [])
        }
    }
    let h = f ? new Array(e.length).fill(Nn) : Nn;
    const g = () => {
        if (_.active)
            if (t) {
                const e = _.run();
                (r || u || (f ? e.some(( (e, t) => q(e, h[t]))) : q(e, h))) && (c && c(),
                Ut(t, a, 3, [e, h === Nn ? void 0 : f && h[0] === Nn ? [] : h, p]),
                h = e)
            } else
                _.run()
    }
    ;
    let y;
    g.allowRecurse = !!t,
    "sync" === o ? y = g : "post" === o ? y = () => mo(g, a && a.suspense) : (g.pre = !0,
    a && (g.id = a.uid),
    y = () => Xt(g));
    const _ = new pe(l,y);
    t ? n ? g() : h = _.run() : "post" === o ? mo(_.run.bind(_), a && a.suspense) : _.run();
    const b = () => {
        _.stop(),
        a && a.scope && C(a.scope.effects, _)
    }
    ;
    return d && d.push(b),
    b
}
function Dn(e, t, n) {
    const r = this.proxy
      , o = P(e) ? e.includes(".") ? Ln(r, e) : () => r[e] : e.bind(r, r);
    let s;
    k(t) ? s = t : (s = t.handler,
    n = t);
    const i = is;
    ls(this);
    const a = $n(o, s.bind(r), n);
    return i ? ls(i) : cs(),
    a
}
function Ln(e, t) {
    const n = t.split(".");
    return () => {
        let t = e;
        for (let e = 0; e < n.length && t; e++)
            t = t[n[e]];
        return t
    }
}
function Bn(e, t) {
    if (!j(e) || e.__v_skip)
        return e;
    if ((t = t || new Set).has(e))
        return e;
    if (t.add(e),
    xt(e))
        Bn(e.value, t);
    else if (O(e))
        for (let n = 0; n < e.length; n++)
            Bn(e[n], t);
    else if (R(e) || A(e))
        e.forEach((e => {
            Bn(e, t)
        }
        ));
    else if (D(e))
        for (const n in e)
            Bn(e[n], t);
    return e
}
function Un() {
    const e = {
        isMounted: !1,
        isLeaving: !1,
        isUnmounting: !1,
        leavingVNodes: new Map
    };
    return fr(( () => {
        e.isMounted = !0
    }
    )),
    hr(( () => {
        e.isUnmounting = !0
    }
    )),
    e
}
const In = [Function, Array]
  , Vn = {
    name: "BaseTransition",
    props: {
        mode: String,
        appear: Boolean,
        persisted: Boolean,
        onBeforeEnter: In,
        onEnter: In,
        onAfterEnter: In,
        onEnterCancelled: In,
        onBeforeLeave: In,
        onLeave: In,
        onAfterLeave: In,
        onLeaveCancelled: In,
        onBeforeAppear: In,
        onAppear: In,
        onAfterAppear: In,
        onAppearCancelled: In
    },
    setup(e, {slots: t}) {
        const n = as()
          , r = Un();
        let o;
        return () => {
            const s = t.default && Jn(t.default(), !0);
            if (!s || !s.length)
                return;
            let i = s[0];
            if (s.length > 1)
                for (const e of s)
                    if (e.type !== To) {
                        i = e;
                        break
                    }
            const a = _t(e)
              , {mode: l} = a;
            if (r.isLeaving)
                return Wn(i);
            const c = Kn(i);
            if (!c)
                return Wn(i);
            const u = zn(c, a, r, n);
            qn(c, u);
            const f = n.subTree
              , d = f && Kn(f);
            let p = !1;
            const {getTransitionKey: h} = c.type;
            if (h) {
                const e = h();
                void 0 === o ? o = e : e !== o && (o = e,
                p = !0)
            }
            if (d && d.type !== To && (!Io(c, d) || p)) {
                const e = zn(d, a, r, n);
                if (qn(d, e),
                "out-in" === l)
                    return r.isLeaving = !0,
                    e.afterLeave = () => {
                        r.isLeaving = !1,
                        !1 !== n.update.active && n.update()
                    }
                    ,
                    Wn(i);
                "in-out" === l && c.type !== To && (e.delayLeave = (e, t, n) => {
                    Hn(r, d)[String(d.key)] = d,
                    e._leaveCb = () => {
                        t(),
                        e._leaveCb = void 0,
                        delete u.delayedLeave
                    }
                    ,
                    u.delayedLeave = n
                }
                )
            }
            return i
        }
    }
};
function Hn(e, t) {
    const {leavingVNodes: n} = e;
    let r = n.get(t.type);
    return r || (r = Object.create(null),
    n.set(t.type, r)),
    r
}
function zn(e, t, n, r) {
    const {appear: o, mode: s, persisted: i=!1, onBeforeEnter: a, onEnter: l, onAfterEnter: c, onEnterCancelled: u, onBeforeLeave: f, onLeave: d, onAfterLeave: p, onLeaveCancelled: h, onBeforeAppear: m, onAppear: g, onAfterAppear: v, onAppearCancelled: y} = t
      , _ = String(e.key)
      , b = Hn(n, e)
      , w = (e, t) => {
        e && Ut(e, r, 9, t)
    }
      , S = (e, t) => {
        const n = t[1];
        w(e, t),
        O(e) ? e.every((e => e.length <= 1)) && n() : e.length <= 1 && n()
    }
      , C = {
        mode: s,
        persisted: i,
        beforeEnter(t) {
            let r = a;
            if (!n.isMounted) {
                if (!o)
                    return;
                r = m || a
            }
            t._leaveCb && t._leaveCb(!0);
            const s = b[_];
            s && Io(e, s) && s.el._leaveCb && s.el._leaveCb(),
            w(r, [t])
        },
        enter(e) {
            let t = l
              , r = c
              , s = u;
            if (!n.isMounted) {
                if (!o)
                    return;
                t = g || l,
                r = v || c,
                s = y || u
            }
            let i = !1;
            const a = e._enterCb = t => {
                i || (i = !0,
                w(t ? s : r, [e]),
                C.delayedLeave && C.delayedLeave(),
                e._enterCb = void 0)
            }
            ;
            t ? S(t, [e, a]) : a()
        },
        leave(t, r) {
            const o = String(e.key);
            if (t._enterCb && t._enterCb(!0),
            n.isUnmounting)
                return r();
            w(f, [t]);
            let s = !1;
            const i = t._leaveCb = n => {
                s || (s = !0,
                r(),
                w(n ? h : p, [t]),
                t._leaveCb = void 0,
                b[o] === e && delete b[o])
            }
            ;
            b[o] = e,
            d ? S(d, [t, i]) : i()
        },
        clone: e => zn(e, t, n, r)
    };
    return C
}
function Wn(e) {
    if (Xn(e))
        return (e = Jo(e)).children = null,
        e
}
function Kn(e) {
    return Xn(e) ? e.children ? e.children[0] : void 0 : e
}
function qn(e, t) {
    6 & e.shapeFlag && e.component ? qn(e.component.subTree, t) : 128 & e.shapeFlag ? (e.ssContent.transition = t.clone(e.ssContent),
    e.ssFallback.transition = t.clone(e.ssFallback)) : e.transition = t
}
function Jn(e, t=!1, n) {
    let r = []
      , o = 0;
    for (let s = 0; s < e.length; s++) {
        let i = e[s];
        const a = null == n ? i.key : String(n) + String(null != i.key ? i.key : s);
        i.type === Ao ? (128 & i.patchFlag && o++,
        r = r.concat(Jn(i.children, t, a))) : (t || i.type !== To) && r.push(null != a ? Jo(i, {
            key: a
        }) : i)
    }
    if (o > 1)
        for (let s = 0; s < r.length; s++)
            r[s].patchFlag = -2;
    return r
}
function Yn(e) {
    return k(e) ? {
        setup: e,
        name: e.name
    } : e
}
const Gn = e => !!e.type.__asyncLoader;
function Zn(e, t) {
    const {ref: n, props: r, children: o, ce: s} = t.vnode
      , i = Ko(e, r, o);
    return i.ref = n,
    i.ce = s,
    delete t.vnode.ce,
    i
}
const Xn = e => e.type.__isKeepAlive
  , Qn = {
    name: "KeepAlive",
    __isKeepAlive: !0,
    props: {
        include: [String, RegExp, Array],
        exclude: [String, RegExp, Array],
        max: [String, Number]
    },
    setup(e, {slots: t}) {
        const n = as()
          , r = n.ctx;
        if (!r.renderer)
            return () => {
                const e = t.default && t.default();
                return e && 1 === e.length ? e[0] : e
            }
            ;
        const o = new Map
          , s = new Set;
        let i = null;
        const a = n.suspense
          , {renderer: {p: l, m: c, um: u, o: {createElement: f}}} = r
          , d = f("div");
        function p(e) {
            ir(e),
            u(e, n, a, !0)
        }
        function h(e) {
            o.forEach(( (t, n) => {
                const r = _s(t.type);
                !r || e && e(r) || m(n)
            }
            ))
        }
        function m(e) {
            const t = o.get(e);
            i && t.type === i.type ? i && ir(i) : p(t),
            o.delete(e),
            s.delete(e)
        }
        r.activate = (e, t, n, r, o) => {
            const s = e.component;
            c(e, t, n, 0, a),
            l(s.vnode, e, t, n, s, a, r, e.slotScopeIds, o),
            mo(( () => {
                s.isDeactivated = !1,
                s.a && J(s.a);
                const t = e.props && e.props.onVnodeMounted;
                t && ns(t, s.parent, e)
            }
            ), a)
        }
        ,
        r.deactivate = e => {
            const t = e.component;
            c(e, d, null, 1, a),
            mo(( () => {
                t.da && J(t.da);
                const n = e.props && e.props.onVnodeUnmounted;
                n && ns(n, t.parent, e),
                t.isDeactivated = !0
            }
            ), a)
        }
        ,
        Mn(( () => [e.include, e.exclude]), ( ([e,t]) => {
            e && h((t => tr(e, t))),
            t && h((e => !tr(t, e)))
        }
        ), {
            flush: "post",
            deep: !0
        });
        let g = null;
        const v = () => {
            null != g && o.set(g, ar(n.subTree))
        }
        ;
        return fr(v),
        pr(v),
        hr(( () => {
            o.forEach((e => {
                const {subTree: t, suspense: r} = n
                  , o = ar(t);
                if (e.type !== o.type)
                    p(e);
                else {
                    ir(o);
                    const e = o.component.da;
                    e && mo(e, r)
                }
            }
            ))
        }
        )),
        () => {
            if (g = null,
            !t.default)
                return null;
            const n = t.default()
              , r = n[0];
            if (n.length > 1)
                return i = null,
                n;
            if (!(Uo(r) && (4 & r.shapeFlag || 128 & r.shapeFlag)))
                return i = null,
                r;
            let a = ar(r);
            const l = a.type
              , c = _s(Gn(a) ? a.type.__asyncResolved || {} : l)
              , {include: u, exclude: f, max: d} = e;
            if (u && (!c || !tr(u, c)) || f && c && tr(f, c))
                return i = a,
                r;
            const p = null == a.key ? l : a.key
              , h = o.get(p);
            return a.el && (a = Jo(a),
            128 & r.shapeFlag && (r.ssContent = a)),
            g = p,
            h ? (a.el = h.el,
            a.component = h.component,
            a.transition && qn(a, a.transition),
            a.shapeFlag |= 512,
            s.delete(p),
            s.add(p)) : (s.add(p),
            d && s.size > parseInt(d, 10) && m(s.values().next().value)),
            a.shapeFlag |= 256,
            i = a,
            Cn(r.type) ? r : a
        }
    }
}
  , er = Qn;
function tr(e, t) {
    return O(e) ? e.some((e => tr(e, t))) : P(e) ? e.split(",").includes(t) : !!e.test && e.test(t)
}
function nr(e, t) {
    or(e, "a", t)
}
function rr(e, t) {
    or(e, "da", t)
}
function or(e, t, n=is) {
    const r = e.__wdc || (e.__wdc = () => {
        let t = n;
        for (; t; ) {
            if (t.isDeactivated)
                return;
            t = t.parent
        }
        return e()
    }
    );
    if (lr(t, r, n),
    n) {
        let e = n.parent;
        for (; e && e.parent; )
            Xn(e.parent.vnode) && sr(r, t, n, e),
            e = e.parent
    }
}
function sr(e, t, n, r) {
    const o = lr(t, e, r, !0);
    mr(( () => {
        C(r[t], o)
    }
    ), n)
}
function ir(e) {
    e.shapeFlag &= -257,
    e.shapeFlag &= -513
}
function ar(e) {
    return 128 & e.shapeFlag ? e.ssContent : e
}
function lr(e, t, n=is, r=!1) {
    if (n) {
        const o = n[e] || (n[e] = [])
          , s = t.__weh || (t.__weh = (...r) => {
            if (n.isUnmounted)
                return;
            ve(),
            ls(n);
            const o = Ut(t, n, e, r);
            return cs(),
            ye(),
            o
        }
        );
        return r ? o.unshift(s) : o.push(s),
        s
    }
}
const cr = e => (t, n=is) => (!ps || "sp" === e) && lr(e, ( (...e) => t(...e)), n)
  , ur = cr("bm")
  , fr = cr("m")
  , dr = cr("bu")
  , pr = cr("u")
  , hr = cr("bum")
  , mr = cr("um")
  , gr = cr("sp")
  , vr = cr("rtg")
  , yr = cr("rtc");
function _r(e, t=is) {
    lr("ec", e, t)
}
function br(e, t) {
    const n = dn;
    if (null === n)
        return e;
    const r = ys(n) || n.proxy
      , o = e.dirs || (e.dirs = []);
    for (let s = 0; s < t.length; s++) {
        let[e,n,i,a=m] = t[s];
        e && (k(e) && (e = {
            mounted: e,
            updated: e
        }),
        e.deep && Bn(n),
        o.push({
            dir: e,
            instance: r,
            value: n,
            oldValue: void 0,
            arg: i,
            modifiers: a
        }))
    }
    return e
}
function wr(e, t, n, r) {
    const o = e.dirs
      , s = t && t.dirs;
    for (let i = 0; i < o.length; i++) {
        const a = o[i];
        s && (a.oldValue = s[i].value);
        let l = a.dir[r];
        l && (ve(),
        Ut(l, n, 8, [e.el, a, e, t]),
        ye())
    }
}
function Sr(e, t) {
    return Or("components", e, !0, t) || e
}
const Cr = Symbol();
function Er(e) {
    return P(e) ? Or("components", e, !1) || e : e || Cr
}
function xr(e) {
    return Or("directives", e)
}
function Or(e, t, n=!0, r=!1) {
    const o = dn || is;
    if (o) {
        const n = o.type;
        if ("components" === e) {
            const e = _s(n, !1);
            if (e && (e === t || e === V(t) || e === W(V(t))))
                return n
        }
        const s = Ar(o[e] || n[e], t) || Ar(o.appContext[e], t);
        return !s && r ? n : s
    }
}
function Ar(e, t) {
    return e && (e[t] || e[V(t)] || e[W(V(t))])
}
function Rr(e, t, n, r) {
    let o;
    const s = n && n[r];
    if (O(e) || P(e)) {
        o = new Array(e.length);
        for (let n = 0, r = e.length; n < r; n++)
            o[n] = t(e[n], n, void 0, s && s[n])
    } else if ("number" == typeof e) {
        o = new Array(e);
        for (let n = 0; n < e; n++)
            o[n] = t(n + 1, n, void 0, s && s[n])
    } else if (j(e))
        if (e[Symbol.iterator])
            o = Array.from(e, ( (e, n) => t(e, n, void 0, s && s[n])));
        else {
            const n = Object.keys(e);
            o = new Array(n.length);
            for (let r = 0, i = n.length; r < i; r++) {
                const i = n[r];
                o[r] = t(e[i], i, r, s && s[r])
            }
        }
    else
        o = [];
    return n && (n[r] = o),
    o
}
function Tr(e, t) {
    for (let n = 0; n < t.length; n++) {
        const r = t[n];
        if (O(r))
            for (let t = 0; t < r.length; t++)
                e[r[t].name] = r[t].fn;
        else
            r && (e[r.name] = r.key ? (...e) => {
                const t = r.fn(...e);
                return t && (t.key = r.key),
                t
            }
            : r.fn)
    }
    return e
}
function kr(e, t, n={}, r, o) {
    if (dn.isCE || dn.parent && Gn(dn.parent) && dn.parent.isCE)
        return "default" !== t && (n.name = t),
        Ko("slot", n, r && r());
    let s = e[t];
    s && s._c && (s._d = !1),
    jo();
    const i = s && Pr(s(n))
      , a = Bo(Ao, {
        key: n.key || i && i.key || `_${t}`
    }, i || (r ? r() : []), i && 1 === e._ ? 64 : -2);
    return !o && a.scopeId && (a.slotScopeIds = [a.scopeId + "-s"]),
    s && s._c && (s._d = !0),
    a
}
function Pr(e) {
    return e.some((e => !Uo(e) || e.type !== To && !(e.type === Ao && !Pr(e.children)))) ? e : null
}
const Fr = e => e ? us(e) ? ys(e) || e.proxy : Fr(e.parent) : null
  , jr = S(Object.create(null), {
    $: e => e,
    $el: e => e.vnode.el,
    $data: e => e.data,
    $props: e => e.props,
    $attrs: e => e.attrs,
    $slots: e => e.slots,
    $refs: e => e.refs,
    $parent: e => Fr(e.parent),
    $root: e => Fr(e.root),
    $emit: e => e.emit,
    $options: e => Ir(e),
    $forceUpdate: e => e.f || (e.f = () => Xt(e.update)),
    $nextTick: e => e.n || (e.n = Zt.bind(e.proxy)),
    $watch: e => Dn.bind(e)
})
  , Nr = (e, t) => e !== m && !e.__isScriptSetup && x(e, t)
  , Mr = {
    get({_: e}, t) {
        const {ctx: n, setupState: r, data: o, props: s, accessCache: i, type: a, appContext: l} = e;
        let c;
        if ("$" !== t[0]) {
            const a = i[t];
            if (void 0 !== a)
                switch (a) {
                case 1:
                    return r[t];
                case 2:
                    return o[t];
                case 4:
                    return n[t];
                case 3:
                    return s[t]
                }
            else {
                if (Nr(r, t))
                    return i[t] = 1,
                    r[t];
                if (o !== m && x(o, t))
                    return i[t] = 2,
                    o[t];
                if ((c = e.propsOptions[0]) && x(c, t))
                    return i[t] = 3,
                    s[t];
                if (n !== m && x(n, t))
                    return i[t] = 4,
                    n[t];
                Dr && (i[t] = 0)
            }
        }
        const u = jr[t];
        let f, d;
        return u ? ("$attrs" === t && _e(e, 0, t),
        u(e)) : (f = a.__cssModules) && (f = f[t]) ? f : n !== m && x(n, t) ? (i[t] = 4,
        n[t]) : (d = l.config.globalProperties,
        x(d, t) ? d[t] : void 0)
    },
    set({_: e}, t, n) {
        const {data: r, setupState: o, ctx: s} = e;
        return Nr(o, t) ? (o[t] = n,
        !0) : r !== m && x(r, t) ? (r[t] = n,
        !0) : !x(e.props, t) && (("$" !== t[0] || !(t.slice(1)in e)) && (s[t] = n,
        !0))
    },
    has({_: {data: e, setupState: t, accessCache: n, ctx: r, appContext: o, propsOptions: s}}, i) {
        let a;
        return !!n[i] || e !== m && x(e, i) || Nr(t, i) || (a = s[0]) && x(a, i) || x(r, i) || x(jr, i) || x(o.config.globalProperties, i)
    },
    defineProperty(e, t, n) {
        return null != n.get ? e._.accessCache[t] = 0 : x(n, "value") && this.set(e, t, n.value, null),
        Reflect.defineProperty(e, t, n)
    }
}
  , $r = S({}, Mr, {
    get(e, t) {
        if (t !== Symbol.unscopables)
            return Mr.get(e, t, e)
    },
    has: (e, n) => "_" !== n[0] && !t(n)
});
let Dr = !0;
function Lr(e) {
    const t = Ir(e)
      , n = e.proxy
      , r = e.ctx;
    Dr = !1,
    t.beforeCreate && Br(t.beforeCreate, e, "bc");
    const {data: o, computed: s, methods: i, watch: a, provide: l, inject: c, created: u, beforeMount: f, mounted: d, beforeUpdate: p, updated: h, activated: m, deactivated: g, beforeDestroy: y, beforeUnmount: _, destroyed: b, unmounted: w, render: S, renderTracked: C, renderTriggered: E, errorCaptured: x, serverPrefetch: A, expose: R, inheritAttrs: T, components: P, directives: F, filters: N} = t;
    if (c && function(e, t, n=v, r=!1) {
        O(e) && (e = Wr(e));
        for (const o in e) {
            const n = e[o];
            let s;
            s = j(n) ? "default"in n ? Pn(n.from || o, n.default, !0) : Pn(n.from || o) : Pn(n),
            xt(s) && r ? Object.defineProperty(t, o, {
                enumerable: !0,
                configurable: !0,
                get: () => s.value,
                set: e => s.value = e
            }) : t[o] = s
        }
    }(c, r, null, e.appContext.config.unwrapInjectedRef),
    i)
        for (const v in i) {
            const e = i[v];
            k(e) && (r[v] = e.bind(n))
        }
    if (o) {
        const t = o.call(n, n);
        j(t) && (e.data = ft(t))
    }
    if (Dr = !0,
    s)
        for (const O in s) {
            const e = s[O]
              , t = k(e) ? e.bind(n, n) : k(e.get) ? e.get.bind(n, n) : v
              , o = !k(e) && k(e.set) ? e.set.bind(n) : v
              , i = bs({
                get: t,
                set: o
            });
            Object.defineProperty(r, O, {
                enumerable: !0,
                configurable: !0,
                get: () => i.value,
                set: e => i.value = e
            })
        }
    if (a)
        for (const v in a)
            Ur(a[v], r, n, v);
    if (l) {
        const e = k(l) ? l.call(n) : l;
        Reflect.ownKeys(e).forEach((t => {
            kn(t, e[t])
        }
        ))
    }
    function M(e, t) {
        O(t) ? t.forEach((t => e(t.bind(n)))) : t && e(t.bind(n))
    }
    if (u && Br(u, e, "c"),
    M(ur, f),
    M(fr, d),
    M(dr, p),
    M(pr, h),
    M(nr, m),
    M(rr, g),
    M(_r, x),
    M(yr, C),
    M(vr, E),
    M(hr, _),
    M(mr, w),
    M(gr, A),
    O(R))
        if (R.length) {
            const t = e.exposed || (e.exposed = {});
            R.forEach((e => {
                Object.defineProperty(t, e, {
                    get: () => n[e],
                    set: t => n[e] = t
                })
            }
            ))
        } else
            e.exposed || (e.exposed = {});
    S && e.render === v && (e.render = S),
    null != T && (e.inheritAttrs = T),
    P && (e.components = P),
    F && (e.directives = F)
}
function Br(e, t, n) {
    Ut(O(e) ? e.map((e => e.bind(t.proxy))) : e.bind(t.proxy), t, n)
}
function Ur(e, t, n, r) {
    const o = r.includes(".") ? Ln(n, r) : () => n[r];
    if (P(e)) {
        const n = t[e];
        k(n) && Mn(o, n)
    } else if (k(e))
        Mn(o, e.bind(n));
    else if (j(e))
        if (O(e))
            e.forEach((e => Ur(e, t, n, r)));
        else {
            const r = k(e.handler) ? e.handler.bind(n) : t[e.handler];
            k(r) && Mn(o, r, e)
        }
}
function Ir(e) {
    const t = e.type
      , {mixins: n, extends: r} = t
      , {mixins: o, optionsCache: s, config: {optionMergeStrategies: i}} = e.appContext
      , a = s.get(t);
    let l;
    return a ? l = a : o.length || n || r ? (l = {},
    o.length && o.forEach((e => Vr(l, e, i, !0))),
    Vr(l, t, i)) : l = t,
    j(t) && s.set(t, l),
    l
}
function Vr(e, t, n, r=!1) {
    const {mixins: o, extends: s} = t;
    s && Vr(e, s, n, !0),
    o && o.forEach((t => Vr(e, t, n, !0)));
    for (const i in t)
        if (r && "expose" === i)
            ;
        else {
            const r = Hr[i] || n && n[i];
            e[i] = r ? r(e[i], t[i]) : t[i]
        }
    return e
}
const Hr = {
    data: zr,
    props: qr,
    emits: qr,
    methods: qr,
    computed: qr,
    beforeCreate: Kr,
    created: Kr,
    beforeMount: Kr,
    mounted: Kr,
    beforeUpdate: Kr,
    updated: Kr,
    beforeDestroy: Kr,
    beforeUnmount: Kr,
    destroyed: Kr,
    unmounted: Kr,
    activated: Kr,
    deactivated: Kr,
    errorCaptured: Kr,
    serverPrefetch: Kr,
    components: qr,
    directives: qr,
    watch: function(e, t) {
        if (!e)
            return t;
        if (!t)
            return e;
        const n = S(Object.create(null), e);
        for (const r in t)
            n[r] = Kr(e[r], t[r]);
        return n
    },
    provide: zr,
    inject: function(e, t) {
        return qr(Wr(e), Wr(t))
    }
};
function zr(e, t) {
    return t ? e ? function() {
        return S(k(e) ? e.call(this, this) : e, k(t) ? t.call(this, this) : t)
    }
    : t : e
}
function Wr(e) {
    if (O(e)) {
        const t = {};
        for (let n = 0; n < e.length; n++)
            t[e[n]] = e[n];
        return t
    }
    return e
}
function Kr(e, t) {
    return e ? [...new Set([].concat(e, t))] : t
}
function qr(e, t) {
    return e ? S(S(Object.create(null), e), t) : t
}
function Jr(e, t, n, r) {
    const [o,s] = e.propsOptions;
    let i, a = !1;
    if (t)
        for (let l in t) {
            if (B(l))
                continue;
            const c = t[l];
            let u;
            o && x(o, u = V(l)) ? s && s.includes(u) ? (i || (i = {}))[u] = c : n[u] = c : fn(e.emitsOptions, l) || l in r && c === r[l] || (r[l] = c,
            a = !0)
        }
    if (s) {
        const t = _t(n)
          , r = i || m;
        for (let i = 0; i < s.length; i++) {
            const a = s[i];
            n[a] = Yr(o, t, a, r[a], e, !x(r, a))
        }
    }
    return a
}
function Yr(e, t, n, r, o, s) {
    const i = e[n];
    if (null != i) {
        const e = x(i, "default");
        if (e && void 0 === r) {
            const e = i.default;
            if (i.type !== Function && k(e)) {
                const {propsDefaults: s} = o;
                n in s ? r = s[n] : (ls(o),
                r = s[n] = e.call(null, t),
                cs())
            } else
                r = e
        }
        i[0] && (s && !e ? r = !1 : !i[1] || "" !== r && r !== z(n) || (r = !0))
    }
    return r
}
function Gr(e, t, n=!1) {
    const r = t.propsCache
      , o = r.get(e);
    if (o)
        return o;
    const s = e.props
      , i = {}
      , a = [];
    let l = !1;
    if (!k(e)) {
        const r = e => {
            l = !0;
            const [n,r] = Gr(e, t, !0);
            S(i, n),
            r && a.push(...r)
        }
        ;
        !n && t.mixins.length && t.mixins.forEach(r),
        e.extends && r(e.extends),
        e.mixins && e.mixins.forEach(r)
    }
    if (!s && !l)
        return j(e) && r.set(e, g),
        g;
    if (O(s))
        for (let u = 0; u < s.length; u++) {
            const e = V(s[u]);
            Zr(e) && (i[e] = m)
        }
    else if (s)
        for (const u in s) {
            const e = V(u);
            if (Zr(e)) {
                const t = s[u]
                  , n = i[e] = O(t) || k(t) ? {
                    type: t
                } : Object.assign({}, t);
                if (n) {
                    const t = eo(Boolean, n.type)
                      , r = eo(String, n.type);
                    n[0] = t > -1,
                    n[1] = r < 0 || t < r,
                    (t > -1 || x(n, "default")) && a.push(e)
                }
            }
        }
    const c = [i, a];
    return j(e) && r.set(e, c),
    c
}
function Zr(e) {
    return "$" !== e[0]
}
function Xr(e) {
    const t = e && e.toString().match(/^\s*function (\w+)/);
    return t ? t[1] : null === e ? "null" : ""
}
function Qr(e, t) {
    return Xr(e) === Xr(t)
}
function eo(e, t) {
    return O(t) ? t.findIndex((t => Qr(t, e))) : k(t) && Qr(t, e) ? 0 : -1
}
const to = e => "_" === e[0] || "$stable" === e
  , no = e => O(e) ? e.map(Xo) : [Xo(e)]
  , ro = (e, t, n) => {
    if (t._n)
        return t;
    const r = vn(( (...e) => no(t(...e))), n);
    return r._c = !1,
    r
}
  , oo = (e, t, n) => {
    const r = e._ctx;
    for (const o in e) {
        if (to(o))
            continue;
        const n = e[o];
        if (k(n))
            t[o] = ro(0, n, r);
        else if (null != n) {
            const e = no(n);
            t[o] = () => e
        }
    }
}
  , so = (e, t) => {
    const n = no(t);
    e.slots.default = () => n
}
;
function io() {
    return {
        app: null,
        config: {
            isNativeTag: y,
            performance: !1,
            globalProperties: {},
            optionMergeStrategies: {},
            errorHandler: void 0,
            warnHandler: void 0,
            compilerOptions: {}
        },
        mixins: [],
        components: {},
        directives: {},
        provides: Object.create(null),
        optionsCache: new WeakMap,
        propsCache: new WeakMap,
        emitsCache: new WeakMap
    }
}
let ao = 0;
function lo(e, t) {
    return function(n, r=null) {
        k(n) || (n = Object.assign({}, n)),
        null == r || j(r) || (r = null);
        const o = io()
          , s = new Set;
        let i = !1;
        const a = o.app = {
            _uid: ao++,
            _component: n,
            _props: r,
            _container: null,
            _context: o,
            _instance: null,
            version: Os,
            get config() {
                return o.config
            },
            set config(e) {},
            use: (e, ...t) => (s.has(e) || (e && k(e.install) ? (s.add(e),
            e.install(a, ...t)) : k(e) && (s.add(e),
            e(a, ...t))),
            a),
            mixin: e => (o.mixins.includes(e) || o.mixins.push(e),
            a),
            component: (e, t) => t ? (o.components[e] = t,
            a) : o.components[e],
            directive: (e, t) => t ? (o.directives[e] = t,
            a) : o.directives[e],
            mount(s, l, c) {
                if (!i) {
                    const u = Ko(n, r);
                    return u.appContext = o,
                    l && t ? t(u, s) : e(u, s, c),
                    i = !0,
                    a._container = s,
                    s.__vue_app__ = a,
                    ys(u.component) || u.component.proxy
                }
            },
            unmount() {
                i && (e(null, a._container),
                delete a._container.__vue_app__)
            },
            provide: (e, t) => (o.provides[e] = t,
            a)
        };
        return a
    }
}
function co(e, t, n, r, o=!1) {
    if (O(e))
        return void e.forEach(( (e, s) => co(e, t && (O(t) ? t[s] : t), n, r, o)));
    if (Gn(r) && !o)
        return;
    const s = 4 & r.shapeFlag ? ys(r.component) || r.component.proxy : r.el
      , i = o ? null : s
      , {i: a, r: l} = e
      , c = t && t.r
      , u = a.refs === m ? a.refs = {} : a.refs
      , f = a.setupState;
    if (null != c && c !== l && (P(c) ? (u[c] = null,
    x(f, c) && (f[c] = null)) : xt(c) && (c.value = null)),
    k(l))
        Bt(l, a, 12, [i, u]);
    else {
        const t = P(l)
          , r = xt(l);
        if (t || r) {
            const a = () => {
                if (e.f) {
                    const n = t ? x(f, l) ? f[l] : u[l] : l.value;
                    o ? O(n) && C(n, s) : O(n) ? n.includes(s) || n.push(s) : t ? (u[l] = [s],
                    x(f, l) && (f[l] = u[l])) : (l.value = [s],
                    e.k && (u[e.k] = l.value))
                } else
                    t ? (u[l] = i,
                    x(f, l) && (f[l] = i)) : r && (l.value = i,
                    e.k && (u[e.k] = i))
            }
            ;
            i ? (a.id = -1,
            mo(a, n)) : a()
        }
    }
}
let uo = !1;
const fo = e => /svg/.test(e.namespaceURI) && "foreignObject" !== e.tagName
  , po = e => 8 === e.nodeType;
function ho(e) {
    const {mt: t, p: n, o: {patchProp: r, createText: o, nextSibling: s, parentNode: i, remove: a, insert: l, createComment: c}} = e
      , u = (n, r, a, c, g, v=!1) => {
        const y = po(n) && "[" === n.data
          , _ = () => h(n, r, a, c, g, y)
          , {type: b, ref: w, shapeFlag: S, patchFlag: C} = r;
        let E = n.nodeType;
        r.el = n,
        -2 === C && (v = !1,
        r.dynamicChildren = null);
        let x = null;
        switch (b) {
        case Ro:
            3 !== E ? "" === r.children ? (l(r.el = o(""), i(n), n),
            x = n) : x = _() : (n.data !== r.children && (uo = !0,
            n.data = r.children),
            x = s(n));
            break;
        case To:
            x = 8 !== E || y ? _() : s(n);
            break;
        case ko:
            if (y && (E = (n = s(n)).nodeType),
            1 === E || 3 === E) {
                x = n;
                const e = !r.children.length;
                for (let t = 0; t < r.staticCount; t++)
                    e && (r.children += 1 === x.nodeType ? x.outerHTML : x.data),
                    t === r.staticCount - 1 && (r.anchor = x),
                    x = s(x);
                return y ? s(x) : x
            }
            _();
            break;
        case Ao:
            x = y ? p(n, r, a, c, g, v) : _();
            break;
        default:
            if (1 & S)
                x = 1 !== E || r.type.toLowerCase() !== n.tagName.toLowerCase() ? _() : f(n, r, a, c, g, v);
            else if (6 & S) {
                r.slotScopeIds = g;
                const e = i(n);
                if (t(r, e, null, a, c, fo(e), v),
                x = y ? m(n) : s(n),
                x && po(x) && "teleport end" === x.data && (x = s(x)),
                Gn(r)) {
                    let t;
                    y ? (t = Ko(Ao),
                    t.anchor = x ? x.previousSibling : e.lastChild) : t = 3 === n.nodeType ? Yo("") : Ko("div"),
                    t.el = n,
                    r.component.subTree = t
                }
            } else
                64 & S ? x = 8 !== E ? _() : r.type.hydrate(n, r, a, c, g, v, e, d) : 128 & S && (x = r.type.hydrate(n, r, a, c, fo(i(n)), g, v, e, u))
        }
        return null != w && co(w, null, c, r),
        x
    }
      , f = (e, t, n, o, s, i) => {
        i = i || !!t.dynamicChildren;
        const {type: l, props: c, patchFlag: u, shapeFlag: f, dirs: p} = t
          , h = "input" === l && p || "option" === l;
        if (h || -1 !== u) {
            if (p && wr(t, null, n, "created"),
            c)
                if (h || !i || 48 & u)
                    for (const t in c)
                        (h && t.endsWith("value") || b(t) && !B(t)) && r(e, t, null, c[t], !1, void 0, n);
                else
                    c.onClick && r(e, "onClick", null, c.onClick, !1, void 0, n);
            let l;
            if ((l = c && c.onVnodeBeforeMount) && ns(l, n, t),
            p && wr(t, null, n, "beforeMount"),
            ((l = c && c.onVnodeMounted) || p) && Rn(( () => {
                l && ns(l, n, t),
                p && wr(t, null, n, "mounted")
            }
            ), o),
            16 & f && (!c || !c.innerHTML && !c.textContent)) {
                let r = d(e.firstChild, t, e, n, o, s, i);
                for (; r; ) {
                    uo = !0;
                    const e = r;
                    r = r.nextSibling,
                    a(e)
                }
            } else
                8 & f && e.textContent !== t.children && (uo = !0,
                e.textContent = t.children)
        }
        return e.nextSibling
    }
      , d = (e, t, r, o, s, i, a) => {
        a = a || !!t.dynamicChildren;
        const l = t.children
          , c = l.length;
        for (let f = 0; f < c; f++) {
            const t = a ? l[f] : l[f] = Xo(l[f]);
            if (e)
                e = u(e, t, o, s, i, a);
            else {
                if (t.type === Ro && !t.children)
                    continue;
                uo = !0,
                n(null, t, r, null, o, s, fo(r), i)
            }
        }
        return e
    }
      , p = (e, t, n, r, o, a) => {
        const {slotScopeIds: u} = t;
        u && (o = o ? o.concat(u) : u);
        const f = i(e)
          , p = d(s(e), t, f, n, r, o, a);
        return p && po(p) && "]" === p.data ? s(t.anchor = p) : (uo = !0,
        l(t.anchor = c("]"), f, p),
        p)
    }
      , h = (e, t, r, o, l, c) => {
        if (uo = !0,
        t.el = null,
        c) {
            const t = m(e);
            for (; ; ) {
                const n = s(e);
                if (!n || n === t)
                    break;
                a(n)
            }
        }
        const u = s(e)
          , f = i(e);
        return a(e),
        n(null, t, f, u, r, o, fo(f), l),
        u
    }
      , m = e => {
        let t = 0;
        for (; e; )
            if ((e = s(e)) && po(e) && ("[" === e.data && t++,
            "]" === e.data)) {
                if (0 === t)
                    return s(e);
                t--
            }
        return e
    }
    ;
    return [ (e, t) => {
        if (!t.hasChildNodes())
            return n(null, e, t),
            nn(),
            void (t._vnode = e);
        uo = !1,
        u(t.firstChild, e, null, null, null),
        nn(),
        t._vnode = e
    }
    , u]
}
const mo = Rn;
function go(e) {
    return yo(e)
}
function vo(e) {
    return yo(e, ho)
}
function yo(e, t) {
    (Z || (Z = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof self ? self : "undefined" != typeof window ? window : "undefined" != typeof global ? global : {})).__VUE__ = !0;
    const {insert: n, remove: r, patchProp: o, createElement: s, createText: i, createComment: a, setText: l, setElementText: c, parentNode: u, nextSibling: f, setScopeId: d=v, insertStaticContent: p} = e
      , h = (e, t, n, r=null, o=null, s=null, i=!1, a=null, l=!!t.dynamicChildren) => {
        if (e === t)
            return;
        e && !Io(e, t) && (r = Y(e),
        I(e, o, s, !0),
        e = null),
        -2 === t.patchFlag && (l = !1,
        t.dynamicChildren = null);
        const {type: c, ref: u, shapeFlag: f} = t;
        switch (c) {
        case Ro:
            y(e, t, n, r);
            break;
        case To:
            _(e, t, n, r);
            break;
        case ko:
            null == e && b(t, n, r, i);
            break;
        case Ao:
            k(e, t, n, r, o, s, i, a, l);
            break;
        default:
            1 & f ? w(e, t, n, r, o, s, i, a, l) : 6 & f ? P(e, t, n, r, o, s, i, a, l) : (64 & f || 128 & f) && c.process(e, t, n, r, o, s, i, a, l, X)
        }
        null != u && o && co(u, e && e.ref, s, t || e, !t)
    }
      , y = (e, t, r, o) => {
        if (null == e)
            n(t.el = i(t.children), r, o);
        else {
            const n = t.el = e.el;
            t.children !== e.children && l(n, t.children)
        }
    }
      , _ = (e, t, r, o) => {
        null == e ? n(t.el = a(t.children || ""), r, o) : t.el = e.el
    }
      , b = (e, t, n, r) => {
        [e.el,e.anchor] = p(e.children, t, n, r, e.el, e.anchor)
    }
      , w = (e, t, n, r, o, s, i, a, l) => {
        i = i || "svg" === t.type,
        null == e ? C(t, n, r, o, s, i, a, l) : A(e, t, o, s, i, a, l)
    }
      , C = (e, t, r, i, a, l, u, f) => {
        let d, p;
        const {type: h, props: m, shapeFlag: g, transition: v, dirs: y} = e;
        if (d = e.el = s(e.type, l, m && m.is, m),
        8 & g ? c(d, e.children) : 16 & g && O(e.children, d, null, i, a, l && "foreignObject" !== h, u, f),
        y && wr(e, null, i, "created"),
        m) {
            for (const t in m)
                "value" === t || B(t) || o(d, t, null, m[t], l, e.children, i, a, q);
            "value"in m && o(d, "value", null, m.value),
            (p = m.onVnodeBeforeMount) && ns(p, i, e)
        }
        E(d, e, e.scopeId, u, i),
        y && wr(e, null, i, "beforeMount");
        const _ = (!a || a && !a.pendingBranch) && v && !v.persisted;
        _ && v.beforeEnter(d),
        n(d, t, r),
        ((p = m && m.onVnodeMounted) || _ || y) && mo(( () => {
            p && ns(p, i, e),
            _ && v.enter(d),
            y && wr(e, null, i, "mounted")
        }
        ), a)
    }
      , E = (e, t, n, r, o) => {
        if (n && d(e, n),
        r)
            for (let s = 0; s < r.length; s++)
                d(e, r[s]);
        if (o) {
            if (t === o.subTree) {
                const t = o.vnode;
                E(e, t, t.scopeId, t.slotScopeIds, o.parent)
            }
        }
    }
      , O = (e, t, n, r, o, s, i, a, l=0) => {
        for (let c = l; c < e.length; c++) {
            const l = e[c] = a ? Qo(e[c]) : Xo(e[c]);
            h(null, l, t, n, r, o, s, i, a)
        }
    }
      , A = (e, t, n, r, s, i, a) => {
        const l = t.el = e.el;
        let {patchFlag: u, dynamicChildren: f, dirs: d} = t;
        u |= 16 & e.patchFlag;
        const p = e.props || m
          , h = t.props || m;
        let g;
        n && _o(n, !1),
        (g = h.onVnodeBeforeUpdate) && ns(g, n, t, e),
        d && wr(t, e, n, "beforeUpdate"),
        n && _o(n, !0);
        const v = s && "foreignObject" !== t.type;
        if (f ? R(e.dynamicChildren, f, l, n, r, v, i) : a || $(e, t, l, null, n, r, v, i, !1),
        u > 0) {
            if (16 & u)
                T(l, t, p, h, n, r, s);
            else if (2 & u && p.class !== h.class && o(l, "class", null, h.class, s),
            4 & u && o(l, "style", p.style, h.style, s),
            8 & u) {
                const i = t.dynamicProps;
                for (let t = 0; t < i.length; t++) {
                    const a = i[t]
                      , c = p[a]
                      , u = h[a];
                    u === c && "value" !== a || o(l, a, c, u, s, e.children, n, r, q)
                }
            }
            1 & u && e.children !== t.children && c(l, t.children)
        } else
            a || null != f || T(l, t, p, h, n, r, s);
        ((g = h.onVnodeUpdated) || d) && mo(( () => {
            g && ns(g, n, t, e),
            d && wr(t, e, n, "updated")
        }
        ), r)
    }
      , R = (e, t, n, r, o, s, i) => {
        for (let a = 0; a < t.length; a++) {
            const l = e[a]
              , c = t[a]
              , f = l.el && (l.type === Ao || !Io(l, c) || 70 & l.shapeFlag) ? u(l.el) : n;
            h(l, c, f, null, r, o, s, i, !0)
        }
    }
      , T = (e, t, n, r, s, i, a) => {
        if (n !== r) {
            if (n !== m)
                for (const l in n)
                    B(l) || l in r || o(e, l, n[l], null, a, t.children, s, i, q);
            for (const l in r) {
                if (B(l))
                    continue;
                const c = r[l]
                  , u = n[l];
                c !== u && "value" !== l && o(e, l, u, c, a, t.children, s, i, q)
            }
            "value"in r && o(e, "value", n.value, r.value)
        }
    }
      , k = (e, t, r, o, s, a, l, c, u) => {
        const f = t.el = e ? e.el : i("")
          , d = t.anchor = e ? e.anchor : i("");
        let {patchFlag: p, dynamicChildren: h, slotScopeIds: m} = t;
        m && (c = c ? c.concat(m) : m),
        null == e ? (n(f, r, o),
        n(d, r, o),
        O(t.children, r, d, s, a, l, c, u)) : p > 0 && 64 & p && h && e.dynamicChildren ? (R(e.dynamicChildren, h, r, s, a, l, c),
        (null != t.key || s && t === s.subTree) && bo(e, t, !0)) : $(e, t, r, d, s, a, l, c, u)
    }
      , P = (e, t, n, r, o, s, i, a, l) => {
        t.slotScopeIds = a,
        null == e ? 512 & t.shapeFlag ? o.ctx.activate(t, n, r, i, l) : F(t, n, r, o, s, i, l) : j(e, t, l)
    }
      , F = (e, t, n, r, o, s, i) => {
        const a = e.component = ss(e, r, o);
        if (Xn(e) && (a.ctx.renderer = X),
        hs(a),
        a.asyncDep) {
            if (o && o.registerDep(a, N),
            !e.el) {
                const e = a.subTree = Ko(To);
                _(null, e, t, n)
            }
        } else
            N(a, e, t, n, o, s, i)
    }
      , j = (e, t, n) => {
        const r = t.component = e.component;
        if (function(e, t, n) {
            const {props: r, children: o, component: s} = e
              , {props: i, children: a, patchFlag: l} = t
              , c = s.emitsOptions;
            if (t.dirs || t.transition)
                return !0;
            if (!(n && l >= 0))
                return !(!o && !a || a && a.$stable) || r !== i && (r ? !i || wn(r, i, c) : !!i);
            if (1024 & l)
                return !0;
            if (16 & l)
                return r ? wn(r, i, c) : !!i;
            if (8 & l) {
                const e = t.dynamicProps;
                for (let t = 0; t < e.length; t++) {
                    const n = e[t];
                    if (i[n] !== r[n] && !fn(c, n))
                        return !0
                }
            }
            return !1
        }(e, t, n)) {
            if (r.asyncDep && !r.asyncResolved)
                return void M(r, t, n);
            r.next = t,
            function(e) {
                const t = zt.indexOf(e);
                t > Wt && zt.splice(t, 1)
            }(r.update),
            r.update()
        } else
            t.el = e.el,
            r.vnode = t
    }
      , N = (e, t, n, r, o, s, i) => {
        const a = e.effect = new pe(( () => {
            if (e.isMounted) {
                let t, {next: n, bu: r, u: a, parent: l, vnode: c} = e, f = n;
                _o(e, !1),
                n ? (n.el = c.el,
                M(e, n, i)) : n = c,
                r && J(r),
                (t = n.props && n.props.onVnodeBeforeUpdate) && ns(t, l, n, c),
                _o(e, !0);
                const d = yn(e)
                  , p = e.subTree;
                e.subTree = d,
                h(p, d, u(p.el), Y(p), e, o, s),
                n.el = d.el,
                null === f && Sn(e, d.el),
                a && mo(a, o),
                (t = n.props && n.props.onVnodeUpdated) && mo(( () => ns(t, l, n, c)), o)
            } else {
                let i;
                const {el: a, props: l} = t
                  , {bm: c, m: u, parent: f} = e
                  , d = Gn(t);
                if (_o(e, !1),
                c && J(c),
                !d && (i = l && l.onVnodeBeforeMount) && ns(i, f, t),
                _o(e, !0),
                a && ee) {
                    const n = () => {
                        e.subTree = yn(e),
                        ee(a, e.subTree, e, o, null)
                    }
                    ;
                    d ? t.type.__asyncLoader().then(( () => !e.isUnmounted && n())) : n()
                } else {
                    const i = e.subTree = yn(e);
                    h(null, i, n, r, e, o, s),
                    t.el = i.el
                }
                if (u && mo(u, o),
                !d && (i = l && l.onVnodeMounted)) {
                    const e = t;
                    mo(( () => ns(i, f, e)), o)
                }
                (256 & t.shapeFlag || f && Gn(f.vnode) && 256 & f.vnode.shapeFlag) && e.a && mo(e.a, o),
                e.isMounted = !0,
                t = n = r = null
            }
        }
        ),( () => Xt(l)),e.scope)
          , l = e.update = () => a.run();
        l.id = e.uid,
        _o(e, !0),
        l()
    }
      , M = (e, t, n) => {
        t.component = e;
        const r = e.vnode.props;
        e.vnode = t,
        e.next = null,
        function(e, t, n, r) {
            const {props: o, attrs: s, vnode: {patchFlag: i}} = e
              , a = _t(o)
              , [l] = e.propsOptions;
            let c = !1;
            if (!(r || i > 0) || 16 & i) {
                let r;
                Jr(e, t, o, s) && (c = !0);
                for (const s in a)
                    t && (x(t, s) || (r = z(s)) !== s && x(t, r)) || (l ? !n || void 0 === n[s] && void 0 === n[r] || (o[s] = Yr(l, a, s, void 0, e, !0)) : delete o[s]);
                if (s !== a)
                    for (const e in s)
                        t && x(t, e) || (delete s[e],
                        c = !0)
            } else if (8 & i) {
                const n = e.vnode.dynamicProps;
                for (let r = 0; r < n.length; r++) {
                    let i = n[r];
                    if (fn(e.emitsOptions, i))
                        continue;
                    const u = t[i];
                    if (l)
                        if (x(s, i))
                            u !== s[i] && (s[i] = u,
                            c = !0);
                        else {
                            const t = V(i);
                            o[t] = Yr(l, a, t, u, e, !1)
                        }
                    else
                        u !== s[i] && (s[i] = u,
                        c = !0)
                }
            }
            c && we(e, "set", "$attrs")
        }(e, t.props, r, n),
        ( (e, t, n) => {
            const {vnode: r, slots: o} = e;
            let s = !0
              , i = m;
            if (32 & r.shapeFlag) {
                const e = t._;
                e ? n && 1 === e ? s = !1 : (S(o, t),
                n || 1 !== e || delete o._) : (s = !t.$stable,
                oo(t, o)),
                i = t
            } else
                t && (so(e, t),
                i = {
                    default: 1
                });
            if (s)
                for (const a in o)
                    to(a) || a in i || delete o[a]
        }
        )(e, t.children, n),
        ve(),
        tn(),
        ye()
    }
      , $ = (e, t, n, r, o, s, i, a, l=!1) => {
        const u = e && e.children
          , f = e ? e.shapeFlag : 0
          , d = t.children
          , {patchFlag: p, shapeFlag: h} = t;
        if (p > 0) {
            if (128 & p)
                return void L(u, d, n, r, o, s, i, a, l);
            if (256 & p)
                return void D(u, d, n, r, o, s, i, a, l)
        }
        8 & h ? (16 & f && q(u, o, s),
        d !== u && c(n, d)) : 16 & f ? 16 & h ? L(u, d, n, r, o, s, i, a, l) : q(u, o, s, !0) : (8 & f && c(n, ""),
        16 & h && O(d, n, r, o, s, i, a, l))
    }
      , D = (e, t, n, r, o, s, i, a, l) => {
        t = t || g;
        const c = (e = e || g).length
          , u = t.length
          , f = Math.min(c, u);
        let d;
        for (d = 0; d < f; d++) {
            const r = t[d] = l ? Qo(t[d]) : Xo(t[d]);
            h(e[d], r, n, null, o, s, i, a, l)
        }
        c > u ? q(e, o, s, !0, !1, f) : O(t, n, r, o, s, i, a, l, f)
    }
      , L = (e, t, n, r, o, s, i, a, l) => {
        let c = 0;
        const u = t.length;
        let f = e.length - 1
          , d = u - 1;
        for (; c <= f && c <= d; ) {
            const r = e[c]
              , u = t[c] = l ? Qo(t[c]) : Xo(t[c]);
            if (!Io(r, u))
                break;
            h(r, u, n, null, o, s, i, a, l),
            c++
        }
        for (; c <= f && c <= d; ) {
            const r = e[f]
              , c = t[d] = l ? Qo(t[d]) : Xo(t[d]);
            if (!Io(r, c))
                break;
            h(r, c, n, null, o, s, i, a, l),
            f--,
            d--
        }
        if (c > f) {
            if (c <= d) {
                const e = d + 1
                  , f = e < u ? t[e].el : r;
                for (; c <= d; )
                    h(null, t[c] = l ? Qo(t[c]) : Xo(t[c]), n, f, o, s, i, a, l),
                    c++
            }
        } else if (c > d)
            for (; c <= f; )
                I(e[c], o, s, !0),
                c++;
        else {
            const p = c
              , m = c
              , v = new Map;
            for (c = m; c <= d; c++) {
                const e = t[c] = l ? Qo(t[c]) : Xo(t[c]);
                null != e.key && v.set(e.key, c)
            }
            let y, _ = 0;
            const b = d - m + 1;
            let w = !1
              , S = 0;
            const C = new Array(b);
            for (c = 0; c < b; c++)
                C[c] = 0;
            for (c = p; c <= f; c++) {
                const r = e[c];
                if (_ >= b) {
                    I(r, o, s, !0);
                    continue
                }
                let u;
                if (null != r.key)
                    u = v.get(r.key);
                else
                    for (y = m; y <= d; y++)
                        if (0 === C[y - m] && Io(r, t[y])) {
                            u = y;
                            break
                        }
                void 0 === u ? I(r, o, s, !0) : (C[u - m] = c + 1,
                u >= S ? S = u : w = !0,
                h(r, t[u], n, null, o, s, i, a, l),
                _++)
            }
            const E = w ? function(e) {
                const t = e.slice()
                  , n = [0];
                let r, o, s, i, a;
                const l = e.length;
                for (r = 0; r < l; r++) {
                    const l = e[r];
                    if (0 !== l) {
                        if (o = n[n.length - 1],
                        e[o] < l) {
                            t[r] = o,
                            n.push(r);
                            continue
                        }
                        for (s = 0,
                        i = n.length - 1; s < i; )
                            a = s + i >> 1,
                            e[n[a]] < l ? s = a + 1 : i = a;
                        l < e[n[s]] && (s > 0 && (t[r] = n[s - 1]),
                        n[s] = r)
                    }
                }
                s = n.length,
                i = n[s - 1];
                for (; s-- > 0; )
                    n[s] = i,
                    i = t[i];
                return n
            }(C) : g;
            for (y = E.length - 1,
            c = b - 1; c >= 0; c--) {
                const e = m + c
                  , f = t[e]
                  , d = e + 1 < u ? t[e + 1].el : r;
                0 === C[c] ? h(null, f, n, d, o, s, i, a, l) : w && (y < 0 || c !== E[y] ? U(f, n, d, 2) : y--)
            }
        }
    }
      , U = (e, t, r, o, s=null) => {
        const {el: i, type: a, transition: l, children: c, shapeFlag: u} = e;
        if (6 & u)
            return void U(e.component.subTree, t, r, o);
        if (128 & u)
            return void e.suspense.move(t, r, o);
        if (64 & u)
            return void a.move(e, t, r, X);
        if (a === Ao) {
            n(i, t, r);
            for (let e = 0; e < c.length; e++)
                U(c[e], t, r, o);
            return void n(e.anchor, t, r)
        }
        if (a === ko)
            return void ( ({el: e, anchor: t}, r, o) => {
                let s;
                for (; e && e !== t; )
                    s = f(e),
                    n(e, r, o),
                    e = s;
                n(t, r, o)
            }
            )(e, t, r);
        if (2 !== o && 1 & u && l)
            if (0 === o)
                l.beforeEnter(i),
                n(i, t, r),
                mo(( () => l.enter(i)), s);
            else {
                const {leave: e, delayLeave: o, afterLeave: s} = l
                  , a = () => n(i, t, r)
                  , c = () => {
                    e(i, ( () => {
                        a(),
                        s && s()
                    }
                    ))
                }
                ;
                o ? o(i, a, c) : c()
            }
        else
            n(i, t, r)
    }
      , I = (e, t, n, r=!1, o=!1) => {
        const {type: s, props: i, ref: a, children: l, dynamicChildren: c, shapeFlag: u, patchFlag: f, dirs: d} = e;
        if (null != a && co(a, null, n, e, !0),
        256 & u)
            return void t.ctx.deactivate(e);
        const p = 1 & u && d
          , h = !Gn(e);
        let m;
        if (h && (m = i && i.onVnodeBeforeUnmount) && ns(m, t, e),
        6 & u)
            K(e.component, n, r);
        else {
            if (128 & u)
                return void e.suspense.unmount(n, r);
            p && wr(e, null, t, "beforeUnmount"),
            64 & u ? e.type.remove(e, t, n, o, X, r) : c && (s !== Ao || f > 0 && 64 & f) ? q(c, t, n, !1, !0) : (s === Ao && 384 & f || !o && 16 & u) && q(l, t, n),
            r && H(e)
        }
        (h && (m = i && i.onVnodeUnmounted) || p) && mo(( () => {
            m && ns(m, t, e),
            p && wr(e, null, t, "unmounted")
        }
        ), n)
    }
      , H = e => {
        const {type: t, el: n, anchor: o, transition: s} = e;
        if (t === Ao)
            return void W(n, o);
        if (t === ko)
            return void ( ({el: e, anchor: t}) => {
                let n;
                for (; e && e !== t; )
                    n = f(e),
                    r(e),
                    e = n;
                r(t)
            }
            )(e);
        const i = () => {
            r(n),
            s && !s.persisted && s.afterLeave && s.afterLeave()
        }
        ;
        if (1 & e.shapeFlag && s && !s.persisted) {
            const {leave: t, delayLeave: r} = s
              , o = () => t(n, i);
            r ? r(e.el, i, o) : o()
        } else
            i()
    }
      , W = (e, t) => {
        let n;
        for (; e !== t; )
            n = f(e),
            r(e),
            e = n;
        r(t)
    }
      , K = (e, t, n) => {
        const {bum: r, scope: o, update: s, subTree: i, um: a} = e;
        r && J(r),
        o.stop(),
        s && (s.active = !1,
        I(i, e, t, n)),
        a && mo(a, t),
        mo(( () => {
            e.isUnmounted = !0
        }
        ), t),
        t && t.pendingBranch && !t.isUnmounted && e.asyncDep && !e.asyncResolved && e.suspenseId === t.pendingId && (t.deps--,
        0 === t.deps && t.resolve())
    }
      , q = (e, t, n, r=!1, o=!1, s=0) => {
        for (let i = s; i < e.length; i++)
            I(e[i], t, n, r, o)
    }
      , Y = e => 6 & e.shapeFlag ? Y(e.component.subTree) : 128 & e.shapeFlag ? e.suspense.next() : f(e.anchor || e.el)
      , G = (e, t, n) => {
        null == e ? t._vnode && I(t._vnode, null, null, !0) : h(t._vnode || null, e, t, null, null, null, n),
        tn(),
        nn(),
        t._vnode = e
    }
      , X = {
        p: h,
        um: I,
        m: U,
        r: H,
        mt: F,
        mc: O,
        pc: $,
        pbc: R,
        n: Y,
        o: e
    };
    let Q, ee;
    return t && ([Q,ee] = t(X)),
    {
        render: G,
        hydrate: Q,
        createApp: lo(G, Q)
    }
}
function _o({effect: e, update: t}, n) {
    e.allowRecurse = t.allowRecurse = n
}
function bo(e, t, n=!1) {
    const r = e.children
      , o = t.children;
    if (O(r) && O(o))
        for (let s = 0; s < r.length; s++) {
            const e = r[s];
            let t = o[s];
            1 & t.shapeFlag && !t.dynamicChildren && ((t.patchFlag <= 0 || 32 === t.patchFlag) && (t = o[s] = Qo(o[s]),
            t.el = e.el),
            n || bo(e, t)),
            t.type === Ro && (t.el = e.el)
        }
}
const wo = e => e && (e.disabled || "" === e.disabled)
  , So = e => "undefined" != typeof SVGElement && e instanceof SVGElement
  , Co = (e, t) => {
    const n = e && e.to;
    if (P(n)) {
        if (t) {
            return t(n)
        }
        return null
    }
    return n
}
;
function Eo(e, t, n, {o: {insert: r}, m: o}, s=2) {
    0 === s && r(e.targetAnchor, t, n);
    const {el: i, anchor: a, shapeFlag: l, children: c, props: u} = e
      , f = 2 === s;
    if (f && r(i, t, n),
    (!f || wo(u)) && 16 & l)
        for (let d = 0; d < c.length; d++)
            o(c[d], t, n, 2);
    f && r(a, t, n)
}
const xo = {
    __isTeleport: !0,
    process(e, t, n, r, o, s, i, a, l, c) {
        const {mc: u, pc: f, pbc: d, o: {insert: p, querySelector: h, createText: m, createComment: g}} = c
          , v = wo(t.props);
        let {shapeFlag: y, children: _, dynamicChildren: b} = t;
        if (null == e) {
            const e = t.el = m("")
              , c = t.anchor = m("");
            p(e, n, r),
            p(c, n, r);
            const f = t.target = Co(t.props, h)
              , d = t.targetAnchor = m("");
            f && (p(d, f),
            i = i || So(f));
            const g = (e, t) => {
                16 & y && u(_, e, t, o, s, i, a, l)
            }
            ;
            v ? g(n, c) : f && g(f, d)
        } else {
            t.el = e.el;
            const r = t.anchor = e.anchor
              , u = t.target = e.target
              , p = t.targetAnchor = e.targetAnchor
              , m = wo(e.props)
              , g = m ? n : u
              , y = m ? r : p;
            if (i = i || So(u),
            b ? (d(e.dynamicChildren, b, g, o, s, i, a),
            bo(e, t, !0)) : l || f(e, t, g, y, o, s, i, a, !1),
            v)
                m || Eo(t, n, r, c, 1);
            else if ((t.props && t.props.to) !== (e.props && e.props.to)) {
                const e = t.target = Co(t.props, h);
                e && Eo(t, e, null, c, 0)
            } else
                m && Eo(t, u, p, c, 1)
        }
        Oo(t)
    },
    remove(e, t, n, r, {um: o, o: {remove: s}}, i) {
        const {shapeFlag: a, children: l, anchor: c, targetAnchor: u, target: f, props: d} = e;
        if (f && s(u),
        (i || !wo(d)) && (s(c),
        16 & a))
            for (let p = 0; p < l.length; p++) {
                const e = l[p];
                o(e, t, n, !0, !!e.dynamicChildren)
            }
    },
    move: Eo,
    hydrate: function(e, t, n, r, o, s, {o: {nextSibling: i, parentNode: a, querySelector: l}}, c) {
        const u = t.target = Co(t.props, l);
        if (u) {
            const l = u._lpa || u.firstChild;
            if (16 & t.shapeFlag)
                if (wo(t.props))
                    t.anchor = c(i(e), t, a(e), n, r, o, s),
                    t.targetAnchor = l;
                else {
                    t.anchor = i(e);
                    let a = l;
                    for (; a; )
                        if (a = i(a),
                        a && 8 === a.nodeType && "teleport anchor" === a.data) {
                            t.targetAnchor = a,
                            u._lpa = t.targetAnchor && i(t.targetAnchor);
                            break
                        }
                    c(l, t, u, n, r, o, s)
                }
            Oo(t)
        }
        return t.anchor && i(t.anchor)
    }
};
function Oo(e) {
    const t = e.ctx;
    if (t && t.ut) {
        let n = e.children[0].el;
        for (; n !== e.targetAnchor; )
            1 === n.nodeType && n.setAttribute("data-v-owner", t.uid),
            n = n.nextSibling;
        t.ut()
    }
}
const Ao = Symbol(void 0)
  , Ro = Symbol(void 0)
  , To = Symbol(void 0)
  , ko = Symbol(void 0)
  , Po = [];
let Fo = null;
function jo(e=!1) {
    Po.push(Fo = e ? null : [])
}
function No() {
    Po.pop(),
    Fo = Po[Po.length - 1] || null
}
let Mo = 1;
function $o(e) {
    Mo += e
}
function Do(e) {
    return e.dynamicChildren = Mo > 0 ? Fo || g : null,
    No(),
    Mo > 0 && Fo && Fo.push(e),
    e
}
function Lo(e, t, n, r, o, s) {
    return Do(Wo(e, t, n, r, o, s, !0))
}
function Bo(e, t, n, r, o) {
    return Do(Ko(e, t, n, r, o, !0))
}
function Uo(e) {
    return !!e && !0 === e.__v_isVNode
}
function Io(e, t) {
    return e.type === t.type && e.key === t.key
}
const Vo = "__vInternal"
  , Ho = ({key: e}) => null != e ? e : null
  , zo = ({ref: e, ref_key: t, ref_for: n}) => null != e ? P(e) || xt(e) || k(e) ? {
    i: dn,
    r: e,
    k: t,
    f: !!n
} : e : null;
function Wo(e, t=null, n=null, r=0, o=null, s=(e === Ao ? 0 : 1), i=!1, a=!1) {
    const l = {
        __v_isVNode: !0,
        __v_skip: !0,
        type: e,
        props: t,
        key: t && Ho(t),
        ref: t && zo(t),
        scopeId: pn,
        slotScopeIds: null,
        children: n,
        component: null,
        suspense: null,
        ssContent: null,
        ssFallback: null,
        dirs: null,
        transition: null,
        el: null,
        anchor: null,
        target: null,
        targetAnchor: null,
        staticCount: 0,
        shapeFlag: s,
        patchFlag: r,
        dynamicProps: o,
        dynamicChildren: null,
        appContext: null,
        ctx: dn
    };
    return a ? (es(l, n),
    128 & s && e.normalize(l)) : n && (l.shapeFlag |= P(n) ? 8 : 16),
    Mo > 0 && !i && Fo && (l.patchFlag > 0 || 6 & s) && 32 !== l.patchFlag && Fo.push(l),
    l
}
const Ko = function(e, t=null, r=null, o=0, s=null, i=!1) {
    e && e !== Cr || (e = To);
    if (Uo(e)) {
        const n = Jo(e, t, !0);
        return r && es(n, r),
        Mo > 0 && !i && Fo && (6 & n.shapeFlag ? Fo[Fo.indexOf(e)] = n : Fo.push(n)),
        n.patchFlag |= -2,
        n
    }
    l = e,
    k(l) && "__vccOpts"in l && (e = e.__vccOpts);
    var l;
    if (t) {
        t = qo(t);
        let {class: e, style: r} = t;
        e && !P(e) && (t.class = a(e)),
        j(r) && (yt(r) && !O(r) && (r = S({}, r)),
        t.style = n(r))
    }
    const c = P(e) ? 1 : Cn(e) ? 128 : (e => e.__isTeleport)(e) ? 64 : j(e) ? 4 : k(e) ? 2 : 0;
    return Wo(e, t, r, o, s, c, i, !0)
};
function qo(e) {
    return e ? yt(e) || Vo in e ? S({}, e) : e : null
}
function Jo(e, t, n=!1) {
    const {props: r, ref: o, patchFlag: s, children: i} = e
      , a = t ? ts(r || {}, t) : r;
    return {
        __v_isVNode: !0,
        __v_skip: !0,
        type: e.type,
        props: a,
        key: a && Ho(a),
        ref: t && t.ref ? n && o ? O(o) ? o.concat(zo(t)) : [o, zo(t)] : zo(t) : o,
        scopeId: e.scopeId,
        slotScopeIds: e.slotScopeIds,
        children: i,
        target: e.target,
        targetAnchor: e.targetAnchor,
        staticCount: e.staticCount,
        shapeFlag: e.shapeFlag,
        patchFlag: t && e.type !== Ao ? -1 === s ? 16 : 16 | s : s,
        dynamicProps: e.dynamicProps,
        dynamicChildren: e.dynamicChildren,
        appContext: e.appContext,
        dirs: e.dirs,
        transition: e.transition,
        component: e.component,
        suspense: e.suspense,
        ssContent: e.ssContent && Jo(e.ssContent),
        ssFallback: e.ssFallback && Jo(e.ssFallback),
        el: e.el,
        anchor: e.anchor,
        ctx: e.ctx
    }
}
function Yo(e=" ", t=0) {
    return Ko(Ro, null, e, t)
}
function Go(e, t) {
    const n = Ko(ko, null, e);
    return n.staticCount = t,
    n
}
function Zo(e="", t=!1) {
    return t ? (jo(),
    Bo(To, null, e)) : Ko(To, null, e)
}
function Xo(e) {
    return null == e || "boolean" == typeof e ? Ko(To) : O(e) ? Ko(Ao, null, e.slice()) : "object" == typeof e ? Qo(e) : Ko(Ro, null, String(e))
}
function Qo(e) {
    return null === e.el && -1 !== e.patchFlag || e.memo ? e : Jo(e)
}
function es(e, t) {
    let n = 0;
    const {shapeFlag: r} = e;
    if (null == t)
        t = null;
    else if (O(t))
        n = 16;
    else if ("object" == typeof t) {
        if (65 & r) {
            const n = t.default;
            return void (n && (n._c && (n._d = !1),
            es(e, n()),
            n._c && (n._d = !0)))
        }
        {
            n = 32;
            const r = t._;
            r || Vo in t ? 3 === r && dn && (1 === dn.slots._ ? t._ = 1 : (t._ = 2,
            e.patchFlag |= 1024)) : t._ctx = dn
        }
    } else
        k(t) ? (t = {
            default: t,
            _ctx: dn
        },
        n = 32) : (t = String(t),
        64 & r ? (n = 16,
        t = [Yo(t)]) : n = 8);
    e.children = t,
    e.shapeFlag |= n
}
function ts(...e) {
    const t = {};
    for (let r = 0; r < e.length; r++) {
        const o = e[r];
        for (const e in o)
            if ("class" === e)
                t.class !== o.class && (t.class = a([t.class, o.class]));
            else if ("style" === e)
                t.style = n([t.style, o.style]);
            else if (b(e)) {
                const n = t[e]
                  , r = o[e];
                !r || n === r || O(n) && n.includes(r) || (t[e] = n ? [].concat(n, r) : r)
            } else
                "" !== e && (t[e] = o[e])
    }
    return t
}
function ns(e, t, n, r=null) {
    Ut(e, t, 7, [n, r])
}
const rs = io();
let os = 0;
function ss(e, t, n) {
    const r = e.type
      , o = (t ? t.appContext : e.appContext) || rs
      , s = {
        uid: os++,
        vnode: e,
        type: r,
        parent: t,
        appContext: o,
        root: null,
        next: null,
        subTree: null,
        effect: null,
        update: null,
        scope: new Q(!0),
        render: null,
        proxy: null,
        exposed: null,
        exposeProxy: null,
        withProxy: null,
        provides: t ? t.provides : Object.create(o.provides),
        accessCache: null,
        renderCache: [],
        components: null,
        directives: null,
        propsOptions: Gr(r, o),
        emitsOptions: un(r, o),
        emit: null,
        emitted: null,
        propsDefaults: m,
        inheritAttrs: r.inheritAttrs,
        ctx: m,
        data: m,
        props: m,
        attrs: m,
        slots: m,
        refs: m,
        setupState: m,
        setupContext: null,
        suspense: n,
        suspenseId: n ? n.pendingId : 0,
        asyncDep: null,
        asyncResolved: !1,
        isMounted: !1,
        isUnmounted: !1,
        isDeactivated: !1,
        bc: null,
        c: null,
        bm: null,
        m: null,
        bu: null,
        u: null,
        um: null,
        bum: null,
        da: null,
        a: null,
        rtg: null,
        rtc: null,
        ec: null,
        sp: null
    };
    return s.ctx = {
        _: s
    },
    s.root = t ? t.root : s,
    s.emit = cn.bind(null, s),
    e.ce && e.ce(s),
    s
}
let is = null;
const as = () => is || dn
  , ls = e => {
    is = e,
    e.scope.on()
}
  , cs = () => {
    is && is.scope.off(),
    is = null
}
;
function us(e) {
    return 4 & e.vnode.shapeFlag
}
let fs, ds, ps = !1;
function hs(e, t=!1) {
    ps = t;
    const {props: n, children: r} = e.vnode
      , o = us(e);
    !function(e, t, n, r=!1) {
        const o = {}
          , s = {};
        Y(s, Vo, 1),
        e.propsDefaults = Object.create(null),
        Jr(e, t, o, s);
        for (const i in e.propsOptions[0])
            i in o || (o[i] = void 0);
        n ? e.props = r ? o : dt(o) : e.type.props ? e.props = o : e.props = s,
        e.attrs = s
    }(e, n, o, t),
    ( (e, t) => {
        if (32 & e.vnode.shapeFlag) {
            const n = t._;
            n ? (e.slots = _t(t),
            Y(t, "_", n)) : oo(t, e.slots = {})
        } else
            e.slots = {},
            t && so(e, t);
        Y(e.slots, Vo, 1)
    }
    )(e, r);
    const s = o ? function(e, t) {
        const n = e.type;
        e.accessCache = Object.create(null),
        e.proxy = bt(new Proxy(e.ctx,Mr));
        const {setup: r} = n;
        if (r) {
            const n = e.setupContext = r.length > 1 ? vs(e) : null;
            ls(e),
            ve();
            const o = Bt(r, e, 0, [e.props, n]);
            if (ye(),
            cs(),
            N(o)) {
                if (o.then(cs, cs),
                t)
                    return o.then((n => {
                        ms(e, n, t)
                    }
                    )).catch((t => {
                        It(t, e, 0)
                    }
                    ));
                e.asyncDep = o
            } else
                ms(e, o, t)
        } else
            gs(e, t)
    }(e, t) : void 0;
    return ps = !1,
    s
}
function ms(e, t, n) {
    k(t) ? e.type.__ssrInlineRender ? e.ssrRender = t : e.render = t : j(t) && (e.setupState = Ft(t)),
    gs(e, n)
}
function gs(e, t, n) {
    const r = e.type;
    if (!e.render) {
        if (!t && fs && !r.render) {
            const t = r.template || Ir(e).template;
            if (t) {
                const {isCustomElement: n, compilerOptions: o} = e.appContext.config
                  , {delimiters: s, compilerOptions: i} = r
                  , a = S(S({
                    isCustomElement: n,
                    delimiters: s
                }, o), i);
                r.render = fs(t, a)
            }
        }
        e.render = r.render || v,
        ds && ds(e)
    }
    ls(e),
    ve(),
    Lr(e),
    ye(),
    cs()
}
function vs(e) {
    const t = t => {
        e.exposed = t || {}
    }
    ;
    let n;
    return {
        get attrs() {
            return n || (n = function(e) {
                return new Proxy(e.attrs,{
                    get: (t, n) => (_e(e, 0, "$attrs"),
                    t[n])
                })
            }(e))
        },
        slots: e.slots,
        emit: e.emit,
        expose: t
    }
}
function ys(e) {
    if (e.exposed)
        return e.exposeProxy || (e.exposeProxy = new Proxy(Ft(bt(e.exposed)),{
            get: (t, n) => n in t ? t[n] : n in jr ? jr[n](e) : void 0,
            has: (e, t) => t in e || t in jr
        }))
}
function _s(e, t=!0) {
    return k(e) ? e.displayName || e.name : e.name || t && e.__name
}
const bs = (e, t) => function(e, t, n=!1) {
    let r, o;
    const s = k(e);
    return s ? (r = e,
    o = v) : (r = e.get,
    o = e.set),
    new Lt(r,o,s || !o,n)
}(e, 0, ps);
function ws() {
    const e = as();
    return e.setupContext || (e.setupContext = vs(e))
}
function Ss(e, t, n) {
    const r = arguments.length;
    return 2 === r ? j(t) && !O(t) ? Uo(t) ? Ko(e, null, [t]) : Ko(e, t) : Ko(e, null, t) : (r > 3 ? n = Array.prototype.slice.call(arguments, 2) : 3 === r && Uo(n) && (n = [n]),
    Ko(e, t, n))
}
const Cs = Symbol("")
  , Es = () => Pn(Cs);
function xs(e, t) {
    const n = e.memo;
    if (n.length != t.length)
        return !1;
    for (let r = 0; r < n.length; r++)
        if (q(n[r], t[r]))
            return !1;
    return Mo > 0 && Fo && Fo.push(e),
    !0
}
const Os = "3.2.45"
  , As = {
    createComponentInstance: ss,
    setupComponent: hs,
    renderComponentRoot: yn,
    setCurrentRenderingInstance: hn,
    isVNode: Uo,
    normalizeVNode: Xo
}
  , Rs = "undefined" != typeof document ? document : null
  , Ts = Rs && Rs.createElement("template")
  , ks = {
    insert: (e, t, n) => {
        t.insertBefore(e, n || null)
    }
    ,
    remove: e => {
        const t = e.parentNode;
        t && t.removeChild(e)
    }
    ,
    createElement: (e, t, n, r) => {
        const o = t ? Rs.createElementNS("http://www.w3.org/2000/svg", e) : Rs.createElement(e, n ? {
            is: n
        } : void 0);
        return "select" === e && r && null != r.multiple && o.setAttribute("multiple", r.multiple),
        o
    }
    ,
    createText: e => Rs.createTextNode(e),
    createComment: e => Rs.createComment(e),
    setText: (e, t) => {
        e.nodeValue = t
    }
    ,
    setElementText: (e, t) => {
        e.textContent = t
    }
    ,
    parentNode: e => e.parentNode,
    nextSibling: e => e.nextSibling,
    querySelector: e => Rs.querySelector(e),
    setScopeId(e, t) {
        e.setAttribute(t, "")
    },
    insertStaticContent(e, t, n, r, o, s) {
        const i = n ? n.previousSibling : t.lastChild;
        if (o && (o === s || o.nextSibling))
            for (; t.insertBefore(o.cloneNode(!0), n),
            o !== s && (o = o.nextSibling); )
                ;
        else {
            Ts.innerHTML = r ? `<svg>${e}</svg>` : e;
            const o = Ts.content;
            if (r) {
                const e = o.firstChild;
                for (; e.firstChild; )
                    o.appendChild(e.firstChild);
                o.removeChild(e)
            }
            t.insertBefore(o, n)
        }
        return [i ? i.nextSibling : t.firstChild, n ? n.previousSibling : t.lastChild]
    }
};
const Ps = /\s*!important$/;
function Fs(e, t, n) {
    if (O(n))
        n.forEach((n => Fs(e, t, n)));
    else if (null == n && (n = ""),
    t.startsWith("--"))
        e.setProperty(t, n);
    else {
        const r = function(e, t) {
            const n = Ns[t];
            if (n)
                return n;
            let r = V(t);
            if ("filter" !== r && r in e)
                return Ns[t] = r;
            r = W(r);
            for (let o = 0; o < js.length; o++) {
                const n = js[o] + r;
                if (n in e)
                    return Ns[t] = n
            }
            return t
        }(e, t);
        Ps.test(n) ? e.setProperty(z(r), n.replace(Ps, ""), "important") : e[r] = n
    }
}
const js = ["Webkit", "Moz", "ms"]
  , Ns = {};
const Ms = "http://www.w3.org/1999/xlink";
function $s(e, t, n, r) {
    e.addEventListener(t, n, r)
}
function Ds(e, t, n, r, o=null) {
    const s = e._vei || (e._vei = {})
      , i = s[t];
    if (r && i)
        i.value = r;
    else {
        const [n,a] = function(e) {
            let t;
            if (Ls.test(e)) {
                let n;
                for (t = {}; n = e.match(Ls); )
                    e = e.slice(0, e.length - n[0].length),
                    t[n[0].toLowerCase()] = !0
            }
            return [":" === e[2] ? e.slice(3) : z(e.slice(2)), t]
        }(t);
        if (r) {
            const i = s[t] = function(e, t) {
                const n = e => {
                    if (e._vts) {
                        if (e._vts <= n.attached)
                            return
                    } else
                        e._vts = Date.now();
                    Ut(function(e, t) {
                        if (O(t)) {
                            const n = e.stopImmediatePropagation;
                            return e.stopImmediatePropagation = () => {
                                n.call(e),
                                e._stopped = !0
                            }
                            ,
                            t.map((e => t => !t._stopped && e && e(t)))
                        }
                        return t
                    }(e, n.value), t, 5, [e])
                }
                ;
                return n.value = e,
                n.attached = ( () => Bs || (Us.then(( () => Bs = 0)),
                Bs = Date.now()))(),
                n
            }(r, o);
            $s(e, n, i, a)
        } else
            i && (!function(e, t, n, r) {
                e.removeEventListener(t, n, r)
            }(e, n, i, a),
            s[t] = void 0)
    }
}
const Ls = /(?:Once|Passive|Capture)$/;
let Bs = 0;
const Us = Promise.resolve();
const Is = /^on[a-z]/;
function Vs(e, t) {
    const n = Yn(e);
    class r extends zs {
        constructor(e) {
            super(n, e, t)
        }
    }
    return r.def = n,
    r
}
const Hs = "undefined" != typeof HTMLElement ? HTMLElement : class {
}
;
class zs extends Hs {
    constructor(e, t={}, n) {
        super(),
        this._def = e,
        this._props = t,
        this._instance = null,
        this._connected = !1,
        this._resolved = !1,
        this._numberProps = null,
        this.shadowRoot && n ? n(this._createVNode(), this.shadowRoot) : (this.attachShadow({
            mode: "open"
        }),
        this._def.__asyncLoader || this._resolveProps(this._def))
    }
    connectedCallback() {
        this._connected = !0,
        this._instance || (this._resolved ? this._update() : this._resolveDef())
    }
    disconnectedCallback() {
        this._connected = !1,
        Zt(( () => {
            this._connected || (Ii(null, this.shadowRoot),
            this._instance = null)
        }
        ))
    }
    _resolveDef() {
        this._resolved = !0;
        for (let n = 0; n < this.attributes.length; n++)
            this._setAttr(this.attributes[n].name);
        new MutationObserver((e => {
            for (const t of e)
                this._setAttr(t.attributeName)
        }
        )).observe(this, {
            attributes: !0
        });
        const e = (e, t=!1) => {
            const {props: n, styles: r} = e;
            let o;
            if (n && !O(n))
                for (const s in n) {
                    const e = n[s];
                    (e === Number || e && e.type === Number) && (s in this._props && (this._props[s] = G(this._props[s])),
                    (o || (o = Object.create(null)))[V(s)] = !0)
                }
            this._numberProps = o,
            t && this._resolveProps(e),
            this._applyStyles(r),
            this._update()
        }
          , t = this._def.__asyncLoader;
        t ? t().then((t => e(t, !0))) : e(this._def)
    }
    _resolveProps(e) {
        const {props: t} = e
          , n = O(t) ? t : Object.keys(t || {});
        for (const r of Object.keys(this))
            "_" !== r[0] && n.includes(r) && this._setProp(r, this[r], !0, !1);
        for (const r of n.map(V))
            Object.defineProperty(this, r, {
                get() {
                    return this._getProp(r)
                },
                set(e) {
                    this._setProp(r, e)
                }
            })
    }
    _setAttr(e) {
        let t = this.getAttribute(e);
        const n = V(e);
        this._numberProps && this._numberProps[n] && (t = G(t)),
        this._setProp(n, t, !1)
    }
    _getProp(e) {
        return this._props[e]
    }
    _setProp(e, t, n=!0, r=!0) {
        t !== this._props[e] && (this._props[e] = t,
        r && this._instance && this._update(),
        n && (!0 === t ? this.setAttribute(z(e), "") : "string" == typeof t || "number" == typeof t ? this.setAttribute(z(e), t + "") : t || this.removeAttribute(z(e))))
    }
    _update() {
        Ii(this._createVNode(), this.shadowRoot)
    }
    _createVNode() {
        const e = Ko(this._def, S({}, this._props));
        return this._instance || (e.ce = e => {
            this._instance = e,
            e.isCE = !0;
            const t = (e, t) => {
                this.dispatchEvent(new CustomEvent(e,{
                    detail: t
                }))
            }
            ;
            e.emit = (e, ...n) => {
                t(e, n),
                z(e) !== e && t(z(e), n)
            }
            ;
            let n = this;
            for (; n = n && (n.parentNode || n.host); )
                if (n instanceof zs) {
                    e.parent = n._instance,
                    e.provides = n._instance.provides;
                    break
                }
        }
        ),
        e
    }
    _applyStyles(e) {
        e && e.forEach((e => {
            const t = document.createElement("style");
            t.textContent = e,
            this.shadowRoot.appendChild(t)
        }
        ))
    }
}
function Ws(e, t) {
    if (128 & e.shapeFlag) {
        const n = e.suspense;
        e = n.activeBranch,
        n.pendingBranch && !n.isHydrating && n.effects.push(( () => {
            Ws(n.activeBranch, t)
        }
        ))
    }
    for (; e.component; )
        e = e.component.subTree;
    if (1 & e.shapeFlag && e.el)
        Ks(e.el, t);
    else if (e.type === Ao)
        e.children.forEach((e => Ws(e, t)));
    else if (e.type === ko) {
        let {el: n, anchor: r} = e;
        for (; n && (Ks(n, t),
        n !== r); )
            n = n.nextSibling
    }
}
function Ks(e, t) {
    if (1 === e.nodeType) {
        const n = e.style;
        for (const e in t)
            n.setProperty(`--${e}`, t[e])
    }
}
const qs = (e, {slots: t}) => Ss(Vn, Xs(e), t);
qs.displayName = "Transition";
const Js = {
    name: String,
    type: String,
    css: {
        type: Boolean,
        default: !0
    },
    duration: [String, Number, Object],
    enterFromClass: String,
    enterActiveClass: String,
    enterToClass: String,
    appearFromClass: String,
    appearActiveClass: String,
    appearToClass: String,
    leaveFromClass: String,
    leaveActiveClass: String,
    leaveToClass: String
}
  , Ys = qs.props = S({}, Vn.props, Js)
  , Gs = (e, t=[]) => {
    O(e) ? e.forEach((e => e(...t))) : e && e(...t)
}
  , Zs = e => !!e && (O(e) ? e.some((e => e.length > 1)) : e.length > 1);
function Xs(e) {
    const t = {};
    for (const S in e)
        S in Js || (t[S] = e[S]);
    if (!1 === e.css)
        return t;
    const {name: n="v", type: r, duration: o, enterFromClass: s=`${n}-enter-from`, enterActiveClass: i=`${n}-enter-active`, enterToClass: a=`${n}-enter-to`, appearFromClass: l=s, appearActiveClass: c=i, appearToClass: u=a, leaveFromClass: f=`${n}-leave-from`, leaveActiveClass: d=`${n}-leave-active`, leaveToClass: p=`${n}-leave-to`} = e
      , h = function(e) {
        if (null == e)
            return null;
        if (j(e))
            return [Qs(e.enter), Qs(e.leave)];
        {
            const t = Qs(e);
            return [t, t]
        }
    }(o)
      , m = h && h[0]
      , g = h && h[1]
      , {onBeforeEnter: v, onEnter: y, onEnterCancelled: _, onLeave: b, onLeaveCancelled: w, onBeforeAppear: C=v, onAppear: E=y, onAppearCancelled: x=_} = t
      , O = (e, t, n) => {
        ti(e, t ? u : a),
        ti(e, t ? c : i),
        n && n()
    }
      , A = (e, t) => {
        e._isLeaving = !1,
        ti(e, f),
        ti(e, p),
        ti(e, d),
        t && t()
    }
      , R = e => (t, n) => {
        const o = e ? E : y
          , i = () => O(t, e, n);
        Gs(o, [t, i]),
        ni(( () => {
            ti(t, e ? l : s),
            ei(t, e ? u : a),
            Zs(o) || oi(t, r, m, i)
        }
        ))
    }
    ;
    return S(t, {
        onBeforeEnter(e) {
            Gs(v, [e]),
            ei(e, s),
            ei(e, i)
        },
        onBeforeAppear(e) {
            Gs(C, [e]),
            ei(e, l),
            ei(e, c)
        },
        onEnter: R(!1),
        onAppear: R(!0),
        onLeave(e, t) {
            e._isLeaving = !0;
            const n = () => A(e, t);
            ei(e, f),
            li(),
            ei(e, d),
            ni(( () => {
                e._isLeaving && (ti(e, f),
                ei(e, p),
                Zs(b) || oi(e, r, g, n))
            }
            )),
            Gs(b, [e, n])
        },
        onEnterCancelled(e) {
            O(e, !1),
            Gs(_, [e])
        },
        onAppearCancelled(e) {
            O(e, !0),
            Gs(x, [e])
        },
        onLeaveCancelled(e) {
            A(e),
            Gs(w, [e])
        }
    })
}
function Qs(e) {
    return G(e)
}
function ei(e, t) {
    t.split(/\s+/).forEach((t => t && e.classList.add(t))),
    (e._vtc || (e._vtc = new Set)).add(t)
}
function ti(e, t) {
    t.split(/\s+/).forEach((t => t && e.classList.remove(t)));
    const {_vtc: n} = e;
    n && (n.delete(t),
    n.size || (e._vtc = void 0))
}
function ni(e) {
    requestAnimationFrame(( () => {
        requestAnimationFrame(e)
    }
    ))
}
let ri = 0;
function oi(e, t, n, r) {
    const o = e._endId = ++ri
      , s = () => {
        o === e._endId && r()
    }
    ;
    if (n)
        return setTimeout(s, n);
    const {type: i, timeout: a, propCount: l} = si(e, t);
    if (!i)
        return r();
    const c = i + "end";
    let u = 0;
    const f = () => {
        e.removeEventListener(c, d),
        s()
    }
      , d = t => {
        t.target === e && ++u >= l && f()
    }
    ;
    setTimeout(( () => {
        u < l && f()
    }
    ), a + 1),
    e.addEventListener(c, d)
}
function si(e, t) {
    const n = window.getComputedStyle(e)
      , r = e => (n[e] || "").split(", ")
      , o = r("transitionDelay")
      , s = r("transitionDuration")
      , i = ii(o, s)
      , a = r("animationDelay")
      , l = r("animationDuration")
      , c = ii(a, l);
    let u = null
      , f = 0
      , d = 0;
    "transition" === t ? i > 0 && (u = "transition",
    f = i,
    d = s.length) : "animation" === t ? c > 0 && (u = "animation",
    f = c,
    d = l.length) : (f = Math.max(i, c),
    u = f > 0 ? i > c ? "transition" : "animation" : null,
    d = u ? "transition" === u ? s.length : l.length : 0);
    return {
        type: u,
        timeout: f,
        propCount: d,
        hasTransform: "transition" === u && /\b(transform|all)(,|$)/.test(r("transitionProperty").toString())
    }
}
function ii(e, t) {
    for (; e.length < t.length; )
        e = e.concat(e);
    return Math.max(...t.map(( (t, n) => ai(t) + ai(e[n]))))
}
function ai(e) {
    return 1e3 * Number(e.slice(0, -1).replace(",", "."))
}
function li() {
    return document.body.offsetHeight
}
const ci = new WeakMap
  , ui = new WeakMap
  , fi = {
    name: "TransitionGroup",
    props: S({}, Ys, {
        tag: String,
        moveClass: String
    }),
    setup(e, {slots: t}) {
        const n = as()
          , r = Un();
        let o, s;
        return pr(( () => {
            if (!o.length)
                return;
            const t = e.moveClass || `${e.name || "v"}-move`;
            if (!function(e, t, n) {
                const r = e.cloneNode();
                e._vtc && e._vtc.forEach((e => {
                    e.split(/\s+/).forEach((e => e && r.classList.remove(e)))
                }
                ));
                n.split(/\s+/).forEach((e => e && r.classList.add(e))),
                r.style.display = "none";
                const o = 1 === t.nodeType ? t : t.parentNode;
                o.appendChild(r);
                const {hasTransform: s} = si(r);
                return o.removeChild(r),
                s
            }(o[0].el, n.vnode.el, t))
                return;
            o.forEach(di),
            o.forEach(pi);
            const r = o.filter(hi);
            li(),
            r.forEach((e => {
                const n = e.el
                  , r = n.style;
                ei(n, t),
                r.transform = r.webkitTransform = r.transitionDuration = "";
                const o = n._moveCb = e => {
                    e && e.target !== n || e && !/transform$/.test(e.propertyName) || (n.removeEventListener("transitionend", o),
                    n._moveCb = null,
                    ti(n, t))
                }
                ;
                n.addEventListener("transitionend", o)
            }
            ))
        }
        )),
        () => {
            const i = _t(e)
              , a = Xs(i);
            let l = i.tag || Ao;
            o = s,
            s = t.default ? Jn(t.default()) : [];
            for (let e = 0; e < s.length; e++) {
                const t = s[e];
                null != t.key && qn(t, zn(t, a, r, n))
            }
            if (o)
                for (let e = 0; e < o.length; e++) {
                    const t = o[e];
                    qn(t, zn(t, a, r, n)),
                    ci.set(t, t.el.getBoundingClientRect())
                }
            return Ko(l, null, s)
        }
    }
};
function di(e) {
    const t = e.el;
    t._moveCb && t._moveCb(),
    t._enterCb && t._enterCb()
}
function pi(e) {
    ui.set(e, e.el.getBoundingClientRect())
}
function hi(e) {
    const t = ci.get(e)
      , n = ui.get(e)
      , r = t.left - n.left
      , o = t.top - n.top;
    if (r || o) {
        const t = e.el.style;
        return t.transform = t.webkitTransform = `translate(${r}px,${o}px)`,
        t.transitionDuration = "0s",
        e
    }
}
const mi = e => {
    const t = e.props["onUpdate:modelValue"] || !1;
    return O(t) ? e => J(t, e) : t
}
;
function gi(e) {
    e.target.composing = !0
}
function vi(e) {
    const t = e.target;
    t.composing && (t.composing = !1,
    t.dispatchEvent(new Event("input")))
}
const yi = {
    created(e, {modifiers: {lazy: t, trim: n, number: r}}, o) {
        e._assign = mi(o);
        const s = r || o.props && "number" === o.props.type;
        $s(e, t ? "change" : "input", (t => {
            if (t.target.composing)
                return;
            let r = e.value;
            n && (r = r.trim()),
            s && (r = G(r)),
            e._assign(r)
        }
        )),
        n && $s(e, "change", ( () => {
            e.value = e.value.trim()
        }
        )),
        t || ($s(e, "compositionstart", gi),
        $s(e, "compositionend", vi),
        $s(e, "change", vi))
    },
    mounted(e, {value: t}) {
        e.value = null == t ? "" : t
    },
    beforeUpdate(e, {value: t, modifiers: {lazy: n, trim: r, number: o}}, s) {
        if (e._assign = mi(s),
        e.composing)
            return;
        if (document.activeElement === e && "range" !== e.type) {
            if (n)
                return;
            if (r && e.value.trim() === t)
                return;
            if ((o || "number" === e.type) && G(e.value) === t)
                return
        }
        const i = null == t ? "" : t;
        e.value !== i && (e.value = i)
    }
}
  , _i = {
    deep: !0,
    created(e, t, n) {
        e._assign = mi(n),
        $s(e, "change", ( () => {
            const t = e._modelValue
              , n = Ei(e)
              , r = e.checked
              , o = e._assign;
            if (O(t)) {
                const e = d(t, n)
                  , s = -1 !== e;
                if (r && !s)
                    o(t.concat(n));
                else if (!r && s) {
                    const n = [...t];
                    n.splice(e, 1),
                    o(n)
                }
            } else if (R(t)) {
                const e = new Set(t);
                r ? e.add(n) : e.delete(n),
                o(e)
            } else
                o(xi(e, r))
        }
        ))
    },
    mounted: bi,
    beforeUpdate(e, t, n) {
        e._assign = mi(n),
        bi(e, t, n)
    }
};
function bi(e, {value: t, oldValue: n}, r) {
    e._modelValue = t,
    O(t) ? e.checked = d(t, r.props.value) > -1 : R(t) ? e.checked = t.has(r.props.value) : t !== n && (e.checked = f(t, xi(e, !0)))
}
const wi = {
    created(e, {value: t}, n) {
        e.checked = f(t, n.props.value),
        e._assign = mi(n),
        $s(e, "change", ( () => {
            e._assign(Ei(e))
        }
        ))
    },
    beforeUpdate(e, {value: t, oldValue: n}, r) {
        e._assign = mi(r),
        t !== n && (e.checked = f(t, r.props.value))
    }
}
  , Si = {
    deep: !0,
    created(e, {value: t, modifiers: {number: n}}, r) {
        const o = R(t);
        $s(e, "change", ( () => {
            const t = Array.prototype.filter.call(e.options, (e => e.selected)).map((e => n ? G(Ei(e)) : Ei(e)));
            e._assign(e.multiple ? o ? new Set(t) : t : t[0])
        }
        )),
        e._assign = mi(r)
    },
    mounted(e, {value: t}) {
        Ci(e, t)
    },
    beforeUpdate(e, t, n) {
        e._assign = mi(n)
    },
    updated(e, {value: t}) {
        Ci(e, t)
    }
};
function Ci(e, t) {
    const n = e.multiple;
    if (!n || O(t) || R(t)) {
        for (let r = 0, o = e.options.length; r < o; r++) {
            const o = e.options[r]
              , s = Ei(o);
            if (n)
                O(t) ? o.selected = d(t, s) > -1 : o.selected = t.has(s);
            else if (f(Ei(o), t))
                return void (e.selectedIndex !== r && (e.selectedIndex = r))
        }
        n || -1 === e.selectedIndex || (e.selectedIndex = -1)
    }
}
function Ei(e) {
    return "_value"in e ? e._value : e.value
}
function xi(e, t) {
    const n = t ? "_trueValue" : "_falseValue";
    return n in e ? e[n] : t
}
const Oi = {
    created(e, t, n) {
        Ri(e, t, n, null, "created")
    },
    mounted(e, t, n) {
        Ri(e, t, n, null, "mounted")
    },
    beforeUpdate(e, t, n, r) {
        Ri(e, t, n, r, "beforeUpdate")
    },
    updated(e, t, n, r) {
        Ri(e, t, n, r, "updated")
    }
};
function Ai(e, t) {
    switch (e) {
    case "SELECT":
        return Si;
    case "TEXTAREA":
        return yi;
    default:
        switch (t) {
        case "checkbox":
            return _i;
        case "radio":
            return wi;
        default:
            return yi
        }
    }
}
function Ri(e, t, n, r, o) {
    const s = Ai(e.tagName, n.props && n.props.type)[o];
    s && s(e, t, n, r)
}
const Ti = ["ctrl", "shift", "alt", "meta"]
  , ki = {
    stop: e => e.stopPropagation(),
    prevent: e => e.preventDefault(),
    self: e => e.target !== e.currentTarget,
    ctrl: e => !e.ctrlKey,
    shift: e => !e.shiftKey,
    alt: e => !e.altKey,
    meta: e => !e.metaKey,
    left: e => "button"in e && 0 !== e.button,
    middle: e => "button"in e && 1 !== e.button,
    right: e => "button"in e && 2 !== e.button,
    exact: (e, t) => Ti.some((n => e[`${n}Key`] && !t.includes(n)))
}
  , Pi = (e, t) => (n, ...r) => {
    for (let e = 0; e < t.length; e++) {
        const r = ki[t[e]];
        if (r && r(n, t))
            return
    }
    return e(n, ...r)
}
  , Fi = {
    esc: "escape",
    space: " ",
    up: "arrow-up",
    left: "arrow-left",
    right: "arrow-right",
    down: "arrow-down",
    delete: "backspace"
}
  , ji = (e, t) => n => {
    if (!("key"in n))
        return;
    const r = z(n.key);
    return t.some((e => e === r || Fi[e] === r)) ? e(n) : void 0
}
  , Ni = {
    beforeMount(e, {value: t}, {transition: n}) {
        e._vod = "none" === e.style.display ? "" : e.style.display,
        n && t ? n.beforeEnter(e) : Mi(e, t)
    },
    mounted(e, {value: t}, {transition: n}) {
        n && t && n.enter(e)
    },
    updated(e, {value: t, oldValue: n}, {transition: r}) {
        !t != !n && (r ? t ? (r.beforeEnter(e),
        Mi(e, !0),
        r.enter(e)) : r.leave(e, ( () => {
            Mi(e, !1)
        }
        )) : Mi(e, t))
    },
    beforeUnmount(e, {value: t}) {
        Mi(e, t)
    }
};
function Mi(e, t) {
    e.style.display = t ? e._vod : "none"
}
const $i = S({
    patchProp: (e, t, n, r, o=!1, s, i, a, l) => {
        "class" === t ? function(e, t, n) {
            const r = e._vtc;
            r && (t = (t ? [t, ...r] : [...r]).join(" ")),
            null == t ? e.removeAttribute("class") : n ? e.setAttribute("class", t) : e.className = t
        }(e, r, o) : "style" === t ? function(e, t, n) {
            const r = e.style
              , o = P(n);
            if (n && !o) {
                for (const e in n)
                    Fs(r, e, n[e]);
                if (t && !P(t))
                    for (const e in t)
                        null == n[e] && Fs(r, e, "")
            } else {
                const s = r.display;
                o ? t !== n && (r.cssText = n) : t && e.removeAttribute("style"),
                "_vod"in e && (r.display = s)
            }
        }(e, n, r) : b(t) ? w(t) || Ds(e, t, 0, r, i) : ("." === t[0] ? (t = t.slice(1),
        1) : "^" === t[0] ? (t = t.slice(1),
        0) : function(e, t, n, r) {
            if (r)
                return "innerHTML" === t || "textContent" === t || !!(t in e && Is.test(t) && k(n));
            if ("spellcheck" === t || "draggable" === t || "translate" === t)
                return !1;
            if ("form" === t)
                return !1;
            if ("list" === t && "INPUT" === e.tagName)
                return !1;
            if ("type" === t && "TEXTAREA" === e.tagName)
                return !1;
            if (Is.test(t) && P(n))
                return !1;
            return t in e
        }(e, t, r, o)) ? function(e, t, n, r, o, s, i) {
            if ("innerHTML" === t || "textContent" === t)
                return r && i(r, o, s),
                void (e[t] = null == n ? "" : n);
            if ("value" === t && "PROGRESS" !== e.tagName && !e.tagName.includes("-")) {
                e._value = n;
                const r = null == n ? "" : n;
                return e.value === r && "OPTION" !== e.tagName || (e.value = r),
                void (null == n && e.removeAttribute(t))
            }
            let a = !1;
            if ("" === n || null == n) {
                const r = typeof e[t];
                "boolean" === r ? n = u(n) : null == n && "string" === r ? (n = "",
                a = !0) : "number" === r && (n = 0,
                a = !0)
            }
            try {
                e[t] = n
            } catch (l) {}
            a && e.removeAttribute(t)
        }(e, t, r, s, i, a, l) : ("true-value" === t ? e._trueValue = r : "false-value" === t && (e._falseValue = r),
        function(e, t, n, r, o) {
            if (r && t.startsWith("xlink:"))
                null == n ? e.removeAttributeNS(Ms, t.slice(6, t.length)) : e.setAttributeNS(Ms, t, n);
            else {
                const r = c(t);
                null == n || r && !u(n) ? e.removeAttribute(t) : e.setAttribute(t, r ? "" : n)
            }
        }(e, t, r, o))
    }
}, ks);
let Di, Li = !1;
function Bi() {
    return Di || (Di = go($i))
}
function Ui() {
    return Di = Li ? Di : vo($i),
    Li = !0,
    Di
}
const Ii = (...e) => {
    Bi().render(...e)
}
  , Vi = (...e) => {
    Ui().hydrate(...e)
}
  , Hi = (...e) => {
    const t = Bi().createApp(...e)
      , {mount: n} = t;
    return t.mount = e => {
        const r = zi(e);
        if (!r)
            return;
        const o = t._component;
        k(o) || o.render || o.template || (o.template = r.innerHTML),
        r.innerHTML = "";
        const s = n(r, !1, r instanceof SVGElement);
        return r instanceof Element && (r.removeAttribute("v-cloak"),
        r.setAttribute("data-v-app", "")),
        s
    }
    ,
    t
}
;
function zi(e) {
    if (P(e)) {
        return document.querySelector(e)
    }
    return e
}
let Wi = !1;
const Ki = Object.freeze(Object.defineProperty({
    __proto__: null,
    compile: () => {}
    ,
    EffectScope: Q,
    ReactiveEffect: pe,
    customRef: function(e) {
        return new jt(e)
    },
    effect: function(e, t) {
        e.effect && (e = e.effect.fn);
        const n = new pe(e);
        t && (S(n, t),
        t.scope && te(n, t.scope)),
        t && t.lazy || n.run();
        const r = n.run.bind(n);
        return r.effect = n,
        r
    },
    effectScope: ee,
    getCurrentScope: ne,
    isProxy: yt,
    isReactive: mt,
    isReadonly: gt,
    isRef: xt,
    isShallow: vt,
    markRaw: bt,
    onScopeDispose: re,
    proxyRefs: Ft,
    reactive: ft,
    readonly: pt,
    ref: Ot,
    shallowReactive: dt,
    shallowReadonly: function(e) {
        return ht(e, !0, De, st, ct)
    },
    shallowRef: At,
    stop: function(e) {
        e.effect.stop()
    },
    toRaw: _t,
    toRef: $t,
    toRefs: Nt,
    triggerRef: function(e) {
        Et(e)
    },
    unref: kt,
    camelize: V,
    capitalize: W,
    normalizeClass: a,
    normalizeProps: l,
    normalizeStyle: n,
    toDisplayString: p,
    toHandlerKey: K,
    BaseTransition: Vn,
    Comment: To,
    Fragment: Ao,
    KeepAlive: er,
    Static: ko,
    Suspense: En,
    Teleport: xo,
    Text: Ro,
    callWithAsyncErrorHandling: Ut,
    callWithErrorHandling: Bt,
    cloneVNode: Jo,
    compatUtils: null,
    computed: bs,
    createBlock: Bo,
    createCommentVNode: Zo,
    createElementBlock: Lo,
    createElementVNode: Wo,
    createHydrationRenderer: vo,
    createPropsRestProxy: function(e, t) {
        const n = {};
        for (const r in e)
            t.includes(r) || Object.defineProperty(n, r, {
                enumerable: !0,
                get: () => e[r]
            });
        return n
    },
    createRenderer: go,
    createSlots: Tr,
    createStaticVNode: Go,
    createTextVNode: Yo,
    createVNode: Ko,
    defineAsyncComponent: function(e) {
        k(e) && (e = {
            loader: e
        });
        const {loader: t, loadingComponent: n, errorComponent: r, delay: o=200, timeout: s, suspensible: i=!0, onError: a} = e;
        let l, c = null, u = 0;
        const f = () => {
            let e;
            return c || (e = c = t().catch((e => {
                if (e = e instanceof Error ? e : new Error(String(e)),
                a)
                    return new Promise(( (t, n) => {
                        a(e, ( () => t((u++,
                        c = null,
                        f()))), ( () => n(e)), u + 1)
                    }
                    ));
                throw e
            }
            )).then((t => e !== c && c ? c : (t && (t.__esModule || "Module" === t[Symbol.toStringTag]) && (t = t.default),
            l = t,
            t))))
        }
        ;
        return Yn({
            name: "AsyncComponentWrapper",
            __asyncLoader: f,
            get __asyncResolved() {
                return l
            },
            setup() {
                const e = is;
                if (l)
                    return () => Zn(l, e);
                const t = t => {
                    c = null,
                    It(t, e, 13, !r)
                }
                ;
                if (i && e.suspense || ps)
                    return f().then((t => () => Zn(t, e))).catch((e => (t(e),
                    () => r ? Ko(r, {
                        error: e
                    }) : null)));
                const a = Ot(!1)
                  , u = Ot()
                  , d = Ot(!!o);
                return o && setTimeout(( () => {
                    d.value = !1
                }
                ), o),
                null != s && setTimeout(( () => {
                    if (!a.value && !u.value) {
                        const e = new Error(`Async component timed out after ${s}ms.`);
                        t(e),
                        u.value = e
                    }
                }
                ), s),
                f().then(( () => {
                    a.value = !0,
                    e.parent && Xn(e.parent.vnode) && Xt(e.parent.update)
                }
                )).catch((e => {
                    t(e),
                    u.value = e
                }
                )),
                () => a.value && l ? Zn(l, e) : u.value && r ? Ko(r, {
                    error: u.value
                }) : n && !d.value ? Ko(n) : void 0
            }
        })
    },
    defineComponent: Yn,
    defineEmits: function() {
        return null
    },
    defineExpose: function(e) {},
    defineProps: function() {
        return null
    },
    get devtools() {
        return an
    },
    getCurrentInstance: as,
    getTransitionRawChildren: Jn,
    guardReactiveProps: qo,
    h: Ss,
    handleError: It,
    initCustomFormatter: function() {},
    inject: Pn,
    isMemoSame: xs,
    isRuntimeOnly: () => !fs,
    isVNode: Uo,
    mergeDefaults: function(e, t) {
        const n = O(e) ? e.reduce(( (e, t) => (e[t] = {},
        e)), {}) : e;
        for (const r in t) {
            const e = n[r];
            e ? O(e) || k(e) ? n[r] = {
                type: e,
                default: t[r]
            } : e.default = t[r] : null === e && (n[r] = {
                default: t[r]
            })
        }
        return n
    },
    mergeProps: ts,
    nextTick: Zt,
    onActivated: nr,
    onBeforeMount: ur,
    onBeforeUnmount: hr,
    onBeforeUpdate: dr,
    onDeactivated: rr,
    onErrorCaptured: _r,
    onMounted: fr,
    onRenderTracked: yr,
    onRenderTriggered: vr,
    onServerPrefetch: gr,
    onUnmounted: mr,
    onUpdated: pr,
    openBlock: jo,
    popScopeId: gn,
    provide: kn,
    pushScopeId: mn,
    queuePostFlushCb: en,
    registerRuntimeCompiler: function(e) {
        fs = e,
        ds = e => {
            e.render._rc && (e.withProxy = new Proxy(e.ctx,$r))
        }
    },
    renderList: Rr,
    renderSlot: kr,
    resolveComponent: Sr,
    resolveDirective: xr,
    resolveDynamicComponent: Er,
    resolveFilter: null,
    resolveTransitionHooks: zn,
    setBlockTracking: $o,
    setDevtoolsHook: function e(t, n) {
        var r, o;
        if (an = t,
        an)
            an.enabled = !0,
            ln.forEach(( ({event: e, args: t}) => an.emit(e, ...t))),
            ln = [];
        else if ("undefined" != typeof window && window.HTMLElement && !(null === (o = null === (r = window.navigator) || void 0 === r ? void 0 : r.userAgent) || void 0 === o ? void 0 : o.includes("jsdom"))) {
            (n.__VUE_DEVTOOLS_HOOK_REPLAY__ = n.__VUE_DEVTOOLS_HOOK_REPLAY__ || []).push((t => {
                e(t, n)
            }
            )),
            setTimeout(( () => {
                an || (n.__VUE_DEVTOOLS_HOOK_REPLAY__ = null,
                ln = [])
            }
            ), 3e3)
        } else
            ln = []
    },
    setTransitionHooks: qn,
    ssrContextKey: Cs,
    ssrUtils: As,
    toHandlers: function(e, t) {
        const n = {};
        for (const r in e)
            n[t && /[A-Z]/.test(r) ? `on:${r}` : K(r)] = e[r];
        return n
    },
    transformVNodeArgs: function(e) {},
    useAttrs: function() {
        return ws().attrs
    },
    useSSRContext: Es,
    useSlots: function() {
        return ws().slots
    },
    useTransitionState: Un,
    version: Os,
    warn: function(e, ...t) {},
    watch: Mn,
    watchEffect: Fn,
    watchPostEffect: jn,
    watchSyncEffect: function(e, t) {
        return $n(e, null, {
            flush: "sync"
        })
    },
    withAsyncContext: function(e) {
        const t = as();
        let n = e();
        return cs(),
        N(n) && (n = n.catch((e => {
            throw ls(t),
            e
        }
        ))),
        [n, () => ls(t)]
    },
    withCtx: vn,
    withDefaults: function(e, t) {
        return null
    },
    withDirectives: br,
    withMemo: function(e, t, n, r) {
        const o = n[r];
        if (o && xs(o, e))
            return o;
        const s = t();
        return s.memo = e.slice(),
        n[r] = s
    },
    withScopeId: e => vn,
    Transition: qs,
    TransitionGroup: fi,
    VueElement: zs,
    createApp: Hi,
    createSSRApp: (...e) => {
        const t = Ui().createApp(...e)
          , {mount: n} = t;
        return t.mount = e => {
            const t = zi(e);
            if (t)
                return n(t, !0, t instanceof SVGElement)
        }
        ,
        t
    }
    ,
    defineCustomElement: Vs,
    defineSSRCustomElement: e => Vs(e, Vi),
    hydrate: Vi,
    initDirectivesForSSR: () => {
        Wi || (Wi = !0,
        yi.getSSRProps = ({value: e}) => ({
            value: e
        }),
        wi.getSSRProps = ({value: e}, t) => {
            if (t.props && f(t.props.value, e))
                return {
                    checked: !0
                }
        }
        ,
        _i.getSSRProps = ({value: e}, t) => {
            if (O(e)) {
                if (t.props && d(e, t.props.value) > -1)
                    return {
                        checked: !0
                    }
            } else if (R(e)) {
                if (t.props && e.has(t.props.value))
                    return {
                        checked: !0
                    }
            } else if (e)
                return {
                    checked: !0
                }
        }
        ,
        Oi.getSSRProps = (e, t) => {
            if ("string" != typeof t.type)
                return;
            const n = Ai(t.type.toUpperCase(), t.props && t.props.type);
            return n.getSSRProps ? n.getSSRProps(e, t) : void 0
        }
        ,
        Ni.getSSRProps = ({value: e}) => {
            if (!e)
                return {
                    style: {
                        display: "none"
                    }
                }
        }
        )
    }
    ,
    render: Ii,
    useCssModule: function(e="$style") {
        {
            const t = as();
            if (!t)
                return m;
            const n = t.type.__cssModules;
            if (!n)
                return m;
            const r = n[e];
            return r || m
        }
    },
    useCssVars: function(e) {
        const t = as();
        if (!t)
            return;
        const n = t.ut = (n=e(t.proxy)) => {
            Array.from(document.querySelectorAll(`[data-v-owner="${t.uid}"]`)).forEach((e => Ks(e, n)))
        }
          , r = () => {
            const r = e(t.proxy);
            Ws(t.subTree, r),
            n(r)
        }
        ;
        jn(r),
        fr(( () => {
            const e = new MutationObserver(r);
            e.observe(t.subTree.el.parentNode, {
                childList: !0
            }),
            mr(( () => e.disconnect()))
        }
        ))
    },
    vModelCheckbox: _i,
    vModelDynamic: Oi,
    vModelRadio: wi,
    vModelSelect: Si,
    vModelText: yi,
    vShow: Ni,
    withKeys: ji,
    withModifiers: Pi
}, Symbol.toStringTag, {
    value: "Module"
}));
function qi(e, t) {
    return function() {
        return e.apply(t, arguments)
    }
}
const {toString: Ji} = Object.prototype
  , {getPrototypeOf: Yi} = Object
  , Gi = (Zi = Object.create(null),
e => {
    const t = Ji.call(e);
    return Zi[t] || (Zi[t] = t.slice(8, -1).toLowerCase())
}
);
var Zi;
const Xi = e => (e = e.toLowerCase(),
t => Gi(t) === e)
  , Qi = e => t => typeof t === e
  , {isArray: ea} = Array
  , ta = Qi("undefined");
const na = Xi("ArrayBuffer");
const ra = Qi("string")
  , oa = Qi("function")
  , sa = Qi("number")
  , ia = e => null !== e && "object" == typeof e
  , aa = e => {
    if ("object" !== Gi(e))
        return !1;
    const t = Yi(e);
    return !(null !== t && t !== Object.prototype && null !== Object.getPrototypeOf(t) || Symbol.toStringTag in e || Symbol.iterator in e)
}
  , la = Xi("Date")
  , ca = Xi("File")
  , ua = Xi("Blob")
  , fa = Xi("FileList")
  , da = Xi("URLSearchParams");
function pa(e, t, {allOwnKeys: n=!1}={}) {
    if (null == e)
        return;
    let r, o;
    if ("object" != typeof e && (e = [e]),
    ea(e))
        for (r = 0,
        o = e.length; r < o; r++)
            t.call(null, e[r], r, e);
    else {
        const o = n ? Object.getOwnPropertyNames(e) : Object.keys(e)
          , s = o.length;
        let i;
        for (r = 0; r < s; r++)
            i = o[r],
            t.call(null, e[i], i, e)
    }
}
function ha(e, t) {
    t = t.toLowerCase();
    const n = Object.keys(e);
    let r, o = n.length;
    for (; o-- > 0; )
        if (r = n[o],
        t === r.toLowerCase())
            return r;
    return null
}
const ma = "undefined" == typeof self ? "undefined" == typeof global ? globalThis : global : self
  , ga = e => !ta(e) && e !== ma;
const va = (ya = "undefined" != typeof Uint8Array && Yi(Uint8Array),
e => ya && e instanceof ya);
var ya;
const _a = Xi("HTMLFormElement")
  , ba = ( ({hasOwnProperty: e}) => (t, n) => e.call(t, n))(Object.prototype)
  , wa = Xi("RegExp")
  , Sa = (e, t) => {
    const n = Object.getOwnPropertyDescriptors(e)
      , r = {};
    pa(n, ( (n, o) => {
        !1 !== t(n, o, e) && (r[o] = n)
    }
    )),
    Object.defineProperties(e, r)
}
  , Ca = {
    isArray: ea,
    isArrayBuffer: na,
    isBuffer: function(e) {
        return null !== e && !ta(e) && null !== e.constructor && !ta(e.constructor) && oa(e.constructor.isBuffer) && e.constructor.isBuffer(e)
    },
    isFormData: e => {
        const t = "[object FormData]";
        return e && ("function" == typeof FormData && e instanceof FormData || Ji.call(e) === t || oa(e.toString) && e.toString() === t)
    }
    ,
    isArrayBufferView: function(e) {
        let t;
        return t = "undefined" != typeof ArrayBuffer && ArrayBuffer.isView ? ArrayBuffer.isView(e) : e && e.buffer && na(e.buffer),
        t
    },
    isString: ra,
    isNumber: sa,
    isBoolean: e => !0 === e || !1 === e,
    isObject: ia,
    isPlainObject: aa,
    isUndefined: ta,
    isDate: la,
    isFile: ca,
    isBlob: ua,
    isRegExp: wa,
    isFunction: oa,
    isStream: e => ia(e) && oa(e.pipe),
    isURLSearchParams: da,
    isTypedArray: va,
    isFileList: fa,
    forEach: pa,
    merge: function e() {
        const {caseless: t} = ga(this) && this || {}
          , n = {}
          , r = (r, o) => {
            const s = t && ha(n, o) || o;
            aa(n[s]) && aa(r) ? n[s] = e(n[s], r) : aa(r) ? n[s] = e({}, r) : ea(r) ? n[s] = r.slice() : n[s] = r
        }
        ;
        for (let o = 0, s = arguments.length; o < s; o++)
            arguments[o] && pa(arguments[o], r);
        return n
    },
    extend: (e, t, n, {allOwnKeys: r}={}) => (pa(t, ( (t, r) => {
        n && oa(t) ? e[r] = qi(t, n) : e[r] = t
    }
    ), {
        allOwnKeys: r
    }),
    e),
    trim: e => e.trim ? e.trim() : e.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, ""),
    stripBOM: e => (65279 === e.charCodeAt(0) && (e = e.slice(1)),
    e),
    inherits: (e, t, n, r) => {
        e.prototype = Object.create(t.prototype, r),
        e.prototype.constructor = e,
        Object.defineProperty(e, "super", {
            value: t.prototype
        }),
        n && Object.assign(e.prototype, n)
    }
    ,
    toFlatObject: (e, t, n, r) => {
        let o, s, i;
        const a = {};
        if (t = t || {},
        null == e)
            return t;
        do {
            for (o = Object.getOwnPropertyNames(e),
            s = o.length; s-- > 0; )
                i = o[s],
                r && !r(i, e, t) || a[i] || (t[i] = e[i],
                a[i] = !0);
            e = !1 !== n && Yi(e)
        } while (e && (!n || n(e, t)) && e !== Object.prototype);
        return t
    }
    ,
    kindOf: Gi,
    kindOfTest: Xi,
    endsWith: (e, t, n) => {
        e = String(e),
        (void 0 === n || n > e.length) && (n = e.length),
        n -= t.length;
        const r = e.indexOf(t, n);
        return -1 !== r && r === n
    }
    ,
    toArray: e => {
        if (!e)
            return null;
        if (ea(e))
            return e;
        let t = e.length;
        if (!sa(t))
            return null;
        const n = new Array(t);
        for (; t-- > 0; )
            n[t] = e[t];
        return n
    }
    ,
    forEachEntry: (e, t) => {
        const n = (e && e[Symbol.iterator]).call(e);
        let r;
        for (; (r = n.next()) && !r.done; ) {
            const n = r.value;
            t.call(e, n[0], n[1])
        }
    }
    ,
    matchAll: (e, t) => {
        let n;
        const r = [];
        for (; null !== (n = e.exec(t)); )
            r.push(n);
        return r
    }
    ,
    isHTMLForm: _a,
    hasOwnProperty: ba,
    hasOwnProp: ba,
    reduceDescriptors: Sa,
    freezeMethods: e => {
        Sa(e, ( (t, n) => {
            if (oa(e) && -1 !== ["arguments", "caller", "callee"].indexOf(n))
                return !1;
            const r = e[n];
            oa(r) && (t.enumerable = !1,
            "writable"in t ? t.writable = !1 : t.set || (t.set = () => {
                throw Error("Can not rewrite read-only method '" + n + "'")
            }
            ))
        }
        ))
    }
    ,
    toObjectSet: (e, t) => {
        const n = {}
          , r = e => {
            e.forEach((e => {
                n[e] = !0
            }
            ))
        }
        ;
        return ea(e) ? r(e) : r(String(e).split(t)),
        n
    }
    ,
    toCamelCase: e => e.toLowerCase().replace(/[_-\s]([a-z\d])(\w*)/g, (function(e, t, n) {
        return t.toUpperCase() + n
    }
    )),
    noop: () => {}
    ,
    toFiniteNumber: (e, t) => (e = +e,
    Number.isFinite(e) ? e : t),
    findKey: ha,
    global: ma,
    isContextDefined: ga,
    toJSONObject: e => {
        const t = new Array(10)
          , n = (e, r) => {
            if (ia(e)) {
                if (t.indexOf(e) >= 0)
                    return;
                if (!("toJSON"in e)) {
                    t[r] = e;
                    const o = ea(e) ? [] : {};
                    return pa(e, ( (e, t) => {
                        const s = n(e, r + 1);
                        !ta(s) && (o[t] = s)
                    }
                    )),
                    t[r] = void 0,
                    o
                }
            }
            return e
        }
        ;
        return n(e, 0)
    }
};
function Ea(e, t, n, r, o) {
    Error.call(this),
    Error.captureStackTrace ? Error.captureStackTrace(this, this.constructor) : this.stack = (new Error).stack,
    this.message = e,
    this.name = "AxiosError",
    t && (this.code = t),
    n && (this.config = n),
    r && (this.request = r),
    o && (this.response = o)
}
Ca.inherits(Ea, Error, {
    toJSON: function() {
        return {
            message: this.message,
            name: this.name,
            description: this.description,
            number: this.number,
            fileName: this.fileName,
            lineNumber: this.lineNumber,
            columnNumber: this.columnNumber,
            stack: this.stack,
            config: Ca.toJSONObject(this.config),
            code: this.code,
            status: this.response && this.response.status ? this.response.status : null
        }
    }
});
const xa = Ea.prototype
  , Oa = {};
["ERR_BAD_OPTION_VALUE", "ERR_BAD_OPTION", "ECONNABORTED", "ETIMEDOUT", "ERR_NETWORK", "ERR_FR_TOO_MANY_REDIRECTS", "ERR_DEPRECATED", "ERR_BAD_RESPONSE", "ERR_BAD_REQUEST", "ERR_CANCELED", "ERR_NOT_SUPPORT", "ERR_INVALID_URL"].forEach((e => {
    Oa[e] = {
        value: e
    }
}
)),
Object.defineProperties(Ea, Oa),
Object.defineProperty(xa, "isAxiosError", {
    value: !0
}),
Ea.from = (e, t, n, r, o, s) => {
    const i = Object.create(xa);
    return Ca.toFlatObject(e, i, (function(e) {
        return e !== Error.prototype
    }
    ), (e => "isAxiosError" !== e)),
    Ea.call(i, e.message, t, n, r, o),
    i.cause = e,
    i.name = e.name,
    s && Object.assign(i, s),
    i
}
;
var Aa = "undefined" != typeof globalThis ? globalThis : "undefined" != typeof window ? window : "undefined" != typeof global ? global : "undefined" != typeof self ? self : {};
function Ra(e) {
    return e && e.__esModule && Object.prototype.hasOwnProperty.call(e, "default") ? e.default : e
}
function Ta(e) {
    var t = e.default;
    if ("function" == typeof t) {
        var n = function() {
            return t.apply(this, arguments)
        };
        n.prototype = t.prototype
    } else
        n = {};
    return Object.defineProperty(n, "__esModule", {
        value: !0
    }),
    Object.keys(e).forEach((function(t) {
        var r = Object.getOwnPropertyDescriptor(e, t);
        Object.defineProperty(n, t, r.get ? r : {
            enumerable: !0,
            get: function() {
                return e[t]
            }
        })
    }
    )),
    n
}
const ka = "object" == typeof self ? self.FormData : window.FormData;
function Pa(e) {
    return Ca.isPlainObject(e) || Ca.isArray(e)
}
function Fa(e) {
    return Ca.endsWith(e, "[]") ? e.slice(0, -2) : e
}
function ja(e, t, n) {
    return e ? e.concat(t).map((function(e, t) {
        return e = Fa(e),
        !n && t ? "[" + e + "]" : e
    }
    )).join(n ? "." : "") : t
}
const Na = Ca.toFlatObject(Ca, {}, null, (function(e) {
    return /^is[A-Z]/.test(e)
}
));
function Ma(e, t, n) {
    if (!Ca.isObject(e))
        throw new TypeError("target must be an object");
    t = t || new (ka || FormData);
    const r = (n = Ca.toFlatObject(n, {
        metaTokens: !0,
        dots: !1,
        indexes: !1
    }, !1, (function(e, t) {
        return !Ca.isUndefined(t[e])
    }
    ))).metaTokens
      , o = n.visitor || u
      , s = n.dots
      , i = n.indexes
      , a = (n.Blob || "undefined" != typeof Blob && Blob) && ((l = t) && Ca.isFunction(l.append) && "FormData" === l[Symbol.toStringTag] && l[Symbol.iterator]);
    var l;
    if (!Ca.isFunction(o))
        throw new TypeError("visitor must be a function");
    function c(e) {
        if (null === e)
            return "";
        if (Ca.isDate(e))
            return e.toISOString();
        if (!a && Ca.isBlob(e))
            throw new Ea("Blob is not supported. Use a Buffer instead.");
        return Ca.isArrayBuffer(e) || Ca.isTypedArray(e) ? a && "function" == typeof Blob ? new Blob([e]) : Buffer.from(e) : e
    }
    function u(e, n, o) {
        let a = e;
        if (e && !o && "object" == typeof e)
            if (Ca.endsWith(n, "{}"))
                n = r ? n : n.slice(0, -2),
                e = JSON.stringify(e);
            else if (Ca.isArray(e) && function(e) {
                return Ca.isArray(e) && !e.some(Pa)
            }(e) || Ca.isFileList(e) || Ca.endsWith(n, "[]") && (a = Ca.toArray(e)))
                return n = Fa(n),
                a.forEach((function(e, r) {
                    !Ca.isUndefined(e) && null !== e && t.append(!0 === i ? ja([n], r, s) : null === i ? n : n + "[]", c(e))
                }
                )),
                !1;
        return !!Pa(e) || (t.append(ja(o, n, s), c(e)),
        !1)
    }
    const f = []
      , d = Object.assign(Na, {
        defaultVisitor: u,
        convertValue: c,
        isVisitable: Pa
    });
    if (!Ca.isObject(e))
        throw new TypeError("data must be an object");
    return function e(n, r) {
        if (!Ca.isUndefined(n)) {
            if (-1 !== f.indexOf(n))
                throw Error("Circular reference detected in " + r.join("."));
            f.push(n),
            Ca.forEach(n, (function(n, s) {
                !0 === (!(Ca.isUndefined(n) || null === n) && o.call(t, n, Ca.isString(s) ? s.trim() : s, r, d)) && e(n, r ? r.concat(s) : [s])
            }
            )),
            f.pop()
        }
    }(e),
    t
}
function $a(e) {
    const t = {
        "!": "%21",
        "'": "%27",
        "(": "%28",
        ")": "%29",
        "~": "%7E",
        "%20": "+",
        "%00": "\0"
    };
    return encodeURIComponent(e).replace(/[!'()~]|%20|%00/g, (function(e) {
        return t[e]
    }
    ))
}
function Da(e, t) {
    this._pairs = [],
    e && Ma(e, this, t)
}
const La = Da.prototype;
function Ba(e) {
    return encodeURIComponent(e).replace(/%3A/gi, ":").replace(/%24/g, "$").replace(/%2C/gi, ",").replace(/%20/g, "+").replace(/%5B/gi, "[").replace(/%5D/gi, "]")
}
function Ua(e, t, n) {
    if (!t)
        return e;
    const r = n && n.encode || Ba
      , o = n && n.serialize;
    let s;
    if (s = o ? o(t, n) : Ca.isURLSearchParams(t) ? t.toString() : new Da(t,n).toString(r),
    s) {
        const t = e.indexOf("#");
        -1 !== t && (e = e.slice(0, t)),
        e += (-1 === e.indexOf("?") ? "?" : "&") + s
    }
    return e
}
La.append = function(e, t) {
    this._pairs.push([e, t])
}
,
La.toString = function(e) {
    const t = e ? function(t) {
        return e.call(this, t, $a)
    }
    : $a;
    return this._pairs.map((function(e) {
        return t(e[0]) + "=" + t(e[1])
    }
    ), "").join("&")
}
;
const Ia = class {
    constructor() {
        this.handlers = []
    }
    use(e, t, n) {
        return this.handlers.push({
            fulfilled: e,
            rejected: t,
            synchronous: !!n && n.synchronous,
            runWhen: n ? n.runWhen : null
        }),
        this.handlers.length - 1
    }
    eject(e) {
        this.handlers[e] && (this.handlers[e] = null)
    }
    clear() {
        this.handlers && (this.handlers = [])
    }
    forEach(e) {
        Ca.forEach(this.handlers, (function(t) {
            null !== t && e(t)
        }
        ))
    }
}
  , Va = {
    silentJSONParsing: !0,
    forcedJSONParsing: !0,
    clarifyTimeoutError: !1
}
  , Ha = "undefined" != typeof URLSearchParams ? URLSearchParams : Da
  , za = FormData
  , Wa = ( () => {
    let e;
    return ("undefined" == typeof navigator || "ReactNative" !== (e = navigator.product) && "NativeScript" !== e && "NS" !== e) && ("undefined" != typeof window && "undefined" != typeof document)
}
)()
  , Ka = {
    isBrowser: !0,
    classes: {
        URLSearchParams: Ha,
        FormData: za,
        Blob: Blob
    },
    isStandardBrowserEnv: Wa,
    protocols: ["http", "https", "file", "blob", "url", "data"]
};
function qa(e) {
    function t(e, n, r, o) {
        let s = e[o++];
        const i = Number.isFinite(+s)
          , a = o >= e.length;
        if (s = !s && Ca.isArray(r) ? r.length : s,
        a)
            return Ca.hasOwnProp(r, s) ? r[s] = [r[s], n] : r[s] = n,
            !i;
        r[s] && Ca.isObject(r[s]) || (r[s] = []);
        return t(e, n, r[s], o) && Ca.isArray(r[s]) && (r[s] = function(e) {
            const t = {}
              , n = Object.keys(e);
            let r;
            const o = n.length;
            let s;
            for (r = 0; r < o; r++)
                s = n[r],
                t[s] = e[s];
            return t
        }(r[s])),
        !i
    }
    if (Ca.isFormData(e) && Ca.isFunction(e.entries)) {
        const n = {};
        return Ca.forEachEntry(e, ( (e, r) => {
            t(function(e) {
                return Ca.matchAll(/\w+|\[(\w*)]/g, e).map((e => "[]" === e[0] ? "" : e[1] || e[0]))
            }(e), r, n, 0)
        }
        )),
        n
    }
    return null
}
const Ja = {
    "Content-Type": void 0
};
const Ya = {
    transitional: Va,
    adapter: ["xhr", "http"],
    transformRequest: [function(e, t) {
        const n = t.getContentType() || ""
          , r = n.indexOf("application/json") > -1
          , o = Ca.isObject(e);
        o && Ca.isHTMLForm(e) && (e = new FormData(e));
        if (Ca.isFormData(e))
            return r && r ? JSON.stringify(qa(e)) : e;
        if (Ca.isArrayBuffer(e) || Ca.isBuffer(e) || Ca.isStream(e) || Ca.isFile(e) || Ca.isBlob(e))
            return e;
        if (Ca.isArrayBufferView(e))
            return e.buffer;
        if (Ca.isURLSearchParams(e))
            return t.setContentType("application/x-www-form-urlencoded;charset=utf-8", !1),
            e.toString();
        let s;
        if (o) {
            if (n.indexOf("application/x-www-form-urlencoded") > -1)
                return function(e, t) {
                    return Ma(e, new Ka.classes.URLSearchParams, Object.assign({
                        visitor: function(e, t, n, r) {
                            return Ka.isNode && Ca.isBuffer(e) ? (this.append(t, e.toString("base64")),
                            !1) : r.defaultVisitor.apply(this, arguments)
                        }
                    }, t))
                }(e, this.formSerializer).toString();
            if ((s = Ca.isFileList(e)) || n.indexOf("multipart/form-data") > -1) {
                const t = this.env && this.env.FormData;
                return Ma(s ? {
                    "files[]": e
                } : e, t && new t, this.formSerializer)
            }
        }
        return o || r ? (t.setContentType("application/json", !1),
        function(e, t, n) {
            if (Ca.isString(e))
                try {
                    return (t || JSON.parse)(e),
                    Ca.trim(e)
                } catch (r) {
                    if ("SyntaxError" !== r.name)
                        throw r
                }
            return (n || JSON.stringify)(e)
        }(e)) : e
    }
    ],
    transformResponse: [function(e) {
        const t = this.transitional || Ya.transitional
          , n = t && t.forcedJSONParsing
          , r = "json" === this.responseType;
        if (e && Ca.isString(e) && (n && !this.responseType || r)) {
            const n = !(t && t.silentJSONParsing) && r;
            try {
                return JSON.parse(e)
            } catch (o) {
                if (n) {
                    if ("SyntaxError" === o.name)
                        throw Ea.from(o, Ea.ERR_BAD_RESPONSE, this, null, this.response);
                    throw o
                }
            }
        }
        return e
    }
    ],
    timeout: 0,
    xsrfCookieName: "XSRF-TOKEN",
    xsrfHeaderName: "X-XSRF-TOKEN",
    maxContentLength: -1,
    maxBodyLength: -1,
    env: {
        FormData: Ka.classes.FormData,
        Blob: Ka.classes.Blob
    },
    validateStatus: function(e) {
        return e >= 200 && e < 300
    },
    headers: {
        common: {
            Accept: "application/json, text/plain, */*"
        }
    }
};
Ca.forEach(["delete", "get", "head"], (function(e) {
    Ya.headers[e] = {}
}
)),
Ca.forEach(["post", "put", "patch"], (function(e) {
    Ya.headers[e] = Ca.merge(Ja)
}
));
const Ga = Ya
  , Za = Ca.toObjectSet(["age", "authorization", "content-length", "content-type", "etag", "expires", "from", "host", "if-modified-since", "if-unmodified-since", "last-modified", "location", "max-forwards", "proxy-authorization", "referer", "retry-after", "user-agent"])
  , Xa = Symbol("internals");
function Qa(e) {
    return e && String(e).trim().toLowerCase()
}
function el(e) {
    return !1 === e || null == e ? e : Ca.isArray(e) ? e.map(el) : String(e)
}
function tl(e, t, n, r) {
    return Ca.isFunction(r) ? r.call(this, t, n) : Ca.isString(t) ? Ca.isString(r) ? -1 !== t.indexOf(r) : Ca.isRegExp(r) ? r.test(t) : void 0 : void 0
}
class nl {
    constructor(e) {
        e && this.set(e)
    }
    set(e, t, n) {
        const r = this;
        function o(e, t, n) {
            const o = Qa(t);
            if (!o)
                throw new Error("header name must be a non-empty string");
            const s = Ca.findKey(r, o);
            (!s || void 0 === r[s] || !0 === n || void 0 === n && !1 !== r[s]) && (r[s || t] = el(e))
        }
        const s = (e, t) => Ca.forEach(e, ( (e, n) => o(e, n, t)));
        return Ca.isPlainObject(e) || e instanceof this.constructor ? s(e, t) : Ca.isString(e) && (e = e.trim()) && !/^[-_a-zA-Z]+$/.test(e.trim()) ? s((e => {
            const t = {};
            let n, r, o;
            return e && e.split("\n").forEach((function(e) {
                o = e.indexOf(":"),
                n = e.substring(0, o).trim().toLowerCase(),
                r = e.substring(o + 1).trim(),
                !n || t[n] && Za[n] || ("set-cookie" === n ? t[n] ? t[n].push(r) : t[n] = [r] : t[n] = t[n] ? t[n] + ", " + r : r)
            }
            )),
            t
        }
        )(e), t) : null != e && o(t, e, n),
        this
    }
    get(e, t) {
        if (e = Qa(e)) {
            const n = Ca.findKey(this, e);
            if (n) {
                const e = this[n];
                if (!t)
                    return e;
                if (!0 === t)
                    return function(e) {
                        const t = Object.create(null)
                          , n = /([^\s,;=]+)\s*(?:=\s*([^,;]+))?/g;
                        let r;
                        for (; r = n.exec(e); )
                            t[r[1]] = r[2];
                        return t
                    }(e);
                if (Ca.isFunction(t))
                    return t.call(this, e, n);
                if (Ca.isRegExp(t))
                    return t.exec(e);
                throw new TypeError("parser must be boolean|regexp|function")
            }
        }
    }
    has(e, t) {
        if (e = Qa(e)) {
            const n = Ca.findKey(this, e);
            return !(!n || t && !tl(0, this[n], n, t))
        }
        return !1
    }
    delete(e, t) {
        const n = this;
        let r = !1;
        function o(e) {
            if (e = Qa(e)) {
                const o = Ca.findKey(n, e);
                !o || t && !tl(0, n[o], o, t) || (delete n[o],
                r = !0)
            }
        }
        return Ca.isArray(e) ? e.forEach(o) : o(e),
        r
    }
    clear() {
        return Object.keys(this).forEach(this.delete.bind(this))
    }
    normalize(e) {
        const t = this
          , n = {};
        return Ca.forEach(this, ( (r, o) => {
            const s = Ca.findKey(n, o);
            if (s)
                return t[s] = el(r),
                void delete t[o];
            const i = e ? function(e) {
                return e.trim().toLowerCase().replace(/([a-z\d])(\w*)/g, ( (e, t, n) => t.toUpperCase() + n))
            }(o) : String(o).trim();
            i !== o && delete t[o],
            t[i] = el(r),
            n[i] = !0
        }
        )),
        this
    }
    concat(...e) {
        return this.constructor.concat(this, ...e)
    }
    toJSON(e) {
        const t = Object.create(null);
        return Ca.forEach(this, ( (n, r) => {
            null != n && !1 !== n && (t[r] = e && Ca.isArray(n) ? n.join(", ") : n)
        }
        )),
        t
    }
    [Symbol.iterator]() {
        return Object.entries(this.toJSON())[Symbol.iterator]()
    }
    toString() {
        return Object.entries(this.toJSON()).map(( ([e,t]) => e + ": " + t)).join("\n")
    }
    get[Symbol.toStringTag]() {
        return "AxiosHeaders"
    }
    static from(e) {
        return e instanceof this ? e : new this(e)
    }
    static concat(e, ...t) {
        const n = new this(e);
        return t.forEach((e => n.set(e))),
        n
    }
    static accessor(e) {
        const t = (this[Xa] = this[Xa] = {
            accessors: {}
        }).accessors
          , n = this.prototype;
        function r(e) {
            const r = Qa(e);
            t[r] || (!function(e, t) {
                const n = Ca.toCamelCase(" " + t);
                ["get", "set", "has"].forEach((r => {
                    Object.defineProperty(e, r + n, {
                        value: function(e, n, o) {
                            return this[r].call(this, t, e, n, o)
                        },
                        configurable: !0
                    })
                }
                ))
            }(n, e),
            t[r] = !0)
        }
        return Ca.isArray(e) ? e.forEach(r) : r(e),
        this
    }
}
nl.accessor(["Content-Type", "Content-Length", "Accept", "Accept-Encoding", "User-Agent"]),
Ca.freezeMethods(nl.prototype),
Ca.freezeMethods(nl);
const rl = nl;
function ol(e, t) {
    const n = this || Ga
      , r = t || n
      , o = rl.from(r.headers);
    let s = r.data;
    return Ca.forEach(e, (function(e) {
        s = e.call(n, s, o.normalize(), t ? t.status : void 0)
    }
    )),
    o.normalize(),
    s
}
function sl(e) {
    return !(!e || !e.__CANCEL__)
}
function il(e, t, n) {
    Ea.call(this, null == e ? "canceled" : e, Ea.ERR_CANCELED, t, n),
    this.name = "CanceledError"
}
Ca.inherits(il, Ea, {
    __CANCEL__: !0
});
const al = Ka.isStandardBrowserEnv ? {
    write: function(e, t, n, r, o, s) {
        const i = [];
        i.push(e + "=" + encodeURIComponent(t)),
        Ca.isNumber(n) && i.push("expires=" + new Date(n).toGMTString()),
        Ca.isString(r) && i.push("path=" + r),
        Ca.isString(o) && i.push("domain=" + o),
        !0 === s && i.push("secure"),
        document.cookie = i.join("; ")
    },
    read: function(e) {
        const t = document.cookie.match(new RegExp("(^|;\\s*)(" + e + ")=([^;]*)"));
        return t ? decodeURIComponent(t[3]) : null
    },
    remove: function(e) {
        this.write(e, "", Date.now() - 864e5)
    }
} : {
    write: function() {},
    read: function() {
        return null
    },
    remove: function() {}
};
function ll(e, t) {
    return e && !/^([a-z][a-z\d+\-.]*:)?\/\//i.test(t) ? function(e, t) {
        return t ? e.replace(/\/+$/, "") + "/" + t.replace(/^\/+/, "") : e
    }(e, t) : t
}
const cl = Ka.isStandardBrowserEnv ? function() {
    const e = /(msie|trident)/i.test(navigator.userAgent)
      , t = document.createElement("a");
    let n;
    function r(n) {
        let r = n;
        return e && (t.setAttribute("href", r),
        r = t.href),
        t.setAttribute("href", r),
        {
            href: t.href,
            protocol: t.protocol ? t.protocol.replace(/:$/, "") : "",
            host: t.host,
            search: t.search ? t.search.replace(/^\?/, "") : "",
            hash: t.hash ? t.hash.replace(/^#/, "") : "",
            hostname: t.hostname,
            port: t.port,
            pathname: "/" === t.pathname.charAt(0) ? t.pathname : "/" + t.pathname
        }
    }
    return n = r(window.location.href),
    function(e) {
        const t = Ca.isString(e) ? r(e) : e;
        return t.protocol === n.protocol && t.host === n.host
    }
}() : function() {
    return !0
}
;
function ul(e, t) {
    let n = 0;
    const r = function(e, t) {
        e = e || 10;
        const n = new Array(e)
          , r = new Array(e);
        let o, s = 0, i = 0;
        return t = void 0 !== t ? t : 1e3,
        function(a) {
            const l = Date.now()
              , c = r[i];
            o || (o = l),
            n[s] = a,
            r[s] = l;
            let u = i
              , f = 0;
            for (; u !== s; )
                f += n[u++],
                u %= e;
            if (s = (s + 1) % e,
            s === i && (i = (i + 1) % e),
            l - o < t)
                return;
            const d = c && l - c;
            return d ? Math.round(1e3 * f / d) : void 0
        }
    }(50, 250);
    return o => {
        const s = o.loaded
          , i = o.lengthComputable ? o.total : void 0
          , a = s - n
          , l = r(a);
        n = s;
        const c = {
            loaded: s,
            total: i,
            progress: i ? s / i : void 0,
            bytes: a,
            rate: l || void 0,
            estimated: l && i && s <= i ? (i - s) / l : void 0,
            event: o
        };
        c[t ? "download" : "upload"] = !0,
        e(c)
    }
}
const fl = {
    http: null,
    xhr: "undefined" != typeof XMLHttpRequest && function(e) {
        return new Promise((function(t, n) {
            let r = e.data;
            const o = rl.from(e.headers).normalize()
              , s = e.responseType;
            let i;
            function a() {
                e.cancelToken && e.cancelToken.unsubscribe(i),
                e.signal && e.signal.removeEventListener("abort", i)
            }
            Ca.isFormData(r) && Ka.isStandardBrowserEnv && o.setContentType(!1);
            let l = new XMLHttpRequest;
            if (e.auth) {
                const t = e.auth.username || ""
                  , n = e.auth.password ? unescape(encodeURIComponent(e.auth.password)) : "";
                o.set("Authorization", "Basic " + btoa(t + ":" + n))
            }
            const c = ll(e.baseURL, e.url);
            function u() {
                if (!l)
                    return;
                const r = rl.from("getAllResponseHeaders"in l && l.getAllResponseHeaders());
                !function(e, t, n) {
                    const r = n.config.validateStatus;
                    n.status && r && !r(n.status) ? t(new Ea("Request failed with status code " + n.status,[Ea.ERR_BAD_REQUEST, Ea.ERR_BAD_RESPONSE][Math.floor(n.status / 100) - 4],n.config,n.request,n)) : e(n)
                }((function(e) {
                    t(e),
                    a()
                }
                ), (function(e) {
                    n(e),
                    a()
                }
                ), {
                    data: s && "text" !== s && "json" !== s ? l.response : l.responseText,
                    status: l.status,
                    statusText: l.statusText,
                    headers: r,
                    config: e,
                    request: l
                }),
                l = null
            }
            if (l.open(e.method.toUpperCase(), Ua(c, e.params, e.paramsSerializer), !0),
            l.timeout = e.timeout,
            "onloadend"in l ? l.onloadend = u : l.onreadystatechange = function() {
                l && 4 === l.readyState && (0 !== l.status || l.responseURL && 0 === l.responseURL.indexOf("file:")) && setTimeout(u)
            }
            ,
            l.onabort = function() {
                l && (n(new Ea("Request aborted",Ea.ECONNABORTED,e,l)),
                l = null)
            }
            ,
            l.onerror = function() {
                n(new Ea("Network Error",Ea.ERR_NETWORK,e,l)),
                l = null
            }
            ,
            l.ontimeout = function() {
                let t = e.timeout ? "timeout of " + e.timeout + "ms exceeded" : "timeout exceeded";
                const r = e.transitional || Va;
                e.timeoutErrorMessage && (t = e.timeoutErrorMessage),
                n(new Ea(t,r.clarifyTimeoutError ? Ea.ETIMEDOUT : Ea.ECONNABORTED,e,l)),
                l = null
            }
            ,
            Ka.isStandardBrowserEnv) {
                const t = (e.withCredentials || cl(c)) && e.xsrfCookieName && al.read(e.xsrfCookieName);
                t && o.set(e.xsrfHeaderName, t)
            }
            void 0 === r && o.setContentType(null),
            "setRequestHeader"in l && Ca.forEach(o.toJSON(), (function(e, t) {
                l.setRequestHeader(t, e)
            }
            )),
            Ca.isUndefined(e.withCredentials) || (l.withCredentials = !!e.withCredentials),
            s && "json" !== s && (l.responseType = e.responseType),
            "function" == typeof e.onDownloadProgress && l.addEventListener("progress", ul(e.onDownloadProgress, !0)),
            "function" == typeof e.onUploadProgress && l.upload && l.upload.addEventListener("progress", ul(e.onUploadProgress)),
            (e.cancelToken || e.signal) && (i = t => {
                l && (n(!t || t.type ? new il(null,e,l) : t),
                l.abort(),
                l = null)
            }
            ,
            e.cancelToken && e.cancelToken.subscribe(i),
            e.signal && (e.signal.aborted ? i() : e.signal.addEventListener("abort", i)));
            const f = function(e) {
                const t = /^([-+\w]{1,25})(:?\/\/|:)/.exec(e);
                return t && t[1] || ""
            }(c);
            f && -1 === Ka.protocols.indexOf(f) ? n(new Ea("Unsupported protocol " + f + ":",Ea.ERR_BAD_REQUEST,e)) : l.send(r || null)
        }
        ))
    }
};
Ca.forEach(fl, ( (e, t) => {
    if (e) {
        try {
            Object.defineProperty(e, "name", {
                value: t
            })
        } catch (n) {}
        Object.defineProperty(e, "adapterName", {
            value: t
        })
    }
}
));
const dl = e => {
    e = Ca.isArray(e) ? e : [e];
    const {length: t} = e;
    let n, r;
    for (let o = 0; o < t && (n = e[o],
    !(r = Ca.isString(n) ? fl[n.toLowerCase()] : n)); o++)
        ;
    if (!r) {
        if (!1 === r)
            throw new Ea(`Adapter ${n} is not supported by the environment`,"ERR_NOT_SUPPORT");
        throw new Error(Ca.hasOwnProp(fl, n) ? `Adapter '${n}' is not available in the build` : `Unknown adapter '${n}'`)
    }
    if (!Ca.isFunction(r))
        throw new TypeError("adapter is not a function");
    return r
}
;
function pl(e) {
    if (e.cancelToken && e.cancelToken.throwIfRequested(),
    e.signal && e.signal.aborted)
        throw new il
}
function hl(e) {
    pl(e),
    e.headers = rl.from(e.headers),
    e.data = ol.call(e, e.transformRequest),
    -1 !== ["post", "put", "patch"].indexOf(e.method) && e.headers.setContentType("application/x-www-form-urlencoded", !1);
    return dl(e.adapter || Ga.adapter)(e).then((function(t) {
        return pl(e),
        t.data = ol.call(e, e.transformResponse, t),
        t.headers = rl.from(t.headers),
        t
    }
    ), (function(t) {
        return sl(t) || (pl(e),
        t && t.response && (t.response.data = ol.call(e, e.transformResponse, t.response),
        t.response.headers = rl.from(t.response.headers))),
        Promise.reject(t)
    }
    ))
}
const ml = e => e instanceof rl ? e.toJSON() : e;
function gl(e, t) {
    t = t || {};
    const n = {};
    function r(e, t, n) {
        return Ca.isPlainObject(e) && Ca.isPlainObject(t) ? Ca.merge.call({
            caseless: n
        }, e, t) : Ca.isPlainObject(t) ? Ca.merge({}, t) : Ca.isArray(t) ? t.slice() : t
    }
    function o(e, t, n) {
        return Ca.isUndefined(t) ? Ca.isUndefined(e) ? void 0 : r(void 0, e, n) : r(e, t, n)
    }
    function s(e, t) {
        if (!Ca.isUndefined(t))
            return r(void 0, t)
    }
    function i(e, t) {
        return Ca.isUndefined(t) ? Ca.isUndefined(e) ? void 0 : r(void 0, e) : r(void 0, t)
    }
    function a(n, o, s) {
        return s in t ? r(n, o) : s in e ? r(void 0, n) : void 0
    }
    const l = {
        url: s,
        method: s,
        data: s,
        baseURL: i,
        transformRequest: i,
        transformResponse: i,
        paramsSerializer: i,
        timeout: i,
        timeoutMessage: i,
        withCredentials: i,
        adapter: i,
        responseType: i,
        xsrfCookieName: i,
        xsrfHeaderName: i,
        onUploadProgress: i,
        onDownloadProgress: i,
        decompress: i,
        maxContentLength: i,
        maxBodyLength: i,
        beforeRedirect: i,
        transport: i,
        httpAgent: i,
        httpsAgent: i,
        cancelToken: i,
        socketPath: i,
        responseEncoding: i,
        validateStatus: a,
        headers: (e, t) => o(ml(e), ml(t), !0)
    };
    return Ca.forEach(Object.keys(e).concat(Object.keys(t)), (function(r) {
        const s = l[r] || o
          , i = s(e[r], t[r], r);
        Ca.isUndefined(i) && s !== a || (n[r] = i)
    }
    )),
    n
}
const vl = {};
["object", "boolean", "number", "function", "string", "symbol"].forEach(( (e, t) => {
    vl[e] = function(n) {
        return typeof n === e || "a" + (t < 1 ? "n " : " ") + e
    }
}
));
const yl = {};
vl.transitional = function(e, t, n) {
    return (r, o, s) => {
        if (!1 === e)
            throw new Ea(function(e, t) {
                return "[Axios v1.2.0] Transitional option '" + e + "'" + t + (n ? ". " + n : "")
            }(o, " has been removed" + (t ? " in " + t : "")),Ea.ERR_DEPRECATED);
        return t && !yl[o] && (yl[o] = !0),
        !e || e(r, o, s)
    }
}
;
const _l = {
    assertOptions: function(e, t, n) {
        if ("object" != typeof e)
            throw new Ea("options must be an object",Ea.ERR_BAD_OPTION_VALUE);
        const r = Object.keys(e);
        let o = r.length;
        for (; o-- > 0; ) {
            const s = r[o]
              , i = t[s];
            if (i) {
                const t = e[s]
                  , n = void 0 === t || i(t, s, e);
                if (!0 !== n)
                    throw new Ea("option " + s + " must be " + n,Ea.ERR_BAD_OPTION_VALUE)
            } else if (!0 !== n)
                throw new Ea("Unknown option " + s,Ea.ERR_BAD_OPTION)
        }
    },
    validators: vl
}
  , bl = _l.validators;
class wl {
    constructor(e) {
        this.defaults = e,
        this.interceptors = {
            request: new Ia,
            response: new Ia
        }
    }
    request(e, t) {
        "string" == typeof e ? (t = t || {}).url = e : t = e || {},
        t = gl(this.defaults, t);
        const {transitional: n, paramsSerializer: r, headers: o} = t;
        let s;
        void 0 !== n && _l.assertOptions(n, {
            silentJSONParsing: bl.transitional(bl.boolean),
            forcedJSONParsing: bl.transitional(bl.boolean),
            clarifyTimeoutError: bl.transitional(bl.boolean)
        }, !1),
        void 0 !== r && _l.assertOptions(r, {
            encode: bl.function,
            serialize: bl.function
        }, !0),
        t.method = (t.method || this.defaults.method || "get").toLowerCase(),
        s = o && Ca.merge(o.common, o[t.method]),
        s && Ca.forEach(["delete", "get", "head", "post", "put", "patch", "common"], (e => {
            delete o[e]
        }
        )),
        t.headers = rl.concat(s, o);
        const i = [];
        let a = !0;
        this.interceptors.request.forEach((function(e) {
            "function" == typeof e.runWhen && !1 === e.runWhen(t) || (a = a && e.synchronous,
            i.unshift(e.fulfilled, e.rejected))
        }
        ));
        const l = [];
        let c;
        this.interceptors.response.forEach((function(e) {
            l.push(e.fulfilled, e.rejected)
        }
        ));
        let u, f = 0;
        if (!a) {
            const e = [hl.bind(this), void 0];
            for (e.unshift.apply(e, i),
            e.push.apply(e, l),
            u = e.length,
            c = Promise.resolve(t); f < u; )
                c = c.then(e[f++], e[f++]);
            return c
        }
        u = i.length;
        let d = t;
        for (f = 0; f < u; ) {
            const e = i[f++]
              , t = i[f++];
            try {
                d = e(d)
            } catch (p) {
                t.call(this, p);
                break
            }
        }
        try {
            c = hl.call(this, d)
        } catch (p) {
            return Promise.reject(p)
        }
        for (f = 0,
        u = l.length; f < u; )
            c = c.then(l[f++], l[f++]);
        return c
    }
    getUri(e) {
        return Ua(ll((e = gl(this.defaults, e)).baseURL, e.url), e.params, e.paramsSerializer)
    }
}
Ca.forEach(["delete", "get", "head", "options"], (function(e) {
    wl.prototype[e] = function(t, n) {
        return this.request(gl(n || {}, {
            method: e,
            url: t,
            data: (n || {}).data
        }))
    }
}
)),
Ca.forEach(["post", "put", "patch"], (function(e) {
    function t(t) {
        return function(n, r, o) {
            return this.request(gl(o || {}, {
                method: e,
                headers: t ? {
                    "Content-Type": "multipart/form-data"
                } : {},
                url: n,
                data: r
            }))
        }
    }
    wl.prototype[e] = t(),
    wl.prototype[e + "Form"] = t(!0)
}
));
const Sl = wl;
class Cl {
    constructor(e) {
        if ("function" != typeof e)
            throw new TypeError("executor must be a function.");
        let t;
        this.promise = new Promise((function(e) {
            t = e
        }
        ));
        const n = this;
        this.promise.then((e => {
            if (!n._listeners)
                return;
            let t = n._listeners.length;
            for (; t-- > 0; )
                n._listeners[t](e);
            n._listeners = null
        }
        )),
        this.promise.then = e => {
            let t;
            const r = new Promise((e => {
                n.subscribe(e),
                t = e
            }
            )).then(e);
            return r.cancel = function() {
                n.unsubscribe(t)
            }
            ,
            r
        }
        ,
        e((function(e, r, o) {
            n.reason || (n.reason = new il(e,r,o),
            t(n.reason))
        }
        ))
    }
    throwIfRequested() {
        if (this.reason)
            throw this.reason
    }
    subscribe(e) {
        this.reason ? e(this.reason) : this._listeners ? this._listeners.push(e) : this._listeners = [e]
    }
    unsubscribe(e) {
        if (!this._listeners)
            return;
        const t = this._listeners.indexOf(e);
        -1 !== t && this._listeners.splice(t, 1)
    }
    static source() {
        let e;
        return {
            token: new Cl((function(t) {
                e = t
            }
            )),
            cancel: e
        }
    }
}
const El = Cl;
const xl = function e(t) {
    const n = new Sl(t)
      , r = qi(Sl.prototype.request, n);
    return Ca.extend(r, Sl.prototype, n, {
        allOwnKeys: !0
    }),
    Ca.extend(r, n, null, {
        allOwnKeys: !0
    }),
    r.create = function(n) {
        return e(gl(t, n))
    }
    ,
    r
}(Ga);
xl.Axios = Sl,
xl.CanceledError = il,
xl.CancelToken = El,
xl.isCancel = sl,
xl.VERSION = "1.2.0",
xl.toFormData = Ma,
xl.AxiosError = Ea,
xl.Cancel = xl.CanceledError,
xl.all = function(e) {
    return Promise.all(e)
}
,
xl.spread = function(e) {
    return function(t) {
        return e.apply(null, t)
    }
}
,
xl.isAxiosError = function(e) {
    return Ca.isObject(e) && !0 === e.isAxiosError
}
,
xl.AxiosHeaders = rl,
xl.formToJSON = e => qa(Ca.isHTMLForm(e) ? new FormData(e) : e),
xl.default = xl;
const Ol = xl;
var Al = {
    exports: {}
};
const Rl = Al.exports = function() {
    var e = 1e3
      , t = 6e4
      , n = 36e5
      , r = "millisecond"
      , o = "second"
      , s = "minute"
      , i = "hour"
      , a = "day"
      , l = "week"
      , c = "month"
      , u = "quarter"
      , f = "year"
      , d = "date"
      , p = "Invalid Date"
      , h = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/
      , m = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g
      , g = {
        name: "en",
        weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),
        months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_"),
        ordinal: function(e) {
            var t = ["th", "st", "nd", "rd"]
              , n = e % 100;
            return "[" + e + (t[(n - 20) % 10] || t[n] || t[0]) + "]"
        }
    }
      , v = function(e, t, n) {
        var r = String(e);
        return !r || r.length >= t ? e : "" + Array(t + 1 - r.length).join(n) + e
    }
      , y = {
        s: v,
        z: function(e) {
            var t = -e.utcOffset()
              , n = Math.abs(t)
              , r = Math.floor(n / 60)
              , o = n % 60;
            return (t <= 0 ? "+" : "-") + v(r, 2, "0") + ":" + v(o, 2, "0")
        },
        m: function e(t, n) {
            if (t.date() < n.date())
                return -e(n, t);
            var r = 12 * (n.year() - t.year()) + (n.month() - t.month())
              , o = t.clone().add(r, c)
              , s = n - o < 0
              , i = t.clone().add(r + (s ? -1 : 1), c);
            return +(-(r + (n - o) / (s ? o - i : i - o)) || 0)
        },
        a: function(e) {
            return e < 0 ? Math.ceil(e) || 0 : Math.floor(e)
        },
        p: function(e) {
            return {
                M: c,
                y: f,
                w: l,
                d: a,
                D: d,
                h: i,
                m: s,
                s: o,
                ms: r,
                Q: u
            }[e] || String(e || "").toLowerCase().replace(/s$/, "")
        },
        u: function(e) {
            return void 0 === e
        }
    }
      , _ = "en"
      , b = {};
    b[_] = g;
    var w = function(e) {
        return e instanceof x
    }
      , S = function e(t, n, r) {
        var o;
        if (!t)
            return _;
        if ("string" == typeof t) {
            var s = t.toLowerCase();
            b[s] && (o = s),
            n && (b[s] = n,
            o = s);
            var i = t.split("-");
            if (!o && i.length > 1)
                return e(i[0])
        } else {
            var a = t.name;
            b[a] = t,
            o = a
        }
        return !r && o && (_ = o),
        o || !r && _
    }
      , C = function(e, t) {
        if (w(e))
            return e.clone();
        var n = "object" == typeof t ? t : {};
        return n.date = e,
        n.args = arguments,
        new x(n)
    }
      , E = y;
    E.l = S,
    E.i = w,
    E.w = function(e, t) {
        return C(e, {
            locale: t.$L,
            utc: t.$u,
            x: t.$x,
            $offset: t.$offset
        })
    }
    ;
    var x = function() {
        function g(e) {
            this.$L = S(e.locale, null, !0),
            this.parse(e)
        }
        var v = g.prototype;
        return v.parse = function(e) {
            this.$d = function(e) {
                var t = e.date
                  , n = e.utc;
                if (null === t)
                    return new Date(NaN);
                if (E.u(t))
                    return new Date;
                if (t instanceof Date)
                    return new Date(t);
                if ("string" == typeof t && !/Z$/i.test(t)) {
                    var r = t.match(h);
                    if (r) {
                        var o = r[2] - 1 || 0
                          , s = (r[7] || "0").substring(0, 3);
                        return n ? new Date(Date.UTC(r[1], o, r[3] || 1, r[4] || 0, r[5] || 0, r[6] || 0, s)) : new Date(r[1],o,r[3] || 1,r[4] || 0,r[5] || 0,r[6] || 0,s)
                    }
                }
                return new Date(t)
            }(e),
            this.$x = e.x || {},
            this.init()
        }
        ,
        v.init = function() {
            var e = this.$d;
            this.$y = e.getFullYear(),
            this.$M = e.getMonth(),
            this.$D = e.getDate(),
            this.$W = e.getDay(),
            this.$H = e.getHours(),
            this.$m = e.getMinutes(),
            this.$s = e.getSeconds(),
            this.$ms = e.getMilliseconds()
        }
        ,
        v.$utils = function() {
            return E
        }
        ,
        v.isValid = function() {
            return !(this.$d.toString() === p)
        }
        ,
        v.isSame = function(e, t) {
            var n = C(e);
            return this.startOf(t) <= n && n <= this.endOf(t)
        }
        ,
        v.isAfter = function(e, t) {
            return C(e) < this.startOf(t)
        }
        ,
        v.isBefore = function(e, t) {
            return this.endOf(t) < C(e)
        }
        ,
        v.$g = function(e, t, n) {
            return E.u(e) ? this[t] : this.set(n, e)
        }
        ,
        v.unix = function() {
            return Math.floor(this.valueOf() / 1e3)
        }
        ,
        v.valueOf = function() {
            return this.$d.getTime()
        }
        ,
        v.startOf = function(e, t) {
            var n = this
              , r = !!E.u(t) || t
              , u = E.p(e)
              , p = function(e, t) {
                var o = E.w(n.$u ? Date.UTC(n.$y, t, e) : new Date(n.$y,t,e), n);
                return r ? o : o.endOf(a)
            }
              , h = function(e, t) {
                return E.w(n.toDate()[e].apply(n.toDate("s"), (r ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(t)), n)
            }
              , m = this.$W
              , g = this.$M
              , v = this.$D
              , y = "set" + (this.$u ? "UTC" : "");
            switch (u) {
            case f:
                return r ? p(1, 0) : p(31, 11);
            case c:
                return r ? p(1, g) : p(0, g + 1);
            case l:
                var _ = this.$locale().weekStart || 0
                  , b = (m < _ ? m + 7 : m) - _;
                return p(r ? v - b : v + (6 - b), g);
            case a:
            case d:
                return h(y + "Hours", 0);
            case i:
                return h(y + "Minutes", 1);
            case s:
                return h(y + "Seconds", 2);
            case o:
                return h(y + "Milliseconds", 3);
            default:
                return this.clone()
            }
        }
        ,
        v.endOf = function(e) {
            return this.startOf(e, !1)
        }
        ,
        v.$set = function(e, t) {
            var n, l = E.p(e), u = "set" + (this.$u ? "UTC" : ""), p = (n = {},
            n[a] = u + "Date",
            n[d] = u + "Date",
            n[c] = u + "Month",
            n[f] = u + "FullYear",
            n[i] = u + "Hours",
            n[s] = u + "Minutes",
            n[o] = u + "Seconds",
            n[r] = u + "Milliseconds",
            n)[l], h = l === a ? this.$D + (t - this.$W) : t;
            if (l === c || l === f) {
                var m = this.clone().set(d, 1);
                m.$d[p](h),
                m.init(),
                this.$d = m.set(d, Math.min(this.$D, m.daysInMonth())).$d
            } else
                p && this.$d[p](h);
            return this.init(),
            this
        }
        ,
        v.set = function(e, t) {
            return this.clone().$set(e, t)
        }
        ,
        v.get = function(e) {
            return this[E.p(e)]()
        }
        ,
        v.add = function(r, u) {
            var d, p = this;
            r = Number(r);
            var h = E.p(u)
              , m = function(e) {
                var t = C(p);
                return E.w(t.date(t.date() + Math.round(e * r)), p)
            };
            if (h === c)
                return this.set(c, this.$M + r);
            if (h === f)
                return this.set(f, this.$y + r);
            if (h === a)
                return m(1);
            if (h === l)
                return m(7);
            var g = (d = {},
            d[s] = t,
            d[i] = n,
            d[o] = e,
            d)[h] || 1
              , v = this.$d.getTime() + r * g;
            return E.w(v, this)
        }
        ,
        v.subtract = function(e, t) {
            return this.add(-1 * e, t)
        }
        ,
        v.format = function(e) {
            var t = this
              , n = this.$locale();
            if (!this.isValid())
                return n.invalidDate || p;
            var r = e || "YYYY-MM-DDTHH:mm:ssZ"
              , o = E.z(this)
              , s = this.$H
              , i = this.$m
              , a = this.$M
              , l = n.weekdays
              , c = n.months
              , u = function(e, n, o, s) {
                return e && (e[n] || e(t, r)) || o[n].slice(0, s)
            }
              , f = function(e) {
                return E.s(s % 12 || 12, e, "0")
            }
              , d = n.meridiem || function(e, t, n) {
                var r = e < 12 ? "AM" : "PM";
                return n ? r.toLowerCase() : r
            }
              , h = {
                YY: String(this.$y).slice(-2),
                YYYY: this.$y,
                M: a + 1,
                MM: E.s(a + 1, 2, "0"),
                MMM: u(n.monthsShort, a, c, 3),
                MMMM: u(c, a),
                D: this.$D,
                DD: E.s(this.$D, 2, "0"),
                d: String(this.$W),
                dd: u(n.weekdaysMin, this.$W, l, 2),
                ddd: u(n.weekdaysShort, this.$W, l, 3),
                dddd: l[this.$W],
                H: String(s),
                HH: E.s(s, 2, "0"),
                h: f(1),
                hh: f(2),
                a: d(s, i, !0),
                A: d(s, i, !1),
                m: String(i),
                mm: E.s(i, 2, "0"),
                s: String(this.$s),
                ss: E.s(this.$s, 2, "0"),
                SSS: E.s(this.$ms, 3, "0"),
                Z: o
            };
            return r.replace(m, (function(e, t) {
                return t || h[e] || o.replace(":", "")
            }
            ))
        }
        ,
        v.utcOffset = function() {
            return 15 * -Math.round(this.$d.getTimezoneOffset() / 15)
        }
        ,
        v.diff = function(r, d, p) {
            var h, m = E.p(d), g = C(r), v = (g.utcOffset() - this.utcOffset()) * t, y = this - g, _ = E.m(this, g);
            return _ = (h = {},
            h[f] = _ / 12,
            h[c] = _,
            h[u] = _ / 3,
            h[l] = (y - v) / 6048e5,
            h[a] = (y - v) / 864e5,
            h[i] = y / n,
            h[s] = y / t,
            h[o] = y / e,
            h)[m] || y,
            p ? _ : E.a(_)
        }
        ,
        v.daysInMonth = function() {
            return this.endOf(c).$D
        }
        ,
        v.$locale = function() {
            return b[this.$L]
        }
        ,
        v.locale = function(e, t) {
            if (!e)
                return this.$L;
            var n = this.clone()
              , r = S(e, t, !0);
            return r && (n.$L = r),
            n
        }
        ,
        v.clone = function() {
            return E.w(this.$d, this)
        }
        ,
        v.toDate = function() {
            return new Date(this.valueOf())
        }
        ,
        v.toJSON = function() {
            return this.isValid() ? this.toISOString() : null
        }
        ,
        v.toISOString = function() {
            return this.$d.toISOString()
        }
        ,
        v.toString = function() {
            return this.$d.toUTCString()
        }
        ,
        g
    }()
      , O = x.prototype;
    return C.prototype = O,
    [["$ms", r], ["$s", o], ["$m", s], ["$H", i], ["$W", a], ["$M", c], ["$y", f], ["$D", d]].forEach((function(e) {
        O[e[1]] = function(t) {
            return this.$g(t, e[0], e[1])
        }
    }
    )),
    C.extend = function(e, t) {
        return e.$i || (e(t, x, C),
        e.$i = !0),
        C
    }
    ,
    C.locale = S,
    C.isDayjs = w,
    C.unix = function(e) {
        return C(1e3 * e)
    }
    ,
    C.en = b[_],
    C.Ls = b,
    C.p = {},
    C
}();
// export {Ss as $, xr as A, ji as B, pr as C, Ta as D, Ki as E, Ao as F, Aa as G, Ra as H, S as I, Vn as J, mn as K, gn as L, jo as M, Lo as N, Wo as O, n as P, Zo as Q, a as R, p as S, qs as T, Nt as U, Sr as V, Bo as W, vn as X, er as Y, Er as Z, At as _, ft as a, kr as a0, l as a1, qo as a2, Tr as a3, ee as a4, bt as a5, mt as a6, _t as a7, ne as a8, re as a9, Ol as aa, Rr as ab, Go as ac, Rl as ad, Pi as ae, hr as b, bs as c, nr as d, rr as e, fr as f, xt as g, as as h, Pn as i, Uo as j, Ko as k, Yn as l, Fn as m, Zt as n, mr as o, kn as p, ts as q, Ot as r, br as s, xo as t, kt as u, Ni as v, Mn as w, dr as x, Yo as y, Hi as z};
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