<?php
/* =====================================================================
   NOVA CLUB — CONFIGURATION
   Seules les lignes marquées  ◀ À REMPLIR  sont à modifier.
   ===================================================================== */
return [

    // Base de données OVH (Web Cloud > Hébergements > nova-serenity.fr > Bases de données)
    'db_host' => 'XXXXXX.mysql.db',        // ◀ À REMPLIR : « Serveur » de la base
    'db_name' => 'XXXXXX',                 // ◀ À REMPLIR : nom de la base
    'db_user' => 'XXXXXX',                 // ◀ À REMPLIR : utilisateur de la base
    'db_pass' => 'XXXXXX',                 // ◀ À REMPLIR : mot de passe de la base

    // Adresse du club sur internet (sans « / » à la fin)
    'base_url' => 'https://www.nova-serenity.fr/club',

    // Comptes d'administration : chacun reçoit toutes les demandes d'accès
    // et se connecte à l'administration avec son adresse et son propre mot de passe.
    // (jamais affichées aux visiteurs)
    'admin_emails' => [
        'contact@nova-serenity.fr',
        'fabien.scheltien@nova-serenity.fr',
    ],

    // Expéditeur des e-mails envoyés aux invités
    'mail_from'      => 'club@nova-serenity.fr',
    'mail_from_name' => 'Maître de Cérémonie · NOVA Club',

    // Durée de validité d'un accès accepté
    'access_months' => 6,

    // Ne pas modifier : réglages techniques
    'db_driver' => 'mysql',   // 'sqlite' uniquement pour les tests
    'mail_mode' => 'mail',    // 'log' uniquement pour les tests
    'timezone'  => 'Europe/Paris',
];
