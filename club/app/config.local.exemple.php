<?php
/* Pour essayer le club sur votre ordinateur (XAMPP, MAMP…) :
   renommez ce fichier en « config.local.php ».
   Il n'est lu que sur localhost : en ligne, il est ignoré. */
return [
    'db_driver' => 'sqlite',                  // pas de base de données à créer
    'mail_mode' => 'log',                     // les e-mails sont écrits dans data/mail.log au lieu d'être envoyés
    'base_url'  => 'http://localhost/club',   // ajoutez le port si besoin, ex. http://localhost:8888/club
    'db_pass'   => 'local',                   // à saisir lors de la « Première visite » de l'administration
];
