<?php
/**
 * Plugin Name: Suggest a Dataset
 * Plugin URI: http://reisystems.com
 * Description: Suggest a Dataset
 * Version: 0.3
 * Author: Alex Perfilov
 * Author URI: http://reisystems.com
 * License:  GPL3
 */
if (!class_exists('SuggestedDatasetsPlugin')) {
    /**
     * Class SuggestedDatasetsPlugin
     */
    class SuggestedDatasetsPlugin
    {
        /**
         * Start up
         */
        public function __construct()
        {
            add_action('admin_menu', array($this, 'add_plugin_page'));
            add_action('admin_init', array($this, 'page_init'));
        }

        /**
         * Add plugin page
         */
        public function add_plugin_page()
        {
            add_menu_page(
                'Dataset Suggestions',
                'Dataset Suggestions',
                'manage_options',
                'datasets-suggested',
                array($this, 'create_admin_page')
            );

            if (isset($_GET['page']) && $_GET['page'] == 'datasets-suggested') {
                $this->preLoader();
            }
        }

        /**
         *
         */
        public function preLoader()
        {
            include_once('DatasetSuggestionDB.class.php');
            include_once('DatasetSuggestion.class.php');
            include_once('DatasetSuggestionImporter.class.php');
            include_once('DatasetSuggestionListTable.class.php');

            $this->processComment();
            $this->processSgdList();

            add_action('admin_print_styles', array($this, 'addAdminPluginStyles'), 1);
            add_action('admin_print_scripts', array($this, 'addAdminPluginScripts'), 1);
        }

        /**
         *
         */
        public function addAdminPluginStyles()
        {
            wp_register_style('sgd-common', plugins_url() . '/suggested-datasets/css/suggested-datasets.css');
            wp_enqueue_style('sgd-common');
        }

        /**
         *
         */
        public function addAdminPluginScripts()
        {
            wp_register_script('sgd-common', plugins_url() . '/suggested-datasets/js/suggested-datasets.js');
            wp_enqueue_script('sgd-common');
        }

        /**
         * Plugin page callback
         */
        public function create_admin_page()
        {
            $this->loadNewSuggestions();

            //Prepare Table of elements
            $suggestions_list_table = new DatasetSuggestionListTable();
            $suggestions_list_table->prepare_items();

            ?>
            <div class="wrap sgd-list">
                <h2>Dataset Suggestions</h2>

                <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="sgd-list" value="1"/>

                    <?php
                    $suggestions_list_table->display();
                    ?>

                </form>
            </div>
        <?php
        }

        /**
         *
         */
        private function processSgdList()
        {
            if (isset($_POST['sgd-list'])) {
                if (isset($_POST['dataset_suggestion']) && sizeof($suggestions = $_POST['dataset_suggestion']) && isset($_POST['action'])) {
                    $action            = str_replace('mark_', '', $_POST['action']);
                    $available_actions = DatasetSuggestion::getAvailableStatuses();
                    if (in_array($action, $available_actions)) {
                        $DSG_DB = new DatasetSuggestionDB();
                        $DSG_DB->updateSuggestionStatuses($suggestions, array_search($action, $available_actions));
                    }
                }
                wp_redirect($_SERVER['HTTP_REFERER']);
            }
        }

        /**
         *
         */
        private function processComment()
        {
            if (isset($_POST['addComment'])) {
                $addComment = $_POST['addComment'];
                foreach ($addComment as $key => $value) {

                    $suggestionManager = new DatasetSuggestionDB();

                    $comment = trim($_POST['comment'][$key]);
                    if ($comment) {
                        $suggestionManager->addCommentForSuggestion($key, $comment);
                    }

                    if (isset($_POST['setStatus'][$key])) {
                        $status = $_POST['setStatus'][$key];
                        $suggestionManager->updateSuggestionStatuses(array($key), $status);
                    }
                }
                wp_redirect($_SERVER['HTTP_REFERER']);
            }
        }

        /**
         * @throws Exception
         */
        private function loadNewSuggestions()
        {
            if (!class_exists('CustomContactForms')) {
                throw new Exception('CustomContactForms class not defined / CustomContactForms plugin not active');
            }
            /**
             * create tables if needed
             */
            $dsg_db = new DatasetSuggestionDB();
            $dsg_db->initDB();

            $suggestImporter = new DatasetSuggestionImporter();
            $suggestImporter->importSuggestions();
        }

        /**
         * Register and tables
         */
        public function page_init()
        {

        }
    }
}