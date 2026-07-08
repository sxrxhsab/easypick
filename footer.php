<?php
// footer.php - Pied de page commun à toutes les pages
// (la variable $lang doit être définie AVANT d'inclure ce fichier)
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>EasyPick</h4>
                <ul>
                    <li><a href="a-propos.php?lang=<?= $lang ?>"><?= __('a_propos') ?></a></li>
                    <li><a href="blog.php?lang=<?= $lang ?>"><?= __('blog') ?></a></li>
                    <li><a href="carrieres.php?lang=<?= $lang ?>"><?= __('carrieres') ?></a></li>
                    <li><a href="contact.php?lang=<?= $lang ?>"><?= __('contact') ?></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><?= __('aide') ?></h4>
                <ul>
                    <li><a href="centre-aide.php?lang=<?= $lang ?>">Centre d'aide</a></li>
                    <li><a href="suivi-commande.php?lang=<?= $lang ?>">Suivi de commande</a></li>
                    <li><a href="retours.php?lang=<?= $lang ?>">Retours</a></li>
                    <li><a href="faq.php?lang=<?= $lang ?>">FAQ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><?= __('legal') ?></h4>
                <ul>
                    <li><a href="cgv.php?lang=<?= $lang ?>"><?= __('cgv') ?></a></li>
                    <li><a href="confidentialite.php?lang=<?= $lang ?>"><?= __('confidentialite') ?></a></li>
                    <li><a href="cookies.php?lang=<?= $lang ?>"><?= __('cookies') ?></a></li>
                    <li><a href="mentions-legales.php?lang=<?= $lang ?>"><?= __('mentions_legales') ?></a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><?= __('suivez_nous') ?></h4>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
                <div class="footer-payments">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-apple-pay"></i>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 EasyPick – <?= __('tous_droits_reserves') ?>. <?= __('design_par') ?> <a href="#">Sarah Sabeur</a>.
        </div>
    </div>
</footer>