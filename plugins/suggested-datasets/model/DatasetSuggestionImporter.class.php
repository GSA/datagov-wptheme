<?php

if (!class_exists('DatasetSuggestionImporter')) {
    /**
     * Class DatasetSuggestionImporter
     */
    class DatasetSuggestionImporter extends CustomContactForms
    {
        /**
         * @var
         */
        public $form;

        /**
         *
         */
        public function __construct()
        {
            $this->form = $this->selectForm('', 'suggest');
            if (empty($this->form)) {
                throw new Exception("Suggest form doesn't exits!
                You must create custom contact form with slug 'suggest' to collect suggestions.");
            }
        }

        /**
         * @throws Exception
         */
        public function importSuggestions()
        {
            $suggestions = $this->selectNewSubmissions();
            if (sizeof($suggestions)) {
                foreach ($suggestions as $suggestion) {
                    $data_array = $this->decodeCcfData($suggestion->data_value);

                    $dataset_suggesion              = new DatasetSuggestion();
                    $dataset_suggesion->name        = $data_array['dataset_name'];
                    $dataset_suggesion->description = $data_array['dataset_description'];
                    $dataset_suggesion->fromName    = $data_array['your_name'];
                    $dataset_suggesion->fromEmail   = $data_array['your_email'];
                    $dataset_suggesion->datetime    = $suggestion->data_time;

                    $id = $dataset_suggesion->add();
                    if ($id > 0) {
                        $this->deleteUserData($suggestion->id);
                    } else {
                        throw new Exception('Could not add suggestion to database');
                    }
                }
            }
        }

        /**
         * @return mixed
         */
        public function selectNewSubmissions()
        {
            return $this->selectAllUserData($this->form->id);
        }

        /**
         * That's kinda weird CCF data format o_O
         * s:12:"dataset_name";s:2:"hi";s:19:"dataset_description";s:5:"hello";s:9:"
         * just copied that function from CustomContactFormsUserData class, didn't want to include that
         * @param $data
         * @return array
         */
        private function decodeCcfData($data)
        {
            $data_array = array();
            while (!empty($data)) {
                $key_length       = $this->strstrb($data, ':"');
                $key_length       = str_replace('s:', '', $key_length);
                $piece_length     = 6 + strlen($key_length) + (int)$key_length;
                $key              = substr($data, (4 + strlen($key_length)), (int)$key_length);
                $data             = substr($data, $piece_length);
                $value_length     = $this->strstrb($data, ':"');
                $value_length     = str_replace('s:', '', $value_length);
                $piece_length     = 6 + strlen($value_length) + (int)$value_length;
                $value            = substr($data, (4 + strlen($value_length)), (int)$value_length);
                $data             = substr($data, $piece_length);
                $data_array[$key] = $value;
            }

            return $data_array;
        }

        /**
         * CustomContactFormsUserData method, used for decodeData()
         * @param $h
         * @param $n
         * @return mixed
         */
        private function strstrb($h, $n)
        {
            return array_shift(explode($n, $h, 2));
        }
    }
}