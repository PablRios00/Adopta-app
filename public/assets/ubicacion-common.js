// ============================================================
// BUSCADOR DE UBICACIÓN MEJORADO — tolerante a erratas y tildes
// ============================================================

function normalizarBusqueda(texto) {
  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9\s]/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

async function buscarUbicaciones(texto, limite) {
  limite = limite || 6;
  const textoOriginal    = texto.trim();
  const textoNormalizado = normalizarBusqueda(textoOriginal);

  const intentos = [textoOriginal];
  if (textoNormalizado !== textoOriginal) {
    intentos.push(textoNormalizado);
  }

  for (var i = 0; i < intentos.length; i++) {
    var intento = intentos[i];
    try {
      var url = 'https://nominatim.openstreetmap.org/search' +
        '?format=json' +
        '&addressdetails=1' +
        '&namedetails=1' +
        '&limit=' + limite +
        '&countrycodes=es' +
        '&featuretype=settlement' +
        '&dedupe=1' +
        '&q=' + encodeURIComponent(intento);

      var res  = await fetch(url, {
        headers: { 'Accept-Language': 'es' }
      });
      var data = await res.json();

      if (data && data.length > 0) {
        return data;
      }
    } catch(e) { /* silencioso */ }
  }

  // Fallback sin featuretype para ampliar resultados
  try {
    var textoFinal = normalizarBusqueda(textoOriginal);
    var urlFallback = 'https://nominatim.openstreetmap.org/search' +
      '?format=json' +
      '&addressdetails=1' +
      '&limit=' + limite +
      '&countrycodes=es' +
      '&q=' + encodeURIComponent(textoFinal);

    var resFallback  = await fetch(urlFallback, { headers: { 'Accept-Language': 'es' } });
    var dataFallback = await resFallback.json();
    return dataFallback || [];
  } catch(e) {
    return [];
  }
}

function extraerUbicacionDesdeNominatim(lugar) {
  var addr       = lugar.address     || {};
  var namedetails = lugar.namedetails || {};

  var comunidad = addr.state    || addr.region || '';
  var provincia = addr.province || addr.county || '';
  var municipio = addr.city     || addr.town   || addr.village ||
                  addr.municipality || addr.county ||
                  namedetails['name:es'] || namedetails['name'] || '';

  var partes = [municipio, provincia, comunidad].filter(function(p) { return p && p !== ''; });

  return {
    comunidad:   comunidad,
    provincia:   provincia,
    municipio:   municipio,
    texto:       partes.join(', '),
    lat:         parseFloat(lugar.lat),
    lon:         parseFloat(lugar.lon),
    boundingbox: lugar.boundingbox || null
  };
}
