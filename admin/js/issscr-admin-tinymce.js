(function( $ ) {
	'use strict';

	tinymce.PluginManager.add('issscr_tinymce_shortcode_button', function(editor, url) {


		var tinymce_menu = [];

		//
		// Company Name
		//

		if ( issscr_active_shortcodes.company_name ) {
			tinymce_menu.push( {
				text: 'Company Name',
				onclick: function() {
					editor.insertContent(
						'[iss_company_name]'
					);
				}
			} );
		}

		//
		// Static Content
		//

		if ( issscr_active_shortcodes.static_content ) {
			tinymce_menu.push( {
				text: 'Static Content',
				onclick: function() {
					editor.windowManager.open( {
						title: 'Insert Static Content Shortcode',
						body: [
							{
								type:  'listbox',
								name:  'block',
								label: 'Block',
								values: [
									{ text: 'Static Content Block 1',  value: '1'  },
									{ text: 'Static Content Block 2',  value: '2'  },
									{ text: 'Static Content Block 3',  value: '3'  },
									{ text: 'Static Content Block 4',  value: '4'  },
									{ text: 'Static Content Block 5',  value: '5'  },
									{ text: 'Static Content Block 6',  value: '6'  },
									{ text: 'Static Content Block 7',  value: '7'  },
									{ text: 'Static Content Block 8',  value: '8'  },
									{ text: 'Static Content Block 9',  value: '9'  },
									{ text: 'Static Content Block 10', value: '10' },
								]
							}
						],
						onsubmit: function( e ) {
							editor.insertContent(
								'[iss_static_content block="' + e.data.block + '"]'
							);
						}
					});
				},
			} );
		}

		//
		// Dynamic Shortcodes
		//

		if ( issscr_shortcode_data.length !== 0 ) {
			const dynamic_shortcode_data = JSON.parse(issscr_shortcode_data);

			$.each(dynamic_shortcode_data, function( shortcode_type_name, shortcode_type ) {
				$.each(shortcode_type, function( shortcode_tag, shortcode_data ) {

					switch(shortcode_type_name) {

						// Image
						case 'image':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'listbox',
												name:  'size',
												label: 'Size',
												values: [
													{ text: 'Medium', value: 'medium' },
													{ text: 'Large',  value: 'large'  },
													{ text: 'Full',   value: 'full'   },
												],
												value: 'large'
											},
											{
												type:  'listbox',
												name:  'alignment',
												label: 'Alignment',
												values: [
													{ text: 'None',   value: ''       },
													{ text: 'Left',   value: 'left'   },
													{ text: 'Center', value: 'center' },
													{ text: 'Right',  value: 'right'  },
												]
											},
											{
												type:  'textbox',
												name:  'classes',
												label: 'Class',
												value: '',
											}
										],
										onsubmit: function( e ) {
											const size = e.data.size ? ` size="${e.data.size}"` : '';
											const classes = e.data.classes ? ` class="${e.data.classes}"` : '';
											const alignment = e.data.alignment ? ` alignment="${e.data.alignment}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${size}${classes}${alignment}]`,
											);
										}
									} );
								}
							} );
							break;

						// Image Slider
						case 'image_slider':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'listbox',
												name:  'size',
												label: 'Size',
												values: [
													{ text: 'Medium', value: 'medium' },
													{ text: 'Large',  value: 'large'  },
													{ text: 'Full',   value: 'full'   }
												],
												value: 'large'
											},
											{
												type:  'listbox',
												name:  'autoplay',
												label: 'Autoplay',
												values: [
													{ text: 'On',  value: 'on'   },
													{ text: 'Off', value: ''     }
												],
												value: 'on'
											},
										],
										onsubmit: function( e ) {
											const size = e.data.size ? ` size="${e.data.size}"` : '';
											const autoplay = e.data.autoplay ? ` auto="${e.data.autoplay}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${size}${autoplay}]`,
											);
										}
									} );
								}
							} );
							break;

						// List
						case 'list':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'textbox',
												name:  'limit',
												label: 'Limit',
												value: '5'
											},
										],
										onsubmit: function( e ) {
											const limit = e.data.limit ? ` limit="${e.data.limit}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${limit}]`,
											);
										}
									} );
								}
							} );
							break;

						// Definition List
						case 'definition_list':
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.windowManager.open( {
										title: `Insert ${shortcode_data.title} Shortcode`,
										body: [
											{
												type:  'textbox',
												name:  'limit',
												label: 'Limit',
												value: '5'
											},
											{
												type:  'listbox',
												name:  'htag',
												label: 'h-Tag',
												default: 'h4',
												values: [
													{ text: 'H1',  value: 'h1' },
													{ text: 'H2',  value: 'h2' },
													{ text: 'H3',  value: 'h3' },
													{ text: 'H4',  value: 'h4' },
													{ text: 'H5',  value: 'h5' },
													{ text: 'H6',  value: 'h6' },
													{ text: 'P',  value: 'p' },
												],
												value: 'h4'
											},
											{
												type:  'listbox',
												name:  'accordion',
												label: 'Accordion',
												values: [
													{ text: 'On',  value: 'on' },
													{ text: 'Off', value: ''   }
												]
											},
										],
										onsubmit: function( e ) {
											const limit = e.data.limit ? ` limit="${e.data.limit}"` : '';
											const htag = e.data.htag ? ` htag="${e.data.htag}"` : '';
											const accordion = e.data.accordion ? ` accordion="${e.data.accordion}"` : '';
											editor.insertContent(
												`[${shortcode_data.tag}${limit}${htag}${accordion}]`,
											);
										}
									} );
								}
							} );
							break;

						// Default
						default:
							tinymce_menu.push( {
								text: shortcode_data.title,
								onclick: function() {
									editor.insertContent(
										'[' + shortcode_data.tag + ']'
									);
								}
							} );
							break;
					  }

				});
			});
		}

		//
		// Add button to toolbar
		//

		editor.addButton('issscr_tinymce_shortcode_button', {
			title: 'SEO Content Randomizer Shortcodes',
			type: 'menubutton',
			icon: 'icon issscr-tinymce-shortcode-button-icon',
			menu: tinymce_menu,
		});

	});
})( jQuery );