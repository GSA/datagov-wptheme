/*
 * v0.1 121011 : First Test Version
 * v1.0 121012 : Added Cookie Synchronizing and filtered out Outbound tracking of cross- and sub-domain links
 * v1.1 121015 : Changed cross-domain to use setAllowAnchor and fixed problem with some links
 * v1.2 121015-2 : Added incoming cross-domain tracking to default _gaq tracker by adding _setAllowLinker and _setAllowAnchor
 * v1.3 121015-3 : All Cross-domain Tracking removed
 * v1.4 121015-4 : Multiple Search parameters and XDT links tracked as events
 * v1.5 121122 : Change to sub-domain level visits (cookies). _DOMReady delays tracking so goes last. ECereto Review. JSHinted
 * v1.6 130107 : Added Agency, sub-agency and Cookie timeout variables and functions
 * v1.61 130115 : Fix for (elm in ... now for (var elm = 0 Added Agency, sub-agency and Cookie timeout variables and functions
 * v1.62 130123 : Using Slots 33, 34, 35 for Page Level Custom Vars
 * v1.7 130503 : Single File Version
 * v1.71 130708 : Single File s/d Ver and AGENCY/SUB defaulting to hostnames instead of 'unspecified'
 */
/**
 * @preserve
 * v1.72 130719 : SFS PUAs and exts
 * Brian Katz, Cardinal Path - Google Analytics Government Wide Site Usage Measurement
 **/

var _gaq = _gaq || [];
var _gas = _gas || [];

