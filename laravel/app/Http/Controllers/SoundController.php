<?php

namespace App\Http\Controllers;

use App\Emotion;
use App\Listening;
use App\LocationMiddleware;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


class SoundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showListening() {

        $listening = DB::table('listening')->take(5)->get();
        return json_encode($listening);
    }

    public function index($id = null)
    {

    $loc = new LocationMiddleware();
    $country = $loc->getIp();
    $emotions = Emotion::all();
    $oldListening = Listening::where("timestamp", "<", time() - 3600  );
    $oldListening->delete();
    $listening = DB::table('listening')->take(5)->get();
    return view("stereoroom.index")->with("country", $_SESSION['location'])->with("emotions", $emotions)->with("id", $id)->with("listening", $listening);
    }


    public function getSongs($emotion, $intensity, $offset = 0) {
        $color= urldecode($emotion);
        $intensity = urldecode($intensity);
        $curl = curl_init();
        $intensity = ($intensity <= 20) ? $intensity + 20 : $intensity;
        $minimumIntensity = (int)($intensity >= 20) ? $intensity - 20 : 1;
        $minimumIntensity = (int)($intensity >= 70) ? $intensity - 35 : $minimumIntensity;
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://api.randrmusic.com/api/search/tracks?catalogue=soundcloud&colors['.$color.'][from]='.$minimumIntensity.'&colors['.$color.'][to]='.$intensity.'&limit=10&offset='.
        $offset,
            CURLOPT_HTTPHEADER => array('x-api-auth: ')
        ));
        $result = curl_exec($curl);
        return json_encode($result);
    }

    public function getGenre($genre, $offset = 0, $color = "", $intensity = "") {

        $curl = curl_init();
        $genre = str_replace("xXx", "/",$genre);
        $add = "";
        if ($color) {
            $add .= "&order_by[colors][".$color . ']=desc';
        }
        else {
            $add = "";
        }
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://api.randrmusic.com/api/search/tracks?catalogue=soundcloud&genre_cloud='.urlencode($genre).'&limit=10&offset=' . $offset . $add ,
            CURLOPT_HTTPHEADER => array('x-api-auth: ')
        ));
        $result = curl_exec($curl);
        return json_encode($result);
    }
    public function getFeeling($feeling, $offset = 0) {

        if (in_array($feeling, array("sad", "happy"))) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'http://api.randrmusic.com/api/search/tracks?catalogue=soundcloud&order_by[moods]['.$feeling.'_songs]=desc&limit=10&offset=' . $offset,
                CURLOPT_HTTPHEADER => array('x-api-auth: ')
            ));
            $result = curl_exec($curl);
            return json_encode($result);
        }

    }
    public function getTrack($trackId) {

        $curl = curl_init();
        $trackId = urldecode($trackId);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://api.randrmusic.com/api/tracks/' . urlencode($trackId),
            CURLOPT_HTTPHEADER => array('x-api-auth: ')
        ));
        $result = curl_exec($curl);
        return json_encode($result);
    }

    public function getSimilar($trackId) {
        $curl = curl_init();
        $trackId = urldecode($trackId);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://api.randrmusic.com/api/tracks/'.$trackId.'/recommendations?catalogue=soundcloud&limit=10&offset=0',
            CURLOPT_HTTPHEADER => array('x-api-auth: ')
        ));
        $result = curl_exec($curl);
        return json_encode($result);
    }

    public function saveSong() {
        $songId = urldecode(strip_tags($_GET['id']));
        $title = urldecode(strip_tags($_GET['title']));
        $imageUrl = urldecode(strip_tags($_GET['image']));
        $country =  urldecode(strip_tags($_GET['country']));
        $artistName =  urldecode(strip_tags($_GET['artist']));
        $listening = new Listening();
        $listening->song_id = $songId;
        $listening->country = $country;
        $listening->artist = $artistName;
        $listening->timestamp = time();
        $listening->img = $imageUrl;
        $listening->title = $title;
        $listening->save();

    }


}
