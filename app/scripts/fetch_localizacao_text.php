<?php
$ctx=stream_context_create([
 'http'=>['timeout'=>25,'header'=>"User-Agent: decom-localizacao-fetch/1.0\r\n"],
 'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false],
]);
$url='https://www3.decom.ufop.br/decom/decom/localizacao/';
$html=@file_get_contents($url,false,$ctx);
if($html===false){fwrite(STDERR,"FAIL\n"); exit(1);} 
$txt=html_entity_decode(strip_tags($html), ENT_QUOTES|ENT_HTML5, 'UTF-8');
$txt=preg_replace('/\s+/u',' ', $txt);
$sent=preg_split('/(?<=[\.!?])\s+/u', $txt);
foreach($sent as $s){
 if(preg_match('/DECOM|Departamento|Computa|secretaria|ICEB|pavilh|sala|aula|campus|Morro|Cruzeiro|Ouro Preto|bloco/iu',$s)){
   echo trim($s),"\n";
 }
}
