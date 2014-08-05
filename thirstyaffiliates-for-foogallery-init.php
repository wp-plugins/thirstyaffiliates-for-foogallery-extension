<?php
//This init class is used to add the extension to the extensions list while you are developing them.
//When the extension is added to the supported list of extensions, this file is no longer needed.

if ( !class_exists( 'ThirstyAffiliatesForFooGallery_Init' ) ) {
	class ThirstyAffiliatesForFooGallery_Init {

		function __construct() {
			add_filter( 'foogallery_available_extensions', array( $this, 'add_to_extensions_list' ) );
		}

		function add_to_extensions_list( $extensions ) {
			$extensions[] = array(
				'slug'=> 'thirstyaffiliates-for-foogallery-extension',
				'class'=> 'ThirstyAffiliatesForFooGallery',
				'title'=> 'ThirstyAffiliates',
				'description'=> 'Lets you create galleries of images that link to affiliate links (powered by ThirstyAffiliates)',
				'author'=> 'ThirstyAffiliates',
				'author_url'=> 'http://thirstyaffiliates.com',
				'thumbnail'=> ThirstyAffiliates_For_FooGallery_URL . '/assets/extension_bg.png',
				'categories'=> array('Build Your Own'),
				'tags'=> array('Affiliate Marketing', 'Affiliate Links')
			);

			return $extensions;
		}
	}

	new ThirstyAffiliatesForFooGallery_Init();
}
