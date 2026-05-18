// ============================================================
// DATOS MAPA (Comunidad → Provincia → Municipio)
// ============================================================
const datosMapa = {
  "Andalucía": {
    "Almería": ["Almería","Adra","Berja","El Ejido","Huércal-Overa","Níjar","Roquetas de Mar","Vera"],
    "Cádiz": ["Cádiz","Algeciras","Chiclana de la Frontera","El Puerto de Santa María","Jerez de la Frontera","La Línea de la Concepción","Rota","San Fernando","Sanlúcar de Barrameda"],
    "Córdoba": ["Córdoba","Baena","Cabra","Lucena","Montilla","Palma del Río","Peñarroya-Pueblonuevo","Pozoblanco","Puente Genil"],
    "Granada": ["Granada","Almuñécar","Baza","Guadix","Loja","Maracena","Motril","Santa Fe"],
    "Huelva": ["Huelva","Almonte","Ayamonte","Isla Cristina","Lepe","Moguer","Nerva","Punta Umbría"],
    "Jaén": ["Jaén","Alcalá la Real","Andújar","Baeza","Linares","Martos","Úbeda","Villacarrillo"],
    "Málaga": ["Málaga","Antequera","Benalmádena","Estepona","Fuengirola","Marbella","Mijas","Nerja","Ronda","Torremolinos","Vélez-Málaga"],
    "Sevilla": ["Sevilla","Alcalá de Guadaíra","Carmona","Dos Hermanas","Écija","Lebrija","Morón de la Frontera","Osuna","Utrera","Umbrete","Camas","Tomares","Bormujos","Mairena del Aljarafe"]
  },
  "Aragón": {
    "Huesca": ["Huesca","Barbastro","Jaca","Monzón","Sabiñánigo","Fraga"],
    "Teruel": ["Teruel","Alcañiz","Andorra","Utrillas","Calamocha"],
    "Zaragoza": ["Zaragoza","Calatayud","Ejea de los Caballeros","Tarazona","Utebo","Cuarte de Huerva"]
  },
  "Asturias": { "Asturias": ["Oviedo","Gijón","Avilés","Mieres","Langreo","Siero","Castrillón","Corvera de Asturias"] },
  "Baleares": { "Baleares": ["Palma","Ibiza","Manacor","Mahón","Calvià","Llucmajor","Marratxí","Sant Antoni de Portmany"] },
  "Canarias": {
    "Las Palmas": ["Las Palmas de Gran Canaria","Arrecife","Puerto del Rosario","Telde","Santa Lucía de Tirajana","San Bartolomé de Tirajana"],
    "Santa Cruz de Tenerife": ["Santa Cruz de Tenerife","San Cristóbal de La Laguna","Arona","Adeje","Puerto de la Cruz","La Orotava","Granadilla de Abona"]
  },
  "Cantabria": { "Cantabria": ["Santander","Torrelavega","Camargo","Castro-Urdiales","Piélagos","El Astillero","Medio Cudeyo"] },
  "Castilla-La Mancha": {
    "Albacete": ["Albacete","Hellín","Almansa","Villarrobledo","La Roda","Caudete"],
    "Ciudad Real": ["Ciudad Real","Alcázar de San Juan","Puertollano","Tomelloso","Valdepeñas","Manzanares"],
    "Cuenca": ["Cuenca","Tarancón","Motilla del Palancar","San Clemente"],
    "Guadalajara": ["Guadalajara","Azuqueca de Henares","Cabanillas del Campo","Alovera"],
    "Toledo": ["Toledo","Talavera de la Reina","Illescas","Consuegra","Ocaña","Seseña"]
  },
  "Castilla y León": {
    "Ávila": ["Ávila","Arenas de San Pedro","El Barco de Ávila","Arévalo"],
    "Burgos": ["Burgos","Miranda de Ebro","Aranda de Duero","Medina de Pomar"],
    "León": ["León","Ponferrada","San Andrés del Rabanedo","Astorga","La Bañeza"],
    "Palencia": ["Palencia","Aguilar de Campoo","Guardo","Venta de Baños"],
    "Salamanca": ["Salamanca","Béjar","Ciudad Rodrigo","Santa Marta de Tormes"],
    "Segovia": ["Segovia","Cuéllar","El Espinar","San Ildefonso"],
    "Soria": ["Soria","Almazán","El Burgo de Osma","Ágreda"],
    "Valladolid": ["Valladolid","Medina del Campo","Laguna de Duero","Arroyo de la Encomienda"],
    "Zamora": ["Zamora","Benavente","Toro","Puebla de Sanabria"]
  },
  "Cataluña": {
    "Barcelona": ["Barcelona","Hospitalet de Llobregat","Badalona","Terrassa","Sabadell","Mataró","Santa Coloma de Gramenet","Cornellà de Llobregat","Sant Boi de Llobregat","Rubí"],
    "Girona": ["Girona","Blanes","Lloret de Mar","Figueres","Olot","Salt"],
    "Lleida": ["Lleida","Balaguer","Cervera","Mollerussa","Tàrrega"],
    "Tarragona": ["Tarragona","Reus","Tortosa","El Vendrell","Cambrils","Salou"]
  },
  "Ceuta": { "Ceuta": ["Ceuta"] },
  "Comunidad de Madrid": { "Madrid": ["Madrid","Móstoles","Alcalá de Henares","Fuenlabrada","Leganés","Getafe","Alcorcón","Torrejón de Ardoz","Parla","Alcobendas","Las Rozas","Pozuelo de Alarcón","Majadahonda","Rivas-Vaciamadrid","Collado Villalba"] },
  "Comunidad Foral de Navarra": { "Navarra": ["Pamplona","Tudela","Barañáin","Burlada","Estella","Sarriguren","Zizur Mayor"] },
  "Comunidad Valenciana": {
    "Alicante": ["Alicante","Benidorm","Elche","Torrevieja","Orihuela","Villena","Alcoy","Petrer","San Vicente del Raspeig"],
    "Castellón": ["Castellón de la Plana","Benicàssim","Burriana","Vinaròs","Vila-real","Peñíscola"],
    "Valencia": ["Valencia","Torrent","Gandia","Paterna","Sagunto","Alzira","Mislata","Burjassot","Xirivella"]
  },
  "Extremadura": {
    "Badajoz": ["Badajoz","Mérida","Don Benito","Almendralejo","Villanueva de la Serena","Zafra"],
    "Cáceres": ["Cáceres","Plasencia","Navalmoral de la Mata","Miajadas","Trujillo"]
  },
  "Galicia": {
    "A Coruña": ["A Coruña","Santiago de Compostela","Ferrol","Lugo","Ourense","Pontevedra","Vigo"],
    "Lugo": ["Lugo","Monforte de Lemos","Vilalba","Sarria","Chantada"],
    "Ourense": ["Ourense","Verín","O Barco de Valdeorras","Xinzo de Limia"],
    "Pontevedra": ["Vigo","Pontevedra","Vilagarcía de Arousa","Redondela","Cangas","Marín","A Estrada"]
  },
  "La Rioja": { "La Rioja": ["Logroño","Calahorra","Arnedo","Haro","Nájera","Alfaro"] },
  "Melilla": { "Melilla": ["Melilla"] },
  "País Vasco": {
    "Álava": ["Vitoria-Gasteiz","Amurrio","Llodio","Salvatierra"],
    "Gipuzkoa": ["San Sebastián","Irun","Errenteria","Zarautz","Eibar","Arrasate"],
    "Bizkaia": ["Bilbao","Barakaldo","Getxo","Basauri","Leioa","Santurtzi","Portugalete"]
  },
  "Región de Murcia": { "Murcia": ["Murcia","Cartagena","Lorca","Molina de Segura","Alcantarilla","Mazarrón","San Javier","Torre-Pacheco","Yecla"] }
};

