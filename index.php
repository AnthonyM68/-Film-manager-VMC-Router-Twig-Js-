<?php
require_once('vendor/autoload.php');

//echo password_hash("mdp", PASSWORD_DEFAULT);

$router = new Router($_GET['url']);


// Route Films
$router->get('/Films/Film_:id_movie', 'Movies.showMovie');
$router->get('/Films/Artiste_:id_artist', 'Artists.showArtist');
$router->get('/Films/:query', 'Movies.showAllMovies');
$router->get('/Film_:id_movie', 'Movies.showMovie');
$router->get('/Films', 'Movies.showAllMovies');


// Route Artistes
$router->get('/Artistes/Artiste_:id_artist', 'Artists.showArtist');
$router->get('/Artiste_:id_artist', 'Artists.showArtist');
$router->get('/Artiste_:id_artist', 'Artists.showArtist');
$router->get('/Artistes', 'Artists.showAllArtists');

// Route Login
$router->get('/Connexion', 'Users.connexion');
$router->post('/Connexion/post', 'Users.login');

// route forgetPassword
$router->get('/MotDePasseOublie', 'Users.forgetPassword');
$router->post('/MotDePasseOublie/post', 'Users.forgetPassword');

// Route Register
$router->get('/Inscription', 'Users.register');
$router->post('/Inscription/post', 'Users.register');

// Route Commentaires
$router->post('/Comments/addComment/:id_movie', 'Comments.addComment');
$router->get('/Comments/Delete_:id_movie', 'Comments.delAllComByMovie');
$router->get('/Comments/GetAll', 'Comments.getAllCom');
$router->get('/Comments', 'Comments.index');

// Route Deconnexion
$router->get('/Deconnexion', 'Users.logout');


// Route Admin
$router->get('/Admin', 'Admin.admin');
$router->get('/Admin/:slug', 'Admin.editUtilisateurs');
$router->get('/Admin/:slug', 'Admin.editFilms');
$router->get('/Admin/:slug', 'Admin.editArtistes');
$router->get('/Admin/:slug', 'Admin.addFilm');
$router->get('/Admin/:slug', 'Admin.addArtiste');


// Route Home
$router->get('/', 'Home.index');


// Route RUN
$router->run();
