<?php
   /**
    *          ANTHONY
    */
class HomeController extends Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->twig = parent::getTwig();
      $this->baseUrl = parent::getBaseUrl();
   }
   /**
    * Démarre et initialise une session utilisateur
    */
   public function startSession()
   {
      if (!isset($_SESSION['status'])) {
         $_SESSION['status'] = null;
         $_SESSION['utilisateur'] = "Visiteur";
         $_SESSION['receiveMessage'] = null;
      }
   }
   /**
    * D2fini une super global
    */
   public function __set($name, $value)
   {
      $_SESSION[$name] = $value;
   }
   /**
    * Recherche une valeur s'une super global
    */
   public function __get($name)
   {
      if (isset($_SESSION[$name])) {
         return $_SESSION[$name];
      }
   }
   /**
    * Vérifie si un formulaire a été posté
    */
   public function __getPOST($name)
   {
      if (isset($_POST[$name])) {
         return $_POST[$name];
      }
   }
   /**
    * Vérifie si une super global est vide
    */
   public function __empty($name)
   {
      return empty($_SESSION[$name]);
   }
   /**
    * Vérifie si une super global existe
    */
   public function __isset($name)
   {
      return isset($_SESSION[$name]);
   }
   /**
    * Efface le tableau des super global
    */
   public function __unsetTab()
   {
      $erase = false;
      for ($i = 0; $i < count($_SESSION['tabSession']); $i++) {
         $name = $_SESSION['tabSession'][$i];
         $this->__unset($name);
         if (false === $this->__isset($name)) {
            $erase = true;
         } else {
            $erase = false;
         }
      }
      if ($erase === true) {
         unset($_SESSION['tabSession']);
      }
   }
   /**
    *  Détruit une super global
    */
   public function __unset($name)
   {
      unset($_SESSION[$name]);
   }
   /**
    * Ecrase une session
    */
   public function destroy()
   {
      session_destroy();
      unset($_SESSION);
      $_SESSION['status'] = null;
      $_SESSION['utilisateur'] = "Visiteur";
      $_SESSION['avatar'] = "$this->baseUrl/assets/avatar/jatilogo.png";
   }
   /**
    * Affiche une alerte(n'est plu utilisé)
    */
   public function __alert($alert)
   {
      if (isset($_SESSION[$alert])) {
         echo $_SESSION[$alert];
      }
   }
   /**
    * rend la vus du Home
    */
   public function index()
   {
      $this->startSession();
      
      $instanceProfils = new ProfilsController();
      $instanceProfils->checkMessage();

      $pageTwig = 'index.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
       'status'       => $_SESSION['status'],
       'alertMessage' => $_SESSION['receiveMessage'], 
       'alertMessage' => $_SESSION['receiveMessage'], 
       'homefooter'   => 'homefooter']);
   }
}
