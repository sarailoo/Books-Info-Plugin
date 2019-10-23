<?php

namespace Actions;

use Helper\Core;

class Post
{
    /**
     * Add Emoji To Contents
     */
    public static function addEmojiToContents()
    {
        add_filter('the_content', function ($title) {
            return $title . PHP_EOL . Core::getRandomEmoji();
        }, 10, 2);
    }
    public static function create_table(){
        $sql ="CREATE TABLE IF NOT EXISTS books_info (
        `id` INT NOT NULL AUTO_INCREMENT , 
        `post_id` INT NOT NULL , 
        `isbn` INT NOT NULL , PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
        require_once ABSPATH.'/wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    public static function drop_table(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS books_info");
    }

    public static function hook_into_wordpress(){
        add_action( 'init', ['Actions\Post', 'create_taxonomy'] );
        add_action( 'init', ['Actions\Post', 'create_posttype'] ); 
        add_action( 'add_meta_boxes', array( 'Actions\Post', 'create_metabox' ) ); 
        add_action( 'save_post_book', array( 'Actions\Post', 'save_metabox' ) ); 
    }
    public static function create_posttype(){
        register_post_type('book', array(
            'supports' => array('title', 'editor', 'thumbnail'),
            'public' => true,
            // 'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'labels' => array(
                'name' => __( 'Book','books-info' ),
                'add_new_item' => __( 'Add New Book','books-info' ),
                'edit_item' => __( 'Edit Book','books-info' ),
                'all_items' =>  __( 'All Books','books-info' ),
                'singular_name' => __( 'Book','books-info' ),
            ),
            'menu_icon' => 'dashicons-dashboard',
        ));       
    }

    public static function create_taxonomy(){
        $labels = array(
            'name'                       => _x( 'Publisher', 'Taxonomy General Name', 'books-info' ),
            'singular_name'              => _x( 'Publisher', 'Taxonomy Singular Name', 'books-info' ),
            'menu_name'                  => __( 'Publisher', 'books-info' ),
            'all_items'                  => __( 'All Items', 'books-info' ),
            'parent_item'                => __( 'Parent Item', 'books-info' ),
            'parent_item_colon'          => __( 'Parent Item:', 'books-info' ),
            'new_item_name'              => __( 'New Item Name', 'books-info' ),
            'add_new_item'               => __( 'Add New Item', 'books-info' ),
            'edit_item'                  => __( 'Edit Item', 'books-info' ),
            'update_item'                => __( 'Update Item', 'books-info' ),
            'view_item'                  => __( 'View Item', 'books-info' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'books-info' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'books-info' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'books-info' ),
            'popular_items'              => __( 'Popular Items', 'books-info' ),
            'search_items'               => __( 'Search Items', 'books-info' ),
            'not_found'                  => __( 'Not Found', 'books-info' ),
            'no_terms'                   => __( 'No items', 'books-info' ),
            'items_list'                 => __( 'Items list', 'books-info' ),
            'items_list_navigation'      => __( 'Items list navigation', 'books-info' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'Publisher', array( 'book' ), $args );

        $labels = array(
            'name'                       => _x( 'Author', 'Taxonomy General Name', 'books-info' ),
            'singular_name'              => _x( 'Author', 'Taxonomy Singular Name', 'books-info' ),
            'name'                       => _x( 'Author', 'Taxonomy General Name', 'books-info' ),
            'menu_name'                  => __( 'Author', 'books-info' ),
            'all_items'                  => __( 'All Items', 'books-info' ),
            'parent_item'                => __( 'Parent Item', 'books-info' ),
            'parent_item_colon'          => __( 'Parent Item:', 'books-info' ),
            'new_item_name'              => __( 'New Item Name', 'books-info' ),
            'add_new_item'               => __( 'Add New Item', 'books-info' ),
            'edit_item'                  => __( 'Edit Item', 'books-info' ),
            'update_item'                => __( 'Update Item', 'books-info' ),
            'view_item'                  => __( 'View Item', 'books-info' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'books-info' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'books-info' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'books-info' ),
            'popular_items'              => __( 'Popular Items', 'books-info' ),
            'search_items'               => __( 'Search Items', 'books-info' ),
            'not_found'                  => __( 'Not Found', 'books-info' ),
            'no_terms'                   => __( 'No items', 'books-info' ),
            'items_list'                 => __( 'Items list', 'books-info' ),
            'items_list_navigation'      => __( 'Items list navigation', 'books-info' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'Author', array( 'book' ), $args );
    }
    public static function create_metabox(){
        add_meta_box(
			'isbn',
			__('ISBN','books-info'),
			array('Helper\Core' , 'metabox_form' ),
			'book'
		);
    }
    
    public static function save_metabox($post_id){
        global $wpdb;
        $check_isbn = $wpdb->get_results( $wpdb->prepare( "SELECT isbn FROM books_info WHERE post_id = %d", $post_id) );
        if (array_key_exists('isbn_input', $_POST) and empty($check_isbn)){
            $table = 'books_info';
            $data = array('post_id' => $post_id , 'isbn' => $_POST['isbn_input']);
            $format = array('%d','%d');
            $wpdb->insert($table,$data,$format);
        }
        elseif(array_key_exists('isbn_input', $_POST) and !empty($check_isbn)){
            $table = 'books_info';
            $data = array('isbn' => $_POST['isbn_input']);
            $where = array( 'post_id' => $post_id );
            $wpdb->update($table,$data,$where);
        }
    }
}