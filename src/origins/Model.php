<?php

namespace Dara\Origins;


use dara\potatoorm;
use Dotenv;


class Model extends basemodel
{
    public function loadEnv()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__.'/../..');
        $dotenv->load();
    }

}