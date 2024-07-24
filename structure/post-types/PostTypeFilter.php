<?php

defined( 'ABSPATH' ) || exit;

class PostTypeFilter {

	private static $postType;

	public static function init( $postType )
	{
		self::$postType = $postType;

		add_action( 'restrict_manage_posts', [ __CLASS__, 'outputFilters' ] );
	}

	public static function outputFilters()
	{
		$taxonomies = get_object_taxonomies( self::$postType, 'objects' );

		array_walk( $taxonomies, [ __CLASS__, 'outputFilterFor' ] );
	}

	private static function outputFilterFor( $taxonomy )
	{
		global $typenow;

		if ( $typenow == self::$postType ) {
			wp_dropdown_categories(
				[
					'show_option_all' => sprintf( __( 'All %s', 'admin-taxonomy-filter' ), $taxonomy->label ),
					'orderby'         => 'name',
					'order'           => 'ASC',
					'hide_empty'      => false,
					'hide_if_empty'   => true,
					'selected'        => filter_input( INPUT_GET, $taxonomy->query_var, FILTER_SANITIZE_STRING ),
					'hierarchical'    => true,
					'name'            => $taxonomy->query_var,
					'taxonomy'        => $taxonomy->name,
					'value_field'     => 'slug',
				]
			);
		}
	}
}

PostTypeFilter::init('signatory-report');
