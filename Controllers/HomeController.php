<?php

class HomeController extends Controller
{



   public function __construct()
   {
      parent::__construct();
      $this->twig = parent::getTwig();
      $this->baseUrl = parent::getBaseUrl();
   }
   /**
    *
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
    *
    */
   public function __set($name, $value)
   {
      $_SESSION[$name] = $value;
   }

   /**
    *
    */
   public function __get($name)
   {
      if (isset($_SESSION[$name])) {
         return $_SESSION[$name];
      }
   }

   /**
    *
    */
   public function __getPOST($name)
   {
      if (isset($_POST[$name])) {
         return $_POST[$name];
      }
   }

   /**
    *
    */
   public function __empty($name)
   {
      return empty($_SESSION[$name]);
   }

   /**
    *
    */
   public function __isset($name)
   {
      return isset($_SESSION[$name]);
   }

   /**
    *
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
    *
    */
   public function __unset($name)
   {
      unset($_SESSION[$name]);
   }

   /**
    *
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
    *
    */
   public function __alert($alert)
   {
      if (isset($_SESSION[$alert])) {
         echo $_SESSION[$alert];
      }
   }

   /**
    *
    */
   public function index()
   {
      $this->startSession();
      
      $instanceProfils = new ProfilsController();
      $instanceProfils->checkMessage();

      $pageTwig = 'index.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['status' => $_SESSION['status'], 'alertMessage' => $_SESSION['receiveMessage'], 'alertMessage' => $_SESSION['receiveMessage']]);
   }
}
