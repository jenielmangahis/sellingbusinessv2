// source --> https://beta.sellingbusinessesaustralia.com.au/wp-content/uploads/411/620/public/assets/js/advanced.js 
advads={supports_localstorage:function(){"use strict";try{return!(!window||void 0===window.localStorage)&&(window.localStorage.setItem("x","x"),window.localStorage.removeItem("x"),!0)}catch(e){return!1}},max_per_session:function(e,t){var o=1;if(void 0!==t&&0!==parseInt(t)||(t=1),this.cookie_exists(e)){if(this.get_cookie(e)>=t)return!0;o+=parseInt(this.get_cookie(e))}return this.set_cookie(e,o),!1},count_up:function(e,t){var o=1;this.cookie_exists(e)&&(o+=parseInt(this.get_cookie(e))),this.set_cookie(e,o)},set_cookie_exists:function(e){return!!get_cookie(e)||(set_cookie(e,"",0),!1)},get_cookie:function(e){var t,o,n,i=document.cookie.split(";");for(t=0;t<i.length;t++)if(o=i[t].substr(0,i[t].indexOf("=")),n=i[t].substr(i[t].indexOf("=")+1),(o=o.replace(/^\s+|\s+$/g,""))===e)return unescape(n)},set_cookie:function(e,t,o,n,i,s){var r=null==o?null:24*o*60*60;this.set_cookie_sec(e,t,r,n,i,s)},set_cookie_sec:function(e,t,o,n,i,s){var r=new Date;r.setSeconds(r.getSeconds()+parseInt(o)),document.cookie=e+"="+escape(t)+(null==o?"":"; expires="+r.toUTCString())+(null==n?"; path=/":"; path="+n)+(null==i?"":"; domain="+i)+(null==s?"":"; secure")},cookie_exists:function(e){var t=this.get_cookie(e);return null!==t&&""!==t&&void 0!==t},move:function(e,t,o){var n=jQuery(e),i=t;if(void 0===o&&(o={}),void 0===o.css&&(o.css={}),void 0===o.method&&(o.method="prependTo"),""===t&&void 0!==o.target)switch(o.target){case"wrapper":var s="left";void 0!==o.offset&&(s=o.offset),t=this.find_wrapper(e,s)}switch(void 0===o.moveintohidden&&(t=jQuery(t).filter(":visible")),1<t.length&&console.log("Advanced Ads: element '"+i+"' found "+t.length+" times."),o.method){case"insertBefore":n.insertBefore(t);break;case"insertAfter":n.insertAfter(t);break;case"appendTo":n.appendTo(t);break;case"prependTo":n.prependTo(t);break;default:n.prependTo(t)}},set_parent_relative:function(e,t){t=void 0!==t?t:{};var o=jQuery(e).parent();t.use_grandparent&&(o=o.parent()),"static"!==o.css("position")&&""!==o.css("position")||o.css("position","relative")},fix_element:function(e,t){t=void 0!==t?t:{};var o=jQuery(e);t.use_grandparent?this.set_parent_relative(o.parent()):this.set_parent_relative(o),t.is_invisible&&o.show();var n=parseInt(o.offset().top),i=parseInt(o.offset().left);t.is_invisible&&o.hide(),o.css("position","fixed").css("top",n+"px").css("left",i+"px").css("right","")},find_wrapper:function(n,i){var s;return jQuery("body").children().each(function(e,t){if(t.id!==n.substring(1)){var o=jQuery(t);if("right"===i&&o.offset().left+jQuery(o).width()<jQuery(window).width()||"left"===i&&0<o.offset().left)return"static"!==o.css("position")&&""!==o.css("position")||o.css("position","relative"),s=t,!1}}),s},center_fixed_element:function(e){var t=jQuery(e),o=jQuery(window).width()/2-parseInt(t.css("width"))/2;t.css("left",o+"px")},center_vertically:function(e){var t=jQuery(e),o=jQuery(window).height()/2-parseInt(t.css("height"))/2;"fixed"!==t.css("position")&&(o-=topoffset=parseInt(t.offset().top)),t.css("top",o+"px")},close:function(e){jQuery(e).remove()},wait_for_images:function(i,s){var r=0,a=[];i.find('img[src][src!=""]').each(function(){a.push(this.src)}),0===a.length&&s.call(i),jQuery.each(a,function(e,t){var o=new Image;o.src=t;var n="load error";jQuery(o).one(n,function e(t){if(jQuery(this).off(n,e),++r==a.length)return s.call(i[0]),!1})})},privacy:{get_state:function(){if(!window.advads_options||!window.advads_options.privacy)return"not_needed";var e=window.advads_options.privacy;if(!e.enabled)return"not_needed";var t=e["consent-method"]?e["consent-method"]:"0";switch(t){case"0":return"not_needed";case"custom":if(e[!1]||void 0===e["custom-cookie-value"])return"not_needed";var o=advads.get_cookie(e["custom-cookie-name"]);return"string"!=typeof o?"unknown":""===e["custom-cookie-value"]&&""===o||""!==e["custom-cookie-value"]&&-1!==o.indexOf(e["custom-cookie-value"])?"accepted":"unknown";default:return advads.cookie_exists(t)?"accepted":"unknown"}},is_adsense_npa_enabled:function(){return!window.advads_options||!window.advads_options.privacy||!!window.advads_options.privacy["show-non-personalized-adsense"]}}},jQuery(document).ready(function(){if(advads.supports_localstorage()&&localStorage.getItem("advads_frontend_picker")){var s,r=jQuery("<div id='advads-picker-overlay'>"),a=[document.body,document.documentElement,document];r.css({position:"absolute",border:"solid 2px #428bca",backgroundColor:"rgba(66,139,202,0.5)",boxSizing:"border-box",zIndex:1e6,pointerEvents:"none"}).prependTo("body"),jQuery(document).mousemove(function(e){if(e.target!==s){if(~a.indexOf(e.target))return s=null,void r.hide();var t=jQuery(e.target),o=t.offset(),n=t.outerWidth(),i=t.outerHeight();s=e.target,r.css({top:o.top,left:o.left,width:n,height:i}).show(),console.log(jQuery(s).getPath())}}),jQuery(document).click(function(e){var t=jQuery(s).getPath();localStorage.setItem("advads_frontend_element",t),window.location=localStorage.getItem("advads_prev_url")})}}),jQuery.fn.extend({getPath:function(e,t){if(void 0===e&&(e=""),void 0===t&&(t=0),this.is("html"))return"html > "+e;if(3===t)return e;var o=this.get(0).nodeName.toLowerCase(),n=this.attr("id"),i=this.attr("class");return t+=1,void 0===n||/\d/.test(n)?void 0!==i&&(i=i.split(/[\s\n]+/),(i=jQuery.grep(i,function(e,t){return!/\d/.test(e)})).length&&(o+="."+i.slice(0,2).join("."))):o+="#"+n,this.siblings(o).length&&(o+=":eq("+this.siblings(o).addBack().not("#advads-picker-overlay").index(this)+")"),""===e?this.parent().getPath(o,t):this.parent().getPath(o+" > "+e,t)}});