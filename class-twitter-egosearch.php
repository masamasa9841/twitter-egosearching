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
	}

	/**
	 * Dashboad function.
	 */
	public function add_dashboard_egosearch_metabox() {
		$blog_url      = get_bloginfo( 'url' );
		$pattern       = '/^(https?):\/\/+(.+)(\.[^.]+$)/';
		$blog_url      = preg_replace( $pattern, '$2', $blog_url );
		$queryfeed_url = 'https://queryfeed.net/tw?q=url%3A';
		$url           = $queryfeed_url . $blog_url;
		$rss           = fetch_feed( $url );
		if ( ! is_wp_error( $rss ) ) {
			$maxitems        = $rss->get_item_quantity( 5 );
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
}
