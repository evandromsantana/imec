<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use App\Models\Mechanic;
use App\Models\MechanicPhotos;
use App\Models\MechanicServices;
use App\Models\MechanicTestimonial;
use App\Models\MechanicAvailability;

class MechanicController extends Controller
{
    private $loggedUser;

    public function __construct() {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    /*
    public function createRandom() {
        $array = ['error'=>''];

        for($q=0; $q<15; $q++) {
            $names = ['Evandro', 'Paulo', 'Pedro', 'Amanda', 'Leticia', 'Gabriel', 'Gabriela', 'Thais', 'Luiz', 'Diogo', 'José', 'Jeremias', 'Francisco', 'Dirce', 'Marcelo' ];
            $lastnames = ['Santos', 'Silva', 'Santos', 'Silva', 'Alvaro', 'Sousa', 'Diniz', 'Josefa', 'Luiz', 'Diogo', 'Limoeiro', 'Santos', 'Limiro', 'Nazare', 'Mimoza' ];

            $servicos = ['Injeção Eletrônica', 'Troca de Óleo', 'Freios', 'Suspensão', 'Revisão', 'Embreagem', 'Balanceamento e Geometria', 'Pintura'];
            $servicos2 = ['Reparos em Motores', 'Sistema Elétrico', 'Suspensão', 'Manutenções preventivas', 'Troca de óleo', 'Reparos', 'Alinhamento e Balanceamento'];

            $depos = [
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.',
                'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate consequatur tenetur facere voluptatibus iusto accusantium vero sunt, itaque nisi esse ad temporibus a rerum aperiam cum quaerat quae quasi unde.'
            ];

            $newMechanic = new Mechanic();
            $newMechanic->name = $names[rand(0, count($names)-1)].' '.$lastnames[rand(0, count($lastnames)-1)];
            $newMechanic->avatar = rand(1, 4).'.png';
            $newMechanic->stars = rand(2, 4).'.'.rand(0, 9);
            $newMechanic->latitude = '-9.0'.rand(0, 9).'54907';
            $newMechanic->longitude = '-42.4'.rand(0,9).'50795';
            $newMechanic->save();

            $ns = rand(3, 6);

            for($w=0;$w<4;$w++) {
                $newMechanicPhoto = new MechanicPhotos();
                $newMechanicPhoto->id_mechanic = $newMechanic->id;
                $newMechanicPhoto->url = rand(1, 5).'.png';
                $newMechanicPhoto->save();
            }

            for($w=0;$w<$ns;$w++) {
                $newMechanicService = new MechanicServices();
                $newMechanicService->id_mechanic = $newMechanic->id;
                $newMechanicService->name = $servicos[rand(0, count($servicos)-1)].' de '.$servicos2[rand(0, count($servicos2)-1)];
                $newMechanicService->price = rand(1, 99).'.'.rand(0, 100);
                $newMechanicService->save();
            }

            for($w=0;$w<3;$w++) {
                $newMechanicTestimonial = new MechanicTestimonial();
                $newMechanicTestimonial->id_mechanic = $newMechanic->id;
                $newMechanicTestimonial->name = $names[rand(0, count($names)-1)];
                $newMechanicTestimonial->rate = rand(2, 4).'.'.rand(0, 9);
                $newMechanicTestimonial->body = $depos[rand(0, count($depos)-1)];
                $newMechanicTestimonial->save();
            }

            for($e=0;$e<4;$e++){
                $rAdd = rand(7, 10);
                $hours = [];
                for($r=0;$r<8;$r++) {
                    $time = $r + $rAdd;
                    if($time < 10) {
                        $time = '0'.$time;
                    }
                    $hours[] = $time.':00';
                }
                $newMechanicAvail = new MechanicAvailability();
                $newMechanicAvail->id_barber = $newMechanic->id;
                $newMechanicAvail->weekday = $e;
                $newMechanicAvail->hours = implode(',', $hours);
                $newMechanicAvail->save();
            }

        }

        return $array;
    }
    */

    private function searchGeo($address) {
        $key = env('MAPS_KEY', null);

        $address = urldecode($address);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address. '&key='.$key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true);
    }

