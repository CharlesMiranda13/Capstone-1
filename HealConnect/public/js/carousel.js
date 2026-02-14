document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.carousel-slide-wrapper');
    const slides = Array.from(track.children);
    const nextButton = document.querySelector('.carousel-next');
    const prevButton = document.querySelector('.carousel-prev');
    const dotsNav = document.querySelector('.carousel-indicators');
    const dots = Array.from(dotsNav.children);

    if (slides.length === 0) return;

    const moveToSlide = (track, currentSlide, targetSlide, targetIndex) => {
        const slideWidth = slides[0].getBoundingClientRect().width;
        track.style.transform = 'translateX(-' + (slideWidth * targetIndex) + 'px)';
        currentSlide.classList.remove('active');
        targetSlide.classList.add('active');
        
        currentSlideCopy = targetSlide;
    };

    const updateDots = (currentDot, targetDot) => {
        currentDot.classList.remove('active');
        targetDot.classList.add('active');
    };

    const nextSlide = () => {
        const currentSlide = track.querySelector('.active') || slides[0];
        const currentDot = dotsNav.querySelector('.active') || dots[0];

        const currentIndex = slides.indexOf(currentSlide);
        let nextIndex = currentIndex + 1;
        
        let nextSlideElement = slides[nextIndex];
        let nextDotElement = dots[nextIndex];

        // loop back to first slide
        if (nextIndex >= slides.length) {
            nextIndex = 0;
            nextSlideElement = slides[0];
            nextDotElement = dots[0];
        }

        moveToSlide(track, currentSlide, nextSlideElement, nextIndex);
        updateDots(currentDot, nextDotElement);
    };

    const prevSlide = () => {
        const currentSlide = track.querySelector('.active') || slides[0];
        const currentDot = dotsNav.querySelector('.active') || dots[0];
        
        const currentIndex = slides.indexOf(currentSlide);
        let prevIndex = currentIndex - 1;

        let prevSlideElement = slides[prevIndex];
        let prevDotElement = dots[prevIndex];

        // loop to last slide
        if (prevIndex < 0) {
            prevIndex = slides.length - 1;
            prevSlideElement = slides[prevIndex];
            prevDotElement = dots[prevIndex];
        }

        moveToSlide(track, currentSlide, prevSlideElement, prevIndex);
        updateDots(currentDot, prevDotElement);
    };

    nextButton.addEventListener('click', () => {
        nextSlide();
        resetTimer();
    });

    prevButton.addEventListener('click', () => {
        prevSlide();
        resetTimer();
    });

    dotsNav.addEventListener('click', e => {
        const targetDot = e.target.closest('button');

        if (!targetDot) return;

        const currentSlide = track.querySelector('.active');
        const currentDot = dotsNav.querySelector('.active');
        const targetIndex = dots.findIndex(dot => dot === targetDot);
        const targetSlide = slides[targetIndex];

        moveToSlide(track, currentSlide, targetSlide, targetIndex);
        updateDots(currentDot, targetDot);
        resetTimer();
    });

    // auto-slide every 10 seconds
    let slideInterval;
    const startTimer = () => {
        slideInterval = setInterval(nextSlide, 10000); 
    };

    const stopTimer = () => {
        clearInterval(slideInterval);
    };

    const resetTimer = () => {
        stopTimer();
        startTimer();
    };

    startTimer();

    // pause on hover
    const carouselContainer = document.querySelector('.carousel-container');
    carouselContainer.addEventListener('mouseenter', stopTimer);
    carouselContainer.addEventListener('mouseleave', startTimer);

    // touch swipe
    let touchStartX = 0;
    let touchEndX = 0;

    carouselContainer.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});

    carouselContainer.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, {passive: true});

    const handleSwipe = () => {
        if (touchEndX < touchStartX - 50) {
            nextSlide();
            resetTimer();
        }
        if (touchEndX > touchStartX + 50) {
            prevSlide();
            resetTimer();
        }
    };
    
    if (!track.querySelector('.active')) {
        slides[0].classList.add('active');
    }
    
    window.addEventListener('resize', () => {
       const currentSlide = track.querySelector('.active') || slides[0];
       const currentIndex = slides.indexOf(currentSlide);
       const slideWidth = slides[0].getBoundingClientRect().width;
       track.style.transform = 'translateX(-' + (slideWidth * currentIndex) + 'px)';
    });
});
