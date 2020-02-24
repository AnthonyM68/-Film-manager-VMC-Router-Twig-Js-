<?php
class UsersController extends Controller
{
   public function __construct()
   {

      parent::__construct();
      $this->model = new Users();
   }

   /**
    *  Affichage du template pour $slug = null (formulaire de connexion)
    */
   public function connexion($slug = null)
   {
      //$slug est null
      $title = "Connexion";

      //si slug est défini et différent de "Inscription" et différent de "MotDePasseOublie" (en gros si l'utilisateur met nimp dans l'url) alors :
      if (isset($slug) && $slug !== "MotDePasseOublie"  && $slug !== "mailEnvoye" && $slug !== "ChangerMotDePasse" && $slug !== "Inscription") {
         //Affiche une erreur 303 dans la console :
         header("HTTP/1.0 303 Redirection");

         //Fait une redirection vers la page d'accueil :
         header("Location: $this->baseUrl");
      }

      //Affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);

      echo $template->render([
         'slug' => $slug,
         "title" => $title,
      ]);
   }

   /**
    *  Gestion de l'envoi du formulaire de connexion
    */
   public function login($slug = null)
   {
      $userInfo = null;
      $inputPseudo = null;
      $error = [];
      $userInfo = $this->model->checkLogin($_POST["pseudo"]);

      //pour chaque valeur d'input
      foreach ($_POST as $value) {

         //si la valeur de l'input est considérée comme vide
         if (empty($value)) {
            $error[] = " ";
            //Dans le formulaire si $error = " " alors le bord devient rouge


            //Sinon UN ou DES inputs sont remplis
         } else {
            $error[] = "";

            //si l'input pseudo est rempli par un pseudo connu
            if ($userInfo) {
               //rempli l'input par cette meme valeur
               $inputPseudo = $_POST['pseudo'];
               if (!empty($_POST['mdp'])) {
                  $hashMdp = $userInfo["mdp"];
                  // si le mot de passe est bon
                  if (password_verify($_POST['mdp'], $hashMdp)) {

                     /*********************ANTHONY*************************/
                     $instanceHome = new HomeController();
                     $instanceHome->__set('utilisateur', $_POST['pseudo']);

                     //on recherche si l'utilisateur connecté est administrateur
                     $this->checkAdministrator($instanceHome->__get('utilisateur'));

                     // Si location existe on redirige vers postAfterLogin()
                     if (isset($_SESSION['location'])) {
                        $instanceComments = new CommentsController();
                        $instanceComments->postAfterLogin();
                        /****************************************************/
                     } else {
                        //Sinon on redirige l'utilisateur sur la page d'accueil
                        if (!$instanceHome->__empty('utilisateur')) {
                           $instanceProfils = new ProfilsController();
                           $instanceProfils->checkMessage();
                           $pageTwig = 'index.html.twig';
                           $template = $this->twig->load($pageTwig);
                           echo $template->render(['status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage']]);
                           exit;
                        }
                     }
                  } else {
                     $error[1] = "Mot de passe incorrect";
                  }
               }
               //sinon l'input pseudo est rempli par un pseudo inconnu
            } else {
               //si le pseudo est inconnu mais défini par ""
               if ($_POST['pseudo'] == "") {
                  $error[] = "";
               } else {
                  $error[0] = 'Le pseudo : "' . $_POST['pseudo'] . '" est inconnu de la base de donnée';
               }
            }
         }
      }

