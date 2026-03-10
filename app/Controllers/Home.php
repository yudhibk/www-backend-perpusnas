<?php

namespace App\Controllers;

class Home extends BaseController
{
    protected $password;
    public function index()
    {
        return view('welcome_message');
    }

	public function password($string = "P@ssw0rd")
    {
        $password = new \Myth\Auth\Password();
        echo $password->hash($string);
    }

}
