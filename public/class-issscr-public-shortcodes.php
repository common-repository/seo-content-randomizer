<?php


class ISSSCR_Public_Shortcodes
{

    protected $helper;

    protected $cache_manager;

    protected $randomization;

    public function __construct()
    {
        $this->cache_manager = new ISSSCR_Cache_Manager();
        $this->helper = new ISSSCR_Public_Shortcode_Helpers();
        $this->randomization = new ISSSCR_Public_Randomization();
    }

    public function content($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = get_the_ID();
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $this->cache_manager->load_record($landing_page_id);
        $cached_content_id = $this->cache_manager->get_current_record_entry_value($sc_name);

        if (!is_null($cached_content_id)) {
            $random_content_id = $cached_content_id;
        } else {
            $template_page_id = ISSSCR_Meta_Data::get_post_id();
            $pinned_content_block_number = get_post_meta($template_page_id, "_issscr_{$post_type}_pinned_content_block", true);
            if (!empty ($pinned_content_block_number)) {
                $random_content_id = $pinned_content_block_number;
            } else {
                $random_content_id = $this->randomization->get_random_content_id("_issscr_{$post_type}_content", 'content');
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_content_id);
        }

        return ISSSCR_Meta_Data::get_processed_content("_issscr_{$post_type}_content", 'content', $random_content_id);
    }

    public function static_content($atts, $content = null, $sc_name = '')
    {
        $post_type = ISSSCR_Meta_Data::get_post_type();
        extract(shortcode_atts(array(
            'block' => '1',
        ), $atts));
        return ISSSCR_Meta_Data::get_processed_content("_issscr_{$post_type}_static_content", 'content', $block);
    }

    public function company($atts, $content = null, $sc_name = '')
    {
        return sanitize_text_field(ISSSCR_Options::get_setting('company_name', false, 'iss_company_info_settings'));
    }

    public function dynamic_content($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $handle = str_replace(array('iss_', '_content'), '', $sc_name);
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $this->cache_manager->load_record($landing_page_id);
        $cached_content_id = $this->cache_manager->get_current_record_entry_value($sc_name);

        if (!is_null($cached_content_id)) {
            $random_content_id = $cached_content_id;
        } else {
            $template_page_id = ISSSCR_Meta_Data::get_post_id();
            $pinned_content_block_number = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_pinned_content_block", true);
            if (!empty ($pinned_content_block_number)) {
                $random_content_id = $pinned_content_block_number;
            } else {
                $random_content_id = $this->randomization->get_random_content_id("_issscr_{$post_type}_{$handle}_content", 'content');
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_content_id);
        }

        return ISSSCR_Meta_Data::get_processed_content("_issscr_{$post_type}_{$handle}_content", 'content', $random_content_id);
    }

    public function dynamic_image($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $handle = str_replace(array('iss_', '_image'), '', $sc_name);
        $this->cache_manager->load_record($landing_page_id);
        $cached_image_id = $this->cache_manager->get_current_record_entry_value($sc_name);

        extract(shortcode_atts(array(
            'size' => 'large',
            'class' => ' ',
        ), $atts));

        if (!is_null($cached_image_id)) {
            $random_image_id = $cached_image_id;
        } else {
            $template_page_id = ISSSCR_Meta_Data::get_post_id();
            $post_type = ISSSCR_Meta_Data::get_post_type();
            $no_duplicates = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_no_duplicate_images", true);
            if ($no_duplicates) {
                $random_image_id = $this->randomization->get_random_image_id_without_duplicates("_issscr_{$post_type}_{$handle}_images", 'image_id');
            } else {
                $random_image_id = $this->randomization->get_random_image_id("_issscr_{$post_type}_{$handle}_images", 'image_id');
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_image_id);
        }

        $image = wp_get_attachment_image( $random_image_id, $size, false, array( 'class' => $class, 'style' => 'max-width: 100%; height: auto;' ) );

        return "<figure class='wp-block-image'>{$image}</figure>";
    }

    public function dynamic_image_slider($atts, $content = null, $sc_name = '')
    {
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $handle = str_replace(array('iss_', '_image_slider'), '', $sc_name);
        return $this->slider("_issscr_{$post_type}_{$handle}_images", 'image_id', $atts);
    }

