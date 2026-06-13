(function () {
    'use strict';

    // Hamburger toggle
    var hamburger = document.getElementById('hamburger');
    var menu = document.getElementById('navbarMenu');
    if (hamburger && menu) {
        hamburger.addEventListener('click', function () {
            menu.classList.toggle('open');
        });
    }

    // Admin sidebar hamburger
    var adminHamburger = document.getElementById('adminHamburger');
    var sidebar = document.getElementById('sidebar');
    if (adminHamburger && sidebar) {
        adminHamburger.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }

    // Sidebar close
    var sidebarClose = document.getElementById('sidebarClose');
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', function () {
            sidebar.classList.remove('open');
        });
    }

    // Accordion
    var accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(function (header) {
        header.addEventListener('click', function () {
            var body = this.nextElementSibling;
            if (body) {
                body.classList.toggle('open');
            }
        });
    });

    // Open first accordion item by default
    var firstBody = document.querySelector('.accordion-item .accordion-body');
    if (firstBody) {
        firstBody.classList.add('open');
    }

    // Quiz confirmation
    var quizForms = document.querySelectorAll('.quiz-form');
    quizForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var unanswered = false;
            var questions = this.querySelectorAll('.quiz-question');
            questions.forEach(function (q) {
                var checked = q.querySelector('input[type="radio"]:checked');
                if (!checked) {
                    unanswered = true;
                }
            });
            if (unanswered) {
                if (!confirm('Certaines questions n\'ont pas de réponse. Voulez-vous quand même soumettre ?')) {
                    e.preventDefault();
                }
            } else {
                if (!confirm('Soumettre le quiz ?')) {
                    e.preventDefault();
                }
            }
        });
    });

    // Auto-dismiss alerts
    var alerts = document.querySelectorAll('#alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 4000);
    });

    // Lesson sidebar - highlight current
    var currentLesson = document.querySelector('.lesson-nav-item.active');
    if (currentLesson) {
        currentLesson.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
})();
