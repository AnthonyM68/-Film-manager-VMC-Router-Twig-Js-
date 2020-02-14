<?php

class AdminController extends Controller
{
   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
   }

   /**
   *
   */
   public function admin()
   {
      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render();
   }

   /**
   *  Affiche la liste de tout les Utilisateurs
   */
   public function editUsers()
   {
      $slug = 'Liste_Utilisateurs';

      $instanceUsers = new User();
      $users = $instanceUsers->getAllUsers();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'users' => $users
      ]);
   }

   /**
   *  Affiche la liste de tout les Films
   */
   public function editMovies()
   {
      $slug = 'Liste_Films';

      $instanceMovies = new Movies();
      $movies = $instanceMovies->getAllMovies();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'movies' => $movies
      ]);
   }

   /**
   *  Affiche la liste de tout les Artistes
   */
   public function editArtists()
   {
      $slug = 'Liste_Artistes';

      $instanceArtists = new Artists();
      $artists = $instanceArtists->getAllArtists();

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
         'slug' => $slug,
         'artists' => $artists
      ]);
   }

   /**
   *  Ajoute un nouveau Film
   */
   public function addMovie()
   {
      $slug = 'Ajout_Film';

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => $slug]);
   }

   /**
   *  Ajoute un nouvel Artiste
   */
   public function addArtist()
   {
      $slug = 'Ajout_Artiste';

      $pageTwig = 'Admin/admin.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(['slug' => $slug]);
   }
}
