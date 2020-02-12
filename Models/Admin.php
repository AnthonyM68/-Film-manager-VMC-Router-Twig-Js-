<?php

class Admin extends Model
{
   public function __construct()
   {
      $this->pdo = parent::getPdo();
   }
}
