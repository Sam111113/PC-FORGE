// Fonction pour comparer les ASIN et afficher la data
export function affichagePriceApi(data) {

  // Fonction pour normaliser les ASIN (enlever espaces, accents, tout en minuscules)
  const normalize = (v) => {
    if (v == null) return '';
    return v.trim().toLowerCase().replace(/\s+/g, '');
  };

  //Boucle pour comparer les dataset avec les asin de priceApiet injecter la data si correspondance
  document.querySelectorAll('.compo-card').forEach((card) => {

    const cardAsin = (card.dataset.ascin);
    const componentData = data.results.find((component) =>
      normalize(component.content.id) === normalize(cardAsin))

    //Sorti de boucle si aucune correspondance
    if (!componentData) return;

    const imgEl = card.querySelector('.compo-card-img img');
    const linkEl = card.querySelector('.compo-card-revendeur');
    const priceEl = card.querySelector('.compo-card-price');
    const noteEl = card.querySelector('.compo-card-note');

    const componentImgUrl = componentData.content.image_url;
    if (!componentImgUrl) {
      imgEl.src = 'erreur';
    }else {
      imgEl.src = componentImgUrl;
    }

    const componentLink = componentData.content.url;
    if (!componentLink) {
      linkEl.href = '#';
      linkEl.textContent = 'erreur';
    }else {
      linkEl.href = componentLink;
    }

    const componentRating = componentData.content.review_rating / 20;

    if (!componentRating) {
      noteEl.textContent = 'erreur';
    }else {
      noteEl.textContent = `Note : ${componentRating} / 5`;
    }

    const componentPrice = componentData.content.rrp;
    if (!componentPrice) return;
    priceEl.textContent = `${componentPrice} €`;
  }
  )
};
