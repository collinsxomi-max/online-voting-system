<?php
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-copy">
            <h1>Secure campus elections with a live voting platform.</h1>
            <p>
                Register, sign in, and cast your vote from one secure election portal built for campus voting.
            </p>
            <div class="hero-buttons">
                <a class="button button-primary view-all" href="<?= $baseUrl ?>/frontend/register.php">Create Account</a>
                <a class="button button-secondary view-all" href="<?= $baseUrl ?>/frontend/results.php">View Results</a>
            </div>
        </div>

        <div class="hero-illustration">
            <img class="hero-ballot-image" src="<?= $baseUrl ?>/assets/images/ballot-box.svg" alt="Ballot box illustration">
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
