export function carouBuild(slide) {
    const leftBtn = document.querySelector('#left-btn');
    const rightBtn = document.querySelector('#right-btn');
    const buildCard = document.querySelector('.build-card');
    const totalCard = document.querySelectorAll('.build-card');
    let limiteTotal = 0;
    totalCard.forEach(element => {
        limiteTotal += 1;
        return limiteTotal;
    });

    const style = getComputedStyle(slide);
    const gap = parseFloat(style.gap);
    const itemWidth = buildCard.getBoundingClientRect().width;

    let index = 0;

    function moveCarousel() {
        const distance = index * (itemWidth + gap);
        slide.style.transform = `translateX(-${distance}px)`;
    }

    rightBtn.addEventListener('click', () => {
        if (index < totalCard.length - 1){
        index++;
        moveCarousel();
        return index;
        }
    });

    leftBtn.addEventListener('click', () => {
        if (index > 0){
        index--;
        moveCarousel();
        return index
        }
    });
}