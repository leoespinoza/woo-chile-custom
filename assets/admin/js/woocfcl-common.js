var commHelper = {
    //encriptacion
    isInternetExplorer: function () { return navigator.userAgent.toLowerCase().indexOf('msie') >= 0; },
    isUndefined: function (o) { return typeof o === 'undefined'; },
    isNumber: function (o) { return !isNaN(o); },
    isString: function (o) { return typeof o === 'string'; },
    isDate(o) { return o && toString.call(o) === "[object Date]" && !isNaN(o); },
    isDateString: function (o) { var dateWrapper = new Date(o); return !isNaN(dateWrapper.getDate()); },
    isFunction(o) { return typeof o === "function" ? true : false; },
    isJSON: function (o) { return Object.prototype.toString.call(o) === '[object Object]'; },
    //jquery required
    isArray(o) { return $.isArray(o); },

    isEmpty: function (o) { return typeof o === "undefined" || o === null || o === ""; },
    isEmptyArray: function (o) { var res = this.isArray(o); if (res === true) return o.length === 0; else return res; },
    isEmptyLookup: function (o) { return o === -1 || o === 0 || o === "-1" || o === "0" || this.isEmpty(o); },
    isPhonenumberCl: function (o) { var phoneno = /^(\+?56)?(\s?)(0?9)(\s?)[9876543]\d{7}$/; if (this.isEmpty(o)) return false; return o.match(phoneno) ? true : false; },
    isEmail: function (o) { return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(o)) ? true : false; },
    isStringValid: function (o, minlength, maxlength) { var str = this.getString(o); return str.length < minlength || str.length > maxlength ? false : true; },
    getString: function (o, defaultvalue) { defaultvalue = this.isEmpty(defaultvalue) ? "" : defaultvalue; return this.isEmpty(o) ? defaultvalue : o; },
    getBool: function (o) { return this.isEmpty(o) ? false : (o === "on" || o === "true" || o === true ? true : false); },
    getDate: function (o) {
        o = this.isEmpty(o) ? null : o;
        if (o !== null) {
            o = this.isString(o) ? (this.isDateString(o) ? new Date(o) : null) : o;
            o = o !== null && this.isDate(o) ? o : null;
        }
        return o;
    },
    getInteger: function (o, defaultvalue) { if (this.isEmpty(o)) return defaultvalue; return this.getNumber(o, defaultvalue, 0); },
    getNumber: function (o, defaultvalue, decimals) {
        defaultvalue = this.isEmpty(defaultvalue) ? 0 : defaultvalue;
        decimals = this.isEmpty(decimals) ? 2 : decimals;
        o = this.isEmpty(o) ? defaultvalue : o.toString().replace(/\$|,/g, '');
        o = isNaN(o) ? defaultvalue : o;
        o = Number(parseInt(o, 10).toFixed(decimals));
        return o;
    },
    getNumberFromString: function (o) {
        if (typeof o === "string") {
            var match = o.match(/[0-9,.]+/g);// commas to delimit thousands need to be removed
            if (null !== match) { o = match[0].replace(/,/g, ''); o = parseFloat(o); }
        }
        return o;
    },
    getDateStringfromISO: function (o) {
        var date = !this.isEmpty(o) ? new Date(o) : "";
        if (date === "") return date;
        return this.appendLeadingZeroes(date.getDate()) + "-" + this.appendLeadingZeroes(date.getMonth() + 1) + "-" + date.getFullYear();
    },
    getDatefromISO: function (o) { return !this.isEmpty(o) ? new Date(o) : null; },
    setDateToISO: function (date, strhour) {
        strhour = this.isEmpty(strhour) ? 'T12:00:00Z' : strhour;
        return date.getFullYear() + "-" + this.appendLeadingZeroes(date.getMonth() + 1) + "-" + this.appendLeadingZeroes(date.getDate()) + strhour;
    },
    setDateToISOFilter: function (date) {
        var self = this;
        var strhour = {
            ini: self.setDateToISO(date, 'T00:00:00Z'),
            end: self.setDateToISO(date, 'T23:59:59Z')
        };
        return strhour;
    },
    //+ Jonas Raoni Soares Silva
    //@ http://jsfromhell.com/number/fmt-money [rev. #2]
    // Modified to pass JSLint
    // n = the number to format
    // c = # of floating point decimal places, default 2
    // d = decimal separator, default "."
    // t = thousands separator, default ","
    getMoneyformat: function (n, c, d, t) {
        c = isNaN(c = Math.abs(c)) ? 2 : c;
        d = d === undefined ? "." : d;
        t = t === undefined ? "," : t;
        var s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c), 10) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    },
    getUrlParameter: function (name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    },
    urlformat: function (url, param) {
        var paramlg = langsupport === true ? "?lg=" + APP_PAGE.lang : "";
        var munion = url.indexOf("?") >= 0 ? "&" : "?";
        param = this.isEmpty(param) ? "" : munion + param;
        return url + param;
    },
    redirect: function (url, param) {
        var self = this;
        window.location.replace(self.urlformat(url, param));
    },
    redirectTime: function (url, param) {
        var self = this;
        if (!this.isEmpty(url)) setTimeout(function () { window.location.replace(self.urlformat(url, param)); }, 500);
    },
    redirectNewWindow: function (url) {
        var win = window.open(url, '_blank');
        win.focus();
    },
    appendLeadingZeroes: function (n) { return n <= 9 ? "0" + n : n; },
    findInArray: function (o, value, key, subkey) {
        var self = this;
        if (self.isEmpty(o) || !self.isArray(o)) return null;
        if (self.isEmpty(value)) return null;

        var resultArray = $.grep(o, function (item, i) {
            if (self.isEmpty(subkey)) return item[key] === value;
            else {
                if (this.isEmpty(item[key][subkey])) return null;
                return item[key][subkey] === value;
            } 
        }, false);

        return resultArray.length > 0 ? resultArray : null;
    },
    getValueFromObject: function (obj, arrkey) {
        try {
            if (this.isEmpty(obj)) return null;

            var keys = null;
            var objx = obj;
            $.each(arrkey, function (ix, itemkey) {

                keys = Object.keys(objx);
                //console.log("objx", objx, keys);
                if (keys.indexOf(itemkey) !== -1) {
                    objx = objx[itemkey];
                }
                else return null;
            });

            return objx;

        } catch (ex) {
            return "";
        }
    },
    getValueFromObject: function (obj, key, subkey) {
        return this.getValuerFromObject(obj, key, subkey);
    }

};