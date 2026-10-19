<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Docente</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
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
                               href="<?php echo e(route('docente.dashboard')); ?>"
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
        <div class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6">
                        
                        <!-- Card Selezione Sessione -->
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center">
                                    <i class="bi bi-calendar-range-fill me-2 fs-5"></i> Selezione Sessione d'Esame
                                </h5>
                            </div>

                            <div class="card-body p-4 bg-white">
                                <form action="<?php echo e(route('docente.procediCalendario')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>

                                    <div class="mb-4">
                                        <label for="sessione_id" class="form-label fw-bold text-dark mb-2">
                                            Scegli una sessione disponibile:
                                        </label>
                                        
                                        <select class="form-select border-2 border-unibs shadow-none py-2" id="sessione_id" name="sessione_id" required>
                                            <option value="" selected disabled>-- Seleziona Sessione --</option>
                                            <?php $__empty_1 = true; $__currentLoopData = $sessioni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sessione): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <option value="<?php echo e($sessione->sessione_id); ?>">
                                                    <?php echo e($sessione->nome); ?> (<?php echo e($sessione->anno); ?>) - [dal <?php echo e(\Carbon\Carbon::parse($sessione->data_inizio)->format('d/m/Y')); ?> al <?php echo e(\Carbon\Carbon::parse($sessione->data_fine)->format('d/m/Y')); ?>]
                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <option value="" disabled>Nessuna sessione d'esame trovata</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4 pt-2 border-top">
                                        <a href="<?php echo e(route('docente.dashboard')); ?>" class="btn btn-outline-secondary fw-bold px-3 py-2 rounded-3">
                                            <i class="bi bi-arrow-left me-1"></i> Annulla
                                        </a>
                                        <button type="submit" class="btn btn-unibs text-white fw-bold px-4 py-2 rounded-3 shadow-sm">
                                            Visualizza Calendario <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/docente/selezionaSessione.blade.php ENDPATH**/ ?>