<?php
session_start();
require 'conexao.php'; // deve definir $pdo (PDO)
if (empty($pdo)) {
  // tenta criar PDO se conexao.php não fez
  try {
    $pdo = new PDO("mysql:host=localhost;dbname=sistema_adocao", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
  }
}

try {
  // busca todos os pets com dados do dono + tipo + raça
  $sql = "SELECT 
                p.id,
                p.nome AS pet_nome,
                t.descricao AS tipo,
                r.descricao AS raca,
                u.nome AS owner_nome,
                u.cep AS owner_cep
            FROM pets p
            LEFT JOIN tipo t ON t.id_tipo = p.id_tipo
            LEFT JOIN raca r ON r.id_raca = p.id_raca
            LEFT JOIN usuario u ON u.cpf = p.usuario_cpf
            ORDER BY p.nome ASC";
  $stmt = $pdo->query($sql);
  $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Erro ao buscar pets: " . $e->getMessage());
}

// envia os dados para o JS
$pets_json = json_encode($pets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Mapa — Todos os Pets</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    #map {
      height: calc(90vh - 200px);
      border-radius: 8px;
    }

    body {
      padding-top: 5%;
    }
  </style>
</head>

<body class="bg-gray-50">
  <?php include 'pedaco.php'; ?>

  <main class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Mapa — Localização de todos os pets</h1>
    <div id="map" class="mb-4"></div>
    <div id="status" class="text-sm text-gray-600"></div>
  </main>

  <script>
    const pets = <?php echo $pets_json; ?>;

    // agrupa pets por cep limpo (8 dígitos). mantém pets sem cep como null
    const cepMap = {};
    pets.forEach(p => {
      const raw = (p.owner_cep || '').toString();
      const cep = raw.replace(/\D/g, '');
      const key = (cep.length === 8) ? cep : null;
      if (!cepMap[key]) cepMap[key] = [];
      cepMap[key].push(p);
    });

    // init map centrado no Brasil
    const map = L.map('map').setView([-15.7801, -47.9292], 4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const statusEl = document.getElementById('status');
    const markers = L.layerGroup().addTo(map);

    const wait = ms => new Promise(res => setTimeout(res, ms));

    // ícones base por tipo (com URL apenas)
    const iconsBase = {
      'Cachorro': 'img-site/dog.png',
      'Gato': 'img-site/gato.png',
      'Coelho': 'img-site/coelho.png',
      'Pássaro': 'img-site/passaro.png',
      'Hamster': 'img-site/hamster.png',
      'Réptil': 'img-site/reptil.png',
      'Peixe': 'img-site/peixe.png',
      'Cavalo': 'img-site/cavalo.png',
      'default': 'img-site/default.png'
    };

    // função para criar ícone dinâmico baseado no zoom
    function createIcon(tipo, zoom) {
      const size = 40 + zoom * 5; // base 40, aumenta 5px por nível de zoom
      const url = iconsBase[tipo] || iconsBase['default'];
      return L.icon({
        iconUrl: url,
        iconSize: [size, size],
        iconAnchor: [size / 2, size], // centraliza na base
        popupAnchor: [0, -size]
      });
    }

    // armazenar referência tipo-id para cada marker
    const markerData = [];

    (async function() {
      const ceps = Object.keys(cepMap).filter(k => k); // remove null
      statusEl.textContent = `Processando ${ceps.length} CEP(s)...`;

      for (let i = 0; i < ceps.length; i++) {
        const cep = ceps[i];
        try {
          const res = await fetch('geocode_cep.php?cep=' + encodeURIComponent(cep), {
            credentials: 'same-origin'
          });
          if (!res.ok) {
            console.warn('geocode erro para', cep, res.status);
            statusEl.textContent = `CEP ${cep}: sem resultado (status ${res.status})`;
            await wait(300);
            continue;
          }
          const data = await res.json();
          if (data && data.lat && data.lon) {
            const petsHere = cepMap[cep];
            petsHere.forEach(pt => {
              const marker = L.marker([data.lat, data.lon], {
                icon: createIcon(pt.tipo, map.getZoom())
              }).addTo(markers);
              const popupContent = `<div class="pet-popup"><strong>${escapeHtml(pt.pet_nome)}</strong><br/>
                                ${escapeHtml(pt.tipo || '')} ${pt.raca ? '- ' + escapeHtml(pt.raca) : ''}<br/>
                                <a href="adotar.php?id=${encodeURIComponent(pt.id)}" target="_blank">Ver detalhes</a></div>`;
              marker.bindPopup(popupContent);

              // guardar para zoom dinamico
              markerData.push({
                marker,
                tipo: pt.tipo
              });
            });
          } else {
            console.warn('sem coords para cep', cep, data);
          }
        } catch (err) {
          console.error('Erro ao geocodificar cep', cep, err);
        }
        await wait(500);
        statusEl.textContent = `Processados ${i + 1}/${ceps.length} CEP(s)...`;
      }

      if (cepMap[null] && cepMap[null].length) {
        const center = [-15.7801, -47.9292];
        cepMap[null].forEach(pt => {
          const marker = L.marker(center, {
            icon: createIcon(pt.tipo, map.getZoom()),
            opacity: 0.7
          }).addTo(markers);
          marker.bindPopup(`<strong>${escapeHtml(pt.pet_nome)}</strong><br/>CEP do dono não disponível.<br/><a href="adotar.php?id=${encodeURIComponent(pt.id)}" target="_blank">Ver detalhes</a>`);
          markerData.push({
            marker,
            tipo: pt.tipo
          });
        });
      }

      statusEl.textContent = 'Mapa carregado.';
      const all = markers.getLayers();
      if (all.length) {
        const group = L.featureGroup(all);
        map.fitBounds(group.getBounds().pad(0.2));
      }
    })();

    // atualizar ícones ao alterar zoom
    map.on('zoomend', () => {
      const zoom = map.getZoom();
      markerData.forEach(m => {
        m.marker.setIcon(createIcon(m.tipo, zoom));
      });
    });

    function escapeHtml(s) {
      if (!s) return '';
      return String(s).replace(/[&<>"']/g, m => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
      } [m]));
    }
  </script>
  <?php include 'footer.php'; ?>
</body>

</html>