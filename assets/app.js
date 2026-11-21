document.querySelectorAll('[data-inline-form-toggle]').forEach(button => button.addEventListener('click', () => {
  const form = document.getElementById(button.getAttribute('aria-controls'));
  if (!form) return;
  form.hidden = !form.hidden;
  button.setAttribute('aria-expanded', String(!form.hidden));
  button.textContent = form.hidden ? button.dataset.openLabel : button.dataset.closeLabel;
}));

document.querySelectorAll('[data-gallery-edit-toggle]').forEach(button => button.addEventListener('click', () => {
  const gallery = document.querySelector('[data-gallery-grid]');
  if (!gallery) return;
  const editing = gallery.classList.toggle('gallery-editing');
  button.setAttribute('aria-pressed', String(editing));
  button.textContent = editing ? button.dataset.closeLabel : button.dataset.openLabel;
}));

document.querySelectorAll('[data-theme-toggle]').forEach(button => button.addEventListener('click', () => {
  const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
  document.documentElement.dataset.theme = next;
  localStorage.setItem('ev-theme', next);
}));
document.querySelectorAll('[data-confirm]').forEach(form => form.addEventListener('submit', event => {
  if (!confirm(form.dataset.confirm)) event.preventDefault();
}));
const savedTheme = localStorage.getItem('ev-theme');
if (savedTheme) document.documentElement.dataset.theme = savedTheme;

async function lookupIsbn(debugOnly = false) {
  const isbnButton = document.querySelector('#lookup-isbn');
  const input = document.querySelector('#isbn');
  const result = document.querySelector('#isbn-result');
  const debugPanel = document.querySelector('#isbn-debug');
  const debugOutput = document.querySelector('#isbn-api-response');
  if (!input) return;
  if (!debugOnly && result) result.textContent = 'Consultando Open Libraryâ€¦';
  if (isbnButton) isbnButton.disabled = true;
  const endpoint = `${document.body.dataset.baseUrl}/pages/isbn.php?isbn=${encodeURIComponent(input.value)}${debugOnly ? '&debug=1' : ''}`;
  try {
    const response = await fetch(endpoint);
    const data = await response.json();
    if (debugOutput) {
      const details = data.diagnostic ? data : data._meta?.diagnostic || (data.error ? {error: data.error} : {cache: data._meta?.cached, source: data._meta?.source});
      debugOutput.textContent = JSON.stringify(details, null, 2);
    }
    if (data.diagnostic) {
      if (debugPanel) debugPanel.open = true;
      return;
    }
    if (!response.ok) throw new Error(data.error || 'Livro nÃ£o encontrado.');
    const fieldMap = {title:'titulo', author:'autor', publisher:'editora', year:'ano_publicacao', page_count:'numero_paginas', synopsis:'descricao'};
    for (const [apiKey, fieldName] of Object.entries(fieldMap)) {
      const field = document.querySelector(`[name="${fieldName}"]`);
      if (field && Object.hasOwn(data, apiKey)) field.value = data[apiKey] ?? '';
    }
    const coverPicker = document.querySelector('[data-cover-picker]');
    const coverUrl = coverPicker?.querySelector('[data-cover-url]');
    const coverFile = coverPicker?.querySelector('[data-cover-file]');
    const coverRemove = coverPicker?.querySelector('[data-cover-remove]');
    if (coverPicker && Object.hasOwn(data, 'cover_url')) {
      const url = data.cover_url ?? '';
      if (coverUrl) coverUrl.value = url;
      if (coverFile) coverFile.value = '';
      if (coverRemove) coverRemove.checked = !url && Boolean(coverPicker.dataset.original);
      if (url) coverPicker.dataset.original = url;
      showCoverPreview(coverPicker, url);
    }
    if (result) result.textContent = data._meta?.cached
      ? 'Dados carregados do cache local da Open Library. Confira e salve o livro.'
      : 'Dados encontrados na Open Library. Confira e salve o livro.';
  } catch (error) {
    if (result) result.textContent = error.message;
    if (debugOutput) debugOutput.textContent = JSON.stringify({error: error.message}, null, 2);
  } finally {
    if (isbnButton) isbnButton.disabled = false;
  }
}

