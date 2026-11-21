<?php
require_once __DIR__ . '/includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') go('/inkora-books/');
csrf_check(); $pdo=db(); $newImage=null;
try {
    $action=post('action');
    if($action==='follow_author'||$action==='unfollow_author') {
        require_login();
        if(($_SESSION['auth']['kind']??'')!=='cliente')throw new RuntimeException('Entre com uma conta de leitor para seguir autores.');
        $author=(int)post('autor_id');
        $check=$pdo->prepare("SELECT id FROM usuario WHERE id=? AND tipo='autor'");$check->execute([$author]);
        if(!$check->fetch())throw new RuntimeException('Autor nao encontrado.');
        if($action==='follow_author')$pdo->prepare('INSERT IGNORE INTO autor_seguidor(cliente_id,autor_id) VALUES(?,?)')->execute([id(),$author]);
        else $pdo->prepare('DELETE FROM autor_seguidor WHERE cliente_id=? AND autor_id=?')->execute([id(),$author]);
        flash($action==='follow_author'?'Voce esta seguindo este autor.':'Voce deixou de seguir este autor.');
        go('/inkora-books/usuarios/ver.php?id='.$author);
    }
    if($action==='rate_book') {
        require_login();
        if(($_SESSION['auth']['kind']??'')!=='cliente')throw new RuntimeException('Entre com uma conta de leitor para avaliar livros.');
        $book=(int)post('livro_id');$rating=(int)post('nota');$comment=post('comentario');
        if($rating<1||$rating>5)throw new RuntimeException('Escolha uma nota de 1 a 5 estrelas.');
        if(mb_strlen($comment)>1500)throw new RuntimeException('O comentario deve ter no maximo 1500 caracteres.');
        $read=$pdo->prepare("SELECT 1 FROM estante WHERE cliente_id=? AND livro_id=? AND status='lido'");$read->execute([id(),$book]);
        if(!$read->fetchColumn())throw new RuntimeException('Marque o livro como lido na sua estante antes de avalia-lo.');
        $pdo->prepare('INSERT INTO avaliacao_livro(cliente_id,livro_id,nota,comentario) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE nota=VALUES(nota),comentario=VALUES(comentario)')->execute([id(),$book,$rating,$comment?:null]);
        flash('Sua avaliacao foi salva.');go('/inkora-books/livros/ver.php?id='.$book.'#avaliacoes');
    }
    if ($action==='logout') { session_destroy(); go('/inkora-books/'); }
    if ($action==='login') {
        $email=strtolower(post('email')); $password=post('senha');
        $q=$pdo->prepare('SELECT id,nome,email,senha,foto,tipo AS role FROM usuario WHERE email=?');$q->execute([$email]);$u=$q->fetch();
        if($u&&password_verify($password,$u['senha'])) $_SESSION['auth']=['id'=>(int)$u['id'],'nome'=>$u['nome'],'email'=>$u['email'],'foto'=>$u['foto'],'kind'=>'usuario','role'=>$u['role']];
        else {$q=$pdo->prepare('SELECT id,nome,email,senha,foto FROM cliente WHERE email=?');$q->execute([$email]);$u=$q->fetch();if($u&&password_verify($password,$u['senha']))$_SESSION['auth']=['id'=>(int)$u['id'],'nome'=>$u['nome'],'email'=>$u['email'],'foto'=>$u['foto'],'kind'=>'cliente','role'=>'cliente'];}
        if(!logged())throw new RuntimeException('E-mail ou senha incorretos.'); session_regenerate_id(true); flash('Que bom ter você de volta!'); go('/inkora-books/');
    }
    if ($action==='register') {
        $name=post('nome');$email=strtolower(post('email'));$password=post('senha');
        if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<8)throw new RuntimeException('Informe nome, e-mail válido e senha com pelo menos 8 caracteres.');
        $newImage=image_upload('foto','clientes');
        $pdo->prepare('INSERT INTO cliente (nome,email,senha,telefone,foto) VALUES (?,?,?,?,?)')->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),post('telefone'),$newImage]);
        $_SESSION['auth']=['id'=>(int)$pdo->lastInsertId(),'nome'=>$name,'email'=>$email,'foto'=>$newImage,'kind'=>'cliente','role'=>'cliente'];session_regenerate_id(true);flash('Conta criada. Boas-vindas à sua estante!');go('/inkora-books/');
    }
    if($action==='save_book') {
        require_login();if(!staff())throw new RuntimeException('Somente autores e administradores podem cadastrar livros.');$bookId=(int)post('id');$isbn=preg_replace('/[^0-9Xx]/','',post('isbn'));
        $title=post('titulo');$author=post('autor');$publisher=post('editora');$year=post('ano_publicacao');$pages=post('numero_paginas');$description=post('descricao');
        if($title===''||$author==='')throw new RuntimeException('Título e autor são obrigatórios.');        if($year!==''&&(!ctype_digit($year)||(int)$year<1000||(int)$year>9999))throw new RuntimeException('Informe um ano de publicação válido.');
        if($pages!==''&&(!ctype_digit($pages)||(int)$pages<1))throw new RuntimeException('Informe um número de páginas válido.');
        $old=null;if($bookId){$q=$pdo->prepare('SELECT usuario_id,capa FROM livro WHERE id=?');$q->execute([$bookId]);$existing=$q->fetch();if(!$existing)throw new RuntimeException('Livro nao encontrado.');if(!can_manage_book($existing['usuario_id']===null?null:(int)$existing['usuario_id']))throw new RuntimeException('Voce so pode editar os livros que cadastrou.');$old=$existing['capa'];}
        $newImage=image_upload('capa_arquivo','livros');$url=trim(post('capa_url'));if($url!==''&&!filter_var($url,FILTER_VALIDATE_URL))throw new RuntimeException('A URL da capa precisa ser válida.');
        $remove=isset($_POST['remover_capa']);$newCover=$newImage?:($remove?null:($url!==''?$url:$old));
        if($bookId)$pdo->prepare('UPDATE livro SET isbn=?,titulo=?,autor=?,editora=?,ano_publicacao=?,numero_paginas=?,descricao=?,capa=? WHERE id=?')->execute([$isbn?:null,$title,$author,$publisher?:null,$year!==''?(int)$year:null,$pages!==''?(int)$pages:null,$description,$newCover,$bookId]);
        else $pdo->prepare('INSERT INTO livro (isbn,titulo,autor,editora,ano_publicacao,numero_paginas,descricao,capa,usuario_id) VALUES (?,?,?,?,?,?,?,?,?)')->execute([$isbn?:null,$title,$author,$publisher?:null,$year!==''?(int)$year:null,$pages!==''?(int)$pages:null,$description,$newCover,staff()?id():null]);
        if(($newImage||$remove||($url!==''&&$old!==$url))&&$old)delete_image($old,'livros');flash($bookId?'Livro atualizado.':'Livro adicionado ao catálogo.');go('/inkora-books/livros/');
    }
    if($action==='delete_book'){require_login();$book=(int)post('id');$q=$pdo->prepare('SELECT usuario_id,capa FROM livro WHERE id=?');$q->execute([$book]);$existing=$q->fetch();if(!$existing)throw new RuntimeException('Livro nao encontrado.');if(!can_manage_book($existing['usuario_id']===null?null:(int)$existing['usuario_id']))throw new RuntimeException('Voce so pode remover os livros que cadastrou.');$pdo->prepare('DELETE FROM livro WHERE id=?')->execute([$book]);if($existing['capa'])delete_image($existing['capa'],'livros');flash('Livro removido.');go('/inkora-books/livros/');}
    if($action==='save_client') {
        require_login();if(!staff())throw new RuntimeException('A equipe é necessária para cadastrar clientes.');$client=(int)post('id');$name=post('nome');$email=strtolower(post('email'));if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Informe nome e e-mail válido.');
        $old=null;if($client){$q=$pdo->prepare('SELECT foto FROM cliente WHERE id=?');$q->execute([$client]);$old=$q->fetchColumn();if($old===false)throw new RuntimeException('Cliente não encontrado.');}
        $newImage=image_upload('foto','clientes');$remove=isset($_POST['remover_foto']);$photo=$newImage?:($remove?null:$old);
        if($client)$pdo->prepare('UPDATE cliente SET nome=?,email=?,telefone=?,foto=? WHERE id=?')->execute([$name,$email,post('telefone'),$photo,$client]);
        else{$password=post('senha');if(strlen($password)<8)throw new RuntimeException('Defina uma senha com pelo menos 8 caracteres.');$pdo->prepare('INSERT INTO cliente(nome,email,senha,telefone,foto) VALUES(?,?,?,?,?)')->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),post('telefone'),$photo]);}
        if(($newImage||$remove)&&$old)delete_image($old,'clientes');flash('Cadastro de cliente salvo.');go('/inkora-books/clientes/');
    }
    if($action==='delete_client'){require_login();if(!staff())throw new RuntimeException('Somente a equipe pode excluir clientes.');$client=(int)post('id');$q=$pdo->prepare('SELECT foto FROM cliente WHERE id=?');$q->execute([$client]);$old=$q->fetchColumn();$pdo->prepare('DELETE FROM cliente WHERE id=?')->execute([$client]);if($old)delete_image($old,'clientes');flash('Cliente removido.');go('/inkora-books/clientes/');}
    if($action==='save_gallery_image') {
        require_login();
        $author=(int)post('usuario_id');
        $q=$pdo->prepare("SELECT id FROM usuario WHERE id=? AND tipo='autor'");$q->execute([$author]);
        if(!$q->fetch())throw new RuntimeException('Autor nao encontrado.');
        if(!can_manage_author_gallery($author))throw new RuntimeException('Sem permissao para adicionar imagens nesta galeria.');
        $newImage=image_upload('imagem','galeria');
        if(!$newImage)throw new RuntimeException('Selecione uma imagem para adicionar.');
        $caption=post('legenda');
        if(strlen($caption)>180)throw new RuntimeException('A legenda deve ter no maximo 180 caracteres.');
        $pdo->prepare('INSERT INTO galeria_autor(usuario_id,imagem,legenda) VALUES(?,?,?)')->execute([$author,$newImage,$caption?:null]);
        flash('Imagem adicionada a galeria.');go('/inkora-books/usuarios/ver.php?id='.$author.'#galeria');
    }
    if($action==='update_gallery_caption') {
        require_login();$imageId=(int)post('id');$author=(int)post('usuario_id');
        if(!can_manage_author_gallery($author))throw new RuntimeException('Sem permissao para editar legendas desta galeria.');
        $caption=post('legenda');if(strlen($caption)>180)throw new RuntimeException('A legenda deve ter no maximo 180 caracteres.');
        $q=$pdo->prepare('UPDATE galeria_autor SET legenda=? WHERE id=? AND usuario_id=?');$q->execute([$caption?:null,$imageId,$author]);
        if($q->rowCount()===0){$check=$pdo->prepare('SELECT id FROM galeria_autor WHERE id=? AND usuario_id=?');$check->execute([$imageId,$author]);if(!$check->fetch())throw new RuntimeException('Imagem nao encontrada nesta galeria.');}
        flash('Legenda atualizada.');go('/inkora-books/usuarios/ver.php?id='.$author.'#galeria');
    }
    if($action==='delete_gallery_image') {
        require_login();$imageId=(int)post('id');$author=(int)post('usuario_id');
        if(!can_manage_author_gallery($author))throw new RuntimeException('Sem permissao para remover imagens desta galeria.');
        $q=$pdo->prepare('SELECT imagem FROM galeria_autor WHERE id=? AND usuario_id=?');$q->execute([$imageId,$author]);$image=$q->fetchColumn();
        if($image===false)throw new RuntimeException('Imagem nao encontrada nesta galeria.');
        $pdo->prepare('DELETE FROM galeria_autor WHERE id=? AND usuario_id=?')->execute([$imageId,$author]);delete_image($image,'galeria');
        flash('Imagem removida da galeria.');go('/inkora-books/usuarios/ver.php?id='.$author.'#galeria');
    }
    if($action==='save_user') {
        require_admin();$user=(int)post('id');$name=post('nome');$email=strtolower(post('email'));$role=post('tipo');if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||!in_array($role,['admin','autor'],true))throw new RuntimeException('Revise os dados do usuário.');
        $bio=trim(post('bio'));if(mb_strlen($bio)>3000)throw new RuntimeException('A biografia deve ter no maximo 3000 caracteres.');if($role!=='autor')$bio=null;$old=null;if($user){$q=$pdo->prepare('SELECT foto FROM usuario WHERE id=?');$q->execute([$user]);$old=$q->fetchColumn();if($old===false)throw new RuntimeException('Usuário não encontrado.');}
        $newImage=image_upload('foto','usuarios');$remove=isset($_POST['remover_foto']);$photo=$newImage?:($remove?null:$old);$password=post('senha');
        if(!$user&&strlen($password)<8)throw new RuntimeException('A senha precisa ter pelo menos 8 caracteres.');
        if($user){if($password!==''){if(strlen($password)<8)throw new RuntimeException('A senha precisa ter pelo menos 8 caracteres.');$pdo->prepare('UPDATE usuario SET nome=?,email=?,tipo=?,foto=?,bio=?,senha=? WHERE id=?')->execute([$name,$email,$role,$photo,$bio,password_hash($password,PASSWORD_DEFAULT),$user]);}else $pdo->prepare('UPDATE usuario SET nome=?,email=?,tipo=?,foto=?,bio=? WHERE id=?')->execute([$name,$email,$role,$photo,$bio,$user]);}
        else $pdo->prepare('INSERT INTO usuario(nome,email,senha,tipo,foto,bio) VALUES(?,?,?,?,?,?)')->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role,$photo,$bio]);
        if(($newImage||$remove)&&$old)delete_image($old,'usuarios');if($user===id()){$_SESSION['auth']['nome']=$name;$_SESSION['auth']['email']=$email;$_SESSION['auth']['foto']=$photo;$_SESSION['auth']['role']=$role;}flash('Usuário salvo.');go('/inkora-books/usuarios/');
    }
    if($action==='delete_user'){require_admin();$user=(int)post('id');if($user===id())throw new RuntimeException('Não é possível remover a própria conta.');$q=$pdo->prepare('SELECT foto FROM usuario WHERE id=?');$q->execute([$user]);$old=$q->fetchColumn();$galleryQuery=$pdo->prepare('SELECT imagem FROM galeria_autor WHERE usuario_id=?');$galleryQuery->execute([$user]);$galleryImages=$galleryQuery->fetchAll(PDO::FETCH_COLUMN);$pdo->prepare('DELETE FROM usuario WHERE id=?')->execute([$user]);if($old)delete_image($old,'usuarios');foreach($galleryImages as $galleryImage)delete_image($galleryImage,'galeria');flash('Usuário removido.');go('/inkora-books/usuarios/');}
    if($action==='save_profile') {
        require_login();$kind=$_SESSION['auth']['kind'];$table=$kind==='cliente'?'cliente':'usuario';$folder=$kind==='cliente'?'clientes':'usuarios';$old=$_SESSION['auth']['foto']??null;$newImage=image_upload('foto',$folder);$remove=isset($_POST['remover_foto']);$photo=$newImage?:($remove?null:$old);$name=post('nome');$email=strtolower(post('email'));if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL))throw new RuntimeException('Informe nome e e-mail válido.');$password=post('senha');
        if($kind==='cliente'){$phone=post('telefone');if($password!==''){if(strlen($password)<8)throw new RuntimeException('A nova senha deve ter ao menos 8 caracteres.');$pdo->prepare('UPDATE cliente SET nome=?,email=?,foto=?,telefone=?,senha=? WHERE id=?')->execute([$name,$email,$photo,$phone,password_hash($password,PASSWORD_DEFAULT),id()]);}else $pdo->prepare('UPDATE cliente SET nome=?,email=?,foto=?,telefone=? WHERE id=?')->execute([$name,$email,$photo,$phone,id()]);}
        elseif($password!==''){if(strlen($password)<8)throw new RuntimeException('A nova senha deve ter ao menos 8 caracteres.');$pdo->prepare('UPDATE usuario SET nome=?,email=?,foto=?,senha=? WHERE id=?')->execute([$name,$email,$photo,password_hash($password,PASSWORD_DEFAULT),id()]);}
        else $pdo->prepare('UPDATE usuario SET nome=?,email=?,foto=? WHERE id=?')->execute([$name,$email,$photo,id()]);
        if(($newImage||$remove)&&$old)delete_image($old,$folder);$_SESSION['auth']['nome']=$name;$_SESSION['auth']['email']=$email;$_SESSION['auth']['foto']=$photo;flash('Perfil atualizado.');go('/inkora-books/perfil.php');
    }
    if($action==='shelf'){require_login();if(($_SESSION['auth']['kind']??'')!=='cliente')throw new RuntimeException('Entre com uma conta de cliente para usar a estante.');$book=(int)post('livro_id');$status=post('status','quero ler');if(!in_array($status,['quero ler','lendo','lido'],true))throw new RuntimeException('Status inválido.');$pdo->prepare('INSERT INTO estante(cliente_id,livro_id,status) VALUES(?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status)')->execute([id(),$book,$status]);flash('Estante atualizada.');go('/inkora-books/estante/');}
    throw new RuntimeException('Ação não reconhecida.');
} catch(Throwable $ex) {if($pdo->inTransaction())$pdo->rollBack();if($newImage)delete_image($newImage,str_contains($newImage,'/livros/')?'livros':(str_contains($newImage,'/clientes/')?'clientes':(str_contains($newImage,'/galeria/')?'galeria':'usuarios')));$msg=$ex instanceof PDOException&&(string)$ex->getCode()==='23000'?'Este e-mail ou registro já existe.':$ex->getMessage();flash($msg,'error');$back=$_SERVER['HTTP_REFERER']??'/inkora-books/';if(!str_starts_with((string)parse_url($back,PHP_URL_PATH),'/inkora-books/'))$back='/inkora-books/';go($back);}
