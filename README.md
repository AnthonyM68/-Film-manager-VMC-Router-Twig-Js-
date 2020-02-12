# Projet !

Nous désirons créer un site qui répertorie des films. La page d’accueil présentera tous nos films présents dans notre base de données.

Un film possède un titre, une année de sortie, une affiche, un résumé, un seul et unique réalisateur, un seul et unique genre ainsi que des acteurs qui ont joué dans le film. La page d’accueil n’est pas obligée de présenter toutes ces informations, cependant un lien vers chaque film permettra de voir toutes les informations concernant le dit film.

On notera cependant qu’un acteur peut aussi être un réalisateur (exemple : Quentin Tarantino) ; ces personnes possèdent également une photo, une biographie et une date de naissance.
Le reste de notre site proposera à nos visiteurs une page pour voir tous les acteurs et leur infos, tous les réalisateurs et leurs infos.

Le site comportera une partie administration qui permettra de gérer nos données : créer, modifier ou supprimer un film, pareil pour les personnes.


# Bonus :

• Pouvoir permettre à des utilisateurs de se créer un compte (mail, mdp) et qu’ils puissent écrire un commentaire sur un film. On considérera qu’un commentaire n’a qu’un utilisateur et que le commentaire ne concerne qu’un film.

• Avoir un système de tri sur la page d’accueil permettant d’afficher les films selon un genre, une année de sortie ou un réalisateur par exemple, il n’est pas nécessaire que ce tri s’effectue en asynchrone.
Objectifs : Réaliser le plus de fonctionnalités possibles (si tout n’y est pas, et surtout les bonus, ce n’est pas si grave), MAIS SURTOUT avoir un code propre et organisé en utilisant un MVC POO. Le responsive n’est pas non plus la priorité.

> **Note** :  Durée : Minimum 5/6 jours


# Schéma :
> Réalisation des schémas conceptuels de données : [UML](https://www.lucidchart.com/invitations/accept/90c1c58c-25d2-4692-bcea-743d184b5abd) - [MCD](https://www.lucidchart.com/invitations/accept/457dde16-143d-4771-9a6e-9944b522f302) - [MPD](https://www.lucidchart.com/invitations/accept/b167b63f-9dd1-4247-bd44-70905bf34788)

# Collaborateurs
[Johana](https://github.com/johanna-Galli) - [Anthony](https://github.com/AnthonyM68) - [Illias](https://github.com/Ilias-Imoula) - [Thomas](https://github.com/Thomas-Loegel)

# NOTE :
 Utilisez la BDD fournie dans le dépot et modifiez le fichier si besoin
> Core/config.json

```json
  {
    "dsn": "mysql:host=localhost;dbname=allo_jati;charset=utf8",
    "user": "root",
    "psswd": "",
    "baseUrl": "http://localhost/mvcPOO_Twig"
  }
```