    public function dynamic_singular_keyword($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $template_page_id = ISSSCR_Meta_Data::get_post_id();
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $handle = str_replace('iss_singular_', '', $sc_name);
        $this->cache_manager->load_record($landing_page_id);
        $cached_keyword_id = $this->cache_manager->get_current_record_entry_value($sc_name);
        $text = get_post_meta($template_page_id, "_issscr_{$post_type}_singular_{$handle}_keywords", true);

        if (!is_null($cached_keyword_id)) {
            $random_line_id = $cached_keyword_id;
        } else {
            $meta_group_id = "_issscr_{$post_type}_singular_{$handle}_keywords";
            $no_duplicates = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_no_duplicate_keywords", true);
            if ($no_duplicates) {
                $random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates($meta_group_id, $text);
            } else {
                $random_line_id = $this->randomization->get_random_line_id_from_text($meta_group_id, $text);
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_line_id);
        }

        $random_line = $this->randomization->get_line_from_text($text, $random_line_id);
        $random_line = do_shortcode($random_line);
        $random_line = $this->helper->add_prefix($random_line, $atts);

        return $random_line;
    }

    public function dynamic_plural_keyword($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $template_page_id = ISSSCR_Meta_Data::get_post_id();
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $handle = str_replace('iss_plural_', '', $sc_name);
        $this->cache_manager->load_record($landing_page_id);
        $cached_keyword_id = $this->cache_manager->get_current_record_entry_value($sc_name);
        $text = get_post_meta($template_page_id, "_issscr_{$post_type}_plural_{$handle}_keywords", true);

        if (!is_null($cached_keyword_id)) {
            $random_line_id = $cached_keyword_id;
        } else {
            $meta_group_id = "_issscr_{$post_type}_plural_{$handle}_keywords";
            $no_duplicates = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_no_duplicate_keywords", true);
            if ($no_duplicates) {
                $random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates($meta_group_id, $text);
            } else {
                $random_line_id = $this->randomization->get_random_line_id_from_text($meta_group_id, $text);
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_line_id);
        }

        $random_line = $this->randomization->get_line_from_text($text, $random_line_id);
        $random_line = do_shortcode($random_line);
        $random_line = $this->helper->add_prefix($random_line, $atts);

        return $random_line;
    }

    public function dynamic_phrase($atts, $content = null, $sc_name = '')
    {
        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $template_page_id = ISSSCR_Meta_Data::get_post_id();
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $handle = str_replace(array('iss_', '_phrase'), '', $sc_name);
        $this->cache_manager->load_record($landing_page_id);
        $cached_phrase_id = $this->cache_manager->get_current_record_entry_value($sc_name);
        $text = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_phrases", true);

        if (!is_null($cached_phrase_id)) {
            $random_line_id = $cached_phrase_id;
        } else {
            $meta_group_id = "_issscr_{$post_type}_{$handle}_phrases";
            $no_duplicates = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_no_duplicate_phrases", true);
            if ($no_duplicates) {
                $random_line_id = $this->randomization->get_random_line_id_from_text_without_duplicates($meta_group_id, $text);
            } else {
                $random_line_id = $this->randomization->get_random_line_id_from_text($meta_group_id, $text);
            }
            $this->cache_manager->add_new_record_entry_value($sc_name, $random_line_id);
        }

        $random_line = $this->randomization->get_line_from_text($text, $random_line_id);
        $random_line = do_shortcode($random_line);
        $random_line = $this->helper->add_prefix($random_line, $atts);

        return $random_line;
    }

    public function dynamic_list($atts, $content = null, $sc_name = '')
    {
        $atts = shortcode_atts(array(
            'limit' => '5',
        ), $atts, $sc_name);

        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $template_page_id = ISSSCR_Meta_Data::get_post_id();
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $handle = str_replace(array('iss_', '_list'), '', $sc_name);
        $this->cache_manager->load_record($landing_page_id);
        $cached_line_ids = $this->cache_manager->get_current_record_entry_value($sc_name);
        $text = get_post_meta($template_page_id, "_issscr_{$post_type}_{$handle}_list", true);
        $list = ISSSCR_String_Helpers::explode_string_by_new_line($text);

        if (empty($list)) {
            return false;
        }

        if (!is_null($cached_line_ids)) {
            $random_line_ids = $cached_line_ids;
        } else {
            // Numerate blocks
            $numerated_list = array();
            for ($i = 0; $i < count($list); $i++) {
                $numerated_list[][$i] = $list[$i];
            }

            // Randomize list items
            shuffle($numerated_list);

            // Limit blocks
            $numerated_list = array_slice($numerated_list, 0, $atts['limit']);

            // Get list IDs
            $random_line_ids = array();
            foreach ($numerated_list as $key => $value) {
                $array_keys = array_keys($value);
                $random_line_ids[] = $array_keys[0];
            }

            $this->cache_manager->add_new_record_entry_value($sc_name, $random_line_ids);
        }

        // Get random list items
        $random_list_items = array();
        foreach ($random_line_ids as $random_line_id) {
            $random_list_items[] = $list[$random_line_id];
        }

        $output = '';
        $output .= "<ul class='issscr-list  issscr-{$handle}-list'>";
        foreach ($random_list_items as $random_list_item) {
            $output .= "<li>$random_list_item</li>";
        }
        $output .= '</ul>';

        return $output;
    }

