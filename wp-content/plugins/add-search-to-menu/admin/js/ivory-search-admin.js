( function( $ ) {

	'use strict';

	if ( typeof ivory_search === 'undefined' || ivory_search === null ) {
		return;
	}

	$( function() {

		var active_tab = ivory_search.activeTab;

		if ( $( '#ivory_search_options' ).length ) {

			if ( location.href.indexOf( 'active-tab=' ) > -1 ) {
				active_tab = ivory_search.getParameterByName( 'active-tab', ivory_search.getCookie( 'active-url' ) );
			} else {
				var active_tab_cookie = ivory_search.getCookie( 'active-tab' );
				active_tab = ( "" !== active_tab_cookie ) ? active_tab_cookie : ivory_search.activeTab;
			}

		}

		$( '#search-form-editor' ).tabs( {
			active: active_tab,
			activate: function( event, ui ) {
				$( '#active-tab' ).val( ui.newTab.index() );

				// If key exists updates the value
				var newUrl = location.href;
				if ( location.href.indexOf( 'active-tab=' ) > -1 ) {
				    newUrl = location.href.replace( /active-tab=\w*\d*/, "active-tab=" + ui.newTab.index() );
				// If not, append
				} else {
				     newUrl = location.href + "&active-tab=" + ui.newTab.index();
				}

				history.pushState( null, null, newUrl );

				if ( $( '#ivory_search_options' ).length ) {
					document.cookie = 'active-url=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
					document.cookie = 'active-tab=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
					document.cookie = 'active-url=' + newUrl + ';path=/';
					document.cookie = 'active-tab=' + ui.newTab.index() + ';path=/';
				}
			}
		} );
		
		$( '#search-form-editor' ).on( 'change', function() {
			if ( ! $( '.search-form-editor-panel .is-warning' ).length ) {
				$( '.search-form-editor-panel' ).prepend('<div class="is-warning">Settings have changed, you should save them!</div>')
			}
		} );

		$( '#search-form-editor-tabs' ).focusin( function( event ) {
			$( '#search-form-editor .keyboard-interaction' ).css(
				'visibility', 'visible' );
		} ).focusout( function( event ) {
			$( '#search-form-editor .keyboard-interaction' ).css(
				'visibility', 'hidden' );
		} );

		if ( '' === $( '#title' ).val() ) {
			$( '#title' ).focus();
		}

		ivory_search.titleHint();

		$( window ).on( 'beforeunload', function( event ) {
			console.log('beforeunload');
			var changed = false;

			$( '#is-admin-form-element :input[type!="hidden"]' ).each( function() {
				if ( $( this ).is( ':checkbox, :radio' ) ) {
					if ( this.defaultChecked != $( this ).is( ':checked' ) ) {
						changed = true;
					}
				} else if ( $( this ).is( 'select' ) ) {
					$( this ).find( 'option' ).each( function() {
						if ( this.defaultSelected != $( this ).is( ':selected' ) ) {
							changed = true;
						}
					} );
				} else {
					if ( this.defaultValue != $( this ).val() ) {
						changed = true;
					}
				}
			} );

			if ( changed ) {
				event.returnValue = ivory_search.saveAlert;
				return ivory_search.saveAlert;
			}
		} );

		$( '#is-admin-form-element' ).submit( function() {
			if ( 'copy' != this.action.value ) {
				$( window ).off( 'beforeunload' );
			}

			if ( 'save' == this.action.value ) {
				$( '#publishing-action .spinner' ).addClass( 'is-active' );
			}
		} );
	} );

	ivory_search.getCookie = function(cname) {
	    var name = cname + "=";
	    var decodedCookie = decodeURIComponent(document.cookie);
	    var ca = decodedCookie.split(';');
	    for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
		    c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
		    return c.substring(name.length, c.length);
		}
	    }
	    return "";
	}

	ivory_search.getParameterByName = function( name, url ) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

	ivory_search.titleHint = function() {
		var $title = $( '#title' );
		var $titleprompt = $( '#title-prompt-text' );

		if ( '' === $title.val() ) {
			$titleprompt.removeClass( 'screen-reader-text' );
		}

		$titleprompt.click( function() {
			$( this ).addClass( 'screen-reader-text' );
			$title.focus();
		} );

		$title.blur( function() {
			if ( '' === $(this).val() ) {
				$titleprompt.removeClass( 'screen-reader-text' );
			}
		} ).focus( function() {
			$titleprompt.addClass( 'screen-reader-text' );
		} ).keydown( function( e ) {
			$titleprompt.addClass( 'screen-reader-text' );
			$( this ).unbind( e );
		} );
	};

} )( jQuery );
