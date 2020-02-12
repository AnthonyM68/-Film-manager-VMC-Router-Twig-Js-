<?php

class MoviesController extends ArtsController
{
   private $picture;
   private $resume;
   private $time;

   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Movies();
   }

   // Affiche tout les Films
   public function showAllMovies()
   {
      $movies   = $this->model->getAllMovies();
      $pageTwig = 'Movies/showAllMovies.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["movies" => $movies]);
   }

   // Affiche un Film avec son Id
   public function showMovie($id_movie) {

      // Affiche les Artistes liés a Id Film
      $instanceArtists = new Artists();
      $artists = $instanceArtists->getByMovie($id_movie);
/**************************************************************ANTHONY********************************************************************************** */
      //Instancie la class comments
      $instanceComments = new Comments();
      //Recherche les commentaire appartenant au film
      $comments = $instanceComments->linkCommentByMovie($id_movie);






      /*Pour une raison inconue cette vus est apparu apres le pull et fais beuger la suite

      $movie = $this->model->getMovie($id_movie);
      $pageTwig = 'Movies/showMovie.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["movie" => $movie, "artists" => $artists, "comments" => $comments]);*/






      $instanceUser = new User();
      session_start();
      //On affiche une alerte si un commentaire vide a été publié
      if(isset($_SESSION['alert'])) {
         echo $_SESSION['alert'];
         unset($_SESSION['alert']);
      }
      //On récupère l'id_user des commentaire et l'on recherche le pseudo leur appartenant
      for($i = 0; $i < count($comments) ; $i++){
         //On récupère l'id_user de tous les commentaire
         $id_user = $comments[$i]['id_user'];
         //On récupère le pseudo par l'id_user
         $user = $instanceUser->getOnePseudo($id_user);
         //On affecte le pseudo a la place de l'id_user
         $comments[$i]['id_user'] = $user['pseudo'];
         //On recherche l'avatar appartenant a l'user qui depose un commentaire
         $avatar = $instanceUser->searchAvatar($id_user);
         //On ajoute au tableau une donnée, celle de l'avatar du dépositaire du commentaire
         $comments[$i]['avatar'] = $this->baseUrl . "/assets/avatar/" .$avatar['avatar'];
      }
      //Défini la date local en europe pour un simple affichage de la date de dépôt du commentaire
      date_default_timezone_set('Europe/Paris');
      setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

      $user = null;
      //Affiche l'utilisateur connecté ou celle de l'admin
      if (isset($_SESSION['status']) && ($_SESSION['status'] === 2 || $_SESSION['status'] === 1)) {
         $user = $instanceUser->getOneUser($_SESSION['utilisateur']);
      } else {
         //si le visiteur n'est pas administrateur ou identifier alors on lui indique qu'il doit s'identifier
         $user = "Vous devez être connecté pour déposer un commentaire";
      }
      //On rends la vus au controller
      $movie = $this->model->getMovie($id_movie);
      $pageTwig = 'Movies/showMovie.html.twig';
      $template = $this->twig->load($pageTwig);
      //Si l'utilisateur non identifié avait déjà déposer un commentaire...
      if(isset($_SESSION['tmpComment'])) {
         echo $template->render(["movie" => $movie, "artists" => $artists, "comments" => $comments, "user" => $user, "datedujour" => strftime("%A %d %B %Y"), "status" => $_SESSION['status'], "tmpTitle" => $_SESSION['tmpTitle'], "tmpComment" => $_SESSION['tmpComment'], "tmpNote" => $_SESSION['tmpNote'], "status" => $_SESSION['status'], "userLogin" => $_SESSION['utilisateur']]);
      //Si ce n'était pas le cas on rends a la vus d'autre paramètres...
      } else {
         echo $template->render(["movie" => $movie, "artists" => $artists, "comments" => $comments, "user" => $user, "datedujour" => strftime("%A %d %B %Y"), "status" => $_SESSION['status'], "userLogin" => $_SESSION['utilisateur']]);
      }
   }
}
