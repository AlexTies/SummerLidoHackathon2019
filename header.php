<header>
      <a onclick="openSidebar()" href="javascript:void(0)"><i class="material-icons">menu</i></a>
        <img src="./img/alta_text.png">
    </header>
    <nav id="navleiste" class="hidden">
        <a onclick="closeSidebar()" href="javascript:void(0)"><i class="material-icons">close</i></a>

          <div>
            <img src="./img/alta_logo.png">
            <a href="./main.php">Homepage</a>
            <a href="./key.php">Key</a>
            <a href="./stay.php">Your Stay</a>
            <a href="./booked.php">Booked</a>
            <a href="./roomservice.php">Room Service</a>
            <a href="./roomexplorer.php">Room Explorer</a>
            <a href="./roomcontrol.php">Room Control</a>
          </div>
        </nav>
        <script>
        function closeSidebar(){
          navleiste.classList.add('hidden');
        }
        function openSidebar(){
          navleiste.classList.remove('hidden');
        }
      </script>
