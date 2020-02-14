<?php
/********************************Controller dev par ANTHONY******************** */
class CommentsController extends Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->model = new Comments();
   }
   //delete comment
   public function deleteComment($id_movie)
   {
      $this->model->delComment($id_movie);
   }

   //render index
   public function index()
   {
      /*$OneComment = $this->model->getOneComment();
      $result = delComment($delId);
      $liaisonCom = $this->model->linkCommentWorks();//argument id movie
      $delete      = $this->model->delAllCommentFromMovie(1);//argument id movie*/
      $pageTwig = 'Comments/index.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["comments" => $comments, "OneComment" => $OneComment/*, "liaison" => $liaisonCom, "del" => $delete*/]);
   }
   //Supprime tous les commentaire liés à id_movie
   public function delAllComByMovie($id)
   {
      $tabCom = $this->model->linkCommentByMovie($id); 
      var_dump($tabCom);
      foreach ($tabCom as $k => $v) {
         $delId = $v['id_comment'];
         $this->model->delComment($delId);
      }
   }
   //Recherche tous les commentaires
   public function getAllCom()
   {
      $comments   = $this->model->getAllComments();
      $pageTwig = 'Comments/index.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["comments" => $comments]);
   }
   //Ajoute un commentaire
   public function addComment($id_movie)
   {
      session_start();
      //On affiche une alerte si un commentaire n'est pas complet après connexion 
      if(isset($_SESSION['alert'])) {
         echo $_SESSION['alert'];
         unset($_SESSION['alert']);
      }
      //Si aucune note celle-ci vaux null
      if(!isset($_POST['note'])){ $_POST['note'] = null; } else { $note = $_POST['note']; }
      //Si l'utilisateur est connecter
      if ($_SESSION['status'] === 2 || $_SESSION['status'] === 1) {

         if (!empty($_SESSION['tmpTitle']) && !empty($_SESSION['tmpComment']) && !empty($_SESSION['tmpNote'])) {

            $user = $this->model->getOneUser($_SESSION['utilisateur']);
            $id_user = $user[0]['id_user'];
   
            //insert le commentaire dans la table et retourne l'ID du commentaire
            $idComment = $this->model->addComment($id_user, $_SESSION['tmpTitle'], $_SESSION['tmpComment'], $_SESSION['tmpNote']);
            $this->model->addUsersComments($id_user, $idComment);
            //insert le commentaire dans la table movie_comments
            $postComment = $this->model->addMovieComments($id_movie, $idComment);
            unset($_SESSION['tmpTitle']);
            unset($_SESSION['tmpComment']);
            unset($_SESSION['tmpNote']);
            unset($_SESSION['idMovie']);
   
            header("Location: $this->baseUrl/Films/Film_$id_movie");
         }

         else if (!empty($_POST['title']) && !empty($_POST['controlText'])) {

            //On recherche l'id de l'utilisateur connecté
            $user = $this->model->getOneUser($_SESSION['utilisateur']);

            $id_user = $user[0]['id_user'];
            $title = $_POST['title'];
            $content = $_POST['controlText'];
            
            //insert le commentaire dans la table et retourne l'ID du commentaire
            $idComment = $this->model->addComment($id_user, $title, $content, $note);
            //insert dans la table users_comment l'ID du commentaire
            $this->model->addUsersComments($id_user, $idComment);
            //insert le commentaire dans la table movie_comments
            $this->model->addMovieComments($id_movie, $idComment);

            header("Location: $this->baseUrl/Films/Film_$id_movie");
         } else {
            //Si le commentaire est vide et publié on prépare une alerte
            $_SESSION['alert'] = "<script>alert(\"Votre commentaire n'a pas été publié car il est vide\")</script>";
            header("Location: $this->baseUrl/Films/Film_$id_movie");
         } 

      }
      //si l'utilisateur n'est pas connecter
      else if ($_SESSION['status'] === null) {
         if (isset($_POST)) {
            //On sauvegarde les éléments du commentaire avant redirection
            $_SESSION['tmpTitle'] = $_POST['title'];
            $_SESSION['tmpComment'] = $_POST['controlText'];
            $_SESSION['tmpNote'] = $_POST['note'];
            $_SESSION['idMovie'] = $id_movie;
         }

         //Initialise la variable $adressPage à la page courante
         $adressPage = "$this->baseUrl/Films/Film_$id_movie";
         $_SESSION['location'] = $adressPage;

         //On renvois l'utilisateur a la page login
         $pageTwig = 'Users/index.html.twig';
         $template = $this->twig->load($pageTwig);
         echo $template->render();
      } 
   }

   public function modifyComment($id_movie, $id_comment){
      
      $content = $_POST['controlText'];
      $result = $this->model->modifyComment($content, $id_comment);
      if($result == true) {
         echo "<script>alert(\"Votre commentaire a bien été modifier\")</script>";
      }
      header("Location: $this->baseUrl/Films/Film_$id_movie");
   }
   public function postAfterLogin(){
      //Si les 3 champs sont bien remplis on peut publier le commentaire
      if(!empty($_SESSION['tmpTitle']) && !empty($_SESSION['tmpComment']) && !empty($_SESSION['tmpNote'])) {
         $id_movie = $_SESSION['idMovie'];
         $this->addComment($id_movie);
      } else {
         $_SESSION['tmpComment'] = $_POST['ControlText'];
         var_dump($_SESSION['tmpComment']);
      }
   }
}
