<?php

class Comments extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }
   //recherche tous les commentaires de la BDD
   public function getAllComments()
   {
      $req = $this->pdo->prepare('SELECT * FROM comments');
      $resultat = $req->execute();

      return $req->fetchAll();
   }

   //recherche un commentaire par id_user
   public function getOneComment($id)
   {
      $req = $this->pdo->prepare('SELECT * FROM comments WHERE id_user= ?');
      $resultat = $req->execute([$id]);

      return $req->fetchAll();
   }
   //suppression commentaire par id_comment
   public function delComment($id_comment)
   {
      $req = $this->pdo->prepare('DELETE FROM comments 
      WHERE id_comment = ?');
      return $req->execute([$id_comment]);
   }
   //insert dans la table comments la publication 
   public function addComment($id_user, $title, $content, $note){
      $req = $this->pdo->prepare('INSERT INTO comments (id_user, title, content, note)
      VALUE (?, ?, ?, ?)');
      $req->execute([$id_user, $title, $content, $note]);
      //Récupère l'id de l'insertion dans la table
      return $this->pdo->lastInsertId();
   }

   public function modifyComment($content, $id_comment){
      $req = $this->pdo->prepare('UPDATE comments SET content=? WHERE id_comment=?');
      return $req->execute([$content, $id_comment]);
   }


   //insert dans la table users_comments le dernier commentaire publier
   public function addUsersComments($id_user, $id_comment){
      $req = $this->pdo->prepare('INSERT INTO users_comments (id_user, id_comment)
      VALUE (?, ?)');
      return $req->execute([$id_user, $id_comment]);
   }
   //recherche la liste des commentaire par id_movie
   public function linkCommentByMovie($id)
   {
      $req = $this->pdo->prepare(
         'SELECT comments.* 
         FROM movies, movie_comments, comments 
         WHERE movies.id_movie = ? 
         AND movies.id_movie = movie_comments.id_movie 
         AND comments.id_comment = movie_comments.id_comment');
      $req->execute([$id]);

      return $req->fetchAll();
   }
}
