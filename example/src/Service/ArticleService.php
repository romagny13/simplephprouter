<?php

namespace App\Service;


use App\Model\Article;

class ArticleService
{
    public static $articles = null;
    public function __construct()
    {
        self::$articles = Array();
        self::$articles[0] = new Article(1, "Premier article", "Lorem ipsum ...");
        self::$articles[1] = new Article(2, "Second article", "Lorem ipsum atum ...");
    }
    public function getAll()
    {
        return self::$articles;
    }
    public function getOne($id)
    {
        $index = $this->getIndex($id);
        if($index !== -1){
            return self::$articles[$index];
        }
        return null;
    }
    public function add($article)
    {
        $article->id = self::getUniqueId();
        array_push(self::$articles, $article);
        return $article;
    }
    public function update($id,$article)
    {
        $index = $this->getIndex($id);
        if($index !== -1){
            $toUpdate = self::$articles[$index];
            $toUpdate->title = $article->title;
            $toUpdate->content = $article->content;
            return $toUpdate;
        }
        return null;
    }
    public function delete($id)
    {
        $index = $this->getIndex($id);
        if($index !== -1){
            unset(self::$articles[$index]);
            return 1;
        }
        return 0;
    }
    private function getIndex($id){
        for ($i = 0; $i <= count(self::$articles); $i++){
            if(self::$articles[$i]->id == $id){
                return $i;
            }
        }
        return -1;
    }
    private function getUniqueId()
    {
        $count = count(self::$articles) - 1;
        $lastId = self::$articles[$count]->id;
        return $lastId + 1;
    }
}