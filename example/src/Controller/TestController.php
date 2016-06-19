<?php

namespace App\Controller;

class TestController
{
    public function index(){
        echo 'Dans index du controleur';
    }


    public function withparam($id){
        echo 'Dans controleur avec id : '.$id;
    }

}