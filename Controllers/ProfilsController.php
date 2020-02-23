<?php

class ProfilsController extends Controller
{
   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Profils();
   }

   public function profil()
   {
      $this->searchAvatar();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function searchAvatar()
   {
      $instanceUsers = new Users();
      $id_user = $instanceUsers->getOneIdUser($_SESSION['utilisateur']);
      $avatar = $instanceUsers->searchAvatar($id_user['id_user']);
      $avatar = $this->baseUrl . "/assets/avatar/" . $avatar['avatar'];
      $_SESSION['avatar'] = $avatar;
   }
   public function modifyPseudo()
   {
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'pseudo', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }

   public function changePseudo()
   {
      $instanceUsers = new Users();
      if (isset($_POST) && !empty($_POST['pseudo'])) {

         //$_POST['pseudo'] = $_SESSION['utilisateur'];

         $newPseudo = $_POST['pseudo'];

         $result = $instanceUsers->pseudoExist($newPseudo);
         if ($result === false) {
            $result = $instanceUsers->updatePseudo($newPseudo, $_SESSION['utilisateur']);
            if ($result === true) {
               $_SESSION['utilisateur'] = $newPseudo;
               $this->searchAvatar();
               $alert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Votre pseudo a bien été modifier!</div>';
            }
         } else if ($result['pseudo'] === $newPseudo) {
            $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Ce pseudo existe déjà!</div>';
         }
      } else {
         $alert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Le champ est vide!</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'pseudo', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alert' => $alert, 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function modifyAvatar()
   {

      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'avatar', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }
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
                     $alert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Avatar bien modifié!</div>';
                  } else {
                     $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Erreur de connection avec la base données</div>';
                  }
               } else {
                  $alert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Votre fichier n\'a pas été déplacer sur le serveur</div>';
               }
            } else {
               $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Votre fichier n\'est pas au bon format</div>';
            }
         } else {
            $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Votre fichier est de tailler trop importante</div>';
         }
      } else {
         $alert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Veuillez indiqué l\'emplacement de votre fichier</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'avatar', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alert' => $alert, 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function modifymdp()
   {
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'mdp', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function changemdp()
   {
      if (isset($_POST) && !empty($_POST['mdp'])) {

         $instanceUsers = new Users();
         $hashMdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
         $result = $instanceUsers->updateMdp($_SESSION['utilisateur'], $hashMdp);
         if ($result === true) {
            $alert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès...</strong> Votre mot de passe a bien été modifié</div>';
         } else {
            $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur...</strong> Erreur de connection avec la base de données</div>';
         }
      } else {
         $alert = '<div class="alert alert-danger text-center" id="alerte"><strong>Erreur...</strong> Veuillez indiqué votre nouveau mote de passe</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'mdp', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alert' => $alert, 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function sendMessage($slug = null)
   {
      date_default_timezone_set('Europe/Paris');
      setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
      if ($slug == null) {$pseudo = "";} else {$pseudo = $slug;}
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);

      echo $template->render(['slug' => 'Envoyer', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'pseudo' => $pseudo, 'alertMessage' => $_SESSION['receiveMessage'], "datedujour" => strftime("%A %d %B %Y"),]);
   }
   public function sendMessageToUser($slug = null)
   {
      if (isset($_POST) && !empty($_POST['pseudoMessage']) && !empty($_POST['title']) && !empty($_POST['message'])) {
         $slug = $_POST['pseudoMessage'];
         $this->model->sendMessage($_SESSION['utilisateur'], $slug, $_POST['title'], $_POST['message']);
         $alert = '<div class="alert alert-success text-center" id="alerte"><strong>Succès</strong> Votre message a bien été envoyer!</div>';
      } else {
         $alert = '<div class="alert alert-warning text-center" id="alerte"><strong>Erreur!</strong> Veuillez vérifier le Destinataire</div>';
      }
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'Envoyer', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alert' => $alert, 'alertMessage' => $_SESSION['receiveMessage']]);
   }

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
   public function receiveMessage()
   {
      $message = $this->checkMessage();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'recevoir', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'message' => $message, 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function deleteMessage($id_message)
   {
      $this->model->delMessage($id_message);
      $message = $this->checkMessage();
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'recevoir', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'message' => $message, 'alertMessage' => $_SESSION['receiveMessage']]);
   }
   public function deleteAccount($slug = null)
   {
      if (isset($_POST) && $slug != null) {
         
         $instanceUsers = new Users();
         $instanceComments = new Comments();
         $instanceProfils = new Profils();

         $id_user = $instanceUsers->getOneIdUser($_SESSION['utilisateur']);

         $result = $instanceComments->deleteAllCommMovieByUser($id_user['id_user']);
         var_dump($result);
         if($result === true){
            $result = $instanceProfils->delAllMessageByUser($_SESSION['utilisateur']);
            var_dump($result);

         }


         //$result = $instanceUsers->deleteAccount($_SESSION['utilisateur']);

         if ($result === true) {
            $alert = 'good';
         } else {
            $alert = 'error';
         }
      } else {
         $alert = null;
      }

      
      $pageTwig = 'Profil/profil.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => 'compte', 'status' => $_SESSION['status'], 'user' => $_SESSION['utilisateur'], 'avatar' => $_SESSION['avatar'], 'alertMessage' => $_SESSION['receiveMessage'], 'alert' => $alert]);
   }
}
