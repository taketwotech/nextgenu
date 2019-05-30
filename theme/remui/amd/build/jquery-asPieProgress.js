/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

!function(global,factory){if("function"==typeof define&&define.amd)define(["jquery"],factory);else if("undefined"!=typeof exports)factory(require("jquery"));else{var mod={exports:{}};factory(global.jQuery),global.jqueryAsPieProgressEs=mod.exports}}(this,function(_jquery){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}var _jquery2=function(obj){return obj&&obj.__esModule?obj:{default:obj}}(_jquery),_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(obj){return typeof obj}:function(obj){return obj&&"function"==typeof Symbol&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj},_createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),SvgElement=function(tag,attrs){var elem=document.createElementNS("http://www.w3.org/2000/svg",tag);if(!attrs)return elem;for(var key in attrs)Object.hasOwnProperty.call(attrs,key)&&elem.setAttribute(key,attrs[key]);return elem};Date.now||(Date.now=function(){return(new Date).getTime()});for(var vendors=["webkit","moz"],i=0;i<vendors.length&&!window.requestAnimationFrame;++i){var vp=vendors[i];window.requestAnimationFrame=window[vp+"RequestAnimationFrame"],window.cancelAnimationFrame=window[vp+"CancelAnimationFrame"]||window[vp+"CancelRequestAnimationFrame"]}!/iP(ad|hone|od).*OS (6|7|8)/.test(window.navigator.userAgent)&&window.requestAnimationFrame&&window.cancelAnimationFrame||function(){var lastTime=0;window.requestAnimationFrame=function(callback){var now=getTime(),nextTime=Math.max(lastTime+16,now);return setTimeout(function(){callback(lastTime=nextTime)},nextTime-now)},window.cancelAnimationFrame=clearTimeout}();var getTime=function(){return void 0!==window.performance&&window.performance.now?window.performance.now():Date.now()},isPercentage=function(n){return"string"==typeof n&&-1!==n.indexOf("%")},svgSupported="createElementNS"in document&&new SvgElement("svg",{}).createSVGRect,easingBezier=function(mX1,mY1,mX2,mY2){var a=function(aA1,aA2){return 1-3*aA2+3*aA1},b=function(aA1,aA2){return 3*aA2-6*aA1},c=function(aA1){return 3*aA1},calcBezier=function(aT,aA1,aA2){return((a(aA1,aA2)*aT+b(aA1,aA2))*aT+c(aA1))*aT},getSlope=function(aT,aA1,aA2){return 3*a(aA1,aA2)*aT*aT+2*b(aA1,aA2)*aT+c(aA1)},getTForX=function(aX){for(var aGuessT=aX,_i=0;_i<4;++_i){var currentSlope=getSlope(aGuessT,mX1,mX2);if(0===currentSlope)return aGuessT;aGuessT-=(calcBezier(aGuessT,mX1,mX2)-aX)/currentSlope}return aGuessT};return mX1===mY1&&mX2===mY2?{css:"linear",fn:function(aX){return aX}}:{css:"cubic-bezier("+mX1+","+mY1+","+mX2+","+mY2+")",fn:function(aX){return calcBezier(getTForX(aX),mY1,mY2)}}},EASING={ease:easingBezier(.25,.1,.25,1),linear:easingBezier(0,0,1,1),"ease-in":easingBezier(.42,0,1,1),"ease-out":easingBezier(0,0,.58,1),"ease-in-out":easingBezier(.42,0,.58,1)},DEFAULTS={namespace:"asPieProgress",classes:{svg:"pie_progress__svg",element:"pie_progress",number:"pie_progress__number",content:"pie_progress__content"},min:0,max:100,goal:100,size:160,speed:15,barcolor:"#ef1e25",barsize:"4",trackcolor:"#f2f2f2",fillcolor:"none",easing:"ease",numberCallback:function(n){return Math.round(this.getPercentage(n))+"%"},contentCallback:null},asPieProgress=function(){function asPieProgress(element,options){_classCallCheck(this,asPieProgress),this.element=element,this.$element=(0,_jquery2.default)(element),this.options=_jquery2.default.extend(!0,{},DEFAULTS,options,this.$element.data()),this.namespace=this.options.namespace,this.classes=this.options.classes,this.easing=EASING[this.options.easing]||EASING.ease,this.$element.addClass(this.classes.element),this.min=this.$element.attr("aria-valuemin"),this.max=this.$element.attr("aria-valuemax"),this.min=this.min?parseInt(this.min,10):this.options.min,this.max=this.max?parseInt(this.max,10):this.options.max,this.first=this.$element.attr("aria-valuenow"),this.first=this.first?parseInt(this.first,10):this.options.first?this.options.first:this.min,this.now=this.first,this.goal=this.options.goal,this._frameId=null,this.initialized=!1,this._trigger("init"),this.init()}return _createClass(asPieProgress,[{key:"init",value:function(){this.$number=this.$element.find("."+this.classes.number),this.$content=this.$element.find("."+this.classes.content),this.size=this.options.size,this.width=this.size,this.height=this.size,this.prepare(),this.initialized=!0,this._trigger("ready")}},{key:"prepare",value:function(){svgSupported&&(this.svg=new SvgElement("svg",{version:"1.1",preserveAspectRatio:"xMinYMin meet",viewBox:"0 0 "+this.width+" "+this.height}),this.buildTrack(),this.buildBar(),(0,_jquery2.default)('<div class="'+this.classes.svg+'"></div>').append(this.svg).appendTo(this.$element))}},{key:"buildTrack",value:function(){var height=this.size,cx=this.size/2,cy=height/2,barsize=this.options.barsize,ellipse=new SvgElement("ellipse",{rx:cx-barsize/2,ry:cy-barsize/2,cx:cx,cy:cy,stroke:this.options.trackcolor,fill:this.options.fillcolor,"stroke-width":barsize});this.svg.appendChild(ellipse)}},{key:"buildBar",value:function(){if(svgSupported){var path=new SvgElement("path",{fill:"none","stroke-width":this.options.barsize,stroke:this.options.barcolor});this.bar=path,this.svg.appendChild(path),this._drawBar(this.first),this._updateBar()}}},{key:"_drawBar",value:function(n){if(svgSupported){this.barGoal=n;var height=this.size,cx=this.size/2,cy=height/2,barsize=this.options.barsize,r=Math.min(cx,cy)-barsize/2;this.r=r;var percentage=this.getPercentage(n);100===percentage&&(percentage-=1e-4);var endAngle=0+percentage*Math.PI*2/100,x1=cx+r*Math.sin(0),x2=cx+r*Math.sin(endAngle),y1=cy-r*Math.cos(0),y2=cy-r*Math.cos(endAngle),big=0;endAngle-0>Math.PI&&(big=1);var d="M"+x1+","+y1+" A"+r+","+r+" 0 "+big+" 1 "+x2+","+y2;this.bar.setAttribute("d",d)}}},{key:"_updateBar",value:function(){if(svgSupported){var percenage=this.getPercentage(this.now),length=this.bar.getTotalLength(),offset=length*(1-percenage/this.getPercentage(this.barGoal));this.bar.style.strokeDasharray=length+" "+length,this.bar.style.strokeDashoffset=offset}}},{key:"_trigger",value:function(eventType){for(var _len=arguments.length,params=Array(_len>1?_len-1:0),_key=1;_key<_len;_key++)params[_key-1]=arguments[_key];var data=[this].concat(params);this.$element.trigger("asPieProgress::"+eventType,data);var onFunction="on"+(eventType=eventType.replace(/\b\w+\b/g,function(word){return word.substring(0,1).toUpperCase()+word.substring(1)}));"function"==typeof this.options[onFunction]&&this.options[onFunction].apply(this,params)}},{key:"getPercentage",value:function(n){return 100*(n-this.min)/(this.max-this.min)}},{key:"go",value:function(goal){var that=this;this._clear(),isPercentage(goal)&&(goal=parseInt(goal.replace("%",""),10),goal=Math.round(this.min+goal/100*(this.max-this.min))),void 0===goal&&(goal=this.goal),goal>this.max?goal=this.max:goal<this.min&&(goal=this.min),this.barGoal<goal&&this._drawBar(goal);var start=that.now,startTime=getTime(),endTime=startTime+100*Math.abs(start-goal)*that.options.speed/(that.max-that.min);that._frameId=window.requestAnimationFrame(function animation(time){var next=void 0;if(time>endTime)next=goal;else{var distance=(time-startTime)/that.options.speed;next=Math.round(that.easing.fn(distance/100)*(that.max-that.min)),goal>start?(next=start+next)>goal&&(next=goal):(next=start-next)<goal&&(next=goal)}that._update(next),next===goal?(window.cancelAnimationFrame(that._frameId),that._frameId=null,that.now===that.goal&&that._trigger("finish")):that._frameId=window.requestAnimationFrame(animation)})}},{key:"_update",value:function(n){this.now=n,this._updateBar(),this.$element.attr("aria-valuenow",this.now),this.$number.length>0&&"function"==typeof this.options.numberCallback&&this.$number.html(this.options.numberCallback.call(this,[this.now])),this.$content.length>0&&"function"==typeof this.options.contentCallback&&this.$content.html(this.options.contentCallback.call(this,[this.now])),this._trigger("update",n)}},{key:"_clear",value:function(){this._frameId&&(window.cancelAnimationFrame(this._frameId),this._frameId=null)}},{key:"get",value:function(){return this.now}},{key:"start",value:function(){this._clear(),this._trigger("start"),this.go(this.goal)}},{key:"reset",value:function(){this._clear(),this._drawBar(this.first),this._update(this.first),this._trigger("reset")}},{key:"stop",value:function(){this._clear(),this._trigger("stop")}},{key:"finish",value:function(){this._clear(),this._update(this.goal),this._trigger("finish")}},{key:"destroy",value:function(){this.$element.data("asPieProgress",null),this._trigger("destroy")}}],[{key:"registerEasing",value:function(name){for(var _len2=arguments.length,args=Array(_len2>1?_len2-1:0),_key2=1;_key2<_len2;_key2++)args[_key2-1]=arguments[_key2];EASING[name]=easingBezier.apply(void 0,args)}},{key:"getEasing",value:function(name){return EASING[name]}},{key:"setDefaults",value:function(options){_jquery2.default.extend(!0,DEFAULTS,_jquery2.default.isPlainObject(options)&&options)}}]),asPieProgress}(),info={version:"0.4.6"},OtherAsPieProgress=_jquery2.default.fn.asPieProgress,jQueryAsPieProgress=function(options){for(var _this=this,_len3=arguments.length,args=Array(_len3>1?_len3-1:0),_key3=1;_key3<_len3;_key3++)args[_key3-1]=arguments[_key3];if("string"==typeof options){var _ret2=function(){var method=options;if(/^_/.test(method))return{v:!1};if(!/^(get)/.test(method))return{v:_this.each(function(){var instance=_jquery2.default.data(this,"asPieProgress");instance&&"function"==typeof instance[method]&&instance[method].apply(instance,args)})};var instance=_this.first().data("asPieProgress");return instance&&"function"==typeof instance[method]?{v:instance[method].apply(instance,args)}:void 0}();if("object"===(void 0===_ret2?"undefined":_typeof(_ret2)))return _ret2.v}return this.each(function(){(0,_jquery2.default)(this).data("asPieProgress")||(0,_jquery2.default)(this).data("asPieProgress",new asPieProgress(this,options))})};_jquery2.default.fn.asPieProgress=jQueryAsPieProgress,_jquery2.default.asPieProgress=_jquery2.default.extend({setDefaults:asPieProgress.setDefaults,registerEasing:asPieProgress.registerEasing,getEasing:asPieProgress.getEasing,noConflict:function(){return _jquery2.default.fn.asPieProgress=OtherAsPieProgress,jQueryAsPieProgress}},info)});