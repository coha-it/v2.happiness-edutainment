/**
 * Froogaloop
 * @since 1.0
 * @version 1.1
 */
var Froogaloop=function(){function e(a){return new e.fn.init(a)}function g(a,c,b){if(!b.contentWindow.postMessage)return!1;a=JSON.stringify({method:a,value:c});b.contentWindow.postMessage(a,h)}function l(a){var c,b;try{c=JSON.parse(a.data),b=c.event||c.method}catch(e){}"ready"!=b||k||(k=!0);if(!/^https?:\/\/player.vimeo.com/.test(a.origin))return!1;"*"===h&&(h=a.origin);a=c.value;var m=c.data,f=""===f?null:c.player_id;c=f?d[f][b]:d[b];b=[];if(!c)return!1;void 0!==a&&b.push(a);m&&b.push(m);f&&b.push(f);
return 0<b.length?c.apply(null,b):c.call()}function n(a,c,b){b?(d[b]||(d[b]={}),d[b][a]=c):d[a]=c}var d={},k=!1,h="*";e.fn=e.prototype={element:null,init:function(a){"string"===typeof a&&(a=document.getElementById(a));this.element=a;return this},api:function(a,c){if(!this.element||!a)return!1;var b=this.element,d=""!==b.id?b.id:null,e=c&&c.constructor&&c.call&&c.apply?null:c,f=c&&c.constructor&&c.call&&c.apply?c:null;f&&n(a,f,d);g(a,e,b);return this},addEvent:function(a,c){if(!this.element)return!1;
var b=this.element,d=""!==b.id?b.id:null;n(a,c,d);"ready"!=a?g("addEventListener",a,b):"ready"==a&&k&&c.call(null,d);return this},removeEvent:function(a){if(!this.element)return!1;var c=this.element,b=""!==c.id?c.id:null;a:{if(b&&d[b]){if(!d[b][a]){b=!1;break a}d[b][a]=null}else{if(!d[a]){b=!1;break a}d[a]=null}b=!0}"ready"!=a&&b&&g("removeEventListener",a,c)}};e.fn.init.prototype=e.fn;window.addEventListener?window.addEventListener("message",l,!1):window.attachEvent("onmessage",l);return window.Froogaloop=
window.$f=e}();

/**
 * Vimeo Handler
 * @since 1.0
 * @version 1.0
 */
(function(){

	// Listen for the ready event for any vimeo video players on the page
	var vimeoPlayers = document.querySelectorAll( 'iframe.mycred-vimeo-video' ),
		player;

	for (var i = 0, length = vimeoPlayers.length; i < length; i++) {
		player = vimeoPlayers[i];
		$f(player).addEvent( 'ready', vimeo_ready );
	}

	/**
	 * Utility function for adding an event. Handles the inconsistencies
	 * between the W3C method for adding events (addEventListener) and
	 * IE's (attachEvent).
	 */
	function addEvent( element, eventName, callback ) {
		if ( element.addEventListener ) {
			element.addEventListener( eventName, callback, false );
		}
		else {
			element.attachEvent( 'on' + eventName, callback );
		}
	}

	/**
	 * Called once a vimeo player is loaded and ready to receive
	 * commands. You can add events and make api calls only after this
	 * function has been called.
	 */
	function vimeo_ready( player_id ) {

		// Keep a reference to Froogaloop for this player
		var container = document.getElementById(player_id).parentNode.parentNode,
			froogaloop = $f(player_id),
			apiConsole = container.querySelector('.console .output'),
			video_id = document.getElementById( player_id ).getAttribute( 'data-vid' );

		/**
		 * Adds listeners for the events. Adding an event
		 * through Froogaloop requires the event name and the callback method
		 * that is called once the event fires.
		 */
		function setupEventListeners() {

			function onLoadProgress() {
				froogaloop.addEvent('loadProgress', function( data ) {
					if ( duration[ video_id ] === undefined )
						duration[ video_id ] = data.duration;
				});
			}

			function onPlay() {
				froogaloop.addEvent('play', function( data ) {
					var fn = window[ player_id ];
					fn( 1 );
				});
			}

			function onPause() {
				froogaloop.addEvent('pause', function( data ) {
					var fn = window[ player_id ];
					fn( 2 );
				});
			}

			function onFinish() {
				froogaloop.addEvent('finish', function( data ) {
					var fn = window[ player_id ];
					fn( 0 );
				});
			}

			function onSeek() {
				froogaloop.addEvent('seek', function( data ) {
					var fn = window[ player_id ];
					fn( 2 );
				});
			}

			onLoadProgress();
			onPlay();
			onPause();
			onFinish();
			onSeek();
		}

		setupEventListeners();
	}

})();