var GSA_CPwrapGA = (function () {

    var instance = this;
    var domainHash;
    var dlh = document.location.hostname;

    var oCONFIG = {
        // System parameters - don't change without discussion with CP
        VERSION : 'v1.72 130719 : SFS PUAs and exts',
        GAS_PATH : '',
        SEARCH_PARAMS : 'querytext|nasaInclude|k|QT', // ver 1.4 Normalize query params
        HOST_DOMAIN_OR : dlh, // default is to track sub-domains individually - override set in _setParams()
        LEADING_PERIOD : '.',
        GWT_UAID 	   : 'UA-33523145-1',

        // GSA Configurable parameters - ver 1.6 -
        AGENCY : '',				// Singular, consistent, succinct, user-friendly abbreviation for the agency.  E.g. DOJ, DOI, Commerce
        VISITOR_TIMEOUT 	: -1,	// Specified in months, 0 = session = when browser closes, -1 = don't change default (24 months)
        CAMPAIGN_TIMEOUT 	: -1,	// Specified in months, 0 = session = when browser closes, -1 = don't change default (6 months)
        // CAMPAIGN_TIMEOUT must be <= VISITOR_TIMEOUT
        VISIT_TIMEOUT		: -1,	// Specified in minutes, 0 = session = when browser closes, -1 = don't change default (30 minutes)
        ANONYMIZE_IP		: true,	// only change to false in rare circumustances where GeoIP location accuracy is critical
        YOUTUBE 			: false
    };

    // Object for centralized control of all Custom Variables reported in this sript file.
    // Since GSA code only ever sets page level CVs, scope is always 3
    var oCVs = {
        agency		: { key : 'Agency', slot : 33, scope : 3},
        sub_agency	: { key : 'Sub-Agency',slot : 34, scope : 3},
        version		: { key : 'Code Ver',slot : 35, scope : 3
        }
    }

    /**
     *  Sets up _gas and configures accounts, domains, etc,
     * In effect, ensures functions are compiled before being called
     * @private
     */
    var _init = function () {

        _setParams();

        oCONFIG.HOST_DOMAIN_OR = oCONFIG.HOST_DOMAIN_OR.replace(/^www\./i, '');

        var ary = setHashAndPeriod(oCONFIG.HOST_DOMAIN_OR);
        oCONFIG.LEADING_PERIOD = ary[1];

        _gas.push(['GSA_CP1._setAccount', oCONFIG.GWT_UAID]);
        if (oCONFIG.PARALLEL_UA && !oCONFIG.DEBUG_MODE)
            for (var i=0;i<oCONFIG.PARALLEL_UA.length;i++) {
                _gas.push(['GSA_CP' + (i+2) + '._setAccount', oCONFIG.PARALLEL_UA[i]]);
            }

        if (oCONFIG.ANONYMIZE_IP) {
            _gaq.push (['_gat._anonymizeIp']);
        }
        _gas.push(['_setDomainName', oCONFIG.LEADING_PERIOD + oCONFIG.HOST_DOMAIN_OR]);

        setGAcookieTimeouts();

        if (ary[0]) {
            _gas.push(['_setAllowHash', false]);
        }

        _gas.push(['_gasTrackOutboundLinks']);

        if (oCONFIG.EXTS) {
            _gas.push(['_gasTrackDownloads',{'extensions': oCONFIG.EXTS.split(',')}]);
        } else {
            _gas.push(['_gasTrackDownloads']);
        }

        _gas.push(['_gasTrackMailto']);
        if (oCONFIG.YOUTUBE) {
            _gas.push(['_gasTrackYoutube', {percentages: [33, 66, 90], force:true}]);
        }

        // Filter out sub-domain links tracked as Outbound
        _gas.push(['_addHook', '_trackEvent', function (cat, act) {
            var linkDomain = act.match(/([^.]+\.(gov|mil)$)/);
            if (cat === 'Outbound' && typeof act === "string" && linkDomain) {
                return (document.location.hostname.indexOf(linkDomain[1]) === -1);
            }
        }
        ]);

        // Add hook to _trackPageview to standardize search parameters
        _gas.push(['_addHook', '_trackPageview', function (pageName) {
            var re = new RegExp('([?&])(' + oCONFIG.SEARCH_PARAMS + ')(=[^&]*)', 'i');
            if (re.test(pageName)) {
                pageName = pageName.replace(re, '$1query$3');
            }
            return [pageName];
        }
        ]);

    };


    /**
     *  Sets the cookie timeouts if values have been set in oCONFIG at the top of this file
     *
     * @private
     */
    var setGAcookieTimeouts = function() {
        if (oCONFIG.VISIT_TIMEOUT > -1) _gaq.push(['_setSessionCookieTimeout', oCONFIG.VISIT_TIMEOUT*1000*60]);					// Specified in minutes
        if (oCONFIG.VISITOR_TIMEOUT > -1) _gaq.push(['_setVisitorCookieTimeout', oCONFIG.VISITOR_TIMEOUT*1000*60*60*24*30.416667]);	// Specified in months - GA uses 30.416.. as the number of days/month
        if (oCONFIG.CAMPAIGN_TIMEOUT > -1) _gaq.push(['_setCampaignCookieTimeout', oCONFIG.CAMPAIGN_TIMEOUT*1000*60*60*24*30.416667]);	// Specified in months
    }


    /**
     *  Returns the domain and top-level domain  - eg example.com, example.ca example.co.uk, example.com.au or ipaddress
     *
     * @private
     * @param {string} strURL a hostname or full url
     */
    var getDomainNameGovMil = function (strURL) {
        strURL = strURL || dlh;

        // extract the host name since full url may have been provided
        strURL = strURL.match(/^(?:https?:\/\/)?([^\/:]+)/)[1]; // this cannot error unless running as file://

        if (strURL.match(/(\d+\.){3}(\d+)/) || strURL.search(/\./) == -1)
            return strURL; // ipaddress


        try {
            if (/\.(gov|mil)$/i.test(strURL)) { // Customized for .gov and .mil
                strURL = strURL.match(/\.([^.]+\.(gov|mil)$)/i)[1];
            } else {
                strURL = strURL.match(/(([^.\/]+\.[^.\/]{2,3}\.[^.\/]{2})|(([^.\/]+\.)[^.\/]{2,4}))(\/.*)?$/)[1];
            }

        } catch (e) {}
        return strURL.toLowerCase();
    };

    /**
     *  Returns the GA hash for the Cookie domain passed
     *
     * @private
     * @param {string} strCookieDomain -  the hostname used for the cookie domain
     */
    var getDomainHash = function (strCookieDomain) {

        fromGaJs_h = function (e) {
            return undefined == e || "-" == e || "" == e;
        };
        fromGaJs_s =
            function (e) {
                var k = 1,
                    a = 0,
                    j,
                    i;
                if (!fromGaJs_h(e)) {
                    k = 0;
                    for (j = e.length - 1; j >= 0; j--) {
                        i = e.charCodeAt(j);
                        k = (k << 6 & 268435455) + i + (i << 14);
                        a = k & 266338304;
                        k = a !== 0 ? k^a >> 21 : k;
                    }
                }
                return k;
            };
        return fromGaJs_s(strCookieDomain);
    };

    /**
     *  Returns an array [bool, str] where bool indicates value for setAllowHash and str is either blank or a leading period
     *
     * @private
     * @param {string} strCookieDomain -  the hostname used for the cookie domain WITHOUT  the leading period
     */
    var setHashAndPeriod = function (strCookieDomain) {
        var utmaCookies = document.cookie.match(/__utma=[^.]+/g);
        var retVals = [false, '']; // setAllowHash = false and leading period = ''

        // if no cookies found
        if (!utmaCookies)
            return retVals;

        domainHash = getDomainHash(strCookieDomain);

        for (var elm = 0; elm < utmaCookies.length ; elm++) {
            utmaCookies[elm] = utmaCookies[elm].substr(7); // strip __utma= leaving only the hash

            // look for the cookie with the matching domain hash
            var hashFound = (domainHash == utmaCookies[elm]);
            // if found, there's a hash and we're done
            if (hashFound) {
                retVals[0] = false;
                return retVals;
            } else { // check for period
                hashFound = (getDomainHash('.' + strCookieDomain) == utmaCookies[elm]);
                retVals[1] = hashFound ? '.' : '';
            }

            // if not found, check for setAllowHashFalse - aka hash = 1
            retVals[0] = retVals[0] || ('1' == utmaCookies[elm]); // true if hash == 1
        }

        return retVals;
    };

    /**
     *  Sets the Custom Variables for Agency and sub-Agency based on the agency and sub_agency objects in oCVs
     *
     * @private
     */
    var setAgencyVars = function() {
        setCustomVar(oCONFIG.AGENCY, oCVs.agency); // Page level variable sent only to GSA account
        setCustomVar(oCONFIG.SUB_AGENCY, oCVs.sub_agency); // Page level variable sent only to GSA account
    }
    /**
     *  Single generic method to set all custom vars based on single control object for all CVs - see oCVs near the top of the file
     *	To keep the cookies synchronized, first check that agency is not already using the slot for a Vistor Level Varialbe
     *  If it is, even a PLCV will remove the value from their cookie.  In that case we don't set the variable.

     * @private
     * @param {string} value -  the only argument set outside of oCVs
     * @param {object} oCV -  the object in oCVs for a particular variable
     */
    var setCustomVar = function (value, oCV) {
        if (!value) return;

        var pageTracker = _gat._getTrackerByName(); // Gets the default tracker.
        var visitorCustomVarValue = pageTracker._getVisitorCustomVar(oCV.slot);

        if (!visitorCustomVarValue)
            _gas.push(['_setCustomVar', oCV.slot, oCV.key, value, oCV.scope]); // Record version in Page Level (oCV.scope ) Custom Variable specified in oCV.slot
    }

    /**
     * Reports a page view and detects if page is a 404 Page not found
     * @public
     */
    this.onEveryPage = function () {

        var pageName = document.location.pathname + document.location.search + document.location.hash;

        // ... Page Not Found
        // Track as a pageview because we need to see if it's a landing page.
        if (document.title.search(/404|not found/i) !== -1) {
            var vpv404 = '/vpv404/' + pageName;
            pageName = vpv404.replace(/\/\//g, '/') + '/' + document.referrer;
        }

        setCustomVar(oCONFIG.VERSION, oCVs.version)
        setAgencyVars();
        _gas.push(['_trackPageview', pageName]);
    };


    /**
     * Retrieves the params from the script block src
     * @private
     */
    var _setParams = function _setParams () {
        var src = document.getElementById('_fed_an_js_tag');
        var tags;
        if (!src) tags = document.getElementsByTagName('script');
        for (var i = 0; tags && !src && i < tags.length; i++) {
            var tag = tags[i];
            if (/federated-analytics.*\.js/i.test(tag.src)) src = tag;
        }

        if (src) {
            src = src.src.split(/[?&]/);
            src.shift();
            for (var i = 0; i < src.length; i++) {

                var param = src[i].split('=');
                src[0] = src[0].toLowerCase();

                // params in the query string
                if ('agency' == param[0]) {
                    oCONFIG.AGENCY = param[1].toUpperCase();
                } else if (/sub(-?agency)?/.test(param[0])) {
                    oCONFIG.SUB_AGENCY = param[1].toUpperCase();
                } else if ('sp' == param[0]) {
                    param[1] = param[1].replace(/[,;\/]/g,'|');
                    oCONFIG.SEARCH_PARAMS = oCONFIG.SEARCH_PARAMS + '|' + param[1];
                    oCONFIG.SEARCH_PARAMS = oCONFIG.SEARCH_PARAMS.replace(/\|\|/g, '|');
                } else if ('vcto' == param[0]) {
                    oCONFIG.VISITOR_TIMEOUT = parseInt(param[1]);
                } else if ('camto' == param[0]) {
                    oCONFIG.CAMPAIGN_TIMEOUT = parseInt(param[1]);
                } else if ('pua' == param[0]) {
                    oCONFIG.PARALLEL_UA = param[1].toUpperCase();
                    oCONFIG.PARALLEL_UA = oCONFIG.PARALLEL_UA.split(',');
                } else if ('devua' == param[0]) {
                    oCONFIG.GWT_UAID = param[1].toUpperCase();
                    oCONFIG.DEBUG_MODE = true;
                } else if ('exts' == param[0]) {
                    oCONFIG.EXTS = param[1].toLowerCase();
                    oCONFIG.EXTS = oCONFIG.EXTS.replace(/ /g,'');
                } else if ('aip' == param[0]) {
                    oCONFIG.ANONYMIZE_IP = ('true' == param[1]) ? true : !('false' == param[1]);
                } else if ('yt' == param[0]) {
                    oCONFIG.YOUTUBE = ('true' == param[1]) ? true : !('false' == param[1]);
                } else if ('sdor' == param[0]) {	// subdomain override
                    // default is false - tracking will be at the sub-domain level
                    if (('true' == param[1]) ? true : !('false' == param[1])) {
                        // getDomainNameGovMil() returns domain name, not sub-domains and with no leading period e.g.  returns usa.gov on http://xyz.usa.gov
                        oCONFIG.HOST_DOMAIN_OR = getDomainNameGovMil();
                    } else {
                        oCONFIG.HOST_DOMAIN_OR = dlh;
                    }
                }
            }
        }

        // Defaults for Agency and Sub-Agency.  Others are in the oCONFIG object
        oCONFIG.AGENCY = oCONFIG.AGENCY || 'unspecified:' + oCONFIG.HOST_DOMAIN_OR;
        oCONFIG.SUB_AGENCY = oCONFIG.SUB_AGENCY || 'unspecified:' + dlh;

        oCONFIG.SUB_AGENCY = oCONFIG.AGENCY + ' - ' + oCONFIG.SUB_AGENCY

        oCONFIG.CAMPAIGN_TIMEOUT = Math.min(oCONFIG.CAMPAIGN_TIMEOUT, oCONFIG.VISITOR_TIMEOUT);
    }
    _init();

});

// -- End of federated-analytics.js ----
// To make the instructions and implementation as easy as possible for all agencies, gas.js has been included below


// -- gasStart--

/**
 * @preserve Copyright 2011, Cardinal Path and DigitalInc.
 *
 * GAS - Google Analytics on Steroids
 * https://github.com/CardinalPath/gas
 *
 * @author Eduardo Cereto <eduardocereto@gmail.com>
 * Licensed under the GPLv3 license.
 */
(function(window, undefined) {
    /**
     * GAS - Google Analytics on Steroids
     *
     * Helper Functions
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the MIT license.
     *
     * @author Eduardo Cereto <eduardocereto@gmail.com>
     */

    /**
     * GasHelper singleton class
     *
     * Should be called when ga.js is loaded to get the pageTracker.
     *
     * @constructor
     */
    var GasHelper = function () {
        this._setDummyTracker();
    };

    GasHelper.prototype._setDummyTracker = function () {
        if (!this['tracker']) {
            var trackers = window['_gat']['_getTrackers']();
            if (trackers.length > 0) {
                this['tracker'] = trackers[0];
            }
        }
    };

    /**
     * Returns true if the element is found in the Array, false otherwise.
     *
     * @param {Array} obj Array to search at.
     * @param {object} item Item to search form.
     * @return {boolean} true if contains.
     */
    GasHelper.prototype.inArray = function (obj, item) {
        if (obj && obj.length) {
            for (var i = 0; i < obj.length; i++) {
                if (obj[i] === item) {
                    return true;
                }
            }
        }
        return false;
    };

    /**
     * Removes special characters and Lowercase String
     *
     * @param {string} str to be sanitized.
     * @param {boolean} strict_opt If we should remove any non ascii char.
     * @return {string} Sanitized string.
     */
    GasHelper.prototype._sanitizeString = function (str, strict_opt) {
        str = str.toLowerCase()
            .replace(/^\ +/, '')
            .replace(/\ +$/, '')
            .replace(/\s+/g, '_')
            .replace(/[áàâãåäæª]/g, 'a')
            .replace(/[éèêëЄ€]/g, 'e')
            .replace(/[íìîï]/g, 'i')
            .replace(/[óòôõöøº]/g, 'o')
            .replace(/[úùûü]/g, 'u')
            .replace(/[ç¢©]/g, 'c');

        if (strict_opt) {
            str = str.replace(/[^a-z0-9_\-]/g, '_');
        }
        return str.replace(/_+/g, '_');
    };

    /**
     * Cross Browser helper to addEventListener.
     *
     * ga_next.js currently have a _addEventListener directive. So _gas will
     * allways prefer that if available, and will use this one only as a fallback
     *
     * @param {HTMLElement} obj The Element to attach event to.
     * @param {string} evt The event that will trigger the binded function.
     * @param {function(event)} ofnc The function to bind to the element.
     * @param {boolean} bubble true if event should be fired at bubble phase.
     * Defaults to false. Works only on W3C compliant browser. MSFT don't support
     * it.
     * @return {boolean} true if it was successfuly binded.
     */
    GasHelper.prototype._addEventListener = function (obj, evt, ofnc, bubble) {
        var fnc = function (event) {
            if (!event || !event.target) {
                event = window.event;
                event.target = event.srcElement;
            }
            return ofnc.call(obj, event);
        };
        // W3C model
        if (obj.addEventListener) {
            obj.addEventListener(evt, fnc, !!bubble);
            return true;
        }
        // M$ft model
        else if (obj.attachEvent) {
            return obj.attachEvent('on' + evt, fnc);
        }
        // Browser doesn't support W3C or M$ft model. Time to go old school
        else {
            evt = 'on' + evt;
            if (typeof obj[evt] === 'function') {
                // Object already has a function on traditional
                // Let's wrap it with our own function inside another function
                fnc = (function (f1, f2) {
                    return function () {
                        f1.apply(this, arguments);
                        f2.apply(this, arguments);
                    };
                }(obj[evt], fnc));
            }
            obj[evt] = fnc;
            return true;
        }
    };

    /**
     * Cross Browser Helper to emulate jQuery.live
     *
     * Binds to the document root. Listens to all events of the specific type.
     * If event don't bubble it won't catch
     */
    GasHelper.prototype._liveEvent = function (tag, evt, ofunc) {
        var gh = this;
        tag = tag.toUpperCase();
        tag = tag.split(',');

        gh._addEventListener(document, evt, function (me) {
            for (var el = me.target; el.nodeName !== 'HTML';
                 el = el.parentNode)
            {
                if (gh.inArray(tag, el.nodeName) || el.parentNode === null) {
                    break;
                }
            }
            if (el && gh.inArray(tag, el.nodeName)) {
                ofunc.call(el, me);
            }

        }, true);
    };

    /**
     * Cross Browser DomReady function.
     *
     * Inspired by: http://dean.edwards.name/weblog/2006/06/again/#comment367184
     *
     * @param {function(Event)} callback DOMReady callback.
     * @return {boolean} Ignore return value.
     */
    GasHelper.prototype._DOMReady = function (callback) {
        var scp = this;
        function cb() {
            if (cb.done) return;
            cb.done = true;
            callback.apply(scp, arguments);
        }
        if (/^(interactive|complete)/.test(document.readyState)) return cb();
        this._addEventListener(document, 'DOMContentLoaded', cb, false);
        this._addEventListener(window, 'load', cb, false);
    };

    /**
     * GAS - Google Analytics on Steroids
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the MIT license.
     *
     * @author Eduardo Cereto <eduardocereto@gmail.com>
     */
    /*global document:true*/

    /**
     * Google Analytics original _gaq.
     *
     * This never tries to do something that is not supposed to. So it won't break
     * in the future.
     */
    window['_gaq'] = window['_gaq'] || [];

    var _prev_gas = window['_gas'] || [];

// Avoid duplicate definition
    if (_prev_gas._accounts_length >= 0) {
        return;
    }

//Shortcuts, these speed up and compress the code
    var document = window.document,
        toString = Object.prototype.toString,
        hasOwn = Object.prototype.hasOwnProperty,
        push = Array.prototype.push,
        slice = Array.prototype.slice,
        trim = String.prototype.trim,
        sindexOf = String.prototype.indexOf,
        url = document.location.href,
        documentElement = document.documentElement;

    /**
     * GAS Sigleton
     * @constructor
     */
    function GAS() {
        var self = this;
        self['version'] = '1.10.1';
        self._accounts = {};
        self._accounts_length = 0;
        self._queue = _prev_gas;
        self._default_tracker = '_gas1';
        self.gh = {};
        self._hooks = {
            '_addHook': [self._addHook]
        };
        // Need to be pushed to make sure tracker is done
        // Sets up helpers, very first thing pushed into gas
        self.push(function () {
            self.gh = new GasHelper();
        });
    }

    /**
     * First standard Hook that is responsible to add next Hooks
     *
     * _addHook calls always reurn false so they don't get pushed to _gaq
     * @param {string} fn The function you wish to add a Hook to.
     * @param {function()} cb The callback function to be appended to hooks.
     * @return {boolean} Always false.
     */
    GAS.prototype._addHook = function (fn, cb) {
        if (typeof fn === 'string' && typeof cb === 'function') {
            if (typeof _gas._hooks[fn] === 'undefined') {
                _gas._hooks[fn] = [];
            }
            _gas._hooks[fn].push(cb);
        }
        return false;
    };

    /**
     * Construct the correct account name to be used on _gaq calls.
     *
     * The account name for the first unamed account pushed to _gas is the standard
     * account name. It's pushed without the account name to _gaq, so if someone
     * calls directly _gaq it works as expected.
     * @param {string} acct Account name.
     * @return {string} Correct account name to be used already with trailling dot.
     */
    function _build_acct_name(acct) {
        return acct === _gas._default_tracker ? '' : acct + '.';
    }

    function _gaq_push(arr) {
        if (_gas.debug_mode) {
            try {
                console.log(arr);
            }catch (e) {}
        }
        return window['_gaq'].push(arr);
    }

    /**
     * Everything pushed to _gas is executed by this call.
     *
     * This function should not be called directly. Instead use _gas.push
     * @return {number} This is the same return as _gaq.push calls.
     */
    GAS.prototype._execute = function () {
        var args = slice.call(arguments),
            self = this,
            sub = args.shift(),
            gaq_execute = true,
            i, foo, hooks, acct_name, repl_sub, return_val = 0;

        if (typeof sub === 'function') {
            // Pushed functions are executed right away
            return _gaq_push(
                (function (s, gh) {
                    return function () {
                        // pushed functions receive helpers through this object
                        s.call(gh);
                    };
                }(sub, self.gh))
            );

        } else if (typeof sub === 'object' && sub.length > 0) {
            foo = sub.shift();

            if (sindexOf.call(foo, '.') >= 0) {
                acct_name = foo.split('.')[0];
                foo = foo.split('.')[1];
            } else {
                acct_name = undefined;
            }

            // Execute hooks
            hooks = self._hooks[foo];
            if (hooks && hooks.length > 0) {
                for (i = 0; i < hooks.length; i++) {
                    try {
                        repl_sub = hooks[i].apply(self.gh, sub);
                        if (repl_sub === false) {
                            // Returning false from a hook cancel the call
                            gaq_execute = false;
                        } else {
                            if (repl_sub && repl_sub.length > 0) {
                                // Returning an array changes the call parameters
                                sub = repl_sub;
                            }
                        }
                    } catch (e) {
                        if (foo !== '_trackException') {
                            self.push(['_trackException', e]);
                        }
                    }
                }
            }
            // Cancel execution on _gaq if any hook returned false
            if (gaq_execute === false) {
                return 1;
            }
            // Intercept _setAccount calls
            if (foo === '_setAccount') {

                for (i in self._accounts) {
                    if (self._accounts[i] === sub[0]) {
                        // Repeated account
                        if (acct_name === undefined) {
                            return 1;
                        }
                    }
                }
                acct_name = acct_name || '_gas' +
                    String(self._accounts_length + 1);
                // Force that the first unamed account is _gas1
                if (typeof self._accounts['_gas1'] === 'undefined' &&
                    sindexOf.call(acct_name, '_gas') !== -1) {
                    acct_name = '_gas1';
                }
                self._accounts[acct_name] = sub[0];
                self._accounts_length += 1;
                acct_name = _build_acct_name(acct_name);
                return_val = _gaq_push([acct_name + foo, sub[0]]);
                // Must try t get the tracker if it's a _setAccount
                self.gh._setDummyTracker();
                return return_val;
            }

            // Intercept functions that can only be called once.
            if (foo === '_link' || foo === '_linkByPost' || foo === '_require' ||
                foo === '_anonymizeIp')
            {
                args = slice.call(sub);
                args.unshift(foo);
                return _gaq_push(args);
            }

            // If user provides account than trigger event for just that account.
            var acc_foo;
            if (acct_name && self._accounts[acct_name]) {
                acc_foo = _build_acct_name(acct_name) + foo;
                args = slice.call(sub);
                args.unshift(acc_foo);
                return _gaq_push(args);
            }

            // Call Original _gaq, for all accounts
            if (self._accounts_length > 0) {
                for (i in self._accounts) {
                    if (hasOwn.call(self._accounts, i)) {
                        acc_foo = _build_acct_name(i) + foo;
                        args = slice.call(sub);
                        args.unshift(acc_foo);
                        return_val += _gaq_push(args);
                    }
                }
            } else {
                // If there are no accounts we just push it to _gaq
                args = slice.call(sub);
                args.unshift(foo);
                return _gaq_push(args);
            }
            return return_val ? 1 : 0;
        }
    };

    /**
     * Standard method to execute GA commands.
     *
     * Everything pushed to _gas is in fact pushed back to _gaq. So Helpers are
     * ready for hooks. This creates _gaq as a series of functions that call
     * _gas._execute() with the same arguments.
     */
    GAS.prototype.push = function () {
        var self = this;
        var args = slice.call(arguments);
        for (var i = 0; i < args.length; i++) {
            (function (arr, self) {
                window['_gaq'].push(function () {
                    self._execute.call(self, arr);
                });
            }(args[i], self));
        }
    };

    /**
     * _gas main object.
     *
     * It's supposed to be used just like _gaq but here we extend it. In it's core
     * everything pushed to _gas is run through possible hooks and then pushed to
     * _gaq
     */
    window['_gas'] = _gas = new GAS();


    /**
     * Hook for _trackException
     *
     * Watchout for circular calls
     */
    _gas.push(['_addHook', '_trackException', function (exception, message) {
        _gas.push(['_trackEvent',
            'Exception ' + (exception.name || 'Error'),
            message || exception.message || exception,
            url
        ]);
        return false;
    }]);

    /**
     * Hook to enable Debug Mode
     */
    _gas.push(['_addHook', '_setDebug', function (set_debug) {
        _gas.debug_mode = !!set_debug;
    }]);

    /**
     * Hook to Remove other Hooks
     *
     * It will remove the last inserted hook from a _gas function.
     *
     * @param {string} func _gas Function Name to remove Hooks from.
     * @return {boolean} Always returns false.
     */
    _gas.push(['_addHook', '_popHook', function (func) {
        var arr = _gas._hooks[func];
        if (arr && arr.pop) {
            arr.pop();
        }
        return false;
    }]);

    /**
     * Hook to set the default tracker.
     *
     * The default tracker is the nameless tracker that is pushed into _gaq_push
     */
    _gas.push(['_addHook', '_gasSetDefaultTracker', function (tname) {
        _gas._default_tracker = tname;
        return false;
    }]);
    /**
     * Hook to sanity check trackEvents
     *
     * The value is rounded and parsed to integer.
     * Negative values are sent as zero.
     * If val is NaN than it is sent as zero.
     */
    _gas.push(['_addHook', '_trackEvent', function () {
        var args = slice.call(arguments);
        if (args[3]) {
            args[3] = (args[3] < 0 ? 0 : Math.round(args[3])) || 0;
        }
        return args;
    }]);

    /**
     * GAS - Google Analytics on Steroids
     *
     * Download Tracking Plugin
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the GPLv3 license.
     *
     * @author Eduardo Cereto <eduardocereto@gmail.com>
     */

    /**
     * Extracts the file extension and check it against a list
     *
     * Will extract the extensions from a url and check if it matches one of
     * possible options. Used to verify if a url corresponds to a download link.
     *
     * @this {GasHelper} GA Helper object.
     * @param {string} src The url to check.
     * @param {Array} extensions an Array with strings containing the possible
     * extensions.
     * @return {boolean|string} the file extension or false.
     */
    function _checkFile(src, extensions) {
        if (typeof src !== 'string') {
            return false;
        }
        var ext = src.split('?')[0];
        ext = ext.split('.');
        ext = ext[ext.length - 1];
        if (ext && this.inArray(extensions, ext)) {
            return ext;
        }
        return false;
    }

    /**
     * Register the event to listen to downloads
     *
     * @this {GasHelper} GA Helper object.
     * @param {Array|object} opts List of possible extensions for download
     * links.
     */
    var _trackDownloads = function (opts) {
        var gh = this;

        if (!gh._downloadTracked) {
            gh._downloadTracked = true;
        } else {
            //Oops double tracking detected.
            return;
        }
        if (!opts) {
            opts = {'extensions': []};
        } else if (typeof opts === 'string') {
            // support legacy opts as String of extensions
            opts = {'extensions': opts.split(',')};
        } else if (opts.length >= 1) {
            // support legacy opts Array of extensions
            opts = {'extensions': opts};
        }
        opts['category'] = opts['category'] || 'Download';

        var ext = 'xls,xlsx,doc,docx,ppt,pptx,pdf,txt,zip';
        ext += ',rar,7z,gz,tgz,exe,wma,mov,avi,wmv,mp3,mp4,csv,tsv,mobi,epub.swf';
        ext = ext.split(',');
        opts['extensions'] = opts['extensions'].concat(ext);

        gh._liveEvent('a', 'mousedown', function (e) {
            var el = this;
            if (el.href) {
                var ext = _checkFile.call(gh,
                    el.href, opts['extensions']
                );
                if (ext) {
                    _gas.push(['_trackEvent',
                        opts['category'], ext, el.href
                    ]);
                }
            }
        });
        return false;
    };

    /**
     * GAA Hook, receive the extensions to extend default extensions. And trigger
     * the binding of the events.
     *
     * @param {string|Array|object} opts GAs Options. Also backward compatible
     * with array or string of extensions.
     */
    _gas.push(['_addHook', '_gasTrackDownloads', _trackDownloads]);

// Old API to be deprecated on v2.0
    _gas.push(['_addHook', '_trackDownloads', _trackDownloads]);

    /**
     * GAS - Google Analytics on Steroids
     *
     * Outbound Link Tracking Plugin
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the GPLv3 license.
     *
     * @author Eduardo Cereto <eduardocereto@gmail.com>
     */

    /**
     * Triggers the Outbound Link Tracking on the page
     *
     * @this {object} GA Helper object.
     * @param {object} opts Custom options for Outbound Links.
     */
    var _gasTrackOutboundLinks = function (opts) {
        if (!this._outboundTracked) {
            this._outboundTracked = true;
        } else {
            //Oops double tracking detected.
            return;
        }
        var gh = this;
        if (!opts) {
            opts = {};
        }
        opts['category'] = opts['category'] || 'Outbound';

        gh._liveEvent('a', 'mousedown', function (e) {
            var l = this;
            if (
                (l.protocol === 'http:' || l.protocol === 'https:') &&
                    sindexOf.call(l.hostname, document.location.hostname) === -1)
            {
                var path = (l.pathname + l.search + ''),
                    utm = sindexOf.call(path, '__utm');
                if (utm !== -1) {
                    path = path.substring(0, utm);
                }
                _gas.push(['_trackEvent',
                    opts['category'],
                    l.hostname,
                    path
                ]);
            }

        });
    };

    _gas.push(['_addHook', '_gasTrackOutboundLinks', _gasTrackOutboundLinks]);

// Old API to be deprecated on v2.0
    _gas.push(['_addHook', '_trackOutboundLinks', _gasTrackOutboundLinks]);


    /**
     * GAS - Google Analytics on Steroids
     *
     * MailTo tracking plugin
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the GPLv3 license.
     */

    /**
     * GAS plugin to track mailto: links
     *
     * @param {object} opts GAS Options.
     */
    var _gasTrackMailto = function (opts) {
        if (!this._mailtoTracked) {
            this._mailtoTracked = true;
        } else {
            //Oops double tracking detected.
            return;
        }

        if (!opts) {
            opts = {};
        }
        opts['category'] = opts['category'] || 'Mailto';

        this._liveEvent('a', 'mousedown', function (e) {
            var el = e.target;
            if (el && el.href && el.href.toLowerCase &&
                sindexOf.call(el.href.toLowerCase(), 'mailto:') === 0) {
                _gas.push(['_trackEvent', opts['category'], el.href.substr(7)]);
            }
        });
        return false;
    };
    _gas.push(['_addHook', '_gasTrackMailto', _gasTrackMailto]);

// Old API to be deprecated on v2.0
    _gas.push(['_addHook', '_trackMailto', _gasTrackMailto]);


// -- gasStartYoutube--

    /**
     * GAS - Google Analytics on Steroids
     *
     * YouTube Video Tracking Plugin
     *
     * Copyright 2011, Cardinal Path and Direct Performance
     * Licensed under the GPLv3 license.
     *
     * @author Eduardo Cereto <eduardocereto@gmail.com>
     */

    /**
     * Array of percentage to fire events.
     */
    var _ytTimeTriggers = [];
    var _ytOpts;


    /**
     * Used to map each vid to a set of timeTriggers and it's pool timer
     */
    var _ytPoolMaps = {};

    function _ytPool(target, hash) {
        if (_ytPoolMaps[hash] === undefined ||
            _ytPoolMaps[hash].timeTriggers.length <= 0) {
            return false;
        }
        var p = target['getCurrentTime']() / target['getDuration']() * 100;
        if (p >= _ytPoolMaps[hash].timeTriggers[0]) {
            var action = _ytPoolMaps[hash].timeTriggers.shift();
            _gas.push([
                '_trackEvent',
                _ytOpts['category'],
                action + '%',
                target['getVideoUrl']()
            ]);
        }
        _ytPoolMaps[hash].timer = setTimeout(_ytPool, 1000, target, hash);
    }

    function _ytStopPool(target) {
        var h = target['getVideoUrl']();
        if (_ytPoolMaps[h] && _ytPoolMaps[h].timer) {
            _ytPool(target, h); // Pool one last time before clearing it.
            clearTimeout(_ytPoolMaps[h].timer);
        }
    }

    function _ytStartPool(target) {
        if (_ytTimeTriggers && _ytTimeTriggers.length) {
            var h = target['getVideoUrl']();
            if (_ytPoolMaps[h]) {
                _ytStopPool(target);
            } else {
                _ytPoolMaps[h] = {};
                _ytPoolMaps[h].timeTriggers = slice.call(_ytTimeTriggers);
            }
            _ytPoolMaps[h].timer = setTimeout(_ytPool, 1000, target, h);
        }
    }


    /**
     * Called when the Video State changes
     *
     * We are currently tracking only finish, play and pause events
     *
     * @param {Object} event the event passed by the YT api.
     */
    function _ytStateChange(event) {
        var action = '';
        switch (event['data']) {
            case 0:
                action = 'finish';
                _ytStopPool(event['target']);
                break;
            case 1:
                action = 'play';
                _ytStartPool(event['target']);
                break;
            case 2:
                action = 'pause';
                _ytStopPool(event['target']);
                break;
        }
        if (action) {
            _gas.push(['_trackEvent',
                _ytOpts['category'], action, event['target']['getVideoUrl']()
            ]);
        }
    }

    /**
     * Called when the player fires an Error Event
     *
     * @param {Object} event the event passed by the YT api.
     */
    function _ytError(event) {
        _gas.push(['_trackEvent',
            _ytOpts['category'],
            'error (' + event['data'] + ')',
            event['target']['getVideoUrl']()
        ]);
    }

    /**
     * Looks for object/embed youtube videos and migrate them to the iframe method
     *  so it tries to track them
     */
    function _ytMigrateObjectEmbed() {
        var objs = document.getElementsByTagName('object');
        var pars, ifr, ytid;
        var r = /(https?:\/\/www\.youtube(-nocookie)?\.com[^\/]*).*\/v\/([^&?]+)/;
        for (var i = 0; i < objs.length; i++) {
            pars = objs[i].getElementsByTagName('param');
            for (var j = 0; j < pars.length; j++) {
                if (pars[j].name === 'movie' && pars[j].value) {
                    // Replace the object with an iframe
                    ytid = pars[j].value.match(r);
                    if (ytid && ytid[1] && ytid[3]) {
                        ifr = document.createElement('iframe');
                        ifr.src = ytid[1] + '/embed/' + ytid[3] + '?enablejsapi=1';
                        ifr.width = objs[i].width;
                        ifr.height = objs[i].height;
                        ifr.setAttribute('frameBorder', '0');
                        ifr.setAttribute('allowfullscreen', '');
                        objs[i].parentNode.insertBefore(ifr, objs[i]);
                        objs[i].parentNode.removeChild(objs[i]);
                        // Since we removed the object the Array changed
                        i--;
                    }
                    break;
                }
            }
        }
    }

    /**
     * Triggers the YouTube Tracking on the page
     *
     * Only works for the iframe tag. The video must have the parameter
     * enablejsapi=1 on the url in order to make the tracking work.
     *
     * @param {(object)} opts GAS Options object.
     */
    function _trackYoutube(opts) {
        var force = opts['force'];
        var opt_timeTriggers = opts['percentages'];
        if (force) {
            try {
                _ytMigrateObjectEmbed();
            }catch (e) {
                _gas.push(['_trackException', e,
                    'GAS Error on youtube.js:_ytMigrateObjectEmbed'
                ]);
            }
        }

        var youtube_videos = [];
        var iframes = document.getElementsByTagName('iframe');
        for (var i = 0; i < iframes.length; i++) {
            if (sindexOf.call(iframes[i].src, '//www.youtube.com/embed') > -1) {
                if (sindexOf.call(iframes[i].src, 'enablejsapi=1') < 0) {
                    if (force) {
                        // Reload the video enabling the api
                        if (sindexOf.call(iframes[i].src, '?') < 0) {
                            iframes[i].src += '?enablejsapi=1';
                        } else {
                            iframes[i].src += '&enablejsapi=1';
                        }
                    } else {
                        // We can't track players that don't have api enabled.
                        continue;
                    }
                }
                youtube_videos.push(iframes[i]);
            }
        }
        if (youtube_videos.length > 0) {
            if (opt_timeTriggers && opt_timeTriggers.length) {
                _ytTimeTriggers = opt_timeTriggers;
            }
            // this function will be called when the youtube api loads
            window['onYouTubePlayerAPIReady'] = function () {
                var p;
                for (var i = 0; i < youtube_videos.length; i++) {
                    p = new window['YT']['Player'](youtube_videos[i]);
                    p.addEventListener('onStateChange', _ytStateChange);
                    p.addEventListener('onError', _ytError);
                }
            };
            // load the youtube player api
            var tag = document.createElement('script');
            //XXX use document.location.protocol
            var protocol = 'http:';
            if (document.location.protocol === 'https:') {
                protocol = 'https:';
            }
            tag.src = protocol + '//www.youtube.com/player_api';
            tag.type = 'text/javascript';
            tag.async = true;
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }
    }

    var _gasTrackYoutube = function (opts) {
        // Support for legacy parameters
        var args = slice.call(arguments);
        if (args[0] && (typeof args[0] === 'boolean' || args[0] === 'force')) {
            opts = {'force': !!args[0]};
            if (args[1] && args[1].length) {
                opts['percentages'] = args[1];
            }
        }

        opts = opts || {};
        opts['force'] = opts['force'] || false;
        opts['category'] = opts['category'] || 'YouTube Video';
        opts['percentages'] = opts['percentages'] || [];

        _ytOpts = opts;
        var gh = this;
        gh._DOMReady(function () {
            _trackYoutube.call(gh, opts);
        });
        return false;
    };

    _gas.push(['_addHook', '_gasTrackYoutube', _gasTrackYoutube]);

// Old API to be deprecated on v2.0
    _gas.push(['_addHook', '_trackYoutube', _gasTrackYoutube]);

// -- gasEndYoutube--

    /**
     * Wrap-up
     */
// Execute previous functions
    while (_gas._queue.length > 0) {
        _gas.push(_gas._queue.shift());
    }

// Import ga.js
    if (typeof window._gat === 'undefined') {
        (function () {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = (
                'https:' === document.location.protocol ?
                    'https://ssl' :
                    'http://www'
                ) +
                '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        }());
    }

})(window);

// -- gasEnd--


// Delayed loading of GSA_CPwrapGA
_gas.push(function () {
    this._DOMReady(function () {
        try {
            var oGSA_CPwrapGA = new GSA_CPwrapGA();

            if (!document._gsaDelayGA)
                oGSA_CPwrapGA.onEveryPage();
        } catch (e) {
            try {
                console.log(e.message);
                console.log(e.stack.toString());
            } catch (e) {}

        }
    });
});

