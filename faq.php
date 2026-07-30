<?php
require_once __DIR__ . '/includes/functions.php';
render_header('FAQ', 'faq', 'Frequently asked questions about SOUND.');
$faqs = [
    ['q' => 'Is SOUND free to use?', 'a' => 'Yes! SOUND is completely free. You can stream music and videos, create playlists, and rate songs without any payment.'],
    ['q' => 'Do I need to create an account?', 'a' => 'You can browse without an account, but to save favourites, create playlists, rate songs, and post reviews, you need to sign up for a free account.'],
    ['q' => 'How do I create a playlist?', 'a' => 'Sign in, go to Playlists, click "Create Playlist", give it a name, then add songs and videos from any detail page using the "Add to Playlist" button.'],
    ['q' => 'Can I download songs or videos?', 'a' => 'Songs have a download button on their detail page. Videos are streamed online and cannot be downloaded.'],
    ['q' => 'How do ratings work?', 'a' => 'You can rate any song or video from 1 to 5 stars. Your rating is combined with others to show an average rating.'],
    ['q' => 'I forgot my password. What do I do?', 'a' => 'Click "Forgot password?" on the sign-in page, enter your email, and follow the reset link to set a new password.'],
    ['q' => 'How do I contact support?', 'a' => 'Use the Contact page to send us a message. We typically respond within 24-48 hours.'],
];
?>
<div class="container-fluid px-3 px-lg-4 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="section-title mb-4">Frequently Asked Questions</h1>
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $i => $f): ?>
                    <div class="accordion-item mb-2" style="background:var(--card);border:1px solid var(--border);border-radius:12px !important;overflow:hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent text-white fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>" style="font-size:0.95rem">
                                <?= e($f['q']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small"><?= e($f['a']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
