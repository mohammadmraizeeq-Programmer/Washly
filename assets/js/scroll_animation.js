    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        const observerOptions = {
            root: null, // The viewport
            threshold: 0.2 // Trigger when 20% of the element is visible
        };
        
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('hidden');
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target); // Stop observing once it's visible
                }
            });
        }, observerOptions);
        
        elements.forEach(el => {
            observer.observe(el);
        });
    });