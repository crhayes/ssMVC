<?php

class Validation {
    
    /**
     * Store the data to be validated.
     * 
     * @var array 
     */
    public $data = array();
    
    /**
     * Store the field validation rules.
     * 
     * @var array 
     */
    private $rules = array();
    
    /**
     * Store any validation errors.
     * 
     * @var array 
     */
    public $errors = array();
    
    /**
     * Initialize validation data.
     * 
     * @param   array   $data 
     */
    function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * Create a new validation instance.
     * 
     * @param   array   $data   Data to be validated.
     * @return \static 
     */
    public static function make($data)
    {
        return new static($data);
    }
    
    /**
     * Check the data of each field against each rule that has been applied to it.
     * 
     * @return boolean 
     */
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
        
        // If we have errors the check fails.
        if ($this->invalid())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Add a set of rules to a field.
     * 
     * @param   mixed   $field  Either a field name (string) or an array with 
     *                          fields as keys and strings (rules) as values.
     * @param   string  $rules  Rules are added as a string separated by '|'.
     * @return \Validation 
     */
    public function rules($field, $rules = null)
    {
        // If they sent in an array we iterate over each field and apply the rules.
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
    
    /**
     * Convert a field rule string to an array and store it.
     * 
     * @param   string  $field
     * @param   string  $rule 
     */
    public function rule($field, $rule)
    {
        $this->rules[$field] = (is_string($rule)) ? explode('|', $rule) : $rule;
    }
    
    /**
     * Parse a rule that requires a parameter into an array that contains the
     * rule name and the paramater passed to it.
     * 
     * ie. min:5 will be parsed to array('min','5')
     * 
     * @param type $rule
     * @return type 
     */
    public function parse_rule($rule)
    {
        return explode(':', $rule);
    }
    
    /**
     * Check if the input is valid by checking if there are errors.
     * 
     * @return boolean 
     */
    public function valid()
    {
        return empty($this->errors);
    }
    
    /**
     * Check if the input is valid by checking if there are errors.
     * 
     * @return boolean 
     */
    public function invalid()
    {
        return ! $this->valid();
    }
    
    /**
     * Get the error message for a failed rule on a particular field.
     * 
     * @param   string   $field
     * @param   type    $rule
     * @return string 
     */
    public function get_error_message($field, $rule)
    {
        return 'there was an error';
    }
    
    /**
     * Checks if there are any field errors.
     * 
     * @return  boolean 
     */
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