    public function list(Request $request) {
        $array = ['error' => ''];

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $city = $request->input('city');
        $offset = $request->input('offset');
        if(!$offset) {
            $offset = 0;
        }

        if(!empty($city)) {
            $res = $this->searchGeo($city);

            if(count($res['results']) > 0) {
                $lat = $res['results'][0]['geometry']['location']['lat'];
                $lng = $res['results'][0]['geometry']['location']['lng'];
            }
        } elseif(!empty($lat) && !empty($lng)) {
            $res = $this->searchGeo($lat.','.$lng);

            if(count($res['results']) > 0) {
                $city = $res['results'][0]['formatted_address'];
            }
        } else {
            $lat = '-9.015';
            $lng = '-42.69889';
            $city = 'São Raimundo Nonato';
        }

        $mechanics = Mechanic::select(Mechanic::raw('*, SQRT(
            POW(69.1 * (latitude - '.$lat.'), 2) +
            POW(69.1 * ('.$lng.' - longitude) * COS(latitude / 57.3), 2)) AS distance'))
            ->orderBy('distance < ?', [10])
            ->orderBy('distance', 'ASC')
            ->offset($offset)
            ->limit(5)
            ->get();

        foreach($mechanics as $bkey => $bvalue) {
            $mechanics[$bkey]['avatar'] = url('media/avatars/'.$mechanics[$bkey]['avatar']);
        }

        $array['data'] = $mechanics;
        $array['loc'] = 'São Raimundo Nonato';

        return $array;
    }

    public function one($id) {
        $array = ['error' => ''];

        $mechanic = Mechanic::find($id);

        if($mechanic) {
            $mechanic['avatar'] = url('media/avatars/'.$mechanic['avatar']);
            $mechanic['favorited'] = false;
            $mechanic['photos'] = [];
            $mechanic['services'] = [];
            $mechanic['testimonials'] = [];
            $mechanic['available'] = [];

            // Verificando favorito
            $cFavorite = UserFavorite::where('id_user', $this->loggedUser->id)
                ->where('id_mechanic', $mechanic->id)
                ->count();
            if($cFavorite > 0) {
                $mechanic['favorited'] = true;
            } 

            // Pegando as fotos do Mecânico
            $mechanic['photos'] = MechacicPhotos::select(['id', 'url'])
            ->where('id_mechanic', $mechanic->id)
            ->get();
            foreach($mechanic['photos'] as $bpkey => $bpvalue) {
                $mechanic['photos'][$bpkey]['url'] = url('media/uploads/'.$mechanic['photos'][$bpkey]['url']);
            }

            // Pegando os serviços do Mecânico
            $mechanic['services'] = MechanicServices::select(['id', 'name', 'price'])
            ->where('id_mechanic', $mechanic->id)
            ->get();

            // Pegando os depoimentos do Mecânico
            $mechanic['testimonials'] = MechanicTestimonial::select(['id', 'name', 'rate', 'body'])
            ->where('id_mechanic', $mechanic->id)
            ->get();

            // Pegando disponibilidade do Mecânico
            $availability = [];

            // - Pegando a disponibilidade crua
            $avails = MechanicAvailability::where('id_mechanic', $mechanic->id)->get();
            $availWeekdays = [];
            foreach($avails as $item) {
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            // - Pegar os agendamentos dos próximos 20 dias
            $appointments = [];
            $appQuery = UserAppointment::where('id_mechanic', $mechanic->id)
                -> whereBetween('ap_datetime', [
                    date('Y-m-d').'00:00:00',
                    date('Y-m-d', strtotime('+20 days')).' 23:59:59'
                ])
                ->get();
            foreach($appQuery as $appItem) {
                $appointments[] = $appItem['ap_datetime'];
            }

            // - Gerar disponibilidade real
            for($q=0;$q<20;$q++) {
                $timeItem = strtotime('+'.$q.' days');
                $weekday = date('w', $timeItem);

                if(in_array($weekday, array_keys($availWeekdays))) {
                    $hours = [];

                    $dayItem = date('Y-m-d', $timeItem);

                    foreach($availWeekdays[$weekday] as $hourItem) {
                        $dayFormated = $dayItem.' '.$hourItem.':00';
                        if(!in_array($dayFormated, $appointments)) {
                            $hours[] = $hourItem;
                        }
                    }

                    if(count($hours) > 0) {
                        $availability[] = [
                            'date' => $dayItem,
                            'hours' => $hours
                        ];
                    }


                }
            }


            $mechanic['available'] = $availability;


            $array['data'] = $mechanic;
        } else {
            $array['error'] = 'Mecânico não existe';
            return $array;
        }

        return $array;
    }

    public function setAppointment() {
        // service, year, month, day, hour
        $array = ['error'=>''];

        $service = $request->input('service');
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));
        $day = intval($request->input('day'));
        $hour = intval($request->input('hour'));

        $month = ($month < 10) ? '0'.$month : $month;
        $day = ($day < 10) ? '0'.$day : $day;
        $hour = ($hour < 10) ? '0'.$hour : $hour;

        // 1. verificar se o serviço do mecânico existe
        $mechanicservice = MechanicServices::select()
            ->where('id', $service)
            ->where('id_mechanic', $id)
        ->first();

        if($mechanicservice) {
            // 2. verificar se a data é real
            $apDate = $year.'-'.$month.'-'.$day.' '.$hour.':00:00';
            if(strtotime($apDate) > 0) {
                // 3. verificar se o mecânico já possui agendamento neste dia/hora               
                $apps = UserAppointment::select()
                    ->where('id_mechanic', $id)
                    ->where('ap_datetime', $apDate)
                ->count();
                if($apps === 0) {
                    // 4.1 verificar se o mecânico atende nesta data
                    $weekday = date('w', strtotime($apDate));
                    $avail = MechanicAvailability::select()
                        ->where('id_mechanic', $id)
                        ->where('weekday', $weekday)
                    ->first();
                    if($avail) {
                        // 4.2 verificar se o mecânico atende nesta hora
                        $hours = explode(',', $avail['hours']);
                        if(in_array($hour.':00', $hours)) {
                            // 5. fazer o agendamento
                            $newApp = new UserAppointment();
                            $newApp->id_user = $this->loggedUser->id;
                            $newApp->id_mechanic = $id;
                            $newApp->id_service = $service;
                            $newApp->ap_datetime = $apDate;
                            $newApp->save();
                        } else {
                            $array['error'] = 'Mecânico não atende nesta hora';
                        }
                    } else {
                        $array['error'] = 'Mecânico não atende neste dia';
                    }                    
                } else {
                    $array['error'] = 'Mecânico já possui agendamento neste dia/hora';
                }

            } else {
                $array['error'] = 'Data inválida';
            }
        } else {
            $array['error'] = 'Serviço inexistente!';
        }
        return $array;
    }

    public function search(Request $request) {
        $array = ['error'=>'', 'list'=>[]];

        $q = $request->input('q');

        if($q) {

            $mechanics = Mechanic::select()
                ->where('name', 'LIKE', '%'.$q.'%')
            ->get();

            foreach($mechanics as $bkey => $mechanic) {
                $mechanics[$bkey]['avatar'] = url('media/avatars/'.$mechanics[$bkey]['avatar']);
            }

            $array['list'] = $mechanics;
        } else {
            $array['error'] = 'Digite algo para buscar';
        }

        return $array;
    }
}