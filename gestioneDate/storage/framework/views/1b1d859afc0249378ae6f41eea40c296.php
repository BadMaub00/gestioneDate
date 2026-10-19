<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Associazioni Docente-Insegnamento - Amministratore</title>

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

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Intestazione -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold text-unibs m-0 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2"></i>Associazioni Docenti - Insegnamenti
                    </h1>
                    <p class="text-muted small m-0 mt-1">Elenco e gestione dei docenti assegnati agli insegnamenti dei vari corsi</p>
                </div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-unibs text-white fw-bold btn-sm rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i>Torna alla Dashboard
                </a>
            </div>

            <hr class="border-unibs border-2 opacity-100 mb-4">

            <!-- Pulsante Aggiungi Associazione -->
            <div class="mb-4">
                <a href="<?php echo e(route('admin.personale.associa')); ?>" class="btn btn-unibs text-white fw-bold w-100 py-2.5 rounded-3 shadow-sm d-flex align-items-center justify-content-center">
                    <i class="bi bi-person-plus-fill me-2 fs-5"></i>Aggiungi Associazione
                </a>
            </div>

            <!-- Tabella Associazioni Raggruppate per Corso -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white border-bottom">
                            <tr>
                                <th class="py-3 px-4 text-dark fw-bold">Docente</th>
                                <th class="py-3 px-4 text-dark fw-bold">Insegnamento</th>
                                <th class="py-3 px-4 text-dark fw-bold">Corso di Laurea</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Ruolo</th>
                                <th class="py-3 px-4 text-center text-dark fw-bold">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($associazioniGrouped->isNotEmpty()): ?>
                                <?php $__currentLoopData = $associazioniGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $corsoNome => $associazioni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <!-- Intestazione Gruppo Corso -->
                                    <tr class="table-light">
                                        <td colspan="5" class="py-2 px-4 fw-bold text-unibs border-top border-bottom">
                                            <i class="bi bi-journal-bookmark-fill me-2"></i><?php echo e($corsoNome); ?>

                                        </td>
                                    </tr>

                                    <!-- Righe Associazioni del Corso -->
                                    <?php $__currentLoopData = $associazioni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <!-- Docente (Cognome e Nome) -->
                                            <td class="py-3 px-4 fw-bold text-dark">
                                                <?php echo e($assoc->docente->cognome ?? ''); ?> <?php echo e($assoc->docente->nome ?? 'N/D'); ?>

                                            </td>

                                            <!-- Insegnamento -->
                                            <td class="py-3 px-4 fw-semibold text-dark">
                                                <?php echo e($assoc->insegnamentiCorso->insegnamento->nome ?? 'N/D'); ?>

                                            </td>

                                            <!-- Corso di Laurea -->
                                            <td class="py-3 px-4 text-dark">
                                                <?php echo e($assoc->insegnamentiCorso->corso->nome ?? 'N/D'); ?>

                                            </td>

                                            <!-- Ruolo -->
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge border border-unibs text-unibs bg-transparent rounded-pill py-2 fw-bold d-inline-block" style="width: 135px;">
                                                    <?php echo e($assoc->ruolo); ?>

                                                </span>
                                            </td>

                                            <!-- Azioni -->
                                            <td class="py-3 px-4 text-center text-nowrap">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- Tasto Modifica -->
                                                    <a href="#" 
                                                    class="btn bg-white border border-unibs text-unibs rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                    style="width: 36px; height: 36px;" 
                                                    title="Modifica Associazione">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>

                                                    <!-- Tasto Elimina -->
                                                    <form action="#" 
                                                        method="POST" 
                                                        onsubmit="return confirm('Sei sicuro di voler rimuovere questa associazione?');" 
                                                        class="d-inline m-0">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" 
                                                                class="btn bg-white border border-danger text-danger rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" 
                                                                style="width: 36px; height: 36px;" 
                                                                title="Rimuovi Associazione">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5 fs-6">
                                        <i class="bi bi-people fs-3 d-block mb-2 text-secondary"></i>
                                        Nessuna associazione presente.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/admin/personale/associazioni_index.blade.php ENDPATH**/ ?>