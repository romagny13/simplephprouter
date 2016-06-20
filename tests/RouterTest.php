<?php


class RouterTest extends PHPUnit_Framework_TestCase
{
    public static $result = "";

    function getRouter(){
        $config = ['controllers_namespace' => '\\App\\Controller'];
        $router = new \PHPRouter\Router($config);
        return $router;
    }

    function testGet(){
        $router = $this->getRouter();

        $router->get('/',function(){
            RouterTest::$result = "ok";
        });
        $router->run("/","GET");

        $this->assertNotEmpty(RouterTest::$result);
    }

    function testGetWithParameters(){
        $router = $this->getRouter();

        $router->get('/articles/:a/detail/:b/:c',function($a,$b,$c){
            $this->assertEquals(10,$a);
            $this->assertEquals(20,$b);
            $this->assertEquals(30,$c);
        });
        $router->run("/articles/10/detail/20/30","GET");
    }

    function testGetWithParametersAndRegexFailed(){
        $router = $this->getRouter();

        $router->get('/articles/:a/detail/:b/:c',function($a,$b,$c){

        })->with("b","[a-zA-Z]+")->with("c","[a-z\\-0-9]+");
        $success = $router->run("/articles/10/detail/123/product-10","GET");
        $this->assertFalse($success);
    }

    function testGetWithParametersAndRegexSuccess(){
        $router = $this->getRouter();

        $router->get('/articles/:a/detail/:b/:c',function($a,$b,$c){
            $this->assertEquals(10,$a);
            $this->assertEquals("message",$b);
            $this->assertEquals("product-10",$c);
        })->with("b","[a-zA-Z]+")->with("c","[a-z\\-0-9]+");
        $success = $router->run("/articles/10/detail/message/product-10","GET");
        $this->assertTrue($success);
    }

}