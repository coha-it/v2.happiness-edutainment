<?php
if ( !class_exists( 'Learndash_Admin_Data_Upgrades_Course_Meta' ) ) {
	class Learndash_Admin_Data_Upgrades_Course_Meta extends Learndash_Admin_Settings_Data_Upgrades {
		
		public static $instance = null;

		private $transient_key = '';
		private $transient_data = array();

		function __construct() {
			self::$instance =& $this;

			$this->data_slug = 'course-access-lists';
			$this->meta_key = 'ld-upgraded-'. $this->data_slug;

			add_filter( 'learndash_admin_settings_upgrades_register_actions', array( $this, 'register_upgrade_action' ) );
		}

		public static function getInstance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		function register_upgrade_action( $upgrade_actions = array() ) {
			// Add ourselved to the upgrade actions.
			$upgrade_actions[ $this->data_slug ] = array(
				'class'		=> get_class( $this ),
				'instance'	=> $this,
				'slug'		=> $this->data_slug
			);

			return $upgrade_actions;
		}

		function show_upgrade_action() {
			?>
			<tr id="learndash-data-upgrades-container-<?php echo $this->data_slug ?>" class="learndash-data-upgrades-container">
				<td class="learndash-data-upgrades-button-container" style="width:20%">
					<button class="learndash-data-upgrades-button button button-primary" data-nonce="<?php echo wp_create_nonce( 'learndash-data-upgrades-'. $this->data_slug .'-'. get_current_user_id() ); ?>" data-slug="<?php echo $this->data_slug ?>"><?php printf( _x( 'Upgrade %s Meta', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></button></td>
				<td class="learndash-data-upgrades-status-container" style="width: 80%">
					<p><?php printf( _x('This upgrade will upgrade the %s Meta elements. (Optional)', 'placeholder: Course', 'learndash'), LearnDash_Custom_Label::get_label( 'course' ) ) ?></p>
					<p class="description"><?php echo $this->get_last_run_info(); ?></p>	
						
					<div style="display:none;" class="meter learndash-data-upgrades-status">
						<div class="progress-meter">
							<span class="progress-meter-image"></span>
						</div>
						<div class="progress-label"></div>
					</div>
				</td>
			</tr>
			<?php
		}
				
		/**
		 * Class method for the AJAX update logic
		 * This function will determine what users need to be converted. Then the course and quiz functions
		 * will be called to convert each individual user data set.
		 *
		 * @since 2.3
		 * 
		 * @param  array 	$data 		Post data from AJAX call
		 * @return array 	$data 		Post data from AJAX call
		 */
		function process_upgrade_action( $data = array() ) {
			global $wpdb;

			$this->init_process_times();

			if ( ( isset( $data['nonce'] ) ) && ( !empty( $data['nonce'] ) ) ) {
				if ( wp_verify_nonce( $data['nonce'], 'learndash-data-upgrades-'. $this->data_slug .'-'. get_current_user_id() ) ) {
					$this->transient_key = $this->data_slug .'_'. $data['nonce'];

					if ( ( isset( $data['init'] ) ) && ( $data['init'] == true ) ) {
						unset( $data['init'] );

						$data['total_count'] = 0;
						$data['process_courses'] = 0;
						$data['result_count'] = 0;
						$data['progress_percent'] = 0;

						$course_query_args = array(
							'post_type' => 'sfwd-courses',
							'post_status' => 'any',
							'fields'    => 'ids',
							'nopaging'  => true,
						);
						$course_query = new WP_Query( $course_query_args );
						if ( ( ! is_wp_error( $course_query ) ) && ( is_a( $course_query, 'WP_Query' ) ) ) {
							if ( property_exists( $course_query, 'found_posts' ) ) {
								$data['total_count'] = 	intval( $course_query->found_posts );
							}
						}
						if ( $data['total_count'] > 0 ) {
							$course_query_args['meta_query'] = array(
								array(
									'key'     => 'course_access_list',
									'compare' => 'NOT EXISTS',
								),
							);
							$course_query = new WP_Query( $course_query_args );
							if ( ( ! is_wp_error( $course_query ) ) && ( is_a( $course_query, 'WP_Query' ) ) ) {
								if ( property_exists( $course_query, 'posts' ) ) {
									$data['process_courses'] = 	$course_query->posts;

									$data['result_count'] 		= 	$data['total_count'] - intval( $course_query->post_count );
									//$data['result_count'] 		= 	intval( $course_query->post_count );
									
									$data['progress_percent'] 	= 	($data['result_count'] / $data['total_count']) * 100;
									$data['progress_label']		= 	sprintf( esc_html_x('%1$d of %2$s %3$s', 'placeholders: result count, total count, courses', 	
										'learndash'), $data['result_count'], $data['total_count'], LearnDash_Custom_Label::get_label( 'courses' ) );
								}
							}
						}
						$this->set_transient( $this->transient_key, $data );

					} else {
						$data = $this->get_transient( $this->transient_key );
						if ( ( isset( $data['process_courses'] ) ) && ( !empty( $data['process_courses'] ) ) ) {

							foreach ( $data['process_courses'] as $course_idx => $course_id ) {

								$course_complete = $this->convert_course_access_list( intval( $course_id ) );

								if ( $course_complete === true ) {
									unset( $data['process_courses'][$course_idx] );
									$data['result_count'] 		= 	$data['total_count'] - count( $data['process_courses'] );
									$data['progress_percent'] 	= 	($data['result_count'] / $data['total_count'] ) * 100;
									$data['progress_label']		= 	sprintf( esc_html_x('%1$d of %2$s %3$s', 'placeholders: result count, total count, courses', 'learndash'), $data['result_count'], $data['total_count'], LearnDash_Custom_Label::get_label( 'courses' ) );

									$this->set_transient( $this->transient_key, $data );
									//break;
								}

								if ( $this->out_of_timer() ) {
									break;
								}
							}
						}
					} 
				}
			} 

			// Remove process users from being returned to AJAX. 
			if ( isset( $data['process_courses'] ) )
				unset( $data['process_courses'] );

			// If we are at 100% then we update the internal data settings so other parts of LD know the upgrade has been run
			if ( ( isset( $data['progress_percent'] ) ) && ( $data['progress_percent'] == 100 ) ) {
				$this->set_last_run_info( $data );
				$data['last_run_info'] = $this->get_last_run_info();

				$this->remove_transient( $this->transient_key );
			}

			return $data;
		}

		function convert_course_access_list( $course_id = 0 ) {

			$course_access_list = learndash_get_setting( $course_id, 'course_access_list' );
			update_post_meta( $course_id, 'course_access_list', learndash_convert_course_access_list( $course_access_list ) );

			return true;
		}
	}
}
