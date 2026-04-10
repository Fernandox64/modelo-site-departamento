<?php
$ctx=stream_context_create([
 'http'=>['timeout'=>25,'header'=>"User-Agent: decom-localizacao-fetch/1.0\r\n"],
 'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false],
]);
$url='https://www3.decom.ufop.br/decom/decom/localizacao/';
$html=@file_get_contents($url,false,$ctx);
if($html===false){fwrite(STDERR,"FAIL\n"); exit(1);} 
$dom=new DOMDocument(); libxml_use_internal_errors(true); $dom->loadHTML($html); libxml_clear_errors();
$xp=new DOMXPath($dom);
$ifr=$xp->query('//iframe');
foreach($ifr as $i){ echo trim($i->getAttribute('src')),PHP_EOL; }
