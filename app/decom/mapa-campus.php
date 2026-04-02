<?php
require __DIR__ . '/../includes/config.php';
page_header('Mapa do campus');
?>
<div class="container py-4">
    <h1 class="section-title h3 mb-3">Mapa do Campus Morro do Cruzeiro</h1>
    <p class="text-muted mb-4">
        Referencias visuais do Campus Morro do Cruzeiro (UFOP), com foco no acesso ao DECOM/ICEB e aos blocos de salas de aula.
    </p>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card news-card overflow-hidden">
                <img
                    class="news-card-cover"
                    src="https://www3.decom.ufop.br/decom/site_media/uploads/cms_page_media/73/campus.png"
                    alt="Mapa oficial do Campus Morro do Cruzeiro"
                    style="height: 430px; object-fit: cover;"
                >
                <div class="card-body">
                    <h2 class="h5 mb-2">Mapa oficial do campus</h2>
                    <p class="mb-3">
                        Imagem oficial publicada pelo DECOM/UFOP para orientacao dentro do Campus Morro do Cruzeiro.
                    </p>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www3.decom.ufop.br/decom/site_media/uploads/cms_page_media/73/campus.png">Abrir imagem em tamanho original</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 mb-3">Acesso rapido</h2>
                    <p class="text-muted mb-3">
                        Use os links para navegacao por mapa e rota ate o DECOM.
                    </p>
                    <div class="d-grid gap-2 mt-auto">
                        <a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="https://maps.google.com/maps?q=-20.396606,-43.509722">Google Maps - DECOM</a>
                        <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://www3.decom.ufop.br/decom/decom/mapa-do-campus/">Pagina oficial do mapa (DECOM)</a>
                        <a class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener" href="https://maps.ufop.br/">Mapa institucional da UFOP</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card news-card h-100">
                <img
                    class="news-card-cover"
                    src="https://ufop.br/profiles/cambridge/themes/cambridge_theme/images/interface/ufop_map.png"
                    alt="Mapa institucional da UFOP"
                >
                <div class="card-body">
                    <h2 class="h5 mb-2">Mapa institucional da UFOP</h2>
                    <p class="news-summary mb-3">
                        Visao geral de acesso aos campi e servicos da universidade.
                    </p>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://maps.ufop.br/">Abrir mapa institucional</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card news-card h-100">
                <div class="ratio ratio-16x9">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d481.4437449172982!2d-43.51061733017198!3d-20.39569720777434!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xa40be440c5729b%3A0xc233c0a6fb18e034!2sDECOM%20-%20Departamento%20de%20Computa%C3%A7%C3%A3o!5e0!3m2!1spt-BR!2sbr!4v1769016209787!5m2!1spt-BR!2sbr"
                        title="Mapa interativo do DECOM no Campus Morro do Cruzeiro"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                    ></iframe>
                </div>
                <div class="card-body">
                    <h2 class="h5 mb-2">Mapa interativo (Google)</h2>
                    <p class="news-summary mb-3">
                        Localizacao exata do DECOM no Morro do Cruzeiro para facilitar deslocamento de visitantes e ingressantes.
                    </p>
                    <a class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener" href="https://maps.google.com/maps?q=-20.396606,-43.509722">Abrir rota no Google Maps</a>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info mb-0">
        Fontes consultadas em 2 de abril de 2026: pagina oficial do DECOM (mapa do campus), portal UFOP e Google Maps.
    </div>
</div>
<?php page_footer(); ?>
