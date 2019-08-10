<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Willkommen</title>
  <link rel="manifest" href="./manifest.json">

    <style>
    body {
        margin: 0;
        background-color: black;
        font-family: 'Roboto', sans-serif;
    }

    main {
        height: 100vh;
        width: 100vw;
        display: table;
    }

    section {
        display: table-cell;
        vertical-align: middle;
    }

    img {
        width: 250px;
        margin: 0 auto;
        display: block;
    }

    .maincont {
        display: block;
        width: calc(100% - 30px);
        max-width: 400px;
        margin: 0 auto;
        height: 80vh;
        position: relative;
        color: white;
        padding: 0px 15px;
    }

    .footer {
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
    }

    .footer a {
        padding: 12px 24px;
        margin: 0 auto;
        background-color: white;
        display: table;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: black;
        font-weight: bold;
    }

    h1 {
        margin: 20px 0 0 0;
    }

    p {
        margin: 5px 0 0 0;
    }
    a {
      text-decoration: none;
    }
    a.button {
        padding: 12px 24px;
        margin: 0 auto;
        background-color: white;
        display: table;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: black;
        font-weight: bold;
    }
    i.material-icons {
        font-size: 60px;
    }

    .hold {
        text-align: center;
    }

    .hold p {
        margin-bottom: 20px;
    }
    div#available.hidden {
    display: none;
}
    </style>
  </head>
  <body>
    <main>
      <section>
        <div class="maincont">
          <img src="./img/alta_logo.png" />
          <h1>
            Tür öffnen
          </h1>
          <div id="reader">

          </div>
          <div class="hold hidden" id="available">
            <i class="material-icons">rss_feed</i>
            <p>
              Halten Sie das Gerät an Ihre Tür und drücken Sie auf öffnen.
            </p>
            <a class="button" href="javascript:void(0)" onclick="sendNFCHello()">ÖFFNEN</a>
          </div>

          <div class="footer">
            <a href="./main.php">HOME</a>
          </div>
        </div>
      </section>
    </main>
  </body>
  <script>
  if(!navigator.nfc){
    reader.innerHTML = "Ihr Gerät unterstützt leider kein NFC.";
  } else {
    available.classList.remove('hidden');
  }
  function sendNFCHello(){
    navigator.nfc.push({
                data: [{
                  recordType: "text",
                  data: "Hello World"
                }]
              });
  }
</script>
</html>
