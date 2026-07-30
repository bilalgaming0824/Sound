// SOUND — common JavaScript
(function () {
  'use strict';

  var BASE_URL = window.BASE_URL || '';
  var placeholderImg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"><rect width="36" height="36" rx="8" fill="%23161628"/><text x="50%" y="55%" font-size="14" fill="%238b93a7" text-anchor="middle">♪</text></svg>';

  // ---- Page Loader ----
  window.addEventListener('load', function () {
    var loader = document.getElementById('pageLoader');
    if (loader) {
      setTimeout(function () { loader.classList.add('hidden'); }, 300);
    }
  });

  // ---- Toast System ----
  window.showToast = function (message, type) {
    type = type || 'success';
    var container = document.querySelector('.toast-container-custom');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container-custom';
      document.body.appendChild(container);
    }
    var icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
    var colors = { success: '#34D399', error: '#FF4D6D', info: '#3B82F6' };
    var toast = document.createElement('div');
    toast.className = 'toast-custom ' + type;
    toast.innerHTML = '<i class="bi ' + (icons[type] || icons.success) + '" style="color:' + (colors[type] || colors.success) + ';font-size:1.1rem"></i><span>' + escapeHtml(message) + '</span>';
    container.appendChild(toast);
    setTimeout(function () {
      toast.classList.add('removing');
      setTimeout(function () { toast.remove(); }, 300);
    }, 3500);
  };

  // ---- Display flash messages as toasts ----
  document.addEventListener('DOMContentLoaded', function () {
    var flashData = document.getElementById('flashData');
    if (flashData) {
      try {
        var flashes = JSON.parse(flashData.textContent);
        for (var type in flashes) {
          if (flashes.hasOwnProperty(type)) {
            flashes[type].forEach(function (msg) { window.showToast(msg, type); });
          }
        }
      } catch (e) {}
    }
  });

  // ---- Back to top ----
  var backBtn = document.getElementById('backToTop');
  if (backBtn) {
    window.addEventListener('scroll', function () {
      backBtn.classList.toggle('show', window.scrollY > 400);
    });
    backBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ---- Newsletter (fake submit) ----
  var nlForm = document.getElementById('newsletterForm');
  if (nlForm) {
    nlForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = document.getElementById('newsletterEmail').value.trim();
      var msg = document.getElementById('newsletterMsg');
      if (!email) return;
      var formData = new FormData();
      formData.append('email', email);
      fetch(BASE_URL + '/api/newsletter.php', { method: 'POST', body: formData })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          msg.textContent = data.message;
          msg.className = 'form-text mt-2 ' + (data.success ? 'text-success' : 'text-danger');
          if (data.success) nlForm.reset();
          window.showToast(data.message, data.success ? 'success' : 'error');
        })
        .catch(function () {
          msg.textContent = 'Something went wrong. Try again.';
          msg.className = 'form-text mt-2 text-danger';
          window.showToast('Something went wrong. Try again.', 'error');
        });
    });
  }

  // ---- Live search suggestions ----
  var searchInput = document.getElementById('globalSearch');
  var sugBox = document.getElementById('searchSuggestions');
  if (searchInput && sugBox) {
    var timer = null;
    searchInput.addEventListener('input', function () {
      var q = searchInput.value.trim();
      clearTimeout(timer);
      if (q.length < 2) {
        sugBox.classList.remove('show');
        sugBox.innerHTML = '';
        return;
      }
      timer = setTimeout(function () {
        fetch(BASE_URL + '/api/search_suggest.php?q=' + encodeURIComponent(q))
          .then(function (r) { return r.json(); })
          .then(function (items) {
            if (!items.length) {
              sugBox.classList.remove('show');
              sugBox.innerHTML = '';
              return;
            }
            sugBox.innerHTML = items.map(function (it) {
              var icon = it.type === 'video' ? 'bi-play-btn' : 'bi-music-note-beamed';
              var link = BASE_URL + '/' + (it.type === 'video' ? 'video_detail.php' : 'song_detail.php') + '?id=' + it.id;
              return '<a class="sug-item" href="' + link + '">' +
                '<img src="' + (it.image || placeholderImg) + '" alt="">' +
                '<div><div class="sug-title">' + escapeHtml(it.title) + '</div>' +
                '<div class="sug-meta"><i class="bi ' + icon + ' me-1"></i>' + escapeHtml(it.meta) + '</div></div>' +
                '</a>';
            }).join('');
            sugBox.classList.add('show');
          }).catch(function () { sugBox.classList.remove('show'); });
      }, 250);
    });
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.search-form')) sugBox.classList.remove('show');
    });
  }

  // ---- Star rating input ----
  document.querySelectorAll('[data-star-input]').forEach(function (wrap) {
    var input = document.getElementById(wrap.getAttribute('data-star-input'));
    var max = parseInt(wrap.getAttribute('data-max') || '5', 10);
    var value = parseInt(input.value || '0', 10);
    renderStars(wrap, value, max);
    wrap.querySelectorAll('button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        value = parseInt(btn.getAttribute('data-value'), 10);
        input.value = value;
        renderStars(wrap, value, max);
      });
    });
  });

  function renderStars(wrap, value, max) {
    wrap.querySelectorAll('button').forEach(function (btn) {
      var v = parseInt(btn.getAttribute('data-value'), 10);
      var icon = btn.querySelector('i.bi');
      if (!icon) return;
      var isFilled = v <= value;
      icon.classList.toggle('bi-star-fill', isFilled);
      icon.classList.toggle('bi-star', !isFilled);
    });
  }

  function escapeHtml(s) {
    var d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  // ---- Favourite badge count ----
  var favBadge = document.getElementById('favBadge');
  if (favBadge) {
    fetch(BASE_URL + '/api/favourite.php?action=count', { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && typeof data.count !== 'undefined') {
          favBadge.textContent = data.count > 99 ? '99+' : data.count;
          favBadge.style.display = data.count > 0 ? 'flex' : 'none';
        }
      })
      .catch(function () { favBadge.style.display = 'none'; });
  }
})();

