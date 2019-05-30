/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

define(["exports","jquery"],function(exports,_jquery){"use strict";function getPlugin(name){return void 0!==plugins[name]&&plugins[name]}function getDefaults(name){var PluginClass=getPlugin(name);return PluginClass?PluginClass.getDefaults():{}}Object.defineProperty(exports,"__esModule",{value:!0}),exports.pluginFactory=exports.getDefaults=exports.getPlugin=exports.getPluginAPI=exports.Plugin=void 0;var _jquery2=babelHelpers.interopRequireDefault(_jquery),plugins={},apis={},Plugin=function(){function Plugin($el){var options=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{};babelHelpers.classCallCheck(this,Plugin),this.name=this.getName(),this.$el=$el,this.options=options,this.isRendered=!1}return babelHelpers.createClass(Plugin,[{key:"getName",value:function(){return"plugin"}},{key:"render",value:function(){if(!_jquery2.default.fn[this.name])return!1;this.$el[this.name](this.options)}},{key:"initialize",value:function(){if(this.isRendered)return!1;this.render(),this.isRendered=!0}}],[{key:"getDefaults",value:function(){return{}}},{key:"register",value:function(name,obj){void 0!==obj&&(plugins[name]=obj,void 0!==obj.api&&Plugin.registerApi(name,obj))}},{key:"registerApi",value:function(name,obj){var api=obj.api();if("string"==typeof api){var _api=obj.api().split("|"),event=_api[0]+".plugin."+name,func=_api[1]||"render",callback=function(e){var $el=(0,_jquery2.default)(this),plugin=$el.data("pluginInstance");plugin||((plugin=new obj($el,_jquery2.default.extend(!0,{},getDefaults(name),$el.data()))).initialize(),$el.data("pluginInstance",plugin)),plugin[func](e)};apis[name]=function(selector,context){context?((0,_jquery2.default)(context).off(event),(0,_jquery2.default)(context).on(event,selector,callback)):(0,_jquery2.default)(selector).on(event,callback)}}else"function"==typeof api&&(apis[name]=api)}}]),Plugin}();exports.default=Plugin,exports.Plugin=Plugin,exports.getPluginAPI=function(name){return void 0===name?apis:apis[name]},exports.getPlugin=getPlugin,exports.getDefaults=getDefaults,exports.pluginFactory=function(name,$el){var options=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},PluginClass=getPlugin(name);if(PluginClass&&void 0===PluginClass.api)return new PluginClass($el,_jquery2.default.extend(!0,{},getDefaults(name),options));if(_jquery2.default.fn[name]){var plugin=new Plugin($el,options);return plugin.getName=function(){return name},plugin.name=name,plugin}return PluginClass.api,!1}});