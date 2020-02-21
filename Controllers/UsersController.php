<?php
class UsersController extends Controller
{
   public function __construct()
   {
      //$this->twig = parent::getTwig();
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
      $errorMail = "";

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
               $errorMail = "$mail n'est pas connu";
            } else {
               //on va checher le pseudo qui correspond au mail entré
               $pseudo = $this->model->recupPseudo($mail);
               //var_dump($pseudo['pseudo']);
               $pseudo = $pseudo['pseudo'];


               //on créer hash du pseudo
               $hash = md5($mail);

               //on insert le mail le hash et le pseudo dans la table Users_Hash
               $this->model->insertInUsersHash($hash, $mail, $pseudo);

               //contenu mail
               $sujet = "Mot de passe oublié";
               $mailBody = "<h2>Bonjour $pseudo ! </h2><p>Vous avez demandé à changer de mot de passe.</p><br><a href='http://localhost/allo_jati/ChangerMotDePasse/$hash'>Changer de mot de passe</a>";

               //si on est en local
               if ($_SERVER['SERVER_NAME'] === "localhost") {
                  //on charge Swiftmailer
                  require_once('vendor/autoload.php');

                  //on instancie une nouvelle méthode d'envois du mail
                  $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 465))
                     //Port 25 ou 465 selon votre configuration
                     //identifiant et mote de passe pour votre swiftmailer
                     ->setUsername('fb4412351e7042')
                     ->setPassword('9377fb0dbcb0f8');
                  //on instancie un nouveau mail
                  $mailer = new Swift_Mailer($transport);
                  //on instancie un nouveau corps de document mail
                  $message = (new Swift_Message($sujet))
                     ->setFrom(['galli.johanna.g2@gmail.com'])
                     ->setTo(['galli.johanna.g2@gmail.com'])
                     ->setBody($mailBody, 'text/html');
                  //on récupère et modifie le header du mail pour l'envois en HTML
                  $type = $message->getHeaders()->get('Content-Type');
                  $type->setValue('text/html');
                  $type->setParameter('charset', 'utf-8');
                  //On envois le mail en local
                  $result = $mailer->send($message);

                  if ($result) {
                     $slug = "mailEnvoye";
                     header("Location: $this->baseUrl/$slug");
                  } else {
                     echo "Votre mail n'a pas pu être envoyé";
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
         //'randomString' => $randomString,
      ]);
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
    *
    */
   public function updatePassword($slug = "ChangerMotDePasse")
   {
      //déclaration des variables
      $mdp = "";
      $errorMdp = "";

      //recupération du hash dans l'url
      $hash = $this->model->returnUrl();
      echo $hash;

      //retourne vrai si les 2 infos
      if (password_verify("johanna", $hash)) {
         echo "pseudo ok";
         //var_dump($pseudo); 
      }




      $userMail = $this->model->checkMail($hash);
      var_dump($userMail);

      //si le champ mdp est vide
      if (empty($_POST['mdp'])) {
         $errorMdp = " ";
      } else {
         $mdp = $_POST['mdp'];
         if (preg_match('`^([a-zA-Z0-9-_]{2,16})$`', $mdp)) {
            //fonction insérer mdp
            $this->model->updateMdp($userMail, $mdp);
            //message tout est ok
            $message = 'Voici un message en javascript écrit par php';
            echo '<script type="text/javascript">window.alert("' . $message . '");</script>';
         } else {
            $errorMdp = "Votre mot de passe doit contenir des lettres (en majuscule et/ou en minuscule) et/ou des chiffres. 2 - 16 caractères";
         }
      }

      $title = "Changer mot de passe";

      //affichage
      $pageTwig = 'Users/login.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'title' => $title,
         'mdp' => $mdp,
         'errorMdp' => $errorMdp,
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


      //fonction mailExist retourne false si le mail n'existe pas dans la bdd
      //$userMail = $this->model->mailExist($mail);



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
                  $error[1] = "2 caractères min. Autorisé : chiffres, lettres en majuscule et minuscule.";
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
                  $error[2] = "2 caractères min. Autorisé : chiffres, lettres en majuscule et minuscule.";
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

         var_dump($insert);

         if ($insert === true) {
            //redirigé vers page accueil
            header("Location: $this->baseUrl");
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
