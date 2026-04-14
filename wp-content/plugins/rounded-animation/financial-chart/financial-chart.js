/* global document */
(function () {
  'use strict';

  var SVG_NS = 'http://www.w3.org/2000/svg';

  // ── Maths helpers ──────────────────────────────────────────────────────────

  function polarToXY(cx, cy, r, angleDeg) {
    var rad = (angleDeg - 90) * Math.PI / 180;
    return {
      x: cx + r * Math.cos(rad),
      y: cy + r * Math.sin(rad),
    };
  }

  function slicePath(cx, cy, r, startAngle, endAngle) {
    var s    = polarToXY(cx, cy, r, startAngle);
    var e    = polarToXY(cx, cy, r, endAngle);
    var large = (endAngle - startAngle) > 180 ? 1 : 0;
    return (
      'M ' + cx + ' ' + cy +
      ' L ' + s.x + ' ' + s.y +
      ' A ' + r + ' ' + r + ' 0 ' + large + ' 1 ' + e.x + ' ' + e.y +
      ' Z'
    );
  }

  // ── Close all popups in a chart wrap ──────────────────────────────────────

  function closeAll(wrap) {
    wrap.querySelectorAll('.fc-popup').forEach(function (p) {
      p.classList.remove('is-open');
      p.setAttribute('aria-hidden', 'true');
    });
    wrap.querySelectorAll('.fc-pin').forEach(function (b) {
      b.classList.remove('is-active');
      b.setAttribute('aria-expanded', 'false');
    });
  }

  // ── Build chart for one wrap element ─────────────────────────────────────

  function buildChart(wrap) {
    var raw = wrap.dataset.slices;
    if (!raw) return;

    var slices;
    try {
      slices = JSON.parse(raw);
    } catch (e) {
      return;
    }
    if (!slices.length) return;

    var SIZE     = 400;
    var cx       = SIZE / 2;
    var cy       = SIZE / 2;
    var R        = SIZE / 2 - 6;   // pie radius (slight inset)
    var PIN_DIST = R * 0.60;        // pin distance from centre

    var total = slices.reduce(function (sum, s) { return sum + s.value; }, 0);
    if (total <= 0) return;

    // ── Create SVG ──────────────────────────────────────────────────────────

    var svg = document.createElementNS(SVG_NS, 'svg');
    svg.setAttribute('viewBox', '0 0 ' + SIZE + ' ' + SIZE);
    svg.setAttribute('width', '100%');
    svg.setAttribute('height', '100%');
    svg.setAttribute('aria-hidden', 'true');
    wrap.appendChild(svg);

    // ── Draw slices ─────────────────────────────────────────────────────────

    var startAngle = 0;
    var pinData    = [];

    slices.forEach(function (slice, i) {
      var angle    = (slice.value / total) * 360;
      var endAngle = startAngle + angle;
      var midAngle = startAngle + angle / 2;

      // Slice path — use stroke to create a thin gap between slices
      var path = document.createElementNS(SVG_NS, 'path');
      path.setAttribute('d', slicePath(cx, cy, R, startAngle, endAngle));
      path.setAttribute('fill', slice.color);
      path.setAttribute('class', 'fc-slice');
      svg.appendChild(path);

      // Store pin position for later
      var pinXY = polarToXY(cx, cy, PIN_DIST, midAngle);
      pinData.push({
        x: pinXY.x,
        y: pinXY.y,
        slice: slice,
        index: i,
      });

      startAngle = endAngle;
    });

    // ── Create pins + popups ────────────────────────────────────────────────

    var popupId = 'fc-popup-' + Math.random().toString(36).slice(2, 8);

    pinData.forEach(function (pd, i) {
      var pctX = (pd.x / SIZE * 100).toFixed(3) + '%';
      var pctY = (pd.y / SIZE * 100).toFixed(3) + '%';

      // Pin button
      var btn = document.createElement('button');
      btn.className = 'fc-pin';
      btn.setAttribute('aria-label', pd.slice.label + ' — click for details');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-controls', popupId + '-' + i);
      btn.style.left = pctX;
      btn.style.top  = pctY;
      btn.style.setProperty('--fc-pin-color', pd.slice.color);

      // Magnifier icon
      btn.innerHTML =
        '<svg viewBox="0 0 16 16" fill="none" aria-hidden="true" focusable="false">' +
          '<circle cx="6.5" cy="6.5" r="4" stroke="currentColor" stroke-width="1.6"/>' +
          '<line x1="9.7" y1="9.7" x2="13.5" y2="13.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>' +
        '</svg>';

      wrap.appendChild(btn);

      // Popup
      var popup = document.createElement('div');
      popup.className = 'fc-popup';
      popup.id = popupId + '-' + i;
      popup.setAttribute('role', 'tooltip');
      popup.setAttribute('aria-hidden', 'true');
      popup.style.left = pctX;
      popup.style.top  = pctY;

      // Popup content
      var html =
        '<strong class="fc-popup__title" style="color:' + pd.slice.color + '">' +
          pd.slice.label +
        '</strong>';

      if (pd.slice.items && pd.slice.items.length) {
        html += '<ul class="fc-popup__list">';
        pd.slice.items.forEach(function (item) {
          html += '<li>' + item + '</li>';
        });
        html += '</ul>';
      }

      popup.innerHTML = html;
      wrap.appendChild(popup);

      // ── Event: toggle popup ──────────────────────────────────────────────
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = popup.classList.contains('is-open');
        closeAll(wrap);
        if (!isOpen) {
          popup.classList.add('is-open');
          popup.setAttribute('aria-hidden', 'false');
          btn.classList.add('is-active');
          btn.setAttribute('aria-expanded', 'true');
        }
      });
    });

    // ── Close on outside click ───────────────────────────────────────────────
    document.addEventListener('click', function () {
      closeAll(wrap);
    });

    // ── Close on Escape ──────────────────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeAll(wrap);
    });
  }

  // ── Init ──────────────────────────────────────────────────────────────────

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.fc-chart-wrap').forEach(buildChart);
  });

})();
