<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ExamCraft Pro — Professional Cambridge-style exam paper authoring. Auto-numbering MCQs, Cambridge typography presets, drag-and-drop blocks, and one-click PDF export for educators.">
    <title>ExamCraft Pro — Professional Exam Paper Authoring</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>

{{-- ── NAVBAR ──────────────────────────────────────────────────── --}}
<nav id="navbar">
    <div class="container">
        <a href="/" class="nav-logo" aria-label="ExamCraft Pro Home">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 1C2.44772 1 2 1.44772 2 2V12.5C2 13.6046 2.89543 14.5 4 14.5H13C13.5523 14.5 14 14.0523 14 13.5C14 12.9477 13.5523 12.5 13 12.5H4.5C3.94772 12.5 3.5 12.0523 3.5 11.5C3.5 10.9477 3.94772 10.5 4.5 10.5H13C13.5523 10.5 14 10.0523 14 9.5V2C14 1.44772 13.5523 1 13 1H3Z" fill="#C9A84C"/>
                <path d="M12 4L5 11" stroke="#1B2A4A" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M12 8V4H8" stroke="#1B2A4A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="logo-gold">ExamCraft</span><span class="logo-white">Pro</span>
        </a>

        <div class="nav-links">
            <a href="#features" class="nav-link">Features</a>
            <a href="#how-it-works" class="nav-link">How it Works</a>
            <a href="#who-for" class="nav-link">For Schools</a>
            <a href="#cta" class="nav-link">Pricing</a>
        </div>

        <div class="nav-actions">
            <a href="/login" class="btn-login">Log in</a>
            <a href="/register" class="btn-primary">Get Started</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

{{-- Mobile Menu --}}
<div id="mobile-menu">
    <a href="#features">Features</a>
    <a href="#how-it-works">How it Works</a>
    <a href="#who-for">For Schools</a>
    <a href="#cta">Pricing</a>
    <div class="mobile-cta">
        <a href="/login" style="color: rgba(255,255,255,0.8);">Log in</a>
        <a href="/register" style="color: var(--gold); font-weight: 600;">Get Started</a>
    </div>
</div>

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<main>
<section class="hero">
    <div class="container">
        <div class="hero-grid">

            {{-- Paper Preview Card --}}
            <div class="paper-card reveal">
                <div class="paper-card-inner">
                    <div class="paper-card-header">
                        <div class="cambridge-logo-box">
                            <span>Cambridge</span>
                            <span>O Level</span>
                        </div>
                        <div class="paper-card-header-info">
                            <strong>Cambridge Assessment</strong>
                            International Education<br>
                            Cambridge Ordinary Level
                        </div>
                    </div>
                    <div class="paper-card-divider"></div>
                    <div class="paper-card-title">PHYSICS</div>
                    <div class="paper-card-meta">
                        <span>Paper 1 Multiple Choice</span>
                        <span class="paper-card-code">5054/11</span>
                    </div>
                    <div class="paper-card-meta" style="margin-bottom:12px;">
                        <span></span>
                        <span style="font-size:11px;">May/June 2025&ensp;1 hour</span>
                    </div>
                    <div class="paper-card-divider2"></div>
                    <div class="paper-card-materials">
                        Additional Materials: <span class="answer-sheet">Multiple Choice Answer Sheet</span>
                    </div>
                    <div class="paper-card-divider3"></div>
                    <div class="paper-card-instructions-header">READ THESE INSTRUCTIONS FIRST</div>
                    <div class="paper-card-instructions">
                        Write in soft pencil.<br>
                        Do not use staples, paper clips, glue or correction fluid.<br>
                        There are forty questions on this paper. Answer all questions.<br>
                        Each correct answer will score one mark. No negative marking.
                    </div>
                </div>
            </div>

            {{-- Hero Text --}}
            <div class="hero-text">
                <div class="pill reveal">&#10026; Cambridge-Style Exam Authoring</div>
                <h1 class="reveal">Build Professional<br>Exam Papers in Minutes</h1>
                <p class="reveal">Auto-numbering MCQs, Cambridge typography presets, drag-and-drop blocks — and one-click PDF export. Designed for educators who demand precision.</p>
                <div class="hero-ctas reveal">
                    <a href="/register" class="btn-primary btn-primary-lg">Start Building Free &#8594;</a>
                    <a href="#how-it-works" class="btn-secondary">Watch Demo</a>
                </div>
                <p class="hero-trust reveal">Trusted by educators teaching O Level &middot; A Level &middot; IGCSE</p>
            </div>

        </div>
    </div>
