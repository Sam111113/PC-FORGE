export function affichagePriceApiError() {
      document.querySelectorAll('.compo-card').forEach((card) => {
        document.querySelectorAll('.compo-card div').forEach((div) => {
            div.remove();
        });
        document.createElement('h3').textContent = 'informations du produit indisponible';
      })
}