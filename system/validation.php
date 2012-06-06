<?php

class Validation {
    
    public $data = array();
    
    private $rules = array();
    
    public $errors = array();
    
    function __construct($data)
    {
        $this->data = $data;
    }
    
    public static function make($data)
    {
        return new static($data);
    }
    
    public function check()
    {
        // Import data locally.
        $data = $this->data;
        $rules = $this->rules;
        
        // Get the expected fields.
        $expected_fields = array_keys($rules);
        
        // Loop through the expected fields.
        foreach ($expected_fields as $field)
        {
            // Set up the data we will use.
            $field_value = $data[$field];
            $field_rules = $rules[$field];
            $param = null;
            
            // Loop through each field rule.
            foreach($field_rules as $rule)
            {
                // If the rule contains a colon we need to parse it into rule and parameter.
                // i.e. min:5 -> $rule = min, $param = 5
                if (strstr($rule, ':'))
                {
                    $rule = $this->parse_rule($rule);
                    
                    // If the parsed field rule is an array we get the rule and the param
                    if (is_array($rule))
                    {
                        list($rule, $param) = $rule;
                    }                    
                }
                                
                // Call the validation function.
                if ( ! call_user_func_array(array('Validation', 'validate_'.$rule), array($field_value, $param)))
                {
                    $this->errors[$field][$rule] = $this->get_error_message($field, $rule);
                }
            }
        }
        
        if (!empty($this->errors))
        {
            return false;
        }
        
        return true;
    }

    
    public function rules($field, $rules = null)
    {
        if (is_array($field))
        {
            foreach ($field as $field => $rule)
            {
                $this->rule($field, $rule);
            }
            
            return $this;
        }
        
        $this->rule($field, $rules);
        
        return $this;
    }
    
    public function rule($field, $rule)
    {
        $this->rules[$field] = (is_string($rule)) ? explode('|', $rule) : $rule;
    }
    
    public function parse_rule($rule)
    {
        return explode(':', $rule);
    }
    
    public function get_error_message($field, $rule)
    {
        return 'there was an error';
    }
    
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * [Validation Rule] Any field with the 'is_required' rule applied is a required field (i.e. not empty).
     * This function makes sure the field input is not empty.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @return 	bool		Whether or not the field input validates.
     */
    private function validate_required($value)
    {
        if (empty($value))
            return false;

        return true;
    }

    /**
     * [Validation Rule] Any field with the 'is_rdate' rule applied must be a valid date.
     * This function makes sure the field input is a valid date.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @return 	bool            Whether or not the field input validates.
     */
    private function validate_date($value)
    {
        date_default_timezone_set('America/Toronto');

        try
        {
            $dt = new DateTime(trim($value, $params = null));
        }
        catch (Exception $e)
        {
            return false;
        }

        $month = $dt->format('m');
        $day = $dt->format('d');
        $year = $dt->format('Y');

        if (checkdate($month, $day, $year))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * [Validation Rule] Any field with the 'is_email' rule applied must be a valid email.
     * This function makes sure the field input is a valid email.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @return 	bool		Whether or not the field input validates.
     */
    private function validate__email($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }

        return false;
    }

    /**
     * [Validation Rule] Any field with the 'in_range' rule applied must be an integer within the specified range.
     * This function makes sure the field input is an integer within the specified range.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @param	array	$params	Bottom and top values for range.
     * @return 	bool		Whether or not the field input validates.
     */
    private function validate_range($value, $params)
    {
        return ( is_numeric($value) && $value >= $params[0] && $value <= $params[1] ) ? true : false;
    }

    /**
     * [Validation Rule] Any field with the 'min_length' rule applied must be at least the specified length.
     * This function makes sure the field input is at least the specified length.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @param	array	$param	Minimum length value specified.
     * @return 	bool		Whether or not the field input validates.
     */
    private function min_length($value, $param)
    {
        return ( strlen($value) >= $param ) ? true : false;
    }

    /**
     * [Validation Rule] Any field with the 'max_length' rule applied must not be longer than the specified length.
     * This function makes sure the field input is not longer than the specified length.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @param	array	$param	Maximum length value specified.
     * @return 	bool		Whether or not the field input validates.
     */
    private function max_length($value, $param)
    {
        return ( strlen($value) <= $params ) ? true : false;
    }

    /**
     * [Validation Rule] Any field with the 'not_same' rule applied must not contain the same value as the string it is 
     * being compared against.
     *
     * @param	string	$value	Field value submitted.
     * @param	string	$param	String to compare.
     * @return 	bool		Returns true for different string, false for same string.
     */
    private function not_same($value, $param)
    {
        // Compare two strings.
        $compare = strcasecmp($value, $param);

        // If they are the same validation check fails.
        if ($compare == 0)
            return false;

        return true;
    }

    /**
     * [Validation Rule] Used to validate that a person is of a certain age.
     *
     * @param	string	$value	Field value submitted.
     * @param	string	$param	Minimum age a user can be.
     * @return 	bool		Returns true if person is of age, false if not.
     */
    private function is_of_age($value, $param)
    {
        return strtotime("-$param year") >= strtotime($value);
    }

}