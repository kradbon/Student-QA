<?php
declare(strict_types=1);
require dirname(__DIR__,2) . '/config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
$q=trim($_GET['q']??'');
if(mb_strlen($q)<4){echo json_encode(['data'=>[]]);exit;}
$terms=preg_split('/\s+/u',$q);
$like='%'.$q.'%';
$stmt=db()->prepare('SELECT id,title FROM questions WHERE status="published" AND (title LIKE ? OR body LIKE ?) ORDER BY created_at DESC LIMIT 7');
$stmt->execute([$like,$like]);
echo json_encode(['data'=>$stmt->fetchAll()],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
