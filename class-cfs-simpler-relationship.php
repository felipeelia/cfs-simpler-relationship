<?php
/**
 * Field class file.
 *
 * @package CFS_Simpler_Relationship
 */

/**
 * Field class.
 */
class cfs_simpler_relationship extends cfs_relationship {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name  = 'simpler_relationship';
		$this->label = __( 'Simple Relationship', 'cfs-simpler-relationship' );
	}

	/**
	 * Show the field to user.
	 *
	 * @param  cfs_field $field Field instance.
	 */
	function html( $field ) {
		global $wpdb;

		$selected_posts  = array();
		$available_posts = array();

		$post_types = array();
		if ( ! empty( $field->options['post_types'] ) ) {
			foreach ( $field->options['post_types'] as $type ) {
				$post_types[] = $type;
			}
		} else {
			$post_types = get_post_types( array( 'exclude_from_search' => true ) );
		}

		$args = array(
			'post_type'      => $post_types,
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$args  = apply_filters( 'cfs_field_simpler_relationship_query_args', $args, array( 'field' => $field ) );
		$query = new WP_Query( $args );

		$multiple = '';
		if ( ! empty( $field->options['multiple'] ) ) {
			$multiple            = ' multiple';
			$field->input_class .= ' multiple';
		}

		?>
		<select name="<?php echo $field->input_name; ?>" class="<?php echo trim( $field->input_class ); ?>"<?php echo $multiple; ?>>
			<option value=""><?php _e( 'Select', 'cfs-simpler-relationship' ); ?></option>
			<?php
			foreach ( $query->posts as $available_post ) {
				if ( 'private' == $available_post->post_status ) {
					$post_title = sprintf(
						/* translators: post name */
						__( '(Private) %s', 'cfs-simpler-relationship' ),
						$available_post->post_title
					);
				} else {
					$post_title = $available_post->post_title;
				}
				?>
				<option
					value="<?php echo $available_post->ID; ?>"
					<?php selected( in_array( $available_post->ID, (array) $field->value ) ); ?>>
						<?php echo $post_title; ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Generate field settings.
	 *
	 * @param  string    $key   Field name.
	 * @param  cfs_field $field Field instance.
	 */
	function options_html( $key, $field ) {
		$args    = array( 'exclude_from_search' => false );
		$choices = apply_filters( 'cfs_field_simpler_relationship_post_types', get_post_types( $args ) );

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e( 'Post Types', 'cfs' ); ?></label>
				<p class="description"><?php _e( 'Limit posts to the following types', 'cfs' ); ?></p>
			</td>
			<td>
				<?php
					CFS()->create_field( array(
						'type'       => 'select',
						'input_name' => "cfs[fields][{$key}][options][post_types]",
						'options'    => array(
							'multiple' => '1',
							'choices'  => $choices,
						),
						'value'      => $this->get_option( $field, 'post_types' ),
					));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e( 'Multi-select?', 'cfs' ); ?></label>
			</td>
			<td>
				<?php
					CFS()->create_field( array(
						'type'        => 'true_false',
						'input_name'  => "cfs[fields][$key][options][multiple]",
						'input_class' => 'true_false',
						'value'       => $this->get_option( $field, 'multiple' ),
						'options'     => array( 'message' => __( 'This is a multi-select field', 'cfs' ) ),
					) );
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Format the value for use with CFS()->get
	 *
	 * @param mixed $value Field value.
	 * @param mixed $field The field object (optional).
	 * @return mixed
	 */
	function format_value_for_api( $value, $field = null ) {
		if ( is_object( $field ) && empty( $field->options['multiple'] ) ) {
			return current( (array) $value );
		}
		return $value;
	}

	/**
	 * Just because we don't need all that JS.
	 *
	 * @param  cfs_field $field Field instance.
	 */
	public function input_head( $field = null ) {}
}
