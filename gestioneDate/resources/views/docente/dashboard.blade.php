<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Docente</title>
        <link rel="stylesheet" href="../../resources/css/bootstrap_v2.min.css">
        
        <link rel="stylesheet" href="../../bootstrap-icons/bootstrap-icons.css">
        <link rel="stylesheet" href="{{ asset('css/docente.css') }}">
    </head>
    <body>

        <!-- 1. Popper.js (necessario per dropdown, tooltip e popover di Bootstrap) -->
        <script src="../../js/popper.min.js"></script>

        <!-- 2. Bootstrap JS -->
        <script src="../../js/bootstrap.min.js"></script>
        <!--<header>
            <h1>Dashboard Docente</h1>
            <nav>
                <ul>
                    <li><a href="{{ route('docente.dashboard') }}">Home</a></li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-link" style="padding: 0; border: none; background: none; color: red; cursor: pointer;">
                            Logout
                        </button>
                    </form>
                </ul>
            </nav>
        </header>-->


        <nav class="navbar navbar-expand-lg bg-body-tertiary">
         <div class="container-fluid">
            <a class="navbar-brand" href="#"><img class="img-logo" src="../../images/unibsLogo.png" alt="Unibs"></a>
            <button class="navbar-toggler" 
                     type="button" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#navbarSupportedContent" 
                     aria-controls="navbarSupportedContent" 
                     aria-expanded="false" 
                     aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('docente.dashboard') }}">
                            <span class="bi bi-house me-1"></span>Home
                        </a>
                    </li>
                    <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link text-danger" style="text-decoration: none;">
                                    Logout
                                </button>
                            </form>
                        </li>
               </ul>
            </div>
         </div>
      </nav>
    </body>
</html>
