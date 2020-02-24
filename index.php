<?php
require_once('vendor/autoload.php');
session_start();

$router = new Router($_GET['url']);

// Route Films
$router->get('/Films/Film_:id_movie', 'Movies.showMovie');
$router->get('/Films/Artiste_:id_artist', 'Artists.showArtist');
$router->post('/Films/Recherche', 'Movies.search');
$router->post('/Films/Genre', 'Movies.genre');
$router->get('/Film_:id_movie', 'Movies.showMovie');
$router->get('/Films', 'Movies.showAllMovies');


// Route Artistes
$router->get('/Artistes/Artiste_:id_artist', 'Artists.showArtist');
$router->get('/Artiste_:id_artist', 'Artists.showArtist');
$router->post('/Artistes/Recherche', 'Artists.search');
$router->get('/Artistes', 'Artists.showAllArtists');

// Route Login
$router->get('/Connection', 'Users.connexion');
$router->post('/', 'Users.login');

// route forgetPassword
$router->get('/MotDePasseOublie', 'Users.forgetPassword');
$router->post('/MotDePasseOublie/post', 'Users.forgetPassword');

// route MailEnvoyé
$router->get('/mailEnvoye', 'Users.mailEnvoye');

// Route ChangePassword
$router->get('/ChangerMotDePasse/:md5', 'Users.updatePassword');
$router->post('/ChangerMotDePasse/:md5', 'Users.updatePassword');

// route MailEnvoyé
$router->get('/MotDePasseRéinitialisé', 'Users.MotDePasseRéinitialisé');


// route forgetPassword
$router->get('/MotDePasseOublie', 'Users.forgetPassword');
$router->post('/Connection/post', 'Users.forgetPassword');

// Route Register
$router->get('/Inscription', 'Users.register');
$router->post('/Inscription/post', 'Users.register');

// route bienvenue (apres inscription)
$router->post('/Bienvenue', 'Users.bienvenue');

// Route Commentaires
$router->get('/Commentaires/Effacer/:id_comment/:id_movie/:pseudo', 'Comments.deleteComment');
$router->post('/Commentaires/Modifier/:id_movie/:id_comment', 'Comments.modifyComment');
$router->post('/Commentaires/Ajouter_:id_movie', 'Comments.addComment');
$router->get('/Commentaires', 'Comments.index');

// Route Profile
$router->get('/Profile/Modifier/Pseudo', 'Profils.modifyPseudo');
$router->post('/Profile/Change/Pseudo', 'Profils.changePseudo');

$router->get('/Profile/Modifier/Avatar', 'Profils.modifyAvatar');
$router->post('/Profile/Modifier/Avatar', 'Profils.changeAvatar');

$router->get('/Profile/Modifier/Mdp', 'Profils.modifymdp');
$router->post('/Profile/Modifier/Mdp', 'Profils.changemdp');

$router->get('/Profile/Envoyer_:slug', 'Profils.sendMessage');
$router->get('/Profile/Envoyer', 'Profils.sendMessage');
$router->post('/Profile/Envoyer_:slug', 'Profils.sendMessageToUser');
$router->post('/Profile/Envoyer_', 'Profils.sendMessageToUser');

$router->get('/Profile/Message/Supprimer_:slug', 'Profils.deleteMessage');
$router->get('/Profile/Modifier/Recevoir_:slug', 'Profils.receiveMessage');

$router->get('/Profile/Supprimer/Compte', 'Profils.deleteAccount');
$router->post('/Profile/Supprimer/Compte_:slug', 'Profils.deleteAccount');

$router->get('/Profile', 'Profils.profil');

// Route Deconnexion
$router->get('/Deconnection', 'Users.logout');

// Route Admin
$router->get('/Admin/Effacer_:id_comment/:id_movie/:pseudo/:id_user', 'Comments.deleteComment');
$router->get('/Admin/Effacer_:id_comment', 'Comments.deleteComment');
$router->post('/Admin/Utilisateur', 'Comments.searchAllCommByUser');
$router->post('/Admin/Titre', 'Comments.searchAllCommByTitleMovie');
$router->get('/Admin/Tous', 'Comments.getAllCom');

$router->get('/Admin', 'Admin.admin');
$router->get('/Admin/Liste_Utilisateurs', 'Admin.editUsers');
$router->get('/Admin/Liste_Films', 'Admin.editMovies');
$router->get('/Admin/Liste_Artistes', 'Admin.editArtists');
$router->get('/Admin/Ajout_Film', 'Admin.addMovie');
$router->post('/Admin/Ajout_Film/Add', 'Admin.addMovie');
$router->post('/Admin/Ajout_Artiste/Add', 'Admin.addArtist');
$router->get('/Admin/Ajout_Artiste', 'Admin.addArtist');
$router->get('/Admin/Associer', 'Admin.association');
$router->post('/Admin/Associer/With', 'Admin.association');


// Route Contact
$router->post('/Suggestion', 'Suggestion.addSuggestion');
$router->get('/Suggestion', 'Suggestion.suggestion');

// Route Home
$router->get('/', 'Home.index');



// Route RUN
$router->run();