    public function dynamic_definition_list($atts, $content = null, $sc_name = '')
    {
        $atts = shortcode_atts(array(
            'accordion' => '',
            'htag' => '',
            'limit' => '5',
        ), $atts, $sc_name);

        $landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $handle = str_replace(array('iss_', '_definition_list'), '', $sc_name);
        $post_type = ISSSCR_Meta_Data::get_post_type();
        $this->cache_manager->load_record($landing_page_id);
        $cached_block_ids = $this->cache_manager->get_current_record_entry_value($sc_name);

        // Get blocks
        $blocks = ISSSCR_Meta_Data::get_group_fields("_issscr_{$post_type}_{$handle}_definition_list");
        if (!$blocks) {
            return false;
        }

        $random_blocks = array();

        if (!is_null($cached_block_ids)) {
            foreach ($cached_block_ids as $cached_block_id) {
                $random_blocks[] = $blocks[$cached_block_id];
            }
        } else {
            // Numerate blocks
            $numerated_blocks = array();
            for ($i = 0; $i < count($blocks); $i++) {
                $numerated_blocks[][$i] = $blocks[$i];
            }

            // Randomize blocks
            shuffle($numerated_blocks);
            // Limit blocks
            $numerated_blocks = array_slice($numerated_blocks, 0, $atts['limit']);

            // Get block IDs
            $block_ids = array();
            foreach ($numerated_blocks as $key => $value) {
                $array_keys = array_keys($value);
                $block_ids[] = $array_keys[0];
            }

            // Cache block IDs
            $this->cache_manager->add_new_record_entry_value($sc_name, $block_ids);

            // Get random blocks
            foreach ($block_ids as $block_id) {
                $random_blocks[] = $blocks[$block_id];
            }
        }

        if ($atts['accordion'] == 'on') {
            $output = "<div class='issscr-definition-list-accordion issscr-{$handle}-definition-list'>";
            foreach ($random_blocks as $random_block) {
                $output .= '<div class="issscr-definition-list-accordion-item js-issscr-accordion" data-status="closed">';
                $output .= '<div class="issscr-definition-list-accordion-item-header js-issscr-accordion-trigger">';
                $output .= '<div class="issscr-definition-list-accordion-icon">';
                $output .= '<span class="dashicons dashicons-arrow-right"></span>';
                $output .= '</div>';
                $output .= '<div class="issscr-definition-list-accordion-title">';
                $output .= $random_block['heading'];
                $output .= '</div>';
                $output .= '</div>';
                $output .= '<div class="issscr-definition-list-accordion-item-body js-issscr-accordion-target">';
                $output .= $random_block['content'];
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div>';

            return $output;
        }

        $output = "<div class='issscr-definition-list issscr-{$handle}-definition-list'>";
        $use_htag = (!empty($atts['htag']) && $atts['htag'] != 'p');
        foreach ($random_blocks as $random_block) {
            $output .= $use_htag ? "<{$atts['htag']}>" : '<p><b>';
            $output .= $random_block['heading'];
            $output .= $use_htag ? "</{$atts['htag']}>" : '</p></b>';
            $output .= "<p>{$random_block['content']}</p>";
        }
        $output .= '</div>';

        return $output;
    }

    private function slider($group_id, $field_id, $atts)
    {

        extract(shortcode_atts(array(
            'auto' => '',
            'size' => 'large',
        ), $this->helper->normalize_empty_atts($atts)));

        $output = '';

        $config_sideshow = $auto ? 'true' : 'false';

        $config = <<<EOT
			<script>
				(function( $ ) {
					if ($.isFunction(jQuery.fn.flexslider)) {
						$('.flexslider').flexslider( {
							namespace: "flex-",             //{NEW} String: Prefix string attached to the class of every element generated by the plugin
							selector: ".slides > li",       //{NEW} Selector: Must match a simple pattern. '{container} > {slide}' -- Ignore pattern at your own peril
							animation: "fade",              //String: Select your animation type, "fade" or "slide"
							easing: "swing",                //{NEW} String: Determines the easing method used in jQuery transitions. jQuery easing plugin is supported!
							direction: "horizontal",        //String: Select the sliding direction, "horizontal" or "vertical"
							reverse: false,                 //{NEW} Boolean: Reverse the animation direction
							animationLoop: true,            //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
							smoothHeight: false,            //{NEW} Boolean: Allow height of the slider to animate smoothly in horizontal mode
							startAt: 0,                     //Integer: The slide that the slider should start on. Array notation (0 = first slide)
							slideshow: $config_sideshow,    //Boolean: Animate slider automatically
							slideshowSpeed: 3000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
							animationSpeed: 600,            //Integer: Set the speed of animations, in milliseconds
							initDelay: 0,                   //{NEW} Integer: Set an initialization delay, in milliseconds
							randomize: false,               //Boolean: Randomize slide order
							// Usability features
							pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
							pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
							useCSS: true,                   //{NEW} Boolean: Slider will use CSS3 transitions if available
							touch: true,                    //{NEW} Boolean: Allow touch swipe navigation of the slider on touch-enabled devices
							video: false,                   //{NEW} Boolean: If using video in the slider, will prevent CSS3 3D Transforms to avoid graphical glitches
							// Primary Controls
							controlNav: false,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
							directionNav: false,             //Boolean: Create navigation for previous/next navigation? (true/false)
							prevText: "Previous",           //String: Set the text for the "previous" directionNav item
							nextText: "Next",               //String: Set the text for the "next" directionNav item
							// Secondary Navigation
							keyboard: true,                 //Boolean: Allow slider navigating via keyboard left/right keys
							multipleKeyboard: false,        //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.
							mousewheel: false,              //{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
							pausePlay: false,               //Boolean: Create pause/play dynamic element
							pauseText: 'Pause',             //String: Set the text for the "pause" pausePlay item
							playText: 'Play',               //String: Set the text for the "play" pausePlay item
							// Special properties
							controlsContainer: "",          //{UPDATED} Selector: USE CLASS SELECTOR. Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be ".flexslider-container". Property is ignored if given element is not found.
							manualControls: "",             //Selector: Declare custom control navigation. Examples would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
							sync: "",                       //{NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
							asNavFor: "",                   //{NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
							// Carousel Options
							itemWidth: 0,                   //{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
							itemMargin: 0,                  //{NEW} Integer: Margin between carousel items.
							minItems: 0,                    //{NEW} Integer: Minimum number of carousel items that should be visible. Items will resize fluidly when below this.
							maxItems: 0,                    //{NEW} Integer: Maxmimum number of carousel items that should be visible. Items will resize fluidly when above this limit.
							move: 0,                        //{NEW} Integer: Number of carousel items that should move on animation. If 0, slider will move all visible items.
							// Callback API
							start: function (slider) {
								slider.container.click(function () {
									if (!slider.animating) {
										slider.flexAnimate(slider.getTarget('next'));
									}
								});
							},            //Callback: function(slider) - Fires when the slider loads the first slide
							before: function () {
							},           //Callback: function(slider) - Fires asynchronously with each slider animation
							after: function () {
							},            //Callback: function(slider) - Fires after each slider animation completes
							end: function () {
							},              //Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)
							added: function () {
							},            //{NEW} Callback: function(slider) - Fires after a slide is added
							removed: function () {
							}           //{NEW} Callback: function(slider) - Fires after a slide is removed
						} );
					}
				})( jQuery );
			</script>
EOT;

//		$landing_page_id = ISSSCR_Meta_Data::get_landing_page_id();
        $image_ids = ISSSCR_Meta_Data::get_group_fields($group_id, $field_id);

        if (!empty ($image_ids)) {

            $image_ids = ISSSCR_Helpers::reduce_array_by_rows_limit($image_ids, 2);
            shuffle($image_ids);

            // Output gallery slider markup and images
            $output .= '<div class="flexslider">';
            $output .= '<ul class="slides">';
            foreach ($image_ids as $image_id) {
                $output .= '<li>' . wp_get_attachment_image($image_id, $size) . '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';

            $output .= $config;
        }

        return $output;
    }

}