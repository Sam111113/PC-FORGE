import { carouBuild } from "./carouBuild.js";
import { affichagePriceApi } from "./priceApiDisplay.js";
import { affichagePriceApiError } from "./priceApiError.js";

let priceApiData = '/API/priceApi.json'

document.addEventListener('DOMContentLoaded', () => {

    // Gestion de la fermeture des messages flash
    if(document.querySelector('.flash-close')) {
        document.querySelectorAll('.flash-close').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.parentElement.remove();
            });
        });
    };

    // Fetch pour l'affichage via l'API
    if (document.querySelector('.compo-card')) {

        fetch(priceApiData).then(function (response) {
            return response.json();
        }).then(function (component) {
            affichagePriceApi(component);
        }).catch(function (error) {
            affichagePriceApiError(error);

        })
    }
    // Gestion des menus
    const comp = document.querySelector('#btn-nav');
    const compList = document.querySelector('.menu-composant');
    const btnCloseList = document.querySelector('.btn-close-list');
    comp.addEventListener('click', (e) => {
        e.stopPropagation();
        compList.classList.toggle('comp');
    });
    if (btnCloseList) {
        btnCloseList.addEventListener('click', () => {
            compList.classList.remove('comp');
        });
    }
    // Empêche la fermeture quand on clique dans le menu composant
    if (compList) {
        compList.addEventListener('click', (e) => e.stopPropagation());
    }

    const connecter = document.querySelector('.connecter > div');
    const profileList = document.querySelector('.connecter ul');
    if (connecter) {
        connecter.addEventListener('click', (e) => {
            e.stopPropagation();
            profileList.classList.toggle('co');
        });
    }
    // Empêche la fermeture quand on clique dans le menu profil
    if (profileList) {
        profileList.addEventListener('click', (e) => e.stopPropagation());
    }

    const menuBurger = document.querySelector('.menu-burger');
    const menuTel = document.querySelector('.navbar ul');
    if (menuBurger) {
        menuBurger.addEventListener('click', (e) => {
            e.stopPropagation();
            menuTel.classList.toggle('tel-menu');
            menuBurger.classList.toggle('burger-active');
        });
    }
    // Empêche la fermeture quand on clique dans le menu mobile
    if (menuTel) {
        menuTel.addEventListener('click', (e) => e.stopPropagation());
    }

    // Ferme tous les menus quand on clique ailleurs sur la page
    document.addEventListener('click', () => {
        if (compList) compList.classList.remove('comp');
        if (profileList) profileList.classList.remove('co');
        if (menuTel && menuBurger) {
            menuTel.classList.remove('tel-menu');
            menuBurger.classList.remove('burger-active');
        }
    });
    //gestion du carrousel
    const slide = document.querySelector('.slide')
    if (slide) {
        carouBuild(slide);
    }
});
