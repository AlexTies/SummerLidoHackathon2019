<!DOCTYPE html>
<html lang="de" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Bluetooth Test</title>
  </head>
  <body id=main>
    Test
    <a onclick="startSearch()">test</a>
  </body>
  <script>
  function startSearch(){
    let options = {
  filters: [
    {name: 'NOI0082#AiBOCf'}
  ]
}

      navigator.bluetooth.requestDevice(options).then(function(device) {
      console.log('Name: ' + device.name);
      main.innerHTML += device.name;
    }).catch(function(error) {
      main.innerHTML += ("Something went wrong. " + error);
    });
  }
</script>

</html>
