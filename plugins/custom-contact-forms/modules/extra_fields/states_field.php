<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('ccf_states_field')) {
    class ccf_states_field
    {
        var $field_code;

        function ccf_states_field($class = null, $id = null, $initial_value = null, $field_instructions = null)
        {
            $this->field_code = '';
            $class_attr       = ($class == null) ? '' : $class;
            $id_attr          = ($id == null) ? '' : ' id="' . $id . '" ';
            if ($field_instructions == null) {
                $instructions_attr = '';
                $tooltip_class     = '';
            } else {
                $instructions_attr = ' title="' . $field_instructions . '" ';
                $tooltip_class     = 'ccf-tooltip-field';
            }
            $this->field_code .= '<select name="usaStates" class="' . $tooltip_class . ' ' . $class_attr . '" ' . $id_attr . $instructions_attr . '>' . "\n";
            $states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida',
                'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana',
                'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska',
                'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota',
                'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas',
                'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
            foreach ($states as $state) {
                if ($initial_value != null && $state == $initial_value)
                    $this->field_code .= '<option selected="selected">' . $state . '</option>' . "\n";
                else
                    $this->field_code .= '<option>' . $state . '</option>' . "\n";
            }
            $this->field_code .= '</select>';
        }

        function getCode()
        {
            return $this->field_code;
        }
    }
}