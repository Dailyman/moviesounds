<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class SearchController extends Controller
{


    public function index()
    {
        //
    }

    //Main function for finding information and soundtracks.
    public function search(Request $request)
    {
        //Get user-input.
        $movietitle=$request->titleinput;

        //Get movieinformation.
        $movieinformation=searchOmdb($movietitle);

        //Get soundtracks from Tunefind

        //Get spotify-uri for soundtracks via Spotify-API.

        //Return view with spotify-uri array and movieinformation array.
    }

    //Search in OMDB API
    public function searchOmdb(String $title)
    {
        $titleX = preg_replace('/\s+/', '+', $title);
        $page = file_get_contents("http://www.omdbapi.com/?t={$titleX}&y=&plot=full&r=json");
        $data = json_decode($page, true);

        $title=$data['Title'];
        $year=$data['Year'];
        $plot=$data['Plot'];
        $poster=$data['Poster'];

        //Return Array
    }

    //Search in Tunefind API
    public function searchTunefind()
    {
        $movietitle = 'pulp-fiction';
        $username='a904641145de3ef28ea19bb65d4e5e7d';
        $password='53f2b51644b997caac2d531af279cc49';
        $URL='https://www.tunefind.com/api/v1/movie/inception-2010';
        
        //Kontrollera om URL om adresser.
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result=curl_exec ($ch);
        curl_close ($ch);
        //print_r("Hej test");
        print_r($result);
        echo($result);

        
        
    }

    //Search in Spotify API
    public function searchspotify(String $artist, String $title)
    {
        //
    }


}
