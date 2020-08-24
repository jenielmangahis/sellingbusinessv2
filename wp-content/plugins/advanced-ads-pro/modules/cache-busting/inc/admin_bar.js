/**
 * Advanced ads cache-busting admin bar.
 */

var advanced_ads_pro_admin_bar;

if (!advanced_ads_pro_admin_bar) {
	advanced_ads_pro_admin_bar = {
			offset: 0,
			adminBar: null,
			bufferedAds: [],

			observe: function (event) {
				var ad, that = advanced_ads_pro_admin_bar, ref;
				if (event.event === 'hasAd' && event.ad && event.ad.title) {
					if (!that.adminBar) {
						// remove 'No Ads found' li
						jQuery('#wp-admin-bar-advads_no_ads_found').remove();

						that.adminBar = jQuery("#wp-admin-bar-advads_current_ads-default");
						that.offset = that.adminBar && that.adminBar.children() ? that.adminBar.children().length : 0;
					}
					if (!that.adminBar) {
						// no admin-bar yet: buffer
						that.bufferedAds.push(ad);
					} else {
						// flush buffer if not empty
						if (that.bufferedAds.length > 0) {
							that.flush();
						}
						// inject current ad
						that.inject(event.ad);
					}
				}
			},

			flush: function() {
				var that = advanced_ads_pro_admin_bar, i = 0;
				for (i = that.bufferedAds.length; i > 0; i -= 1) {
					that.inject(that.bufferedAds[i]);
				}
				that.bufferedAds = [];
			},

			inject: function (ad) {
				try {
					postscribe(this.adminBar, "<li id=\"wp-admin-bar-advads_current_ad_" + this.offset + "\"><div class=\"ab-item ab-empty-item\">" + ad.title + " (" + ad.type + ")</div></li>");
				} catch (err) {
					console.log('Advanced ads error: ' + err);
				}
				this.offset += 1;
			}
	};
}

if (advanced_ads_pro) {
	advanced_ads_pro.observers.add( advanced_ads_pro_admin_bar.observe );
	jQuery( advanced_ads_pro_admin_bar.flush );
}
