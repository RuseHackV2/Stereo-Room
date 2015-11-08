<?php
$fullUrl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
switch($country) {
    case "BG":
        $texts = array(
            'intensity' => "Изберете интензитет",
            'feeling' => "Чувствам се",
            'submit' => "Виж",
            'close' => "Затвори",
            'recommendations' => "Харесва ти песента? Зареди подобни песни",
            "keep-emotion" => "Да търсим ли песни в този жанр, отговарящи на текущата ви емоция?",
            'truthy' => 'Да',
            "introduce-genre" => "Жанр",
            "introduce-cheer" => "Усещане",
            'tbplaceholder' => 'Как се чувствате?',
            'history' => "История",
            "historyListened" => " песни изслушани досега.",
            "listeningTo" => "Хората в твоята държава слушат сега: "
        );
            break;
    default:
        $texts = array(
            'intensity' => "Choose Intensity",
            'feeling' => "I feel",
            'submit' => "View",
            'close' => "Close",
            'recommendations' => "You like the song? Load similar songs",
            "keep-emotion" => "Should we search for songs in this genre corresponding to your emotion?",
            'truthy' => "Yes",
            "introduce-genre" => "Genre",
            "introduce-cheer" => "Feeling",
            'tbplaceholder' => 'How do you feel?',
            "history" => "History",
            "historyListened" => " songs listened so far.",
            "listeningTo" => "People in your country are now listening to: "
        );
}
?>
@extends("master")

@section("content")

    <span style="display: none;" id="song-id">{{ $id }}</span>
    
    <div class="container-fluid">
        <div class="row">
            
            <div class="col-sm-12 col-lg-4 col-md-4 col-sm-4 fill pull-left">
                
                <div class="row">
                    <img class="logo" style="width: 60%; display: block; margin: 0 auto" src="/images/logo_PURPLE.png" alt="">
                </div>
                
                <div style="margin-top: 10px" class="row">
                    <form action="">
                        <div class="input-group input-group-lg col-md-10 col-md-offset-1">
                            <input list="emotions" id="searchForEmotions" class="form-control input-lg" type="text" placeholder="{{$texts['tbplaceholder']}}"/>
                            <span class="input-group-btn">
                                <button class="sButtonBG btn btn-lg submitEmotion" type="submit">{{$texts['submit']}}</button>
                            </span>
                        </div>

                    </form>
                    
                    <datalist id="emotions">
                        @foreach($emotions as $emotion)
                            @if ($country === "BG")
                                <option data-color="{{$emotion->color}}" value="{{$emotion->emotion_bg}}"></option>
                            @else
                                <option data-color="{{$emotion->color}}" value="{{$emotion->emotion_en}}"></option>
                            @endif
                        @endforeach
                    </datalist>
                    
                </div>
                
                <div class="row">
                    <div style="margin-top: 20px" class="col-sm-12 col-md-10 col-md-offset-1">
                        <div class="panel panel-default emotions">
                            <div style="font-size: 15px" class="panel-body text-center">
                                @foreach($emotions as $emotion)
                                    @if($country !== "BG")
                                        <div style="color: #000" class="label particular-emotion" data-color="{{$emotion->color}}"> <img src="/images/{{ $emotion->emotion_en }}.png" style="width: 10%; background-color: #fff"/> {{$emotion->emotion_en}} </div>
                                    @else
                                        <div class="label particular-emotion" data-color="{{$emotion->color}}"> <img src="/images/{{ $emotion->emotion_en }}.png" style="width: 10%; background-color: #fff"/> {{$emotion->emotion_bg}} </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row text-center">
                    <a id="history" style="margin-top: 10px; margin-left: 10px" class="sButtonBG btn btn-lg" href="#">{{$texts['history']}}</a>
                </div>
                
            </div>
            
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{ $texts['intensity'] }}</h4>
                        </div>
                        <div class="modal-body">
                            <input class="intensity-range" type="range" min="0.1" max="100" step="1"/>
                            <input type="submit" style="margin-left: 85%" class="btn btn-default btn-lg intensity" value="{{ $texts['submit'] }}"/>                      
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 10px" class="col-lg-8 col-md-8 col-sm-8 pull-right">
                
                <div id="history-modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">0 {{ $texts['historyListened'] }}</h4>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group text-center"></ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-10 col-md-offset-1">
                    <div style="display: none;" class="panel currentSong panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Panel title</h3>
                        </div>
                        <div class="panel-body">
                            <div class="text-center">
                                <div class="song-image"></div><br>
                                <div class="song-audio"></div>
                            </div>
                            <div id="genre-modal" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">{{ $texts['keep-emotion'] }}</h4>
                                        </div>
                                        <div class="modal-body">
                                            <label for="keep-emotion">{{ $texts['truthy'] }}</label>
                                            <input type="radio" name="goo" id="keep-emotion" value="y"/><br>
                                            <label for="goog">No</label>
                                            <input id="goog" type="radio" name="goo" value="n"/>
                                            <br>
                                            <input type="submit" class="btn btn-default btn-lg submit-genre" value="{{ $texts['submit'] }}"/>
                                            <button type="button" class="btn btn-default btn-lg pull-right" data-dismiss="modal">{{ $texts['close'] }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center"> <p style="font-size: 1.3em;font-weight:bold;"> {{$texts['introduce-genre']}}</p>
                                <ul class="list-inline text-center genre"></ul>
                            </div>
                            <div class="text-center">  <p style="font-size: 1.3em;font-weight:bold;">  {{ $texts['introduce-cheer'] }}</p>
                                <ul class="list-inline text-center cheer"></ul>
                            </div>
                            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        </div>
                    </div>
                </div>
                
                    <div class="col-md-12 text-center">
                        <ul class="list-group nextSongs"></ul>
                    </div>
                
                    <div class="col-md-12 text-center">
                        <h3>{{$texts['listeningTo']}}</h3>
                        <div>
                            <ul class="list-group listening-to" >
                                @foreach($listening as $song)
                                    <li class="list-group-item">
                                        <div data-src="{{$song->song_id}}">  {{ $song->artist . " - " .  $song->title   }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>   
                    </div>
                
            </div>
            
        </div>
    </div>

@stop