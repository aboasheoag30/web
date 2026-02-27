<?php
class FPDF{
protected $page,$n,$offsets,$buffer,$pages,$state,$compress,$k,$wPt,$hPt,$w,$h,$lMargin,$tMargin,$rMargin,$bMargin,$cMargin,$x,$y,$lasth,$LineWidth,$CoreFonts,$fonts,$FontFamily,$FontStyle,$underline,$CurrentFont,$FontSizePt,$FontSize,$DrawColor,$FillColor,$TextColor,$ColorFlag,$ws,$AutoPageBreak,$PageBreakTrigger,$InHeader,$InFooter,$PDFVersion,$AliasNbPages,$info,$catalog;
function __construct($orientation='P',$unit='mm',$size='A4'){
$this->state=0;$this->page=0;$this->n=2;$this->buffer='';$this->pages=[];$this->offsets=[];
$this->CoreFonts=['courier'=>'Courier','courierB'=>'Courier-Bold','courierI'=>'Courier-Oblique','courierBI'=>'Courier-BoldOblique',
'helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold','helveticaI'=>'Helvetica-Oblique','helveticaBI'=>'Helvetica-BoldOblique',
'times'=>'Times-Roman','timesB'=>'Times-Bold','timesI'=>'Times-Italic','timesBI'=>'Times-BoldItalic','symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats'];
$this->fonts=[];
$this->k=($unit=='pt'?1:($unit=='mm'?72/25.4:($unit=='cm'?72/2.54:($unit=='in'?72:1))));
$Std=['a4'=>[595.28,841.89]];
if(is_string($size)){$size=strtolower($size);$size=$Std[$size]??$Std['a4'];}
$this->wPt=$size[0];$this->hPt=$size[1];
$this->w=$this->wPt/$this->k;$this->h=$this->hPt/$this->k;
$this->lMargin=10;$this->tMargin=10;$this->rMargin=10;$this->bMargin=10;$this->cMargin=1;$this->x=$this->lMargin;$this->y=$this->tMargin;
$this->LineWidth=.2;$this->DrawColor='0 G';$this->FillColor='0 g';$this->TextColor='0 g';$this->ColorFlag=false;
$this->FontFamily='';$this->FontStyle='';$this->FontSizePt=12;$this->FontSize=12/$this->k;$this->underline=false;$this->CurrentFont=[];
$this->AutoPageBreak=true;$this->PageBreakTrigger=$this->h-$this->bMargin;$this->InHeader=false;$this->InFooter=false;
$this->AliasNbPages='{nb}';$this->PDFVersion='1.3';$this->compress=function_exists('gzcompress');}
function SetMargins($l,$t,$r=null){$this->lMargin=$l;$this->tMargin=$t;$this->rMargin=($r===null?$l:$r);}
function SetAutoPageBreak($a,$m=0){$this->AutoPageBreak=$a;$this->bMargin=$m;$this->PageBreakTrigger=$this->h-$m;}
function AddPage(){
if($this->state==0)$this->Open();
$family=$this->FontFamily;$style=$this->FontStyle;$size=$this->FontSizePt;
if($this->page>0){$this->InFooter=true;$this->Footer();$this->InFooter=false;$this->_endpage();}
$this->_beginpage();$this->InHeader=true;$this->Header();$this->InHeader=false;
$this->SetFont($family,$style,$size);
}
function Header(){} function Footer(){}
function SetFont($family,$style='',$size=0){
$family=strtolower($family); if($family=='')$family=$this->FontFamily;
$style=strtoupper($style);
if(strpos($style,'U')!==false){$this->underline=true;$style=str_replace('U','',$style);} else $this->underline=false;
if($size==0)$size=$this->FontSizePt;
if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)return;
$key=$family.$style;
if(!isset($this->fonts[$key])){
if(isset($this->CoreFonts[$key])) $name=$this->CoreFonts[$key];
elseif(isset($this->CoreFonts[$family])) $name=$this->CoreFonts[$family];
else throw new Exception('Undefined font');
$this->fonts[$key]=['i'=>count($this->fonts)+1,'type'=>'core','name'=>$name,'up'=>-100,'ut'=>50,'cw'=>[]];
}
$this->FontFamily=$family;$this->FontStyle=$style;$this->FontSizePt=$size;$this->FontSize=$size/$this->k;$this->CurrentFont=$this->fonts[$key];
if($this->page>0)$this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}
function Ln($h=null){$this->x=$this->lMargin;$this->y+=($h===null?$this->lasth:$h);}
function GetStringWidth($s){return strlen((string)$s)*0.5*$this->FontSize;}
function AcceptPageBreak(){return $this->AutoPageBreak;}
function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=false){
if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()){$x=$this->x;$this->AddPage();$this->x=$x;}
if($w==0)$w=$this->w-$this->rMargin-$this->x;
$s='';
if($fill||$border){$op=$fill?($border?'B':'f'):'S';$s.=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$this->k,($this->h-$this->y)*$this->k,$w*$this->k,-$h*$this->k,$op);}
if($txt!==''){
$dx=2;
if($align=='R')$dx=$w-2-$this->GetStringWidth($txt);
elseif($align=='C')$dx=($w-$this->GetStringWidth($txt))/2;
$txt2=str_replace(')','\)',str_replace('(','\(',str_replace('\\','\\\\',$txt)));
$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$this->k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$this->k,$txt2);
}
if($s)$this->_out($s);
$this->lasth=$h;
if($ln>0){$this->y+=$h;$this->x=$this->lMargin;} else $this->x+=$w;
}
function Output($dest='D',$name='doc.pdf'){
if($this->state<3)$this->Close();
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'.$name.'"');
echo $this->buffer;
}
function Open(){$this->state=1;}
function Close(){
if($this->state==3)return;
if($this->page==0)$this->AddPage();
$this->InFooter=true;$this->Footer();$this->InFooter=false;
$this->_endpage();$this->_enddoc();$this->state=3;
}
function _beginpage(){$this->page++;$this->pages[$this->page]='';$this->state=2;$this->x=$this->lMargin;$this->y=$this->tMargin;$this->FontFamily='';}
function _endpage(){$this->state=1;}
function _out($s){ if($this->state==2)$this->pages[$this->page].=$s."\n"; else $this->buffer.=$s."\n"; }
function _enddoc(){ $this->_putheader(); $this->_putpages(); $this->_putresources(); $this->_putinfo(); $this->_putcatalog(); $this->_putxref(); }
function _putheader(){ $this->_out('%PDF-'.$this->PDFVersion); }
function _newobj(){ $this->n++; $this->offsets[$this->n]=strlen($this->buffer); $this->_out($this->n.' 0 obj'); return $this->n; }
function _putpages(){
$nb=$this->page;
for($n=1;$n<=$nb;$n++){
$this->_newobj();$this->_out('<</Type /Page /Parent 1 0 R /Resources 2 0 R /MediaBox [0 0 '.$this->wPt.' '.$this->hPt.'] /Contents '.($this->n+1).' 0 R>>');$this->_out('endobj');
$p=$this->pages[$n];
$this->_newobj();
if($this->compress){$p=gzcompress($p);$this->_out('<</Length '.strlen($p).' /Filter /FlateDecode>>');}
else $this->_out('<</Length '.strlen($p).'>>');
$this->_out('stream');$this->_out($p);$this->_out('endstream');$this->_out('endobj');
}
$this->offsets[1]=strlen($this->buffer);
$this->_out('1 0 obj');$this->_out('<</Type /Pages /Kids [');
for($i=0;$i<$nb;$i++)$this->_out((3+2*$i).' 0 R');
$this->_out('] /Count '.$nb.'>>');$this->_out('endobj');
}
function _putresources(){
$this->offsets[2]=strlen($this->buffer);
$this->_out('2 0 obj');$this->_out('<</ProcSet [/PDF /Text] /Font <<');
foreach($this->fonts as $font){
$this->_newobj();
$this->_out('<</Type /Font /Subtype /Type1 /BaseFont /'.$font['name'].' /Encoding /WinAnsiEncoding>>');$this->_out('endobj');
$this->_out('/F'.$font['i'].' '.$this->n.' 0 R');
}
$this->_out('>> >>');$this->_out('endobj');
}
function _putinfo(){ $this->_newobj(); $this->_out('<</Producer (FPDF) >>'); $this->_out('endobj'); $this->info=$this->n; }
function _putcatalog(){ $this->_newobj(); $this->_out('<</Type /Catalog /Pages 1 0 R>>'); $this->_out('endobj'); $this->catalog=$this->n; }
function _putxref(){
$offset=strlen($this->buffer);
$this->_out('xref');$this->_out('0 '.($this->n+1));$this->_out('0000000000 65535 f ');
for($i=1;$i<=$this->n;$i++)$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]??0));
$this->_out('trailer');$this->_out('<</Size '.($this->n+1).' /Root '.$this->catalog.' 0 R /Info '.$this->info.' 0 R>>');
$this->_out('startxref');$this->_out($offset);$this->_out('%%EOF');
}
}
