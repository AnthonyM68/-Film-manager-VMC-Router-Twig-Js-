//Déclaration des constantes
const CheminComplet = document.location.href;
const CheminRepertoire = CheminComplet.substring(0, CheminComplet.lastIndexOf("/"));



let tabButton = [];
let btn = []
let tabcontent = document.getElementsByClassName("name");

for (i = 0; i < tabcontent.length; i++) {
   tabButton[i] = tabcontent[i].getElementsByTagName("button");
}

for (let i = 0, iMax = tabButton.length; i < iMax; ++i) {
   for (j = 0; j < tabButton[i].length; j++) {
      //Récupère l'ID de tous les élèments dans un tableau
      let id = tabButton[i][j].id;
      document.getElementById(id).addEventListener("click", function () {
         let id_elem = id;
         let btn_name = id_elem.split(i + 1);
         switch (btn_name[0]) {
            case "btnmodify":
               i++;
               //récupère le commentaire auquel appartient l'action sur le bouton modifier
               let pComment = document.getElementById("pComment" + i);
               let tmp = pComment.innerText;
               //On récupère le container du commentaire lié
               let controlText = document.getElementById("controlComment" + i);
               //On récupère les datas liés au commentaire
               let dataBase = controlText.dataset.base;
               let dataIdMovie = controlText.dataset.idmovie;

               let dataIdComment = controlText.dataset.idcomment;

               //On ré-injecte le formulaire de soumission 
               controlText.innerHTML = '<form method="POST" id="comment" action="' + dataBase + '/Commentaires/Modifier/' + dataIdMovie + '/' + dataIdComment + '"><textarea class="form-control" name="controlText" id="ControlText" rows="3">' + tmp + '</textarea><div id="contenaireBtn' + i + '" class="col-12 d-flex justify-content-between name"><button type="submit" id="btnmodify' + i + '" class="btn btn-secondary btn-sm mb-2">Publier</button><button type="submit" id="btndelete' + i + '" class="btn btn-secondary btn-sm mb-2">Supprimer</button><button type="submit" id="btncontact' + i + '" class="btn btn-secondary btn-sm mb-2">Contacter</button></form>';
               break;

            case "btncontact":
               i++
               let btncontact = document.getElementById("btncontact" + i);
               let user = btncontact.dataset.user;
               let mail = btncontact.dataset.mail;
               redirectMail(user, mail);
               break;
         }
      });
   }
}
//Ouvre le gestionnaire de l'email
function redirectMail(user, mail) {
   window.location.href = "mailto:" + mail + "?subject=Notre site Allo_jati souhaite correspondre avec vous&body=Bonjour, " + user;
}
//Si nous somme bien sur la page Films
if (CheminRepertoire === "http://localhost/allo_jati/Films") {
   let tabInputs = [];
   let conteneur = document.getElementById('containerstar');

   //Récupère tous les éléments input
   let inputs = conteneur.getElementsByTagName("input");

   //Boucle sur éléments
   for (let inp, i = 0, iMax = inputs.length; i < iMax; ++i) {
      inp = inputs[i];
      //Récupère l'ID de tous les élèments dans un tableau
      tabInputs[i] = inp.id;
   }
   //Check l'action sur les élèents de type RADIO ETOILE
   for (let star, i = 0; i < tabInputs.length; i++) {
      document.getElementById(tabInputs[i]).addEventListener("change", function () {
         element = document.getElementById(tabInputs[i]);
         star = element.value;
         el = document.getElementById('containerstar');
         //Switch sur les étoile et colorie les étoiles en fonction de la note
         switch (star) {
            case "0":
               el.setAttribute('class', 'star-complet');
               break;
            case "1":
               el.setAttribute('class', 'star-complet1');
               break;
            case "2":
               el.setAttribute('class', 'star-complet2');
               break;
            case "3":
               el.setAttribute('class', 'star-complet3');
               break;
            case "4":
               el.setAttribute('class', 'star-complet4');
               break;
            case "5":
               el.setAttribute('class', 'star-complet5');
               break;
         }
      });
   }
}

(function () {
   alerte = document.getElementById('alerte');
   test = document.getElementById('alertDisplay');
   if (alerte != null) {
      $("#alerte").fadeTo(3000, 500).slideUp(500, function () {
         $("#alerte").slideUp(500);
         test.innerHTML = "";

      });
   }
})()

function uploadFile(target) {
   file = "<b>Votre image:</b><br>" + target.files[0].name;
   document.getElementById("file-name").innerHTML = file;
}
$('.collapse').collapse();