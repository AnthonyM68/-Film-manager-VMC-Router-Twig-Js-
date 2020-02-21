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
      echo $template->render(["suggestion" => $suggestion, 'alertMessage' => $_SESSION['receiveMessage']]);
   }

   public function addSuggestion()
   {
      $pseudo = $_POST['pseudo'];
      $email = $_POST['email'];
      $textArea = $_POST['text'];

      //function pour fichier zip
      if ($_FILES && $_FILES['zip_file']) {

         if (!empty($_FILES['zip_file']['name'][0])) {
     
            $zip = new ZipArchive();
            $zip_name = "assets/FileZip/FileZip" .rand(0 , 999). ".zip";
            
            // Create a zip target
            if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
               $error .= "Sorry ZIP creation is not working currently.<br/>";
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
            
         } else {
            $error = '<strong>Error!! </strong> Please select a file.';}                  
      }

      if (isset($zip_name)){
         $download = " <a href = http://localhost/allo_jati/$zip_name ><h1>Download</h1> </a>";
      }else{
         $download = "";
      }

      if ($_SERVER['SERVER_NAME'] == 'localhost'){
         
         $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 25))
            ->setUsername('0cf32673dea69d')
            ->setPassword('50f544d056b8b2');
      }  else{
            $transport = new Swift_SendmailTransport();
         }
         $mailer = new Swift_Mailer($transport);
      
         $message = (new Swift_Message('Suggestion site Allo_Jati'))
            ->setFrom([$email => $pseudo])
            ->setTo(['receiver@suggestion.org', 'allo_jati@suggestion.org' => 'A name'])
            ->setBody($textArea.$download, 'text/html');
        
         $result = $mailer->send($message);

         if ($result){
            $alertsEmail = 'yes';
         } else {
            $alertsEmail = 'no';
         }
                 
      $pageTwig = 'Suggestion/suggestion.html.twig';
      $template = $this->twig->load($pageTwig);
      echo $template->render(["alertsEmail" =>$alertsEmail, 'alertMessage' => $_SESSION['receiveMessage']]);
   }          
}