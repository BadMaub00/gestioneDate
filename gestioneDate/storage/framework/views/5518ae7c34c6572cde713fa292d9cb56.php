<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seleziona Edificio - Amministratore</title>

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

        <script src="<?php echo e(asset('js/confirmDelete.js')); ?>"></script>

        <!-- Navbar Istituzionale -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                
                <!-- Logo Università -->
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="<?php echo e(asset('images/unibsLogo.png')); ?>" alt="Unibs" style="max-height: 70px;">
                </a>

                <button class="navbar-toggler border-0 shadow-none" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-2 mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a class="btn btn-unibs btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" 
                               href="<?php echo e(route('admin.dashboard')); ?>" style="width: 115px;">
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

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Intestazione e Pulsante Torna alla Dashboard -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                    <i class="bi bi-building me-2"></i>Seleziona un Edificio
                </h1>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                </a>
            </div>
            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Pulsante Aggiungi Edificio -->
            <div class="mb-4">
                <a href="<?php echo e(route('admin.sedi.createEdificio')); ?>" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill me-2 fs-5"></i>Aggiungi Edificio
                </a>
            </div>

            <!-- Griglia degli Edifici -->
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $edifici; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $edificio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3">
                            <div class="card-body d-flex flex-column justify-content-between p-4 bg-white">
                                <div>
                                    <!-- Titolo e Bottoni Azione -->
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <h5 class="card-title fw-bold text-unibs m-0 pe-2"><?php echo e($edificio->nome); ?></h5>
                                        
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <!-- Tasto Modifica -->
                                            <a href="<?php echo e(route('admin.sedi.editEdificio', $edificio->edificio_id)); ?>" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Edificio">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Tasto Elimina -->
                                            <form action="<?php echo e(route('admin.sedi.destroyEdificio', $edificio->edificio_id)); ?>" 
                                                method="POST" 
                                                data-confirm="Sei sicuro di voler eliminare l'edificio '<?php echo e($edificio->nome); ?>'? Verranno eliminate anche tutte le aule associate." 
                                                class="d-inline m-0">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                        style="width: 36px; height: 36px;" 
                                                        title="Elimina Edificio">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Indirizzo / Via dell'Edificio -->
                                    <p class="text-muted small mb-3 fw-medium">
                                        <i class="bi bi-geo-alt-fill me-1 text-unibs"></i><?php echo e($edificio->via ?? $edificio->indirizzo ?? 'Indirizzo non specificato'); ?>

                                    </p>
                                </div>

                                <a href="<?php echo e(route('admin.sedi.aule', $edificio->edificio_id)); ?>" class="btn btn-unibs text-white fw-bold w-100 rounded-pill shadow-sm mt-3">
                                    Visualizza Aule <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Nessun edificio presente nel sistema.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/sedi/edifici.blade.php ENDPATH**/ ?>