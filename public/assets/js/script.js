(function () {
  const dataLayer = window.dataLayer = window.dataLayer || [];

  window.rrTrack = function rrTrack(eventName, params = {}) {
    const payload = {
      event: eventName,
      page_path: window.location.pathname,
      ...params
    };

    dataLayer.push(payload);

    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, params);
    }
  };

  function initNavigation() {
    const menuBtn = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (!menuBtn || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('show');
      menuBtn.textContent = isOpen ? '×' : '☰';
      menuBtn.setAttribute('aria-expanded', String(isOpen));
    });
  }

  function initFooterYear() {
    document.querySelectorAll('[data-current-year]').forEach((target) => {
      target.textContent = String(new Date().getFullYear());
    });
  }

  function initTrackingHooks() {
    document.querySelectorAll('[data-track-event]').forEach((element) => {
      element.addEventListener('click', () => {
        window.rrTrack(element.dataset.trackEvent, {
          event_label: element.dataset.trackLabel || element.textContent.trim(),
          link_url: element.getAttribute('href') || ''
        });
      });
    });

    document.querySelectorAll('a[href^="tel:"]').forEach((element) => {
      if (element.dataset.trackEvent) return;
      element.addEventListener('click', () => {
        window.rrTrack('click_phone', {
          event_label: element.textContent.trim(),
          link_url: element.getAttribute('href') || ''
        });
      });
    });

    document.querySelectorAll('a[href^="mailto:"]').forEach((element) => {
      if (element.dataset.trackEvent) return;
      element.addEventListener('click', () => {
        window.rrTrack('click_email', {
          event_label: element.textContent.trim(),
          link_url: element.getAttribute('href') || ''
        });
      });
    });

    const serviceName = document.body.dataset.servicePage;
    if (serviceName) {
      window.rrTrack('view_service_page', { service_name: serviceName });
    }
  }

  function initCookieSettingsLink() {
    document.querySelectorAll('[data-cookie-settings]').forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();

        if (window.CCM && typeof window.CCM.openWidget === 'function') {
          window.CCM.openWidget();
        }
      });
    });
  }

  function initFormTime() {
    const timeInput = document.getElementById('form_time');
    if (timeInput) {
      timeInput.value = Math.floor(Date.now() / 1000);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initFooterYear();
    initTrackingHooks();
    initCookieSettingsLink();
    initFormTime();
  });
})();
