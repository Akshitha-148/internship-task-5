```php
<?php
session_start();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<style>

/* =====================================================
   EDUCONNECT HOME PAGE
   MODERN PURPLE + PINK + CORAL THEME
===================================================== */

:root {
    --dark: #171329;
    --dark-purple: #24163f;
    --purple: #7c3aed;
    --violet: #a855f7;
    --pink: #ec4899;
    --coral: #ff6b5f;
    --cream: #fffaf5;
    --text: #29223b;
    --muted: #746b82;
    --white: #ffffff;
}

/* =====================================================
   HERO
===================================================== */

.ec-hero {
    min-height: 650px;
    position: relative;
    overflow: hidden;

    background:
        radial-gradient(
            circle at 80% 25%,
            rgba(168, 85, 247, 0.40),
            transparent 30%
        ),
        radial-gradient(
            circle at 15% 85%,
            rgba(255, 107, 95, 0.25),
            transparent 30%
        ),
        linear-gradient(
            135deg,
            #171329 0%,
            #281746 48%,
            #542b82 100%
        );

    color: white;

    display: flex;
    align-items: center;
}

/* Decorative circles */

.ec-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.ec-circle-one {
    width: 320px;
    height: 320px;
    top: -160px;
    right: -80px;
    background: rgba(168, 85, 247, 0.12);
}

.ec-circle-two {
    width: 240px;
    height: 240px;
    bottom: -130px;
    left: -80px;
    background: rgba(255, 107, 95, 0.10);
}

.ec-hero-content {
    position: relative;
    z-index: 2;
}

.ec-welcome {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 8px 17px;

    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 50px;

    background: rgba(255,255,255,0.08);

    color: #f0dfff;

    font-size: 14px;
    font-weight: 600;

    margin-bottom: 24px;
}

.ec-welcome span {
    color: #ff9b8f;
    font-size: 18px;
}

.ec-hero h1 {
    font-size: clamp(3rem, 7vw, 5.3rem);

    line-height: 1.02;

    font-weight: 900;

    letter-spacing: -2px;

    margin-bottom: 25px;
}

.ec-highlight {
    background:
        linear-gradient(
            90deg,
            #c084fc,
            #f472b6,
            #ff8b7e
        );

    -webkit-background-clip: text;
    background-clip: text;

    color: transparent;
}

.ec-description {
    max-width: 650px;

    color: #ddd5ea;

    font-size: 1.08rem;

    line-height: 1.9;

    margin-bottom: 32px;
}

/* HERO BUTTONS */

.ec-buttons {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.ec-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #a855f7
        );

    color: white !important;

    padding: 13px 25px;

    border-radius: 10px;

    font-weight: 700;

    box-shadow:
        0 12px 30px rgba(124,58,237,0.35);

    transition: 0.3s;
}

.ec-btn-primary:hover {
    color: white !important;

    transform: translateY(-3px);

    box-shadow:
        0 17px 38px rgba(168,85,247,0.45);
}

.ec-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 9px;

    border: 1px solid rgba(255,255,255,0.4);

    color: white !important;

    padding: 13px 25px;

    border-radius: 10px;

    font-weight: 600;

    transition: 0.3s;
}

.ec-btn-secondary:hover {
    color: white !important;

    background: rgba(255,255,255,0.10);

    border-color: white;

    transform: translateY(-3px);
}

/* =====================================================
   HERO RIGHT CARD
===================================================== */

.ec-visual {
    position: relative;
    z-index: 2;
}

.ec-main-card {
    background: rgba(255,255,255,0.10);

    border: 1px solid rgba(255,255,255,0.18);

    border-radius: 28px;

    padding: 30px;

    backdrop-filter: blur(15px);

    box-shadow:
        0 30px 80px rgba(0,0,0,0.30);

    transform: rotate(2deg);

    transition: 0.4s;
}

.ec-main-card:hover {
    transform: rotate(0deg) translateY(-8px);
}

.ec-learning-icon {
    width: 90px;
    height: 90px;

    border-radius: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    background:
        linear-gradient(
            135deg,
            #7c3aed,
            #ec4899
        );

    font-size: 42px;

    margin-bottom: 24px;

    box-shadow:
        0 15px 35px rgba(168,85,247,0.35);
}

.ec-main-card h3 {
    font-size: 25px;
    font-weight: 800;
}

.ec-main-card p {
    color: #ddd5ea;
}

.ec-mini-stats {
    display: flex;
    gap: 12px;

    margin-top: 25px;
}

.ec-mini-stat {
    flex: 1;

    background: rgba(255,255,255,0.08);

    border-radius: 14px;

    padding: 15px;

    text-align: center;
}

.ec-mini-stat strong {
    display: block;

    font-size: 20px;
}

.ec-mini-stat span {
    color: #cfc5df;

    font-size: 12px;
}

/* =====================================================
   FEATURES SECTION
===================================================== */

.ec-features {
    background: var(--cream);

    padding: 90px 0;
}

.ec-section-heading {
    text-align: center;

    margin-bottom: 50px;
}

.ec-tag {
    display: inline-block;

    background: #f3e8ff;

    color: #7e22ce;

    padding: 7px 16px;

    border-radius: 50px;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 14px;
}

.ec-section-heading h2 {
    color: var(--dark);

    font-size: 38px;

    font-weight: 900;

    margin-bottom: 12px;
}

.ec-section-heading p {
    color: var(--muted);

    max-width: 650px;

    margin: auto;
}

/* =====================================================
   FEATURE CARDS
===================================================== */

.ec-feature {
    height: 100%;

    background: white;

    border: 1px solid #eee7f7;

    border-radius: 22px;

    overflow: hidden;

    box-shadow:
        0 10px 30px rgba(55,35,90,0.07);

    transition:
        transform 0.35s ease,
        box-shadow 0.35s ease;
}

.ec-feature:hover {
    transform: translateY(-9px);

    box-shadow:
        0 22px 45px rgba(76,38,119,0.15);
}

/* FEATURE IMAGE */

.ec-feature-image {
    width: 100%;

    height: 190px;

    overflow: hidden;

    position: relative;
}

.ec-feature-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform 0.5s ease;
}

.ec-feature:hover .ec-feature-image img {
    transform: scale(1.08);
}

/* Image overlay */

.ec-feature-image::after {
    content: "";

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            to top,
            rgba(23,19,41,0.25),
            transparent 55%
        );

    pointer-events: none;
}

.ec-feature-content {
    padding: 28px;
}

.ec-feature h4 {
    color: var(--dark);

    font-size: 22px;

    font-weight: 800;

    margin-bottom: 12px;
}

.ec-feature p {
    color: var(--muted);

    line-height: 1.8;

    margin-bottom: 20px;
}

.ec-feature-link {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    color: var(--purple);

    font-weight: 700;

    font-size: 14px;
}

.ec-feature-link:hover {
    color: var(--pink);
}

/* =====================================================
   STATISTICS
===================================================== */

.ec-stats {
    background: var(--dark);

    color: white;

    padding: 70px 0;
}

.ec-stat-box {
    text-align: center;
}

.ec-stat-box i {
    color: #c084fc;

    font-size: 28px;
}

.ec-stat-box h3 {
    font-size: 34px;

    font-weight: 900;

    margin: 8px 0;
}

.ec-stat-box p {
    color: #aaa0bc;

    margin: 0;
}

/* =====================================================
   CTA
===================================================== */

.ec-cta {
    padding: 85px 20px;

    text-align: center;

    color: white;

    background:
        radial-gradient(
            circle at 15% 40%,
            rgba(168,85,247,0.35),
            transparent 30%
        ),

        linear-gradient(
            135deg,
            #3b1d5c,
            #7c3aed
        );
}

.ec-cta h2 {
    font-size: 40px;

    font-weight: 900;

    margin-bottom: 12px;
}

.ec-cta p {
    color: #eee6f6;

    max-width: 620px;

    margin: 0 auto 30px;
}

/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 991px) {

    .ec-hero {
        padding: 80px 15px;
    }

    .ec-visual {
        margin-top: 50px;
    }
}

@media (max-width: 576px) {

    .ec-hero h1 {
        font-size: 2.7rem;
    }

    .ec-buttons {
        flex-direction: column;
    }

    .ec-btn-primary,
    .ec-btn-secondary {
        justify-content: center;
    }

    .ec-section-heading h2 {
        font-size: 30px;
    }

    .ec-cta h2 {
        font-size: 30px;
    }

    .ec-feature-image {
        height: 170px;
    }
}

</style>


<!-- =====================================================
     HERO SECTION
===================================================== -->

<section class="ec-hero">

    <div class="ec-circle ec-circle-one"></div>
    <div class="ec-circle ec-circle-two"></div>

    <div class="container">

        <div class="row align-items-center">

            <!-- LEFT SIDE -->
            <div class="col-lg-7">

                <div class="ec-hero-content">

                    <div class="ec-welcome">
                        <span>✦</span>
                        Welcome to EduConnect
                    </div>

                    <h1>
                        Learn.
                        <span class="ec-highlight">Grow.</span>
                        Succeed.
                    </h1>

                    <p class="ec-description">
                        Discover powerful courses, build practical skills,
                        and take your learning journey to the next level
                        with EduConnect.
                    </p>

                    <div class="ec-buttons">

                        <a href="courses.php"
                           class="ec-btn-primary">

                            <i class="bi bi-compass"></i>

                            Explore Courses

                        </a>


                        <?php if (!isset($_SESSION['user_id'])): ?>

                            <a href="auth/register.php"
                               class="ec-btn-secondary">

                                <i class="bi bi-person-plus"></i>

                                Join EduConnect

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="col-lg-5">

                <div class="ec-visual">

                    <div class="ec-main-card">

                        <div class="ec-learning-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>

                        <h3>
                            Your Learning Journey
                        </h3>

                        <p>
                            Explore technology courses and learn
                            at your own pace.
                        </p>

                        <div class="ec-mini-stats">

                            <div class="ec-mini-stat">
                                <strong>18+</strong>
                                <span>Courses</span>
                            </div>

                            <div class="ec-mini-stat">
                                <strong>24/7</strong>
                                <span>Learning</span>
                            </div>

                            <div class="ec-mini-stat">
                                <strong>100%</strong>
                                <span>Online</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     FEATURES SECTION
===================================================== -->

<section class="ec-features">

    <div class="container">

        <div class="ec-section-heading">

            <div class="ec-tag">
                WHY EDUCONNECT?
            </div>

            <h2>
                Everything You Need to Learn
            </h2>

            <p>
                A simple and engaging learning platform designed
                to help students build useful technical skills.
            </p>

        </div>


        <div class="row g-4">

            <!-- =========================================
                 FEATURE 1
            ========================================== -->

            <div class="col-lg-4 col-md-6">

                <div class="ec-feature">

                    <div class="ec-feature-image">

                        <img
                            src="images/learning-skills.jpg"
                            alt="Learn New Skills">

                    </div>


                    <div class="ec-feature-content">

                        <h4>
                            Learn New Skills
                        </h4>

                        <p>
                            Explore programming, web development,
                            databases, AI, cybersecurity and other
                            technology-focused courses.
                        </p>

                        <a href="courses.php"
                           class="ec-feature-link">

                            Explore Courses
                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 FEATURE 2
            ========================================== -->

            <div class="col-lg-4 col-md-6">

                <div class="ec-feature">

                    <div class="ec-feature-image">

                        <img
                            src="images/learn-pace.jpg"
                            alt="Learn at Your Pace">

                    </div>


                    <div class="ec-feature-content">

                        <h4>
                            Learn at Your Pace
                        </h4>

                        <p>
                            Browse available courses and choose
                            the subjects that match your interests
                            and career goals.
                        </p>

                        <a href="courses.php"
                           class="ec-feature-link">

                            Find Your Course
                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>


            <!-- =========================================
                 FEATURE 3
            ========================================== -->

            <div class="col-lg-4 col-md-6">

                <div class="ec-feature">

                    <div class="ec-feature-image">

                        <img
                            src="images/track-learning.jpg"
                            alt="Track Your Learning">

                    </div>


                    <div class="ec-feature-content">

                        <h4>
                            Track Your Learning
                        </h4>

                        <p>
                            Keep your enrolled courses organized
                            and monitor your learning journey
                            through your student dashboard.
                        </p>
```
