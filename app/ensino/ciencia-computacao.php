<?php
require __DIR__ . '/../includes/config.php';

$course = course_data('ciencia-da-computacao');
page_header('Ciencia da Computacao');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3"><?= e($course['name']) ?></h1>
    <p class="lead mb-4">
        Guia rapido para interessados e ingressantes no curso de Ciencia da Computacao da UFOP.
    </p>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Visao geral do curso</h2>
                    <p class="mb-3">
                        O curso tem foco em formacao solida em fundamentos de computacao e desenvolvimento de software,
                        com base teorica e pratica para atuar em engenharia de software, dados, inteligencia artificial,
                        sistemas, redes e pesquisa academica.
                    </p>
                    <p class="mb-0">
                        A estrutura curricular combina matematica e logica, algoritmos, programacao, arquitetura,
                        banco de dados, sistemas operacionais, redes, engenharia de software e disciplinas optativas
                        para trilhas de especializacao ao longo da graduacao.
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Grade curricular e formacao</h2>
                    <p class="mb-3">A grade curricular e organizada em eixos:</p>
                    <ul class="mb-3">
                        <li>Fundamentos matematicos e teoricos (calculo, algebra, matematica discreta, logica).</li>
                        <li>Programacao e algoritmos (introducao, estruturas de dados, paradigmas).</li>
                        <li>Sistemas computacionais (arquitetura, sistemas operacionais, redes).</li>
                        <li>Software e dados (engenharia de software, banco de dados, qualidade e testes).</li>
                        <li>Complementacao e aprofundamento (optativas, projetos, atividades complementares e TCC).</li>
                    </ul>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener"
                       href="https://zeppelin10.ufop.br/SistemaAcademico/MatrizCurricular?codCurso=COM">
                        Ver matriz curricular oficial
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">O que o curso oferece</h2>
                    <ul class="mb-0">
                        <li>Laboratorios de pesquisa e desenvolvimento no DECOM.</li>
                        <li>Projetos de pesquisa e extensao com aplicacao real.</li>
                        <li>Experiencia com desenvolvimento de software e ciencia de dados.</li>
                        <li>Base forte para mercado de tecnologia e para pos-graduacao.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Dados academicos</h2>
                    <p><strong>Modalidade:</strong> <?= e($course['modality']) ?></p>
                    <p><strong>Duracao:</strong> <?= e($course['duration']) ?></p>
                    <p class="mb-0"><strong>Turno:</strong> <?= e($course['shift']) ?></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">MEC e avaliacao</h2>
                    <p class="mb-2">
                        <strong>Status no e-MEC:</strong> curso superior em atividade.
                    </p>
                    <p class="mb-3">
                        Para a nota oficial mais atual do curso (CC/CPC/ENADE), use a consulta publica do e-MEC,
                        pois esses indicadores podem mudar a cada ciclo de avaliacao.
                    </p>
                    <a class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener"
                       href="https://emec.mec.gov.br/">
                        Consultar e-MEC
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Ingresso (SISU/ENEM)</h2>
                    <p class="mb-2">
                        A UFOP utiliza, principalmente, o SISU (nota do ENEM) para ingresso no curso.
                    </p>
                    <p class="mb-2">
                        <strong>Referencia recente de nota de corte:</strong> ~699,60 pontos
                        (SISU 2025, modalidade escola publica/PPI).
                    </p>
                    <p class="small text-muted mb-3">
                        Observacao: a nota de corte varia por modalidade e chamada. Consulte sempre o processo seletivo vigente.
                    </p>
                    <a class="btn btn-primary btn-sm" target="_blank" rel="noopener"
                       href="https://acessounico.mec.gov.br/sisu">
                        Ver ingresso no SISU
                    </a>
                </div>
            </div>

            <div class="card news-card">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Links uteis</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www3.decom.ufop.br/decom/ensino/ciencia-computacao/">Pagina oficial do curso</a>
                        <a class="btn btn-outline-primary btn-sm" href="/pesquisa/labs.php">Laboratorios do DECOM</a>
                        <a class="btn btn-outline-primary btn-sm" href="/pesquisa/projetos.php">Projetos de pesquisa/extensao</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        Dados de ingresso e avaliacao consultados e organizados em 2 de abril de 2026.
        Recomendado confirmar periodicamente no e-MEC, SISU e nos canais oficiais da UFOP.
    </div>
</div>
<?php page_footer(); ?>
