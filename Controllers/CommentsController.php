<?php

/********************************Controller dev par ANTHONY******************** */
class CommentsController extends Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->model = new Comments();
   }

   /**
    *  render index
    */
   public function index()
   {
      $pageTwig = 'Comments/index.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['alertMessage' => $_SESSION['receiveMessage']]);
   }
   /**
    * Modification d'un commentairedeja publier
    */
   
   public function modifyComment($id_movie, $id_comment){
      var_dump($id_movie);
      var_dump($id_comment);
      $content = $_POST['controlText'];
      $result = $this->model->modifyComment($content, $id_comment);
      if($result == true) {
         echo "<script>alert(\"Votre commentaire a bien été modifier\")</script>";
      }
      header("Location: $this->baseUrl/Films/Film_$id_movie");
   }
   /**
    *  Affiche tous les commentaire d'un utilisateur
    */
   public function searchAllCommByUser()
   {
      $instanceHome = new HomeController();
      if (isset($_POST) && !empty($instanceHome->__getPOST('pseudo'))) {
         $pseudo = $instanceHome->__getPOST('pseudo');
         $this->refreshUserForCommByUser($pseudo);
      }
   }
   /**
    * Rafraichit la liste des comm par utilisateur (pseudo)
    */
   public function refreshUserForCommByUser($pseudo)
   {
      $comments = $this->model->searchAllCommByUser($pseudo);
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["comments" => $comments, 'slug' => 'Utilisateur', 'status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage'], 'pseudo' => $pseudo]);
   }
   /**
    *  Supprime un commentaire
    */
   public function deleteComment($id_comment, $id_movie, $pseudo, $id_user = null)
   {
      if ($id_user != null) {
         $this->model->delComment($id_comment);
         $this->refreshAfterDeteleCommByUser($id_user, $pseudo);
      } else {
         $this->model->delComment($id_comment);
         header("Location: $this->baseUrl/Films/Film_$id_movie");
      }
   }

   /**
    * Rafraichit la liste après suppression
    */

   public function refreshAfterDeteleCommByUser($id_user, $pseudo)
   {
      $comments = $this->model->searchAllCommById($id_user);
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["comments" => $comments, 'slug' => 'Utilisateur', 'status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage'], 'pseudo' => $pseudo]);
   }
   /**
    *  Affiche tous les commentaire par titre de film
    */
   public function searchAllCommByTitleMovie()
   {
      if (isset($_POST) && !empty($_POST['title'])) {
         $this->refreshUserForCommByTitle($_POST['title']);
      }
   }
   /**
    * Rafraichit la liste des comm par titre de films
    */
    public function refreshUserForCommByTitle($title)
    {
       $movie = $this->model->searchAllCommByTitleMovie($title);
       $comments = $this->model->linkCommentByMovie($movie[0]['id_movie']);
       $pageTwig = 'Admin/admin.html.twig';
       $template = $this->twig->load($pageTwig);
       echo $template->render(["comments" => $comments, 'slug' => 'titleMovie', 'status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage'], 'title' => $title]);
    }
   /**
    *  Supprime tous les commentaire liés à id_movie
    */
   public function delAllComByMovie($id)
   {
      $tabCom = $this->model->linkCommentByMovie($id);
      foreach ($tabCom as $k => $v) {
         $delId = $v['id_comment'];
         $this->model->delComment($delId);
      }
   }

   /**
    *  Recherche tous les commentaires
    */
   public function getAllCom()
   {
      $comments   = $this->model->getAllComments();
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["comments" => $comments, 'slug' => 'Tous', 'status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }

   /**
    *  Mise en temporaire de la publication
    */
   public function temporaryFiles($id_movie)
   {

      $_SESSION['tabSession'] = [];
      $instanceHome = new HomeController();
      //On sauvegarde les éléments du commentaire avant redirection
      $instanceHome->__set('tmpTitle', $instanceHome->__getPOST('title'));
      $_SESSION['tabSession'][] = 'tmpTitle';
      $instanceHome->__set('tmpComment', $instanceHome->__getPOST('controlText'));
      $_SESSION['tabSession'][] = 'tmpComment';
      $instanceHome->__set('tmpNote', $instanceHome->__getPOST('note'));
      $_SESSION['tabSession'][] = 'tmpNote';
      $instanceHome->__set('location', "$this->baseUrl/Films/Film_$id_movie");
      $_SESSION['tabSession'][] = 'location';

      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['alertMessage' => $_SESSION['receiveMessage']]);
   }

   /**
    *  Ajoute un commentaire
    */
   public function addComment($id_movie)
   {

      $instanceHome = new HomeController();
      $instanceHome->__set('id_movie', $id_movie);

      //si location n'existe pas et que nous somme connecter on traite le commentaire
      if ($instanceHome->__get('status') === 2 || $instanceHome->__get('status') === 1) {
         $instanceUsers = new Users();
         $user = $instanceUsers->getOneUser($instanceHome->__get('utilisateur'));
         $id_user = $user['id_user'];

         //Si le post existe et que les champs ne sont pas vide
         if (isset($_POST) && !empty($instanceHome->__getPOST('title')) && !empty($instanceHome->__getPOST('controlText'))) {
            //On recherche l'id de l'utilisateur connecté
            $title = $instanceHome->__getPOST('title');
            $content = $instanceHome->__getPOST('controlText');
            $note = $instanceHome->__getPOST('note');

            //insert le commentaire dans la table et retourne l'ID du commentaire
            $idComment = $this->model->addComment($id_user, $title, $content, $note);
            //insert dans la table users_comment l'ID du commentaire appartenant a l'ID user
            $this->model->addUsersComments($id_user, $idComment);
            //insert le commentaire dans la table movie_comments
            $this->model->addMovieComments($id_movie, $idComment);

            header("Location: $this->baseUrl/Films/Film_$id_movie");
         }
         // Si le post existe mais que l'une ou l'autre information manque on les mets en temporaire
         else {
            // On affiche une alerte
            $instanceHome->__set('alert', "<script>alert(\"Votre commentaire n'a pas été publié car il est incomplet\")</script>");
            $instanceHome->__alert('alert');
            // On mets les éléments du commentaire en temporaire
            $this->temporaryFiles($id_movie);
         }
      } else {
         // On affiche une alerte
         $instanceHome->__set('alert', "<script>alert(\"Vous devez vous identifier vous publier.\")</script>");
         $instanceHome->__alert('alert');
         // On mets les éléments du commentaire en temporaire
         $this->temporaryFiles($id_movie);
      }
   }

   /**
    *
    */
   public function postAfterLogin()
   {

      $instanceHome = new HomeController();
      //Si l'un ou l'autre champ est vide on affiche une alerte

      if (empty($_SESSION['tmpTitle']) || empty($_SESSION['tmpComment'])) {
         // On affiche une alerte
         $instanceHome->__set('alert', "<script>alert(\"Votre commentaire n'a pas été publié car il est incomplet.Veuillez-vérifié.\")</script>");
         $instanceHome->__alert('alert');
         // On redirige sur la page du commentaire
         $location = $instanceHome->__get('location');
         header("Location: $location");
      } else {
         $instanceUsers = new Users();
         // On recherche les infos de l'utilisateur
         $user = $instanceUsers->getOneUser($instanceHome->__get('utilisateur'));
         // On récupère son id
         $id_user = $user['id_user'];

         // On insert le commentaire dans la table et retourne l'ID de celui-ci
         $idComment = $this->model->addComment($id_user, $instanceHome->__get('tmpTitle'), $instanceHome->__get('tmpComment'), $instanceHome->__get('tmpNote'));
         // On insert dans la table users_comment l'ID du commentaire appartenant a l'ID user
         $result = $this->model->addUsersComments($id_user, $idComment);
         if ($result === true) {
            // On insert le commentaire dans la table movie_comments
            $result = $this->model->addMovieComments($instanceHome->__get('id_movie'), $idComment);
            if ($result === true) {
               // On affiche une alerte
               $instanceHome->__set('alert', "<script>alert(\"Votre commentaire a bien été publié. Merci.\")</script>");
               $instanceHome->__alert('alert');
               // On efface toutes les super-global
               $location = $instanceHome->__get('location');
               $instanceHome->__unsetTab();
            }
         }
         if ($result === false) {
            // Si une erreur surviens lors de l'ajout du commentaire a la BDD
            $instanceHome->__set('alert', "<script>alert(\"Un erreur est survenu lors de la connexion a la base de données.Veuillez recommencer\")</script>");
            $instanceHome->__alert('alert');
            // On efface toutes les super-global
            $location = $instanceHome->__get('location');
         }
         header("Location: $location");
      }
   }
}
