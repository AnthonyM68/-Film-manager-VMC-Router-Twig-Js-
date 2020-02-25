<?php

class SuggestionController extends Controller
{
   private $pseudo;
   private $email;
   private $textArea;
   private $file;

   public function __construct()
   {
      $this->twig = parent::getTwig();
      parent::__construct();
      $this->model = new Suggestion();
   }

   public function suggestion()
   {
      $suggestion  = $this->model->suggestion();
      $pageTwig = 'Suggestion/suggestion.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([
      "suggestion" => $suggestion,
      "status" => $_SESSION['status'],
      "pageSug" => "pageSug"
      ]);
   }

   public function addSuggestion()
   {
      
      if(empty($_POST['pseudo'])){
         $erPseudo = 'no';
      }else{
         $erPseudo = 'yes';
      }
      if(empty($_POST['email'])){
         $erEmail = 'no';
      }else{
         $erEmail = 'yes';
      }
      if(empty($_POST['text'])){
         $erText = 'no';
      }else{
         $erText = 'yes';
         
      }          
      
      if(!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['text'])){

         if($_FILES['zip_file']['size'] === null){
            $zip_size = 2000000;
            }  
         //Création du fichier zip 
         if ($_FILES && $_FILES['zip_file']) {
            if (!empty($_FILES['zip_file']['name'][0])) {  

               if ($_FILES['zip_file']['size'] > 2000000) {
               $zip = 'yes';
               
               $zip = new ZipArchive();

               $zip_name = "assets/FileZip/FileZip" .rand(0 , 999). ".zip";              
               // Create a zip target
               if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
                  $error .= "Désolé, la création du ZIP n'a pas pu s'effectuer correctement.<br/>";
                 
               }
               $imageCount = count($_FILES['zip_file']['name']);
               for ($i = 0; $i < $imageCount; $i++) {
                  if ($_FILES['zip_file']['tmp_name'][$i] == '') {
                        continue;
                  }
                  $newname = date('YmdHis', time()) . mt_rand() . '.jpg';
                  // Moving files to zip.
                  $zip->addFromString($_FILES['zip_file']['name'][$i], file_get_contents($_FILES['zip_file']['tmp_name'][$i]));
               }
               $zip->close();
               // Create HTML Link option to download zip
               $success = basename($zip_name); 
               
               }else{
              if(empty($_POST['pseudo'])){
                  $name ='';
               }else{
                  $name = $_POST['pseudo'];
               }
               if(empty($_POST['email'])){
                  $mail ='';
               }else{
                  $mail = $_POST['email'];
               }
               if(empty($_POST['text'])){
                  $txtAre ='';
               }else{
                  $txtAre = $_POST['text'];
               }             
            }
            }
         }

         //Si le fichier zip existe créer un lien pour le télécharge
         if (isset($zip_name)){
            $download = " <a href=http://localhost/allo_jati/$zip_name><h1>Télécharger</h1></a>";
            $zip = 'yes';

         }else{
            $download = "";
            $zip = 'yes';
            
         }


         //Envoyer un e-mail
         if ($_SERVER['SERVER_NAME'] == 'localhost'){         
            $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 25))
               ->setUsername('0cf32673dea69d')
               ->setPassword('50f544d056b8b2');
         }else{
            $transport = new Swift_SendmailTransport();
         }
         $mailer = new Swift_Mailer($transport);      
         $message = (new Swift_Message('Suggestion site Allo_Jati'))
            ->setFrom([$_POST['email'] => $_POST['pseudo']])
            ->setTo(['receiver@suggestion.org', 'allo_jati@suggestion.org' => 'A name'])
            ->setBody($_POST['text'].$download, 'text/html');        
         $result = $mailer->send($message);
         if ($result){
            $alertsEmail = 'yes';
            $name = '';
            $mail = '';
            $txtAre = '';
         } else {
            $alertsEmail = 'no';
            $name = '';
            $mail = '';
            $txtAre = '';
         }


      }else{
         $alertsEmail ='noText';
      
         if(!isset($zip_name)){
            $zip = 'no';
            if(empty($_POST['pseudo'])){
               $name ='';
            }else{
               $name = $_POST['pseudo'];
            }
            if(empty($_POST['email'])){
               $mail ='';
            }else{
               $mail = $_POST['email'];
            }
            if(empty($_POST['text'])){
               $txtAre ='';
            }else{
               $txtAre = $_POST['text'];
            } 
         }else{
            $zip ='yes';
         }

         if(empty($_POST['pseudo'])){
            $name ='';
         }else{
            $name = $_POST['pseudo'];
         }
         if(empty($_POST['email'])){
            $mail ='';
         }else{
            $mail = $_POST['email'];
         }
         if(empty($_POST['text'])){
            $txtAre ='';
         }else{
            $txtAre = $_POST['text'];
         }        
         $errSize = 'yes';        
      }  

      $pageTwig = 'Suggestion/suggestion.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render([   "alertsEmail" => $alertsEmail,
                                 "name" => $name,
                                 "mail" => $mail,
                                 "txtAre" => $txtAre,
                                 "erPseudo" => $erPseudo,
                                 "erEmail" => $erEmail,
                                 "erText" => $erText,                                 
                                 "zip" => $zip,
                                 "alertsEmail" =>$alertsEmail, 
                                 "alertMessage" => $_SESSION['receiveMessage'], 
                                 "status" => $_SESSION['status']  ]);

   }         
}