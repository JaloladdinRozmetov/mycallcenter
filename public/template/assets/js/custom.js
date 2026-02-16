/**
 * Custom UI helpers for the site header.
 */
document.addEventListener('DOMContentLoaded', () => {
  const authActions = document.querySelector('.auth-actions');

  const syncScrollState = () => {
    if (!authActions) return;
    const isScrolled = document.body.classList.contains('scrolled');
    authActions.classList.toggle('scrolled', isScrolled);
  };

  document.addEventListener('scroll', syncScrollState, { passive: true });
  syncScrollState();
});
