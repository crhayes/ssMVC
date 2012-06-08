<?php

class Index_Controller extends Controller {
    
    public $restful = false;
    
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
        
        /*DB::query("SELECT * FROM users WHERE email = :email")
            ->bind(':email', 'chris@twst.com')
            ->execute()
            ->fetch_all();*/
        
        echo URL::to_route('about');
        
        return View::make('index')
            ->with('another', 'I am testing')
            ->nest('test', View::make('test')
                ->with('param1', 'this is a param'));
    }
    
    function action_about($section = null)
    {        
        //$result = Database::instance()->query("SELECT * FROM users")->fetch_all('array');
        
        return View::make('about');;
    }
    
}