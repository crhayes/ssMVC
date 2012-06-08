<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Creates a wrapper for HTML pages with embedded PHP called 'views'. Views
 * allow you to cleanly your presentation code from your application logic.
 * 
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class View {
    
    /**
     * The name of the view.
     */
    public $view;
    
    /**
     * The view data.
     */
    public $data = array();
    
    /**
     * Initialize data.
     * 
     * @param   string  $view
     * @param   array   $data 
     */
    public function __construct($view, $data = array())
    {
        $this->view = $view;
        $this->data = $data;
    }
    
    /**
     * Load a view.
     * 
     * @param   string  $file_name  Name of file to load.
     * @param   array   $data       Array of data for the view.
     * @return  View
     */
    public static function make($view, $data = array())
    {
        return new static($view, $data);
    }
    
    /**
     * Include data for the view.
     * 
     * @param   mixed   $key    Either a string or an array of data.
     * @param   string  $value  Data
     * @return  View
     */
    public function with($key, $value = null)
    {
        if (is_array($key))
        {
            $this->data = array_merge($this->data, $key);
        }
        else
        {
            $this->data[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * Created a view nested within another view. The view is stored in the
     * parent views $data array.
     * 
     * @param   string  $key    Key to use for storage in the $data array.
     * @param   View    $view   View Object
     * @return type 
     */
    public function nest($key, $view)
    {
        return $this->with($key, $view);
    }
    
    /**
     * Render a view.
     * 
     * @return void
     */ 
    public function render()
    {
        // Render nested views.
        echo $this->render_nested();        
       
    }
    
    /**
     * Recursively loop through view data to see if it is a View object.
     * If it is a View object we convert it's contents to a string.
     * 
     * @return string   View compiled into a string. 
     */
    public function render_nested()
    {
        foreach ($this->data as $key => $data)
        {
            if ($data instanceOf View)
            {
                $this->data[$key] = $data->render_nested();
            }
        }
        
        // Compile the view into a string and return it so it can be stored
        // in the data array
        return $this->compile_view();
    }

    /**
     * Compile a view by getting the contents of the view file and then
     * eval'ing it to execute any php code within.
     * 
     * @return string 
     */
    public function compile_view()
    {
        $__data = $this->data;
        $__content = $this->load();
        
        // Convert template echos to php echos.
        $__content = $this->compile_echos($__content);
        
        ob_start() and extract($__data);
        
        eval('?>'.$__content);
        $contents = ob_get_contents();
        
        ob_end_clean();
        
        return $contents;
    }
    
    /**
     * Load a view file.
     * 
     * @return string 
     */
    public function load()
    {
        return file_get_contents(APPPATH.'views'.DS.$this->view.EXT);
    }
    
    public function compile_echos($value)
    {
        return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
    }
    
}