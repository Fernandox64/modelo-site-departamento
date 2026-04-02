<?php
require __DIR__ . '/../includes/config.php';
page_header('Comunicacao e logo');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-4">Comunicacao e logo</h1>
    <p class="lead mb-4">
        Materiais oficiais de comunicacao institucional do DECOM/UFOP para uso em apresentacoes,
        documentos, divulgacao academica e projetos.
    </p>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Logomarca oficial do DECOM</h2>
                    <div class="border rounded p-4 bg-light text-center mb-3">
                        <img
                            src="http://www.decom.ufop.br/decom/site_media/img/decom_logo.png"
                            alt="Logomarca oficial do DECOM UFOP"
                            style="max-width:100%;height:auto"
                        >
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary btn-sm" target="_blank" rel="noopener"
                           href="http://www.decom.ufop.br/decom/site_media/img/decom_logo.svg">
                            Baixar logo (SVG)
                        </a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener"
                           href="http://www.decom.ufop.br/decom/site_media/img/decom_logo.png">
                            Baixar logo (PNG)
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h5 mb-3">Diretrizes de uso</h2>
                    <ul class="mb-0">
                        <li>Utilize preferencialmente o formato SVG para materiais digitais e impressos.</li>
                        <li>Mantenha proporcao e legibilidade da marca, sem deformacao.</li>
                        <li>Evite alterar cores, tipografia ou elementos da identidade visual oficial.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Contato de comunicacao</h2>
                    <p class="mb-2">
                        <strong>E-mail:</strong>
                        <a href="mailto:comunicacao.decom@ufop.edu.br">comunicacao.decom@ufop.edu.br</a>
                    </p>
                    <p class="mb-0 text-muted">
                        Canal oficial para alinhamento de divulgacao, publicacoes e materiais institucionais.
                    </p>
                </div>
            </div>

            <div class="card news-card">
                <div class="card-body">
                    <h2 class="h5 mb-3">Redes oficiais</h2>
                    <div class="d-grid gap-2">
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www.facebook.com/decomufop">Facebook @decomufop</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www.instagram.com/decom.ufop/">Instagram @decom.ufop</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www.youtube.com/channel/UCLyNoJPriWD9s8YaEGjspJw">YouTube - canal do DECOM</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-4 mb-0">
        Referencias obtidas da pagina oficial de comunicacao do DECOM em 2 de abril de 2026.
    </div>
</div>
<?php page_footer(); ?>
