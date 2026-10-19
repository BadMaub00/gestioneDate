<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calendario Generale Appelli - Amministratore</title>

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
            
            <!-- Alert messaggi di successo -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Intestazione e Selettore Sessione -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check-fill me-2"></i>Calendario Generale Appelli
                    </h1>
                    <p class="text-muted small m-0 mt-1">Supervisione e gestione globale di tutti gli appelli d'esame</p>
                </div>

                <!-- Form selezione sessione -->
                <form action="" method="GET" class="d-flex align-items-center gap-2">
                    <label for="sessione_id" class="form-label fw-bold text-nowrap mb-0">Sessione:</label>
                    <select name="sessione_id" 
                            id="sessione_id" 
                            class="form-select form-select-sm shadow-sm" 
                            onchange="if(this.value) window.location.href = this.value;">
                        <?php $__currentLoopData = $sessioni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sessione): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e(route('admin.calendario', $sessione->sessione_id)); ?>" <?php echo e((isset($sessione_id) && $sessione_id == $sessione->sessione_id) ? 'selected' : ''); ?>>
                                <?php echo e($sessione->nome); ?> (<?php echo e($sessione->anno); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Tabella degli Appelli -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Insegnamento</th>
                                <th class="py-3 px-4 text-dark fw-bold">Corso di Laurea</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Data</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Orario</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $appelli; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appello): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <!-- Insegnamento -->
                                    <td class="py-3 px-4 fw-semibold text-dark">
                                        <?php echo e($appello->insegnamentoCorso->insegnamento->nome ?? 'N/D'); ?>

                                    </td>

                                    <!-- Corso -->
                                    <td class="py-3 px-4 text-dark">
                                        <?php echo e($appello->insegnamentoCorso->corso->nome ?? 'N/D'); ?>

                                    </td>

                                    <!-- Data -->
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <span class="badge border border-unibs text-unibs bg-transparent px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="bi bi-calendar-event me-1"></i><?php echo e(\Carbon\Carbon::parse($appello->data)->format('d/m/Y')); ?>

                                        </span>
                                    </td>

                                    <!-- Orario -->
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <span class="badge border border-unibs text-unibs bg-transparent px-3 py-1.5 rounded-pill fw-bold">
                                            <i class="bi bi-clock me-1"></i><?php echo e(substr($appello->ora_inizio, 0, 5)); ?> - <?php echo e(substr($appello->ora_fine, 0, 5)); ?>

                                        </span>
                                    </td>

                                    <!-- Pulsanti Azione (Modifica ed Elimina) -->
                                    <td class="py-3 px-4 text-center text-nowrap">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Modifica -->
                                            <a href="<?php echo e(route('admin.appelli.edit', $appello->getKey())); ?>" 
                                               class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                               style="width: 36px; height: 36px;" 
                                               title="Modifica Appello">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            <!-- Elimina -->
                                            <form action="<?php echo e(route('admin.appelli.destroy', $appello->getKey())); ?>" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Sei sicuro di voler eliminare questo appello?');" 
                                                  class="d-inline m-0">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                        style="width: 36px; height: 36px;" 
                                                        title="Elimina Appello">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2 text-secondary"></i>
                                        Nessun appello fissato per questa sessione.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/calendario.blade.php ENDPATH**/ ?>