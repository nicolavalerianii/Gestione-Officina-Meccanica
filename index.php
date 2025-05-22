<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    .mySlides {
      display: none;
    }

    .foto {
      width: 100%;
      height: 100vh;
      background-image: url(foto/sfondo.jpg);
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .overlay {
      position: absolute;
      background-color: rgba(0, 0, 0, 0.75);
      width: 100%;
      height: 100%;
    }

    .titolo {
      font-weight: bold;
      color: white;
      font-size: 4rem;
    }

    .sottotitolo {
      font-size: 2.5rem;
      font-family: 'Brush Script MT', cursive;
      font-weight: normal;
    }

    @media (max-width: 768px) {
      .titolo {
        font-size: 2.5rem;
      }

      .sottotitolo {
        font-size: 1.5rem;
      }
    }

    .fotobrand {
      width: 200px;
      height: 200px;
      object-fit: contain;
    }
    .brand-logo {
      max-height: 220px;
      object-fit: contain;
      filter: drop-shadow(0 0 5px rgba(0, 0, 0, 0.1));
    }
  </style>
</head>
<body class="d-flex flex-column" style="min-height: 100vh;">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#"><i class="bi bi-tools"></i> Potsdam Autohaus</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="#chisiamoancora">Chi siamo?</a></li>
          <li class="nav-item"><a class="nav-link" href="#servizi">Servizi</a></li>
          <li class="nav-item"><a class="nav-link" href="#contatti">Contatti</a></li>
        </ul>
        <a href="login.php" class="btn btn-warning fw-bold px-4">Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <div class="foto">
    <div class="overlay"></div>
    <div class="titolo position-relative z-1">
      POTSDAM AUTOHAUS <br>
      <div class="sottotitolo">La meccanica non si fa, si vive!</div>
    </div>
  </div>


  <div id="chisiamoancora"></div>
  <!-- Chi siamo -->
  <section id="chisiamo" class="flex-fill py-5 mt-5">
    <div class="container w-75 text-center">
      <div class="row">
        <div class="col">
          <div class="w3-content w3-section" style="max-width:500px">
            <img class="mySlides rounded-3" src="foto/foto1.jpg" style="width:100%">
            <img class="mySlides rounded-3" src="foto/foto2.jpeg" style="width:100%">
            <img class="mySlides rounded-3" src="foto/foto3.jpg" style="width:100%">
          </div>
        </div>
        <div class="col text-end mt-2">
          <h3 class="fw-bold">Benvenuti da Potsdam Autohaus</h3>
          <p class="fs-4">
            Servizi meccanici di altissima qualità, soluzioni rapide, sicure e professionali per ogni tipo di veicolo.
            Con più di 30 anni di esperienza nel settore, il nostro team è pronto a risolvere ogni problema meccanico,
            dalla manutenzione ordinaria alle riparazioni più complesse.
          </p>
        </div>
      </div>
    </div>
  </section>

<!-- Storia dell'azienda -->
<section class="py-5">
  <div class="container w-75">
    <div class="row align-items-center">
      
      <!-- Testo -->
      <div class="col-md-6">
        <h2 class="fw-bold mb-4 text-center text-md-start">La nostra storia</h2>
        <p class="fs-5">
          Fondata nel cuore della Germania riunificata nei primi anni '90, la Potsdam Autohaus nasce come una piccola officina a conduzione familiare con una grande passione per la meccanica.
        </p>
        <p class="fs-5">
          In oltre 30 anni di attività, l’azienda ha saputo evolversi mantenendo solidi valori di qualità, affidabilità e attenzione al cliente. Dalla semplice riparazione alla diagnostica avanzata, abbiamo accompagnato generazioni di automobilisti con competenza e dedizione.
        </p>
      </div>
      
      <!-- Immagine -->
      <div class="col-md-6 text-center">
        <img src="foto/potsdam.jpg" alt="Potsdam" class="img-fluid rounded-3 shadow-lg" style="max-height: 340px; object-fit: cover;">
        <p class="mt-2 text-muted">La storica città di Potsdam, dove tutto ha avuto inizio.</p>
      </div>
      
    </div>
  </div>
</section>


<div id="servizi" style="heigth: 0px;"></div>
<!-- Servizi -->
<section class="py-5" style="background-color: #212529;margin-top: 5%;">
  <div class="container w-75 text-center ">
    <h2 class="fw-bold mb-4 text-white">I nostri servizi</h2>
    <p class="fs-5 mb-5 text-white">
      Da Potsdam Autohaus, lo staff di meccanici altamente qualificati offre autoriparazioni per ogni marca e veicolo:
      meccanica, elettrauto e diagnostica professionale.
    </p>

    <div class="row g-4">
      <!-- Servizio 1 -->
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
          <img src="foto/servizi/servizio1.jpeg" class="rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="Tagliando">
          <h5 class="fw-bold">Tagliando Completo</h5>
          <p class="text-muted">
            Controlli accurati, sostituzione olio e filtri, per garantire sempre prestazioni ottimali al tuo veicolo.
          </p>
        </div>
      </div>

      <!-- Servizio 2 -->
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
          <img src="foto/servizi/servizio2.jpg" class="rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="Gomme">
          <h5 class="fw-bold">Cambio Gomme</h5>
          <p class="text-muted">
            Servizio stagionale per pneumatici, equilibratura e convergenza per la tua sicurezza su strada.
          </p>
        </div>
      </div>

      <!-- Servizio 3 -->
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
          <img src="foto/servizi/servizio3.jpeg" class="rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="Climatizzatore">
          <h5 class="fw-bold">Ricarica Clima</h5>
          <p class="text-muted">
            Ricarica gas e igienizzazione impianto per un clima interno sempre fresco e pulito.
          </p>
        </div>
      </div>

      <!-- Servizio 4 -->
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
          <img src="foto/servizi/servizio4.jpeg" class="rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="Revisione">
          <h5 class="fw-bold">Pre-Revisione</h5>
          <p class="text-muted">
            Controllo completo del veicolo in vista della revisione ufficiale: sicurezza e tranquillità garantite.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Assistenza tecnica -->
<section class="py-5 bg-white" style="margin-top: 5%; margin-bottom: 5%;">
  <div class="container w-75 text-center">
    <h2 class="fw-bold mb-5">Assistenza Tecnica Certificata Per:</h2>
    <div class="row justify-content-center align-items-center g-4">
      
      <div class="col-6 col-md-3">
        <img src="foto/brand/bmw.png" alt="BMW" class="img-fluid brand-logo">
      </div>

      <div class="col-6 col-md-3">
        <img src="foto/brand/opel.png" alt="Opel" class="img-fluid" style="max-height: 220px;object-fit: contain;">
      </div>

      <div class="col-6 col-md-3">
        <img src="foto/brand/mercedes.png" alt="Mercedes" class="img-fluid brand-logo">
      </div>

      <div class="col-6 col-md-3">
        <img src="foto/brand/porsche.png" alt="Porsche" class="img-fluid brand-logo">
      </div>

    </div>
  </div>
</section>

<!-- Sezione Contatti -->
<section id="contatti" class="py-5">
  <div class="container w-75">
    <h2 class="fw-bold mb-4 text-center">Contatti</h2>
    <div class="row align-items-center">
      <!-- Info contatti -->
      <div class="col-md-6 mb-4 mb-md-0">
        <div class="mb-3">
          <i class="bi bi-telephone-fill me-2"></i>
          <span class="fs-5">+49 331 1234567</span>
        </div>
        <div class="mb-3">
          <i class="bi bi-phone-fill me-2"></i>
          <span class="fs-5">+49 171 9876543</span>
        </div>
        <div class="mb-3">
          <i class="bi bi-envelope-fill me-2"></i>
          <span class="fs-5">info@potsdamautohaus.de</span>
        </div>
        <div class="mb-3">
          <i class="bi bi-geo-alt-fill me-2"></i>
          <span class="fs-5">Friedrich-Ebert-Straße 10, 14467 Potsdam, Germania</span>
        </div>
      </div>
      <!-- Mappa -->
      <div class="col-md-6">
        <div class="ratio ratio-4x3 rounded-3 shadow">
          <iframe 
            src="https://www.google.com/maps?q=Friedrich-Ebert-Straße+10,+14467+Potsdam,+Germania&output=embed"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Footer -->
  <footer class="container mt-auto">
    <div class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
      <div class="col-md-4 d-flex align-items-center">
        <span class="mb-3 mb-md-0 text-body-secondary">© 2025 Potsdam Autohaus SRL, tutti i diritti riservati.</span>
      </div>
      <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
        <li class="ms-3"><a class="text-body-secondary" href="http://instagram.com"><i class="bi bi-instagram"></i></a></li>
        <li class="ms-3"><a class="text-body-secondary" href="http://facebook.com"><i class="bi bi-facebook"></i></a></li>
      </ul>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <!-- Slider automatico -->
  <script>
    let myIndex = 0;
    carousel();

    function carousel() {
      const slides = document.getElementsByClassName("mySlides");
      for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
      }
      myIndex++;
      if (myIndex > slides.length) { myIndex = 1 }
      slides[myIndex - 1].style.display = "block";
      setTimeout(carousel, 2000);
    }
  </script>

</body>
</html>
