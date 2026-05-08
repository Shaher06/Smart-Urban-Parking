<?php require_once BASE_PATH . '/views/layouts/header_public.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar_public.php'; ?>

<header class="landing-hero">
    <div class="container py-5">
        <?= render_flash() ?>

        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="landing-kicker mb-3">
                    <i class="bi bi-geo-alt"></i>
                    Real-time city parking marketplace
                </div>
                <h1 class="display-5 fw-bold mb-3">
                    Find, Reserve, and Manage Smart Parking Spaces
                </h1>
                <p class="lead text-muted mb-4">
                    Book parking in minutes. Payments are held securely in escrow and released on checkout.
                    Owners track earnings and request payouts. Admins manage zones, fines, and system health.
                </p>

                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary btn-lg px-4" href="<?= page_url('register') ?>">
                        Get Started <i class="bi bi-arrow-right"></i>
                    </a>
                    <a class="btn btn-outline-primary btn-lg px-4" href="<?= page_url('login') ?>">
                        Login
                    </a>
                    <a class="btn btn-outline-secondary btn-lg px-4" href="<?= page_url('home') ?>#how">
                        How it works
                    </a>
                </div>

                <div class="mt-4 small text-muted">
                    Tip: for demo, you can login using seeded accounts from the database import.
                </div>
            </div>
            <div class="col-lg-5">
                <div class="landing-panel">
                    <div class="landing-panel-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="icon-circle bg-warning text-dark">
                                <i class="bi bi-search fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Search & filter instantly</div>
                                <div class="text-muted small">EV support, size constraints, availability, and pricing.</div>
                            </div>
                        </div>
                    </div>
                    <div class="landing-panel-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="bi bi-shield-lock fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Secure escrow payments</div>
                                <div class="text-muted small">Funds are held until checkout is completed.</div>
                            </div>
                        </div>
                    </div>
                    <div class="landing-panel-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="icon-circle bg-success text-white">
                                <i class="bi bi-qr-code-scan fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">QR check-in / check-out</div>
                                <div class="text-muted small">Fast status transitions for a clean demo flow.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section id="how" class="container py-5">
    <div class="row align-items-end g-3 mb-4">
        <div class="col-lg-7">
            <h2 class="landing-section-title fw-bold mb-2">How it works</h2>
            <p class="landing-section-subtitle text-muted mb-0">
                Search, reserve, check-in, then checkout. Clear steps that make the demo easy to follow.
            </p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="landing-step card h-100 p-4">
                <div class="landing-step-icon"><i class="bi bi-search"></i></div>
                <div class="fw-semibold">Search</div>
                <div class="text-muted small">Browse and filter spots by city, EV, and constraints.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="landing-step card h-100 p-4">
                <div class="landing-step-icon"><i class="bi bi-calendar2-check"></i></div>
                <div class="fw-semibold">Reserve</div>
                <div class="text-muted small">Book instantly with overlap protection and buffer time.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="landing-step card h-100 p-4">
                <div class="landing-step-icon"><i class="bi bi-qr-code-scan"></i></div>
                <div class="fw-semibold">Check-in</div>
                <div class="text-muted small">Use the QR flow to start your session.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="landing-step card h-100 p-4">
                <div class="landing-step-icon"><i class="bi bi-box-arrow-right"></i></div>
                <div class="fw-semibold">Checkout</div>
                <div class="text-muted small">Escrow releases on checkout. Refunds apply on cancellation rules.</div>
            </div>
        </div>
    </div>
</section>

<hr class="divider-soft m-0">

<section id="audiences" class="container py-5">
    <div class="row align-items-end g-3 mb-4">
        <div class="col-lg-7">
            <h2 class="landing-section-title fw-bold mb-2">Built for every role</h2>
            <p class="landing-section-subtitle text-muted mb-0">
                Drivers reserve faster, owners earn confidently, and admins keep the city running smoothly.
            </p>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                    <i class="bi bi-car-front"></i> For Drivers
                </div>
                <ul class="text-muted small mb-0">
                    <li>Browse and book available parking spots</li>
                    <li>Escrow payments + cancellation refunds</li>
                    <li>Reservations, vehicles, favorites, waitlist</li>
                    <li>Fines, appeals, notifications</li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                    <i class="bi bi-building"></i> For Space Owners
                </div>
                <ul class="text-muted small mb-0">
                    <li>List and manage parking spots</li>
                    <li>Availability & pricing tools</li>
                    <li>Earnings dashboard and payouts</li>
                    <li>Verification workflow and reviews</li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4">
                <div class="d-flex align-items-center gap-2 fw-bold mb-2">
                    <i class="bi bi-bank"></i> For Admins
                </div>
                <ul class="text-muted small mb-0">
                    <li>User management, roles, and blacklists</li>
                    <li>Fines & appeals handling</li>
                    <li>Event zones and emergency operations</li>
                    <li>Reports, audits, and health monitoring</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<hr class="divider-soft m-0">

<section id="features" class="container py-5">
    <div class="row align-items-end g-3 mb-4">
        <div class="col-lg-6">
            <h2 class="landing-section-title fw-bold mb-2">Key features</h2>
            <p class="landing-section-subtitle text-muted mb-0">
                Practical features that support a clean, stable university demo.
            </p>
        </div>
        <div class="col-lg-6 text-lg-end">
            <a class="btn btn-primary" href="<?= page_url('register') ?>">
                Create an account <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-broadcast"></i> Real-time availability</div></div>
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-qr-code-scan"></i> QR check-in/check-out</div></div>
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-shield-lock"></i> Secure escrow payments</div></div>
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-graph-up"></i> Owner earnings dashboard</div></div>
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-exclamation-triangle"></i> Automated fines</div></div>
        <div class="col-md-4"><div class="feature-chip"><i class="bi bi-star"></i> Reviews and ratings</div></div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer_public.php'; ?>

