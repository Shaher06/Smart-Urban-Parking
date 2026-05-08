<?php
require_once BASE_PATH . '/helpers/url_helper.php';
?>
<nav class="navbar navbar-expand-lg navbar-light landing-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= page_url('home') ?>">
            <span class="landing-mark"><i class="bi bi-p-circle-fill"></i></span>
            <span><?= APP_NAME ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?= page_url('home') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= page_url('home') ?>#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= page_url('home') ?>#how">How it works</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= page_url('home') ?>#audiences">For you</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-outline-primary btn-sm px-3" href="<?= page_url('login') ?>">
                        Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm px-3" href="<?= page_url('register') ?>">
                        Register
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