document.querySelector('#lookup-isbn')?.addEventListener('click', () => lookupIsbn(false));
document.querySelector('#debug-isbn')?.addEventListener('click', () => lookupIsbn(true));

function showCoverPreview(picker, src) {
  const preview = picker.querySelector('[data-cover-preview]');
  preview.replaceChildren();
  if (src) {
    const image = document.createElement('img');
    image.src = src;
    image.alt = 'PrÃ©via da capa do livro';
    image.onerror = () => {
      const urlField = picker.querySelector('[data-cover-url]');
      const removeField = picker.querySelector('[data-cover-remove]');
      if (urlField && urlField.value === src) urlField.value = '';
      if (removeField) removeField.checked = true;
      picker.dataset.original = '';
      showCoverPreview(picker, '');
    };
    preview.append(image);
    return;
  }
  const placeholder = document.createElement('span');
  placeholder.className = 'cover-placeholder';
  placeholder.setAttribute('aria-label', 'Sem capa');
  placeholder.innerHTML = '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M12 13h19c6 0 10 4 10 10v29c0-5-4-8-10-8H12z"></path><path d="M52 13H33c-6 0-10 4-10 10v29c0-5 4-8 10-8h19z"></path></svg>';
  preview.append(placeholder);
}

document.querySelectorAll('[data-photo-picker]').forEach(picker => {
  const input = picker.querySelector('[data-photo-file]');
  const preview = picker.querySelector('[data-photo-preview]');
  const remove = picker.querySelector('[data-photo-remove]');
  let previewUrl = null;
  const showImage = src => {
    preview.replaceChildren();
    if (src) {
      const image = document.createElement('img');
      image.src = src;
      image.alt = 'PrÃ©via da foto de perfil';
      preview.append(image);
      return;
    }
    const icon = document.createElement('span');
    icon.className = 'photo-placeholder';
    icon.setAttribute('aria-label', 'Sem foto');
    icon.innerHTML = '<svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="22" r="12"></circle><path d="M9 58c1-13 10-21 23-21s22 8 23 21"></path></svg>';
    preview.append(icon);
  };
  input.addEventListener('change', () => {
    const file = input.files?.[0];
    if (!file) return;
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = URL.createObjectURL(file);
    if (remove) remove.checked = false;
    showImage(previewUrl);
  });
  if (remove) remove.addEventListener('change', () => {
    if (remove.checked) {
      input.value = '';
      if (previewUrl) URL.revokeObjectURL(previewUrl);
      previewUrl = null;
      showImage('');
    } else {
      showImage(picker.dataset.original || '');
    }
  });
});

document.querySelectorAll('[data-gallery-picker]').forEach(picker => {
  const input = picker.querySelector('[data-gallery-file]');
  const preview = picker.querySelector('[data-gallery-preview]');
  const pencil = picker.querySelector('.gallery-pencil');
  let previewUrl = null;
  pencil?.addEventListener('click', () => input?.click());
  input?.addEventListener('change', () => {
    const file = input.files?.[0];
    if (!file) return;
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = URL.createObjectURL(file);
    const image = document.createElement('img');
    image.src = previewUrl;
    image.alt = 'Prévia da foto da galeria';
    preview.replaceChildren(image);
  });
});

