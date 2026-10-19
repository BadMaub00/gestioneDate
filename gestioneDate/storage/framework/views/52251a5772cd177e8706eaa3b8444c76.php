<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuova Sessione d'Esame</title>
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    </head>
    <body class="min-vh-100" style="background-color: #e1edf7;">

        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="h4 fw-bold text-unibs m-0">Crea Sessione d'Esame</h1>
                        <a href="<?php echo e(route('sessioni.index')); ?>" class="btn btn-outline-secondary btn-sm rounded-pill">Annulla</a>
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
                            <form action="<?php echo e(route('sessioni.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label for="nome" class="form-label fw-bold">Nome Sessione <span class="text-danger">*</span></label>
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="es. Sessione Invernale" required value="<?php echo e(old('nome')); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="anno" class="form-label fw-bold">Anno Accademico <span class="text-danger">*</span></label>
                                        <input type="number" name="anno" id="anno" class="form-control" placeholder="es. 2026" required value="<?php echo e(old('anno', date('Y'))); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_appelli" class="form-label fw-bold">N° Appelli Previsti <span class="text-danger">*</span></label>
                                        <input type="number" name="numero_appelli" id="numero_appelli" class="form-control" min="1" max="10" required value="<?php echo e(old('numero_appelli', 2)); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="data_inizio" class="form-label fw-bold">Data Inizio <span class="text-danger">*</span></label>
                                        <input type="date" name="data_inizio" id="data_inizio" class="form-control" required value="<?php echo e(old('data_inizio')); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="data_fine" class="form-label fw-bold">Data Fine <span class="text-danger">*</span></label>
                                        <input type="date" name="data_fine" id="data_fine" class="form-control" required value="<?php echo e(old('data_fine')); ?>">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-unibs text-white fw-bold w-100 rounded-pill py-2 mt-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Salva Sessione
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/sessioni/create.blade.php ENDPATH**/ ?>