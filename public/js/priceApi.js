export function affichagePriceApi(data) {
  const results = Array.isArray(data) ? data : (data.results ?? []);

  const normalize = (v) => {
    if (v == null) return '';
    return String(v)
      .normalize('NFD')
      .replace(/\p{Diacritic}/gu, '')
      .toLowerCase()
      .replace(/[^a-z0-9]/g, '');
  };

  document.querySelectorAll('#compo-card').forEach((card) => {
    const cardAscin = (card.dataset.ascin || '').trim();

    if (!cardAscin) return; // exit if no ASIN

    // exact match on ASIN
    const match = results.find((item) => {
      const c = item.content ?? {};
      const idCandidates = [
        c.id,
        c.asin,
        ...(c.mpns || []),
        ...(c.eans || []),
      ].filter(Boolean).map(String);

      return idCandidates.some(id => normalize(id) === normalize(cardAscin));
    });

    if (!match) return; // exit if no match found

    const content = match.content ?? {};

    const imgEl = card.querySelector('#compo-card-img img');
    const noteEl = card.querySelector('#compo-card-note');
    const linkEl = card.querySelector('#compo-card-revendeur');

    const imgUrl = content.image_url
      || (content.multimedia && content.multimedia[0] && content.multimedia[0].url)
      || null;
    if (imgEl && imgUrl) {
      imgEl.src = imgUrl;
    }

    if (linkEl && content.url) {
      if (linkEl.tagName === 'A') {
        linkEl.href = content.url;
      } else {
        linkEl.setAttribute('data-url', content.url);
      }
    }

    const noteValue = content.review_rating ?? null;
    const rating = noteValue / 20;
    if (noteEl && noteValue != null) {
      noteEl.textContent = `Note : ${rating} / 5`;
    }
  });
}