document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. Toast Notification System
    // ==========================================
    function showToast(message, type = 'success') {
        const container = document.querySelector('.toast-container') || createToastContainer();

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideIn 0.3s ease reverse forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    // ==========================================
    // 2. Form Handling (Supabase Integration)
    // ==========================================

    // Contact Form
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('contact-submit-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                service: document.getElementById('service').value,
                subject: document.getElementById('subject').value,
                company: document.getElementById('company').value,
                budget: document.getElementById('budget').value,
                message: document.getElementById('message').value
            };

            // Call Supabase Wrapper
            const result = await submitContactForm(formData);

            if (result.success) {
                showToast(result.message, 'success');
                contactForm.reset();
            } else {
                showToast(result.message, 'error');
            }

            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Newsletter Form (Footer)
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value;
            const btn = newsletterForm.querySelector('button');
            const originalIcon = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            const result = await subscribeNewsletter(email);

            if (result.success) {
                showToast(result.message, 'success');
                emailInput.value = '';
            } else {
                showToast(result.message, 'error');
            }

            btn.innerHTML = originalIcon;
            btn.disabled = false;
        });
    }

    // ==========================================
    // 3. UI Interactions & Animations
    // ==========================================

    // Scroll Header Effect
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            header.style.background = 'rgba(255, 255, 255, 0.95)';
        } else {
            header.style.boxShadow = 'none';
            header.style.background = 'rgba(255, 255, 255, 0.85)';
        }
    });

    // Mobile Menu Toggle & Navigation Logic
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-link');
    const body = document.body;

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');

            // Prevent background scrolling when menu is open
            if (navMenu.classList.contains('active')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        });
    }

    // Close menu when clicking a link (Auto-close)
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            // If it's a dropdown toggle, don't close yet
            if (link.parentElement.classList.contains('dropdown') ||
                link.nextElementSibling?.classList.contains('sub-dropdown-menu')) {
                return;
            }

            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            body.style.overflow = '';

            // Close all open dropdowns
            document.querySelectorAll('.dropdown-menu.active, .sub-dropdown-menu.active').forEach(menu => {
                menu.classList.remove('active');
            });
        });
    });

    // Mobile Dropdown Logic with Back Button
    const dropdowns = document.querySelectorAll('.dropdown > a, .dropdown-submenu > a');

    dropdowns.forEach(dropdownToggle => {
        dropdownToggle.addEventListener('click', (e) => {
            // Only apply on mobile (when hamburger is visible)
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const submenu = dropdownToggle.nextElementSibling;

                if (submenu) {
                    // Add Back Button if not exists
                    if (!submenu.querySelector('.dropdown-back')) {
                        const backBtn = document.createElement('div');
                        backBtn.className = 'dropdown-back';
                        backBtn.innerHTML = '<i class="fas fa-chevron-left"></i> Back';

                        backBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            submenu.classList.remove('active');
                        });

                        submenu.prepend(backBtn);
                    }

                    submenu.classList.add('active');
                }
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('active')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            body.style.overflow = '';
        }
    });

    // Dynamic Copyright Year
    const yearSpan = document.getElementById('current-year');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(i => i.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            });
        }
    });
});
