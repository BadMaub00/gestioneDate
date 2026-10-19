<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accedi - Gestione Date d'Esame</title>

        <!-- CSS Bootstrap & Icone -->
        <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap_v2.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('bootstrap-icons/bootstrap-icons.css')); ?>">
        
        <!-- CSS Personalizzati -->
        <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('css/docente.css')); ?>">
    </head>
    <body class="bg-light min-vh-100 d-flex flex-column justify-content-between">

        <!-- Script Bootstrap & Popper -->
        <script src="<?php echo e(asset('js/popper.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>


        <!-- Contenuto Principale: Card di Login Centrata -->
        <div class="my-auto py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Logo Ingrandito e Centrato -->
                    <div class="text-center mb-5">
                        <img class="img-fluid" 
                            src="<?php echo e(asset('images/unibsLogo.png')); ?>" 
                            alt="Unibs" 
                            style="max-height: 110px;">
                    </div>
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                        
                        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                            
                            <!-- Header Card Blu Istituzionale -->
                            <div class="card-header bg-unibs text-white py-3 px-4 border-0 text-center">
                                <h5 class="card-title mb-0 fw-bold d-flex align-items-center justify-content-center">
                                    <i class="bi bi-shield-lock-fill me-2 fs-4"></i> Accedi al Sistema
                                </h5>
                            </div>

                            <!-- Body Card con Form -->
                            <div class="card-body p-4 bg-white">
                                <form method="POST" action="<?php echo e(route('login.perform')); ?>">
                                    <?php echo csrf_field(); ?>

                                    <!-- Campo Username -->
                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-bold text-dark mb-1">
                                            <i class="bi bi-person-fill me-1 text-unibs"></i> Username
                                        </label>
                                        <input type="text" 
                                               class="form-control border-2 <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> py-2" 
                                               id="username" 
                                               name="username" 
                                               value="<?php echo e(old('username')); ?>" 
                                               placeholder="Inserisci il tuo username" 
                                               required 
                                               autofocus>
                                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback fw-bold mt-1">
                                                <?php echo e($message); ?>

                                            </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <!-- Campo Password -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-bold text-dark mb-1">
                                            <i class="bi bi-lock-fill me-1 text-unibs"></i> Password
                                        </label>
                                        <input type="password" 
                                               class="form-control border-2 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> py-2" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Inserisci la tua password" 
                                               required>
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback fw-bold mt-1">
                                                <?php echo e($message); ?>

                                            </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <!-- Pulsante Accedi -->
                                    <button type="submit" class="btn btn-unibs text-white fw-bold py-2.5 rounded-3 shadow-sm w-100 d-inline-flex align-items-center justify-content-center">
                                        <i class="bi bi-box-arrow-in-right me-2 fs-5"></i> Accedi
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Istituzionale Discreto -->
        <footer class="py-3 text-center small">
            <div class="container">
                <span class="fw-semibold text-dark">&copy; Università degli Studi di Brescia - Portale del Personale Universitario</span>
            </div>
        </footer>

    </body>
</html><?php /**PATH C:\xampp\htdocs\gestioneDate\gestioneDate\resources\views/utente/login.blade.php ENDPATH**/ ?>