<?php require_once __DIR__ . '/../../Includes/header_formador.php';
?>

    <main>
        <!-- Hero Section / Carrossel -->
        <section id="heroCarousel" class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>

            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="d-block w-100" alt="Corpo Docente ITC">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>Corpo Docente Qualificado</h1>
                        <p>Formadores experientes que impulsionam a excelência no ensino técnico-profissional.</p>
                        <a href="#sobre-formadores" class="btn btn-primary btn-lg mt-3">Saber Mais</a>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg" class="d-block w-100" alt="Formadores em Acção">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>Especialização e Inovação</h1>
                        <p>Metodologias modernas e práticas para preparar os formandos para o mercado de trabalho.</p>
                        <a href="#formadores" class="btn btn-outline-light btn-lg mt-3">Ver Formadores</a>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="carousel-item" data-bs-interval="5000">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="d-block w-100" alt="Formação e Excelência">
                    <div class="carousel-mask"></div>
                    <div class="carousel-caption">
                        <h1>Excelência no Ensino</h1>
                        <p>Décadas de experiência a formar os profissionais do futuro em Moçambique.</p>
                        <a href="#estatisticas" class="btn btn-primary btn-lg mt-3">Ver Estatísticas</a>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </section>

        <!-- Secção Sobre os Formadores -->
        <section id="sobre-formadores" class="about-section">
            <div class="container">
                <div class="section-title fade-in">
                    <h2>Sobre o Corpo Docente</h2>
                </div>

                <div class="row align-items-center g-5">
                    <!-- Esquerda: Texto institucional -->
                    <div class="col-lg-6 about-text">
                        <h2>Formadores de Referência Nacional</h2>
                        <p class="lead">O ITC conta com um corpo docente altamente qualificado, composto por profissionais com vasta experiência académica e prática nas suas áreas de especialização.</p>
                        <p>Os nossos formadores estão comprometidos com a transmissão de conhecimentos actualizados e técnicas inovadoras, garantindo que os formandos estejam prontos para os desafios do mercado de trabalho.</p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Formação académica e profissional de excelência</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Actualização contínua de conhecimentos</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Metodologias pedagógicas inovadoras</li>
                        </ul>
                    </div>

                    <!-- Direita: Icon-boxes -->
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-chalkboard"></i>
                                    <h4>Pedagogia</h4>
                                    <p class="text-muted small">Métodos modernos centrados no formando e na aprendizagem prática.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-trophy"></i>
                                    <h4>Excelência</h4>
                                    <p class="text-muted small">Avaliação média acima de 4.7/5 pelos formandos de todos os cursos.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-book-open"></i>
                                    <h4>Experiência</h4>
                                    <p class="text-muted small">Mais de 20 anos de experiência acumulada no ensino técnico-profissional.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="icon-box">
                                    <i class="bx bx-certification"></i>
                                    <h4>Certificação</h4>
                                    <p class="text-muted small">Formadores certificados a nível nacional e internacional.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Secção de Estatísticas -->
        <section id="estatisticas" class="formadores-stats">
            <div class="container">
                <div class="section-title fade-in">
                    <h2>Em Números</h2>
                </div>
                <div class="row text-center g-4">
                    <div class="col-md-3 col-6">
                        <div class="stat-box">
                            <h4 class="stat-number">50+</h4>
                            <p class="stat-label">Formadores</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-box">
                            <h4 class="stat-number">20+</h4>
                            <p class="stat-label">Anos de Experiência</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-box">
                            <h4 class="stat-number">15</h4>
                            <p class="stat-label">Áreas de Especialização</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-box">
                            <h4 class="stat-number">5000+</h4>
                            <p class="stat-label">Alunos Formados</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Listagem de Formadores -->
        <section id="formadores" class="formadores-list">
            <div class="container">
                <div class="section-title fade-in">
                    <h2>Conheça os Nossos Formadores</h2>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">

                    <!-- Formador 1 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="card-img-top" alt="Barbione Aogusto">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">Informática</span>
                                <h5 class="card-title">Barbione Aogusto</h5>
                                <p class="card-text small text-muted">Especialista em Sistemas</p>
                                <p class="card-text">Formador com mais de 15 anos de experiência em desenvolvimento de software e gestão de sistemas.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 200+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.8/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 2 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg" class="card-img-top" alt="Sheila Momade">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-success mb-2">Comunicações</span>
                                <h5 class="card-title">Sheila Momade</h5>
                                <p class="card-text small text-muted">Especialista em Redes</p>
                                <p class="card-text">Profissional dedicada ao ensino de infraestruturas de comunicação e redes de dados.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 180+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.9/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 3 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Evaristo Escrivao">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">Transportes</span>
                                <h5 class="card-title">Evaristo Escrivão</h5>
                                <p class="card-text small text-muted">Especialista em Logística</p>
                                <p class="card-text">Formador experiente em gestão de transportes e cadeia de abastecimento.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 220+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.7/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 4 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Zefanias Malate">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-info mb-2">Gestão</span>
                                <h5 class="card-title">Zefanias Malate</h5>
                                <p class="card-text small text-muted">Especialista em Administração</p>
                                <p class="card-text">Especialista em gestão empresarial e administração de recursos humanos.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 190+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.8/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 5 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Angelo">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-danger mb-2">Eletrónica</span>
                                <h5 class="card-title">Angelo</h5>
                                <p class="card-text small text-muted">Especialista em Eletrônica</p>
                                <p class="card-text">Formador com vasta experiência em instalação e manutenção de sistemas eletrónicos.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 170+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.6/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 6 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Rita Nunes">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2">Soft Skills</span>
                                <h5 class="card-title">Rita Nunes</h5>
                                <p class="card-text small text-muted">Especialista em Desenvolvimento Pessoal</p>
                                <p class="card-text">Formadora especializada em competências transversais e desenvolvimento pessoal.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 250+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.9/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
</body>

</html>