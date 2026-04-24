<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITC</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="Assets/CSS/global.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>


</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://www.instagram.com/itc.ac"
                                    class="me-3 text-white fs-4">
                                    <i class="fa-brands fa-square-instagram" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://pt-br.facebook.com/itc.transcom">
                                    <i class="fa-brands fa-facebook" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="https://plus.google.com/share?url=https://simplesharebuttons.com">
                                    <i class="fab fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/estagio/login" class="btn btn-primary">Login</a>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>

        <!-- Nav Secundária -->
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero Section / Carrossel -->
        <section id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="d-block w-100"
                        alt="Fachada do Instituto">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>Instituto de Transportes e Comunicações</h1>
                        <p>Excelência no ensino técnico-profissional há várias décadas.</p>
                        <a href="#sobre" class="btn btn-primary btn-lg mt-3">Saber Mais</a>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg"
                        class="d-block w-100" alt="Feira do ITC">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>Inovação e Tecnologia</h1>
                        <p>Acompanhamos a evolução preparando-o para o mercado de trabalho do futuro.</p>
                        <a href="/estagio/login" class="btn btn-outline-light btn-lg mt-3">Faça Login</a>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg"
                        class="d-block w-100" alt="Eventos e Formação">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>O Seu Futuro Começa Aqui</h1>
                        <p>Junte-se à nossa comunidade dinâmica e desenvolva suas capacidades.</p>
                        <a href="#noticias" class="btn btn-primary btn-lg mt-3">Ver Notícias</a>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </section>

        <!-- Secção Sobre a Escola (About) -->
        <section id="sobre" class="about-section">
            <div class="container">
                <div class="section-title fade-in">
                    <h2>Sobre o ITC</h2>
                </div>

                <div class="row align-items-center g-5">
                    <!-- Esquerda: Texto institucional -->
                    <div class="col-lg-6 about-text">
                        <h2>Formando Profissionais de Excelência</h2>
                        <p class="lead">O Instituto de Transportes e Comunicações (ITC) é uma instituição de ensino
                            técnico e profissional de referência nacional, focada em entregar educação de excelência e
                            prática.</p>
                        <p>Nossa missão é qualificar jovens e adultos para o mercado de trabalho dinâmico, fornecendo
                            competências sólidas nas áreas das tecnologias de informação, gestão empresarial, e claro,
                            no nosso forte que engloba as comunicações e vias de transporte.</p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Corpo docente
                                altamente qualificado</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Instalações e
                                laboratórios modernos</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Alta taxa de
                                empregabilidade dos formandos</li>
                        </ul>
                    </div>

                    <!-- Direita: Grelha de Ícones / Vantagens -->
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-laptop"></i>
                                    <h4>Tecnologia</h4>
                                    <p class="text-muted small">Cursos focados nas tendências informáticas actuais.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-run"></i>
                                    <h4>Desporto & Cultura</h4>
                                    <p class="text-muted small">Promoção de eventos dinamizadores anuais.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-briefcase-alt-2"></i>
                                    <h4>Mercado</h4>
                                    <p class="text-muted small">Programas modulares alinhados ao mercado corporativo.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-certification"></i>
                                    <h4>Certificação</h4>
                                    <p class="text-muted small">Diplomas reconhecidos a nível nacional e internacional.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Secção de Destaques / Notícias -->
        <section id="noticias" class="destaques bg-white">
            <div class="container">
                <div class="section-title fade-in">
                    <h2>Destaques & Eventos</h2>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">

                    <!-- Card 1 -->
                    <div class="col fade-in">
                        <div class="card h-100">
                            <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="card-img-top"
                                alt="Fachada">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">Institucional</span>
                                <h5 class="card-title">Abertura do Novo Ano Lectivo</h5>
                                <p class="card-text">O Instituto de Transportes e Comunicações (ITC) prepara o arranque
                                    de mais um ano lectivo repleto de novos desafios e infraestruturas renovadas para os
                                    formandos.</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="#" class="btn btn-outline-primary w-100">Ler Artigo</a>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col fade-in text-center" style="animation-delay: 0.1s;">
                        <div class="card h-100">
                            <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg"
                                class="card-img-top" alt="Feira">
                            <div class="card-body text-start">
                                <span class="badge bg-success mb-2">Eventos</span>
                                <h5 class="card-title">Grande Feira de Ciências e Tecnologia</h5>
                                <p class="card-text">Participe na maior expô anual onde formandos expõem projectos
                                    tecnológicos, soluções de electrónica e propostas na área das comunicações criadas
                                    pelos mesmos.</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="#" class="btn btn-outline-primary w-100">Ver Galeria</a>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="col fade-in" style="animation-delay: 0.2s;">
                        <div class="card h-100">
                            <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg"
                                class="card-img-top" alt="Cerimônia">
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">Graduação</span>
                                <h5 class="card-title">Cerimónia de Graduação e Corte do Bolo</h5>
                                <p class="card-text">Celebramos hoje a transição e a inserção no mercado de trabalho de
                                    mais uma centena de finalistas dos diversos cursos modulares realizados no ITC.</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <a href="#" class="btn btn-outline-primary w-100">Ler Mais</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>
    <!-- Rodapé -->
    <?php require_once __DIR__ . '/Includes/footer.php'?>
</body>

</html>