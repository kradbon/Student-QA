<?php
declare(strict_types=1);
require dirname(__DIR__) . '/config/bootstrap.php';
verify_csrf();
$page = $_GET['page'] ?? 'home';

if ($page === 'language') {
    $_SESSION['lang'] = in_array($_POST['lang'] ?? '', ['ru','tj','en'], true) ? $_POST['lang'] : 'ru';
    redirect($_SERVER['HTTP_REFERER'] ?? '?');
}
if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? ''); $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || strlen($password) < 8) { flash('Enter a name, valid email and password (8+ characters).','danger'); redirect('?page=register'); }
    $role = db()->query("SELECT id FROM roles WHERE name='student'")->fetchColumn();
    $stmt = db()->prepare('INSERT INTO users(role_id,full_name,email,password_hash) VALUES(?,?,?,?)');
    try { $stmt->execute([$role,$name,$email,password_hash($password,PASSWORD_DEFAULT)]); flash('Account created. Please log in.'); redirect('?page=login'); }
    catch (PDOException $e) { flash('This email may already be registered.','danger'); redirect('?page=register'); }
}
if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('SELECT u.*,r.name role FROM users u JOIN roles r ON r.id=u.role_id WHERE email=? AND status="active"');
    $stmt->execute([trim($_POST['email'] ?? '')]); $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true); $_SESSION['user']=['id'=>$u['id'],'name'=>$u['full_name'],'role'=>$u['role'],'reputation'=>$u['reputation']];
        redirect('?');
    }
    flash('Invalid credentials.','danger'); redirect('?page=login');
}
if ($page === 'logout') { $_SESSION=[]; session_destroy(); redirect('?'); }

if ($page === 'ask' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u=require_login(); $title=trim($_POST['title']??''); $body=trim($_POST['body']??'');
    if (mb_strlen($title)<12 || mb_strlen($body)<20) { flash('Title must be 12+ characters and details 20+ characters.','danger'); redirect('?page=ask'); }
    $stmt=db()->prepare('INSERT INTO questions(user_id,title,body) VALUES(?,?,?)'); $stmt->execute([$u['id'],$title,$body]); $qid=(int)db()->lastInsertId();
    $tagIds=$_POST['tags']??[]; foreach(array_slice(array_unique(array_map('intval',$tagIds)),0,5) as $tid){$s=db()->prepare('INSERT IGNORE INTO question_tags(question_id,tag_id) VALUES(?,?)');$s->execute([$qid,$tid]);}
    flash('Your question has been published.'); redirect('?page=question&id='.$qid);
}
if ($page === 'answer' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u=require_login(); $qid=(int)($_POST['question_id']??0); $body=trim($_POST['body']??'');
    if (mb_strlen($body)<10) { flash('Answer must contain at least 10 characters.','danger'); redirect('?page=question&id='.$qid); }
    $s=db()->prepare('INSERT INTO answers(question_id,user_id,body) SELECT id,?,? FROM questions WHERE id=? AND status="published"');
    $s->execute([$u['id'],$body,$qid]); flash('Answer submitted.'); redirect('?page=question&id='.$qid);
}
if ($page === 'vote' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u=require_login(); $aid=(int)($_POST['answer_id']??0); $value=(int)($_POST['value']??0);
    if (!in_array($value,[-1,1],true)) { http_response_code(400); exit('Invalid vote'); }
    $s=db()->prepare('SELECT user_id,question_id FROM answers WHERE id=?');$s->execute([$aid]);$a=$s->fetch();
    if (!$a || (int)$a['user_id']===(int)$u['id']) { flash('You cannot vote for your own answer.','danger'); redirect('?page=question&id='.(int)($a['question_id']??0)); }
    db()->beginTransaction();
    try {
      $old=db()->prepare('SELECT value FROM votes WHERE user_id=? AND answer_id=? FOR UPDATE');$old->execute([$u['id'],$aid]);$previous=$old->fetchColumn();
      if ($previous===false) { $ins=db()->prepare('INSERT INTO votes(user_id,answer_id,value) VALUES(?,?,?)');$ins->execute([$u['id'],$aid,$value]); }
      elseif ((int)$previous===$value) { $del=db()->prepare('DELETE FROM votes WHERE user_id=? AND answer_id=?');$del->execute([$u['id'],$aid]); }
      else { $up=db()->prepare('UPDATE votes SET value=? WHERE user_id=? AND answer_id=?');$up->execute([$value,$u['id'],$aid]); }
      $sum=db()->prepare('SELECT COALESCE(SUM(value),0) FROM votes v JOIN answers a ON a.id=v.answer_id WHERE a.user_id=?');$sum->execute([$a['user_id']]);
      $upd=db()->prepare('UPDATE users SET reputation=? WHERE id=?');$upd->execute([(int)$sum->fetchColumn(),$a['user_id']]);
      db()->commit();
    } catch(Throwable $e){db()->rollBack();throw $e;}
    redirect('?page=question&id='.(int)$a['question_id']);
}
if ($page === 'accept' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u=require_login();$aid=(int)($_POST['answer_id']??0);$qid=(int)($_POST['question_id']??0);
    $check=db()->prepare('SELECT q.user_id FROM questions q WHERE q.id=?');$check->execute([$qid]);
    if ((int)$check->fetchColumn() !== (int)$u['id']) {http_response_code(403);exit('Only the question author can accept an answer.');}
    db()->beginTransaction();
    db()->prepare('UPDATE answers SET is_accepted=0 WHERE question_id=?')->execute([$qid]);
    db()->prepare('UPDATE answers SET is_accepted=1 WHERE id=? AND question_id=?')->execute([$aid,$qid]);
    db()->prepare('UPDATE questions SET accepted_answer_id=? WHERE id=?')->execute([$aid,$qid]);
    db()->commit(); flash('Best answer selected.'); redirect('?page=question&id='.$qid);
}

