import { affichagePriceApi } from "./priceApi.js";
import { carouBuild } from "./carouBuild.js";

let priceApi = '../API/priceApi.json'

fetch('/API/priceApi.json')
    .then(r => r.json())
    .then(affichagePriceApi)
    .catch(console.error);

document.addEventListener('DOMContentLoaded', () => {

    const comp = document.querySelector('#btn-nav');
    const compList = document.querySelector('.menu-composant');
    const btnCloseList = document.querySelector('.btn-close-list');
    comp.addEventListener('click', (e) => {
        compList.classList.toggle('comp');
    });
    btnCloseList.addEventListener('click', (e) => {
        compList.classList.add('comp');
    });
    const connecter = document.querySelector('.connecter > div');
    const profileList = document.querySelector('.connecter ul');
    if (connecter) {
        connecter.addEventListener('click', (e) => {
            profileList.classList.toggle('co');
        });
    }

    const slide = document.querySelector('.slide')
    if (slide) {
        carouBuild(slide);
    }
    const menuBurger = document.querySelector('.menu-burger');
    const menuTel = document.querySelector('.navbar ul');
    if (menuBurger) {
        menuBurger.addEventListener('click', () => {
            menuTel.classList.toggle('tel-menu');
            menuBurger.classList.toggle('burger-active');
        });
    }
});
