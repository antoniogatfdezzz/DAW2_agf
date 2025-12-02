<?php
class ApiDisponibilidadControlador {
    public function __construct(private DisponibilidadRepositorio $repo = new DisponibilidadRepositorio()){
        $this->requerirAdmin();
    }
    private function requerirAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $u = $_SESSION['user_type'] ?? null;
        if ($u !== 'administrador') { respuesta_json(['error'=>'No autorizado'],401); }
        if (!empty($_SESSION['password_temporal'])) { respuesta_json(['error'=>'Debe cambiar la contraseña'],409); }
    }

    public function consultar(): void {
        $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($metodo === 'GET') {
            if (!empty($_GET['fecha'])) {
                $fecha = $_GET['fecha'];
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fecha)) { respuesta_json(['error'=>'Formato de fecha inválido'],400); }
                if ($fecha < date('Y-m-d')) { respuesta_json(['error'=>'Fecha pasada no permitida'],400); }
                $datos = $this->repo->obtenerPorFecha($fecha);
                respuesta_json($datos);
            }
            if (!empty($_GET['arbitro_id'])) {
                $arbitroId = (int)$_GET['arbitro_id'];
                if (!empty($_GET['month'])) {
                    $month = $_GET['month'];
                    if (!preg_match('/^\d{4}-\d{2}$/',$month)) { respuesta_json(['error'=>'Formato de mes inválido'],400); }
                    $firstDay = $month.'-01';
                    $lastDay = date('Y-m-t', strtotime($firstDay));
                    $disp = $this->repo->obtenerRango($arbitroId,$firstDay,$lastDay);
                    $map = [];
                    foreach($disp as $d){ $map[$d['fecha']]=$d; }
                    $calendar=[]; $cur = new DateTime($firstDay); $end = new DateTime($lastDay); $today = new DateTime(date('Y-m-d'));
                    $startDow = (int)$cur->format('N');
                    if ($startDow>1){ $prev = clone $cur; $prev->modify('-'.($startDow-1).' days'); for($i=0;$i<$startDow-1;$i++){ $calendar[]=['date'=>$prev->format('Y-m-d'),'day'=>(int)$prev->format('d'),'isOtherMonth'=>true,'isToday'=>false,'manana'=>0,'tarde'=>0,'observaciones'=>'']; $prev->modify('+1 day'); } }
                    while($cur <= $end){ $ds=$cur->format('Y-m-d'); $isToday = $cur->format('Y-m-d')===$today->format('Y-m-d'); $d=$map[$ds]??null; $obs=''; if($d){ $o=[]; if(!empty($d['observacion_manana'])){$o[]='M: '.$d['observacion_manana'];} if(!empty($d['observacion_tarde'])){$o[]='T: '.$d['observacion_tarde'];} $obs=implode(' | ',$o);} $calendar[]=['date'=>$ds,'day'=>(int)$cur->format('d'),'isOtherMonth'=>false,'isToday'=>$isToday,'manana'=>$d?(int)$d['manana']:0,'tarde'=>$d?(int)$d['tarde']:0,'observaciones'=>$obs]; $cur->modify('+1 day'); }
                    $rem = 7 - (count($calendar)%7); if($rem<7){ $nx = new DateTime($lastDay); $nx->modify('+1 day'); for($i=0;$i<$rem;$i++){ $calendar[]=['date'=>$nx->format('Y-m-d'),'day'=>(int)$nx->format('d'),'isOtherMonth'=>true,'isToday'=>false,'manana'=>0,'tarde'=>0,'observaciones'=>'']; $nx->modify('+1 day'); } }
                    respuesta_json(['success'=>true,'month'=>$month,'calendar'=>$calendar,'totalDays'=>count($calendar),'disponibilidadCount'=>count($disp)]);
                }
                // rango por defecto 3 meses
                $startDate = date('Y-m-d'); $endDate = date('Y-m-t', strtotime('+3 months'));
                $result = $this->repo->obtenerRango($arbitroId,$startDate,$endDate);
                respuesta_json($result);
            }
            respuesta_json(['error'=>'Parámetros insuficientes'],400);
        }
        if ($metodo === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            if (!isset($input['arbitro_id'],$input['fecha'])) { respuesta_json(['error'=>'Datos incompletos'],400); }
            $aid=(int)$input['arbitro_id']; $fecha=$input['fecha']; $manana=isset($input['manana'])?(int)$input['manana']:0; $tarde=isset($input['tarde'])?(int)$input['tarde']:0; $obs=$input['observaciones']??'';
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fecha)) { respuesta_json(['error'=>'Formato de fecha inválido'],400); }
            if ($fecha < date('Y-m-d')) { respuesta_json(['error'=>'No se puede modificar pasado'],400); }
            $res=$this->repo->upsert($aid,$fecha,$manana,$tarde,$obs); $code=$res['ok']?200:400; respuesta_json($res,$code);
        }
        if ($metodo === 'DELETE') {
            $input=json_decode(file_get_contents('php://input'), true) ?? [];
            if (!isset($input['arbitro_id'],$input['fecha'])) { respuesta_json(['error'=>'Datos incompletos'],400); }
            $aid=(int)$input['arbitro_id']; $fecha=$input['fecha'];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$fecha)) { respuesta_json(['error'=>'Formato de fecha inválido'],400); }
            if ($fecha < date('Y-m-d')) { respuesta_json(['error'=>'No se puede eliminar pasado'],400); }
            $res=$this->repo->eliminar($aid,$fecha); respuesta_json($res,$res['ok']?200:404);
        }
        respuesta_json(['error'=>'Método no soportado'],405);
    }
}
