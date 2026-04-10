<?php
declare(strict_types=1);

require __DIR__ . '/../includes/config.php';

$course = course_data('ciencia-da-computacao');
$activeTab = (string)($_GET['curso'] ?? 'cc');
if (!in_array($activeTab, ['cc', 'ia'], true)) {
    $activeTab = 'cc';
}

page_header('Graduacao - Ciencia da Computacao e Inteligencia Artificial');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3">Graduacao no DECOM/UFOP</h1>
    <p class="lead mb-4">Escolha a aba para ver as informacoes completas de cada curso.</p>

    <ul class="nav nav-tabs mb-4" id="graduacaoTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link<?= $activeTab === 'cc' ? ' active' : '' ?>" id="tab-cc" data-bs-toggle="tab" data-bs-target="#pane-cc" type="button" role="tab" aria-controls="pane-cc" aria-selected="<?= $activeTab === 'cc' ? 'true' : 'false' ?>">
                Ciencia da Computacao
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link<?= $activeTab === 'ia' ? ' active' : '' ?>" id="tab-ia" data-bs-toggle="tab" data-bs-target="#pane-ia" type="button" role="tab" aria-controls="pane-ia" aria-selected="<?= $activeTab === 'ia' ? 'true' : 'false' ?>">
                Inteligencia Artificial
            </button>
        </li>
    </ul>

    <div class="tab-content" id="graduacaoTabsContent">
        <div class="tab-pane fade<?= $activeTab === 'cc' ? ' show active' : '' ?>" id="pane-cc" role="tabpanel" aria-labelledby="tab-cc" tabindex="0">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3"><?= e($course['name']) ?></h2>
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

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Grade curricular (imagem e download)</h2>
                            <p class="text-muted mb-3">Visualizacao da grade curricular utilizada pelo curso.</p>
                            <a href="/uploads/grade/grade_curricular.pdf" target="_blank" rel="noopener">
                                <img
                                    src="/uploads/grade/grade_curricular_preview.jpg"
                                    alt="Preview da grade curricular de Ciencia da Computacao"
                                    class="img-fluid rounded border"
                                >
                            </a>
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <a class="btn btn-primary btn-sm" href="/uploads/grade/grade_curricular.pdf" download>Baixar grade curricular (PDF)</a>
                                <a class="btn btn-outline-secondary btn-sm" href="/uploads/grade/grade_curricular.pdf" target="_blank" rel="noopener">Abrir PDF</a>
                            </div>
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

                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Video institucional do curso</h2>
                            <div class="ratio ratio-16x9">
                                <iframe
                                    src="https://www.youtube.com/embed/PXTTDs5Lk8o"
                                    title="Apresentacao do curso de Ciencia da Computacao"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
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
                            <p class="mb-2"><strong>Status no e-MEC:</strong> curso superior em atividade.</p>
                            <p class="mb-3">Para a nota oficial mais atual (CC/CPC/ENADE), use a consulta publica do e-MEC.</p>
                            <a class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener" href="https://emec.mec.gov.br/">Consultar e-MEC</a>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h6 text-uppercase text-muted mb-3">Ingresso (SISU/ENEM)</h2>
                            <p class="mb-2">A UFOP utiliza, principalmente, o SISU (nota do ENEM) para ingresso no curso.</p>
                            <p class="mb-2"><strong>Referencia recente de nota de corte:</strong> ~699,60 pontos (SISU 2025).</p>
                            <p class="small text-muted mb-3">A nota de corte varia por modalidade e chamada.</p>
                            <a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="https://acessounico.mec.gov.br/sisu">Ver ingresso no SISU</a>
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
                Dados de ingresso e avaliacao organizados em 2 de abril de 2026.
            </div>
        </div>

        <div class="tab-pane fade<?= $activeTab === 'ia' ? ' show active' : '' ?>" id="pane-ia" role="tabpanel" aria-labelledby="tab-ia" tabindex="0">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5">Bacharelado em Inteligencia Artificial - UFOP</h2>
                    <p class="lead mb-2">
                        A UFOP aprovou a criacao de dois cursos de Bacharelado em Inteligencia Artificial em dezembro de 2025,
                        com inicio previsto para 2026 via Sisu, em Ouro Preto e Joao Monlevade.
                    </p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h3 class="h5">Campus Ouro Preto (Morro do Cruzeiro)</h3>
                            <p class="mb-2"><strong>Localizacao:</strong> ICEB.</p>
                            <p class="mb-2"><strong>Modalidade:</strong> Integral.</p>
                            <p class="mb-2"><strong>Vagas:</strong> 80 vagas anuais (40 por semestre).</p>
                            <p class="mb-2"><strong>Duracao:</strong> 4 anos (8 semestres), 2.820 horas.</p>
                            <p class="mb-0"><strong>Objetivo:</strong> formar profissionais para sistemas inteligentes e autonomos, alinhados ao PBIA.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h3 class="h5">Campus Joao Monlevade (ICEA)</h3>
                            <p class="mb-2"><strong>Localizacao:</strong> ICEA.</p>
                            <p class="mb-2"><strong>Modalidade:</strong> Matutino (presencial).</p>
                            <p class="mb-2"><strong>Vagas:</strong> 50 vagas anuais com entrada unica.</p>
                            <p class="mb-2"><strong>Duracao:</strong> 4 anos (8 semestres).</p>
                            <p class="mb-0"><strong>Foco regional:</strong> Industria 4.0, mineracao e siderurgia.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5">Matriz curricular e grade</h3>
                    <ul>
                        <li>Fundamentos matematicos, desenvolvimento de IA, raciocinio e representacao do conhecimento.</li>
                        <li>Ciencia de dados, aprendizado de maquina, percepcao e atuacao.</li>
                        <li>Aperfeicoamento pessoal e profissional (etica e governanca).</li>
                    </ul>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://seja.ufop.br/cursos/inteligencia-artificial-op">Matriz oficial - IA Ouro Preto</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://saci2.ufop.br/data/pauta/39378_matriz_curricular_ia_icea_jm.pdf">Matriz oficial - IA Joao Monlevade</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h5">Video institucional</h3>
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.youtube.com/embed/J1RCTWLMOpo" title="Entrevista sobre o curso" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="h5">Download da grade curricular</h3>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="https://saci2.ufop.br/data/pauta/39378_matriz_curricular_ia_icea_jm.pdf" download>Baixar grade - IA Joao Monlevade (PDF)</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://seja.ufop.br/cursos/inteligencia-artificial-op">Grade - IA Ouro Preto (pagina oficial)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php page_footer(); ?>
