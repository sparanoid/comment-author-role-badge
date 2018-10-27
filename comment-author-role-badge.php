<?php
/*
 * Plugin Name: Comment Author Role Badge
 * Plugin URI: https://sparanoid.com/work/comment-author-role-badge/
 * Description: Displays a user badge next to the comment author name based on author's role
 * Author: Tunghsiao Liu
 * Version: 1.0.1
 * Author URI: https://sparanoid.com
 * License: GPL2+
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! class_exists( 'Comment_Author_Role_Badge' ) ) :

  class Comment_Author_Role_Badge {

    protected $user_role = '';

    public function __construct() {
      add_action( 'init', array( $this, 'load_admin_textdomain_in_frontend' ) );
      add_filter( 'wp_enqueue_scripts', array( $this, 'add_stylesheets' ) );
      add_filter( 'get_comment_author', array( $this, 'get_comment_author_role' ), 10, 3 );
      add_filter( 'get_comment_author_link', array( $this, 'comment_author_role' ) );
    }

    /**
     * Register text domain
     *
     * @since 1.0.0
     *
     * @link  https://core.trac.wordpress.org/ticket/37539
     */
    public function load_admin_textdomain_in_frontend() {
      if ( ! is_admin() ) {
        load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
      }
    }

    /**
     * Modify author link with role badges
     *
     * @since  1.0.0
     */
    public function get_comment_author_role( $author, $comment_id, $comment ) {
      global $wp_roles;
      if ( $wp_roles ) {
        $reply_user_id = $comment->user_id;
        if ( $reply_user_id && $reply_user = new WP_User( $reply_user_id ) ) {
          if ( isset( $reply_user->roles[0] ) ) {
            $user_role = translate_user_role( $wp_roles->roles[ $reply_user->roles[0] ]['name'] );
            $this->user_role = '<div class="comment-author-role-badge comment-author-role-badge--'. $reply_user->roles[0] .'">'. $user_role .'</div>';
          }
        } else {
          $this->user_role = '';
        }
      }
      return $author;
    }

    /**
     * Update author link ouput
     *
     * @since  1.0.0
     */
    public function comment_author_role( $author ) {
      return $author .= $this->user_role;
    }

    /**
     * Add stylesheets
     *
     * @since  1.0.0
     */
    public function add_stylesheets() {
      wp_enqueue_style( 'comment-author-role-badge', plugins_url( 'comment-author-role-badge.css', __FILE__ ) );
    }
  }

  new Comment_Author_Role_Badge;

endif;
