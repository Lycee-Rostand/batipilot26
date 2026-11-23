document.addEventListener('click', (e) => {
    const toggleBtn = e.target.closest('.bp-section__toggle');
    if (!toggleBtn) return;

    const section = toggleBtn.closest('.bp-section');
    if (!section) return;

    const isOpen = section.classList.toggle('is-open');
    toggleBtn.setAttribute('aria-expanded', String(isOpen));
});