</section>

{{-- ── FEATURES ─────────────────────────────────────────────────── --}}
<section class="features" id="features">
    <div class="container">
        <div class="section-header">
            <span class="eyebrow reveal">Core Features</span>
            <h2 class="reveal">Everything you need to craft perfect papers</h2>
        </div>

        <div class="features-grid">

            <article class="feature-card reveal">
                <div class="feature-icon gold">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <text x="3" y="16" font-family="EB Garamond, serif" font-weight="700" font-size="18" fill="currentColor">A</text>
                    </svg>
                </div>
                <h3>Cambridge-Accurate Typography</h3>
                <p>Six presets including O Level, A Level, IGCSE, and Primary. Thirteen CSS variables keep every font size, margin, and line-height exam-accurate.</p>
            </article>

            <article class="feature-card reveal">
                <div class="feature-icon navy">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="14" height="3" rx="1" fill="currentColor" opacity="0.85"/>
                        <rect x="3" y="8.5" width="14" height="3" rx="1" fill="currentColor" opacity="0.55"/>
                        <rect x="3" y="14" width="14" height="3" rx="1" fill="currentColor" opacity="0.25"/>
                        <circle cx="1.5" cy="4.5" r="1.5" fill="currentColor"/>
                        <circle cx="1.5" cy="10" r="1.5" fill="currentColor"/>
                        <circle cx="1.5" cy="15.5" r="1.5" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Modular Block System</h3>
                <p>MCQ, Section headers, Rich text, Images, Tables, and Dividers. Drag to reorder anywhere on the canvas. Click any block to configure it.</p>
            </article>

            <article class="feature-card reveal">
                <div class="feature-icon gold">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <text x="2" y="16" font-family="EB Garamond, serif" font-weight="700" font-size="18" fill="currentColor">#1</text>
                    </svg>
                </div>
                <h3>Smart Auto-Renumbering</h3>
                <p>Add or remove questions anywhere in the paper and numbering updates instantly across the entire document. No manual fixes.</p>
            </article>

            <article class="feature-card reveal">
                <div class="feature-icon crimson">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2V14M10 14L5 9M10 14L15 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 18H18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h3>One-Click PDF Export</h3>
                <p>Export exam-ready PDFs using jsPDF and html2canvas. QR code embedding and Cambridge-accurate margins included.</p>
            </article>

        </div>
    </div>
</section>

{{-- ── BLOCK SYSTEM SHOWCASE ────────────────────────────────────── --}}
<section class="block-showcase" id="blocks">
    <div class="container">
        <div class="section-header">
            <h2 class="reveal">Six block types. One powerful canvas.</h2>
            <p class="section-subtitle reveal">Build any exam structure from our library of professional blocks.</p>
        </div>

        <div class="blocks-grid">

            <article class="block-card reveal">
                <h3>MCQ Block</h3>
                <p>4 mandatory options (A–D) + 2 dynamic (E–F). Three layout modes: 1-column, 2-column, and inline.</p>
            </article>

            <article class="block-card reveal">
                <h3>Section Block</h3>
                <p>Main headers and sub-headers with optional horizontal divider lines below.</p>
            </article>

            <article class="block-card reveal">
                <h3>Text Block</h3>
                <p>Instruction and context blocks with font-size and text-alignment controls.</p>
            </article>

            <article class="block-card reveal">
                <h3>Image Block</h3>
                <p>Independent image blocks with caption support and percentage width scaling.</p>
            </article>

            <article class="block-card reveal">
                <h3>Table Block</h3>
                <p>Dynamic row and column management with specialized variants for exam data.</p>
            </article>

            <article class="block-card reveal">
                <h3>Divider Block</h3>
                <p>Horizontal separators in solid, dashed, or dotted styles to structure sections.</p>
            </article>

        </div>
    </div>
</section>

