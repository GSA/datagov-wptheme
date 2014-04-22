<?php
/**
 *
 * DG-1652
 *
 * Create New Metrics to track:
 * Number of harvest sources per agency
 * Last Run timestamp for all harvest sources broken down by agency
 * Monthly breakdown of how many harvest sources ran - how many passed, how many failed.
 *
 * Depending upon how much/how little the data is, we can provide more directions on which visualizations to use, and where to display this information(menu labels etc).
 */
if (!class_exists('CKAN_Harvest_Stats')) {
    /**
     * Class CKAN_Harvest_Stats
     */
    class CKAN_Harvest_Stats
    {
        /**
         *
         */
        const CKAN_HARVESTS_API_URL = 'https://catalog.data.gov/api/3/action/package_search?q=type:harvest&rows=1000';

        /**
         *
         */
        const FED_JSON_URL = 'https://idm.data.gov/fed_agency.json';

        /**
         *
         */
        const CKAN_HARVEST_RESULTS_TABLE = 'wp_ckan_harvest_results';

        /**
         *
         */
        const CKAN_HARVEST_ORGANIZATIONS_TABLE = 'wp_ckan_harvest_organizations';

        /**
         *
         */
        const CKAN_HARVEST_META_TABLE = 'wp_ckan_harvest_meta';

        /**
         * Get ids of federal agencies only
         * @return array|bool
         */
        private function getFedJsonAgencies()
        {
            $return_agencies = array();

            try {
                $fed_json = file_get_contents(self::FED_JSON_URL);
                if (false === $fed_json) {
                    throw new Exception('could not access page');
                }
//                decode result as array
                $json_result = json_decode($fed_json, true);
                if (!isset($json_result['taxonomies'])) {
                    throw new Exception('taxonomies not found');
                }
                $results = $json_result['taxonomies'];

                foreach ($results as $taxonomy) {
                    if (!isset($taxonomy['taxonomy'])) {
                        continue;
                    }
                    $return_agencies[] = $taxonomy['taxonomy']['unique id'];
                }
            } catch (Exception $ex) {
                return false;
            }

            return $return_agencies;
        }

        /**
         * Get latest harvest statistics from CKAN
         */
        public function updateDB()
        {
            try {
                $json = file_get_contents(self::CKAN_HARVESTS_API_URL);
                if (false === $json) {
                    throw new Exception('could not access page');
                }
//                decode result as array
                $json_result = json_decode($json, true);
                if (true != $json_result['success']) {
                    throw new Exception('json returned [success]=false');
                }
                $results = $json_result['result']['results'];

                $organizations = $this->getOrganizationsAsArray();
                $fed_agencies = $this->getFedJsonAgencies();

                foreach ($results as $harvest_result) {

                    $organization = $harvest_result['organization'];

                    if (!in_array($organization['name'], $fed_agencies)) {
                        continue;
                    }

                    if (!isset($organizations[$organization['name']])) {
                        $organizations[$organization['name']] = $organization['title'];
                        if (!$this->saveOrganization($organization)) {
                            throw new Exception(
                                'Could not add organization ' . $organization['name'] . ' to ' . self::CKAN_HARVEST_ORGANIZATIONS_TABLE
                            );
                        }
                    }

                    $this->saveHarvestResult($harvest_result);
                }

            } catch (Exception $ex) {
                return false;
            }

            echo 'done';

            return true;
        }


        /**
         * Get organization list as an array with keys
         * id,name,title
         *
         * @return mixed
         */
        public function getOrganizationsForContent()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $organizations = $wpdb->get_results(
                "
                    SELECT *
                    FROM " . self::CKAN_HARVEST_ORGANIZATIONS_TABLE . "
                    WHERE 1
                    ORDER BY title
	            "
            );

            $results_array   = array();
            $harvest_results = $this->getHarvestResutls();
            foreach ($organizations as $organization) {

                if (isset($harvest_results[$organization->name])) {
                    $organization->harvest_results = $harvest_results[$organization->name];
                } else {
                    $organization->harvest_results = array();
                }

                $results_array[] = $organization;
            }

            return $results_array;
        }


        /**
         * @return mixed
         */
        public function getContent()
        {
            return $this->getOrganizationsForContent();
        }

        /**
         * @param bool $asArray
         * @return array
         */
        public function getHarvestResutls($asArray = true)
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;
            $results = $wpdb->get_results(
                "
                    SELECT *
                    FROM " . self::CKAN_HARVEST_RESULTS_TABLE . "
                    WHERE 1
                    ORDER BY `organization_name`
                "
            );

            if ($asArray) {
                $results_array = array();
                $meta          = $this->getHarvestMetaDataAsArray();
                foreach ($results as $harvest_result) {
                    if (isset($meta[$harvest_result->harvest_id])) {
                        $harvest_result->metas = $meta[$harvest_result->harvest_id];
                    } else {
                        $harvest_result->metas = array();
                    }
                    $results_array[$harvest_result->organization_name][] = $harvest_result;
                }

                return $results_array;
            }

            return $results;
        }

        /**
         * @return array
         */
        private function getHarvestMetaDataAsArray()
        {
            global $wpdb;
            $metas  = $wpdb->get_results(
                "
                    SELECT *
                    FROM " . self::CKAN_HARVEST_META_TABLE . "
                    WHERE 1
                    ORDER BY `id` DESC
                    LIMIT 1000
                "
            );
            $return = array();
            foreach ($metas as $meta) {
                $return[$meta->harvest_id][] = $meta;
            }

            return $return;
        }

        /**
         * Get organizations from db as array 'name'=>'title'
         * @return array
         */
        private function getOrganizationsAsArray()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $organizations = $wpdb->get_results(
                "
                    SELECT name, title
                    FROM " . self::CKAN_HARVEST_ORGANIZATIONS_TABLE . "
                    WHERE 1
                    ORDER BY name
	            "
            );

            $return = array();
            foreach ($organizations as $organization) {
                $return[$organization->name] = $organization->title;
            }

            return $return;
        }

        /**
         * @param mixed $organization
         * @return false|int
         */
        private function saveOrganization($organization)
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            return $wpdb->insert(
                self::CKAN_HARVEST_ORGANIZATIONS_TABLE,
                array(
                    'name'  => $organization['name'],
                    'title' => $organization['title'],
                ),
                array(
                    '%s',
                    '%s',
                )
            );
        }

        /**
         * @param $jsonHarvestInfo
         * @return bool
         */
        private function saveHarvestResult($jsonHarvestInfo)
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            if (!isset($jsonHarvestInfo['extras'])) {
                return false;
            }

            $extras     = $jsonHarvestInfo['extras'];
            $isDataJson = false;
            foreach ($extras as $extra) {
                if ('source_type' == $extra['key'] && 'datajson' == $extra['value']) {
                    $isDataJson = true;
                }
            }
            if (!$isDataJson) {
                return false;
            }

            $localLastJob = array(
                'status'          => 'Unknown',
                'gather_started'  => 'Unknown',
                'gather_finished' => 'Unknown',
            );

            $localHarvestStatus = array(
                'job_count'      => 0,
                'total_datasets' => 0,
            );

            $lastJobStats = array(
                'added'   => 0,
                'updated' => 0,
                'errored' => 0,
                'deleted' => 0,
            );

            if (isset($jsonHarvestInfo['status'])) {
                $jsonHarvestStatus                    = $jsonHarvestInfo['status'];
                $localHarvestStatus['job_count']      = isset($jsonHarvestStatus['job_count']) ? $jsonHarvestStatus['job_count'] : 0;
                $localHarvestStatus['total_datasets'] = isset($jsonHarvestStatus['total_datasets']) ? $jsonHarvestStatus['total_datasets'] : 0;

                if (isset($jsonHarvestStatus['last_job'])) {
                    $jsonLastJob            = $jsonHarvestStatus['last_job'];
                    $localLastJob['status'] = isset($jsonLastJob['status']) ? $jsonLastJob['status'] : 'Unknown';

                    if (isset($jsonLastJob['stats'])) {
                        $lastJobStats = array_merge($lastJobStats, $jsonLastJob['stats']);
                    }

                    $localLastJob['gather_started'] = isset($jsonLastJob['gather_started']) ? date(
                        'Y-m-d H:i:s',
                        strtotime($jsonLastJob['gather_started'])
                    ) : 'Unknown';

                    $localLastJob['gather_finished'] = isset($jsonLastJob['gather_finished']) ? date(
                        'Y-m-d H:i:s',
                        strtotime($jsonLastJob['gather_finished'])
                    ) : 'Unknown';
                }
            }

            $added = $wpdb->replace(
                self::CKAN_HARVEST_RESULTS_TABLE,
                array(
                    'harvest_id'        => $jsonHarvestInfo['id'],
                    'meta_created_at'   => $jsonHarvestInfo['metadata_created'],
                    'meta_updated_at'   => $jsonHarvestInfo['metadata_created'],
                    'status'            => $localLastJob['status'],
                    'gather_started'    => $localLastJob['gather_started'],
                    'gather_finished'   => $localLastJob['gather_finished'],
                    'organization_name' => $jsonHarvestInfo['organization']['name'],
                    'title'             => $jsonHarvestInfo['title'],
                    'job_count'         => $localHarvestStatus['job_count'],
                    'total_datasets'    => $localHarvestStatus['total_datasets'],
                    'added'             => $lastJobStats['added'],
                    'updated'           => $lastJobStats['updated'],
                    'errored'           => $lastJobStats['errored'],
                    'deleted'           => $lastJobStats['deleted'],
                    'url' => $jsonHarvestInfo['url'],
                ),
                array(
                    '%s', //  1fc919e5-e870-4d57-91b8-78e14081ce52
                    '%s', //  2014-03-31 16:11:47
                    '%s', //  2014-04-10 16:47:57
                    '%s', //  Finished
                    '%s', //  2014-04-17 14:00:23
                    '%s', //  2014-04-17 14:00:35
                    '%s', //  usgs-gov
                    '%s', //  USGS High Resolution Orthoimagery Collection - Current
                    '%d', //  3
                    '%d', //  834
                    '%d', //  0
                    '%d', //  1
                    '%d', //  2
                    '%d', //  3
                    '%s', //  http://www.treasury.gov/data.json
                )
            );

            /**
             * Filling meta data
             */
            if ($added) {
                foreach ($extras as $extra) {
                    $wpdb->replace(
                        self::CKAN_HARVEST_META_TABLE,
                        array(
                            'harvest_id' => $jsonHarvestInfo['id'],
                            'key'        => $extra['key'],
                            'value'      => $extra['value'],
                        ),
                        array(
                            '%s', //  1fc919e5-e870-4d57-91b8-78e14081ce52
                            '%s', //  frequency
                            '%s', //  WEEKLY
                        )
                    );
                }
            } else {
                return false;
            }

            return true;
        }

        /**
         *
         */
        public function truncateDB()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $wpdb->query("TRUNCATE " . self::CKAN_HARVEST_RESULTS_TABLE);
            $wpdb->query("TRUNCATE " . self::CKAN_HARVEST_ORGANIZATIONS_TABLE);
            $wpdb->query("TRUNCATE " . self::CKAN_HARVEST_META_TABLE);
        }

        /**
         * Create tables if not exist
         */
        public function initDB()
        {
            $version = get_option('datagov_ckan_metrics_db_version', 0);
            switch ($version) {
                case 0:
                    $this->migration1();
                case 1:
                    $this->migration2();
                case 2:
                    $this->migration3();
            }
        }

        /**
         * Migration 1: create tables
         */
        private function migration1()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $wpdb->query(
                "
                CREATE TABLE IF NOT EXISTS `" . self::CKAN_HARVEST_ORGANIZATIONS_TABLE . "` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(100) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            "
            );

            $wpdb->query(
                "
                CREATE TABLE IF NOT EXISTS `" . self::CKAN_HARVEST_RESULTS_TABLE . "` (
                  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                  `harvest_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
                  `meta_created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `meta_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `status` varchar(30) NOT NULL,
                  `gather_started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `gather_finished` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `organization_name` varchar(50) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `job_count` int(10) unsigned NOT NULL,
                  `total_datasets` bigint(20) unsigned NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `harvest_id` (`harvest_id`),
                  KEY `updated_at` (`updated_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            "
            );

            $wpdb->query(
                "
                CREATE TABLE IF NOT EXISTS `" . self::CKAN_HARVEST_META_TABLE . "` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `harvest_id` char(36) NOT NULL,
                  `key` varchar(50) NOT NULL,
                  `value` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `harvest_id` (`harvest_id`,`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            "
            );

            update_option('datagov_ckan_metrics_db_version', 1);
        }

        /**
         * Migration 2: update tables
         */
        private function migration2()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $wpdb->query(
                "
                ALTER TABLE  `" . self::CKAN_HARVEST_RESULTS_TABLE . "`
                    ADD  `added` INT UNSIGNED NOT NULL ,
                    ADD  `updated` INT UNSIGNED NOT NULL ,
                    ADD  `errored` INT UNSIGNED NOT NULL ,
                    ADD  `deleted` INT UNSIGNED NOT NULL ;
            "
            );

            update_option('datagov_ckan_metrics_db_version', 2);

        }

        /**
         * Migration 3: update tables
         */
        private function migration3()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            $wpdb->query(
                "
                ALTER TABLE  `" . self::CKAN_HARVEST_RESULTS_TABLE . "`
                    ADD  `url` VARCHAR( 255 ) NOT NULL ;
            "
            );

            update_option('datagov_ckan_metrics_db_version', 3);

        }
    }
}