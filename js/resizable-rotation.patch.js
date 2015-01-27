	/**
	* 2010-2014 Tuni-Soft
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Academic Free License (AFL 3.0)
	* It is available through the world-wide-web at this URL:
	* http://opensource.org/licenses/afl-3.0.php
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to tunisoft.solutions@gmail.com so we can send you a copy immediately.
	*
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade this module to newer
	* versions in the future. If you wish to customize the module for your
	* needs please refer to
	* http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
	* for more information.
	*
	* @author    Tunis-Soft <tunisoft.solutions@gmail.com>
	* @copyright 2010-2014 Tuni-Soft
	* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*/

$(document).ready(function(){function e(e){return parseInt(e,10)||0}function t(e){var t=window.getComputedStyle(e,null),i=t.getPropertyValue("-webkit-transform")||t.getPropertyValue("-moz-transform")||t.getPropertyValue("-ms-transform")||t.getPropertyValue("-o-transform")||t.getPropertyValue("transform")||null;if(i&&"none"!=i){var s=i.split("(")[1];s=s.split(")")[0],s=s.split(",");for(var n=s[0],l=s[1],o=Math.round(Math.atan2(l,n)*(180/Math.PI));o>=360;)o=360-o;for(;0>o;)o=360+o;return o}return 0}function i(e){return isNaN(parseFloat(e))?0:parseFloat(e)}function s(e){return Math.round(100*(e+1e-5))/100}$.getCorrection=function(e,t,i,s,n){var n=n*Math.PI/180,l=-e/2,o=t/2,a=o*Math.sin(n)+l*Math.cos(n),r=o*Math.cos(n)-l*Math.sin(n),h={left:a-l,top:r-o},c=e+i,d=t+s,l=-c/2,o=d/2,a=o*Math.sin(n)+l*Math.cos(n),r=o*Math.cos(n)-l*Math.sin(n),u={left:a-l,top:r-o},p={left:u.left-h.left,top:u.top-h.top};return p},$.ui.resizable.prototype._mouseStart=function(t){var i,s,n,l=this.options,o=this.element;return this.resizing=!0,this._renderProxy(),i=e(this.helper.css("left")),s=e(this.helper.css("top")),l.containment&&(i+=$(l.containment).scrollLeft()||0,s+=$(l.containment).scrollTop()||0),this.offset=this.helper.offset(),this.position={left:i,top:s},this.size=this._helper?{width:this.helper.width(),height:this.helper.height()}:{width:o.width(),height:o.height()},this.originalSize=this._helper?{width:o.outerWidth(),height:o.outerHeight()}:{width:o.width(),height:o.height()},this.sizeDiff={width:o.outerWidth()-o.width(),height:o.outerHeight()-o.height()},this.originalPosition={left:i,top:s},this.originalMousePosition={left:t.pageX,top:t.pageY},this.lastData=this.originalPosition,this.aspectRatio="number"==typeof l.aspectRatio?l.aspectRatio:this.originalSize.width/this.originalSize.height||1,n=$(".ui-resizable-"+this.axis).css("cursor"),$("body").css("cursor","auto"===n?this.axis+"-resize":n),o.addClass("ui-resizable-resizing"),this._propagate("start",t),!0},$.ui.resizable.prototype._mouseDrag=function(e){var n,l=t(this.element[0]),o=l*Math.PI/180,a=this.helper,r={},h=this.originalMousePosition,c=this.axis,d=this.position.top,u=this.position.left,p=this.size.width,f=this.size.height,m=e.pageX-h.left||0,g=e.pageY-h.top||0,v=this._change[c],b=this.size.width,w=this.size.height;if(!v)return!1;var y=Math.cos(o),M=Math.sin(o);ndx=m*y+g*M,ndy=g*y-m*M,m=ndx,g=ndy,n=v.apply(this,[e,m,g]),this._updateVirtualBoundaries(e.shiftKey),(this._aspectRatio||e.shiftKey)&&(n=this._updateRatio(n,e)),n=this._respectSize(n,e);var C={left:this.position.left,top:this.position.top};this._updateCache(n),this.position={left:C.left,top:C.top};var x={left:i(n.left||this.lastData.left)-i(this.lastData.left),top:i(n.top||this.lastData.top)-i(this.lastData.top)},U={};U.left=x.left*y-x.top*M,U.top=x.top*y+x.left*M,U.left=s(U.left),U.top=s(U.top),this.position.left+=U.left,this.position.top+=U.top,this.lastData={left:i(n.left||this.lastData.left),top:i(n.top||this.lastData.top)},this._propagate("resize",e);var z=b-this.size.width,D=w-this.size.height,S=$.getCorrection(b,w,z,D,l);return this.position.left+=S.left,this.position.top-=S.top,this.position.top!==d&&(r.top=this.position.top+"px"),this.position.left!==u&&(r.left=this.position.left+"px"),this.size.width!==p&&(r.width=this.size.width+"px"),this.size.height!==f&&(r.height=this.size.height+"px"),a.css(r),!this._helper&&this._proportionallyResizeElements.length&&this._proportionallyResize(),$.isEmptyObject(r)||this._trigger("resize",e,this.ui()),!1}});