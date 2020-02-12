<?php

class User extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }


   //-----> la fonction ckeckLogin : renvoi true si le pseudo entré par l'utilisateur est connu dans la bdd et renvoi un false si le pseudo entré par l'utilisateur est connu dans la bdd
   function checkLogin($pseudo)
   {
      $req = $this->pdo->prepare('SELECT pseudo, mdp, admin FROM users WHERE pseudo = :pseudo');
      $req->bindValue(':pseudo', $pseudo);
      $req->execute();
      $data = $req->fetch();
      return $data;
   }

   public function insertUser($mail, $pseudo, $mdp)
   {
      $req = $this->pdo->prepare("INSERT INTO users(mail, pseudo, mdp) VALUES ('$mail', '$pseudo', '$mdp')");

      $req->execute();
   }
   
   public function getAllUsers()
   {
      $req = $this->pdo->prepare('SELECT * FROM users');
      $req->execute();
      $req->fetchAll();
   }



   /***************************************************************** */
   public function getOneUser($pseudo)
   {
      $req = $this->pdo->prepare('SELECT * FROM users WHERE pseudo= ?');
      $req->execute([$pseudo]);

      return $req->fetch();
   }

   public function getOnePseudo($id_user)
   {
      $req = $this->pdo->prepare('SELECT pseudo FROM users WHERE id_user= ?');
      $req->execute([$id_user]);

      return $req->fetch();
   }
   public function getOneIdUser($pseudo)
   {
      $req = $this->pdo->prepare('SELECT `id_user` FROM users WHERE pseudo= ?');
      $req->execute([$pseudo]);

      return $req->fetch();
   }
   public function checkAdmin($id_user)
   {
      $req = $this->pdo->prepare('SELECT `admin` FROM users WHERE id_user= ?');
      $req->execute([$id_user]);

      return $req->fetch();
   }
   public function searchAvatar($id_user)
   {
      $req = $this->pdo->prepare('SELECT `avatar` FROM users WHERE id_user= ?');
      $req->execute([$id_user]);

      return $req->fetch();
   }

   /****************************************************************** */
}
