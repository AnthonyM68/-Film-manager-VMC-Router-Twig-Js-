<?php

class HomeController extends Controller
{

    public function __construct()
    {
        $this->twig = parent::getTwig();
    }
    
   public function index()
   {
      $pageTwig = 'index.html.twig';
      $template = $this->twig->load($pageTwig);



      session_start();
      //session_destroy();
      $test = $_SESSION;

      //parent::test();

      echo $template->render(['test' => $test]);
   }
}
