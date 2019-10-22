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
}
