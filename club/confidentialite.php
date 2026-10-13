<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/layout.php';
page_start('Confidentialité');
?>
<main class="c-prose">
  <p class="c-kicker">NOVA Club</p>
  <h1>Vos données, avec discrétion</h1>
  <h2>Ce que nous collectons</h2>
  <p>Uniquement votre adresse e-mail professionnelle, la date de votre demande et, si elle est acceptée, la durée de votre invitation.</p>
  <h2>Pourquoi</h2>
  <p>Pour vérifier votre identité, vous donner accès au NOVA Club et vous envoyer vos clés d'accès. Votre adresse n'est ni revendue, ni utilisée à d'autres fins.</p>
  <h2>Combien de temps</h2>
  <p>Votre invitation dure <?= (int)cfg('access_months') ?> mois. À son terme, vos données ne servent plus et peuvent être supprimées sur simple demande.</p>
  <h2>Le cookie du club</h2>
  <p>Un seul cookie, indispensable, permet à votre navigateur de vous reconnaître pendant la durée de votre invitation. Aucun cookie publicitaire ni de mesure d'audience n'est utilisé sur le club.</p>
  <h2>Vos droits</h2>
  <p>Vous pouvez demander à consulter, corriger ou supprimer vos données en écrivant à <a href="mailto:<?= h(cfg('mail_from')) ?>"><?= h(cfg('mail_from')) ?></a>. Vous pouvez aussi saisir la CNIL.</p>
  <h2>Responsable</h2>
  <p>NOVA Serenity, SAS au capital de 1 000 € — 60 rue François 1er, 75008 Paris — RCS Paris 993 754 431.</p>
  <p style="margin-top:2.4rem"><a class="c-btn" href="<?= h(current_member() ? base_url('club.php') : base_url()) ?>">Retour</a></p>
</main>
<?php page_end(false);
