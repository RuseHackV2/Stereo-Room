<?php
switch($country) {
    case "BG":
        $texts = array(
            'intensity' => "Изберете интензитет",
            'feeling' => "Чувствам се",
            'submit' => "Покажи песни",
            'close' => "Затвори",
            'recommendations' => "Харесва ти песента? Зареди подобни песни",
            "keep-emotion" => "Да търсим ли песни в този жанр, отговарящи на текущата ви емоция?",
            'truthy' => 'Да',
            'tbplaceholder' => 'Как се чувствате?'
        );
            break;
    default:
        $texts = array(
            'intensity' => "Choose Intensity",
            'feeling' => "I feel",
            'submit' => "View Songs",
            'close' => "Close",
            'recommendations' => "You like the song? Load similar songs",
            'keep-emotion' => "Should we search for songs in this genre corresponding to your emotion?",
            'truthy' => "Yes",
            'tbplaceholder' => 'How you feel?'
        );
}
?>
@extends("master")

@section("content")
<div class="container-fluid">
    
    <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="row">
                        <img style="width: 80%; display: block; margin: 0 auto" src="images/stereoroom2.png" alt="">
                    </div>
                    <form action="">
                        <div class="input-group input-group-md">
                            <input list="emotions" id="searchForEmotions" class="form-control input-lg" type="text" placeholder="{{$texts['tbplaceholder']}}"/>
                            <span class="input-group-btn">
                              <button class="sButtonBG btn btn-primary btn-lg submitEmotion" type="submit">{{$texts['submit']}}</button>
                            </span>
                        </div>    
                    </form>
                </div>
            </div>
    
  
    <div style="margin-top: 20px" class="col-md-6 col-md-offset-3">
        
    <div class="panel panel-default emotions">
        <div class="panel-body">
            @foreach($emotions as $emotion)
                @if($country !== "BG")
                    <div class="label particular-emotion label-default" data-color="{{$emotion->color}}">  {{$emotion->emotion_en}} </div>
                @else
                    <div class="label particular-emotion label-default" data-color="{{$emotion->color}}">  {{$emotion->emotion_bg}} </div>
                @endif
            @endforeach
        </div>
    </div>
        
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ $texts['intensity'] }}</h4>
                </div>
                <div class="modal-body">
                    <input class="intensity-range" type="range" min="0.1" max="100" step="1"/>
                    <input type="submit" class="btn btn-default btn-lg intensity" value="{{ $texts['submit'] }}"/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ $texts['close'] }}</button>
                </div>
            </div>

        </div>
    </div>
    
    </div>
    <div class="col-md-6 col-md-offset-3">
    <div style="display: none;" class="panel currentSong panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Panel title</h3>
        </div>
        <div class="panel-body">
            <span class="song-image"></span>
            <div class="song-audio">

            </div>
            <div id="genre-modal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{ $texts['keep-emotion'] }}</h4>
                        </div>
                        <div class="modal-body">
                            <label for="keep-emotion">{{ $texts['truthy'] }}</label>
                            <input type="radio" id="keep-emotion" value="y"/>
                            <input type="submit" class="btn btn-default btn-lg submit-genre" value="{{ $texts['submit'] }}"/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ $texts['close'] }}</button>
                        </div>
                    </div>

                </div>
            </div>
            <ul class="list-inline text-center genre"></ul>
            <a href="#" id="see-similar"> {{$texts['recommendations']}}</a>
        </div>
    </div>
    </div>
    <div class="col-md-12 text-center">
    <ul class="list-group nextSongs">
    </ul>
    </div>
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
@stop