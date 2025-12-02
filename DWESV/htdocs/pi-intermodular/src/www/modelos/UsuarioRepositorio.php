<?php
class UsuarioRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerTodos(): array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return [];
        $sql = "SELECT id, usuario, email, tipo_usuario, activo, fecha_creacion FROM usuarios ORDER BY id DESC";
        return $pdo->query($sql)->fetchAll() ?: [];
    }

    public function obtenerPorId(int $id): ?array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return null;
        $stmt = $pdo->prepare("SELECT id, usuario, email, tipo_usuario, activo, fecha_creacion FROM usuarios WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function crear(array $data): array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $usuario = $data['usuario'] ?? '';
        $email = $data['email'] ?? '';
        $tipo = $data['tipo_usuario'] ?? '';
        $passwordPlano = $data['password'] ?? '';
        if ($usuario===''||$email===''||$tipo===''||$passwordPlano==='') {
            return ['ok'=>false,'msg'=>'Campos obligatorios faltantes'];
        }
        $hash = password_hash($passwordPlano, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (tipo_usuario, usuario, email, password, password_temporal, activo) VALUES (?,?,?,?,1,1)');
        try {
            $stmt->execute([$tipo,$usuario,$email,$hash]);
            $id = (int)$pdo->lastInsertId();
            // Crear registro detalle según tipo
            if ($tipo==='administrador') {
                $adm = $pdo->prepare('INSERT INTO administradores (usuario_id,nombre,apellidos,telefono) VALUES (?,?,?,?)');
                $adm->execute([$id,$data['nombre']??'',$data['apellidos']??'',$data['telefono']??null]);
            } elseif ($tipo==='arbitro') {
                $arb = $pdo->prepare('INSERT INTO arbitros (usuario_id,nombre,apellidos,dni,telefono,ciudad,licencia,numero_licencia) VALUES (?,?,?,?,?,?,?,?)');
                $arb->execute([$id,$data['nombre']??'',$data['apellidos']??'',$data['dni']??'', $data['telefono']??null,$data['ciudad']??'', $data['licencia']??'colaborador',$data['numero_licencia']??null]);
            }
            return ['ok'=>true,'id'=>$id];
        } catch (Throwable $e) {
            return ['ok'=>false,'msg'=>$e->getMessage()];
        }
    }

    public function actualizar(int $id, array $data): array {
        $pdo = $this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $usuario = $data['usuario'] ?? null; $email = $data['email'] ?? null; $activo = isset($data['activo'])?(int)$data['activo']:null;
        $sets=[];$vals=[];
        if($usuario!==null){$sets[]='usuario=?';$vals[]=$usuario;}
        if($email!==null){$sets[]='email=?';$vals[]=$email;}
        if(isset($data['password']) && $data['password']!==''){ $sets[]='password=?';$vals[]=password_hash($data['password'],PASSWORD_DEFAULT); $sets[]='password_temporal=0'; }
        if($activo!==null){$sets[]='activo=?';$vals[]=$activo;}
        if(!$sets) return ['ok'=>false,'msg'=>'Nada que actualizar'];
        $vals[]=$id;
        $sql='UPDATE usuarios SET '.implode(',',$sets).' WHERE id=?';
        try { $pdo->prepare($sql)->execute($vals); return ['ok'=>true]; } catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }

    public function eliminar(int $id): array {
        $pdo = $this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        try { $pdo->prepare('DELETE FROM usuarios WHERE id=?')->execute([$id]); return ['ok'=>true]; } catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }
}
