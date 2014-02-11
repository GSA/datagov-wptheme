<?php

if (!class_exists('DatasetSuggestionDB')) {
    /**
     * Class DatasetSuggestionDB
     */
    class DatasetSuggestionDB extends CustomContactFormsDB
    {
        /**
         *
         */
        public function __construct()
        {
            global $wpdb;
            $prefix = $wpdb->prefix;

            if (!defined('SGD_SUGGESTIONS_TABLE')) {
                define('SGD_SUGGESTIONS_TABLE', $prefix . 'suggested_datasets_suggestions');
            }
            if (!defined('SGD_SUGGESTION_COMMENTS_TABLE')) {
                define('SGD_SUGGESTION_COMMENTS_TABLE', $prefix . 'suggested_datasets_comments');
            }
        }

        /**
         *
         */
        public function initDB()
        {
            $this->query("
                CREATE TABLE IF NOT EXISTS `" . SGD_SUGGESTIONS_TABLE . "` (
                      `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `name` varchar(250) NOT NULL,
                      `description` text NOT NULL,
                      `from_name` varchar(250) NOT NULL,
                      `from_email` varchar(250) NOT NULL,
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `status` tinyint(4) NOT NULL COMMENT 'new(0), open(1), closed(2), rejected(3), under_review(4)',
                      PRIMARY KEY (`id`),
                      KEY `status` (`status`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ");

            $this->query("
                CREATE TABLE IF NOT EXISTS `" . SGD_SUGGESTION_COMMENTS_TABLE . "` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `sgd_id` bigint(20) unsigned NOT NULL,
                    `user_id` bigint(20) unsigned NOT NULL,
                    `comment` text NOT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `sgd_id` (`sgd_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                ");
        }

        /**
         * @param $sgd_ids
         * @param $status
         */
        public function updateSuggestionStatuses($sgd_ids, $status)
        {
            $ids = array();
            foreach ($sgd_ids as $id) {
                $ids[] = intval($id);
            }
            $ids = join(',', $ids);

            $this->query("
                UPDATE " . SGD_SUGGESTIONS_TABLE . "
                    SET `status` = $status
                WHERE `id` IN ($ids)
                LIMIT 5
            ");
        }

        /**
         * @param $id
         * @return mixed
         */
        public function getCommentsBySuggestionId($id)
        {
            /** @var wpdb $wpdb */
            global $wpdb;
            $comments = $wpdb->get_results("SELECT cm.*, u.user_login
                FROM " . SGD_SUGGESTION_COMMENTS_TABLE . " cm
                    LEFT JOIN " . $wpdb->users . " u ON cm.user_id = u.ID
            WHERE `sgd_id` = $id ORDER BY created_at ASC");

            return $comments;
        }

        /**
         * @param $id
         * @param $comment
         * @return mixed
         */
        public function addCommentForSuggestion($id, $comment)
        {
            /** @var wpdb $wpdb */
            global $wpdb;

            $admin = wp_get_current_user();

            return $wpdb->insert(
                SGD_SUGGESTION_COMMENTS_TABLE,
                array(
                    'sgd_id'  => $id,
                    'comment' => $comment,
                    'user_id' => $admin->ID,
                ),
                array(
                    '%d',
                    '%s',
                    '%d'
                )
            );
        }
    }
}