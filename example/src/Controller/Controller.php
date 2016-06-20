<?php

namespace App\Controller;


class Controller
{
    protected function NotFound(){
        header("HTTP/1.1 404 Not found");
        exit;
    }

    protected function Created($result){
        header("HTTP/1.1 201 Created");
        echo json_encode($result);
    }

    protected function NoContent(){
        header("HTTP/1.1 204 No Content");
        exit;
    }

    protected function Unauthorized(){
        header("HTTP/1.1 401 Unauthorized");
        exit;
    }

    protected function BadRequest(){
        header("HTTP/1.1 400 Bad Request");
        exit;
    }

    protected function Json($result){
        echo json_encode($result);
    }

}