/**
 * This file is part of Goodahead_Core extension
 *
 * This extension is supplied with every Goodahead extension and provide common
 * features, used by Goodahead extensions.
 *
 * Copyright (C) 2013 Goodahead Ltd. (http://www.goodahead.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * and GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Goodahead
 * @package    Goodahead_Core
 * @copyright  Copyright (c) 2013 Goodahead Ltd. (http://www.goodahead.com)
 * @license    http://www.gnu.org/licenses/lgpl-3.0-standalone.html
 */

if (typeof Goodahead !== 'object') {
    var Goodahead = {};
}

/*global
    Prototype: false, Element: false, Translator: false, Class: false,
    $$: false, $: false, $H: false,
*/

/*jslint regexp: true, browser: true, maxerr: 50, indent: 4, nomen: true */

(function () {

    'use strict';

    Object.extend(Goodahead, {
        VERSION: '0.1.0',
        // REQUIRED_PROTOTYPE: '1.6.0.3',
        includePath: '',
        useTranslate: null,
        modules: {},
        scriptUrl: null,
        scriptParams: {},
        baseUrl: null,

        require: function (scriptName, onload) {
            var module, script;
            module = this._getModule(scriptName);
            if (module.state === 'pending') {
                var additionalParams = [];
                var additionalParamsStr = '';

                for (var param in this.scriptParams) {
                    if (this.scriptParams.hasOwnProperty(param)) {
                        additionalParams.push(param.toString() + '=' + encodeURI(this.scriptParams[param]));
                    }
                }

                if (additionalParams.length > 0) {
                    additionalParamsStr = '?' + additionalParams.join('&');
                }
                try {
                    script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = this.includePath + scriptName + '.js'
                        + additionalParamsStr.toString();
                    document.getElementsByTagName('head')[0].appendChild(script);
                    module.state = 'fetching';
                } catch (e) {
                    document.write(
                        '<script type="text/javascript" src="'
                            + this.includePath
                            + scriptName
                            + '.js'
                            + additionalParamsStr.toString()
                            +'"><\/script>'
                    );
                    module.state = 'fetching';
                }
            }
            if ('function' === typeof onload) {
                this.onLoad(scriptName, onload);
            }
        },

        translate: function (text) {
            try {
                if (this.useTranslate === null) {
                    this.useTranslate = 'undefined' === typeof Translator;
                }
                if (this.useTranslate) {
                    return Translator.translate(text);
                }
                return text;
            } catch (e) {
                return text;
            }
        },

        init: function () {
            if (
                'undefined' === typeof Prototype
                    || 'undefined' === typeof Element
                    || 'undefined' === typeof Element.Methods
            ) {
                throw (this.translate(
                    "Goodahead Modules requires Prototype JavaScript framework"
                ));
            }

            var jsPattern = /goodahead.js(\?.*)?$/;

            var currentScript = $$('script[src]').findAll(function (script) {
                return script.src.match(jsPattern);
            }).last();

            if (currentScript) {
                this.scriptUrl = currentScript.src;
                this.includePath = currentScript.src.replace(jsPattern, '');

                var localParams = this.scriptUrl.split("?").pop().split("&");
                localParams.each(function(param) {
                    var paramParts = param.split("=");
                    if (paramParts.length > 1) {
                        this.scriptParams[paramParts[0]] = paramParts[1];
                    }
                }.bind(this));
            } else {
                jsPattern = /media\/js(.*)?$/;

                currentScript = $$('script[src]').findAll(function (script) {
                    return script.src.match(jsPattern);
                }).last();

                if (currentScript) {
                    this.scriptUrl = currentScript.src;
                    this.includePath = currentScript.src.replace(jsPattern, '') + 'js/goodahead/';
                    this.scriptParams['v'] = currentScript.src
                        .substring(currentScript.src.lastIndexOf('/')+1).replace(/.js(.*)?$/, '');
                } else {
                    throw (this.translate(
                        "Can't initialize js folder location!"
                    ));
                }
            }

            var urlParts = this.scriptUrl.split('/');
            if (urlParts.length > 2) {
                urlParts.splice(3, urlParts.length-3);
                this.baseUrl = urlParts.join('/');
            }

            currentScript = $('goodahead-js-init');

            if (currentScript && currentScript.getAttribute('jsPath') && this.baseUrl) {
                this.scriptUrl = currentScript.src;
                this.includePath = this.baseUrl.toString()
                    + currentScript.getAttribute('jsPath').toString()
                    + 'goodahead/';
            }

            /*
             To initialize js folder correctly, you should not add this script
             using addJs method. Use JsInit block instead.
             */

            var queryParams = document.URL.toQueryParams();
            if (typeof queryParams === 'object'
                && typeof queryParams['v'] === 'string') {
                this.scriptParams['v'] = encodeURI(queryParams['v']);
            }
        },

        register: function (moduleName, dependencies, initializer) {
            var module = this._getModule(moduleName);
            module.state = 'initializing';

            if (typeof dependencies === 'string') {
                dependencies = [dependencies];
            }
            if (dependencies instanceof Array) {
                dependencies.each(function (dependency) {
                    this.depends(moduleName, dependency);
                }.bind(this));
            }

            if (typeof initializer === 'function') {
                module.initializer = initializer;
            }
            if (module.requiredModules.keys().length === 0) {
                this._initialize(moduleName);
            }
        },

        depends: function (moduleName, dependsFrom) {
            this.require(dependsFrom);
            if (this.getModuleState(moduleName) !== 'loaded') {
                var requiredModule, dependentModule;
                requiredModule = this._getModule(dependsFrom);
                requiredModule.dependentModules.push(moduleName);
                if (requiredModule.state !== 'loaded') {
                    dependentModule = this._getModule(moduleName);
                    dependentModule.requiredModules.set(dependsFrom, true);
                }
            }
        },

        onLoad: function (moduleName, method) {
            this.require(moduleName);
            var module = this._getModule(moduleName);
            if (module.state === 'loaded') {
                method();
            } else {
                module.onLoad.push(method);
            }
        },

        _initialize: function (moduleName) {
            var module = this._getModule(moduleName);
            if (module.state === 'initializing') {
                if (
                    module.hasOwnProperty('initializer')
                        && 'function' === typeof module.initializer
                ) {
                    try {
                        module.initializer();
                        module.state = 'loaded';
                    } catch (e) {
                        module.state = 'error';
                    }
                } else {
                    module.state = 'loaded';
                }
                if (module.state === 'loaded') {
                    module.onLoad.each(function (onLoad) {
                        onLoad();
                    }.bind(this));
                    module.dependentModules.each(function (dependentModuleName) {
                        var dependentModule = this._getModule(dependentModuleName);
                        dependentModule.requiredModules.unset(moduleName);
                        if (dependentModule.requiredModules.keys().length === 0) {
                            this._initialize(dependentModuleName);
                        }
                    }.bind(this));
                }
            }
        },

        getModuleState: function (moduleName) {
            var module = this._getModule(moduleName, true);
            if (module === false) {
                return false;
            }
            return module.state;
        },

        getModuleFetched: function (moduleName) {
            var state = this.getModuleState(moduleName);
            return !(false === state || 'fetching' === state || 'pending' === state);
        },

        _getModule: function (moduleName, doNotCreate) {
            if (!this.modules.hasOwnProperty(moduleName)) {
                if (doNotCreate === true) {
                    return false;
                }
                this.modules[moduleName] = {
                    state: 'pending',
                    requiredModules: $H({}),
                    dependentModules: [],
                    onLoad: []
                };
            }
            return this.modules[moduleName];
        }
    });
    Goodahead.init();
}());