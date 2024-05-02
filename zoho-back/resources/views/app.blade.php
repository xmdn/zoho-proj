<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @inertiaHead
    <style>
        /* Add your header styles here */
        header {
            background-color: #8587a136;
            padding: 10px 0;
        }
        .loged-header {
          padding-left: 40%;
          padding-right: 10px;
          display: flex;
          justify-content: space-between;
        }
        .unloged-header {
          padding-right: 10px;
          display: flex;
          justify-content: flex-end;
        }
        nav ul {
            display: flex;
            align-items: center;
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        ul li {
          text-decoration: underline;
        }
        nav ul li {
            display: inline-block;
            margin-right: 10px;
        }
        nav ul li a {
            color: #000;
            text-decoration: none;
            padding: 5px 10px;
        }
        .logout-btn {
          padding: 10px 10px;
        }
        .login-btn {
          font-family: monospace;
          color: white;
          border-radius: 18px;
          background-color: #5964e8;
          padding: 10px 20px;
        }
    </style>
  </head>
  <body>
    <header>
      
        @php
            $accessToken = Session::get('accessToken');
            $refreshToken = Session::get('refreshToken');
            if($accessToken) {
        @endphp
        <nav class="loged-header">
        <ul>
          <li><a href="/deals">Create Deal</a></li>
          <li><a href="/account">Create Account</a></li>
          
        </ul>
        <button class="logout-btn" onclick="logout()">logout</button>
        </nav>
        @php
        } else {
        @endphp
        <nav class="unloged-header">
          <button class="login-btn" onclick="login()">login</button>
        @php
        }
        @endphp
      
    </header>
    @inertia
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
      const accessToken = @json($accessToken);
      const refreshToken = @json($refreshToken);
      console.log('Refresh ', refreshToken);
      console.log('Accs ', accessToken);
      async function login() {
          try {
            window.location.href = '/oauth'; // Make a POST request to your logout endpoint
              // location.reload(); // Reload the page after successful logout
          } catch (error) {
              console.error('Logout error:', error);
          }
      }
      // Function to handle logout
      async function logout() {
          try {
              await axios.post('api/logout'); // Make a POST request to your logout endpoint
              location.reload(); // Reload the page after successful logout
          } catch (error) {
              console.error('Logout error:', error);
          }
      }
    </script>
  </body>
</html>