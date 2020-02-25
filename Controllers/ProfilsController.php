<?php
   /**
    *          ANTHONY
    */
class ProfilsController extends Controller
{
   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Profils();
   }
   /**
    *  Rends la vus du controller
    */
   public function profil()
   {
      $this->searchAvatar();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alertMessage' => $_SESSION['receiveMessage'],
         'slug'         => 'accueil'
         ]);
   }
   /**
    * Recherche un avatar
    */
   public function searchAvatar()
   {
      $instanceUsers = new Users();
      $id_user = $instanceUsers->getOneIdUser($_SESSION['utilisateur']);
      $avatar = $instanceUsers->searchAvatar($id_user['id_user']);
      $avatar = $this->baseUrl . "/assets/avatar/" . $avatar['avatar'];
      $_SESSION['avatar'] = $avatar;
   }
   /**
    * Page Modifier un pseudo
    */
   public function modifyPseudo()
   {
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'pseudo', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }
   /**
    *  Modifie un pseudo
    */
   public function changePseudo()
   {
      $instanceUsers = new Users();
      if (isset($_POST) && !empty($_POST['pseudo'])) {
         $newPseudo = $_POST['pseudo'];
         $result = $instanceUsers->pseudoExist($newPseudo);
         if ($result === false) {
            $result = $instanceUsers->updatePseudo($newPseudo, $_SESSION['utilisateur']);
            if ($result === true) {
               $_SESSION['utilisateur'] = $newPseudo;
               $this->searchAvatar();
               $displayAlert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Votre pseudo a bien été modifié !</div>';
            }
         } else if ($result['pseudo'] === $newPseudo) {
            $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Ce pseudo existe déjà !</div>';
         } else {
            $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Ce pseudo existe déjà !</div>';
         }
      } else {
         $displayAlert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Le champ est vide !</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'pseudo', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alert'        => $displayAlert, 
         'alertMessage' => $_SESSION['receiveMessage']]);
   }
   /**
    *  Page modifier un avatar
    */
   public function modifyAvatar()
   {
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'avatar', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }
   /**
    *  Modifier un avatar
    */
   public function changeAvatar()
   {

      $instanceUsers = new Users();
      if (isset($_FILES['avatarUpload']) && $_FILES['avatarUpload']['error'] == 0) {
         if ($_FILES['avatarUpload']['size'] <= 2000000) {
            $infosfichier = pathinfo($_FILES['avatarUpload']['name']);
            $extension_upload = $infosfichier['extension'];
            $extension_upload = strtolower($extension_upload);
            $extensions_autorisees = array('bmp', 'jpg', 'jpeg', 'png', 'gif', 'tiff');
            if (in_array($extension_upload, $extensions_autorisees)) {
               $fileNameNew = uniqid('', true) . "." . $extension_upload;
               $fileDestination = "assets/avatar/$fileNameNew";
               $result = move_uploaded_file($_FILES['avatarUpload']['tmp_name'], $fileDestination);
               if ($result === true) {
                  $id_user = $instanceUsers->getOneIdUser($_SESSION['utilisateur']);
                  $actualAvatar = $instanceUsers->searchAvatar($id_user['id_user']);
                  $actualAvatar = $actualAvatar['avatar'];
                  $ifExist = "assets/avatar/$actualAvatar";
                  if (file_exists($ifExist) && $actualAvatar != "jatilogo.png") {
                     unlink($ifExist);
                  }
                  $result = $instanceUsers->modifyAvatar($fileNameNew, $_SESSION['utilisateur']);
                  if ($result === true) {
                     $_SESSION['avatar'] = "$this->baseUrl/assets/avatar/$fileNameNew";
                     $displayAlert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Avatar bien modifié !</div>';
                  } else {
                     $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Erreur de connexion avec la base données</div>';
                  }
               } else {
                  $displayAlert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Votre fichier n\'a pas été déplacé sur le serveur</div>';
               }
            } else {
               $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Votre fichier n\'est pas au bon format</div>';
            }
         } else {
            $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Votre fichier est de taille trop importante</div>';
         }
      } else {
         $displayAlert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Veuillez indiquer l\'emplacement de votre fichier</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'avatar', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alert'        => $displayAlert, 
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }
   /**
    *  Page modifier un mdp
    */
   public function modifymdp()
   {
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'mdp', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }
   /**
    *  Modifie un avatar
    */
   public function changemdp()
   {
      if (isset($_POST) && !empty($_POST['mdp'])) {
         $instanceUsers = new Users();
         $hashMdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
         $result = $instanceUsers->updateMdpByPseudo($_SESSION['utilisateur'], $hashMdp);
         if ($result === true) {
            $displayAlert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Votre mot de passe a bien été modifié</div>';
         } else {
            $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Erreur de connexion avec la base de données</div>';
         }
      } else {
         $displayAlert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Veuillez indiquer votre nouveau mot de passe</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'mdp', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alert'        => $displayAlert, 
         'alertMessage' => $_SESSION['receiveMessage']]);
   }
   /**
    *  Page Envoyé un message
    */
   public function sendMessage($slug = null)
   {
      date_default_timezone_set('Europe/Paris');
      setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
      $this->searchAvatar();
      if ($slug == null) {$pseudo = "";} else {$pseudo = $slug;}
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'Envoyer',
         'status'       => $_SESSION['status'],
         'user'         => $_SESSION['utilisateur'],
         'avatar'       => $_SESSION['avatar'],
         'pseudo'       => $pseudo,
         'alertMessage' => $_SESSION['receiveMessage'],
         'datedujour'   => strftime("%A %d %B %Y"),
      ]);
   }
   /**
    *  Enovyé un message a un utilisateur
    */
   public function sendMessageToUser($slug = null)
   {
      if (isset($_POST) && !empty($_POST['pseudoMessage']) && !empty($_POST['title']) && !empty($_POST['message'])) {
         $slug = $_POST['pseudoMessage'];
         $this->model->sendMessage($_SESSION['utilisateur'], $slug, $_POST['title'], $_POST['message']);
         $displayAlert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès</strong> Votre message a bien été envoyé !</div>';
      } else {
         $displayAlert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur!</strong> Veuillez vérifier le destinataire</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'Envoyer', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'alert'        => $displayAlert, 
         'alertMessage' => $_SESSION['receiveMessage']
         ]);
   }
   /**
    *  Vérifie les message reçus
    */
   public function checkMessage()
   {
      $message = $this->model->receiveMessage($_SESSION['utilisateur']);
      if (!empty($message)) {
         $_SESSION['receiveMessage'] = count($message);
      } else {
         $_SESSION['receiveMessage'] = 0;
      }
      return $message;
   }
   /**
    *  Affiche les message reçus
    */
   public function receiveMessage()
   {
      $message = $this->checkMessage();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'recevoir', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'message'      => $message, 
         'alertMessage' => $_SESSION['receiveMessage']]);
   }
   /**
    *  Efface les message
    */
   public function deleteMessage($id_message)
   {
      $this->model->delMessage($id_message);
      $message = $this->checkMessage();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'recevoir', 
         'status'       => $_SESSION['status'], 
         'user'         => $_SESSION['utilisateur'], 
         'avatar'       => $_SESSION['avatar'], 
         'message'      => $message, 
         'alertMessage' => $_SESSION['receiveMessage']]);
   }
   /**
    *  Supprime un compte
    */
   public function deleteAccount($slug = null)
   {
      $displayAlert = null;
      if (isset($_POST) && $slug != null) {

         $instanceUsers = new Users();
         $instanceComments = new Comments();
         $instanceHome = new HomeController();
         $instanceProfils = new Profils();

         // Recherche tous les commentaires d'un utilisateur dans la table Comments
         $id_user = $instanceUsers->getOneIdUser($_SESSION['utilisateur']);
         $result = $instanceComments->searchAllCommByUser($_SESSION['utilisateur']);
         foreach ($result as $key => $value) {
            // Supprimer tous les commentaires de la tables Comments
            $instanceComments->delComment($value['id_comment']);
            $instanceComments->deleteAllCommMovieById_comment($value['id_comment']);
            $instanceProfils->delAllMessageByUser($_SESSION['utilisateur']);
         }
         $instanceComments->deleteAllCommMovieByUser($id_user['id_user']);
         $result = $instanceUsers->deleteAccount($_SESSION['utilisateur']);
         if ($result === true) {
            $instanceHome->destroy();
            $instanceHome->startSession();
            $adress = "$this->baseUrl";
            $_SESSION['avatar'] = "$this->baseUrl/assets/avatar/jatilogo.png";
            $displayAlert = '<div class="alert alert-success text-center" id="alerte" data-adress="' . $adress . '"><strong>Succès...</strong> Votre compte a bien été supprimé</div>';
         } else {
            $displayAlert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Une erreur est survenue lors de la suppression de votre compte</div>';
         }
      }
     $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug'         => 'compte',
         'status'       => $_SESSION['status'],
         'user'         => $_SESSION['utilisateur'],
         'avatar'       => $_SESSION['avatar'],
         'alertMessage' => $_SESSION['receiveMessage'],
         'alert'        => $displayAlert
      ]);
   }
}
