<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Associa Docente a Insegnamento</title>

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
            <div class="row justify-content-center">
                <div class="col-12 col-md-8">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                            <i class="bi bi-person-plus-fill me-2"></i>Associa Docente a Insegnamento
                        </h1>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                        </a>
                    </div>
                    <hr class="border-unibs border-2 opacity-100 mb-4">

                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">

                            <form action="<?php echo e(route('admin.personale.storeAssocia')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <!-- Seleziona Insegnamento / Corso -->
                                <div class="mb-3">
                                    <label for="ins_corso_id" class="form-label fw-bold">Insegnamento e Corso <span class="text-danger">*</span></label>
                                    <select name="ins_corso_id" id="ins_corso_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un insegnamento --</option>
                                        <?php $__currentLoopData = $insegnamentiCorso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($ic->ins_corso_id); ?>">
                                                <?php echo e($ic->insegnamento->nome ?? 'Insegnamento N/D'); ?> 
                                                (<?php echo e($ic->corso->nome ?? 'Corso N/D'); ?> - <?php echo e($ic->anno_di_corso); ?>° Anno)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Seleziona Docente -->
                                <div class="mb-3">
                                    <label for="utente_id" class="form-label fw-bold">Docente <span class="text-danger">*</span></label>
                                    <select name="utente_id" id="utente_id" class="form-select" required>
                                        <option value="" disabled selected>-- Seleziona un docente --</option>
                                        <?php $__currentLoopData = $docenti; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($d->utente_id); ?>">
                                                <?php echo e($d->cognome); ?> <?php echo e($d->nome); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <!-- Seleziona Ruolo -->
                                <div class="mb-4">
                                    <label for="ruolo" class="form-label fw-bold">Ruolo <span class="text-danger">*</span></label>
                                    <select name="ruolo" id="ruolo" class="form-select" required>
                                        <option value="Primario">Primario</option>
                                        <option value="Secondario">Secondario</option>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i>Salva Associazione
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/personale/associa.blade.php ENDPATH**/ ?>