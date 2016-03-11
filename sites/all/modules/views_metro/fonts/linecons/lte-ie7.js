/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'linecons\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-heart' : '&#xe000;',
			'icon-cloud' : '&#xe001;',
			'icon-star' : '&#xe002;',
			'icon-tv' : '&#xe003;',
			'icon-sound' : '&#xe004;',
			'icon-video' : '&#xe005;',
			'icon-trash' : '&#xe006;',
			'icon-user' : '&#xe007;',
			'icon-key' : '&#xe008;',
			'icon-search' : '&#xe009;',
			'icon-settings' : '&#xe00a;',
			'icon-camera' : '&#xe00b;',
			'icon-tag' : '&#xe00c;',
			'icon-lock' : '&#xe00d;',
			'icon-bulb' : '&#xe00e;',
			'icon-pen' : '&#xe00f;',
			'icon-diamond' : '&#xe010;',
			'icon-display' : '&#xe011;',
			'icon-location' : '&#xe012;',
			'icon-eye' : '&#xe013;',
			'icon-bubble' : '&#xe014;',
			'icon-stack' : '&#xe015;',
			'icon-cup' : '&#xe016;',
			'icon-phone' : '&#xe017;',
			'icon-news' : '&#xe018;',
			'icon-mail' : '&#xe019;',
			'icon-like' : '&#xe01a;',
			'icon-photo' : '&#xe01b;',
			'icon-note' : '&#xe01c;',
			'icon-clock' : '&#xe01d;',
			'icon-paperplane' : '&#xe01e;',
			'icon-params' : '&#xe01f;',
			'icon-banknote' : '&#xe020;',
			'icon-data' : '&#xe021;',
			'icon-music' : '&#xe022;',
			'icon-megaphone' : '&#xe023;',
			'icon-study' : '&#xe024;',
			'icon-lab' : '&#xe025;',
			'icon-food' : '&#xe026;',
			'icon-t-shirt' : '&#xe027;',
			'icon-fire' : '&#xe028;',
			'icon-clip' : '&#xe029;',
			'icon-shop' : '&#xe02a;',
			'icon-calendar' : '&#xe02b;',
			'icon-wallet' : '&#xe02c;',
			'icon-vynil' : '&#xe02d;',
			'icon-truck' : '&#xe02e;',
			'icon-world' : '&#xe02f;',
			'icon-untitled' : '&#xe030;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, html, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};