<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITC</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>

    <style>
        /* ====== VARIÁVEIS E CONFIGURAÇÕES GLOBAIS ====== */
        :root {
            --primary-color: #3a4c91;
            --secondary-color: #3c9bff;
            --accent-color: #0d6efd;
            --text-light: #ffffff;
            --text-dark: #000000;
            --shadow-light: 0 3px 6px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 8px 20px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 12px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        /* ====== ESTRUTURA PRINCIPAL ====== */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background-color: var(--primary-color);
            box-shadow: var(--shadow-medium);
        }

        html,
        body {
            height: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        main {
            flex: 1;
            padding-top: 80px;
            /* Espaço para o header fixo */
            padding-bottom: 20px;
        }

        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            text-align: center;
            padding: 15px 0;
            width: 100%;
        }

        .container-footer p {
            color: var(--text-light);
            font-size: clamp(12px, 3vw, 16px);
            margin: 0;
        }

        .container-footer p span {
            color: var(--secondary-color);
        }

        /* ====== NAVEGAÇÃO ====== */
        .nav-link {
            color: var(--text-light) !important;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
        }

        .dropdown-menu {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
        }

        /* ====== HERO SECTION ====== */
        .hero-section {
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
            z-index: 1;
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        /* ====== MOTOR DE BUSCA ====== */
        .search-box {
            background: var(--text-light);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-heavy);
            padding: 2rem;
            margin: 20px auto;
        }

        .search-box h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 12px;
            font-size: 16px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 12px 24px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
        }

        /* ====== CARDS E SEÇÕES ====== */
        .destaques,
        .dicas-noticias {
            padding: 3rem 0;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .card-img-top {
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .card-text {
            color: #6c757d;
            line-height: 1.6;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5em 0.75em;
        }


        /* ====== RESPONSIVIDADE ====== */
        @media (min-width: 768px) {
            main {
                padding-top: 100px;
            }

            .search-box {
                margin: 40px auto;
            }

            .hero-section {
                padding: 100px 0;
            }
        }

        @media (max-width: 767.98px) {
            main {
                padding-top: 70px;
            }

            .search-box {
                padding: 1.5rem;
                margin: 15px;
            }

            .card-img-top {
                height: 150px;
            }

            .destaques,
            .dicas-noticias {
                padding: 2rem 0;
            }
        }

        @media (max-width: 480px) {
            main {
                padding-top: 60px;
            }

            .search-box {
                padding: 1rem;
                margin: 10px;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                width: 100%;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 320px) {
            .search-box {
                padding: 0.75rem;
                margin: 5px;
            }

            #resultadosBusca .card img {
                height: 120px;
            }
        }

        /* Para tablets em modo retrato */
        @media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
            .search-box {
                padding: 1.5rem;
            }

            .card-img-top {
                height: 180px;
            }
        }

        /* Animações suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Melhorias de acessibilidade */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus visível para acessibilidade */
        .btn:focus,
        .form-control:focus,
        .form-select:focus {
            outline: 2px solid var(--accent-color);
            outline-offset: 2px;
        }
    </style>
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
                                <a class="nav-link" aria-current="page" href="https://www.instagram.com/itc.ac" class="me-3 text-white fs-4">
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
                                <a class="nav-link" href="https://plus.google.com/share?url=https://simplesharebuttons.com">
                                    <i class="fab fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="View/Login.php" class="btn btn-primary">Login</a>
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
        <section class="noticias" style="padding-top: 7rem;">
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">INSTITUTO DE TRANSPORTES E COMUICAÇÕES(ITC)</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Feira Do ITC</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Corte de bolo na Feira</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Card title</h5>
                            <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional
                                content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rodapé -->
        <footer>
            <div class="container-footer">
                <p>© 2019 TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
            </div>
        </footer>
    </main>
    <!-- Scripts do BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</body>

</html>