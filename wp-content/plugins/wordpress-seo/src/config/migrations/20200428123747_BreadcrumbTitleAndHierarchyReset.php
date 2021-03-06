<?php
/**
 * Yoast SEO Plugin File.
 *
 * @package WPSEO\Migrations
 */

use Yoast\WP\Lib\Model;
use YoastSEO_Vendor\Ruckusing_Migration_Base;

/**
 * BreadcrumbTitleAndHierarchyReset
 */
class BreadcrumbTitleAndHierarchyReset extends Ruckusing_Migration_Base {
	/**
	 * Migration up.
	 */
	public function up() {
		$this->change_column( $this->get_indexable_table_name(), 'breadcrumb_title', 'text', [ 'null' => true ] );
		$this->query( 'DELETE FROM ' . $this->get_indexable_hierarchy_table_name() );
	}

	/**
	 * Migration down.
	 */
	public function down() {
		$this->change_column(
			$this->get_indexable_table_name(),
			'breadcrumb_title',
			'string',
			[ 'null' => true, 'limit' => 191 ]
		);
	}

	/**
	 * Retrieves the table name to use for storing indexables.
	 *
	 * @return string The table name to use.
	 */
	protected function get_indexable_table_name() {
		return Model::get_table_name( 'Indexable' );
	}

	/**
	 * Retrieves the table name to use.
	 *
	 * @return string The table name to use.
	 */
	protected function get_indexable_hierarchy_table_name() {
		return Model::get_table_name( 'Indexable_Hierarchy' );
	}
}
