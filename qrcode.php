<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>QR-Code scannen</title>
    <link rel="manifest" href="./manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <style>
    body {
        margin: 0;
        margin-top: 54px;
        font-family: 'Roboto', sans-serif;
    }

    header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background-color: black;
        padding: 4px;
        height: 46px;
        text-align: center;
    }

    header img {
        height: 46px;
        text-align: center;
        transform: scale(1.2);
    }

    main {
        position: absolute;
        top: 54px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: black;
    }

    camera {
        display: block;
        margin: 0 auto;
        padding: 20px 0;
        width: calc(100% - 20px);
        max-width: 400px;
        height: calc(100% - 40px);
        position: relative;
    }

    video {
      background-color: black;
        position: absolute;
        display: block;
        width: 100%;
        height: calc(100% - 40px);
    }

    camera .cameras {
        position: absolute;
        bottom: 20px;
        right: 0;
        left: 0;
        background-color: #00000094;
        text-align: center;
    }

    camera .cameras a {
        margin: 10px 5px;
        display: inline-block;
        padding: 8px 15px;
        background-color: white;
        text-transform: uppercase;
        font-size: 14px;
    }

    .error {
        position: absolute;
        top: 20px;
        right: 0;
        left: 0;
        z-index: 1;
        text-align: center;
        padding: 20px;
    }

    .error.hidden {
        display: none;
    }
    .cameras span {
        color: white;
        display: block;
    padding: 10px 10px 0 10px;
    }
    a {
      text-decoration: none;
      color: inherit;
    }
    </style>
  </head>
  <body>
    <header>
      <!--<a href="javascript:void(0)"><i class="material-icons">menu</i></a>-->
        <img src="./img/alta_text.png" />
    </header>
    <main>
      <camera>
        <span class="error hidden" id="nocam">Please use your Smartphone.</span>
        <video id=preview></video>
        <div class="cameras">
          <span id="text">Please now scan the QR code on your room card</span>
          <div id="cams">

          </div>
          <a href="javascript:void(0)" onclick="checkRoom('azazer')">JUST CONTINUE</a>
        </div>
      </camera>
    </main>
    <script type="text/javascript">

      let scanner = new Instascan.Scanner({ video: document.getElementById('preview'), mirror: false });

      scanner.addListener('scan', function (content) {

        checkRoom(content);

      });

      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          cams.innerHTML = "";
          for (var i = 0; i < cameras.length; i++) {
            cams.innerHTML += '<a href="javascript:void(0)" onclick="selectCam(' + i + ')">Camera ' + (i+1) + '</a>';
          }
          scanner.start(cameras[1]);


        } else {

          nocam.classList.remove('hidden');

        }

      }).catch(function (e) {
                nocam.classList.remove('hidden');
      });
      function selectCam(camid){
        Instascan.Camera.getCameras().then(function (cameras) {

          if (cameras.length > 0) {

            scanner.start(cameras[camid]);


          } else {

            nocam.classList.remove('hidden');

          }

        }).catch(function (e) {
                  nocam.classList.remove('hidden');
        });
      }
      function checkRoom(id){
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var info = JSON.parse(this.responseText);
                if(info[0] == 0){
                  text.innerHTML = "Room not known. Please scan code again.";
                } else {
                  document.location = "./main.php?room=" + id;
                }
            }
        };
        xmlhttp.open("POST", "api/api.php");
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(JSON.stringify(['roomcheck', id]));


      }
    </script>
  </body>
</html>
