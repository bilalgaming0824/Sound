<?php
/**
 * Render the footer and closing tags. Call render_footer() at the bottom of each page.
 */
function render_footer(): void {
    $year = date('Y');
?>
</main>

<!-- FOOTER -->
<footer class="site-footer mt-5">
    <div class="container-fluid px-3 px-lg-4 py-5">
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2 mb-2" href="<?= url('index.php') ?>">
                    <span class="brand-logo"><i class="bi bi-soundwave"></i></span>
                    <span class="brand-name">SOUND</span>
                </a>
                <p class="footer-brand-tagline">FEEL THE MUSIC</p>
                <p class="footer-desc">Your home for music and video entertainment. Stream the latest releases across English and regional languages, organized by album, artist, year, genre and language.</p>
                <div class="footer-socials">
                    <a href="#" class="footer-social-btn footer-social-fb" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social-btn footer-social-tw" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="footer-social-btn footer-social-ig" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-social-btn footer-social-yt" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="footer-social-btn footer-social-tk" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Browse</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('music.php') ?>">Music</a></li>
                    <li><a href="<?= url('videos.php') ?>">Videos</a></li>
                    <li><a href="<?= url('albums.php') ?>">Albums</a></li>
                    <li><a href="<?= url('artists.php') ?>">Artists</a></li>
                    <li><a href="<?= url('categories.php') ?>">Categories</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Account</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('login.php') ?>">Sign In</a></li>
                    <li><a href="<?= url('register.php') ?>">Create Account</a></li>
                    <li><a href="<?= url('dashboard.php') ?>">Dashboard</a></li>
                    <li><a href="<?= url('playlists.php') ?>">Playlists</a></li>
                    <li><a href="<?= url('contact.php') ?>">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="footer-title">Company</h6>
                <ul class="footer-links">
                    <li><a href="<?= url('about.php') ?>">About Us</a></li>
                    <li><a href="<?= url('faq.php') ?>">FAQ</a></li>
                    <li><a href="<?= url('terms.php') ?>">Terms &amp; Privacy</a></li>
                    <li><a href="<?= url('search.php') ?>">Search</a></li>
                    <li><a href="<?= url('sitemap.php') ?>">Sitemap</a></li>
                </ul>
            </div>
            <div class="col-lg-2">
                <h6 class="footer-stay-title">Stay Updated</h6>
                <p class="footer-stay-sub">Get the latest releases and trending tracks in your inbox.</p>
                <form class="newsletter-form" id="newsletterForm">
                    <div class="footer-email-row">
                        <input type="email" id="newsletterEmail" placeholder="Your email" required>
                        <button type="submit" aria-label="Subscribe"><i class="bi bi-arrow-right"></i></button>
                    </div>
                    <div id="newsletterMsg" class="form-text mt-2"></div>
                </form>
            </div>
        </div>
        <hr class="footer-divider my-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <p class="small text-secondary mb-0">&copy; <?= $year ?> SOUND Entertainment. All rights reserved.</p>
            <p class="small text-secondary mb-0">Built with <i class="bi bi-heart-fill text-danger"></i> for music lovers</p>
        </div>
    </div>
</footer>

<!-- BACK TO TOP -->
<button id="backToTop" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
<?php
}
