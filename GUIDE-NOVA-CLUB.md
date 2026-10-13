# NOVA Club — Guide de mise en ligne

Ce guide vous accompagne pas à pas pour installer le NOVA Club à l'adresse **www.nova-serenity.fr/club**, puis pour l'utiliser au quotidien. Comptez environ 45 minutes la première fois. Aucune connaissance technique n'est nécessaire.

---

## Ce que contient le dossier `club`

| Élément | Rôle |
|---|---|
| `index.php` | La porte d'entrée : console, verre brisé, vidéos, puis « Tenue correcte exigée » |
| `club.php`, `evenement.php`, `histoire.php` | Le club : à venir, événements passés, Notre histoire |
| `admin/` | Votre espace d'administration |
| `app/config.php` | **Le seul fichier à compléter** (étape 3) |
| `assets/`, `videos/` | Images, sons et vidéos |
| `data/` | Les photos que vous ajouterez (dossier privé, invisible depuis internet) |

---

## Étape 1 — Vérifier l'hébergement OVH

1. Connectez-vous à votre espace client OVH.
2. Allez dans **Web Cloud**, puis **Hébergements**, puis cliquez sur **nova-serenity.fr**.
3. Dans l'encadré **Informations générales**, regardez :
   - **Offre** : Starter, Perso, Pro ou Performance conviennent. Si vous voyez « hébergement gratuit 100 Mo », il faut passer à l'offre Perso.
   - **Version PHP globale** : elle doit être **7.4 ou plus** (8.2 ou 8.3 idéalement). Si elle est plus ancienne, cliquez sur les « … » à côté pour la changer. Vos pages actuelles en HTML ne sont pas concernées par ce réglage.
   - **Certificat SSL** : il doit être actif (le site s'ouvre en `https://`).

## Étape 2 — Créer la base de données

1. Toujours sur la page de l'hébergement, ouvrez l'onglet **Bases de données**.
2. Cliquez sur **Créer une base de données**.
3. Choisissez **MySQL**, la version proposée par défaut, puis un nom d'utilisateur et un mot de passe. **Notez ce mot de passe** : il vous servira deux fois.
4. OVH vous envoie un e-mail quand la base est prête (quelques minutes).
5. Dans la liste des bases, notez :
   - le **nom de la base** ;
   - l'**adresse du serveur** (elle se termine par `.mysql.db`) ;
   - le **nom d'utilisateur**.

## Étape 3 — Compléter le fichier de configuration

1. Ouvrez `club/app/config.php` avec le Bloc-notes (Windows) ou TextEdit en mode « texte brut » (Mac). N'utilisez pas Word.
2. Remplacez les quatre `XXXXXX` des lignes marquées **◀ À REMPLIR**, en gardant les apostrophes autour :

```php
'db_host' => 'monserveur.mysql.db',
'db_name' => 'nomdelabase',
'db_user' => 'nomutilisateur',
'db_pass' => 'le-mot-de-passe-de-la-base',
```

3. Enregistrez. Ne touchez pas aux autres lignes.

## Étape 4 — Créer l'adresse club@nova-serenity.fr

C'est l'adresse qui apparaît comme expéditeur des e-mails du « Maître de Cérémonie ». Elle renvoie tout vers contact@, qui reste invisible pour vos invités.

1. Dans l'espace client OVH : **Web Cloud**, puis **E-mails**, puis **nova-serenity.fr**.
2. Ouvrez l'onglet **Redirection**, puis cliquez sur **Ajouter une redirection**.
3. De : `club@nova-serenity.fr` — Vers : `contact@nova-serenity.fr`.
4. Choisissez « Ne pas conserver de copie », puis validez.

## Étape 5 — Envoyer le dossier sur le site

1. Installez le logiciel gratuit **FileZilla** (filezilla-project.org).
2. Dans OVH, sur la page de l'hébergement, ouvrez l'onglet **FTP - SSH**. Vous y trouverez le **serveur FTP** et l'**identifiant**. Si vous ne connaissez pas le mot de passe FTP, cliquez sur « … » à côté de l'identifiant, puis **Modifier le mot de passe**.
3. Dans FileZilla, renseignez en haut : Hôte, Identifiant, Mot de passe, Port `21`, puis cliquez sur **Connexion rapide**.
4. À droite apparaît votre hébergement. Ouvrez le dossier **www** (c'est là que se trouvent vos pages actuelles).
5. À gauche, retrouvez le dossier `club` sur votre ordinateur, et glissez-le **entier** dans `www`.
6. Attendez la fin du transfert (les vidéos prennent un peu de temps).

Vérification : le dossier doit se trouver à l'emplacement `www/club/` et contenir directement `index.php`.

> Astuce : les fichiers qui commencent par un point (`.htaccess`) protègent les dossiers privés. S'ils n'apparaissent pas dans FileZilla, activez le menu **Serveur**, puis **Forcer l'affichage des fichiers cachés**. Ils doivent bien être envoyés.

## Étape 6 — Créer les deux accès administrateur

Le club a deux comptes d'administration : **contact@nova-serenity.fr** et **fabien.scheltien@nova-serenity.fr**. Chacun reçoit toutes les demandes d'accès et se connecte avec son adresse et son propre mot de passe.

Pour chacun des deux comptes :

1. Ouvrez **https://www.nova-serenity.fr/club/admin/**
2. Saisissez l'adresse du compte, laissez le mot de passe vide et cliquez sur **Entrer**.
3. La page « Première visite » s'affiche. Saisissez :
   - le **mot de passe de la base de données** (celui de l'étape 2) : Fabien en aura besoin pour sa première visite, communiquez-le-lui ou faites cette étape ensemble ;
   - puis le **mot de passe d'administration** de ce compte, deux fois (10 caractères minimum).
4. Mettez l'adresse de l'administration dans vos favoris.

Si l'un de vous oublie son mot de passe, il saisit son adresse sous « Mot de passe oublié ? » : un lien arrive sur sa propre boîte.

Pour ajouter ou retirer un administrateur plus tard, modifiez la liste `admin_emails` dans `app/config.php`.

## Étape 7 — Faire un essai complet

1. Ouvrez **https://www.nova-serenity.fr/club/** et levez le levier.
2. À la fin des vidéos, saisissez une adresse professionnelle à vous (par exemple celle en @nova-serenity.fr).
3. Sur contact@ et sur la boîte de Fabien, vous recevez « Demande d'accès au NOVA Club ». L'un de vous clique sur **Accepter**, puis confirme.
4. L'adresse testée reçoit « Votre place au NOVA Club est réservée ». Cliquez sur **Entrer au club**.
5. Vous êtes dans le club. En revenant plus tard sur /club/, le navigateur vous reconnaîtra.

Si un e-mail n'arrive pas, regardez d'abord dans les **indésirables**, et marquez-le comme « non indésirable ».

---

## Essayer le club sur votre ordinateur (facultatif)

1. Installez **XAMPP** (apachefriends.org), gratuit, pour Windows ou Mac.
2. Ouvrez le panneau XAMPP et cliquez sur **Start** en face d'**Apache** (MySQL n'est pas nécessaire).
3. Copiez le dossier `club` dans le dossier `htdocs` de XAMPP :
   - Windows : `C:\xampp\htdocs\club`
   - Mac : `/Applications/XAMPP/htdocs/club`
4. Dans `club/app/`, renommez `config.local.exemple.php` en **`config.local.php`**.
5. Ouvrez **http://localhost/club/** dans votre navigateur.

En mode essai, aucun e-mail n'est envoyé : ils sont écrits dans `club/data/mail.log`, un fichier que vous ouvrez avec le Bloc-notes. Copiez-y le lien « Accepter », puis le lien « Entrer au club », et collez-les dans le navigateur. L'administration est à **http://localhost/club/admin/** : saisissez contact@nova-serenity.fr, laissez le mot de passe vide et cliquez sur « Entrer ». Lors de la « Première visite », le mot de passe de la base de données est `local`.

Ce fichier `config.local.php` n'est lu que sur votre ordinateur : même envoyé chez OVH par erreur, il est ignoré.

---

## Au quotidien

### Accepter une personne
- **Depuis l'e-mail** : chaque demande arrive sur contact@ et chez Fabien. Le premier qui clique sur **Accepter** et confirme valide l'entrée ; si l'autre clique ensuite, il voit « Déjà parmi les invités ».
- **Pour refuser** : ne faites rien. La personne ne reçoit aucune réponse. Dans l'administration, le bouton **Écarter** retire simplement la demande de votre liste.
- Toutes les demandes en attente sont aussi dans l'onglet **Demandes** de l'administration.

### Gérer les invités (onglet « Invités »)
- **Renvoyer la clé** : si un invité a perdu son e-mail.
- **Retirer l'accès** : l'invité est déconnecté immédiatement, sans être prévenu.
- **Supprimer** : efface définitivement l'adresse (utile si quelqu'un vous le demande, au titre du RGPD).
- Au bout de 6 mois, l'invitation expire d'elle-même : la personne devra se présenter à nouveau, et vous recevrez une nouvelle demande.

### Ajouter un événement (onglet « Événements »)
1. **+ Nouvel événement** : titre, date, horaire, lieu, texte. Laissez une ligne vide pour commencer un nouveau paragraphe.
2. **Enregistrer** : l'événement apparaît aussitôt dans « À venir ».
3. Après la soirée, rouvrez-le et ajoutez les photos (autant que vous voulez, depuis votre téléphone ou votre ordinateur). Elles sont allégées automatiquement.
4. Cliquez sur **Couverture** sous la photo qui doit représenter la soirée.
5. Le lendemain de la date, l'événement passe tout seul dans « Événements passés ».

### Notre histoire (onglet « Notre histoire »)
Ajoutez un à un les visuels du lancement de NOVA : affiches, annonces LinkedIn, invitations, premières photos. Pour chacun : l'image, un titre, une date (facultative) et quelques mots. Ils s'affichent dans une frise, du plus ancien au plus récent, et s'agrandissent au clic.

### Espace de stockage
L'offre Starter dispose de 1 Go. Les photos étant allégées à l'envoi (environ 300 à 500 Ko chacune), vous pouvez en stocker plusieurs centaines. L'espace utilisé est visible sur la page de l'hébergement OVH.

---

## En cas de souci

| Symptôme | Solution |
|---|---|
| Page blanche ou « erreur 500 » sur /club/admin | Vérifiez les 4 lignes de la base de données dans `app/config.php` (apostrophes, pas d'espace en trop), puis renvoyez ce fichier par FileZilla. |
| Les e-mails n'arrivent pas | Regardez dans les indésirables. Vérifiez que la redirection club@ existe (étape 4). |
| « Clé invalide » pour un invité | Il a sans doute cliqué sur un ancien e-mail : qu'il se présente de nouveau à la porte, une nouvelle clé lui sera envoyée automatiquement. |
| Une photo est refusée | Seuls les formats JPEG, PNG et WebP sont acceptés. |
| Vous voulez un lien vers le club depuis le site | Ajoutez simplement un lien vers `/club/` dans le menu ou le pied de page de vos pages. |