ob_start();
switch ($page) {
case 'register':
 echo '<div class="auth-card"><h2>Create account</h2><form method="post">'.csrf_field().'<label class="form-label">Full name</label><input class="form-control mb-3" name="full_name" required maxlength="120"><label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required><label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" minlength="8" required><button class="btn btn-accent w-100">Register</button></form></div>'; break;
case 'login':
 echo '<div class="auth-card"><h2>Welcome back</h2><form method="post">'.csrf_field().'<label class="form-label">Email</label><input class="form-control mb-3" type="email" name="email" required><label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required><button class="btn btn-accent w-100">Login</button></form></div>'; break;
case 'ask':
 require_login(); $tags=db()->query('SELECT id,name FROM tags ORDER BY name')->fetchAll();
 echo '<h1>Ask a question</h1><form method="post" class="panel p-4">'.csrf_field().'<label class="form-label">Title</label><input class="form-control mb-3" name="title" minlength="12" maxlength="180" required placeholder="Be specific and imagine you are asking another student"><label class="form-label">Details</label><textarea class="form-control mb-3" name="body" rows="7" minlength="20" required></textarea><label class="form-label">Tags (up to 5)</label><select class="form-select mb-3" name="tags[]" multiple size="5">';
 foreach($tags as $tag) echo '<option value="'.(int)$tag['id'].'">'.e($tag['name']).'</option>';
 echo '</select><button class="btn btn-accent">Publish question</button></form>'; break;
case 'question':
 $id=(int)($_GET['id']??0);$s=db()->prepare('SELECT q.*,u.full_name FROM questions q JOIN users u ON u.id=q.user_id WHERE q.id=? AND q.status="published"');$s->execute([$id]);$q=$s->fetch();
 if(!$q){http_response_code(404);echo '<h2>Question not found</h2>';break;}
 db()->prepare('UPDATE questions SET views=views+1 WHERE id=?')->execute([$id]);
 $s=db()->prepare('SELECT a.*,u.full_name,(SELECT COALESCE(SUM(value),0) FROM votes v WHERE v.answer_id=a.id) score FROM answers a JOIN users u ON u.id=a.user_id WHERE a.question_id=? ORDER BY a.is_accepted DESC,score DESC,a.created_at');$s->execute([$id]);$answers=$s->fetchAll();
 echo '<div class="panel p-4 mb-4"><div class="text-secondary small">Asked by '.e($q['full_name']).' · '.e($q['created_at']).' · '.(int)$q['views'].' views</div><h1 class="mt-2">'.e($q['title']).'</h1><p class="question-body">'.nl2br(e($q['body'])).'</p></div><h3>'.count($answers).' Answers</h3>';
 foreach($answers as $a){echo '<div class="panel p-3 mb-3 '.($a['is_accepted']?'accepted':'').'"><div class="d-flex justify-content-between"><strong>'.e($a['full_name']).'</strong><span class="badge text-bg-light">Score '.(int)$a['score'].'</span></div><p class="mt-3">'.nl2br(e($a['body'])).'</p>';
 if($a['is_accepted']) echo '<span class="text-success fw-bold">✓ Best answer</span>';
 if(current_user()){echo '<div class="d-flex gap-2"><form method="post" action="?page=vote">'.csrf_field().'<input type="hidden" name="answer_id" value="'.(int)$a['id'].'"><input type="hidden" name="value" value="1"><button class="btn btn-sm btn-outline-primary">▲ Upvote</button></form><form method="post" action="?page=vote">'.csrf_field().'<input type="hidden" name="answer_id" value="'.(int)$a['id'].'"><input type="hidden" name="value" value="-1"><button class="btn btn-sm btn-outline-secondary">▼ Downvote</button></form>';
 if((int)$q['user_id']===(int)current_user()['id']) echo '<form method="post" action="?page=accept">'.csrf_field().'<input type="hidden" name="answer_id" value="'.(int)$a['id'].'"><input type="hidden" name="question_id" value="'.$id.'"><button class="btn btn-sm btn-success">Accept answer</button></form>';
 echo '</div>';} echo '</div>';}
 if(current_user()) echo '<form method="post" action="?page=answer" class="panel p-4 mt-4">'.csrf_field().'<input type="hidden" name="question_id" value="'.$id.'"><h4>Your answer</h4><textarea class="form-control mb-3" name="body" rows="5" required minlength="10"></textarea><button class="btn btn-accent">Post answer</button></form>';
 else echo '<p><a href="?page=login">Log in</a> to answer this question.</p>'; break;
case 'admin':
 require_role(['admin']); $stats=['users'=>db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),'questions'=>db()->query('SELECT COUNT(*) FROM questions')->fetchColumn(),'answers'=>db()->query('SELECT COUNT(*) FROM answers')->fetchColumn(),'reports'=>db()->query("SELECT COUNT(*) FROM reports WHERE status='pending'")->fetchColumn()];
 echo '<h1>Administration</h1><div class="row g-3">';
 foreach($stats as $k=>$v) echo '<div class="col-md-3"><div class="panel p-3"><div class="text-secondary">'.ucfirst($k).'</div><div class="display-6">'.(int)$v.'</div></div></div>';
 echo '</div><p class="mt-4">User and report management screens are the next implementation milestone.</p>'; break;
