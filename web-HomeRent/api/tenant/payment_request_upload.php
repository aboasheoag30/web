<?php
declare(strict_types=1);
require_once __DIR__ . '/../_core/db.php';
require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/auth.php';
require_once __DIR__ . '/../_core/config.php';
require_once __DIR__ . '/../_core/mailer.php';
cors(); require_method('POST');

$me=require_auth();
if(($me['role']??'')!=='tenant') json_error('Forbidden',403);
$tenantId=(int)$me['uid'];

$scheduleId=safe_int($_POST['scheduleId']??0);
if($scheduleId<=0) json_error('scheduleId مطلوب',422);
if(!isset($_FILES['file'])) json_error('الملف مطلوب',422);

$f=$_FILES['file'];
if(($f['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) json_error('فشل رفع الملف',422);
$size=(int)($f['size']??0);
if($size<=0 || $size>MAX_UPLOAD_BYTES) json_error('حجم الملف غير مسموح',422);

$tmp=$f['tmp_name']??'';
$mime=mime_content_type($tmp)?:'';
if(!in_array($mime, ALLOWED_MIMES, true)) json_error('نوع الملف غير مسموح',422);
$kind=($mime==='application/pdf')?'pdf':'image';

$pdo=db();
$st=$pdo->prepare("SELECT cs.id,cs.contract_id,c.tenant_id,c.contract_number,
                          tu.full_name AS tenant_name,
                          ou.email AS owner_email, ou.full_name AS owner_name
                   FROM contract_schedules cs
                   JOIN contracts c ON c.id=cs.contract_id
                   JOIN users tu ON tu.id=c.tenant_id
                   JOIN users ou ON ou.id=c.owner_id
                   WHERE cs.id=? LIMIT 1");
$st->execute([$scheduleId]);
$sch=$st->fetch();
if(!$sch || (int)$sch['tenant_id']!==$tenantId) json_error('غير مصرح',403);

$contractId=(int)$sch['contract_id'];

$pdo->beginTransaction();
try{
  $st=$pdo->prepare("INSERT INTO payment_requests (contract_id,schedule_id,tenant_id) VALUES (?,?,?)");
  $st->execute([$contractId,$scheduleId,$tenantId]);
  $requestId=(int)$pdo->lastInsertId();

  $y=date('Y'); $m=date('m');
  $dir=UPLOAD_ROOT . "/payment_requests/$y/$m";
  if(!is_dir($dir) && !mkdir($dir,0775,true)) throw new RuntimeException('Cannot create upload folder');

  $ext=($kind==='pdf')?'pdf':($mime==='image/png'?'png':($mime==='image/webp'?'webp':'jpg'));
  $stamp=date('Ymd_His');
  $fileName="pr_{$requestId}_{$stamp}.{$ext}";
  $dest=$dir.'/'.$fileName;
  if(!move_uploaded_file($tmp,$dest)) throw new RuntimeException('Cannot move file');

  $relPath="storage/uploads/payment_requests/$y/$m/$fileName";

  $st=$pdo->prepare("INSERT INTO attachments (payment_request_id,kind,file_path,original_name,mime_type,size_bytes)
                     VALUES (?,?,?,?,?,?)");
  $st->execute([$requestId,$kind,$relPath,$f['name']??null,$mime,$size]);

  $st=$pdo->prepare("UPDATE contract_schedules SET status='pending' WHERE id=? AND status='unpaid'");
  $st->execute([$scheduleId]);

  $pdo->commit();

  if((string)$sch['owner_email']!==''){
    send_owner_new_request_email((string)$sch['owner_email'],(string)$sch['owner_name'],(string)$sch['tenant_name'],(string)$sch['contract_number']);
  }

  json_ok(['requestId'=>$requestId,'scheduleId'=>$scheduleId,'filePath'=>$relPath,'kind'=>$kind,'uploadedAt'=>date('c')]);
}catch(Throwable $e){
  $pdo->rollBack();
  json_error('فشل إنشاء طلب السداد',500,['error'=>$e->getMessage()]);
}
