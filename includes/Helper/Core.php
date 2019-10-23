<?php

namespace Helper;

class Core
{
    /**
     * @return mixed
     */
    public static function metabox_form($post_id){
        global $wpdb;
        $value = $wpdb->get_var( $wpdb->prepare( "SELECT isbn from books_info where post_id = %d", $post_id->ID ) );
        ?>
        <label for="isbn_input">Add ISBN: </label>
        <input type="text" id="isbn_input" name="isbn_input" class="postbox" value="<?php echo $value; ?>">
        <?php
    }

}
