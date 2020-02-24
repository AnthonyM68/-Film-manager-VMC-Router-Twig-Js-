<?php

class AdminController extends Controller
{
   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Admin();
   }

   /**
   *  Page admin de base
   */
   public function admin()
   {
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }

   /**
   *  Affiche la liste de tout les Utilisateurs
   */
   public function editUsers()
   {
      $slug = 'Liste_Utilisateurs';

      $instanceUsers = new Users();
      $users = $instanceUsers->getAllUsers();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => $slug,
         'users'        => $users,
         'status'       => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche la liste de tout les Films
   */
   public function editMovies()
   {
      $slug = 'Liste_Films';

      $instanceMovies = new Movies();
      $movies = $instanceMovies->getAllMovies();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => $slug,
         'movies'       => $movies,
         'status'       => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Affiche la liste de tout les Artistes
   */
   public function editArtists()
   {
      $slug = 'Liste_Artistes';

      $instanceArtists = new Artists();
      $artists = $instanceArtists->getAllArtists();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => $slug,
         'artists'      => $artists,
         'status'       => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Ajoute un nouveau Film
   */
   public function addMovie()
   {
      $slug = 'Ajout_Film';
      $info = $error = $success= null;
      $title = $year = $time = $picture = $style = $resume = $trailer = null;
      $pregYear = '/^[0-9]{4}$/m';
      $pregTime = '/^([01]?[0-9]|2[0-3])\:+[0-5][0-9]$/';
      $pregUrl  = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';


      if (empty($_POST)) {
         $info = "Veuillez remplir tout les champs !";
      }else {

         if (empty($_POST['resume'])) {
            $error = "Résumé non renseigné !";
         }else{
            $resume = $_POST['resume'];
         }

         if (empty($_POST['trailer'])) {
            $error = "Trailer non renseigné !";
         }else{
            $trailer = $_POST['trailer'];
         }

         if (empty($_POST['style'])) {
            $error = "Style non renseigné !";
         }else{
            $style = $_POST['style'];
         }

         if (empty($_POST['picture']) || !preg_match($pregUrl,$_POST['picture'])) {
            $error = "Format url non valide !";
         }else{
            $picture = $_POST['picture'];
         }

         if (empty($_POST['time']) || !preg_match($pregTime,$_POST['time'])) {
            $error = "Format durée non valide (H:MM)!";
         }else{
            $time = $_POST['time'];
         }

         if (empty($_POST['year']) || !preg_match($pregYear,$_POST['year'])) {
            $error = "Format date non valide (YYYY) !";
         }else{
            $year = $_POST['year'];
         }

         if (empty($_POST['title'])) {
            $error = "Titre non renseigné !";
         }else{
            $title = $_POST['title'];
         }

         if (!empty($_POST['title']) && !empty($_POST['year']) && !empty($_POST['time']) && !empty($_POST['picture']) && !empty($_POST['style']) && !empty($_POST['resume']) && !empty($_POST['trailer'])) {
            $success = "Votre ajout à bien été effectué !";
         }
      }

      $this->model->addMovie($picture,$title,$year,$style,$resume,$trailer,$time);
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => $slug,
         'title'        => $title,
         'year'         => $year,
         'time'         => $time,
         'picture'      => $picture,
         'style'        => $style,
         'resume'       => $resume,
         'trailer'      => $trailer,
         'info'         => $info,
         'error'        => $error,
         'success'      => $success,
         'status'       => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage']
      ]);
   }

   /**
   *  Ajoute un nouvel Artiste
   */
   public function addArtist()
   {
      $slug = 'Ajout_Artiste';
      $info = $error = $success= null;
      $picture = $first_name = $last_name = $birth_day = $bio = $role = null;
      $pregUrl = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';


      if (empty($_POST)) {
         $info = "Veuillez remplir tout les champs !";
      }else {

         if (empty($_POST['bio'])) {
            $error = "Renseignez une bio !";
         }else{
            $bio = $_POST['bio'];
         }

         if (empty($_POST['role'])) {
            $error = "Renseignez une role !";
         }else{
            $role = $_POST['role'];
         }

         if (empty($_POST['picture'])) {
            $error = "Renseignez une url !";
         }else{
            $picture = $_POST['picture'];
         }

         if (empty($_POST['birth_day'])) {
            $error = "Renseignez une date de naissance !";
         }else{
            $birth_day = $_POST['birth_day'];
         }

         if (empty($_POST['last_name'])) {
            $error = "Renseignez un nom !";
         }else{
            $last_name = $_POST['last_name'];
         }

         if (empty($_POST['first_name'])) {
            $error = "Renseignez un prénom !";
         }else{
            $first_name = $_POST['first_name'];
         }

         if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['birth_day']) && !empty($_POST['picture']) && !empty($_POST['bio']) && !empty($_POST['role'])) {
            $success = "Votre ajout à bien été effectué !";
         }
      }


      $this->model->addArtist($picture,$first_name,$last_name,$birth_day,$bio,$role);
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => $slug,
         'status'       => $_SESSION['status'],
         'alertMessage' => $_SESSION['receiveMessage'],
         'info'         => $info,
         'error'        => $error,
         'success'      => $success,
         'picture'      => $picture,
         'first_name'   => $first_name,
         'last_name'    => $last_name,
         'birth_day'    => $birth_day,
         'bio'          => $bio,
         'role'         => $role
      ]);
   }

   /**
   *  Associe un Artiste et son Rôle dans un Film
   */
   public function association()
   {
      $slug = "Association";
      $info = $error = $success= null;

      $id_artist = $id_movie = $role = null;

      if (empty($_POST)) {
         $info = "Veuillez faire une association !";
      }else {

         if (empty($_POST['id_artist'])) {
            $error = "Selectionnez un artiste !";
         }else{
            $id_artist = $_POST['id_artist'];
         }

         if (empty($_POST['id_movie'])) {
            $error = "Selectionnez un film !";
         }else{
            $id_movie = $_POST['id_movie'];
         }

         if (empty($_POST['role'])) {
            $error = "Selectionnez son rôle !";
         }else{
            $role = $_POST['role'];
         }

         if (isset($_POST['id_artist']) && isset($_POST['id_movie']) && isset($_POST['role'])) {
            $success = "Votre association à bien été effectué !";
         }
      }

      $instanceArtists = new Artists();
      $artists = $instanceArtists->getAllArtists();

      $instanceMovies = new Movies();
      $movies = $instanceMovies->getAllMovies();

      $this->model->association($id_artist, $id_movie, $role);

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'status'    => $_SESSION['status'],
         'slug'      => $slug,
         'info'      => $info,
         'error'     => $error,
         'success'   => $success,
         'artists'   => $artists,
         'movies'    => $movies,
         'id_artist' => $id_artist,
         'id_movie'  => $id_movie,
         'role'      => $role,
      ]);
   }
}
