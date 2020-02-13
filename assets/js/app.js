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
               console.log(i);
               //récupère le commentaire auquel appartient l'action sur le bouton modifier
               let controlText = document.getElementById("controlComment" + i);
               console.log(controlText);
               //On enregistre le commentaire dans une variable temporaire
               let tmp = controlText.innerText;
               controlText.innerHTML = ' ';
               let p = controlText.getElementsByTagName("p");


               let dataBase = controlText.dataset.base;
               let dataIdMovie = controlText.dataset.idmovie;
               let dataIdComment = controlText.dataset.idcomment;
               

               controlText.innerHTML = '<form method="POST" id="comment" action="' + dataBase +'/Comments/modifyComment/' + dataIdMovie + '/' + dataIdComment + '"><textarea class="form-control" name="controlText" id="ControlText" rows="3">'+ tmp + '</p></textarea>';

               let controlBtn = document.getElementById("contenaireBtn" + i);
               //controlBtn.innerHTML = " ";
               /*console.log(controlBtn);

               controlBtn.innerHTML += '<button type="submit" id="btnmodify' + i  + '" class="btn btn-success btn-sm mb-2">Publier</button>',
               controlBtn.innerHTML += '<button type="submit" id="btndelete' + i  + '" class="btn btn-success btn-sm mb-2">Supprimer</button>', 
               controlBtn.innerHTML += '<button type="submit" id="btncontact' + i + '" class="btn btn-success btn-sm mb-2">Contacter</button></form>';
               */

               //console.log(dataIdComment);


               break;
            case "btndelete":
               break;
            case "btncontact":
               break;
         }
      });
   }
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
/*
btnmodify.addEventListener("click", function () {
   textArea
});*/

/*
let NomDuFichier     = CheminComplet.substring(CheminComplet.lastIndexOf( "/" )+1 );
alert ('NomDuFichier : \n'+NomDuFichier+ ' \n\n CheminRepertoire : \n' +CheminRepertoire+ ' \n\n CheminComplet :\n ' +CheminComplet);*/