// ---- Scroll-triggered entrance animations ----
(function () {
  if (!('IntersectionObserver' in window)) return;
  var els = document.querySelectorAll('.card-media, .section-title, .hero-featured, .chip, .empty-state, .admin-card');
  els.forEach(function (el) { el.classList.add('reveal'); });
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var idx = Array.prototype.indexOf.call(entry.target.parentElement.children, entry.target);
        entry.target.style.transitionDelay = (idx % 8) * 60 + 'ms';
        entry.target.classList.add('reveal-in');
        io.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  els.forEach(function (el) { io.observe(el); });
})();

// ---- Latest Music: Favourite + Play helpers ----
window.toggleFavourite = function (btn) {
  var id = btn.getAttribute('data-id');
  var type = btn.getAttribute('data-type');
  if (!id || !type) return;
  btn.classList.toggle('fav-active');
  var active = btn.classList.contains('fav-active');
  fetch(BASE_URL + '/api/favourite.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=' + (active ? 'add' : 'remove') + '&id=' + encodeURIComponent(id) + '&type=' + encodeURIComponent(type)
  }).then(function (r) { return r.json(); }).then(function (data) {
    if (window.showToast) window.showToast(active ? 'Added to favourites' : 'Removed from favourites', active ? 'success' : 'info');
    var badge = document.getElementById('favBadge');
    if (badge && typeof data.count !== 'undefined') {
      badge.textContent = data.count > 99 ? '99+' : data.count;
      badge.style.display = data.count > 0 ? 'flex' : 'none';
    }
  }).catch(function () { if (window.showToast) window.showToast('Could not update favourite', 'error'); });
};

window.playSong = function (id, title, artist, img) {
  window.location.href = BASE_URL + '/song_detail.php?id=' + encodeURIComponent(id);
};

// ---- Add to Playlist (detail pages) ----
(function () {
  document.addEventListener('click', function (e) {
    var link = e.target.closest('.add-to-pl');
    if (!link) return;
    e.preventDefault();
    var plId = link.getAttribute('data-pl');
    var type = link.getAttribute('data-type');
    var id = link.getAttribute('data-id');
    var csrf = link.getAttribute('data-csrf');
    fetch(BASE_URL + '/api/playlist_add.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=add&playlist_id=' + encodeURIComponent(plId) + '&media_type=' + encodeURIComponent(type) + '&media_id=' + encodeURIComponent(id) + '&csrf=' + encodeURIComponent(csrf)
    }).then(function (r) { return r.json(); }).then(function (data) {
      if (window.showToast) window.showToast(data.message || data.error || 'Done', data.success ? 'success' : 'error');
    }).catch(function () { if (window.showToast) window.showToast('Could not add to playlist', 'error'); });
  });
})();

