<?php

class Artists extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   /**
   *  Récupère les Artistes
   */
   public function getAllArtists()
   {
      $req = $this->pdo->prepare('SELECT * FROM artists');
      $req->execute();
      return $req->fetchAll();
   }

   /**
   *  Récupère un Artiste avec Id
   */
   public function getArtist($id_artist)
   {
      $req = $this->pdo->prepare(
        'SELECT *
         FROM artists
         WHERE artists.id_artist = ?
         AND artists.id_artist = artists.id_artist');
         $req->execute([$id_artist]);
         return $req->fetch();
   }

   /**
   *  Récupère les Artistes liés a Id du Film
   */
   public function getByMovie($id)
   {
      $req = $this->pdo->prepare(
        "SELECT artists.*
         FROM artists, artists_movies, movies
         WHERE movies.id_movie = ?
         AND movies.id_movie = artists_movies.id_movie
         AND artists.id_artist = artists_movies.id_artist"
      );
      $req->execute([$id]);
      return $req->fetchAll();
   }

   /**
   *  Recherche un Artiste par nom ou prénom
   */
   public function getBySearch($search)
   {
      $req = $this->pdo->prepare(
         'SELECT *
         FROM artists
         WHERE first_name
         LIKE "%'.$search.'%"
         OR last_name
         LIKE "%'.$search.'%"');
      $req->execute();
      return $req->fetchAll();
   }

   /**
   *  Récupère le Rôle de l'Artiste dans un Film avec l'ID du Film
   */
   public function getRole($id_movie)
   {
      $req = $this->pdo->prepare(
         "SELECT artists_movies.*, artists.first_name, artists.last_name
         FROM artists_movies, artists
         WHERE artists_movies.id_movie = ?
         AND artists_movies.id_artist = artists.id_artist");
      $req->execute([$id_movie]);
      return $req->fetchAll();
   }
}
