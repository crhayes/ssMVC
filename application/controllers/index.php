<?php

class Index_Controller extends Controller {
    
    function action_index()
    { 
        $post = array('name' => 'Chris', 'email' => 'chayes@okd.com');
        
        $post = Validation::make($post)
            ->rules('name', 'required')
            ->rules('email', 'required')
            ->rules('facebook', 'required');
        
        if ( ! $post->check())
        {
            print_r($post->errors());
        }
        
        return View::make('index')
            ->with('another', 'I am testing')
            ->nest('test', View::make('test')
                ->with('param1', 'this is a param'));
    }
    
    function action_about($section = null)
    {        
        $result = Database::instance()->query("SELECT * FROM users")->fetch_all('array');
        
        echo Export::csv_from_mysql_resource($result, 'about');
        
        return View::make('about')
            ->with('people', $result);
    }
    
}