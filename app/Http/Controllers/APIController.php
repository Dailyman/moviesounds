<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class APIController extends Controller
{
    //Main function for handling API-requests.
    public function index(Request $request)
    {
        $filters = $request->query('t');
        $jsoninfo = $this->search($filters);
        return json_encode($jsoninfo);
    }
    //Main function for finding information and soundtracks.
    public function search($movietitle)
    {
        //Get movieinformation.
        $movieinformation=$this->searchOmdb($movietitle);

        //Controll if movie was found in Omdb-API.
        if ($movieinformation['status']==0){
            $arr = array('Status' => 0);
            return $arr;
        }

        else{
            $soundtrackinformation=$this->searchTunefind($movieinformation['title'],$movieinformation['year']);

            //Check if soundtrackinformation exists.Returns array.
            if($soundtrackinformation){
                $spotifyinformation=$this->searchSpotify($soundtrackinformation);
                array_forget($movieinformation,'status');
                $information=array();
                $information['status']=['status',1];
                $information['movieinfo']=$movieinformation;
                $information['spotifyids']=$spotifyinformation;
                return $information;

            }
            //Returns array.
            else{
                array_forget($movieinformation,'status');
                $information=array();
                $spotifyids=array();
                $information['status']=['status',2];
                $information['movieinfo']=$movieinformation;
                $information['spotifyids']=$spotifyids;
                return $information;
            }

        }
    }

    //Search in OMDB API and retrieves Title,Year,Plot and Poster-link. Returns this in a Array.
    public function searchOmdb($title)
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
            \Session::put('title',$title);
            $information=['status'=>1,'title'=>$title,'year'=>$year,'plot'=>$plot,'poster'=>$poster];
        }

        return $information;

    }

    //Search in Tunefind API and retrieves artist and songtitle for all soundtracks found. Returns artist and songtitle in a nested array.
    public function searchTunefind($title,$year)
    {
        //Keys for Tunefind-API
        $username='a904641145de3ef28ea19bb65d4e5e7d';
        $password='53f2b51644b997caac2d531af279cc49';
        $title = preg_replace('/\s+/', '-', $title);
        $title=strtolower($title);
        $URL="https://www.tunefind.com/api/v1/movie/{$title}";


        //CURL-Setup
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $result=curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close ($ch);

        //Control status received from API. If status_code equals 200 information about soundtracks is stored in a array.
        if($status_code=="200"){
            $data = json_decode($result, true);
            $information=[];
            foreach ($data['songs'] as $song){
                $information[]=['title'=>$song['name'],'artist'=>$song['artist']['name']];
            }
            return $information;

        }

        //Control status received from API. Adds Year to URL and then search in API again.
        elseif($status_code!="200"){

            $URL="https://www.tunefind.com/api/v1/movie/{$title}-{$year}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$URL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $result=curl_exec ($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            curl_close ($ch);

            //Control status received from API. If status_code equals 200 information about soundtracks is stored in a array.
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

    //Search in Spotify API. Retrieves id for tracks and returns them in array.
    public function searchspotify($soundtracks)
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

