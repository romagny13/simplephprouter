<?php

namespace App\Controller;


use App\Model\Article;
use App\Service\ArticleService;

class ArticleController extends Controller
{
    public $articleService;

    public function __construct()
    {
        $this->articleService = new ArticleService();
    }

    public function getAll()
    {
        $articles = $this->articleService->getAll();
        echo json_encode($articles);
    }

    public function getOne($id)
    {
        // tester autorisation
        // $this->Unauthorized();
        $article = $this->articleService->getOne($id);
        if ($article == null)
        {
            $this->NotFound();
        }

        $this->Json($article);
    }

    public function add()
    {
        // erreurs
        //        header("HTTP/1.1 400 Bad Request");
        //        $errors = array(
        //            'title' => 'empty Erreur 1',
        //            'content' => 'empty Erreur 2'
        //        );
        //        echo json_encode($errors);
        
        if($_SERVER["CONTENT_TYPE"] === "application/json"){
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            $result = $this->articleService->add($data);
            $this->Created($result);
        }
        else{
            $newArticle = new Article(0, $_POST['title'],$_POST['content']);
            $result = $this->articleService->add($newArticle);
            $this->Created($result);
        }
    }
    public function update($id)
    {
        if($_SERVER["CONTENT_TYPE"] === "application/json"){

            $toUpdate = $this->articleService->getOne($id);
            if ($toUpdate == null)
            {
                $this->NotFound();
            }

            $json = file_get_contents('php://input');
            $data = json_decode($json);
            $result = $this->articleService->update($id,$data);
            $this->Json($result);
        }
        else{

            $toUpdate = $this->articleService->getOne($id);
            if ($toUpdate == null)
            {
              $this->NotFound();
            }

            parse_str(file_get_contents('php://input'),$vars);
            $frombody = new Article($id,$vars['title'],$vars['content']);
            $result = $this->articleService->update($id,$frombody);
            $this->Json($result);
        }
    }

    public function delete($id){
        $this->articleService->delete($id);
        $this->NoContent();
    }

}