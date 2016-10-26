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
        //Lägg till felhantering.
        //Get user-input.
        $movietitle=$request->titleinput;
        //print_r($movietitle);

        //Get movieinformation.
        $movieinformation=$this->searchOmdb($movietitle);

        if ($movieinformation['status']==0){
            \Session::flash('message', 'Filmtitel hittades ej!');
            return redirect('/')->withInput();     
        }

        else{
            $soundtrackinformation=$this->searchTunefind($movieinformation['title'],$movieinformation['year']);
            //print_r($soundtrackinformation);
            
            if($soundtrackinformation){
                $spotifyinformation=$this->searchSpotify($soundtrackinformation);
                $spotifytrackset=implode(',',$spotifyinformation);
                return view('main',compact('spotifytrackset'));
            }

            else{
                \Session::flash('message', 'Inga soundtracks hittades');
                return view ('main');
            }

        }
    }

    //Search in OMDB API
    public function searchOmdb(String $title)
    {
        $titleX = preg_replace('/\s+/', '+', $title);
        $page = file_get_contents("http://www.omdbapi.com/?t={$titleX}&plot=full&r=json");
        $data = json_decode($page, true);

        if($data['Response']=='False'){
            $information=['status'=>0];

        }else{
            $title=$data['Title'];
            $year=$data['Year'];
            $plot=$data['Plot'];
            $poster=$data['Poster'];

            $information=['status'=>1,'title'=>$title,'year'=>$year,'plot'=>$plot,'poster'=>$poster];
        }

        return $information;

    }

    //Search in Tunefind API
    public function searchTunefind(String $title,$year)
    {
        $username='a904641145de3ef28ea19bb65d4e5e7d';
        $password='53f2b51644b997caac2d531af279cc49';
        $title = preg_replace('/\s+/', '-', $title);
        $title=strtolower($title);
        $URL="https://www.tunefind.com/api/v1/movie/{$title}";


        //Kontrollera om URL om adresser.

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $result=curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);//get status code
        curl_close ($ch);
        //print_r($status_code);


        if($status_code=="200"){
            $data = json_decode($result, true);
            $information=[];
            foreach ($data['songs'] as $song){
                $information[]=['title'=>$song['name'],'artist'=>$song['artist']['name']];
            }
        return $information;
            
        }

        elseif($status_code!="200"){

            $URL="https://www.tunefind.com/api/v1/movie/{$title}-{$year}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$URL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");  //get status code
            $result=curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close ($ch);
            
            if($status_code=="200"){
                $data = json_decode($result, true);
                $information=[];
                foreach ($data['songs'] as $song){
                    $information[]=['title'=>$song['name'],'artist'=>$song['artist']['name']];
                }
            return $information; 
            }
           
        }
        

    }

    //Search in Spotify API
    public function searchspotify(Array $soundtracks)
    {
        $information=[];
        foreach ($soundtracks as $item){

            $name=$item['artist'];
            $name = preg_replace('/\s+/', '-', $name);
            $name=strtolower($name);

            $title=$item['title'];
            $title = preg_replace('/\s+/', '-', $title);
            $title=strtolower($title);

            $page = file_get_contents("https://api.spotify.com/v1/search?q=track%3A{$title}+artist%3A{$name}&type=track&limit=1");
            $data = json_decode($page, true);

            if($data['tracks']['total']!=0){
                $uri=$data['tracks']['items'][0]['id'];
                array_push($information,$uri);

            }


        }

        return $information;


    }


}
