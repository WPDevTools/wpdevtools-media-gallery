<?php
/*
Plugin Name: WPDevTools Media Gallery
Plugin URI: http://wpdevtools.com/plugins/media-gallery/
Description: Displays a customizable list of files available in the site Media library
Author: Christopher Frazier, David Sutoyo
Version: 0.1
Author URI: http://wpdevtools.com/
*/

require_once dirname( __FILE__ ) . '/lib/wpdevtools-core/wpdevtools-core.php';

/**
 * WPDT_MediaGallery
 *
 * Core functionality for the Media Gallery plugin
 * 
 * @author Christopher Frazier, David Sutoyo
 */
class WPDT_MediaGallery extends WPDT_Core
{

	/**
	 * Sets up the plugin JS and CSS
	 */
	public function enqueue_scripts ()
	{
		if (!is_admin()) {
			// wp_enqueue_script("wpdevtools_media_gallery", plugins_url('/lib/tablesorter/jquery.tablesorter.js', __FILE__), array('jquery'));
			// wp_enqueue_style("wpdevtools_media_gallery", plugins_url('/style.css', __FILE__));
		}
	}

	/**
	 * Displays a list of items from the media library using the WPDT template system
	 *
	 * @author Christopher Frazier
	 * @param $atts		An array of the short code attributes specified by the user
	 * @param $content	A string of the text inside the start and end short code blocks.  This is the template code for output
	 * @return string	The processed HTML
	 */
	public function display ($atts, $content = null)
	{

		extract(shortcode_atts(array(
			'count' => null,
			'order' => 'DESC',
			'order_by' => 'post_date',
			'post_type' => 'attachment',
			'type' => null
		), $atts));

		// Split up the comma separated string
		$mime_types = split(',', $type);

		$items = self::get_content(array(
			'numberposts' => $count,
			'orderby' => $order_by,
			'order' => $order,
			'post_mime_type' => $mime_types,
			'post_type' => $post_type
		));

		$html = '';

		if ($content != null) {
			$pre_html = '<span class="wpdt_media_gallery">';
			$template = $content;
			$post_html = '</span>';
		} else {
			$pre_html = '<table class="wpdt_media_gallery"><tr><th></th><th>Title</th><th>Description</th><th>Date</th></tr>';
			$template = '<tr><td class="icon"><a href="[permalink]"><img src="[thumbnail_src]" height="16"></a></td><td class="post_title"><a href="[permalink]">[post_title]</a></td><td class="post_content">[post_content]</td><td class="post_date">[post_date format="n/j/Y"]</td></tr>';
			$post_html = '</table>';
		}
		
		foreach ($items as $item) {
			$html .= self::replace_template_tags($template, $item);
		}
		
		$html = $pre_html . $html . $post_html;
		
		return $html;

	}
}

// Set up the WordPress hooks
add_shortcode('media_gallery', 'WPDT_MediaGallery::display');
add_action('init', 'WPDT_MediaGallery::enqueue_scripts');