// ============================================================
// BADGE COLOR (compartido)
// ============================================================
function badgeColor(e) {
  if (e === "En adopción") return "bg-success";
  if (e === "Perdido")     return "bg-warning text-dark";
  if (e === "Abandonado")  return "bg-danger";
  if (e === "Adoptado")    return "bg-info text-dark";
  return "bg-secondary";
}

// ============================================================
// CARGAR FOTO DE PERFIL EN SIDEBAR (compartido)
// ============================================================
async function cargarFotoSidebar() {
  try {
    const res  = await fetch("/adopciones/php/obtenerPerfil.php");
    const data = await res.json();
    if (data.tieneFoto) {
      const src = `/adopciones/php/verFotoPerfil.php?id=${data.idUsuario}&t=${Date.now()}`;
      const avatarSidebar = document.getElementById("avatarSidebar");
      if (avatarSidebar) avatarSidebar.src = src;
    }
  } catch { /* silencioso */ }
}

// ============================================================
// INICIALIZAR SESIÓN Y SIDEBAR (compartido)
// ============================================================
async function inicializar() {
  try {
    const res  = await fetch("/adopciones/php/index.php");
    if (!res.ok) throw new Error();
    const data = await res.json();

    const navEl = document.getElementById("nombreUsuarioNav");
    if (navEl) navEl.textContent = data.usuario;

    const sidebarNombre = document.getElementById("sidebarNombre");
    if (sidebarNombre) sidebarNombre.textContent = data.usuario;

    window._usuarioId = data.id;

    await cargarFotoSidebar();

  } catch {
    window.location.href = "login.html";
  }

  const btnCerrar = document.getElementById("btnCerrarSesion");
  if (btnCerrar) {
    btnCerrar.addEventListener("click", async () => {
      await fetch("/adopciones/php/logout.php");
      window.location.href = "login.html";
    });
  }
}