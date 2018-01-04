<?php
/**
 * View for Integration Setting Meta Box.
 *
 * @package compass-sns
 * @author Masaya Okawa
 * @license GPL-2.0+
 */

/**
 * load Main program.
 */
require __DIR__ . '/class-twitter-egosearch.php';

/**
 * Setting page.
 */
class Egosearch_Setting extends Twitter_Egosearch {
	/**
	 * Holds the values to be used in the fields callbacks.
	 *
	 * @var object Option.
	 */
	private $options;
	/**
	 * Start up.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}
	/**
	 * Add options page.
	 */
	public function add_plugin_page() {
		add_options_page( 'Twitter Egosearching', 'Twitter Egosearching', 'manage_options', 'eg_setting', array( $this, 'create_admin_page' ) );
	}
	/**
	 * Register and add settings.
	 */
	public function page_init() {
		register_setting( 'eg_setting', 'eg_setting', array( $this, 'sanitize' ) );
		add_settings_section( 'cp_eg_setting', 'キーワード（複数の場合は半角スペースで区切る）', '', 'eg_setting' );
		add_settings_field( 'key1', '次のキーワードをすべて含む', array( $this, 'key1' ), 'eg_setting', 'cp_eg_setting' );
		add_settings_field( 'key2', '次のキーワード全体を含む', array( $this, 'key2' ), 'eg_setting', 'cp_eg_setting' );
		add_settings_field( 'key3', '次のキーワードのいずれかを含む', array( $this, 'key3' ), 'eg_setting', 'cp_eg_setting' );
		add_settings_field( 'key4', '次のキーワードを含まない', array( $this, 'key4' ), 'eg_setting', 'cp_eg_setting' );
		add_settings_field( 'key5', '次のハッシュタグを含む', array( $this, 'key5' ), 'eg_setting', 'cp_eg_setting' );
		add_settings_field( 'key6', '表示するツイート制限', array( $this, 'selectbox_callback' ), 'eg_setting', 'cp_eg_setting' );
	}
	/**
	 * Options page callback.
	 */
	public function create_admin_page() {
		echo '<div class="wrap">';
			echo '<h2>高度な検索</h2>';
			echo '<form method="post" action="options.php">';
				settings_fields( 'eg_setting' );
				$this->options = get_option( 'eg_setting' );
				do_settings_sections( 'eg_setting' );
				submit_button();
				echo '<h3>取得するRSS(エラーが出る場合は設定を見直す)</h3>';
				printf(
					'<p style="font-size: 20px;"><strong><a href="%s" target="blank">%s</a></strong></p>',
					esc_html( $this->get_queryfeed_url() ),
					esc_html( $this->get_queryfeed_url() )
				);
			echo '</form>';
		echo '</div>';
	}
	/**
	 * Get the settings option array and print one of its values.
	 */
	public function key1() {
		$url = $this->get_blog_url();
		printf(
			'<input type="text" size="100" style="height: 40px;" id="key1" name="eg_setting[key1]" placeholder="%s" value="%s" />',
			$url, isset( $this->options['key1'] ) ? esc_attr( $this->options['key1'] ) : ''
		);
	}
	/**
	 * Get the settings option array and print one of its values.
	 */
	public function key2() {
		printf(
			'<input type="text" size="100" style="height: 40px;" id="key2" name="eg_setting[key2]" value="%s" />',
			isset( $this->options['key2'] ) ? esc_attr( $this->options['key2'] ) : ''
		);
	}
	/**
	 * Get the settings option array and print one of its values.
	 */
	public function key3() {
		printf(
			'<input type="text" size="100" style="height: 40px;" id="key3" name="eg_setting[key3]" value="%s" />',
			isset( $this->options['key3'] ) ? esc_attr( $this->options['key3'] ) : ''
		);
	}
	/**
	 * Get the settings option array and print one of its values.
	 */
	public function key4() {
		printf(
			'<input type="text" size="100" style="height: 40px;" id="key4" name="eg_setting[key4]" value="%s" />',
			isset( $this->options['key4'] ) ? esc_attr( $this->options['key4'] ) : ''
		);
	}
	/**
	 * Get the settings option array and print one of its values.
	 */
	public function key5() {
		printf(
			'<input type="text" size="100" style="height: 40px;" id="key5" name="eg_setting[key5]" value="%s" />',
			isset( $this->options['key5'] ) ? esc_attr( $this->options['key5'] ) : ''
		);
		echo '<p>ハッシュタグはつける</p>';
	}
	/**
	 * Get the settings option and print its values (Select Box).
	 */
	public function selectbox_callback() {
		echo '<select name="eg_setting[key6]" id="key6">';
			printf( '<option value="%s" %s >%s</option>', 1, selected( $this->options['key6'], 1 ), 1 );
			printf( '<option value="%s" %s >%s</option>', 2, selected( $this->options['key6'], 2 ), 2 );
			printf( '<option value="%s" %s >%s</option>', 3, selected( $this->options['key6'], 3 ), 3 );
			printf( '<option value="%s" %s >%s</option>', 4, selected( $this->options['key6'], 4 ), 4 );
			printf( '<option value="%s" %s >%s</option>', 5, selected( $this->options['key6'], 5 ), 5 );
		echo '</select>';
	}
	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$new_input = array();
		$new_input = $this->santize_text( 'key1', $input, $new_input );
		$new_input = $this->santize_text( 'key2', $input, $new_input );
		$new_input = $this->santize_text( 'key3', $input, $new_input );
		$new_input = $this->santize_text( 'key4', $input, $new_input );
		$new_input = $this->santize_text( 'key5', $input, $new_input );
		$new_input = $this->santize_text( 'key6', $input, $new_input );
		return $new_input;
	}
	/**
	 * Sanitize each setting field as needed for text.
	 *
	 * @param string $key Key.
	 * @param array  $input Contains all settings fields as array keys.
	 * @param array  $new_input Contains all settings fields as array keys.
	 */
	public function santize_text( $key, $input, $new_input ) {
		if ( isset( $input[ $key ] ) && trim( $input[ $key ] ) !== '' ) {
			$new_input[ $key ] = sanitize_text_field( $input[ $key ] );
		}
		return $new_input;
	}
}
