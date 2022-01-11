# Printoclock

Projet d'envoi de newsletter.

- Formulaire d'inscription à choix multiple sur plusieurs newsletters.
- Inscription et envoi de confirmation d'inscription par mail.
- Liste des newsletters par catégorie.
- Commande symfony pour un envois  des newsletters avec mise en file d'attente.

## Installation
Modifier les variables d'environnement dans le fichier .env (connexion BDD et SMTP)
Creation de la base de donnée avec les commandes :
- symfony console doctrine:database:create
- symfony console doctrine:migrations:migrate

Génération d'un jeu de données fictifs avec la commande :
- symfony console doctrine:fixture:load

Commande envois des newsletters :
- symfony console app:send-newsletter

Commande pour lancer les envois différés :
- symfony console messenger:consume async -vv
