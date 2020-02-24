<?php

class Users extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   /**
    *  Renvoi true si Pseudo est connu dans la bdd,
    *  Renvoi false si Pseudo est inconnu dans la bdd
    */
   public function checkLogin($pseudo)
   {
      $req = $this->pdo->prepare('SELECT pseudo, mdp, admin FROM users WHERE pseudo = :pseudo');
      $req->bindValue(':pseudo', $pseudo);
      $req->execute();
      $data = $req->fetch();
      return $data;
   }

   /**
    *  Ajoute un nouveau User
    */
   public function insertUser($pseudo, $mdp, $mail, $avatar)
   {
      $req = $this->pdo->prepare("INSERT INTO users(pseudo, mdp, mail, avatar) VALUES (:pseudo, :mdp, :mail, :avatar)");
      return $req->execute(array(
         'pseudo' => $pseudo,
         'mdp' => $mdp,
         'mail' => $mail,
         'avatar' => $avatar,
      ));
   }

   /**
    * verifie si le pseudo entré existe dans la bdd
    */
   public function pseudoExist($pseudo)
   {
      $req = $this->pdo->prepare("SELECT pseudo FROM users WHERE pseudo = :pseudo");
      $req->bindValue(':pseudo', $pseudo);
      $req->execute();
      return $req->fetch();
   }

   /**
    * verifie si le mail entré existe dans la bdd
    */
   public function mailExist($mail)
   {
      $req = $this->pdo->prepare("SELECT mail FROM users WHERE mail = :mail");
      $req->bindValue(':mail', $mail);
      $req->execute();
      return $req->fetch();
   }


   /******************FONCTIONS POUR CHANGER MOT DE PASSE**************************/

   /*
    * retourner le pseudo d'un utilisateur par rapport a son mail
    */
   public function recupPseudo($mail)
   {
      //chercher dans table users le pseudo correspondant au mail
      $req = $this->pdo->prepare("SELECT pseudo FROM users WHERE mail = ?");
      $req->execute([$mail]);
      return $req->fetch();
   }



   public function checkMail($hash)
   {
      $req = $this->pdo->prepare("SELECT mail FROM users WHERE mail = :mail");
      $req->bindValue(':mail', $hash);
      $req->execute();
      return $req->fetch();
   }

   /**************POUR FORMULAIRE DE MOT DE PASSE OUBLIE**************/
   /**
    * check dans la table Usershash
    */
   public function checkMailInUsersHash($mail)
   {
      //tester si le mail existe déja 
      $req = $this->pdo->prepare("SELECT * FROM users_hash WHERE mail = :mail");
      $req->bindValue(':mail', $mail);
      $req->execute();
      $data = $req->fetch();
      return $data;
      //var_dump($data);
   }

   /**
    * insertion dans la table Usershash
    */
   public function insertInUsersHash($mail, $md5, $pseudo) {
      $req = $this->pdo->prepare("INSERT INTO users_hash (mail, md5, pseudo) VALUES ('$mail', '$md5', '$pseudo')");

      $data = $req->execute();
      return $data;
      var_dump($data);
   }

   /**
    * update dans la table Usershash
    */
   public function updateInUsersHash($mail, $md5) {
         $req = $this->pdo->prepare("UPDATE users_hash SET md5 = :md5 WHERE mail = :mail");
         $req->bindValue(':md5', $md5);
         $req->bindValue(':mail', $mail);

         $data = $req->execute();
         return $data;
         var_dump($data);
   }
    /********************************************************************/


     /**************POUR FORMULAIRE CHANGER DE MOT DE PASSE**************/
   /*
    * va chercher des caractères dans l'url après l'emplacement donné ()
    */
   public function returnUrl($pointeur)
   {
      $adresse = $_SERVER['PHP_SELF'];
      $i = 0;
      foreach ($_GET as $cle => $valeur) {
         $adresse .= ($i == 0 ? '?' : '&') . $cle . ($valeur ? '=' . $valeur : '');
         $i++;
      }
      return substr($adresse, $pointeur);
   }

   /**
    * check dans la table Usershash
    */
    public function checkMd5InUsersHash($md5)
    {
       //tester si le md5 existe déja 
       $req = $this->pdo->prepare("SELECT * FROM users_hash WHERE md5 = '$md5'");
       $req->execute();
       $data = $req->fetch();
       return $data;
       
    }



   /*
    * changer le mot de passe d'un utilisateur par celui entré dans l'input en fonction de son pseudo
    */
   public function updateMdpByPseudo($pseudo, $mdp)
   {
      $req = $this->pdo->prepare("UPDATE users SET mdp = :mdp WHERE pseudo= :pseudo");
      $req->bindValue(':pseudo', $pseudo);
      $req->bindValue(':mdp', $mdp);
      return $req->execute();
   }

   public function updateMdp($mail, $mdp)
   {
      $req = $this->pdo->prepare("UPDATE users SET mdp = :mdp WHERE mail= :mail");
      $req->bindValue(':mail', $mail);
      $req->bindValue(':mdp', $mdp);
      return $req->execute();
   }


   /********************************************************************/


   /*************************FONCTIONS ANTHONY**************************/

   /**
    *  Récupère les Utilisateurs
    */
   public function getAllUsers()
   {
      $req = $this->pdo->prepare('SELECT * FROM users');
      $req->execute();
      return $req->fetchAll();
   }

   /**
    *  Récupère l'id_user depuis son pseudo
    */
   public function getOneUser($pseudo)
   {
      $req = $this->pdo->prepare('SELECT * FROM users WHERE pseudo= ?');
      $req->execute([$pseudo]);
      return $req->fetch();
   }

   /**
    * Récupère le pseudo depuis l'id user
    */
   public function getOnePseudo($id_user)
   {
      $req = $this->pdo->prepare('SELECT pseudo FROM users WHERE id_user= ?');
      $req->execute([$id_user]);
      return $req->fetch();
   }

   /**
    * Vérifie si l'id_user est admin
    */
   public function checkAdmin($id_user)
   {
      $req = $this->pdo->prepare('SELECT `admin` FROM users WHERE id_user= ?');
      $req->execute([$id_user]);
      return $req->fetch();
   }

   /**
    *  Récupère l'avatar depuis un id user
    */
   public function searchAvatar($id_user)
   {
      $req = $this->pdo->prepare('SELECT avatar FROM users WHERE id_user= ?');
      $req->execute([$id_user]);
      return $req->fetch();
   }

   /**
    * 
    */
   public function modifyAvatar($newAvatar, $pseudo)
   {
      $req = $this->pdo->prepare("UPDATE users SET avatar = ? WHERE pseudo = ?");
      return $req->execute([$newAvatar, $pseudo]);
   }
   /**
    * Récupère l'id d'un utilisateur depuis son pseudo
    */
   public function getOneIdUser($pseudo)
   {
      $req = $this->pdo->prepare('SELECT `id_user` FROM users WHERE pseudo= ?');
      $req->execute([$pseudo]);
      return $req->fetch();
   }
   public function getMailById($id_user)
   {
      $req = $this->pdo->prepare('SELECT `mail` FROM users WHERE id_user= ?');
      $req->execute([$id_user]);
      return $req->fetch();
   }


   public function updatePseudo($newpseudo, $pseudo)
   {
      $req = $this->pdo->prepare('UPDATE users SET pseudo = ? WHERE pseudo = ?');
      return $req->execute([$newpseudo, $pseudo]);
   }

   public function deleteAccount($pseudo)
   {
      $req = $this->pdo->prepare('DELETE FROM users WHERE pseudo = ?');
      return $req->execute([$pseudo]);
   }
   /*******************************************************************/
}
