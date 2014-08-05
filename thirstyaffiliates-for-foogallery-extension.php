<?php
/**
 * ThirstyAffiliates For FooGallery Extension
 *
 * Lets you create galleries of images that link to affiliate links (powered by ThirstyAffiliates)
 *
 * @package   ThirstyAffiliates For FooGallery
 * @author    ThirstyAffiliates
 * @license   GPL-2.0+
 * @link      http://thirstyaffiliates.com
 * @copyright 2014 ThirstyAffiliates
 *
 * @wordpress-plugin
 * Plugin Name: ThirstyAffiliates For FooGallery
 * Description: Lets you create galleries of images that link to affiliate links (powered by ThirstyAffiliates)
 * Version:     1.0.1
 * Author:      ThirstyAffiliates
 * Author URI:  http://thirstyaffiliates.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( !class_exists( 'ThirstyAffiliatesForFooGallery' ) ) {

	define('ThirstyAffiliates_For_FooGallery_URL', plugin_dir_url( __FILE__ ));
	define('ThirstyAffiliates_For_FooGallery_VERSION', '1.0.1');

	require_once( 'thirstyaffiliates-for-foogallery-init.php' );

	class ThirstyAffiliatesForFooGallery {

		/**
		 * Wire up everything we need to run the extension
		 */
		function __construct() {
			add_filter('foogallery_gallery_template_field_thumb_links', array($this, 'filterLinkFieldChoices'), 10, 2);
			add_filter('foogallery_attachment_html_link', array($this, 'filterThumbLinkOutput'), 10, 3);
			add_action('admin_head', array($this, 'addTAJavascriptRequirements'));
			add_action('admin_enqueue_scripts', array($this, 'addTAJavascriptForGallery'));
			add_action('foogallery_after_save_gallery', array($this, 'saveThirstyDataOnGallery'), 10, 2);

			// Register ajax call for populating the link url box on the backend
			add_action('wp_ajax_foogalleryThirstyGetLinkDetails', array($this, 'getLinkDetails')); // must have priviledges

		}

		function filterLinkFieldChoices($fields) {
			$fields['Affiliate Link'] = 'Affiliate Link';
			return $fields;
		}

		function filterThumbLinkOutput($html, $args, $img) {
			global $current_foogallery;

			$galleryAttachmentIDs = get_post_meta($current_foogallery->ID, 'foogallery_attachments', true);
			$gallerySettings = get_post_meta($current_foogallery->ID, 'foogallery_settings', true);
			$foundID = array_search($img->ID, $galleryAttachmentIDs);
			$attachmentID = $galleryAttachmentIDs[$foundID];

			if (isset($gallerySettings['default_thumbnail_link']) && $gallerySettings['default_thumbnail_link'] == 'Affiliate Link') {
				// This gallery is using Affiliate Links, lets do our thang

				// Get the affiliate link properly formatted from ThirstyAffiliates

				$thirstyAff = get_post_meta($current_foogallery->ID, 'thirstyaff', false); // get the link urls
				$linkUrl = $thirstyAff[0][$attachmentID]; // find the link url for this attachment

				/* Only do this if there has been a URL set for the current attachment
				** else, just leave it and let the link default to the current page as it normally does */
				if (isset($linkUrl) && !empty($linkUrl)) {

					// Remove the existing <a> tag stuff, we're going to replace this later
					$html = preg_replace('/\<a[^>]*\>/', '', $html, 1);
					$html = preg_replace('/\<\/a\>/', '', $html, 1);

					$linkID = url_to_postid($linkUrl); // find the link ID from the url
					$linkCodeHtml = thirstyGetLinkCode('link', $linkID, $html, '', false); // get the link code

					return $linkCodeHtml;
				}
			}

			// If not using affiliate links, just return with nothing changed
			return $html;
		}

		function saveThirstyDataOnGallery($post_id, $POSTDATA) {
			update_post_meta($post_id, 'thirstyaff', $POSTDATA['thirstyaff']);
		}

		function getLinkDetails() {
			$attachmentID = $_POST['attachmentID'];
			$galleryID = $_POST['galleryID'];

			$thirstyAff = get_post_meta($galleryID, 'thirstyaff', false);

			$linkUrl = $thirstyAff[0][$attachmentID];

			echo $linkUrl;
			die();
		}

		function addTAJavascriptForGallery() {
			wp_enqueue_script('thirstyaffiliates-for-foogallery-gallery-edit-screen-enhancements', ThirstyAffiliates_For_FooGallery_URL . 'js/gallery-edit-screen-enhancements.js', array('jquery'), ThirstyAffiliates_For_FooGallery_VERSION);
			wp_enqueue_style('thirstyaffiliates-for-foogallery-gallery-edit-screen-enhancements', ThirstyAffiliates_For_FooGallery_URL . 'css/gallery-edit-screen-enhancements.css', array(), ThirstyAffiliates_For_FooGallery_VERSION);
		}

		function addTAJavascriptRequirements() {
			echo '<script type="text/javascript">
			var thirstyAffiliates_For_FooGallery_URL = "' . ThirstyAffiliates_For_FooGallery_URL . '";
			</script>';
		}

	}
}
