<?php

if (!class_exists('DatasetSuggestion')) {
    class DatasetSuggestion extends DatasetSuggestionDB
    {
        public $id;
        public $name;
        public $description;
        public $fromName;
        public $fromEmail;
        public $datetime;

        public $status = self::STATUS_NEW;

        const STATUS_NEW          = 0;
        const STATUS_OPEN         = 1;
        const STATUS_CLOSED       = 2;
        const STATUS_REJECTED     = 3;
        const STATUS_UNDER_REVIEW = 4;

        /**
         * @return array
         */
        public static function getAvailableStatuses()
        {
//            New, Open, Closed, Rejected, Under Review
//            https://tracker.reisys.com/browse/DG-1648
            return array(
                self::STATUS_NEW          => 'new',
                self::STATUS_OPEN         => 'open',
                self::STATUS_CLOSED       => 'closed',
                self::STATUS_REJECTED     => 'rejected',
                self::STATUS_UNDER_REVIEW => 'under_review',
            );
        }

        /**
         * @return mixed
         */
        public function add()
        {
            global $wpdb;

            $data = array(
                'name'        => $this->name, // %s
                'description' => $this->description, // %s
                'from_name'   => $this->fromName, // %s
                'from_email'  => $this->fromEmail, // %s
                'created_at'  => date('Y-m-d H:i:s', $this->datetime), // %s
                'status'      => $this->status, // %d
            );

            $format = array('%s', '%s', '%s', '%s', '%s', '%d');

            $wpdb->insert(SGD_SUGGESTIONS_TABLE, $data, $format);

            return $wpdb->insert_id;
        }
    }
}