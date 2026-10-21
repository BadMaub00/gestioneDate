<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Aula - <?php echo e($aula->nome); ?></title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/docente.css')); ?>">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <script src="<?php echo e(asset('js/popper.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>

        <!-- Navbar Istituzionale -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom border-3 border-unibs py-2 mb-4">
            <div class="container-fluid px-3 px-md-5">
                <a class="navbar-brand py-0" href="#">
                    <img class="img-logo" src="<?php echo e(asset('images/unibsLogo.png')); ?>" alt="Unibs" style="max-height: 70px;">
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
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
                                <button type="submit" class="btn btn-danger btn-sm text-white fw-bold px-3 py-1.5 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-start" style="width: 115px;">
                                    <i class="bi bi-box-arrow-right text-center me-2" style="width: 18px;"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h1 class="h4 fw-bold text-unibs m-0">Modifica Aula</h1>
                            <p class="text-muted small m-0">Edificio: <strong><?php echo e($aula->edificio->nome ?? 'N/D'); ?></strong></p>
                        </div>
                        <a href="<?php echo e(route('admin.sedi.aule', $aula->edificio_id)); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna Indietro
                        </a>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="m-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4 bg-white">
                            <form action="<?php echo e(route('admin.sedi.updateAula', $aula->aula_id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <!-- Nome Aula -->
                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold text-dark small mb-1">
                                        Nome Aula <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="nome" 
                                           id="nome" 
                                           class="form-control shadow-sm" 
                                           value="<?php echo e(old('nome', $aula->nome)); ?>" 
                                           required>
                                </div>

                                <!-- Capienza -->
                                <div class="mb-4">
                                    <label for="capienza" class="form-label fw-bold text-dark small mb-1">
                                        Capienza (Posti a sedere) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="capienza" 
                                           id="capienza" 
                                           class="form-control shadow-sm" 
                                           min="1"
                                           value="<?php echo e(old('capienza', $aula->capienza)); ?>" 
                                           required>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Aggiorna Aula
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/sedi/editAula.blade.php ENDPATH**/ ?>