      $title = "Connexion";

      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'title' => $title,
         'error' => $error,
         'inputPseudo' => $inputPseudo,
      ]);
   }


   /**
    *  Gestion de l'envoi du formulaire de Mot De Passe Oublié
    */
   public function forgetPassword($slug = "MotDePasseOublie")
   {
      //déclaration des variables
      $mail = null;
      $errorMail = null;
      $lienInscription = null;
      $insert = null;

      //formulaire soumit ?
      if (!empty($_POST)) {

         //si mail vide a l'envoi
         if (empty($_POST['mail'])) {
            $errorMail = " ";
         } else {
            $mail = $_POST['mail'];

            //mail existant ?
            $userMail = $this->model->mailExist($mail);

            //mail n'existe pas dans la bdd ?
            if ($userMail == false) {
               $lienInscription = " ";
               $errorMail = "Nous ne connaissons pas votre email, si vous n'avez pas encore de compte";
            } else {
               //on va checher le pseudo qui correspond au mail entré
               $pseudo = $this->model->recupPseudo($mail);
               $pseudo = $pseudo['pseudo'];

               //on créer md5 du mail
               $md5 = md5($mail);

               //on check si l'utilisateur a déja fait une demande de changer de mot de passe
               $checkUsersHash = $this->model->checkMailInUsersHash($mail);


               //si l'utilisateur n'a jamais fait de demande alors il n'existe pas donc on l'insert dans la table
               if ($checkUsersHash == false) {
                  $insert = $this->model->insertInUsersHash($mail, $md5, $pseudo);
               }
               //si l'utilisateur a déja fait une demande alors on lui update seulement son md5
               else {
                  $update = $this->model->updateInUsersHash($mail, $md5);
               }

               //si l'une des 2 requette a fonctionné alors on peut lui envoyer le mail
               if ($insert || $update) {
                  
                  //si on est en local
                  if ($_SERVER['SERVER_NAME'] === "localhost") {
                     $envoiMailLocal = $this->envoiMailLocal($pseudo, $md5);
                  } else {
                     echo "impossible de vous envoyer un mail car nous sommes en local";
                  }

                  if ($envoiMailLocal) {
                     $slug = "mailEnvoye";
                     header("Location: $this->baseUrl/$slug");
                  } else {
                     echo "Votre mail n'a pas pu être envoyé pour une raison inconnue";
                  }
               }
            }
         }
      }

      //affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'mail' => $mail,
         'errorMail' => $errorMail,
         'lienInscription' => $lienInscription,
         //'randomString' => $randomString,
      ]);
   }


   public function envoiMailLocal($pseudo, $md5)
   {
      

      //on charge Swiftmailer
      require_once('vendor/autoload.php');
      
      $sujet = "Mot de passe oublié";
      //on instancie un nouveau corps de document mail
      $message = (new Swift_Message($sujet));
      $img = $message->embed(Swift_Image::fromPath($this->baseUrl . '/assets/avatar/jatilogo.png'));
      
      //contenu mail
      $mailBody =
         '
         <html>

         <body>
         <div class="main">

            <div class="body">

               <h1 class="title">Bonjour ' . $pseudo . ' !</h1>
               
               <h3>Vous avez effectué une demande de réinitialisation de mot de passe.</h3>
               
               <h4>Cliquez sur le lien ci-dessous :</h4>
               
               <a class="link" href="http://localhost/allo_jati/ChangerMotDePasse/' . $md5 . '">Changer de mot de passe</a>
               <br>

               <img class="logo" src="'. $img .'" />

               <p>À bientôt chez ALLO JATI !</p>


            </div>
         </div>
         
         <style type="text/css">

            .main {
               margin: 20px;
               box-shadow: 0px 5px 20px rgba(153, 28, 59, 0.1);
               max-width: 100%;
            }

            .body {
               padding: 20px;
               text-align: center;
               font-family: "Gill Sans", sans-serif;
            }

            .title {
               color: #991c3b;
            }

            .link {
               padding: 3px;
               border-style: solid 1px;
               border
               border-color: #991c3b;
               color: #991c3b;
            }
            
            .logo {
               max-height:130px;
               max-width:130px;
            }

         </style>
         </body>
         </html>

      ';

      //on instancie une nouvelle méthode d'envois du mail
      $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 465))
         //Port 25 ou 465 selon votre configuration
         //identifiant et mot de passe pour votre swiftmailer
         ->setUsername('fb4412351e7042')
         ->setPassword('9377fb0dbcb0f8');
      //on instancie un nouveau mail
      $mailer = new Swift_Mailer($transport);
      //on instancie un nouveau corps de document mail
      $message
         ->setFrom(['Allo-jati@mail.fr'])
         ->setTo(['galli.johanna.g2@gmail.com'])
         ->setBody($mailBody, 'text/html');
      //on récupère et modifie le header du mail pour l'envois en HTML
      $type = $message->getHeaders()->get('Content-Type');
      $type->setValue('text/html');
      $type->setParameter('charset', 'utf-8');
      //On envois le mail en local
      $result = $mailer->send($message);
      return $result;
   }


   public function mailEnvoye($slug = "mailEnvoye")
   {
      $title = "Mail envoyé";

      //affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'title' => $title,
      ]);
   }

   /**
    * Fonction changer de mot de passe
    */
   public function updatePassword($slug = "ChangerMotDePasse")
   {
      //déclaration des variables
      $mdp = null;
      $goodMail = null;
      $inputMail = null;
      $hashMdp = null;
      $error = [];

      //recupération du md5 dans l'url
      $md5 = $this->model->returnUrl(43);
      //echo $md5;

      //cherche dans userhash la ligne qui contient le md5
      $checkmd5 = $this->model->checkMd5InUsersHash($md5);
      //on recupere le pseudo et le mail
      $pseudo = $checkmd5['pseudo'];
      $mail = $checkmd5['mail'];

      //pour chaque valeur d'input
      foreach ($_POST as $value) {

         //si la valeur de l'input est considérée comme vide
         if (empty($value)) {
            $error[] = " ";
            //Dans le formulaire si $error = " " alors le bord devient rouge

            //Sinon UN ou DES inputs sont remplis
         } else {

            //si l'input mail est rempli par le mail correct
            if ($mail == $_POST['mail']) {
               $error[] = "";
               //rempli l'input par cette meme valeur
               $inputMail = $_POST['mail'];
               //affiche un message vert qui indique de remplir un nouveau mot de passe
               $goodMail = "$pseudo... il faut entrer un nouveau mot de passe !";

               if (preg_match('`^([a-zA-Z0-9-_]{2,16})$`', $_POST['mdp'])) {
                  $hashMdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
               } else {
                  if ($_POST['mdp'] === "") {
                     $error[1] = " ";
                  } else {
                     $error[1] = "Le champ contient des caractères non valides";
                  }
               }
               //sinon l'input mail est rempli par un mail inconnu
            } else {
               //si le mail est inconnu mais défini par ""
               if ($_POST['mail'] == "") {
                  $error[0] = "";
               } else {
                  $error[0] = "$pseudo, ce n'est pas votre mail ...";
               }
            }
         }
      }

      if ($hashMdp && $inputMail) {
         $updateMdp = $this->model->updateMdp($inputMail, $hashMdp);
         if ($updateMdp == true) {


            $slug = "MotDePasseRéinitialisé";
            header("Location: $this->baseUrl/$slug");
         } else {
            var_dump("erreur lors du changement de mot de passe");
         }
      }

      $title = "Changer mot de passe";

      //affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'md5' => $md5,
         'title' => $title,
         'pseudo' => $pseudo,
         'inputMail' => $inputMail,
         'goodMail' => $goodMail,
         'mdp' => $mdp,
         'error' => $error,
      ]);
   }



   public function MotDePasseRéinitialisé($slug = "MotDePasseRéinitialisé")
   {
      $title = "Mot De Passe Réinitialisé";

      //affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'title' => $title,
      ]);
   }




   /**
    *  gestion de l'envoi du formulaire d'inscription
    */
   public function register($slug = "Inscription")
   {
      //déclaration des variables
      $mail = null;
      $pseudo = null;
      //$avatar = NULL;
      $generalError = NULL;
      $error = [];
      $inputMail = null;
      $inputPseudo = null;

      //pour chaque valeur d'input
      foreach ($_POST as $value) {
         //si la valeur de l'input est considérée comme vide
         if (empty($value)) {
            $error[] = " ";
            //Dans le formulaire si $error = " " alors le bord devient rouge

            //sinon UN ou DES inputs sont remplis
         } else {
            $error[] = "";
            /****MAIL****/
            $mail = $_POST['mail'];
            // mail correspond aux attentes ?
            if (preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#", $mail)) {
               $userMail = $this->model->mailExist($mail);

               // $user mail n'existe pas dans bdd
               if ($userMail === false) {
                  $inputMail = $mail;
               } else {
                  $error[0] = 'Le mail : "' . $_POST['mail'] . '" est existe déja';
               }
            } //si le pseudo est inconnu mais défini par ""
            else {
               if ($_POST['mail'] === "") {
                  $error[0] = " ";
               } else {
                  $error[0] = 'L\'adresse mail : "' . $_POST['mail'] . '" ne correspond pas aux attentes';
               }
            }
            /****PSEUDO****/
            $pseudo = $_POST['pseudo'];
            //pseudo correspond aux attentes ?
            if (preg_match('`^([a-zA-Z0-9-_]{2,16})$`', $pseudo)) {
               $userPseudo = $this->model->pseudoExist($pseudo);
               // le pseudo entré n'existe pas dans la bdd
               if ($userPseudo == false) {
                  $inputPseudo = $pseudo;
               } else {
                  //si le pseudo est inconnu mais défini par ""
                  $error[1] = 'Ce pseudo : "' . $pseudo . '" existe déjà';
               }
            } else {
               if ($_POST['pseudo'] === "") {
                  $error[1] = " ";
               } else {
                  $error[1] = "Le champ contient des caractères non valides.";
               }
            }
            /****MOT DE PASSE****/
            // mot de passe correspond aux attentes ?
            if (preg_match('`^([a-zA-Z0-9-_]{2,16})$`', $_POST['mdp'])) {
               if ($inputPseudo && $inputMail) {
                  $hashMdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
               } else {
                  if ($_POST === "") {
                     $error[] = " ";
                  }
               }
            } else {
               if ($_POST['mdp'] === "") {
                  $error[2] = " ";
               } else {
                  $error[2] = "Le champ contient des caractères non valides";
               }
            }
         }
      }


      /****INSERTION DE L'UTILISATEUR****/

      //tous les inputs sont définis ?
      if (isset($inputPseudo) && isset($hashMdp) && isset($inputMail)) {
         $avatar = "jatilogo.png";

         // insertion des données dans la bdd
         $insert = $this->model->insertUser($inputPseudo, $hashMdp, $inputMail, $avatar);

         if ($insert === true) {

            //redirection en étant connecté
            $instanceHome = new HomeController();
            $instanceHome->__set('utilisateur', $inputPseudo);

            //var_dump($slug);
            if (!$instanceHome->__empty('utilisateur')) {

               $_SESSION['status'] = 2;

               $slug = "Bienvenue";
               $title = "Bienvenue chez Allo Jati";

               //affichage
               $pageTwig = 'Users/login.html.twig';
               $template = $this->twig->load($pageTwig);
               echo $template->render([
                  'slug' => "Bienvenue",
                  'title' => $title,
                  'status' => $_SESSION['status'],
                  'user' => $_SESSION['utilisateur'],
               ]);
               exit;
            }
         } else {
            $generalError = "Malheureusement nous n'avons pas pu vous créer un compte";
         }
      }

      $title = "Inscription";

      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'title' => $title,
         'generalError' => $generalError,
         'error' => $error,
         'inputMail' => $inputMail,
         'inputPseudo' => $inputPseudo,
      ]);
   }

   /*
    *création de numéro aléatoire
    */
   public function random($max)
   {
      $string = "";
      $chaine = "abcdefghijklmnpqrstuvwxy";
      srand((float) microtime() * 1000000);
      for ($i = 0; $i < $max; $i++) {
         $string .= $chaine[rand() % strlen($chaine)];
      }
      return $string;
   }

   /********************************ANTHONY************************************/
   /**
    *  On déconnecte la SESSION
    */
   public function logout()
   {
      $instanceHome = new HomeController();
      $instanceHome->destroy();
      header("Location: $this->baseUrl");
   }

   /**
    *
    */
   public function checkAdministrator($pseudo)
   {
      $instanceHome = new HomeController();

      //On récupère l'id utilisateur par le pseudo
      $id_user = $this->model->getOneIdUser($pseudo);
      //On vérifie si l'id utilisateur est Admin
      $admin = $this->model->checkAdmin($id_user['id_user']);

      $instanceHome = new HomeController();

      if ($admin['admin'] == 1) {
         $_SESSION['status'] = 1;
      } else {
         $_SESSION['status'] = 2;
      }
   }
}
