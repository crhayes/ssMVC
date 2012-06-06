<?php

class Router {
    
    /**
     * The requested route.
     * 
     * @var string 
     */
    private $requested_route;
    
    /**
     * An array of the route containing controller, action, and params.
     * 
     * @var array 
     */
    private $route;
    
    /**
     * Matched controller for the request.
     * 
     * @var string 
     */
    private $controller;
    
    /**
     * Matched action for the request.
     * 
     * @var string 
     */
    private $action;
    
    /**
     * Array to store the parameters of the request.
     * 
     * @var array 
     */
    private $params = array();
    
    /**
     * Get the requested route.
     * 
     * @return  void 
     */
    function __construct()
    {
        if (isset($_GET['rt']))
        {
            // Store the requested route.
            $this->requested_route = $_GET['rt'];

            // Break route into array to determine controller, action and params.
            $this->route = explode('/', rtrim(strtolower($this->requested_route), '/'));
        
        }            
    }
    
    /**
     * Load a controller, call the appropriate action and render a response.
     * 
     * @return void
     */
    function route()
    {
        $route = $this->route;
        
        // Check if the controller exists. If it does we shift it off the array,
        // if not we default to the index controller.
        $this->controller = ($this->controller_exists($route[0]))
            ? array_shift($route)
            : $this->get_default_controller();
        
        // Load the controller.
        $controller = $this->load_controller($this->controller);

        // Check if the action exists. If it does we shift it off the array,
        // if not we default to the index action.
        $this->action = ($this->action_exists($controller, $route[0]))
            ? array_shift($route)
            : 'index';
        
        // Format the action so we can call it.
        $action = $this->format_action($this->action);
        
        // If there is anything left in the array it becomes our parameters.
        if (is_array($route))
        {
            $this->params = $route;
        }
        
        // Call the action with parameters and get the response.
        $response = call_user_func_array(array($controller, $action), $this->params);
        
        // If the response is a View object we render it.
        if ($response instanceOf View)
            $response->render();
    }
    
    /**
     * Get the requested route.
     * 
     * @return  void 
     */
    private function get_requested_route()
    {
        $this->requested_route = explode('/', rtrim(strtolower($_GET['rt']), '/'));
    }
    
    /**
     * Get the default controller.
     * 
     * @return string 
     */
    private function get_default_controller()
    {
        return Config::get('default.controller');
    }
    
    /**
     * Check if a controller exists that matches the request.
     * 
     * @param   string   $controller    Name of the controller to look for
     * @return  bool                    True if controller exists 
     */
    private function controller_exists($controller)
    {
         return file_exists(APPPATH.'controllers'.DS.$controller.EXT);
    }
        
    /**
     * Check if an action exists that matches the request.
     * 
     * @param   string  $controller
     * @param   string  $action
     * @return  bool 
     */
    private function action_exists($controller, $action)
    {
        return method_exists($controller, $this->format_action($action));
    }
    
    /**
     * Load a controller.
     * 
     * @param   string  $controller Controller to load
     * @return  object  Controller
     */
    private function load_controller($controller)
    {
        require_once APPPATH.'controllers'.DS.$controller.EXT;        
        $controller = $this->format_controller($controller);
        
        return new $controller();
    }
    
    /**
     * Format a controller so we can create a new instance.
     * 
     * @param   string  $controller
     * @return  string 
     */
    private function format_controller($controller)
    {
        return ucfirst($controller).'_Controller';
    }
    
    /**
     * Format an action so we can call it.
     * 
     * @param   string  $action
     * @return  string 
     */
    private function format_action($action)
    {
        return 'action_'.$action;
    }
    
}