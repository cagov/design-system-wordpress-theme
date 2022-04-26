<?php
/**
 * Navigation elements.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'cagov_navigation_position' ) ) {
	/**
	 * Build the navigation.
	 *
	 * @since 0.1
	 */
	function cagov_navigation_position() {
		/**
		 * cagov_before_navigation hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'cagov_before_navigation' );
		?>
		<nav <?php cagov_do_attr( 'navigation' ); ?>>
			<div <?php cagov_do_attr( 'inside-navigation' ); ?>>
				<?php
				/**
				 * cagov_inside_navigation hook.
				 *
				 * @since 0.1
				 *
				 * @hooked cagov_navigation_search - 10
				 * @hooked cagov_mobile_menu_search_icon - 10
				 */
				do_action( 'cagov_inside_navigation' );
				?>
				<button <?php cagov_do_attr( 'menu-toggle' ); ?>>
					<?php
					/**
					 * cagov_inside_mobile_menu hook.
					 *
					 * @since 0.1
					 */
					do_action( 'cagov_inside_mobile_menu' );

					cagov_do_svg_icon( 'menu-bars', true );

					$mobile_menu_label = apply_filters( 'cagov_mobile_menu_label', __( 'Menu', 'generatepress' ) );

					if ( $mobile_menu_label ) {
						printf(
							'<span class="mobile-menu">%s</span>',
							$mobile_menu_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML allowed in filter.
						);
					} else {
						printf(
							'<span class="screen-reader-text">%s</span>',
							esc_html__( 'Menu', 'generatepress' )
						);
					}
					?>
				</button>
				<?php
				/**
				 * cagov_after_mobile_menu_button hook
				 *
				 * @since 3.0.0
				 */
				do_action( 'cagov_after_mobile_menu_button' );

				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container' => 'div',
						'container_class' => 'main-nav',
						'container_id' => 'primary-menu',
						'menu_class' => '',
						'fallback_cb' => 'cagov_menu_fallback',
						'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', cagov_get_element_classes( 'menu' ) ) . '">%3$s</ul>',
					)
				);

				/**
				 * cagov_after_primary_menu hook.
				 *
				 * @since 2.3
				 */
				do_action( 'cagov_after_primary_menu' );
				?>
			</div>
		</nav>
		<?php
		/**
		 * cagov_after_navigation hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'cagov_after_navigation' );
	}
}

add_action( 'cagov_before_navigation', 'cagov_do_header_mobile_menu_toggle' );
/**
 * Build the mobile menu toggle in the header.
 *
 * @since 3.0.0
 */
function cagov_do_header_mobile_menu_toggle() {
	if ( ! cagov_is_using_flexbox() ) {
		return;
	}

	if ( ! cagov_has_inline_mobile_toggle() ) {
		return;
	}
	?>
	<nav <?php cagov_do_attr( 'mobile-menu-control-wrapper' ); ?>>
		<?php
		/**
		 * cagov_inside_mobile_menu_control_wrapper hook.
		 *
		 * @since 3.0.0
		 */
		do_action( 'cagov_inside_mobile_menu_control_wrapper' );
		?>
		<button <?php cagov_do_attr( 'menu-toggle', array( 'data-nav' => 'site-navigation' ) ); ?>>
			<?php
			/**
			 * cagov_inside_mobile_menu hook.
			 *
			 * @since 0.1
			 */
			do_action( 'cagov_inside_mobile_menu' );

			cagov_do_svg_icon( 'menu-bars', true );

			$mobile_menu_label = __( 'Menu', 'generatepress' );

			if ( 'nav-float-right' === cagov_get_navigation_location() || 'nav-float-left' === cagov_get_navigation_location() ) {
				$mobile_menu_label = '';
			}

			$mobile_menu_label = apply_filters( 'cagov_mobile_menu_label', $mobile_menu_label );

			if ( $mobile_menu_label ) {
				printf(
					'<span class="mobile-menu">%s</span>',
					$mobile_menu_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML allowed in filter.
				);
			} else {
				printf(
					'<span class="screen-reader-text">%s</span>',
					esc_html__( 'Menu', 'generatepress' )
				);
			}
			?>
		</button>
	</nav>
	<?php
}

