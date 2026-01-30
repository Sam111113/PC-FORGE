export function affichagePriceApiError() {
      document.querySelectorAll('.compo-card').forEach((card) => {
        document.querySelectorAll('.compo-card div').forEach((div) => {
            div.remove();
        });
        let msg = document.createElement('h3');
        msg.textContent = 'informations du produit indisponible';
        msg.classList.add('apiError');
        card.appendChild(msg);
      })
}