<?php

class ArtistsController extends Controller
{
   private $id_artist;
   private $role;
   private $picture;
   private $first_name;
   private $last_name;
   private $birth_day;
   private $bio;

   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Artists();
   }

   /**
   *  Affiche la fiche Artiste
   */
   public function showArtist(int $id_artist)
   {
      // Affiche les Films de Artiste par Id
      $instanceMovies = new Movies();
      $movies = $instanceMovies->getAllMovies($id_artist);

      $artist   = $this->model->getArtist($id_artist);

      // Défini le role de l'artiste
      if($artist['role'] == 1){
         $role = 'Acteur';
      }else if($artist['role'] == 2){
         $role = 'Réalisateur';
      }else if($artist['role'] == 3){
         $role = 'Acteur & Réalisateur';
      }

      $pageTwig = 'Artists/showArtist.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'artist' => $artist,
         'movies' => $movies,
         'role' => $role
      ]);
   }

   /**
   *  Affiche la fiche Artiste
   */
   public function showAllArtists()
   {
      $artists  = $this->model->getAllArtists();
      $pageTwig = 'Artists/showAllArtists.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['artists' => $artists]);
   }

   /**
   *  Affiche les Artistes du Film
   */
   public function showByMovie()
   {
      $artists  = $this->model->getByFilm();
      $pageTwig = 'Artists/showByMovie.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["artists" => $artists]);
   }
}
