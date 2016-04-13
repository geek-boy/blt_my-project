/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "riddle_marketplace" plugin that makes it possible to embed a riddle from a list of users' riddles
 *
 *
 */
CKEDITOR.plugins.add( 'RiddleButton', {
	requires: ['button','clipboard'],
	init: function( editor ) {

        // pull ajax data from RiddleButton.php
		var config = editor.config;
		var riddles = JSON.parse(config.data);

        // add the button with the dir path so we can find the icon
        if ( !CKEDITOR.env.hc ) {
            var icon = this.path +"images/riddle.jpg";
            addButton( 'RiddleButton', icon );
		}

        // add the button
		function addButton( name, icon ) {
			var title = 'Riddle';
			editor.ui.add( name, "RiddleButton", {
				label: title,
				title: title,
                icon: icon,
				modes: {wysiwyg: 1},
				editorFocus: 0,

                // define the popup panel
				panel: {
					css: CKEDITOR.skin.getPath('editor'),
					attributes: {role: 'listbox', 'aria-label': "Riddle"}
				},

                // when the popup shows
				onBlock: function (panel, block) {
                    var riddleList = renderRiddleList(panel);
                    block.autoSize = true;
					block.element.addClass('cke_colorblock');
					block.element.$.style.width = '300%';
                    block.element.setHtml(riddleList);

					CKEDITOR.ui.fire('ready', this);
				},

				refresh: function () {

				},

			});
		};

        // build the list of the dropdown menu contents. This is a html list with click events
		function renderRiddleList( panel ) {

            // create a function which embeds the riddle on teh page.
            // TODO: SEO embed data
			var clickFn = CKEDITOR.tools.addFunction( function( riddleId ) {
				editor.focus();
				panel.hide();
				editor.fire( 'saveSnapshot' );
                var html = '<iframe width="100%" height="600" frameborder="0" src="https://www.riddle.com/a/'+riddleId+'?fixed"></iframe>';
                editor.insertHtml(html);
				editor.fire( 'saveSnapshot' );
			});


            // loop through all riddles given by the controller ajax call and build the contents of the dropdown menu
			var output = [];
			riddles.forEach(function(riddle)
			{
				var title = riddle.data.title;
				if (!title)
					title = "Riddle " + riddle.uid;
				var html = "";
				html +=	'<p><a title="' + title + '"';
				html += ' onclick="CKEDITOR.tools.callFunction(' + clickFn + ',\'' + riddle.uid + '\'); return false;"';
				html += ' href="javascript:void(\'' + riddle.uid + '\')" role="option">';
				html += title;
				html += '</a></p>';

				output.push(html);
			});

			return output.join( '' );

		}


        // check for the pasting of shortcodes into the editor. At the moment shortcodes are hardcoded
        //TODO: store the shortcode config inside a configfile or pull it from riddle.com to keep it synced
        editor.on( 'paste', function( evt ) {
            var data = evt.data.dataValue;

            // check if its a riddle by scanning for the following string
            var tag = '[riddle=';
            var idIndex = data.indexOf(tag);
            var riddleId = -1;
            // if the string exists then search for the riddle id, ie [riddle=1823] => 1823
            if (data.indexOf(idIndex > -1)) {
                riddleId = data.substring(tag.length, data.length - 1);
            }

            if (riddleId != -1) {
                // swap out the shortcode with a riddle embed
                // TODO SEO code needed ( as above )
                data = '<iframe width="100%" height="600" frameborder="0" src="https://www.riddle.com/a/' + riddleId + '?fixed"></iframe>';
            }
            // update the data going back into CKE
            evt.data.dataValue = data;
        } );
    }


	,onLoad: function() {
        //setup a function to popup the list of riddles
		function clickFn( editor ) {
			var _ = this._;

			if ( _.state == CKEDITOR.TRISTATE_DISABLED )
				return;

			this.createPanel( editor );

			if ( _.on ) {
				_.panel.hide();
				return;
			}

			_.panel.showBlock( this._.id, this.document.getById( this._.id ), 4 );
		}

		/**
		 * @class
		 * @extends CKEDITOR.ui.button
		 * @todo class and methods
		 */
		CKEDITOR.ui.RiddleButton = CKEDITOR.tools.createClass( {
			base: CKEDITOR.ui.button,

			/**
			 * Creates a panelButton class instance.
			 *
			 * @constructor
			 */
			$: function( definition ) {
                console.log(definition);
				// We don't want the panel definition in this object.
				var panelDefinition = definition.panel || {};
				delete definition.panel;

				this.base( definition );

				this.document = ( panelDefinition.parent && panelDefinition.parent.getDocument() ) || CKEDITOR.document;

				panelDefinition.block = {
					attributes: panelDefinition.attributes
				};
				panelDefinition.toolbarRelated = true;

				this.hasArrow = true;

				this.click = clickFn;

				this._ = {
					panelDefinition: panelDefinition
				};
			},

			statics: {
				handler: {
					create: function( definition ) {
						return new CKEDITOR.ui.RiddleButton( definition );
					}
				}
			},

			proto: {
				createPanel: function( editor ) {
					var _ = this._;

					if ( _.panel )
						return;

					var panelDefinition = this._.panelDefinition,
							panelBlockDefinition = this._.panelDefinition.block,
							panelParentElement = panelDefinition.parent || CKEDITOR.document.getBody(),
							panel = this._.panel = new CKEDITOR.ui.floatPanel( editor, panelParentElement, panelDefinition ),
							block = panel.addBlock( _.id, panelBlockDefinition ),
							me = this;

					panel.onShow = function() {
						if ( me.className )
							this.element.addClass( me.className + '_panel' );

						me.setState( CKEDITOR.TRISTATE_ON );

						_.on = 1;

						me.editorFocus && editor.focus();

						if ( me.onOpen )
							me.onOpen();
					};

					panel.onHide = function( preventOnClose ) {
						if ( me.className )
							this.element.getFirst().removeClass( me.className + '_panel' );

						me.setState( me.modes && me.modes[ editor.mode ] ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED );

						_.on = 0;

						if ( !preventOnClose && me.onClose )
							me.onClose();
					};

					panel.onEscape = function() {
						panel.hide( 1 );
						me.document.getById( _.id ).focus();
					};

					if ( this.onBlock )
						this.onBlock( panel, block );

					block.onHide = function() {
						_.on = 0;
						me.setState( CKEDITOR.TRISTATE_OFF );
					};
				}
			}
		} );

	},
	beforeInit: function( editor ) {
		editor.ui.addHandler( "RiddleButton", CKEDITOR.ui.RiddleButton.handler );
	}
} );