/*
 * jQuery.fontselect - A font selector for the Google Web Fonts api
 * Tom Moor, http://tommoor.com
 * Copyright (c) 2011 Tom Moor
 * MIT Licensed
 * @version 0.1
*/(function(a){a.fn.fontselect=function(b){var c=function(a,b){return function(){return a.apply(b,arguments)}},d=["Aclonica","Allan","Annie+Use+Your+Telescope","Anonymous+Pro","Allerta+Stencil","Allerta","Amaranth","Anton","Architects+Daughter","Arimo","Artifika","Arvo","Asset","Astloch","Bangers","Bentham","Bevan","Bigshot+One","Bowlby+One","Bowlby+One+SC","Brawler","Buda:300","Cabin","Calligraffitti","Candal","Cantarell","Cardo","Carter One","Caudex","Cedarville+Cursive","Cherry+Cream+Soda","Chewy","Coda","Coming+Soon","Copse","Corben:700","Cousine","Covered+By+Your+Grace","Crafty+Girls","Crimson+Text","Crushed","Cuprum","Damion","Dancing+Script","Dawning+of+a+New+Day","Didact+Gothic","Droid+Sans","Droid+Sans+Mono","Droid+Serif","EB+Garamond","Expletus+Sans","Fontdiner+Swanky","Forum","Francois+One","Geo","Give+You+Glory","Goblin+One","Goudy+Bookletter+1911","Gravitas+One","Gruppo","Hammersmith+One","Holtwood+One+SC","Homemade+Apple","Inconsolata","Indie+Flower","IM+Fell+DW+Pica","IM+Fell+DW+Pica+SC","IM+Fell+Double+Pica","IM+Fell+Double+Pica+SC","IM+Fell+English","IM+Fell+English+SC","IM+Fell+French+Canon","IM+Fell+French+Canon+SC","IM+Fell+Great+Primer","IM+Fell+Great+Primer+SC","Irish+Grover","Irish+Growler","Istok+Web","Josefin+Sans","Josefin+Slab","Judson","Jura","Jura:500","Jura:600","Just+Another+Hand","Just+Me+Again+Down+Here","Kameron","Kenia","Kranky","Kreon","Kristi","La+Belle+Aurore","Lato:100","Lato:100italic","Lato:300","Lato","Lato:bold","Lato:900","League+Script","Lekton","Limelight","Lobster","Lobster Two","Lora","Love+Ya+Like+A+Sister","Loved+by+the+King","Luckiest+Guy","Maiden+Orange","Mako","Maven+Pro","Maven+Pro:500","Maven+Pro:700","Maven+Pro:900","Meddon","MedievalSharp","Megrim","Merriweather","Metrophobic","Michroma","Miltonian Tattoo","Miltonian","Modern Antiqua","Monofett","Molengo","Mountains of Christmas","Muli:300","Muli","Neucha","Neuton","News+Cycle","Nixie+One","Nobile","Nova+Cut","Nova+Flat","Nova+Mono","Nova+Oval","Nova+Round","Nova+Script","Nova+Slim","Nova+Square","Nunito:light","Nunito","OFL+Sorts+Mill+Goudy+TT","Old+Standard+TT","Open+Sans:300","Open+Sans","Open+Sans:600","Open+Sans:800","Open+Sans+Condensed:300","Orbitron","Orbitron:500","Orbitron:700","Orbitron:900","Oswald","Over+the+Rainbow","Reenie+Beanie","Pacifico","Patrick+Hand","Paytone+One","Permanent+Marker","Philosopher","Play","Playfair+Display","Podkova","PT+Sans","PT+Sans+Narrow","PT+Sans+Narrow:regular,bold","PT+Serif","PT+Serif Caption","Puritan","Quattrocento","Quattrocento+Sans","Radley","Raleway:100","Redressed","Rock+Salt","Rokkitt","Ruslan+Display","Schoolbell","Shadows+Into+Light","Shanti","Sigmar+One","Six+Caps","Slackey","Smythe","Sniglet:800","Special+Elite","Stardos+Stencil","Sue+Ellen+Francisco","Sunshiney","Swanky+and+Moo+Moo","Syncopate","Tangerine","Tenor+Sans","Terminal+Dosis+Light","The+Girl+Next+Door","Tinos","Ubuntu","Ultra","Unkempt","UnifrakturCook:bold","UnifrakturMaguntia","Varela","Varela Round","Vibur","Vollkorn","VT323","Waiting+for+the+Sunrise","Wallpoet","Walter+Turncoat","Wire+One","Yanone+Kaffeesatz","Yanone+Kaffeesatz:300","Yanone+Kaffeesatz:400","Yanone+Kaffeesatz:700","Yeseva+One","Zeyada"],e={style:"font-select",placeholder:"Select a font",lookahead:2,api:"//fonts.googleapis.com/css?family="},f=function(){function b(b,c){this.$original=a(b);this.options=c;this.active=!1;this.setupHtml();this.getVisibleFonts();this.bindEvents();var d=this.$original.val();if(d){this.updateSelected();this.addFontLink(d)}}b.prototype.bindEvents=function(){a("li",this.$results).click(c(this.selectFont,this)).mouseenter(c(this.activateFont,this)).mouseleave(c(this.deactivateFont,this));a("span",this.$select).click(c(this.toggleDrop,this));this.$arrow.click(c(this.toggleDrop,this))};b.prototype.toggleDrop=function(a){if(this.active){this.$element.removeClass("font-select-active");this.$drop.hide();clearInterval(this.visibleInterval)}else{this.$element.addClass("font-select-active");this.$drop.show();this.moveToSelected();this.visibleInterval=setInterval(c(this.getVisibleFonts,this),500)}this.active=!this.active};b.prototype.selectFont=function(){var b=a("li.active",this.$results).data("value");this.$original.val(b).change();this.updateSelected();this.toggleDrop()};b.prototype.moveToSelected=function(){var b,c=this.$original.val();c?b=a("li[data-value='"+c+"']",this.$results):b=a("li",this.$results).first();this.$results.scrollTop(b.addClass("active").position().top)};b.prototype.activateFont=function(b){a("li.active",this.$results).removeClass("active");a(b.currentTarget).addClass("active")};b.prototype.deactivateFont=function(b){a(b.currentTarget).removeClass("active")};b.prototype.updateSelected=function(){var b=this.$original.val();a("span",this.$element).text(this.toReadable(b)).css(this.toStyle(b))};b.prototype.setupHtml=function(){this.$original.empty().hide();this.$element=a("<div>",{"class":this.options.style});this.$arrow=a("<div><b></b></div>");this.$select=a("<a><span>"+this.options.placeholder+"</span></a>");this.$drop=a("<div>",{"class":"fs-drop"});this.$results=a("<ul>",{"class":"fs-results"});this.$original.after(this.$element.append(this.$select.append(this.$arrow)).append(this.$drop));this.$drop.append(this.$results.append(this.fontsAsHtml())).hide()};b.prototype.fontsAsHtml=function(){var a=d.length,b,c,e="";for(var f=0;f<a;f++){b=this.toReadable(d[f]);c=this.toStyle(d[f]);e+='<li data-value="'+d[f]+'" style="font-family: '+c["font-family"]+"; font-weight: "+c["font-weight"]+'">'+b+"</li>"}return e};b.prototype.toReadable=function(a){return a.replace(/[\+|:]/g," ")};b.prototype.toStyle=function(a){var b=a.split(":");return{"font-family":this.toReadable(b[0]),"font-weight":b[1]||400}};b.prototype.getVisibleFonts=function(){if(this.$results.is(":hidden"))return;var b=this,c=this.$results.scrollTop(),d=c+this.$results.height();if(this.options.lookahead){var e=a("li",this.$results).first().height();d+=e*this.options.lookahead}a("li",this.$results).each(function(){var e=a(this).position().top+c,f=e+a(this).height();if(f>=c&&e<=d){var g=a(this).data("value");b.addFontLink(g)}})};b.prototype.addFontLink=function(b){var c=this.options.api+b;a("link[href*='"+b+"']").length===0&&a("link:last").after('<link href="'+c+'" rel="stylesheet" type="text/css">')};return b}();return this.each(function(b){b&&a.extend(e,b);return new f(this,e)})}})(jQuery);
