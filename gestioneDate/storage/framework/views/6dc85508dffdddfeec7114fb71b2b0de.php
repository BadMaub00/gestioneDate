<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seleziona Corso</title>

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
                
                <!-- Logo Università -->
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="<?php echo e(asset('images/unibsLogo.png')); ?>" alt="Unibs" style="max-height: 70px;">
                </a>

                <!-- Toggler Mobile -->
                <button class="navbar-toggler border-0 shadow-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu di Navigazione -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <!-- Pulsante Home -->
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               aria-current="page" 
                               href="<?php echo e(route('admin.dashboard')); ?>"
                               style="width: 115px;">
                                <i class="bi bi-house-door-fill text-center me-2" style="width: 18px;"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        <!-- Pulsante Logout -->
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
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                    <i class="bi bi-mortarboard-fill me-2"></i>Seleziona un Corso di Laurea
                </h1>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                </a>
            </div>
            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Pulsante Aggiungi Corso -->
            <!-- Pulsante Aggiungi Corso -->
            <div class="mb-4">
                <a href="<?php echo e(route('admin.corsi.create')); ?>" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi Corso
                </a>
            </div>

            <!-- Griglia dei Corsi -->
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $corsi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <!-- Titolo e Bottoni Azione in alto a destra -->
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <h5 class="card-title fw-bold text-unibs m-0 pe-2"><?php echo e($corso->nome); ?></h5>
                                        
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <!-- Tasto Modifica (Sfondo bianco, bordo e icona blu Unibs) -->
                                            <a href="#" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Corso">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Tasto Elimina (Sfondo bianco, bordo e icona rossa) -->
                                            <form action="#" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Sei sicuro di voler eliminare questo corso?');" 
                                                  class="d-inline m-0">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                        style="width: 36px; height: 36px;" 
                                                        title="Elimina Corso">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <p class="text-muted small mb-3">Codice: <?php echo e($corso->codice ?? 'N/D'); ?></p>
                                </div>

                                <a href="<?php echo e(route('admin.corsi.pianoDiStudi', $corso->corso_id)); ?>" class="btn btn-unibs text-white fw-bold w-100 rounded-pill shadow-sm mt-3">
                                    Visualizza Piano di Studi <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Nessun corso presente nel sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/corsi/index.blade.php ENDPATH**/ ?>