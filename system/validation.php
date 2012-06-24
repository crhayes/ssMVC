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
    function __construct($post, $files)
    {
        $this->data = array_merge($post, $files);
    }

    /**
     * Create a new validation instance.
     * 
     * @param   array   $data   Data to be validated.
     * @return  \self
     */
    public static function make($post = array(), $files = array())
    {
        return new self($post, $files);
    }

    /**
     * Validate data based on a set of validation rules.
     * 
     * @return  boolean 
     */
    public function valid()
    {
        // Load error messages.
        Message::load('error.validation');

        // Loop through each field.
        foreach ($this->rules as $field => $rules)
        {
            // Loop through each of the field's rules.
            foreach ($rules as $rule)
                $this->check($field, $rule);
        }

        return empty($this->errors);
    }

    /**
     * Validate data based on a set of validation rules.
     * 
     * @return  boolean 
     */
    public function invalid()
    {
        return !$this->valid();
    }
    
    /**
     * Validate data based on a set of validation rules.
     * 
     * @return  boolean 
     */
    public function passes()
    {
        return $this->valid();
    }
    
    /**
     * Validate data based on a set of validation rules.
     * 
     * @return  boolean 
     */
    public function fails()
    {
        return $this->invalid();
    }

    /**
     * Check each field's data against each rule that has been applied to it.
     * 
     * @param   string  $field
     * @param   string  $rule 
     */
    public function check($field, $rule)
    {
        // Get the rule name and any parameters.
        list($rule, $parameters) = $this->parse($rule);

        // Get the value for the current field.
        $value = Arr::get($field, $this->data);

        // Before validating the field we need to make sure it is actually 
        // validatable. We only run the validation rule if the value for the
        // field is set (i.e. passes the validate_required rule), or if the
        // rule is the required rule itself.
        $validatable = $this->validatable($field, $value, $rule);

        // Call the validation function.
        if ($validatable and !$this->{'validate_' . $rule}($field, $value, $parameters))
        {
            $this->errors[$field][$rule] = $this->get_error_message($field, $rule, $parameters);
        }
    }

    /**
     * Determine if an attribute is validatable.
     * 
     * To be considered validatable, the attribute must either exist, or the rule
     * being checked must implicitly validate "required", such as the "required" rule.
     * 
     * @param   sting   $field
     * @param   mixed   $value
     * @param   sring   $rule
     * @return  boolean 
     */
    private function validatable($field, $value, $rule)
    {
        return $this->validate_required($field, $value) or $rule == 'required';
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
        // Get an array of the rules.
        $rules = (is_string($rule)) ? explode('|', $rule) : $rule;

        // Trim each rule and then store it.
        $this->rules[$field] = array_map('trim', $rules);
    }

    /**
     * Parse a rule that requires a parameter into an array that contains the
     * rule name and the paramater passed to it.
     * 
     * i.e. min:5 will be parsed to array('min','5')
     * 
     * @param   string  $rule
     * @return  array 
     */
    public function parse($rule)
    {
        $parameters = array();

        // If the rule has parameters we parse them out.
        if (strstr($rule, ':'))
        {
            list($rule, $parameters) = explode(':', $rule);

            // Trim off any whitespace.
            $rule = trim($rule);
            $parameters = trim($parameters);


            // If there are multiple parameters they'll be separated by a
            // comma, so we'll parse those out as well.
            if (strstr($parameters, ','))
            {
                $parameters = explode(',', $parameters);
                $parameters = array_map('trim', $parameters);
            }
        }

        return array($rule, (array) $parameters);
    }

    /**
     * Get the error message for a failed rule on a particular field.
     * 
     * @param   string  $field
     * @param   string  $rule
     * @param   array   $parameters
     * @return  string 
     */
    public function get_error_message($field, $rule, $parameters)
    {
        if ($message = Message::get("error.validation.$field.$rule"))
        {
            return $message;
        }
        else
        {
            $message = Message::get("error.validation.$rule");

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
                Arr::get(0, $parameters),
                Arr::get(1, $parameters),
                ' ',
                ' '
            );

            // Use find and replace parameters to format the message.
            return str_replace($find, $replace, $message);
        }
    }

    /**
     * Get field errors.
     * 
     * @return  array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * [Validation Rule] Validate that a value exists.
     * 
     * @param   string  $field
     * @param   mxied   $value
     * @return  boolean 
     */
    private function validate_required($field, $value)
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
     * [Validation Rule] Validate that a value is a date.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_date($field, $value)
    {
        try
        {
            $dt = new DateTime(trim($value));
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
     * [Validation Rule] Validate that a value is an email address.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_email($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * [Validation Rule] Validate that a value is an IP address.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_ip($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * [Validation Rule] Validate that a value is a URL.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_url($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * [Validation Rule] Validate that a value is between two values.
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_between($field, $value, $parameters)
    {
        if (is_numeric($value))
        {
            return $value >= $parameters[0] && $value <= $parameters[1];
        }

        return strlen($value) >= $parameters[0] && strlen($value) <= $parameters[1];
    }

    /**
     * [Validation Rule] Validate the size of a value is greater than a
     * minimum value.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $paramaters
     * @return  boolean 
     */
    private function validate_min($field, $value, $paramaters)
    {
        if (is_numeric($value))
        {
            return $value >= $paramaters[0];
        }

        return strlen(trim($value)) >= $paramaters[0];
    }

    /**
     * [Validation Rule] Validate the size of a value is less than a
     * maximum value.
     * 
     * @param   string  $field
     * @param   sring   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_max($field, $value, $parameters)
    {
        if (is_numeric($value))
        {
            return $value <= $parameters[0];
        }

        return strlen(trim($value)) <= $parameters[0];
    }

    /**
     * [Validation Rule] Validate that the value of one field is the same as
     * the value of another field.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_match($field, $value, $parameters)
    {
        $other = $parameters[0];

        return isset($this->data[$other]) and $value == $this->data[$other];
    }

    /**
     * [Validation Rule] Validate that the value of one field is different from
     * the value of another field.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_mismatch($field, $value, $parameters)
    {
        return !$this->validate_match($field, $value, $parameters);
    }

    /**
     * [Validation Rule] Validate that a value is the same as a given value.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_same($field, $value, $parameters)
    {
        if (is_numeric($value))
        {
            return $value != $parameters[0];
        }

        // Compare two strings.
        return (strcasecmp($value, $parameters[0]) == 0) ? true : false;
    }

    /**
     * [Validation Rule] Validate that a value is different from a given value.
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_different($field, $value, $parameters)
    {
        return !$this->validate_same($field, $value, $parameters);
    }

    /**
     * [Validation Rule] Validate a person's age given their birthdate
     * (formatted YYYY-MM-DD).
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_age($field, $value, $parameters)
    {
        return strtotime("-$parameters[0] year") >= strtotime($value);
    }

    /**
     * Validate that an attribute is numeric.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    protected function validate_numeric($field, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that an attribute is an integer.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    protected function validate_integer($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * [Validation Rule] Validate that a value contains only
     * alphabetic characters.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    protected function validate_alpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * [Validation Rule] Validate that a value contains only
     * alpha-numeric characters.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    protected function validate_alpha_num($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * [Validation Rule] Validate that a value contains only alpha-numeric
     * characters, dashes, and underscores.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_alpha_dash($field, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * [Validation Rule] Validate that a value is a Canadian postal code.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_postal_code($field, $value)
    {
        return preg_match('/[ABCEGHJKLMNPRSTVXY]\d[A-Z] \d[A-Z]\d/', $value);
    }

    /**
     * [Validation Rule] Validate that a value is an American zip code.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @return  boolean 
     */
    private function validate_zip_code($field, $value)
    {
        return preg_match('/\d{5}(?(?=-)-\d{4})/', $value);
    }

    /**
     * [Validation Rule] Validate that a value is in a comma-delimated list.
     * 
     * @param   string  $field
     * @param   mixed   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_in($field, $value, $parameters)
    {
        return in_array($value, $parameters);
    }

    /**
     * [Validation Rule] Validate that a file has an allowed extension.
     * 
     * @param   string  $field
     * @param   array   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_file_type($field, $value, $parameters)
    {
        // Get the file extension
        $ext = pathinfo($value['name'], PATHINFO_EXTENSION);

        return in_array($ext, $parameters);
    }

    /**
     * [Validation Rule] Validate that the size of a file is not too large.
     * 
     * @param   string  $field
     * @param   array   $value
     * @param   array   $parameters
     * @return  boolean 
     */
    private function validate_file_size($field, $value, $parameters)
    {
        // If the rule is specified in KB convert to bytes
        if ($size = stristr($parameters[0], 'kb', true))
        {
            $size = $size * 1024;
        }
        // If the rule is specified in MB convert to bytes
        else if ($size = stristr($parameters[0], 'mb'))
        {
            $size = $size * (1024 * 2);
        }

        return $value['size'] <= $size;
    }

}