<?php

class Suggestion extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   // 
   public function suggestion()
   {
      $req = $this->pdo->prepare('SELECT * FROM suggestion');
      $req->execute();
      return $req->fetchAll();
   }

   public function addSuggestion(){
      $req = $this->pdo->prepare('INSERT INTO suggestion (pseudo, email, textarea) 
      VALUE (:id_user, :title, :content, :note, :date)');
      $result = $req->execute();

      return $result;
   }

}