// ---- Hero background carousel (auto-rotating + dots) ----
(function () {
  var slides = document.querySelectorAll('.hero-bg-slide');
  var dots = document.querySelectorAll('#heroDots .hero-dot');
  if (!slides.length) return;
  var current = 0; var timer = null;

  function show(idx) {
    slides.forEach(function (s, i) { s.classList.toggle('active', i === idx); });
    dots.forEach(function (d, i) { d.classList.toggle('active', i === idx); });
    current = idx;
  }
  function next() { show((current + 1) % slides.length); }
  function start() { timer = setInterval(next, 5000); }
  function reset() { clearInterval(timer); start(); }

  dots.forEach(function (dot, i) {
    dot.style.cursor = 'pointer';
    dot.addEventListener('click', function () { show(i); reset(); });
  });
  start();
})();

// ---- Generic carousel controller: arrows + auto-animate slider ----
// Works for any carousel with [data-carousel="name"] track + [data-carousel-prev] / [data-carousel-next]
(function () {
  document.querySelectorAll('[data-carousel]').forEach(function (track) {
    var name = track.getAttribute('data-carousel');
    var prev = document.querySelector('[data-carousel-prev="' + name + '"]');
    var next = document.querySelector('[data-carousel-next="' + name + '"]');
    var wrapper = track.closest('.lm-carousel-wrapper');
    var gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '20') || 20;
    var autoTimer = null;
    var AUTO_INTERVAL = 3500;

    function step() {
      var card = track.children[0];
      if (!card) return 300;
      return card.offsetWidth + gap;
    }
    function maxScroll() { return track.scrollWidth - track.clientWidth; }
    function scrollBy(dir) { track.scrollBy({ left: dir * step(), behavior: 'smooth' }); }

    function updateArrows() {
      var ms = maxScroll() - 4;
      if (prev) {
        prev.style.opacity = track.scrollLeft <= 4 ? '0.35' : '1';
        prev.style.pointerEvents = track.scrollLeft <= 4 ? 'none' : 'auto';
      }
      if (next) {
        next.style.opacity = track.scrollLeft >= ms ? '0.35' : '1';
        next.style.pointerEvents = track.scrollLeft >= ms ? 'none' : 'auto';
      }
    }

    // ---- Auto-animate (infinite loop) ----
    function autoNext() {
      if (track.scrollLeft >= maxScroll() - 2) {
        track.classList.add('paused');
        track.scrollTo({ left: 0, behavior: 'smooth' });
        setTimeout(function () { track.classList.remove('paused'); }, 600);
      } else {
        scrollBy(1);
      }
    }
    function startAuto() {
      if (track.children.length < 2) return;
      stopAuto();
      autoTimer = setInterval(autoNext, AUTO_INTERVAL);
    }
    function stopAuto() { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } }
    function resetAuto() { stopAuto(); startAuto(); }

    if (prev) prev.addEventListener('click', function () { scrollBy(-1); resetAuto(); });
    if (next) next.addEventListener('click', function () { scrollBy(1); resetAuto(); });

    // Pause on hover/focus, resume on leave
    if (wrapper) {
      wrapper.addEventListener('mouseenter', stopAuto);
      wrapper.addEventListener('mouseleave', startAuto);
    }
    track.addEventListener('scroll', updateArrows, { passive: true });
    window.addEventListener('resize', updateArrows);
    updateArrows();

    // Start auto only when the carousel scrolls into view
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) startAuto(); else stopAuto();
        });
      }, { threshold: 0.25 });
      io.observe(track);
    } else {
      startAuto();
    }
  });
})();

var BASE_URL = window.BASE_URL || '';
var placeholderImg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"><rect width="36" height="36" rx="8" fill="%23161628"/><text x="50%" y="55%" font-size="14" fill="%238b93a7" text-anchor="middle">♪</text></svg>';

// ---- Scroll reveal animations ----
(function() {
  var els = document.querySelectorAll('.lm-card, .row > div');
  els.forEach(function(el) { el.classList.add('reveal'); });
  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('reveal-in');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  els.forEach(function(el) { observer.observe(el); });
})();
