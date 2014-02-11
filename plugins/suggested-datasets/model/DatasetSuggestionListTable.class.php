<?php

//Our class extends the WP_List_Table class, so we need to make sure that it's there
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if (!class_exists('DatasetSuggestionListTable')) {
    /**
     * Class DatasetSuggestionListTable
     */
    class DatasetSuggestionListTable extends WP_List_Table
    {
        /** ************************************************************************
         * REQUIRED. Set up a constructor that references the parent constructor. We
         * use the parent reference to set some default configs.
         ***************************************************************************/
        function __construct()
        {
            global $status, $page;

            //Set parent defaults
            parent::__construct(array(
                'singular' => 'dataset_suggestion', //singular name of the listed records
                'plural'   => 'dataset_suggestions', //plural name of the listed records
                'ajax'     => false //does this table support ajax?
            ));

        }

        /**
         * Add extra markup in the toolbars before or after the list
         * @param string $which , helps you decide if you add the markup after (bottom) or before (top) the list
         */
        function extra_tablenav($which)
        {
            if ($which == "top") {
                //The code that goes before the table is here
//                echo "Hello, I'm before the table";
            }
            if ($which == "bottom") {
                //The code that goes after the table is there
//                echo "Hi, I'm after the table";
            }
        }

        /**
         * Define the columns that are going to be used in the table
         * @return array $columns, the array of columns to use with the table
         */
        function get_columns()
        {
            $columns = array(
                'cb'         => '<input type="checkbox" />', //Render a checkbox instead of text
                'status'     => __('Status'),
                'author'     => __('Submitted by'),
                'suggestion' => __('Suggestion'),
            );

            return $columns;
        }

        /**
         * Decide which columns to activate the sorting functionality on
         * @return array $sortable, the array of columns that can be sorted by the user
         */
        public function get_sortable_columns()
        {
            return $sortable = array(
                'name'   => 'name',
                'status' => 'status',
//                'col_link_visible'=>'link_visible'
            );
        }

        /** ************************************************************************
         * Optional. If you need to include bulk actions in your list table, this is
         * the place to define them. Bulk actions are an associative array in the format
         * 'slug'=>'Visible Title'
         * If this method returns an empty value, no bulk action will be rendered. If
         * you specify any bulk actions, the bulk actions box will be rendered with
         * the table automatically on display().
         * Also note that list tables are not automatically wrapped in <form> elements,
         * so you will need to create those manually in order for bulk actions to function.
         * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
         **************************************************************************/
        function get_bulk_actions()
        {
            $actions = array(
                'mark_new'          => 'Mark as new',
                'mark_open'         => 'Open',
                'mark_closed'       => 'Close',
                'mark_rejected'     => 'Reject',
                'mark_under_review' => 'Mark as Under Review',
            );

            return $actions;
        }

        /**
         * Prepare the table with different parameters, pagination, columns and table elements
         */
        function prepare_items()
        {
            /**
             * @var wpdb $wpdb
             */
            global $wpdb;

            /* -- Preparing your query -- */
            $query = "SELECT * FROM " . SGD_SUGGESTIONS_TABLE;

            /* -- Ordering parameters -- */
            //Parameters that are going to be used to order the result
            $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'DESC';
            $order   = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'created_at';
            if (!empty($orderby) & !empty($order)) {
                $query .= ' ORDER BY ' . $order . ' ' . $orderby;
            }

            /* -- Pagination parameters -- */
            //Number of elements in your table?
            $totalitems = $wpdb->query($query); //return the total number of affected rows

            //How many to display per page?
            $perpage = 4;
            //Which page is this?
            $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
            //Page Number
            if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
                $paged = 1;
            }
            //How many pages do we have in total?
            $totalpages = ceil($totalitems / $perpage);
            //adjust the query to take pagination into account
            if (!empty($paged) && !empty($perpage)) {
                $offset = ($paged - 1) * $perpage;
                $query .= ' LIMIT ' . (int)$offset . ',' . (int)$perpage;
            }

            /* -- Register the pagination -- */
            $this->set_pagination_args(array(
                "total_items" => $totalitems,
                "total_pages" => $totalpages,
                "per_page"    => $perpage,
            ));
            //The pagination links are automatically built according to those parameters

            /**
             * REQUIRED. Now we need to define our column headers. This includes a complete
             * array of columns to be displayed (slugs & titles), a list of columns
             * to keep hidden, and a list of columns that are sortable. Each of these
             * can be defined in another method (as we've done here) before being
             * used to build the value for our _column_headers property.
             */
            $columns  = $this->get_columns();
            $hidden   = array();
            $sortable = $this->get_sortable_columns();

            /**
             * REQUIRED. Finally, we build an array to be used by the class for column
             * headers. The $this->_column_headers property takes an array which contains
             * 3 other arrays. One for all columns, one for hidden columns, and one
             * for sortable columns.
             */
            $this->_column_headers = array($columns, $hidden, $sortable);

            /* -- Fetch the items -- */
            $this->items = $wpdb->get_results($query);

            $suggestionManager = new DatasetSuggestionDB();

            foreach ($this->items as $item) {
                $item->comments = $suggestionManager->getCommentsBySuggestionId($item->id);
            }
        }

        /**
         * Display the records
         * @since  3.1.0
         * @access public
         */
        function display()
        {
            $this->display_tablenav('top');

            $admin = wp_get_current_user();
            define('ADMIN_LOGIN_NAME', $admin->user_nicename);

            ?>
            <table class="wp-list-table <?php echo implode(' ', $this->get_table_classes()); ?>" cellspacing="0">
                <thead>
                <tr>
                    <?php $this->print_column_headers(); ?>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <?php $this->print_column_headers(false); ?>
                </tr>
                </tfoot>

                <tbody id="the-list">
                <?php $this->display_rows_or_placeholder(); ?>
                </tbody>
            </table>
            <?php

            $this->display_tablenav('bottom');
        }

        /**
         *
         */
        function display_rows_or_placeholder()
        {
            if ($this->has_items()) {
                $this->display_rows();
            } else {
                $this->no_items();
            }
        }

        /**
         *
         */
        function display_rows()
        {
            foreach ($this->items as $item)
                $this->single_row($item);
        }

        /**
         * @param $item
         */
        function single_row($item)
        {
            static $row_class = '';
            $row_class = ($row_class == '' ? ' class="alternate"' : '');

            echo '<tr' . $row_class . '>';
            $this->single_row_columns($item);
            echo '</tr>';
        }

        /** ************************************************************************
         * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
         * is given special treatment when columns are processed. It ALWAYS needs to
         * have it's own method.
         * @see WP_List_Table::::single_row_columns()
         * @param array $item A singular item (one full row's worth of data)
         * @return string Text to be placed inside the column <td> (movie title only)
         *                    *************************************************************************/
        function column_cb($item)
        {
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /*$1%s*/
                $this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
                /*$2%s*/
                $item->id //The value of the checkbox should be the record's id
            );
        }

        /**
         * Generates the columns for a single row of the table
         * @since  3.1.0
         * @access protected
         * @param object $item The current item
         */
        function single_row_columns($item)
        {

            echo '<th scope="row" class="check-column">';
            echo $this->column_cb($item);
            echo '</th>';

            $statuses = DatasetSuggestion::getAvailableStatuses();
            $status   = $statuses[$item->status];

            echo '<td class="sgd-status status-' . $status . '">' . $status . '</td>';

            echo '<td class="sgd-submitted">
                <div class="sgd-from-name">' . nl2br(stripslashes($item->from_name)) . '</div>
                <div class="sgd-from-email">' . nl2br(stripslashes($item->from_email)) . '</div>
            </td>';


            echo '<td class="sgd-suggestion-td">
                <div class="sgd-suggestion sgd-bubble">
                    <div class="sgd-name">' . nl2br(stripslashes($item->name)) . '</div>
                    <div class="sgd-date">' . $item->created_at . '</div>
                    <div class="sgd-description">' . nl2br(stripslashes($item->description)) . '</div>
                ';

            echo '<div class="sgd-comments">';

            unset($comments);
            if (is_array($item->comments) && sizeof($item->comments)) {
                $comments     = $item->comments;
                $total        = sizeof($comments);
                $commentsLink = $total . ' ' . ((1 == $total) ? 'comment' : 'comments');
            } else {
                $commentsLink = 'Add a comment';
            }
            echo '<div class="sgd-trigger">
                <a class="comments-trigger" id="trigger_' . $item->id . '" rel="comments_' . $item->id . '" href="javascript:void(0)">' . $commentsLink . '</a>
            </div>';

            echo '<div class="comments-wrap comments_' . $item->id . '">';

            if ($comments) {
                foreach ($comments as $comment) {
                    echo '<div class="sgd-bubble sgd-comment">
                    <div class="sgd-admin-name">' . $comment->user_login . '</div>
                    <div class="sgd-date">' . $comment->created_at . '</div>
                    <div class="sgd-comment-text">' . nl2br(stripslashes($comment->comment)) . '</div>
                </div>';
                }
            }

            echo '<div class="sgd-bubble sgd-new-comment">
                    <div class="sgd-admin-name">' . ADMIN_LOGIN_NAME . '</div>
                    <div class="sgd-newcomment-form">
                        <textarea class="sgd-textarea" name="comment[' . $item->id . ']"></textarea>
                        <div class="sgd-submit-comment">';
            $this->printStatusOptions($item->id, $status);
            echo '<button name="addComment[' . $item->id . ']" value=1 type="submit">Add a comment & Update Suggestion status</button
                        </div>
                    </div>
                </div>';

            echo '      </div>
                    </div>
                </div>
            </td>';
        }

        /**
         * @param $id
         * @param $oldstatus
         */
        public function printStatusOptions($id, $oldstatus)
        {
            echo '<select name="setStatus[' . $id . ']">';
            $statuses = DatasetSuggestion::getAvailableStatuses();
            foreach ($statuses as $key => $status) {
                echo '<option value="' . $key . '" ' . ($oldstatus == $status ? 'selected="selected"' : '') . '>' . $status . '</option>';
            }
            echo '</select>';
        }
    }


}