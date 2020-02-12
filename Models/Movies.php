 <?php

class Movies extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   // Recupère tout les Films
   public function getAllMovies()
   {
      $req = $this->pdo->prepare('SELECT * FROM movies');
      $req->execute();
      return $req->fetchAll();
   }

   // Recupère un Film avec ID
   public function getMovie($id_movie) {
      $req = $this->pdo->prepare(
        'SELECT *
         FROM movies
         WHERE movies.id_movie = ?
         AND movies.id_movie = movies.id_movie');
      $req->execute([$id_movie]);
      return $req->fetch();
   }

   // Recherche un Film
   public function getBySearch($query)
   {
      $req = $this->pdo->prepare(
        'SELECT title
         FROM movies
         WHERE title
         LIKE "%?%"');
      $req->execute([$query]);
      return $req->fetch();
   }
}
