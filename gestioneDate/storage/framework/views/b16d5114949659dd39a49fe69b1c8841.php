<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuovo Personale Universitario - Università degli Studi di Brescia</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/docente.css')); ?>">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <!-- Script Bootstrap & Popper -->
        <script src="<?php echo e(asset('js/popper.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>

        <!-- Navbar Istituzionale -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                <a class="navbar-brand py-0" href="<?php echo e(route('admin.dashboard')); ?>">
                    <img class="img-logo" src="<?php echo e(asset('images/unibsLogo.png')); ?>" alt="Unibs" style="max-height: 70px;">
                </a>

                <button class="navbar-toggler border-0 shadow-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="<?php echo e(route('admin.dashboard')); ?>"
                               style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                                <?php echo csrf_field(); ?>
                                <button type="submit" 
                                        class="btn btn-danger btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start"
                                        style="width: 115px;">
                                    <i class="bi bi-box-arrow-right text-center me-2" style="width: 18px;"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenuto Principale -->
        <div class="container py-3">
            
            <!-- Header della Pagina con Pulsante Pill Torna all'Elenco -->
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-person-plus-fill me-2"></i>Aggiungi Personale
                    </h1>
                    <p class="text-muted small mb-0 mt-1">Inserisci un nuovo docente o amministratore nel sistema</p>
                </div>
                <a href="<?php echo e(route('admin.personale.index')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-uppercase">
                    <i class="bi bi-arrow-left me-2"></i>TORNA ALL'ELENCO
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <!-- Gestione Errori Validazione -->
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3" role="alert">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Attenzione:</div>
                            <ul class="mb-0 ps-3 small">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Card Form -->
                    <div class="card shadow-sm border-0 rounded-3 bg-white">
                        <div class="card-body p-4">
                            <form action="<?php echo e(route('admin.personale.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label for="nome" class="form-label text-dark fw-bold">Nome <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control rounded-3" required value="<?php echo e(old('nome')); ?>" placeholder="Es. Mario">
                                </div>

                                <div class="mb-3">
                                    <label for="cognome" class="form-label text-dark fw-bold">Cognome <span class="text-danger">*</span></label>
                                    <input type="text" name="cognome" id="cognome" class="form-control rounded-3" required value="<?php echo e(old('cognome')); ?>" placeholder="Es. Rossi">
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label text-dark fw-bold">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control rounded-3" required value="<?php echo e(old('username')); ?>" placeholder="Es. mrossi">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label text-dark fw-bold">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control rounded-3" required minlength="6" placeholder="Minimo 6 caratteri">
                                </div>

                                <div class="mb-3">
                                    <label for="data_nascita" class="form-label text-dark fw-bold">Data di Nascita <span class="text-danger">*</span></label>
                                    <input type="date" name="data_nascita" id="data_nascita" class="form-control rounded-3" required value="<?php echo e(old('data_nascita')); ?>">
                                </div>

                                <div class="mb-4">
                                    <label for="ruolo" class="form-label text-dark fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select rounded-3" required>
                                        <option value="Docente" <?php echo e(old('ruolo') == 'Docente' ? 'selected' : ''); ?>>Docente</option>
                                        <option value="Amministratore" <?php echo e(old('ruolo') == 'Amministratore' ? 'selected' : ''); ?>>Amministratore</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-3 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center text-uppercase">
                                    <i class="bi bi-person-plus-fill me-2"></i>CREA UTENTE
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/personale/create.blade.php ENDPATH**/ ?>