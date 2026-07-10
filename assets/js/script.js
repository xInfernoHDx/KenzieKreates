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

/* ---------- Cart (menu page only) ---------- */

(function () {
  var items = document.querySelectorAll('.menu-item--orderable[data-item-id]');
  var bar = document.getElementById('cartBar');
  var panel = document.getElementById('cartPanel');
  var overlay = document.getElementById('cartOverlay');
  var openBtn = document.getElementById('cartOpen');
  var closeBtn = document.getElementById('cartClose');
  var linesEl = document.getElementById('cartLines');
  var jsonInput = document.getElementById('cartJson');
  var notesInput = document.getElementById('cartNotesJson');
  var form = document.getElementById('cartForm');
  var submitBtn = document.getElementById('cartSubmit');

  if (!items.length || !bar || !panel || !overlay || !linesEl || !jsonInput || !form) {
    return;
  }

  var STORAGE_KEY = 'kk_cart';
  var NOTES_KEY = 'kk_cart_notes';

  // After a successful order, the server renders the success message: clear the cart.
  if (document.querySelector('.order-msg--success')) {
    try {
      localStorage.removeItem(STORAGE_KEY);
      localStorage.removeItem(NOTES_KEY);
    } catch (e) { /* storage unavailable */ }
  }

  // Catalog from the DOM (names/prices rendered server-side from menu data).
  var catalog = {};
  items.forEach(function (el) {
    catalog[el.getAttribute('data-item-id')] = {
      name: el.getAttribute('data-item-name'),
      price: parseFloat(el.getAttribute('data-item-price')) || 0,
      notePrompt: el.getAttribute('data-item-note-prompt') || '',
      el: el
    };
  });

  var cart = {};
  try {
    var saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    Object.keys(saved).forEach(function (id) {
      // Drop unknown items (menu changed) and bad values.
      var qty = parseInt(saved[id], 10);
      if (catalog[id] && qty > 0) {
        cart[id] = Math.min(999, qty);
      }
    });
  } catch (e) { cart = {}; }

  // Per-item customization notes (only for items whose menu entry asks for one).
  var itemNotes = {};
  try {
    var savedNotes = JSON.parse(localStorage.getItem(NOTES_KEY) || '{}');
    Object.keys(savedNotes).forEach(function (id) {
      if (catalog[id] && catalog[id].notePrompt && typeof savedNotes[id] === 'string' && savedNotes[id]) {
        itemNotes[id] = savedNotes[id].slice(0, 1000);
      }
    });
  } catch (e) { itemNotes = {}; }

  function saveNotes() {
    try { localStorage.setItem(NOTES_KEY, JSON.stringify(itemNotes)); } catch (e) { /* storage unavailable */ }
  }

  function money(n) {
    return '$' + n.toFixed(2);
  }

  function save() {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(cart)); } catch (e) { /* storage unavailable */ }
  }

  function buildLine(id) {
    var entry = catalog[id];
    var li = document.createElement('li');
    li.className = 'cart-line';

    var name = document.createElement('span');
    name.className = 'cart-line-name';
    name.textContent = entry.name;

    var qtyWrap = document.createElement('span');
    qtyWrap.className = 'cart-line-qty';

    var minus = document.createElement('button');
    minus.type = 'button';
    minus.className = 'qty-btn';
    minus.setAttribute('data-line-minus', id);
    minus.setAttribute('aria-label', 'Remove one ' + entry.name);
    minus.innerHTML = '&#8722;';

    var count = document.createElement('span');
    count.className = 'qty-count';
    count.textContent = cart[id];

    var plus = document.createElement('button');
    plus.type = 'button';
    plus.className = 'qty-btn';
    plus.setAttribute('data-line-plus', id);
    plus.setAttribute('aria-label', 'Add one ' + entry.name);
    plus.textContent = '+';

    qtyWrap.appendChild(minus);
    qtyWrap.appendChild(count);
    qtyWrap.appendChild(plus);

    var total = document.createElement('span');
    total.className = 'cart-line-total';
    total.textContent = money(cart[id] * entry.price);

    li.appendChild(name);
    li.appendChild(qtyWrap);
    li.appendChild(total);

    // Customization request box for items that ask for one (e.g. decorated cookies).
    if (entry.notePrompt) {
      var noteLabel = document.createElement('label');
      noteLabel.className = 'cart-line-note';

      var promptSpan = document.createElement('span');
      promptSpan.className = 'cart-line-note-prompt';
      promptSpan.textContent = entry.notePrompt;

      var noteBox = document.createElement('textarea');
      noteBox.rows = 2;
      noteBox.maxLength = 1000;
      noteBox.placeholder = 'Colors, theme, characters, the occasion...';
      noteBox.value = itemNotes[id] || '';
      noteBox.addEventListener('input', function () {
        var v = noteBox.value.slice(0, 1000);
        if (v.trim()) {
          itemNotes[id] = v;
        } else {
          delete itemNotes[id];
        }
        saveNotes();
      });

      noteLabel.appendChild(promptSpan);
      noteLabel.appendChild(noteBox);
      li.appendChild(noteLabel);
    }
    return li;
  }

  function render() {
    var count = 0;
    var total = 0;

    Object.keys(catalog).forEach(function (id) {
      var qty = cart[id] || 0;
      var el = catalog[id].el;
      var badge = el.querySelector('[data-cart-count]');
      if (badge) {
        badge.textContent = qty;
      }
      el.classList.toggle('in-cart', qty > 0);
      count += qty;
      total += qty * catalog[id].price;
    });

    bar.hidden = count === 0;
    var summary = bar.querySelector('[data-cart-summary]');
    if (summary) {
      summary.textContent = count + (count === 1 ? ' item' : ' items') + ' · ' + money(total);
    }

    linesEl.innerHTML = '';
    if (count === 0) {
      var empty = document.createElement('li');
      empty.className = 'cart-empty';
      empty.textContent = 'Your order is empty. Tap treats on the menu to add them.';
      linesEl.appendChild(empty);
    } else {
      Object.keys(cart).forEach(function (id) {
        linesEl.appendChild(buildLine(id));
      });
    }

    var totalEl = panel.querySelector('[data-cart-total]');
    if (totalEl) {
      totalEl.textContent = money(total);
    }

    jsonInput.value = JSON.stringify(cart);
    if (notesInput) {
      notesInput.value = JSON.stringify(itemNotes);
    }
    if (submitBtn) {
      submitBtn.disabled = count === 0;
    }
  }

  function changeQty(id, delta) {
    if (!catalog[id]) { return; }
    var next = (cart[id] || 0) + delta;
    if (next <= 0) {
      delete cart[id];
      delete itemNotes[id];
      saveNotes();
    } else {
      cart[id] = Math.min(999, next);
    }
    save();
    render();
  }

  // Menu rows: steppers, plus click-anywhere-on-the-row to add one.
  items.forEach(function (el) {
    var id = el.getAttribute('data-item-id');
    el.addEventListener('click', function (e) {
      if (e.target.closest('[data-cart-minus]')) {
        changeQty(id, -1);
      } else if (e.target.closest('[data-cart-plus]')) {
        changeQty(id, 1);
      } else {
        changeQty(id, 1);
      }
    });
  });

  // Panel line steppers (delegated: lines are rebuilt on every render).
  linesEl.addEventListener('click', function (e) {
    var minus = e.target.closest('[data-line-minus]');
    var plus = e.target.closest('[data-line-plus]');
    if (minus) {
      changeQty(minus.getAttribute('data-line-minus'), -1);
    } else if (plus) {
      changeQty(plus.getAttribute('data-line-plus'), 1);
    }
  });

  function openPanel() {
    panel.hidden = false;
    overlay.hidden = false;
    requestAnimationFrame(function () {
      panel.classList.add('open');
      overlay.classList.add('open');
    });
    document.body.style.overflow = 'hidden';
  }

  function closePanel() {
    panel.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
    window.setTimeout(function () {
      panel.hidden = true;
      overlay.hidden = true;
    }, 450);
  }

  if (openBtn) { openBtn.addEventListener('click', openPanel); }
  if (closeBtn) { closeBtn.addEventListener('click', closePanel); }
  overlay.addEventListener('click', closePanel);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('open')) {
      closePanel();
    }
  });

  form.addEventListener('submit', function (e) {
    if (!Object.keys(cart).length) {
      e.preventDefault();
    }
    jsonInput.value = JSON.stringify(cart);
    if (notesInput) {
      notesInput.value = JSON.stringify(itemNotes);
    }
  });

  render();
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