if ( ! function_exists( 'cagov_menu_fallback' ) ) {
	/**
	 * Menu fallback.
	 *
	 * @since 1.1.4
	 *
	 * @param array $args Existing menu args.
	 */
	function cagov_menu_fallback( $args ) {
		$cagov_settings = wp_parse_args(
			get_option( 'cagov_settings', array() ),
			cagov_get_defaults()
		);
		?>
		<div id="primary-menu" class="main-nav">
			<ul <?php cagov_do_element_classes( 'menu' ); ?>>
				<?php
				$args = array(
					'sort_column' => 'menu_order',
					'title_li' => '',
					'walker' => new CaGov_Wp_Theme(),
				);

				wp_list_pages( $args );

				if ( ! cagov_is_using_flexbox() && 'enable' === $cagov_settings['nav_search'] ) {
					$search_item = apply_filters(
						'cagov_navigation_search_menu_item_output',
						sprintf(
							'<li class="search-item menu-item-align-right"><a aria-label="%1$s" href="#">%2$s</a></li>',
							esc_attr__( 'Open Search Bar', 'generatepress' ),
							cagov_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
						)
					);

					echo $search_item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Safe output.
				}
				?>
			</ul>
		</div>
		<?php
	}
}

if ( ! function_exists( 'cagov_add_navigation_after_header' ) ) {
	add_action( 'cagov_after_header', 'cagov_add_navigation_after_header', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function cagov_add_navigation_after_header() {
		if ( 'nav-below-header' === cagov_get_navigation_location() ) {
			cagov_navigation_position();
		}
	}
}

if ( ! function_exists( 'cagov_add_navigation_before_header' ) ) {
	add_action( 'cagov_before_header', 'cagov_add_navigation_before_header', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function cagov_add_navigation_before_header() {
		if ( 'nav-above-header' === cagov_get_navigation_location() ) {
			cagov_navigation_position();
		}
	}
}

if ( ! function_exists( 'cagov_add_navigation_float_right' ) ) {
	add_action( 'cagov_after_header_content', 'cagov_add_navigation_float_right', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function cagov_add_navigation_float_right() {
		if ( 'nav-float-right' === cagov_get_navigation_location() || 'nav-float-left' === cagov_get_navigation_location() ) {
			cagov_navigation_position();
		}
	}
}

if ( ! function_exists( 'cagov_add_navigation_before_right_sidebar' ) ) {
	add_action( 'cagov_before_right_sidebar_content', 'cagov_add_navigation_before_right_sidebar', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function cagov_add_navigation_before_right_sidebar() {
		if ( 'nav-right-sidebar' === cagov_get_navigation_location() ) {
			echo '<div class="gen-sidebar-nav">';
				cagov_navigation_position();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'cagov_add_navigation_before_left_sidebar' ) ) {
	add_action( 'cagov_before_left_sidebar_content', 'cagov_add_navigation_before_left_sidebar', 5 );
	/**
	 * Generate the navigation based on settings
	 *
	 * It would be better to have all of these inside one action, but these
	 * are kept this way to maintain backward compatibility for people
	 * un-hooking and moving the navigation/changing the priority.
	 *
	 * @since 0.1
	 */
	function cagov_add_navigation_before_left_sidebar() {
		if ( 'nav-left-sidebar' === cagov_get_navigation_location() ) {
			echo '<div class="gen-sidebar-nav">';
				cagov_navigation_position();
			echo '</div>';
		}
	}
}

if ( ! class_exists( 'CaGov_Wp_Theme' ) && class_exists( 'Walker_Page' ) ) {
	/**
	 * Add current-menu-item to the current item if no theme location is set
	 * This means we don't have to duplicate CSS properties for current_page_item and current-menu-item
	 *
	 * @since 1.3.21
	 */
	class CaGov_Wp_Theme extends Walker_Page {
		function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) { // phpcs:ignore
			$css_class = array( 'page_item', 'page-item-' . $page->ID );
			$button = '';

			if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
				$css_class[] = 'menu-item-has-children';
				$icon = cagov_get_svg_icon( 'arrow' );
				$button = '<span role="presentation" class="dropdown-menu-toggle">' . $icon . '</span>';
			}

			if ( ! empty( $current_page ) ) {
				$_current_page = get_post( $current_page );
				if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
					$css_class[] = 'current-menu-ancestor';
				}

				if ( $page->ID == $current_page ) { // phpcs:ignore
					$css_class[] = 'current-menu-item';
				} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) { // phpcs:ignore
					$css_class[] = 'current-menu-parent';
				}
			} elseif ( $page->ID == get_option( 'page_for_posts' ) ) { // phpcs:ignore
				$css_class[] = 'current-menu-parent';
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter name.
			$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

			$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
			$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

			$output .= sprintf(
				'<li class="%s"><a href="%s">%s%s%s%s</a>',
				$css_classes,
				get_permalink( $page->ID ),
				$args['link_before'],
				apply_filters( 'the_title', $page->post_title, $page->ID ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter name.
				$args['link_after'],
				$button
			);
		}
	}
}

if ( ! function_exists( 'cagov_dropdown_icon_to_menu_link' ) ) {
	add_filter( 'nav_menu_item_title', 'cagov_dropdown_icon_to_menu_link', 10, 4 );
	/**
	 * Add dropdown icon if menu item has children.
	 *
	 * @since 1.3.42
	 *
	 * @param string   $title The menu item title.
	 * @param WP_Post  $item All of our menu item data.
	 * @param stdClass $args All of our menu item args.
	 * @param int      $depth Depth of menu item.
	 * @return string The menu item.
	 */
	function cagov_dropdown_icon_to_menu_link( $title, $item, $args, $depth ) {
		$role = 'presentation';
		$tabindex = '';

		if ( 'click-arrow' === cagov_get_option( 'nav_dropdown_type' ) ) {
			$role = 'button';
			$tabindex = ' tabindex="0"';
		}

		if ( isset( $args->container_class ) && 'main-nav' === $args->container_class ) {
			foreach ( $item->classes as $value ) {
				if ( 'menu-item-has-children' === $value ) {
					$arrow_direction = 'down';

					if ( 'primary' === $args->theme_location ) {
						if ( 0 !== $depth ) {
							$arrow_direction = 'right';

							if ( 'left' === cagov_get_option( 'nav_dropdown_direction' ) ) {
								$arrow_direction = 'left';
							}
						}

						if ( 'nav-left-sidebar' === cagov_get_navigation_location() ) {
							$arrow_direction = 'right';

							if ( 'both-right' === cagov_get_layout() ) {
								$arrow_direction = 'left';
							}
						}

						if ( 'nav-right-sidebar' === cagov_get_navigation_location() ) {
							$arrow_direction = 'left';

							if ( 'both-left' === cagov_get_layout() ) {
								$arrow_direction = 'right';
							}
						}

						if ( 'hover' !== cagov_get_option( 'nav_dropdown_type' ) ) {
							$arrow_direction = 'down';
						}
					}

					$arrow_direction = apply_filters( 'cagov_menu_item_dropdown_arrow_direction', $arrow_direction, $args, $depth );

					if ( 'down' === $arrow_direction ) {
						$arrow_direction = '';
					} else {
						$arrow_direction = '-' . $arrow_direction;
					}

					$icon = cagov_get_svg_icon( 'arrow' . $arrow_direction );
					$title = $title . '<span role="' . $role . '" class="dropdown-menu-toggle"' . $tabindex . '>' . $icon . '</span>';
				}
			}
		}

		return $title;
	}
}

if ( ! function_exists( 'cagov_navigation_search' ) ) {
	add_action( 'cagov_inside_navigation', 'cagov_navigation_search' );
	/**
	 * Add the search bar to the navigation.
	 *
	 * @since 1.1.4
	 */
	function cagov_navigation_search() {
		$cagov_settings = wp_parse_args(
			get_option( 'cagov_settings', array() ),
			cagov_get_defaults()
		);

		if ( 'enable' !== $cagov_settings['nav_search'] ) {
			return;
		}

		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'cagov_navigation_search_output',
			sprintf(
				'<form method="get" class="search-form navigation-search" action="%1$s">
					<input type="search" class="search-field" value="%2$s" name="s" title="%3$s" />
				</form>',
				esc_url( home_url( '/' ) ),
				esc_attr( get_search_query() ),
				esc_attr_x( 'Search', 'label', 'generatepress' )
			)
		);
	}
}

add_action( 'cagov_after_primary_menu', 'cagov_do_menu_bar_item_container' );
add_action( 'cagov_inside_mobile_menu_control_wrapper', 'cagov_do_menu_bar_item_container' );
/**
 * Add a container for menu bar items.
 *
 * @since 3.0.0
 */
function cagov_do_menu_bar_item_container() {
	if ( ! cagov_is_using_flexbox() ) {
		return;
	}

	if ( cagov_has_menu_bar_items() ) {
		echo '<div class="menu-bar-items">';
			do_action( 'cagov_menu_bar_items' );
		echo '</div>';
	}
}

add_action( 'wp', 'cagov_add_menu_bar_items' );
/**
 * Add menu bar items to the primary navigation.
 *
 * @since 3.0.0
 */
function cagov_add_menu_bar_items() {
	if ( ! cagov_is_using_flexbox() ) {
		return;
	}

	if ( 'enable' === cagov_get_option( 'nav_search' ) ) {
		add_action( 'cagov_menu_bar_items', 'cagov_do_navigation_search_button' );
	}
}

/**
 * Add the navigation search button.
 *
 * @since 3.0.0
 */
function cagov_do_navigation_search_button() {
	if ( ! cagov_is_using_flexbox() ) {
		return;
	}

	if ( 'enable' !== cagov_get_option( 'nav_search' ) ) {
		return;
	}

	$search_item = apply_filters(
		'cagov_navigation_search_menu_item_output',
		sprintf(
			'<span class="menu-bar-item search-item"><a aria-label="%1$s" href="#">%2$s</a></span>',
			esc_attr__( 'Open Search Bar', 'generatepress' ),
			cagov_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
		)
	);

	echo $search_item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- No escaping needed.
}

if ( ! function_exists( 'cagov_menu_search_icon' ) ) {
	add_filter( 'wp_nav_menu_items', 'cagov_menu_search_icon', 10, 2 );
	/**
	 * Add search icon to primary menu if set.
	 * Only used if using old float system.
	 *
	 * @since 1.2.9.7
	 *
	 * @param string   $nav The HTML list content for the menu items.
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 * @return string The search icon menu item.
	 */
	function cagov_menu_search_icon( $nav, $args ) {
		$cagov_settings = wp_parse_args(
			get_option( 'cagov_settings', array() ),
			cagov_get_defaults()
		);

		if ( cagov_is_using_flexbox() ) {
			return $nav;
		}

		// If the search icon isn't enabled, return the regular nav.
		if ( 'enable' !== $cagov_settings['nav_search'] ) {
			return $nav;
		}

		// If our primary menu is set, add the search icon.
		if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$search_item = apply_filters(
				'cagov_navigation_search_menu_item_output',
				sprintf(
					'<li class="search-item menu-item-align-right"><a aria-label="%1$s" href="#">%2$s</a></li>',
					esc_attr__( 'Open Search Bar', 'generatepress' ),
					cagov_get_svg_icon( 'search', true ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function.
				)
			);

			return $nav . $search_item;
		}

		// Our primary menu isn't set, return the regular nav.
		// In this case, the search icon is added to the cagov_menu_fallback() function in navigation.php.
		return $nav;
	}
}

if ( ! function_exists( 'cagov_mobile_menu_search_icon' ) ) {
	add_action( 'cagov_inside_navigation', 'cagov_mobile_menu_search_icon' );
	/**
	 * Add search icon to mobile menu bar.
	 * Only used if using old float system.
	 *
	 * @since 1.3.12
	 */
	function cagov_mobile_menu_search_icon() {
		$cagov_settings = wp_parse_args(
			get_option( 'cagov_settings', array() ),
			cagov_get_defaults()
		);

		// If the search icon isn't enabled, return the regular nav.
		if ( 'enable' !== $cagov_settings['nav_search'] ) {
			return;
		}

		if ( cagov_is_using_flexbox() ) {
			return;
		}

		?>
		<div class="mobile-bar-items">
			<?php do_action( 'cagov_inside_mobile_menu_bar' ); ?>
			<span class="search-item">
				<a aria-label="<?php esc_attr_e( 'Open Search Bar', 'generatepress' ); ?>" href="#">
					<?php cagov_do_svg_icon( 'search', true ); ?>
				</a>
			</span>
		</div>
		<?php
	}
}

add_action( 'wp_footer', 'cagov_clone_sidebar_navigation' );
/**
 * Clone our sidebar navigation and place it below the header.
 * This places our mobile menu in a more user-friendly location.
 *
 * We're not using wp_add_inline_script() as this needs to happens
 * before menu.js is enqueued.
 *
 * @since 2.0
 */
function cagov_clone_sidebar_navigation() {
	if ( 'nav-left-sidebar' !== cagov_get_navigation_location() && 'nav-right-sidebar' !== cagov_get_navigation_location() ) {
		return;
	}
	?>
	<script>
		var target, nav, clone;
		nav = document.getElementById( 'site-navigation' );
		if ( nav ) {
			clone = nav.cloneNode( true );
			clone.className += ' sidebar-nav-mobile';
			clone.setAttribute( 'aria-label', '<?php esc_attr_e( 'Mobile Menu', 'generatepress' ); ?>' );
			target = document.getElementById( 'masthead' );
			if ( target ) {
				target.insertAdjacentHTML( 'afterend', clone.outerHTML );
			} else {
				document.body.insertAdjacentHTML( 'afterbegin', clone.outerHTML )
			}
		}
	</script>
	<?php
}
