<?php
require __DIR__ . '/../includes/config.php';
page_header('Localizacao');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3">Localizacao do DECOM</h1>
    <p class="text-muted mb-4">
        Confira o mapa oficial do Departamento de Computacao da UFOP, os dados da secretaria e os principais predios onde acontecem atividades de ensino.
    </p>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card news-card overflow-hidden">
                <div class="ratio ratio-16x9">
                    <iframe
                        src="https://www.google.com/maps?q=-20.396606,-43.509722&z=17&output=embed"
                        title="Mapa da localizacao do DECOM UFOP"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
                <div class="card-body">
                    <h2 class="h5 mb-2">Endereco institucional</h2>
                    <p class="mb-3">
                        Departamento de Computacao (DECOM) - Instituto de Ciencias Exatas e Biologicas (ICEB)<br>
                        Universidade Federal de Ouro Preto - Campus Universitario Morro do Cruzeiro<br>
                        Ouro Preto - MG, Brasil
                    </p>
                    <a
                        class="btn btn-outline-primary btn-sm"
                        href="https://maps.google.com/maps?q=-20.396606,-43.509722"
                        target="_blank"
                        rel="noopener"
                    >
                        Abrir no Google Maps
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Secretaria do DECOM</h2>
                    <p class="mb-2"><strong>E-mail:</strong> <a href="mailto:decom@ufop.edu.br">decom@ufop.edu.br</a></p>
                    <p class="mb-2"><strong>Telefone:</strong> +55 (31) 3559-1692</p>
                    <p class="mb-0 text-muted">
                        Atendimento administrativo do departamento no campus Morro do Cruzeiro.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Links rapidos</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www3.decom.ufop.br/decom/decom/localizacao/">Pagina oficial de localizacao (DECOM)</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://iceb.ufop.br/">Site do ICEB</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://maps.ufop.br/">Mapa da UFOP</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card news-card h-100">
                <div class="card-body">
                    <span class="badge text-bg-primary mb-2">Predio Academico</span>
                    <h2 class="h5 mb-2">ICEB</h2>
                    <p class="news-summary mb-3">
                        O Instituto de Ciencias Exatas e Biologicas concentra atividades do DECOM e de outros cursos, com laboratorios, salas e setores administrativos.
                    </p>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://iceb.ufop.br/">Ver detalhes do ICEB</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card news-card h-100">
                <div class="card-body">
                    <span class="badge text-bg-success mb-2">Predio de Aulas</span>
                    <h2 class="h5 mb-2">Bloco de Salas de Aula</h2>
                    <p class="news-summary mb-3">
                        Parte das disciplinas de graduacao e atividades letivas ocorre em salas do bloco de aulas do campus, conforme a alocacao semestral.
                    </p>
                    <a class="btn btn-outline-success btn-sm" target="_blank" rel="noopener" href="https://iceb.ufop.br/salas-de-aula">Consultar alocacao de salas</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php page_footer(); ?>
