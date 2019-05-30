/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

define(["exports","jquery","./Component"],function(exports,_jquery,_Component2){"use strict";Object.defineProperty(exports,"__esModule",{value:!0});var _jquery2=babelHelpers.interopRequireDefault(_jquery),_Component3=babelHelpers.interopRequireDefault(_Component2),$BODY=(0,_jquery2.default)("body"),_class=function(_Component){function _class(){var _ref;babelHelpers.classCallCheck(this,_class);for(var _len=arguments.length,args=Array(_len),_key=0;_key<_len;_key++)args[_key]=arguments[_key];var _this=babelHelpers.possibleConstructorReturn(this,(_ref=_class.__proto__||Object.getPrototypeOf(_class)).call.apply(_ref,[this].concat(args)));return _this.$scroll=_this.$el.find(".page-aside-scroll"),_this.scrollable=_this.$scroll.asScrollable({namespace:"scrollable",contentSelector:"> [data-role='content']",containerSelector:"> [data-role='container']"}).data("asScrollable"),_this}return babelHelpers.inherits(_class,_Component),babelHelpers.createClass(_class,[{key:"processed",value:function(){var _this2=this;($BODY.is(".page-aside-fixed")||$BODY.is(".page-aside-scroll"))&&this.$el.on("transitionend",function(){_this2.scrollable.update()}),Breakpoints.on("change",function(){var current=Breakpoints.current().name;$BODY.is(".page-aside-fixed")||$BODY.is(".page-aside-scroll")||("xs"===current?(_this2.scrollable.enable(),_this2.$el.on("transitionend",function(){_this2.scrollable.update()})):(_this2.$el.off("transitionend"),_this2.scrollable.update()))}),(0,_jquery2.default)(document).on("click.pageAsideScroll",".page-aside-switch",function(){_this2.$el.hasClass("open")?_this2.$el.removeClass("open"):(_this2.scrollable.update(),_this2.$el.addClass("open"))}),(0,_jquery2.default)(document).on("click.pageAsideScroll",'[data-toggle="collapse"]',function(e){var $trigger=(0,_jquery2.default)(e.target);$trigger.is('[data-toggle="collapse"]')||($trigger=$trigger.parents('[data-toggle="collapse"]'));var href=void 0,target=$trigger.attr("data-target")||(href=$trigger.attr("href"))&&href.replace(/.*(?=#[^\s]+$)/,"");"site-navbar-collapse"===(0,_jquery2.default)(target).attr("id")&&_this2.scrollable.update()})}}]),_class}(_Component3.default);exports.default=_class});