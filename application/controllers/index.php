<?php

class Index_Controller extends Controller {
    
    public $restful = true;
        
    public function get_index()
    {               
        return View::make('index');
    }
    
    public function post_index()
    {   
        echo Upload::save($_FILES['upload'], Upload::directory());
        
        return View::make('index');
    }
    
}