document.querySelectorAll('[data-cover-picker]').forEach(picker => {
  const input = picker.querySelector('[data-cover-file]');
  const urlInput = picker.querySelector('[data-cover-url]');
  const preview = picker.querySelector('[data-cover-preview]');
  const remove = picker.querySelector('[data-cover-remove]');
  let previewUrl = null;
  input.addEventListener('change', () => {
    const file = input.files?.[0];
    if (!file) return;
    if (previewUrl) URL.revokeObjectURL(previewUrl);
    previewUrl = URL.createObjectURL(file);
    if (remove) remove.checked = false;
    showCoverPreview(picker, previewUrl);
  });
  if (remove) remove.addEventListener('change', () => {
    if (remove.checked) {
      input.value = '';
      if (urlInput) urlInput.value = '';
      if (previewUrl) URL.revokeObjectURL(previewUrl);
      previewUrl = null;
      showCoverPreview(picker, '');
    } else {
      showCoverPreview(picker, picker.dataset.original || '');
      if (/^https?:\/\//i.test(picker.dataset.original || '') && urlInput) urlInput.value = picker.dataset.original;
    }
  });
});

const navToggle = document.querySelector('[data-nav-toggle]');
const siteMenu = document.querySelector('[data-site-menu]');
if (navToggle && siteMenu) {
  const setMenuOpen = open => {
    navToggle.setAttribute('aria-expanded', String(open));
    navToggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
    siteMenu.classList.toggle('is-open', open);
  };
  navToggle.addEventListener('click', () => setMenuOpen(navToggle.getAttribute('aria-expanded') !== 'true'));
  siteMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenuOpen(false)));
  document.addEventListener('keydown', event => { if (event.key === 'Escape') setMenuOpen(false); });
  window.addEventListener('resize', () => { if (window.innerWidth > 1000) setMenuOpen(false); });
}

const homeCarouselViewport = document.querySelector('.home-book-carousel-viewport');
const homeCarouselTrack = homeCarouselViewport?.querySelector('.home-book-carousel-track');
if (homeCarouselViewport && homeCarouselTrack && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  let drag = null;
  let suppressDraggedClick = false;
  let suppressClickTimer;

  homeCarouselViewport.addEventListener('pointerdown', event => {
    if (event.button !== 0) return;
    const transform = getComputedStyle(homeCarouselTrack).transform;
    const offset = transform === 'none' ? 0 : new DOMMatrixReadOnly(transform).m41;
    const captureTarget = event.target.closest('a') || homeCarouselViewport;
    homeCarouselTrack.style.animationPlayState = 'paused';
    homeCarouselTrack.style.animationName = 'none';
    homeCarouselTrack.style.transform = `translateX(${offset}px)`;
    drag = { pointerId: event.pointerId, startX: event.clientX, offset, moved: false, captureTarget };
    try { captureTarget.setPointerCapture(event.pointerId); } catch (_) {}
  });

  homeCarouselViewport.addEventListener('pointermove', event => {
    if (!drag || event.pointerId !== drag.pointerId) return;
    const distance = event.clientX - drag.startX;
    if (Math.abs(distance) > 5) {
      drag.moved = true;
      homeCarouselViewport.classList.add('is-dragging');
      homeCarouselTrack.style.transform = `translateX(${drag.offset + distance}px)`;
    }
  });

  const finishCarouselDrag = event => {
    if (!drag || event.pointerId !== drag.pointerId) return;
    const groupWidth = homeCarouselTrack.scrollWidth / 2;
    let offset = drag.offset + (drag.moved ? event.clientX - drag.startX : 0);
    if (groupWidth > 0) {
      offset %= groupWidth;
      if (offset > 0) offset -= groupWidth;
    }
    homeCarouselTrack.style.setProperty('--carousel-start', `${offset}px`);
    homeCarouselTrack.style.transform = '';
    homeCarouselTrack.style.animationName = 'home-cover-scroll-resume';
    homeCarouselTrack.style.animationPlayState = '';
    homeCarouselViewport.classList.remove('is-dragging');
    if (drag.moved) {
      suppressDraggedClick = true;
      clearTimeout(suppressClickTimer);
      suppressClickTimer = setTimeout(() => { suppressDraggedClick = false; }, 500);
    }
    drag = null;
  };

  homeCarouselViewport.addEventListener('pointerup', finishCarouselDrag);
  homeCarouselViewport.addEventListener('pointercancel', finishCarouselDrag);
  homeCarouselViewport.addEventListener('click', event => {
    if (!suppressDraggedClick) return;
    event.preventDefault();
    event.stopPropagation();
    suppressDraggedClick = false;
    clearTimeout(suppressClickTimer);
  }, true);
}
