<?php

add_post_type_support('page', 'grid');

class Paged_Grid_Metabox {

	private $metabox_id;

	public function __construct() {
		$this->metabox_id = 'grid';
	}

	public function initialize() {
		if(isset ($_REQUEST['edit_grid_slot']) ) {
			$this->handle_edit_grid_frame();
		}

		add_action('add_meta_boxes', array($this, '_add_metabox'));

		//add the scripts and styles for the metabox
		add_action('admin_print_styles-post.php', array($this, '_enqueue_scripts' ) );
		add_action('admin_print_styles-post-new.php', array($this, '_enqueue_scripts' ) );

		add_action('save_post', array($this, '_update_meta'));
	}

	public function _add_metabox($post_type) {
		if(post_type_supports($post_type, $this->metabox_id)) {
			add_meta_box($this->metabox_id, 'Gallery Assets', array($this, '_metabox'), $post_type, 'normal');
		}
	}

	public function _metabox($post) {
		?>
		<div class="grid-page-frame">
			<?php for($i = 1; $i <= 3; $i++) : ?>
				<div class="grid-page" id="grid-page-<?php echo $i; ?>">
					<?php for($x = 1; $x <= 12; $x++) : ?>
						<div class="grid-slot" id="<?php printf("slot_%d_%d", $i, $x); ?>">
							<?php
							$query_vars = array(
								'TB_iframe' => true,
								'width' => 400,
								'height' => 600,
								'edit_grid_slot' => true,
								'post_id' => $post->ID,
								'grid_page' => $i,
								'grid_slot' => $x,
							);
							$set_url = add_query_arg($query_vars, admin_url());
							echo $set_url;
							?>
							<a href="<?php echo admin_url() . $set_url ?>" class="thickbox">Set Widget</a>
						</div>
					<?php endfor; ?>
				</div>
			<?php endfor; ?>
		</div>
		<?php
	}

	public function _enqueue_scripts() {
	 wp_enqueue_style('grid-metabox', Paged_Grid_Handler::Plugins_URL('css/metabox.css', __FILE__));
	 wp_enqueue_script('grid-metabox', Paged_Grid_Handler::Plugins_URL('js/metabox.js', __FILE__), array('jquery'));
	}

	public function _update_meta($post_id) {
		if(wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
			return $post_id;
		}

		if(!post_type_supports( get_post_type($post_id), $this->metabox_id)) {
			return $post_id;
		}

		if(isset($_REQUEST['$this->metabox_id_nonce']) && wp_verify_nonce($_REQUEST['$this->metabox_id_nonce'], 'update_$this->metabox_id')) {
			
		}
		return $post_id;
	}

	public function handle_edit_grid_frame() {
		die("HHHHHHHHEEEEEEEEEEEEEEERRRRRRRRE");
	}
}
add_action('init', array(new Paged_Grid_Metabox(), 'initialize'));

class Paged_Grid_Handler {

	public static function Plugins_URL($relative_path, $plugin_path) {
		$template_dir = get_template_directory();

		foreach ( array('template_dir', 'plugin_path') as $var ) {
			$$var = str_replace('\\' ,'/', $$var); // sanitize for Win32 installs
			$$var = preg_replace('|/+|', '/', $$var);
		}
		if(0 === strpos($plugin_path, $template_dir)) {
			$url = get_template_directory_uri();
			$folder = str_replace($template_dir, '', dirname($plugin_path));
			if ( '.' != $folder ) {
				$url .= '/' . ltrim($folder, '/');
			}
			if ( !empty($relative_path) && is_string($relative_path) && strpos($relative_path, '..') === false ) {
				$url .= '/' . ltrim($relative_path, '/');
			}
			return $url;
		} else {
			return plugins_url($relative_path, $plugin_path);
		}
	}

}

class Paged_Grid_Set {

	const MAX_NUM_PAGES = 9;
	private $page_data;

	public function __construct($page_data) {
		
	}

	public function add_page() {
		if($this->num_pages() >= self::MAX_NUM_PAGES) {
			return false;
		}
		return true;
	}

	public function remove_page() {
		if($this->num_pages() <= 1) {
			return false;
		}
		return true;
	}

	public function num_pages() {

	}

}

class Grid_Page {

}


//grid page set -> grid page -> grid slot