case 'moderator':
 require_role(['moderator','admin']); echo '<h1>Moderation</h1><p class="text-secondary">Review reported questions and answers. Moderation queue is prepared in the database.</p>'; break;
default:
 $query=trim($_GET['q']??'');$pageNo=max(1,(int)($_GET['p']??1));$limit=10;$offset=($pageNo-1)*$limit;
 if($query!==''){$like='%'.$query.'%';$s=db()->prepare('SELECT q.id,q.title,q.body,q.created_at,q.views,u.full_name,(SELECT COUNT(*) FROM answers a WHERE a.question_id=q.id) answer_count FROM questions q JOIN users u ON u.id=q.user_id WHERE q.status="published" AND (q.title LIKE ? OR q.body LIKE ?) ORDER BY q.created_at DESC LIMIT ? OFFSET ?');$s->bindValue(1,$like);$s->bindValue(2,$like);$s->bindValue(3,$limit,PDO::PARAM_INT);$s->bindValue(4,$offset,PDO::PARAM_INT);$s->execute();}
 else {$s=db()->prepare('SELECT q.id,q.title,q.body,q.created_at,q.views,u.full_name,(SELECT COUNT(*) FROM answers a WHERE a.question_id=q.id) answer_count FROM questions q JOIN users u ON u.id=q.user_id WHERE q.status="published" ORDER BY q.created_at DESC LIMIT ? OFFSET ?');$s->bindValue(1,$limit,PDO::PARAM_INT);$s->bindValue(2,$offset,PDO::PARAM_INT);$s->execute();}
 $rows=$s->fetchAll(); echo '<section class="hero p-4 p-md-5 mb-4"><span class="eyebrow">LEARN · SHARE · GROW</span><h1 class="display-5 fw-bold mt-2">Find answers. Share knowledge.</h1><p class="lead">'.e(t('welcome')).'</p><form class="d-flex gap-2" method="get"><input type="hidden" name="page" value="home"><input class="form-control form-control-lg" name="q" value="'.e($query).'" placeholder="'.e(t('search')).'"><button class="btn btn-accent btn-lg">Search</button></form></section>';
 echo '<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Latest questions</h2><a class="btn btn-accent" href="?page=ask">Ask question</a></div>';
 foreach($rows as $r) echo '<article class="panel p-3 mb-3"><div class="d-flex gap-3"><div class="stats"><strong>'.(int)$r['answer_count'].'</strong><small>answers</small></div><div class="flex-grow-1"><a class="question-link" href="?page=question&id='.(int)$r['id'].'">'.e($r['title']).'</a><p class="text-secondary mt-2 mb-2">'.e(mb_strimwidth($r['body'],0,180,'…')).'</p><small class="text-secondary">by '.e($r['full_name']).' · '.e($r['created_at']).' · '.(int)$r['views'].' views</small></div></div></article>';
 if(!$rows) echo '<div class="panel p-4">No questions found.</div>';
 echo '<nav><ul class="pagination"><li class="page-item"><a class="page-link" href="?q='.urlencode($query).'&p='.max(1,$pageNo-1).'">Previous</a></li><li class="page-item"><a class="page-link" href="?q='.urlencode($query).'&p='.($pageNo+1).'">Next</a></li></ul></nav>';
}
$content=ob_get_clean();
layout(ucfirst($page),$content);
