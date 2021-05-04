<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;

use stdClass;

/**
 * Class Loader
 */
class Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    0.9.0
	 * @access   protected
	 * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions = array();
	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    0.9.0
	 * @access   protected
	 * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.9.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param string $hook The name of the WordPress action that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the action is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 *
	 * @since    0.9.0
	 */
	public function add_action( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove action.
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority The priority at which the function should be fired.
	 */
	public function remove_action( string $hook, $component, string $callback, int $priority = 10 ): void {
		$signature = $this->sign( $hook, $component, $callback, $priority );

		$this->actions = $this->remove( $this->actions, $signature );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
	 * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 *
	 * @since    0.9.0
	 */
	public function add_filter( string $hook, $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove filter.
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority The priority at which the function should be fired.
	 */
	public function remove_filter( string $hook, $component, string $callback, int $priority = 10 ): void {
		$signature = $this->sign( $hook, $component, $callback, $priority );

		$this->filters = $this->remove( $this->filters, $signature );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority The priority at which the function should be fired.
	 * @param int $accepted_args The number of arguments that should be passed to the $callback.
	 *
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 * @since    0.9.0
	 * @access   private
	 */
	private function add( array $hooks, string $hook, $component, string $callback, int $priority, int $accepted_args ): array {
		$signature = $this->sign( $hook, $component, $callback, $priority );

		$hooks[ $signature ] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Remove hook from hooks.
	 *
	 * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
	 * @param string $signature Signature of hook.
	 *
	 * @return array
	 */
	private function remove( array $hooks, string $signature ): array {
		if ( true === isset( $hooks[ $signature ] ) ) {
			unset( $hooks[ $signature ] );
		}

		return $hooks;
	}

	/**
	 * Generate sha1 hash for given hook.
	 *
	 * @param string $hook The name of the WordPress filter that is being registered.
	 * @param mixed $component A reference to the instance of the object on which the filter is defined.
	 * @param string $callback The name of the function definition on the $component.
	 * @param int $priority The priority at which the function should be fired.
	 *
	 * @return string
	 */
	private function sign( string $hook, $component, string $callback, int $priority ): string {
		if ( false === is_object( $component ) ) {
			$component = new stdClass();
		}

		return sha1( sprintf( '%s[%s-%s]%d', $hook, get_class( $component ), $callback, $priority ) );
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    0.9.0
	 */
	public function run(): void {
		$this->run_filters();
		$this->run_actions();
	}

	/**
	 * Run filters.
	 *
	 * @since 2.2.1
	 */
	protected function run_filters(): void {
		foreach ( $this->filters as $hook ) {
			$method = array( $hook['component'], $hook['callback'] );
			if ( true === is_null( $hook['component'] ) ) {
				$method = $hook['callback'];
			}
			$callable = is_callable( $method, true );

			if ( true === $callable ) {
				add_filter(
					$hook['hook'],
					$method,
					$hook['priority'],
					$hook['accepted_args']
				);
			}
		}
	}

	/**
	 * Run actions.
	 *
	 * @since 2.2.1
	 */
	protected function run_actions(): void {
		foreach ( $this->actions as $hook ) {
			$method = array( $hook['component'], $hook['callback'] );
			if ( true === is_null( $hook['component'] ) ) {
				$method = $hook['callback'];
			}
			$callable = is_callable( $method, true );

			if ( true === $callable ) {
				add_action(
					$hook['hook'],
					$method,
					$hook['priority'],
					$hook['accepted_args']
				);
			}
		}
	}
}
