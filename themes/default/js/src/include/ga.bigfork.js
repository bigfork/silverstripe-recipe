/**
 * GA link handler
 */
(() => {
  if (!window.ga) {
    return;
  }

  const anchors = document.querySelectorAll('a');
  const extensions = ['pdf', 'docx?', 'xlsx?', 'pp(t|s)x?', 'csv', 'rtf'];
  const extensionsRegex = new RegExp('.(' + extensions.join('|') + ')$', 'i');
  const emailRegex = new RegExp('^mailto:', 'i');

  [].forEach.call(anchors, (el) => {
    const href = el.getAttribute('href');

    // If tracking has been set up manually, bail out
    if (el.getAttribute('data-track')) {
      return;
    }

    // Ignore "internal" links, unless they either match the file extension list above or are email addresses
    if (el.host === window.location.host && !el.pathname.match(extensionsRegex) && !href.match(emailRegex)) {
      return;
    }

    // Store event parameters in data attributes for later use
    const attributes = {
      'data-track': 'link',
      'data-category': 'Link Clicked',
      'data-action': (el.pathname.replace(/(.*\/)+/, '') || el.innerHTML),
      'data-label': window.location.pathname
    };

    // Adjust event parameters for email links
    if (href && href.match(emailRegex)) {
      const address = href.replace(emailRegex, '').trim();
      attributes['data-category'] = 'Email Link';
      attributes['data-action'] = address;
    }

    // Store attributes on element
    for (let attr in attributes) {
      if (!attributes.hasOwnProperty(attr)) {
        continue;
      }

      el.setAttribute(attr, attributes[attr]);
    }
  });

  // Add event handler to push GA event on click
  [].forEach.call(anchors, (el) => {
    if (!el.getAttribute('data-track')) {
      return;
    }

    el.addEventListener('click', (event) => {
      const anchor = event.target;

      gtag('event', anchor.getAttribute('data-action'), {
        'event_category': anchor.getAttribute('data-category'),
        'event_label': anchor.getAttribute('data-label'),
        'value': 1
      });

      ga('send', 'event', parameters);
    });
  });
})();
