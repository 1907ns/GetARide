@extends('layouts.sidebar')
<link href="{{ asset('/css/user_edit.css') }}" rel="stylesheet" type="text/css" >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
@section('content')<div class="panel-body">

    <header class="header" style="color: #d6d8db;font-family: 'Agency FB'">
        <h3 align="center">Mes groupes crées</h3>
    </header>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th style="color: #d6d8db">Ville de départ</th>
            <th style="color: #d6d8db">Ville d'arrivée</th>
            <th style="color: #d6d8db">Date et heure de départ du trajet</th>
            <th style="color: #d6d8db">Nombre de places</th>
            <th style="color: #d6d8db">Prix</th>
            <th style="color: #d6d8db">+ de détails</th>
            <th style="color: #d6d8db">Supprimer le trajet</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $trip)
            <tr>
                <td><label style="color: #d6d8db" for={{$trip->starting_town}}>{{$trip->starting_town}}</label></td>
                <td><label style="color: #d6d8db" for={{$trip->ending_town}}>{{$trip->ending_town}}</label></td>
                <td><label style="color: #d6d8db" for={{$trip->date_trip}}>{{$trip->date_trip}}</label></td>
                <td><label style="color: #d6d8db" for={{$trip->number_of_seats}}>{{$trip->number_of_seats}}</label></td>
                <td><label style="color: #d6d8db" for={{$trip->price}}>{{$trip->price}}</label></td>
                <td><a href="#"><button type="submit" class="btn btn-block btn-primary">+ de détails</button></a></td>
                <td><a href="/trip/delete_trip/{{$trip->id}}"><button type="submit" class="btn btn-block btn-danger" onclick="return confirm('Êtes-vous sûr? Vous supprimez ce trajet. Une fois confirmé, le système supprimera le trajet et ce dernier ne pourra plus être récupéré.')">Supprimer</button></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div><a href="../dashboard"><button class="btn-dark btn" type="button">Revenir à l'accueil</button></a></div>
@endsection
