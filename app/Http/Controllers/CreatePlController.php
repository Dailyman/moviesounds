<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use SpotifyWebAPI;


class CreatePlController extends Controller
{
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function index()
 {

  $clientid='414d68d43e5f4fa6ba684b969063ccdb';
  $clientsecret='2b22d869dfaf481fb2c46c7296cd445d';
  $redirecturi='http://localhost:8000/create';

  $session = new SpotifyWebAPI\Session($clientid, $clientsecret, $redirecturi);
  $api = new SpotifyWebAPI\SpotifyWebAPI();

  $scopes = array(
   'playlist-read-private',
   'user-read-private',
   'playlist-modify-public',
   'playlist-modify-private'
  );

  $authorizeUrl = $session->getAuthorizeUrl(array(
   'scope' => $scopes
  ));

  header('Location: ' . $authorizeUrl);

  die();

 }

 /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function create()
 {

  $clientid='414d68d43e5f4fa6ba684b969063ccdb';
  $clientsecret='2b22d869dfaf481fb2c46c7296cd445d';
  $redirecturi='http://localhost:8000/create';
  $session = new SpotifyWebAPI\Session($clientid, $clientsecret, $redirecturi);
  $api = new SpotifyWebAPI\SpotifyWebAPI();

  // Request a access token using the code from Spotify
  $session->requestAccessToken($_GET['code']);
  $accessToken = $session->getAccessToken();

  // Set the access token on the API wrapper
  $api->setAccessToken($accessToken);
  $user = $api->me();
  $username=$user->id;

  $title=\Session::pull('title','default');
  $title=$title.':by Moviesounds';

  //Create playlist
  $playlist=$api->createUserPlaylist($username, [
   'name' => $title,
  ]);
  $playlistid=$playlist->id;
  $value = \Session::pull('tracks', 'default');
  //$_SESSION['tracks'];

  $api->addUserPlaylistTracks($username, $playlistid, $value);
  \Session::flash('message', 'Playlist was created.');
  return redirect('/'); 
 }

 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
 public function store(Request $request)
 {
  //
 }

 /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function show($id)
 {
  //
 }

 /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function edit($id)
 {
  //
 }

 /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function update(Request $request, $id)
 {
  //
 }

 /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 public function destroy($id)
 {
  //
 }
}
