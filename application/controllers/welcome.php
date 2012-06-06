<?php

class Welcome_Controller extends Controller {

    function action_test($param1 = null, $param2 = null, $param3 = null)
    {
        return View::make('welcome/test')
            ->with('param1', $param1)
            ->with('param2', $param2);
    }
}