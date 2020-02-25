<?php

class Admin extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }

   /**
   *  Ajoute un Film dans la BDD
   */
   public function addMovie($picture, $title, $year, $style, $resume, $trailer, $time)
   {
      $req = $this->pdo->prepare(
        "INSERT INTO movies (picture, title, year, style, resume, trailer, time)
         VALUES (?,?,?,?,?,?,?)");
      $req->execute([$picture, $title, $year, $style, $resume, $trailer, $time]);
   }

   /**
   *  Ajoute un Artiste dans la BDD
   */
   public function addArtist($picture, $first_name, $last_name, $birth_day, $bio, $role)
   {
      $req = $this->pdo->prepare(
        "INSERT INTO artists (picture, first_name, last_name, birth_day, bio, role)
         VALUES (?,?,?,?,?,?)");
      $req->execute([$picture, $first_name, $last_name, $birth_day, $bio, $role]);
   }

   /**
   *  Ajoute un Artiste et son Rôle dans un Film dans la BDD
   */
   public function association($id_artist, $id_movie, $role)
   {
      $req = $this->pdo->prepare(
         "INSERT INTO artists_movies (id_artist, id_movie, role)
         VALUES (?,?,?)");
      $req->execute([$id_artist, $id_movie, $role]);
   }
}

