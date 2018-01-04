<?php
/**
 * Unko.
 *
 * @package twitter-egosearch
 * @author Masaya Okawa
 * @license GPL-2.0+
 */

/**
 * Twitter Egosearch class.
 */
class Twitter_Egosearch {
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_egosearch_metabox' ) );
		// timezone set of WordPress.
		$this_timezone = get_option( 'timezone_string' );
		date_default_timezone_set( $this_timezone ); // Ignore errors.
	}

	/**
	 * Dashboad function.
	 */
	public function add_dashboard_egosearch_metabox() {
		$url = $this->get_queryfeed_url();
		$rss = fetch_feed( $url );
		if ( ! is_wp_error( $rss ) ) {
			$input = get_option( 'eg_setting' );
			// get max tweet for option.
			if ( isset( $input['key6'] ) ) {
				$maxitems = $rss->get_item_quantity( $input['key6'] );
			} else {
				// default 5.
				$maxitems = $rss->get_item_quantity( 5 );
			}
			$this->rss_items = $rss->get_items( 0, $maxitems );
			$now             = date( 'Y.m.d' );
			$hoge            = false;
			foreach ( $this->rss_items as $item ) {
				// Get Tweet date.
				$date = $item->get_date( 'Y.m.d' );
				if ( $date === $now ) {
					$hoge = true;
				}
			}
			if ( true === $hoge ) {
				$title = "Today's ego search results";
				add_meta_box( 'dashboard_egosearch', $title, array( $this, 'dashboard_egosearch' ), get_current_screen(), 'side', 'high' );
			}
		}
	}

	/**
	 * Echo html function.
	 */
	public function dashboard_egosearch() {
		$now = date( 'Y.m.d' );
		foreach ( $this->rss_items as $item ) {
			$date = $item->get_date( 'Y.m.d' );
			if ( $date === $now ) {
				$link = $item->get_link();
				echo '<blockquote class="twitter-tweet" data-cards="hidden" lang="ja"><p>';
				echo '<a href=' . esc_html( $link ) . '></a>';
				echo '</blockquote>';
			}
		}
		// Load js.
		wp_enqueue_script( 'twitter_egosearch', '//platform.twitter.com/widgets.js' );
	}

	/**
	 * Get blog url.
	 */
	public function get_blog_url() {
		$blog_url = get_bloginfo( 'url' );
		$pattern  = '/^(https?):\/\/+(.+)(\.[^.]+$)/';
		$blog_url = preg_replace( $pattern, '$2', $blog_url );
		return 'url:' . $blog_url;
	}

	/**
	 * Get queryfeed_url.
	 * aaa "bbb" ccc -ddd #eee ]
	 */
	public function get_queryfeed_url() {
		$queryfeed_url = 'https://queryfeed.net/tw?q=';
		$input         = get_option( 'eg_setting' );
		// 次のキーワードをすべて含む.
		if ( isset( $input['key1'] ) && trim( $input['key1'] ) !== '' ) {
			$pieces = explode( ' ', $input['key1'] );
			$i      = 0;
			foreach ( $pieces as $piece ) {
				$i++;
				if ( 1 === $i ) { // 最初は＋をつけない.
					$queryfeed_url = $queryfeed_url . $piece;
				} else {
					$queryfeed_url = $queryfeed_url . '+' . $piece;
				}
			}
		} else {
			$queryfeed_url = $queryfeed_url . $this->get_blog_url();
		}
		// 次のキーワード全体を含む.
		if ( isset( $input['key2'] ) && trim( $input['key2'] ) !== '' ) {
			$pieces = explode( ' ', $input['key2'] );
			foreach ( $pieces as $piece ) {
				$queryfeed_url = $queryfeed_url . '+"' . $piece . '"';
			}
		}
		// 次のキーワードのいずれかを含む.
		if ( isset( $input['key3'] ) && trim( $input['key3'] ) !== '' ) {
			$pieces = explode( ' ', $input['key3'] );
			foreach ( $pieces as $piece ) {
				$queryfeed_url = $queryfeed_url . '+' . $piece;
			}
		}
		// 次のキーワードを含まない.
		if ( isset( $input['key4'] ) && trim( $input['key4'] ) !== '' ) {
			$pieces = explode( ' ', $input['key4'] );
			foreach ( $pieces as $piece ) {
				$queryfeed_url = $queryfeed_url . '+-' . $piece;
			}
		}
		// 次のハッシュタグを含む.
		if ( isset( $input['key5'] ) && trim( $input['key5'] ) !== '' ) {
			$pieces = explode( ' ', $input['key5'] );
			foreach ( $pieces as $piece ) {
				$queryfeed_url = $queryfeed_url . '+' . $piece;
			}
		}
		return $queryfeed_url;
	}

}
