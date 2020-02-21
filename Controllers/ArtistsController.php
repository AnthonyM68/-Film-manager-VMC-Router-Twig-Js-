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

      $artist = $this->model->getArtist($id_artist);

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
         'role' => $role,
         'status' => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }
   /**
   *  Affiche les artistes
   */
   public function showAllArtists()
   {
      $artists  = $this->model->getAllArtists();
      $pageTwig = 'Artists/showAllArtists.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'artists' => $artists,
         'status' => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }

   /**
   *  Affiche les Artistes du Film
   */
   public function showByMovie()
   {
      $artists  = $this->model->getByFilm();
      $pageTwig = 'Artists/showByMovie.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         "artists" => $artists,
         'status' => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche les films en fonction de la recherhce
   */
   public function search($search = null)
   {
      $slug = "Recherche";
      $notFound = null;

      if (isset($_POST['search']) && !empty($_POST['search'])) {

         // Affiche la recherche Film
         $search = $_POST['search'];
         $search = $this->model->getBySearch($search);

      }else{
         $notFound = "L'artiste recherché n'est pas répertorié, mais vous pouvez nous envoyer des suggestion via le formulaire !";
      }

      $pageTwig = 'Artists/showAllArtists.html.twig';
      $template = $this->twig->load($pageTwig);
      $artists  = $this->model->getAllArtists();
      echo $template->render([
         'slug' => $slug,
         'artists' => $artists,
         'search' => $search,
         'notFound' => $notFound,
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }
}
