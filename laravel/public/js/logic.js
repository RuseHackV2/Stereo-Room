/**
 * Created by Z50-70 on 6.11.2015 г..
 */
var genre1;
var test;
$(function() {
    StereoRoom.initialize();

})

var StereoRoom = {
    data: {
        emotion: "",
        emotionType: "",
        currentAudio: "",
        currentSong: 0,
        data: {},
        actionType: "",
        country: "",
        offset: 0,
        genre: "",
        colors: {
            'yellow': '#FFCE54',
            "red": "#ED5565",
            'white': "#F5F7FA",
            'green': "#A0D468",
            "purple": "#EC87C0",
            'orange': "#F6BB42",
            "grey": "#AAB2BD",
            "black" : "#434A54",
            "blue": "#4FC1E9"
        },
        history: [],
        cheer: ""
    },
    initialize: function() {
        this.pickIntensity();
        this.makeRequest();
        this.playNext();
        this.addSearch();
        this.handleGenreClick();
        this.showSimilarSongs();
        this.loadMore();
        this.showHistory();
        this.loadHistoryItems();
        this.checkForId();
        this.handleCheer();
        this.onListening();
        this.updateListening();
        StereoRoom.data.country =  $("#country").text();
        StereoRoom.data.history = (localStorage.history) ? JSON.parse(localStorage.history) : [];
    },
    onListening: function() {
          $("body").on("click", ".listening-to div", function() {
              var id = $(this).data("src");
              StereoRoom.checkForId(id);
          })
    },
    handleCheer: function() {
      $("body").on("click", ".cheer .list-group-item", function() {
          StereoRoom.data.offset = 0;
          var mood = $(this).hasClass("happy") ? "happy" : "sad";
          $.get("/getFeeling/" + mood + "/" + StereoRoom.data.offset, function(data) {
                 var dataJson = JSON.parse(JSON.parse(data));
                  StereoRoom.data.data = dataJson;
                  StereoRoom.data.actionType = "cheer";
                  StereoRoom.data.cheer = mood;
                  StereoRoom.loadSong();
                  $("body,html").scrollTop($(".currentSong").offset().top);
              })
          })
      },
    updateListening: function() {
      setInterval(function() {

          $.get("/showListening", function(data) {
              $(".listening-to").html("");
              data = JSON.parse(data);
              console.log(data);
              for (var i = 0;i < data.length;i++) {
                  
                  $(".listening-to").append("<li class='list-group-item'><div data-src='"+data[i].song_id+"'>"+data[i].artist + " - " + data[i].title + "</div></li>");
              }
          })
      }, 1000 * 30)
    },
    checkForId: function(trackId) {
        var id;
        if (!trackId) {
            id = $("#song-id").text();

        }
        else {
            id = trackId;
        }
        if (id.length) {

            $.get("/getTrack/" + id, function(data) {
                var dataJson = JSON.parse(JSON.parse(data));
                console.log(dataJson);
                if (dataJson.track) {

                    StereoRoom.data.data = dataJson;
                    StereoRoom.loadSong(false, true);
                    $("body,html").scrollTop($(".currentSong").offset().top);
                }
            })
        }
    },
    loadHistoryItems: function() {
      $("body").on("click", ".history-item", function() {
          $.get("/getTrack/" + $(this).data("song"), function(data) {

              var dataJson = JSON.parse(JSON.parse(data));
              if (dataJson.track) {
                  StereoRoom.data.data = dataJson;
                  StereoRoom.loadSong(false, true);
                  $("#history-modal").modal("hide");
                  $("body,html").scrollTop($(".currentSong").offset().top);
              }
          })
      })
    },
    showHistory: function() {

        $("#history").click(function(evt) {
            $("#history-modal").find(".list-group").children().remove();
            evt.preventDefault();

            var history = StereoRoom.data.history;
            for (var i = 0; i < history.length;i++) {
                var itemText = (StereoRoom.data.country === "BG") ? history.length + " песни в историята Ви." : history.length + " songs in your history.";
                $("#history-modal").find(".modal-title").text(itemText);
                $("#history-modal").find(".list-group").append('<li data-song="'+history[i].id+'" class="history-item list-group-item">'+history[i].details+ '(' +history[i].time+ ')</li>')
            }
            $("#history-modal").modal("show");

        })
    },
    showSimilarSongs: function() {
        $("body").on("click", "#see-similar", function(evt) {
            evt.preventDefault();
            $.get("/getSimilar/" + StereoRoom.data.data.tracks[StereoRoom.data.currentSong].id,function(data) {
                console.log(data);return;
                var dataJson = JSON.parse(JSON.parse(data));
                if (dataJson.tracks.length) {
                    var emoString = (StereoRoom.data.country === "BG") ? "Сходно с" : "Similar to";
                    StereoRoom.data.emotionType = emoString + ' ' + StereoRoom.data.data.tracks[StereoRoom.data.currentSong].title;
                    StereoRoom.data.data = dataJson;
                    StereoRoom.data.currentSong = 0;
                    StereoRoom.loadSong();
                    StereoRoom.data.actionType = "similar";
                }



            })
        })
    },
    handleGenreClick: function() {

        $("body").on("click", ".submit-genre", function() {
            var genre = genre1;
            if (!$("#keep-emotion").is(":checked")) {
                $.get("/getGenre/" + decodeURIComponent(genre).replace("/", "xXx") + "/", function(data) {
                    processData(data);
                })
            }
            else {
                $.get("/getGenre/" + decodeURIComponent(genre).replace("/", "xXx") + "/0/" + StereoRoom.data.emotion, function(data) {
                    processData(data);
                })
            }

        })

        function processData(data) {
            console.log(data);
            var dataJson = JSON.parse(JSON.parse(data));
            StereoRoom.data.data = dataJson;
            var emoStr = (StereoRoom.data.country === "BG") ? "Жанр: " : "Genre: ";
            StereoRoom.data.emotionType = emoStr + genre;
            StereoRoom.data.currentSong = 0;
            StereoRoom.loadSong();
            StereoRoom.data.actionType = "genre";
            StereoRoom.data.genre = genre;
            $("#genre-modal").modal('hide');
        }

        $("body").on("click", ".genre li", function() {
             genre1 = $(this).text();
            $("#genre-modal").modal('show');




        })
    },
    addSearch: function() {
        $(".submitEmotion").click(function(evt) {
            evt.preventDefault();
            var val = $("#searchForEmotions").val();
            var datalistItem = $("#emotions option[value='" + val + "']");
            if (datalistItem.length) {
                var color = datalistItem.data('color');
                StereoRoom.data.emotion = color;
                StereoRoom.data.emotionType = val;
                StereoRoom.data.actionType = "emotion";
                $("#myModal").modal("show");
                return;
            }
            var contents = (StereoRoom.data.country === "BG")  ? "Несъществуваща емоция!" : "The emotion does not exist!";
            return alert(contents);

        })
    },
    playNext: function() {
        $("body").on("click", ".playNext",function() {
            var songNum = $(this).data("num");
            if (songNum !== StereoRoom.data.currentSong) {
                StereoRoom.data.currentSong = songNum;
                StereoRoom.loadSong();
            }
        });
    },
    pickIntensity: function() {
        $(".particular-emotion").click(function() {
            StereoRoom.data.emotion = $(this).data("color");
            StereoRoom.data.emotionType = $(this).text();
            $("#myModal").modal("show");
        })
    },
    loadGenreCloud: function(genres) {
        $(".genre").html("");
        for (genre in genres) {
            if (genre !== "top_value" && genre !== 'top') {
                var height = genres[genre] * 2;
                height += 30;
                var color = StereoRoom.data.colors[StereoRoom.data.emotion];
                var html = $("<li><span style='background-color:"+color+";display:inline-block;height:"+height+"px' class='list-group-item '>"+genre+"</span>");
                $(".genre").append(html);

            }

        }
    },
    loadSong: function (noRefresh, singleTrack) {
            var noRfrsh = (noRefresh) ? true : false;
            var isSingle = (singleTrack) ? true : false;
            var container = $(".currentSong");
            if (StereoRoom.data.currentAudio && !StereoRoom.data.currentAudio.paused) {
                StereoRoom.data.currentAudio.pause();
            }
            var val;
            if (isSingle) {
                val = StereoRoom.data.data.track;
            }
             else {
                val = StereoRoom.data.data.tracks[StereoRoom.data.currentSong];
            }

            if (!val) {
                return;
            }

            if (!val.music_url) {
                ++StereoRoom.data.currentSong;
                StereoRoom.loadSong();
                return;
            }
            audio = new Audio(val.music_url);
            audio.onerror = function() {
                ++StereoRoom.data.currentSong;
                StereoRoom.loadSong();


            }
            audio.onended = function() {
                ++StereoRoom.data.currentSong;
                StereoRoom.loadSong();
                var songDetails = val.artist_name + " - " + val.title + " <span class='emotion-type'>("+StereoRoom.data.emotionType+")</span>";
                var time = new Date().getFullYear()  +  "/" + (new Date().getMonth() + 1) + "/" + new Date().getDate() + " " + new Date().getHours() + ":" + new Date().getMinutes() + ":" + new Date().getSeconds()
                StereoRoom.data.history.push({details: songDetails, id: val.id, time: time});
                localStorage.history = JSON.stringify(StereoRoom.data.history);
                console.log("/saveSong/?id=" + encodeURIComponent(val.id) + "&title=" +
                    encodeURIComponent(val.title) + "&artist=" + encodeURIComponent(val.artist_name) + "&image=" +
                     encodeURIComponent(val.image_url) + "&country=" + encodeURIComponent(StereoRoom.data.country)
                );
                $.get("/saveSong/?id=" + encodeURIComponent(val.id) + "&title=" +
                    encodeURIComponent(val.title) + "&artist=" + encodeURIComponent(val.artist_name) + "&image=" +
                      encodeURIComponent(val.image_url) + "&country=" + encodeURIComponent(StereoRoom.data.country)
                );
            }
            audio.oncanplay = function() {
              ;
                audio.controls = true;
                $(".song-audio").html("");
                $(".song-audio").append(audio);
                var songDetails = val.artist_name + " - " + val.title + " <span class='emotion-type'>("+StereoRoom.data.emotionType+")</span>";
                container.find(".panel-title").html(songDetails);
                container.find(".song-image").html('<img src="' + val.image_url + '">');
                StereoRoom.loadGenreCloud(val.genre_cloud);
                $(".cheer").html("");

                var cheer;
                if (StereoRoom.data.country === "BG") {
                    cheer = {
                        'happy': "Щастие",
                        "sad": "Тъга"
                    };
                }
                else {
                    cheer = {
                        "happy": "Happy",
                        "sad" : "Sad"
                    }
                }
                var color = StereoRoom.data.colors[StereoRoom.data.emotion];
                var height = val.mood.happy_songs * 2;
                height += 30;
                var html = $("<li><span style='background-color:"+color+";display:inline-block;height:"+height+"px' class='list-group-item happy '>"+cheer.happy+"</span>");
                $(".cheer").append(html);
                height = val.mood.sad_songs * 2;
                height += 30;
                var html = $("<li><span style='background-color:"+color+";display:inline-block;height:"+height+"px' class='list-group-item sad '>"+cheer.sad+"</span>");
                $(".cheer").append(html);

                $(".cheer").append('<li></li>')
                container.show();
                audio.play();
                StereoRoom.data.currentAudio = audio;
                StereoRoom.loadNextSongs(noRfrsh);
                history.pushState({} , songDetails , "/id/" + val.id);
                var color2 = StereoRoom.data.emotion;
                $('.fill').css("background-color", color);
                if (color2 === 'white') {
                     $(".logo").attr("src", "/images/logo_PURPLE.png");
                }
                else {
                    $(".logo").attr("src", "/images/logo_WHITE.png");
                }
                







        }
    },
    loadMore: function() {
      $("body").on("click", ".load-more", function() {
          StereoRoom.data.offset += 10;
          if (StereoRoom.data.actionType === 'emotion') {
              $.get("/getSongs/" + encodeURIComponent(StereoRoom.data.emotion) + "/" + encodeURIComponent($(".intensity-range").val()) + "/" +
              encodeURIComponent(StereoRoom.data.offset), function (data) {
                  var dataJson = JSON.parse(JSON.parse(data));
                  StereoRoom.data.data = dataJson;
                  StereoRoom.loadSong(true);
              });
          }
          if (StereoRoom.data.actionType === 'genre') {
              $.get("/getGenre/" + decodeURIComponent(StereoRoom.data.genre).replace("/", "xXx") + "/", function (data) {
                  var dataJson = JSON.parse(JSON.parse(data));
                  StereoRoom.data.data = dataJson;
                  StereoRoom.loadSong(true);

              })
          }
          if (StereoRoom.data.actionType === "cheer") {
              $.get("/getFeeling/" + StereoRoom.data.cheer + "/" + StereoRoom.data.offset, function(data) {
                  var dataJson = JSON.parse(JSON.parse(data));
                  StereoRoom.data.data = dataJson;
                  StereoRoom.loadSong(true);
              })
          }
          //TODO: add load more for recommendations (similar)
      })
    },
    loadNextSongs: function(noRefresh) {
        if (!noRefresh) {
            $(".nextSongs").html("");
        }
        if (noRefresh) {
            $(".load-more").remove();
        }
        var html = "";

        for (var i = 0;i < StereoRoom.data.data.tracks.length;i++) {
            if (i === StereoRoom.data.currentSong) {
                continue;
            }
            var val = StereoRoom.data.data.tracks[i];
            var songDetails = val.artist_name + " - " + val.title + " <span class='emotion-type'>("+StereoRoom.data.emotionType+")</span>";
            $(".nextSongs").append($("<li data-num='"+i+"' class='list-group-item playNext'>"+songDetails+"</li>"));
        }
        var btnText = (StereoRoom.data.country === "BG") ? "Зареди още" : "Load More";
        $(".nextSongs").append('<li class="list-group-item load-more sButtonBG btn btn-lg">'+btnText+'</li>')



    },
    playNextSong: function(i) {

    },
    makeRequest: function() {
        $(".intensity").click(function() {

            $.get("/getSongs/" + encodeURIComponent(StereoRoom.data.emotion) + "/" + encodeURIComponent($(".intensity-range").val()) + "/", function(data) {
                test = JSON.parse(JSON.parse(data));
                var dataJson = JSON.parse(JSON.parse(data));
                StereoRoom.data.data = dataJson;
                 StereoRoom.loadSong();
                StereoRoom.data.actionType = "emotion";
                $("#myModal").modal("hide");


                })



            })
        }
    }

