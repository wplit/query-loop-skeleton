<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A custom element that runs a Bricks query loop but prints no item per post. It prints the posts as one JSON block,
 * and json-loop.js draws the display from that, the way a calendar, map or chart would.
 */
class Bricks_Json_Loop_Element extends \Bricks\Element {

	public $category = 'general';
	public $name     = 'jsonloop';
	public $icon     = 'ti-list';

	// The function Bricks runs to set up the element in the builder.
	public $scripts = array( 'bricksJsonLoopInit' );

	/**
	 * The posts collected while the query runs.
	 *
	 * @var array
	 */
	private $items = array();

	public function get_label() {
		return 'JSON Loop';
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'bricks-json-loop', plugins_url( 'json-loop.js', __FILE__ ), array( 'bricks-scripts' ), '1.0.0', true );
	}

	/**
	 * The same two settings as any Bricks query loop. Having them is what makes the element a target for query filters.
	 */
	public function set_controls() {
		$this->controls['hasLoop'] = array(
			'tab'   => 'content',
			'label' => 'Query loop',
			'type'  => 'checkbox',
		);

		$this->controls['query'] = array(
			'tab'      => 'content',
			'label'    => 'Query',
			'type'     => 'query',
			'popup'    => true,
			'inline'   => true,
			'required' => array( 'hasLoop', '!=', '' ),
		);
	}

	/**
	 * Runs once per post. It collects the post and prints nothing, so there is no DOM item per post.
	 */
	public function collect_item() {
		$this->items[] = array(
			'id'    => get_the_ID(),
			'title' => html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ),
		);

		return '';
	}

	/**
	 * Also runs when a filter changes: Bricks renders the element again with the filtered query and sends the HTML back.
	 */
	public function render() {
		$this->items = array();

		if ( ! empty( $this->settings['hasLoop'] ) ) {
			// Runs the query, with any active filters applied by Bricks, calling collect_item() for each post.
			$query = new \Bricks\Query(
				array(
					'id'       => $this->id,
					'settings' => $this->settings,
				)
			);

			if ( $query->count > 0 ) {
				$query->render( array( $this, 'collect_item' ), array() );
			}

			$query->destroy();
			unset( $query );
		}

		// How json-loop.js knows which query this element belongs to.
		$this->set_attribute( '_root', 'data-query-id', $this->id );

		// One JSON block and an empty list, which json-loop.js fills in.
		echo "<div {$this->render_attributes( '_root' )}>";
		echo '<script type="application/json" class="json-loop-data">' . wp_json_encode( $this->items, JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>';
		echo '<ul class="json-loop-list"></ul>';
		echo '</div>';
	}
}
