<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Sessione d'Esame</title>
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <!-- Script Bootstrap & Popper -->
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

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Modifica Sessione</h1>
                        <a href="<?php echo e(route('admin.sessioni.index')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
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
                        <div class="card-body p-4">
                            <form action="<?php echo e(route('admin.sessioni.update', $sessione->sessione_id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Sessione <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" required value="<?php echo e(old('nome', $sessione->nome)); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="numero_appelli" class="form-label fw-bold">N° Appelli Previsti (1-4) <span class="text-danger">*</span></label>
                                    <input type="number" name="numero_appelli" id="numero_appelli" class="form-control" min="1" max="4" required value="<?php echo e(old('numero_appelli', $sessione->numero_appelli)); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_inizio" class="form-label fw-bold">Data Inizio <span class="text-danger">*</span></label>
                                        <input type="date" name="data_inizio" id="data_inizio" class="form-control" required value="<?php echo e(old('data_inizio', $sessione->data_inizio)); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="data_fine" class="form-label fw-bold">Data Fine <span class="text-danger">*</span></label>
                                        <input type="date" name="data_fine" id="data_fine" class="form-control" required value="<?php echo e(old('data_fine', $sessione->data_fine)); ?>">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 mt-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i>Aggiorna Sessione
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collegamento allo script esterno -->
        <script src="<?php echo e(asset('js/editSessione.js')); ?>"></script>
    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/sessioni/edit.blade.php ENDPATH**/ ?>