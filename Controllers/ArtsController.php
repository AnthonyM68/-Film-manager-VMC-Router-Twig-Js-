<?php
class ArtsController extends Controller
{
   private $id_movie;
   private $title;
   private $year;
   private $style;

   public function __construct()
   {
       $this->twig = parent::getTwig();
       parent::__construct();
       $this->model = new Movies();
   }
}
