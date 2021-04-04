<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Stage;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psr\Log\NullLogger;

use App\Notifications\trip\newPrivateTrip;


/**
 * Class RideController
 * @package App\Http\Controllers
 */
class RideController extends Controller
{
    /**
     * Rennvoie la page de création d'offre de trajet
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create_ride_form()
    {

        if(session()->has('LoggedUser')) { // Si l'utilisateur est toujours connecté, on met à jour les données
            // Récupération du nom de l'utilisateur et du tuble de la BDD correspondant à son compte
            $username = session()->get('LoggedUser'); // pseudo de l'utilisateur connecté
            $user = User::where('username', '=', $username)->first();

            $data = DB::select('SELECT * FROM `groups` WHERE id_creator=:id', ['id' => $user->id]);
        }
        return view('trip.create_trip',['data'=>$data]);
    }

    /**
     * Vérifie les informations données par l'utilisateur et rajoute un voyage en bdd
     * si tout est correct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create_ride_form_submission(Request $request){


        //vérification des champs obligatoires
        $request->validate([
            'departure'=> 'required',
            'date'=>'required',
            'time'=>'required',
            'final'=>'required',
            'nb_passengers'=>'required',
            'price'=>'required',
            'privacy'=>'required',
            'group' => 'required_if:privacy,==,private',
        ]);


        if(session()->has('LoggedUser')) { // Si l'utilisateur est toujours connecté, on met à jour les données
            // Récupération du nom de l'utilisateur et du tuble de la BDD correspondant à son compte
            $username = session()->get('LoggedUser'); // pseudo de l'utilisateur connecté
            $user = User::where('username', '=', $username)->first();
            if ($user->vehicle == 1) {
                $ride = new Trip;
                $ride->id_driver = $user->id;
                $ride->starting_town = $request->departure;
                $ride->ending_town = $request->final;
                $ride->description = $request->info;
                $ride->precision = $request->rdv;
                $ride->price = $request->price;
                $ride->number_of_seats = $request->nb_passengers;
                $date=new \DateTime($request->date);
                $heure = explode(':',$request->time);
                $date->setTime($heure[0],$heure[1]);
                $ride->date_trip = $date;
                if ($request->privacy == 'public') {
                    $ride->private = 0;
                } else {
                    $ride->private = 1;
                    $data = Group::where('name', '=', $request->group)->first();
                    $ride->id_group = $data->id;
                }

                //$trip=Trip::latest()->where('id_driver', '=', $user->id)->first();

                $query = $ride->save();

                if ($query) {
                    // $trip= Trip::where('id_driver', '=', $user->id)->last();

                    $count = 1;
                    if(!empty($request->stage)) {
                        foreach ($request->stage as $item) {
                            $stage = new Stage;
                            $stage->stage = $item;
                            $stage->id_trip = $ride->id;
                            $stage->order = $count;
                            $count += 1;
                            $query = $stage->save();

                            if (!$query) {
                                return back()->with('fail', 'Something went wrong');
                            }

                            //notifyPrivateGroup($ride->id_group, $ride);
                        }

                    }
                    return back()->with('success', 'Your trip has been successfully registered');
                } else {
                    return back()->with('fail', 'Something went wrong');
                }
            } else {
                return back()->with('not_driver', 'You are not registered as driver, change your status before create ride');
            }
        }
    }

    public function show_trip_in_waiting(){

        if (session()->has('LoggedUser')) { // Si l'utilisateur est toujours connecté, on met à jour les données
            $username = session()->get('LoggedUser'); // pseudo de l'utilisateur connecté
            $user = User::where('username', '=', $username)->first();
            $id=$user->id;
            $trips = DB::select("select * from users,trips,link_user_trip where users.id=? and
            link_user_trip.id_user=users.id and trips.id=link_user_trip.id_trip", [$id]);
            $link_trips=DB::select("select * from users,trips,link_user_trip where users.id=? and
            link_user_trip.id_user=users.id", [$id]);
        }
        //Afficher tous les trajets en attente
        return view('trip/trip_in_waiting',['trips'=>$trips,'link_trips'=>$link_trips]);
    }

    /**
     * @param int $id:id de utilisateur qui est enlevé
     * @param int $idRide:id de trajet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function  delete_user_from_ride(int $id,int $idRide)
    {
        //Cas normal
        if (session()->has('LoggedUser')) { // Si l'utilisateur est toujours connecté, on met à jour les données
            //on enlève le tuple de utilisateur
            $trip = Trip::where('id', '=', $idRide)->first();
            $select=DB::selectOne('select TIMESTAMPDIFF(SECOND,Now(),?) AS Diff
            from trips where id=? ',[$trip->date_trip,$idRide]);
            //il reste au moin 24 h
            if($select[0]/3600>24) {
                $deleted = DB::delete('delete from link_user_trip where id_user=? And id_trip=?', [$id, $idRide]);
                if ($deleted) {
                    $update = DB::update('update trips set number_of_seats= number_of_seats +1 where id=?', [$idRide]);
                    if ($update) {
                        return back()->with("delete successfully");
                    } else {
                        return back()->with("delete failed ");
                    }
                } else {
                    return back()->with("delete failed ");
                }
            } else {
                return back()->with("delete failed ");
            }
        }
    }

    private function notifyPrivateGroup(int $idGroup, Trip $trip)
    {
        // Récupération du nom de l'utilisateur et du tuble de la BDD correspondant à son compte
        $username = session()->get('LoggedUser'); // pseudo de l'utilisateur connecté
        $userLogged = User::where('username', '=', $username)->first();

        $users = DB::table('link_users_groups')
                    ->where('id_group','=', $idGroup)
                    ->get();
        foreach($users as $user)
        {
            $userToNotify = User::find($user->id);

            $userToNotify->notify(new newPrivateTrip(userLogged,$userToNotify,$trip));
        }

    }
}
