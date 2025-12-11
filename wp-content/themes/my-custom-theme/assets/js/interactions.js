/**
 * Interactions & Animations
 * Handles Sticky Header, Mobile Menu, and Scroll Reveal.
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Sticky Header
    const header = document.querySelector('.site-header');
    const scrollThreshold = 100;

    const handleScroll = () => {
        if (window.scrollY > scrollThreshold) {
            header.classList.add('is-sticky');
        } else {
            header.classList.remove('is-sticky');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });

    // 2. Mobile Menu Toggle
    const toggleBtn = document.querySelector('.mobile-menu-toggle');
    const headerActions = document.querySelector('.header-actions');
    const body = document.body;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
            toggleBtn.setAttribute('aria-expanded', !isExpanded);
            headerActions.classList.toggle('is-active');
            toggleBtn.classList.toggle('is-active');
            body.classList.toggle('no-scroll'); // Prevent background scrolling
        });
    }

    // 3. Scroll Reveal Animations
    const revealElements = document.querySelectorAll('.directory-card, .review-item, h2, h3');

    // Add base class for CSS transitions
    revealElements.forEach(el => el.classList.add('reveal-item'));

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target); // Only animate once
            }
        });
    }, {
        root: null,
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
});
