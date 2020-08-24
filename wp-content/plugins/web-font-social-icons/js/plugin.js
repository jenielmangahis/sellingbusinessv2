(function () {
	tinymce.create("tinymce.plugins.wfsi",{

		init : function(ed, url){

            ed.addButton('wfsi_button', {
            title : 'Web Font Social Icons',
                onclick : function() {
                    tb_show("", url + "/popup.php?width=" + 400 + "&height=" + 700);
                },
                image: url + '/images/icon.png',

            });
        },
		getInfo: function () {
			return {
				longname: 'Web Fonts Social Icons',
				author: 'Purethemes.net',
				authorurl: 'http://themeforest.net/user/purethemes/',
				infourl: 'http://themeforest.net/user/purethemes/',
				version: "1.0"
			}
		}
	});

	tinymce.PluginManager.add("wfsi", tinymce.plugins.wfsi);
})();