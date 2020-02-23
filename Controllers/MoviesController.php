<?php

class MoviesController extends ArtsController
{
   private $picture;
   private $resume;
   private $time;

   public function __construct()
   {
      //$this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Movies();
   }

   /**
   *  Affiche tout les Films
   */
   public function showAllMovies()
   {
      $pageTwig = 'Movies/showAllMovies.html.twig';
      $template = $this->twig->load($pageTwig);
      $movies   = $this->model->getAllMovies();
      echo $template->render([
         'movies' => $movies,
         'status' => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche les films en fonction de la recherhce
   */
   public function search($search = null)
   {
      $slug = null;
      $notFound = null;

      // Recherche par nom
      if ($slug = "Recherche") {

         if (!empty($_POST['search'])) {

            $search = $_POST['search'];
            $search = $this->model->getBySearch($search);

         }else{
            $notFound = "Nous n'avons pas ce film !";
         }
      }

      $pageTwig = 'Movies/showAllMovies.html.twig';
      $template = $this->twig->load($pageTwig);
      $movies   = $this->model->getAllMovies();
      echo $template->render([
         'slug' => $slug,
         'movies' => $movies,
         'search' => $search,
         'notFound' => $notFound,
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche les films par genre
   */
   public function genre($style = null)
   {
      $slug = null;
      $notFound = null;

      // Recherche par Genre
      if ($slug = "Genre") {

         if (!empty($_POST['style'])) {

            $style = $_POST['style'];
            $style = $this->model->getByStyle($style);

         }else{
            $notFound = "Nous n'avons pas de films dans cette catégorie !";
         }
      }

      $pageTwig = 'Movies/showAllMovies.html.twig';
      $template = $this->twig->load($pageTwig);
      $movies   = $this->model->getAllMovies();
      echo $template->render([
         'slug' => $slug,
         'movies' => $movies,
         'style' => $style,
         'notFound' => $notFound,
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche un Film avec son Id
   */
   public function showMovie($id_movie) {

      // Affiche les Artistes liés a Id Film
      $instanceArtists = new Artists();
      //$artists = $instanceArtists->getByMovie($id_movie);
      $infos = $instanceArtists->getRole($id_movie);

      // Recherche les commentaire appartenant au film
      $instanceComments = new Comments();
      $comments = $instanceComments->linkCommentByMovie($id_movie);


      // On affiche une alerte si un commentaire vide a été publié
      $instanceUser = new Users();
      if(isset($_SESSION['alert'])) {
         echo $_SESSION['alert'];
         unset($_SESSION['alert']);
      }

      // On récupère l'id_user des commentaire et l'on recherche le pseudo leur appartenant
      for($i = 0; $i < count($comments) ; $i++){
         //On récupère l'id_user de tous les commentaire
         $id_user = $comments[$i]['id_user'];

         //On récupère le pseudo par l'id_user
         $user = $instanceUser->getOnePseudo($id_user);
         $mail = $instanceUser->getMailById($id_user);
         $comments[$i]['mail'] = $mail['mail'];
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
      if ($_SESSION['status'] === 2 ||   $_SESSION['status'] === 1) {

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
         echo $template->render([
            "movie"        => $movie,
            'infos'        => $infos,
            "comments"     => $comments,
            "user"         => $user,
            "datedujour"   => strftime("%A %d %B %Y"),
            "status"       => $_SESSION['status'],
            "tmpTitle"     => $_SESSION['tmpTitle'],
            "tmpComment"   => $_SESSION['tmpComment'],
            "tmpNote"      => $_SESSION['tmpNote'],
            "status"       => $_SESSION['status'],
            "userLogin"    => $_SESSION['utilisateur'],
            //'avatar'       => $_SESSION['avatar'],
            'alertMessage' => $_SESSION['receiveMessage']]);
      //Si ce n'était pas le cas on rends a la vus d'autre paramètres...
      } else {
         echo $template->render([
            "movie"        => $movie,
            'infos'        => $infos,
            "comments"     => $comments,
            "user"         => $user,
            "datedujour"   => strftime("%A %d %B %Y"),
            "status"       => $_SESSION['status'],
            "userLogin"    => $_SESSION['utilisateur'],
            //'avatar'       => $_SESSION['avatar'],
            'alertMessage' => $_SESSION['receiveMessage']]);
      }
   }
}
