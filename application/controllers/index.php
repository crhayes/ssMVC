<?php

class Index_Controller extends Controller {
    
    public $restful = true;
    
    function get_index()
    { 
        $post = array('name' => 'Chris', 'email' => 'chayes@okd.com', 'age' => 'five is a number');
        
        $post = Validation::make($post)
            ->rules('name', 'required')
            ->rules('email', 'required|email')
            ->rules('age', 'required|range:0,10')
            ->rules('facebook', 'required');
        
        if ( ! $post->check())
        {
            print_r($post->errors());
        }
        
        $users = DB::query("SELECT * FROM users")
            ->execute();
        
        echo '<pre>';
        print_r($users->fetch_row(2));
        
        return View::make('index')
            ->with('users', $users);
    }
    
    function get_about($section = null)
    {        
        //$result = Database::instance()->query("SELECT * FROM users")->fetch_all('array');
        
        return View::make('template')
            ->with('title', 'This is a test GET')
            ->nest('content', View::make('about')
                ->with('param', 'herrrrro'));
    }
    
    function post_about()
    {
        $post = Validation::make($_POST)
            ->rules('name', 'required');
        
        $post->check();
        
        return View::make('template')
            ->with('title', 'This is a test POST')
            ->nest('content', View::make('about')
                    ->with('post', $post));
    }
    
}