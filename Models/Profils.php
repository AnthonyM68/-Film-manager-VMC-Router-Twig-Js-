<?php

class Profils extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }
   public function sendMessage($expeditor, $pseudo,  $title, $message){
      $req = $this->pdo->prepare('INSERT INTO messages (expeditor, pseudo,  title, message)
      VALUE (?, ?, ?, ?)');
      return $req->execute([$expeditor, $pseudo,  $title, $message]);
   }
   public function receiveMessage($pseudo){
      $req = $this->pdo->prepare('SELECT * FROM messages WHERE pseudo = ?');
      $req->execute([$pseudo]);
      return $req->fetchAll();
   }
   public function delMessage($id_message)
   {
      $req = $this->pdo->prepare('DELETE FROM messages WHERE id_message = ?');
      return $req->execute([$id_message]);
   }
   public function delAllMessageByUser($pseudo)
   {
      $req = $this->pdo->prepare('DELETE FROM messages WHERE pseudo = ?');
      return $req->execute([$pseudo]);
   }
}
