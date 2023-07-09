<?php
/**
 * Plugin Name: Custom Posts Pages
 * Description: カスタム投稿のページ数を管理画面から設定できるようにするプラグインです。
 * Version: 1.0
 * Author: Yuki
 */

// プラグインのメイン関数を定義
function change_posts_per_page($query) {
    if ( is_admin() || ! $query->is_main_query() )
        return;

    $post_types = get_option('custom_posts_per_page');
    if ( !empty($post_types) ) {
        foreach ( $post_types as $post_type => $posts_per_page ) {
            if ( $query->is_post_type_archive( $post_type ) ) {
                $query->set( 'posts_per_page', $posts_per_page );
                break;
            }
        }
    }
}
add_action( 'pre_get_posts', 'change_posts_per_page' );

// カスタム投稿タイプと表示件数の設定を保存するための関数を定義
function custom_posts_per_page_settings() {
    add_settings_section(
        'custom_posts_per_page_section',
        'Custom Posts Per Page',
        'custom_posts_per_page_section_callback',
        'reading'
    );

    add_settings_field(
        'custom_posts_per_page',
        'Custom Post Types and Posts Per Page',
        'custom_posts_per_page_field_callback',
        'reading',
        'custom_posts_per_page_section'
    );

    register_setting( 'reading', 'custom_posts_per_page' );
}
add_action( 'admin_init', 'custom_posts_per_page_settings' );

// 設定ページのセクションのコールバック関数
function custom_posts_per_page_section_callback() {
    echo 'Set custom posts per page for different post types.';
}

// 設定ページのフィールドのコールバック関数
function custom_posts_per_page_field_callback() {
    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    $custom_posts_per_page = get_option( 'custom_posts_per_page' );

    foreach ( $post_types as $post_type ) {
        if ( $post_type->name == 'attachment' || $post_type->name == 'page' )
            continue;

        $posts_per_page = isset( $custom_posts_per_page[$post_type->name] ) ? $custom_posts_per_page[$post_type->name] : '';
        echo '<label for="custom_posts_per_page[' . $post_type->name . ']">' . $post_type->labels->name . '</label>';
        echo '<input type="number" id="custom_posts_per_page[' . $post_type->name . ']" name="custom_posts_per_page[' . $post_type->name . ']" value="' . $posts_per_page . '" min="1">';
        echo '<br>';
    }
}

// 管理画面メニューに設定ページを追加するための関数を定義
function custom_posts_per_page_menu() {
    add_options_page(
        'Custom Posts Per Page',
        'Custom Posts Per Page',
        'manage_options',
        'custom-posts-per-page',
        'custom_posts_per_page_options_page'
    );
}
add_action( 'admin_menu', 'custom_posts_per_page_menu' );

// 設定ページのコールバック関数
function custom_posts_per_page_options_page() {
    ?>
    <div class="wrap">
        <h1>Custom Posts Per Page</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'reading' );
            do_settings_sections( 'reading' );
            submit_button();
            ?>
        </form>
    </div>
<?php
    }
?>