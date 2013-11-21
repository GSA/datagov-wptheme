(function () {
	tinymce.create('tinymce.plugins.Subscribe2Plugin', {
		init : function (ed, url) {
			var pb = '<img src="' + url + '/../include/spacer.gif" class="mceSubscribe2 mceItemNoResize" />',
			cls = 'mceSubscribe2',
			shortcode = '[subscribe2]',
			pbreplaced = [],
			pbRE = new RegExp(/(\[|<!--)subscribe2.*(\]|-->)/g);

			// Register commands
			ed.addCommand('mceSubscribe2', function () {
				ed.execCommand('mceInsertContent', 0, pb);
			});

			// Register buttons
			ed.addButton('subscribe2', {
				title : 'Insert Subscribe2 Token',
				image : url + '/../include/s2_button.png',
				cmd : cls
			});

			// load the CSS and enable it on the right class
			ed.onInit.add(function () {
				ed.dom.loadCSS(url + "/css/content.css");

				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function (th, o) {
						if (o.node.nodeName === 'IMG' && ed.dom.hasClass(o.node, cls)) {
							o.name = 'subscribe2';
						}
					});
				}
			});

			// allow selection of the image placeholder
			ed.onClick.add(function (ed, e) {
				e = e.target;

				if (e.nodeName === 'IMG' && ed.dom.hasClass(e, cls)) {
					ed.selection.select(e);
				}
			});

			// re-enable the CSS when the node changes
			ed.onNodeChange.add(function (ed, cm, n) {
				cm.setActive('subscribe2', n.nodeName === 'IMG' && ed.dom.hasClass(n, cls));
			});

			// create an array of replaced shortcodes so we have additional parameters
			// then swap in the graphic
			ed.onBeforeSetContent.add(function (ed, o) {
				pbreplaced = o.content.match(pbRE);
				o.content = o.content.replace(pbRE, pb);
			});

			// swap back the array of shortcodes to preserve parameters
			// replace any other instances with the default shortcode
			ed.onPostProcess.add(function (ed, o) {
				if (o.get) {
					if ( pbreplaced !== null ) {
						var i;
						for ( i = 0; i < pbreplaced.length; i++ ) {
							o.content = o.content.replace(/<img[^>]+>/, function (im) {
								if (im.indexOf('class="mceSubscribe2') !== -1) {
									im = pbreplaced[i];
								}
								return im;
							});
						}
					}
					o.content = o.content.replace(/<img[^>]+>/g, function (im) {
						if (im.indexOf('class="mceSubscribe2') !== -1) {
							im = shortcode;
						}
						return im;
					});
				}
			});
		},

		getInfo : function () {
			return {
				longname : 'Insert Subscribe2 Token',
				author : 'Matthew Robinson',
				authorurl : 'http://subscribe2.wordpress.com',
				infourl : 'http://subscribe2.wordpress.com',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('subscribe2', tinymce.plugins.Subscribe2Plugin);
})();