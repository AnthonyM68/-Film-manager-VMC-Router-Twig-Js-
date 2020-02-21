<?php

class Movies extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();

   }

   /**
   *  Recupère tout les Films
   */
   public function getAllMovies()
   {
      $req = $this->pdo->prepare('SELECT * FROM movies');
      $req->execute();
      return $req->fetchAll();
   }

   /**
   *  Recupère un Film avec ID
   */
   public function getMovie($id_movie)
   {
      $req = $this->pdo->prepare(
         'SELECT *
         FROM movies
         WHERE movies.id_movie = ?
         AND movies.id_movie = movies.id_movie');
      $req->execute([$id_movie]);
      return $req->fetch();
   }

   /**
   *  Recherche un Film par nom
   */
   public function getBySearch($search)
   {
      $req = $this->pdo->prepare(
        'SELECT *
         FROM movies
         WHERE title
         LIKE "%'.$search.'%"');
      $req->execute();
      return $req->fetchAll();
   }

   /**
   *  Recherche un Film par genre
   */
   public function getByStyle($style)
   {
      $req = $this->pdo->prepare(
        'SELECT *
         FROM movies
         WHERE style
         LIKE "%'.$style.'%"');
      $req->execute();
      return $req->fetchAll();
   }
}
