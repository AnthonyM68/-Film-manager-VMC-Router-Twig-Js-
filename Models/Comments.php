<?php

class Comments extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   /**
    *  recherche tous les commentaires de la BDD
    */
   public function getAllComments()
   {
      $req = $this->pdo->prepare('SELECT * FROM comments ORDER BY `date` DESC');
      $req->execute();

      return $req->fetchAll();
   }

   /**
    *  recherche un commentaire par id_user dans la table comment
    */
   public function getOneComment($id_user)
   {
      $req = $this->pdo->prepare('SELECT * FROM comments WHERE id_user= ? ORDER BY `date` DESC');
      $req->execute([$id_user]);

      return $req->fetchAll();
   }

   /**
    *  recherche tous les commentaires d'un user par son id_user
    */
   public function searchAllCommById($id_user)
   {
      $req = $this->pdo->prepare('SELECT * FROM comments WHERE id_user= ? ORDER BY `date` DESC');
      $req->execute([$id_user]);
      return $req->fetchAll();
   }

   /**
    *  recherche tous les commentaires d'un user par son pseudo 
    */
   public function searchAllCommByUser($pseudo)
   {
      $instanceUsers = new Users();
      $user = $instanceUsers->getOneUser($pseudo);
      $id_user = $user['id_user'];
      $req = $this->pdo->prepare('SELECT * FROM comments WHERE id_user = ? ORDER BY `date` DESC');
      $req->execute([$id_user]);

      return $req->fetchAll();
   }
   /**
    *  recherche tous les commentaires par titre de film
    */
   public function searchAllCommByTitleMovie($title)
   {

      $req = $this->pdo->prepare('SELECT id_movie FROM movies WHERE title = ? ORDER BY `date` DESC');
      $req->execute([$title]);
      return $req->fetchAll();
   }

   /**
    *  suppression commentaire par id_comment
    */
   public function delComment($id_comment)
   {
      $req = $this->pdo->prepare('DELETE FROM comments WHERE id_comment = ?');
      return $req->execute([$id_comment]);
   }
   /**
    *   Supprime tous les commentaire d'un utilisateur
    */
   public function deleteAllCommMovieByUser($id_user)
   {

      $req = $this->pdo->prepare('DELETE FROM users_comments WHERE id_user =?');
      return $req->execute([$id_user]);
   }

   public function deleteAllCommMovieById_comment($id_comment)
   {
      $req = $this->pdo->prepare('DELETE FROM movie_comments WHERE id_comment =?');
      return $req->execute([$id_comment]);
   }








   /**
    *  insert dans la table comments la publication
    */
   public function addComment($id_user, $title, $content, $note)
   {
      $req = $this->pdo->prepare('INSERT INTO comments (id_user, title, content, note)
      VALUE (?, ?, ?, ?)');
      $req->execute([$id_user, $title, $content, $note]);
      //Récupère l'id de l'insertion dans la table
      return $this->pdo->lastInsertId();
   }

   /**
    *  edite un commentaire
    */
   public function modifyComment($content, $id_comment)
   {
      $req = $this->pdo->prepare('UPDATE comments SET content=? WHERE id_comment=?');
      return $req->execute([$content, $id_comment]);
   }

   /**
    *  insert dans la table users_comments le dernier commentaire publié
    */
   public function addUsersComments($id_user, $id_comment)
   {
      $req = $this->pdo->prepare('INSERT INTO users_comments (id_user, id_comment)
      VALUE (?, ?)');
      return $req->execute([$id_user, $id_comment]);
   }

   /**
    *   insert dans la table users_comments le dernier commentaire publié
    */
   public function addMovieComments($id_movie, $id_comment)
   {
      $req = $this->pdo->prepare('INSERT INTO movie_comments (id_movie, id_comment)
      VALUE (?, ?)');
      return $req->execute([$id_movie, $id_comment]);
   }



   /**
    *  recherche la liste des commentaire par id_movie
    */
   public function linkCommentByMovie($id)
   {

      $req = $this->pdo->prepare(
         'SELECT comments.*
         FROM movies, movie_comments, comments
         WHERE movies.id_movie = ?
         AND movies.id_movie = movie_comments.id_movie
         AND comments.id_comment = movie_comments.id_comment
         ORDER BY `date` DESC'
      );
      $req->execute([$id]);

      return $req->fetchAll();
   }
}
