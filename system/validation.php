<?php defined('SYSPATH') or die('No direct script access.');

/**
 * The validation library provides a utility that makes it dead simple to 
 * validate forms. It allows rules to be applied to form fields and then 
 * automagically validates the rules against each field.
 * 
 * This class also handles returning error messages via the messages class.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
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
     * @return \self
     */
    public static function make($data)
    {
        return new self($data);
    }

    /**
     * Check the data of each field against each rule that has been applied to it.
     * 
     * @return boolean 
     */
    public function check()
    {
        // Load error messages.
        Message::load('error.validation');
        
        // Import data locally.
        $data = $this->data;
        $rules = $this->rules;

        // Get the expected fields.
        $expected_fields = array_keys($rules);

        // Loop through the expected fields.
        foreach ($expected_fields as $field)
        {
            // Set up the data we will use.
            $field_value = Arr::get($field, $data);
            $field_rules = Arr::get($field, $rules);
            $params = null;

            // Loop through each field rule.
            foreach ($field_rules as $rule)
            {
                $rule = $this->parse_rule($rule);

                // If the parsed field rule is an array we get the rule and the param
                if (is_array($rule))
                {
                    list($rule, $params) = $rule;
                }

                // Call the validation function.
                if (!call_user_func_array(array('Validation', 'validate_' . $rule), array($field_value, $params)))
                {
                    $this->errors[$field][$rule] = $this->get_error_message($field, $rule, $params);
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
        // If the rule has parameters we parse them out.
        if (strstr($rule, ':'))
        {
            $rule = explode(':', $rule);
            
            // If there are multiple parameters they'll be separated by a
            // comma, so we'll parse those out as well.
            if (strstr($rule[1], ','))
            {
                $rule[1] = explode(',', $rule[1]);
            }
        }
        
        return $rule;
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
        return !$this->valid();
    }

    /**
     * Get the error message for a failed rule on a particular field.
     * 
     * @param   string  $field
     * @param   type    $rule
     * @param   mixed   $params
     * @return string 
     */
    public function get_error_message($field, $rule, $params)
    {
        $message = Message::get('error.validation.'.$rule);
                
        // Set up some find parameters...
        $find = array(
            ':field',
            ':param1',
            ':param2',
            '-',
            '_'
        );            
        // And some replace parameters...
        $replace = array(
            $field,
            is_array($params) ? $params[0] : $params,
            is_array($params) ? $params[1] : '',
            ' ',
            ' '
        );

        // Use find and replace parameters to format the message.
        return str_replace($find, $replace, $message);
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
     * [Validation Rule] Validate that an attribute exists.
     *
     * @param	mixed	$value	Field value we are checking the rule against.
     * @param   mixed   $param
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_required($value, $param = null)
    {
        if (is_null($value))
        {
            return false;
        }
        elseif (is_string($value) and trim($value) === '')
        {
            return false;
        }
        elseif ($value instanceof File)
        {
            return (string) $value->getPath() !== '';
        }

        return true;
    }

    /**
     * [Validation Rule] Validate that an attribute is a date.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @param   mixed   $param
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_date($value, $param = null)
    {
        try
        {
            $dt = new DateTime(trim($value, $param));
        }
        catch (Exception $e)
        {
            return false;
        }

        $month = $dt->format('m');
        $day = $dt->format('d');
        $year = $dt->format('Y');

        return checkdate($month, $day, $year);
    }

    /**
     * [Validation Rule] Validate that an attribute is an email address.
     *
     * @param	string	$value	Field value we are checking the rule against.
     * @param   mixed   $param
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_email($value, $param = null)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * [Validation Rule] Validate that an attribute is an IP address.
     * 
     * @param   string  $value
     * @param   mixed   $param
     * @return  boolean 
     */
    private function validate_ip($value, $param = null)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * [Validation Rule] Validate that an attribute is a URL.
     * 
     * @param   string  $value
     * @param   mixed   $param
     * @return  boolean 
     */
    private function validate_url($value, $param = null)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * [Validation Rule] Validate that an attribute is within a given range.
     *
     * @param	mixed	$value	Field value we are checking the rule against.
     * @param	string	$params	Bottom and top values for range.
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_range($value, $params = null)
    {
        if (is_numeric($value))
        {
            return $value >= $params[0] && $value <= $params[1];
        }

        return strlen($value) >= $params[0] && strlen($value) <= $params[1];
    }

    /**
     * [Validation Rule] Validate that the size of an attribute is at least
     * the minimum size.
     *
     * @param	mixed	$value	Field value we are checking the rule against.
     * @param	mixed	$param	Minimum length value specified.
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_min($value, $param = null)
    {
        if (is_numeric($value))
        {
            return $value >= $param;
        }

        return strlen($value) >= $param;
    }

    /**
     * [Validation Rule] Validate that the size of an attribute is not more
     * than the max size.
     *
     * @param	mixed	$value	Field value we are checking the rule against.
     * @param	mixed	$param	Maximum length value specified.
     * @return 	boolean         Whether or not the field input validates.
     */
    private function validate_max($value, $param = null)
    {
        if (is_numeric($value))
        {
            return $value <= $param;
        }

        return strlen($value) <= $param;
    }

    /**
     * [Validation Rule] Validate that an attribute is the same as a given value.
     *
     * @param	mixed	$value	Field value submitted.
     * @param	mixed	$param	String to compare.
     * @return 	boolean         
     */
    private function validate_same($value, $param = null)
    {
        if (is_numeric($value))
        {
            return $value != $param;
        }

        // Compare two strings.
        return (strcasecmp($value, $param) == 0) ? true : false;
    }

    /**
     * [Validation Rule] Validate that an attribute is not the same 
     * as a given value.
     * 
     * @param   mixed   $value
     * @param   mixed   $param
     * @return  boolean 
     */
    private function validate_not($value, $param = null)
    {
        return !$this->validate_same($value, $param);
    }

    /**
     * [Validation Rule] Used to validate that a person is of a certain age.
     *
     * @param	string	$value	Field value submitted.
     * @param	string	$param	Minimum age a user can be.
     * @return 	boolean         Returns true if person is of age, false if not.
     */
    private function validate_age($value, $param = null)
    {
        return strtotime("-$param year") >= strtotime($value);
    }

    /**
     * [Validation Rule] Validate that an attribute contains only
     * alphabetic characters.
     *
     * @param   mixed   $value
     * @param   mixed   $param
     * @return  bool
     */
    protected function validate_alpha($value, $param = null)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * [Validation Rule] Validate that an attribute contains only
     * alpha-numeric characters.
     *
     * @param   mixed   $value
     * @param   mixed   $param
     * @return  bool
     */
    protected function validate_alpha_num($value, $param = null)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * [Validation Rule] Validate that an attribute contains only alpha-numeric
     * characters, dashes, and underscores.
     *
     * @param   mixed   $value
     * @param   mixed   $param
     * @return  bool
     */
    private function validate_alpha_dash($value, $param = null)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

}