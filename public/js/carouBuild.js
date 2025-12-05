export function carouBuild(slide) {
    const leftBtn = document.querySelector('#left-btn');
    const rightBtn = document.querySelector('#right-btn');
    const buildCard = document.querySelectorAll('.build-card')
    let max = 0;
    buildCard.forEach(element => {
        max += 1;
        return max;
    });
    //ACC
    let index = 0;
    let d = 0;
    //EVENT FLECHE DROITE
    if (!rightBtn || !leftBtn) return;
    rightBtn.addEventListener('click', function () {

        if (index < max) {
            index += 1;
            d -= 100;
            slide.style.transform = `translateX(${d}%)`;
        };
    });

    //EVENT FLECHE GAUCHE
    leftBtn.addEventListener('click', function () {
        if (index > 0) {
            index -= 1;
            d += 100;
            slide.style.transform = `translateX(${d}%)`;
        };
    });
}