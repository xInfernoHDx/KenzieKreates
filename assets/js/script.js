/**
 * Kenzy Kreates theme scripts.
 * Every block is null-guarded so any single missing element on a template
 * never kills the rest of the script (see the lessons this theme is built on).
 */

// Flag JS availability first: .reveal only hides under html.has-js,
// so a broken or blocked script never blanks the page.
document.documentElement.classList.add('has-js');

/* ---------- Mobile navigation ---------- */

(function () {
  var toggle = document.getElementById('navToggle');
  var drawer = document.getElementById('mobileNav');
  var overlay = document.getElementById('navOverlay');
  var closeBtn = document.getElementById('mobileNavClose');

  if (!toggle || !drawer || !overlay) {
    return;
  }

  function openNav() {
    drawer.hidden = false;
    overlay.hidden = false;
    // Let the browser paint the un-hidden state before transitioning.
    requestAnimationFrame(function () {
      drawer.classList.add('open');
      overlay.classList.add('open');
    });
    toggle.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    drawer.classList.remove('open');
    overlay.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    window.setTimeout(function () {
      drawer.hidden = true;
      overlay.hidden = true;
    }, 450);
  }

  toggle.addEventListener('click', openNav);
  overlay.addEventListener('click', closeNav);
  if (closeBtn) {
    closeBtn.addEventListener('click', closeNav);
  }
  drawer.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', closeNav);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && drawer.classList.contains('open')) {
      closeNav();
    }
  });
})();

/* ---------- Scroll reveal ---------- */

(function () {
  var items = document.querySelectorAll('.reveal');
  if (!items.length) {
    return;
  }

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (reduceMotion.matches || !('IntersectionObserver' in window)) {
    items.forEach(function (el) {
      el.classList.add('visible');
    });
    return;
  }

  var observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );

  items.forEach(function (el) {
    observer.observe(el);
  });
})();

/* ---------- Inquiry form: scroll status message into view ---------- */

(function () {
  var msg = document.querySelector('.form-msg');
  if (!msg) {
    return;
  }
  // The redirect lands on #custom-orders; make sure the result is seen.
  msg.scrollIntoView({ block: 'center', behavior: 'auto' });
})();