{{-- ── HOW IT WORKS ──────────────────────────────────────────────── --}}
<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="section-header">
            <h2 class="reveal">From blank page to exam paper — in three steps.</h2>
        </div>

        <div class="steps-row">

            <div class="step-item reveal">
                <div class="step-number">1</div>
                <h3>Configure your paper</h3>
                <p>Enter organization, subject, paper code, duration, date, and exam instructions in the Paper Settings panel on the left sidebar.</p>
            </div>

            <div class="step-connector reveal">
                <div class="step-connector-line"></div>
            </div>

            <div class="step-item reveal">
                <div class="step-number">2</div>
                <h3>Build block by block</h3>
                <p>Add MCQ questions, section headers, text instructions, images, and tables from the toolbar. Drag blocks to reorder them at any time.</p>
            </div>

            <div class="step-connector reveal">
                <div class="step-connector-line"></div>
            </div>

            <div class="step-item reveal">
                <div class="step-number">3</div>
                <h3>Export your paper</h3>
                <p>Hit Print or Export to generate a Cambridge-ready PDF with correct margins, typography, and formatting — ready to hand to students.</p>
            </div>

        </div>
    </div>
</section>

{{-- ── WHO IT'S FOR ──────────────────────────────────────────────── --}}
<section class="who-for" id="who-for">
    <div class="container">
        <div class="section-header">
            <h2 class="reveal">Built for the educators who set the standard.</h2>
        </div>

        <div class="audience-grid">

            <article class="audience-card reveal">
                <h3>Schools &amp; Academies</h3>
                <p>Set uniform exam papers across departments with consistent formatting. Share question banks between teachers.</p>
                <a href="/register" class="audience-link">Get started &#8594;</a>
            </article>

            <article class="audience-card reveal">
                <h3>Private Tutors</h3>
                <p>Create professional-looking practice papers that reflect the real exam experience. Impress students and parents with publication-quality output.</p>
                <a href="/register" class="audience-link">Get started &#8594;</a>
            </article>

            <article class="audience-card reveal">
                <h3>University Departments</h3>
                <p>Handle large question banks, multi-section papers, and diverse typography requirements all within one organized interface.</p>
                <a href="/register" class="audience-link">Get started &#8594;</a>
            </article>

        </div>
    </div>
</section>

{{-- ── CTA BANNER ────────────────────────────────────────────────── --}}
<section class="cta-banner" id="cta">
    <div class="container">
        <h2 class="reveal">Ready to set your first paper?</h2>
        <p class="cta-subtitle reveal">Join hundreds of educators building better exams with ExamCraft Pro.</p>
        <a href="/register" class="btn-primary btn-primary-xl reveal">Get Started — It&rsquo;s Free</a>
        <p class="cta-fine-print reveal">No credit card required. Start building immediately.</p>
    </div>
</section>
</main>

{{-- ── FOOTER ────────────────────────────────────────────────────── --}}
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 1C2.44772 1 2 1.44772 2 2V12.5C2 13.6046 2.89543 14.5 4 14.5H13C13.5523 14.5 14 14.0523 14 13.5C14 12.9477 13.5523 12.5 13 12.5H4.5C3.94772 12.5 3.5 12.0523 3.5 11.5C3.5 10.9477 3.94772 10.5 4.5 10.5H13C13.5523 10.5 14 10.0523 14 9.5V2C14 1.44772 13.5523 1 13 1H3Z" fill="#C9A84C"/>
                        <path d="M12 4L5 11" stroke="#1B2A4A" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M12 8V4H8" stroke="#1B2A4A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span style="font-family:'EB Garamond',serif;font-weight:700;font-size:20px;color:#C9A84C;">ExamCraft</span><span style="font-family:'Inter',sans-serif;font-weight:500;font-size:16px;color:#fff;">Pro</span>
                </div>
                <p class="footer-tagline">Professional exam authoring for Cambridge educators.</p>
            </div>

            <div class="footer-links">
                <div class="footer-col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How it Works</a></li>
                        <li><a href="#blocks">Block System</a></li>
                        <li><a href="#features">Typography Presets</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">GitHub</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} ExamCraft Pro. Built for educators. Powered by Laravel &amp; Vue.</p>
        </div>
    </div>
</footer>

{{-- ── JAVASCRIPT ─────────────────────────────────────────────────── --}}
<script>
(function() {
    // ── Navbar scroll effect
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        navbar.classList.toggle('scrolled', window.scrollY > 80);
    });

    // ── Mobile hamburger toggle
    document.getElementById('hamburger').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('open');
    });

    // Close mobile menu when a link is clicked
    var mobileLinks = document.querySelectorAll('#mobile-menu a');
    mobileLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.remove('open');
        });
    });

    // ── Scroll reveal (IntersectionObserver)
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(function(el) {
        observer.observe(el);
    });

    // ── Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
})();
</script>

</body>
</html>
