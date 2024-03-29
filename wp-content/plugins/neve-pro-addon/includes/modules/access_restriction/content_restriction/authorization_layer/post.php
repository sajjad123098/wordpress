<?php
/**
 * Post
 *
 * @package  Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer
 */
namespace   Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Resource_Factory;
use Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer\Layer;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password as Password_Authorization_Type;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;
use WP_Post;

/**
 * Class Post
 * That class manages object level restriction.
 *
 * That's for if the restrict the queries which are not main.
 *
 * Like restrict a post, taxonomy etc.
 */
class Post implements Layer {
	use Context_Trait;
	const EXPIRE_DAY = 1;

	/**
	 * Initialization (register hooks)
	 *
	 * @return void
	 */
	public function init() {
		add_filter(
			'post_password_required',
			[ $this, 'maybe_restrict_post' ],
			10,
			2
		);

		/*
		 * Will filter the thumbnail HTML if the post is restricted.
		 */
		add_filter( 'post_thumbnail_html', [ $this, 'maybe_restrict_thumbnail' ], 10, 5 );

		add_filter( 'the_title', [ $this, 'maybe_restrict_title' ], 10, 2 );

		add_filter( 'woocommerce_product_get_image', [ $this, 'maybe_restrict_product_image' ], 10, 5 );

		add_filter( 'woocommerce_short_description', [ $this, 'maybe_restrict_product_short_description' ] );

		/**
		 * Change the password protected message.
		 * That message is used in somewhere such as latest posts core block.
		 */
		add_filter(
			'gettext',
			[ $this, 'rename_restricted_content_notification' ],
			10,
			2
		);

		add_action( 'login_form_arpass', [ $this, 'handle_password_login' ] );
		add_action( 'neve_ar_password_login_content', [ $this, 'render_password_login' ] );
		add_action( 'template_redirect', [ $this, 'on_template_redirect' ] );
	}

	/**
	 * Add filters required on template redirect.
	 *
	 * @return void
	 */
	public function on_template_redirect() {
		$this->add_title_filter_for_context();
	}

	/**
	 * Maybe restrict the short description.
	 *
	 * @param string $description The short description.
	 *
	 * @return string
	 */
	public function maybe_restrict_product_short_description( $description ) {
		global $product;
		if ( ! $product instanceof \WC_Product ) {
			return $description;
		}
		$query_object = get_post( $product->get_id(), OBJECT );
		if ( ! $query_object instanceof WP_Post ) {
			return $description;
		}
		if ( $this->maybe_restrict_post( false, $query_object ) ) {
			return __( 'This product is restricted and can only be viewed by authorized users.', 'neve' );
		}
		return $description;
	}

	/**
	 * Restrict product image if required.
	 *
	 * @param string      $image The image HTML.
	 * @param \WC_Product $product The Product object.
	 * @param string      $size The image size.
	 * @param array       $attr The attributes.
	 * @param boolean     $placeholder Flag to use the placeholder..
	 *
	 * @return string
	 */
	public function maybe_restrict_product_image( $image, $product, $size, $attr, $placeholder ) {
		$query_object = get_post( $product->get_id(), OBJECT );
		if ( ! $query_object instanceof WP_Post ) {
			return $image;
		}

		if ( $this->maybe_restrict_post( false, $query_object ) ) {
			return wc_placeholder_img( $size, $attr );
		}
		return $image;
	}

	/**
	 * Restrict the title if required.
	 *
	 * @param string $title The title.
	 * @param int    $id The post ID.
	 *
	 * @return mixed|null
	 */
	public function maybe_restrict_title( $title, $id ) {
		$query_object = get_post( $id, OBJECT );
		if ( ! $query_object instanceof WP_Post ) {
			return $title;
		}

		if ( $this->maybe_restrict_post( false, $query_object ) ) {
			return apply_filters( 'neve_restricted_title', $title );
		}
		return $title;
	}

	/**
	 * Restrict the post thumbnail if required.
	 *
	 * @param string $html The thumbnail HTML.
	 * @param int    $post_id The post ID.
	 * @param string $post_thumbnail The post thumbnail ID.
	 * @param string $size The image size.
	 * @param array  $attr The attributes.
	 *
	 * @return string
	 */
	public function maybe_restrict_thumbnail( $html, $post_id, $post_thumbnail, $size, $attr ) {
		$query_object = get_post( $post_id, OBJECT );
		if ( ! $query_object instanceof WP_Post ) {
			return $html;
		}

		if ( $this->maybe_restrict_post( false, $query_object ) ) {
			return '';
		}
		return $html;
	}

	/**
	 * Maybe restrict queried post.
	 *
	 * @param  bool         $is_protected Current restriction status.
	 * @param WP_Post|null $post The post object.
	 * @return bool Returns true if the post should be restricted.
	 */
	public function maybe_restrict_post( $is_protected, $post ) {
		if ( empty( $post ) ) {
			return $is_protected;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX === true || is_admin() ) {
			return $is_protected;
		}
		$resource = ( new Resource_Factory() )->get_resource( $post );

		$allowed = $resource->get_authorization_checker()->check();

		return ( ! $allowed ) ? true : $is_protected;
	}

	/**
	 * If "Neve Access Restriction Module" is enabled,
	 *  override WP's default protected content notification.
	 *
	 * Because, we leverage from WP's post restriction mechanism
	 * for different contents (post, product, etc.) not only posts.
	 *
	 * There is no way to override WP's default protected content notification,
	 * therefore we use gettext filter to override it.
	 *
	 * @param  string $translated Translated string.
	 * @param  string $original Original string.
	 * @return string
	 */
	public function rename_restricted_content_notification( $translated, $original ) {
		if ( $original === 'This content is password protected.' ) {
			return __( 'This content is restricted.', 'neve' );
		}

		return $translated;

	}

	/**
	 * Handle password form login.
	 *
	 * Make authentication check.
	 *
	 * @return void
	 */
	public function handle_password_login() {

		if ( ! isset( $_POST['nv_ar_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nv_ar_nonce'] ), 'nv_ar_pass_form' ) ) {
			return;
		}

		$referer = wp_get_referer();

		if ( ! array_key_exists( 'nv_ar_password', $_POST ) ) {
			wp_safe_redirect( $referer );
			exit;
		}

		$password = trim( wp_unslash( sanitize_text_field( $_POST['nv_ar_password'] ) ) );

		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher = new \PasswordHash( 8, true );

		$expire = time() + self::EXPIRE_DAY * DAY_IN_SECONDS;
		$secure = wp_parse_url( $referer, PHP_URL_SCHEME ) === 'https';

		$cookie_key = Password_Authorization_Type::COOKIE_KEY_PREFIX . COOKIEHASH;

		$cookie_domain = COOKIE_DOMAIN === false ? '' : COOKIE_DOMAIN;

		setcookie( $cookie_key, $hasher->HashPassword( $password ), $expire, COOKIEPATH, $cookie_domain, $secure ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.cookies_setcookie
		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Render password login form.
	 *
	 * @return void
	 */
	public static function render_password_login() {
		require_once NEVE_PRO_PATH . 'includes/modules/access_restriction/templates/password-login-form.php';
	}
}
